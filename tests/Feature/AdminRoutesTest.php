<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(302)->assertRedirect('/login');
    }

    public function test_non_admin_forbidden(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes(): void
    {
        $user = User::factory()->create();
        // Ensure isAdmin() returns true (set role if needed)
        if (method_exists($user, 'forceFill')) {
            $user->forceFill(['role' => 'admin'])->save();
        }
        $this->actingAs($user);

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/import')->assertOk();
        $this->get('/admin/export')->assertStatus(200);
    }
}
