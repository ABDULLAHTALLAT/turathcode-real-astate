<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * معالجة آمنة للصور وحفظها كملفات منظمة داخل public/uploads (بدون storage وبدون storage:link):
 *   public/uploads/properties/{رقم العقار}/{ulid}-thumb|medium|large.webp
 *   public/uploads/avatars/{رقم المستخدم}/{ulid}-thumb|medium|large.webp
 *  1) نتحقق من المحتوى الحقيقي (مش الامتداد ولا الـ MIME)
 *  2) نعيد ترميز الصورة WebP => بيمسح أي EXIF/GPS أو كود مخفي
 *  3) 3 مقاسات (thumb/medium/large) بدون تكبير
 */
class ImageProcessor
{
    public const MAX_BYTES = 8 * 1024 * 1024;
    public const MAX_PIXELS = 40_000_000;
    public const ALLOWED = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF];
    public const VARIANTS = ['thumb' => 480, 'medium' => 960, 'large' => 1600];
    public const QUALITY = 82;

    /** يرجّع "أساس المسار" (بدون المقاس والامتداد) مثل uploads/properties/12/01HXYZ */
    public function storeUpload(UploadedFile $file, string $folder): string
    {
        if (! $file->isValid() || $file->getSize() > self::MAX_BYTES) {
            throw new InvalidArgumentException('حجم الصورة أكبر من 8 ميجا أو الملف تالف.');
        }

        return $this->storeBinary((string) file_get_contents($file->getRealPath()), $folder);
    }

    public function storeBinary(string $binary, string $folder): string
    {
        return $this->write($this->process($binary), $folder);
    }

    /**
     * يتحقق من الصورة ويرجّع المقاسات الجاهزة [thumb|medium|large => بيانات WebP].
     *
     * @return array<string,string>
     */
    public function process(string $binary): array
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

        $out = [];
        foreach (self::VARIANTS as $name => $maxW) {
            $out[$name] = $this->encode($src, $maxW)['data'];
        }
        imagedestroy($src);

        return $out;
    }

    /**
     * يكتب المقاسات في public/uploads/{folder}/ ويرجّع أساس المسار.
     *
     * @param array<string,string> $variants
     */
    public function write(array $variants, string $folder): string
    {
        $folder = 'uploads/'.trim(preg_replace('#[^a-z0-9/_-]#i', '', $folder), '/');
        File::ensureDirectoryExists(public_path($folder), 0755);

        $base = $folder.'/'.Str::ulid();
        foreach ($variants as $name => $data) {
            File::put(public_path($base.'-'.$name.'.webp'), $data);
        }

        return $base;
    }

    /** يمسح كل مقاسات صورة محفوظة (لو المسار مش http ولا صورة افتراضية) */
    public function delete(?string $base): void
    {
        if (! $base || ! str_starts_with($base, 'uploads/') || str_contains($base, '..')) {
            return;
        }
        foreach (array_keys(self::VARIANTS) as $name) {
            File::delete(public_path($base.'-'.$name.'.webp'));
        }
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
