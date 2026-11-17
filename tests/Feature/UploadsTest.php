<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/** الصور بتتخزن في public/uploads (منظمة بالعقار) مش في storage ولا قاعدة البيانات */
class UploadsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path('uploads/properties'));
        File::deleteDirectory(public_path('uploads/avatars'));
        parent::tearDown();
    }

    private function payload(array $extra = []): array
    {
        return $extra + [
            'title' => 'شقة 100م على المحارة في طنطا', 'property_type' => 'apartment', 'governorate' => 'الغربية', 'city' => 'طنطا',
            'area_m2' => 100, 'finishing_stage' => 'plaster', 'allowed_purposes' => ['family'], 'market_rent' => 8000,
            'offered_rent' => 6000, 'min_lease_years' => 1, 'max_lease_years' => 5, 'finishing_by' => 'tenant', 'terms' => 1,
        ];
    }

    public function test_owner_upload_goes_to_public_uploads_folder_per_property(): void
    {
        $this->seed();
        $owner = User::where('email', 'owner@example.com')->firstOrFail();

        $this->actingAs($owner)->post('/owner/properties', $this->payload([
            'images' => [UploadedFile::fake()->image('a.jpg', 1200, 800)],
        ]))->assertSessionHasNoErrors();

        $property = Property::where('owner_id', $owner->id)->latest('id')->firstOrFail();
        $img = $property->images()->firstOrFail();

        $this->assertStringStartsWith("uploads/properties/{$property->id}/", $img->path);
        foreach (['thumb', 'medium', 'large'] as $v) {
            $this->assertFileExists(public_path($img->path."-$v.webp"));
        }
        $this->assertStringContainsString('/uploads/properties/'.$property->id.'/', $img->thumb_url);
        $this->assertDirectoryDoesNotExist(storage_path('app/public/properties'));

        $img->delete();
        $this->assertFileDoesNotExist(public_path($img->path.'-thumb.webp'));
    }

    public function test_seeded_properties_all_have_cover_and_gallery_files(): void
    {
        $this->seed();

        $this->assertSame(0, Property::doesntHave('images')->count());
        $img = PropertyImage::where('path', 'like', 'uploads/properties/%')->first();
        $this->assertNotNull($img);
        $this->assertFileExists(public_path($img->path.'-large.webp'));
        $this->assertGreaterThanOrEqual(4, Property::first()->images()->count());
    }
}
