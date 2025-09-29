<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_requires_admin(): void
    {
        $this->get('/admin/export')->assertStatus(302);

        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/admin/export')->assertStatus(403);
    }

    public function test_import_requires_admin(): void
    {
        $this->post('/admin/import')->assertStatus(302);

        $user = User::factory()->create();
        $this->actingAs($user);
        $this->post('/admin/import')->assertStatus(403);
    }

    public function test_admin_can_access_import_export_pages(): void
    {
        $user = User::factory()->create();
        if (method_exists($user, 'forceFill')) {
            $user->forceFill(['role' => 'admin'])->save();
        }

        $this->actingAs($user);
        $this->get('/admin/import')->assertOk();
        $this->get('/admin/export')->assertStatus(200);
    }

    public function test_import_validation(): void
    {
        $user = User::factory()->create();
        if (method_exists($user, 'forceFill')) {
            $user->forceFill(['role' => 'admin'])->save();
        }
        $this->actingAs($user);

        $this->post('/admin/import', [])->assertSessionHasErrors(['file']);
    }
}
