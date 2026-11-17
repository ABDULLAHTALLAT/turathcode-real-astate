<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\User;
use App\Support\ArabicData;
use App\Support\EgyptData;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Property> */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $loc = EgyptData::random();
        $type = fake()->randomElement(array_keys(catalog('property_types')));
        $stage = fake()->randomElement(['concrete', 'red_brick', 'plaster', 'plaster', 'half_finished', 'finished']);
        $residential = in_array($type, ['apartment', 'villa', 'duplex', 'floor']);
        $area = match ($type) {
            'apartment' => fake()->numberBetween(70, 200),
            'villa' => fake()->numberBetween(250, 600),
            'duplex' => fake()->numberBetween(180, 320),
            'floor' => fake()->numberBetween(150, 400),
            'building' => fake()->numberBetween(500, 1500),
            'shop' => fake()->numberBetween(25, 120),
            'office' => fake()->numberBetween(50, 250),
            'warehouse' => fake()->numberBetween(300, 2000),
            default => fake()->numberBetween(200, 900),
        };
        $perMeter = fake()->numberBetween(35, 130);
        $market = (int) (round($area * $perMeter / 500) * 500);
        $market = max($market, 1500);
        $offered = (int) (round($market * fake()->randomFloat(2, 0.6, 0.85) / 250) * 250);

        $typeLabel = catalog('property_types')[$type];
        $stageLabel = catalog('finishing_stages')[$stage]['label'];

        return [
            'owner_id' => User::factory()->owner(),
            'title' => "{$typeLabel} {$area}م {$stageLabel} في {$loc['city']}",
            'property_type' => $type,
            'governorate' => $loc['governorate'],
            'city' => $loc['city'],
            'district' => $loc['district'],
            'street' => ArabicData::pick(ArabicData::STREETS),
            'building_number' => (string) fake()->numberBetween(1, 120),
            'floor_number' => $residential ? fake()->randomElement(['الأرضي', 'الأول', 'الثاني', 'الثالث', 'الرابع', 'الخامس']) : null,
            'unit_number' => $residential ? (string) fake()->numberBetween(1, 12) : null,
            'landmark' => ArabicData::pick(ArabicData::LANDMARKS),
            'latitude' => $loc['latitude'],
            'longitude' => $loc['longitude'],
            'area_m2' => $area,
            'rooms' => $residential ? fake()->numberBetween(2, 6) : null,
            'bathrooms' => $residential ? fake()->numberBetween(1, 4) : fake()->numberBetween(1, 2),
            'floors_count' => in_array($type, ['building', 'villa']) ? fake()->numberBetween(2, 8) : null,
            'finishing_stage' => $stage,
            'finishing_notes' => 'العقار فاضي من سنين ومحتاج استكمال البنود المذكورة في جدول التشطيب.',
            'description' => "{$typeLabel} موجودة ومعطلة بالكامل في {$loc['city']} - {$loc['governorate']}، المالك مستعد يأجرها بسعر أقل من السوق مقابل مدة إيجار طويلة واستكمال التشطيب.",
            'utilities' => fake()->randomElements(array_keys(catalog('utilities')), fake()->numberBetween(2, 5)),
            'allowed_purposes' => fake()->randomElements(array_keys(catalog('purposes')), fake()->numberBetween(1, 3)),
            'vacant_since_years' => fake()->numberBetween(1, 15),
            'market_rent' => $market,
            'offered_rent' => $offered,
            'min_lease_years' => $min = fake()->numberBetween(1, 3),
            'max_lease_years' => $min + fake()->numberBetween(2, 7),
            'rent_free_months' => fake()->randomElement([0, 1, 2, 3, 6]),
            'annual_increase_percent' => fake()->randomElement([0, 3, 5, 7, 10]),
            'finishing_by' => fake()->randomElement(array_keys(catalog('finishing_by'))),
            'status' => 'approved',
            'is_featured' => fake()->boolean(12),
            'views' => fake()->numberBetween(0, 900),
            'terms_accepted_at' => now(),
            'approved_at' => now()->subDays(fake()->numberBetween(0, 60)),
        ];
    }

    /** العنوان والوصف لازم يطابقوا المحافظة والمدينة النهائية حتى لو اتغيرت بـ state */
    public function configure(): static
    {
        return $this->afterMaking(function (Property $p) {
            $type = catalog('property_types')[$p->property_type];
            $stage = catalog('finishing_stages')[$p->finishing_stage]['label'];
            $p->title = "{$type} {$p->area_m2}م {$stage} في {$p->city}";
            $p->description = "{$type} موجودة ومعطلة بالكامل في {$p->city} - {$p->governorate}، المالك مستعد يأجرها بسعر أقل من السوق مقابل مدة إيجار طويلة واستكمال التشطيب.";
        });
    }

    public function stage(string $stage): static { return $this->state(['finishing_stage' => $stage]); }
    public function featured(): static { return $this->state(['is_featured' => true]); }
    public function pending(): static { return $this->state(['status' => 'pending', 'approved_at' => null]); }
    public function rejected(): static { return $this->state(['status' => 'rejected', 'approved_at' => null, 'reject_reason' => 'الصور غير واضحة، برجاء رفع صور أوضح للعقار.']); }
    public function rented(): static { return $this->state(['status' => 'rented']); }
    public function in(string $governorate): static { return $this->state(fn () => EgyptData::random($governorate)); }
}
