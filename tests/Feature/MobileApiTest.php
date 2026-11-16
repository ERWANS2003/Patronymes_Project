<?php

namespace Tests\Feature;

use App\Models\Patronyme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_patronymes()
    {
        // Create test data
        $patronyme = Patronyme::factory()->create([
            'nom' => 'TestPatronyme',
            'signification' => 'Test Signification',
            'origine' => 'Test Origine',
        ]);

        // Make API request
        $response = $this->getJson('/api/v1/mobile/patronymes?per_page=5');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'nom',
                        'signification',
                        'origine',
                        'created_at',
                    ]
                ],
                'pagination' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'has_more'
                ]
            ]);
    }

    /** @test */
    public function it_can_show_a_single_patronyme()
    {
        $patronyme = Patronyme::factory()->create([
            'nom' => 'SingleTest',
            'signification' => 'Single Test Signification',
            'origine' => 'Test Origin',
        ]);

        $response = $this->getJson("/api/v1/mobile/patronymes/{$patronyme->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $patronyme->id,
                    'nom' => 'SingleTest',
                    'signification' => 'Single Test Signification',
                    'origine' => 'Test Origin',
                ]
            ]);
    }
}
