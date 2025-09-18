<?php

namespace Tests\Feature;

use App\Models\Patronyme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatronymeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_patronyme()
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $response = $this->actingAs($admin)->post('/patronymes', [
            'nom' => 'Martin',
            'origine' => 'France',
            'signification' => 'Dérivé de Mars',
            'frequence' => 1000,
        ]);

        $response->assertRedirect('/patronymes');
        $this->assertDatabaseHas('patronymes', ['nom' => 'Martin']);
    }

    public function test_regular_user_cannot_create_patronyme()
    {
        $user = User::factory()->create(['role' => User::ROLE_USER]);

        $response = $this->actingAs($user)->post('/patronymes', [
            'nom' => 'Martin',
            'origine' => 'France',
        ]);

        $response->assertStatus(403);
    }

    public function test_search_functionality()
    {
        Patronyme::factory()->create(['nom' => 'Dupont']);
        Patronyme::factory()->create(['nom' => 'Martin']);

        $response = $this->get('/patronymes?search=Martin');

        $response->assertSee('Martin');
        $response->assertDontSee('Dupont');
    }

    public function test_api_returns_patronymes()
    {
        Patronyme::factory()->count(5)->create();

        $response = $this->getJson('/api/patronymes');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
}
