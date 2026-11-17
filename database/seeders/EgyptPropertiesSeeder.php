<?php

namespace Database\Seeders;

use App\Models\FinishingItem;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Services\ImageProcessor;
use App\Support\EgyptData;
use Illuminate\Database\Seeder;

/** عقارات معطلة من كل محافظات مصر: كل محافظة ليها عدد عقارات بمراحل تشطيب مختلفة */
class EgyptPropertiesSeeder extends Seeder
{
    public const PER_GOVERNORATE = 6;

    /** @var array<string,array<string,string>> */
    private array $pool = [];

    public function run(): void
    {
        $owners = User::where('role', 'owner')->pluck('id');

        foreach (EgyptData::governorates() as $gov) {
            Property::factory()
                ->count(self::PER_GOVERNORATE)
                ->in($gov)
                ->sequence(fn ($seq) => ['owner_id' => $owners->random()])
                ->create()
                ->each(fn (Property $p) => $this->fill($p));
        }

        // حالات إضافية لتجربة لوحة الأدمن
        Property::factory()->count(12)->pending()->create(['owner_id' => $owners->random()])->each(fn ($p) => $this->fill($p));
        Property::factory()->count(4)->rejected()->create(['owner_id' => $owners->random()])->each(fn ($p) => $this->fill($p));
        Property::factory()->count(8)->rented()->create(['owner_id' => $owners->random()])->each(fn ($p) => $this->fill($p));
    }

    /** بنود التشطيب + صورة الغلاف حسب نوع العقار ومرحلته */
    private function fill(Property $p): void
    {
        $level = $p->stage_level;
        foreach (array_keys(catalog('finishing_items')) as $n => $key) {
            $status = match (true) {
                $level >= 5 => 'done',
                $key === 'structure' => 'done',
                $key === 'bricks' && $level >= 2 => 'done',
                $key === 'plaster' && $level >= 3 => 'done',
                $key === 'elevator' && $p->property_type !== 'building' => 'na',
                $level >= 4 && $n % 3 === 0 => 'partial',
                default => 'missing',
            };
            FinishingItem::create(['property_id' => $p->id, 'item_key' => $key, 'status' => $status]);
        }

        $this->images($p);
    }

    /**
     * صور العقار: صور حقيقية (واجهة + غرفة نوم + مطبخ + حمام) من مكتبة database/seed-images.
     * بتتخزن في public/uploads/properties/{id}/ بنفس مسار الرفع الحقيقي (من غير storage).
     * صور مؤقتة للتجربة — استبدلها بصور العقارات الحقيقية متى أحببت (انظر README).
     */
    private function images(Property $p): void
    {
        $dir = database_path('seed-images');
        $processor = app(ImageProcessor::class);
        $house = sprintf('house-%02d', ($p->id % 40) + 1);
        $files = array_map(fn ($k) => "{$house}-{$k}.jpg", ['frontal', 'bedroom', 'kitchen', 'bathroom']);

        foreach ($files as $sort => $file) {
            if (! is_file("$dir/$file")) {
                continue;
            }
            PropertyImage::create([
                'property_id' => $p->id,
                // نعالج كل صورة مرة واحدة ونكتب نسخها لكل عقار (أسرع بكتير)
                'path' => $processor->write($this->pool[$file] ??= $processor->process(file_get_contents("$dir/$file")), 'properties/'.$p->id),
                'is_cover' => $sort === 0,
                'sort' => $sort,
            ]);
        }
    }
}
