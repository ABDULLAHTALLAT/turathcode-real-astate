<?php

namespace App\Services;

use App\Models\Media;
use App\Models\MediaVariant;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * معالجة آمنة للصور:
 *  1) نتحقق من المحتوى الحقيقي (مش الامتداد ولا الـ MIME اللي بعته المستخدم)
 *  2) نفك الصورة بـ GD ونعيد ترميزها WebP => بيمسح أي EXIF/GPS أو كود مخفي (polyglot)
 *  3) 3 مقاسات (thumb/medium/large) بدون تكبير
 *  4) منع التكرار بالبصمة sha256
 */
class ImageProcessor
{
    public const MAX_BYTES = 8 * 1024 * 1024;
    public const MAX_PIXELS = 40_000_000;
    public const ALLOWED = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF];
    public const VARIANTS = ['thumb' => 480, 'medium' => 960, 'large' => 1600];
    public const QUALITY = 82;

    public function storeUpload(UploadedFile $file, ?User $owner = null, ?string $alt = null): Media
    {
        if (! $file->isValid() || $file->getSize() > self::MAX_BYTES) {
            throw new InvalidArgumentException('حجم الصورة أكبر من 8 ميجا أو الملف تالف.');
        }

        return $this->storeBinary((string) file_get_contents($file->getRealPath()), $owner, $alt);
    }

    public function storeBinary(string $binary, ?User $owner = null, ?string $alt = null): Media
    {
        if ($binary === '' || strlen($binary) > self::MAX_BYTES) {
            throw new InvalidArgumentException('الصورة فاضية أو أكبر من الحد المسموح.');
        }

        $info = @getimagesizefromstring($binary);
        if (! $info || ! in_array($info[2], self::ALLOWED, true)) {
            throw new InvalidArgumentException('الملف مش صورة صالحة (JPG / PNG / WebP / GIF فقط).');
        }
        if ($info[0] * $info[1] > self::MAX_PIXELS || $info[0] < 64 || $info[1] < 64) {
            throw new InvalidArgumentException('أبعاد الصورة غير مناسبة.');
        }

        $src = @imagecreatefromstring($binary);
        if (! $src) {
            throw new InvalidArgumentException('تعذّر قراءة الصورة.');
        }
        $src = $this->autoOrient($src, $binary, $info[2]);

        $variants = [];
        foreach (self::VARIANTS as $name => $maxW) {
            $variants[$name] = $this->encode($src, $maxW);
        }
        imagedestroy($src);

        $sha = hash('sha256', $variants['large']['data']);
        if ($existing = Media::where('sha256', $sha)->first()) {
            return $existing;
        }

        return DB::transaction(function () use ($variants, $sha, $owner, $alt) {
            $media = Media::create([
                'uuid' => (string) Str::ulid(), 'user_id' => $owner?->id, 'sha256' => $sha, 'mime' => 'image/webp',
                'width' => $variants['large']['width'], 'height' => $variants['large']['height'],
                'bytes' => strlen($variants['large']['data']), 'alt' => $alt ? Str::limit(strip_tags($alt), 190, '') : null,
            ]);
            foreach ($variants as $name => $v) {
                MediaVariant::create(['media_id' => $media->id, 'name' => $name, 'width' => $v['width'], 'height' => $v['height'], 'data' => $v['data']]);
            }

            return $media;
        });
    }

    /** @return array{data:string,width:int,height:int} */
    private function encode(\GdImage $src, int $maxW): array
    {
        $w = imagesx($src);
        $h = imagesy($src);
        $nw = min($w, $maxW);
        $nh = (int) round($h * ($nw / $w));

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 255, 255, 255, 0));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagewebp($dst, null, self::QUALITY);
        $data = (string) ob_get_clean();
        imagedestroy($dst);

        return ['data' => $data, 'width' => $nw, 'height' => $nh];
    }

    /** تصحيح اتجاه صور الموبايل (EXIF Orientation) قبل ما نمسح الـ EXIF */
    private function autoOrient(\GdImage $img, string $binary, int $type): \GdImage
    {
        if ($type !== IMAGETYPE_JPEG || ! function_exists('exif_read_data')) {
            return $img;
        }
        $exif = @exif_read_data('data://image/jpeg;base64,'.base64_encode($binary));
        $rot = match ($exif['Orientation'] ?? 1) { 3 => 180, 6 => -90, 8 => 90, default => 0 };

        return $rot ? (imagerotate($img, $rot, 0) ?: $img) : $img;
    }
}
