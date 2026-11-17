<?php

namespace Database\Seeders;

use App\Models\FinishingItem;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use App\Support\EgyptData;
use Illuminate\Database\Seeder;

/** عقارات معطلة من كل محافظات مصر: كل محافظة ليها عدد عقارات بمراحل تشطيب مختلفة */
class EgyptPropertiesSeeder extends Seeder
{
    public const PER_GOVERNORATE = 6;

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

        PropertyImage::create(['property_id' => $p->id, 'path' => 'images/covers/'.$p->property_type.'.svg', 'is_cover' => true, 'sort' => 0]);
    }
}
