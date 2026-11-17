<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\RentalRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_pages_load(): void
    {
        foreach (['/', '/properties', '/how-it-works', '/about', '/terms', '/contact', '/login', '/register'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_dashboards_per_role(): void
    {
        foreach (['admin@turathcode.com' => 'Admin@12345', 'owner@example.com' => 'password', 'tenant@example.com' => 'password'] as $email => $pw) {
            $user = User::where('email', $email)->firstOrFail();
            $this->actingAs($user)->get('/dashboard')->assertOk();
            auth()->logout();
        }
    }

    public function test_tenant_cannot_open_admin_or_owner_areas(): void
    {
        $tenant = User::where('email', 'tenant@example.com')->first();
        $this->actingAs($tenant)->get('/admin/users')->assertForbidden();
        $this->actingAs($tenant)->get('/owner/properties')->assertForbidden();
    }

    public function test_full_negotiation_and_commission_flow(): void
    {
        $tenant = User::where('email', 'tenant@example.com')->first();
        $owner = User::where('email', 'owner@example.com')->first();
        $admin = User::where('email', 'admin@turathcode.com')->first();
        $property = Property::where('status', 'approved')->first();
        $this->assertNotNull($property);

        $this->actingAs($tenant)->get('/properties/'.$property->slug)->assertOk();

        $this->actingAs($tenant)->post("/properties/{$property->id}/request", [
            'purpose' => $property->allowed_purposes[0], 'marital_status' => 'married', 'occupants' => 4,
            'proposed_rent' => 10000, 'lease_years' => $property->min_lease_years, 'terms' => 1,
            'message' => 'كلمني على 01012345678',
        ])->assertSessionHasNoErrors();

        $rr = RentalRequest::firstOrFail();
        $this->assertStringNotContainsString('01012345678', $rr->message, 'أرقام التواصل لازم تتخفى قبل الدفع');

        $this->actingAs($owner)->post("/requests/{$rr->id}/counter", ['proposed_rent' => 11000, 'lease_years' => $property->min_lease_years])->assertSessionHasNoErrors();
        $this->actingAs($tenant)->post("/requests/{$rr->id}/accept")->assertSessionHasNoErrors();

        $rr->refresh();
        $this->assertSame('accepted', $rr->status);
        $this->assertGreaterThan(0, (float) $rr->owner_commission);
        $this->assertGreaterThan(0, (float) $rr->tenant_commission);

        $this->actingAs($admin)->post("/admin/requests/{$rr->id}/paid", ['party' => 'owner'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post("/admin/requests/{$rr->id}/paid", ['party' => 'tenant'])->assertSessionHasNoErrors();
        $this->assertSame('completed', $rr->fresh()->status);
    }
}
