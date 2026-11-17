<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Support\EgyptData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_egypt_data_covers_all_27_governorates(): void
    {
        $this->assertCount(27, EgyptData::governorates());
        foreach (EgyptData::governorates() as $gov) {
            $this->assertNotEmpty(EgyptData::cities($gov), "{$gov} بدون مدن");
        }
    }

    public function test_seeded_properties_span_every_governorate_with_matching_titles(): void
    {
        $this->seed();

        $this->assertSame(27, Property::distinct('governorate')->count('governorate'));
        Property::all()->each(fn ($p) => $this->assertStringContainsString($p->city, $p->title));
    }
}
