<?php

namespace Tests\Feature;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatronymeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_patronyme()
    {
        $region = Region::factory()->create();
        $departement = Departement::factory()->create(['region_id' => $region->id]);

        $data = [
            'nom' => 'Dupont',
            'origine' => 'France',
            'signification' => 'Fils de Pont',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
        ];

        $patronyme = Patronyme::create($data);

        $this->assertDatabaseHas('patronymes', $data);
        $this->assertEquals('Dupont', $patronyme->nom);
        $this->assertEquals('France', $patronyme->origine);
    }

    public function test_can_search_patronyme()
    {
        Patronyme::factory()->create(['nom' => 'Martin']);
        Patronyme::factory()->create(['nom' => 'Dupont']);
        Patronyme::factory()->create(['nom' => 'Durand']);

        $results = Patronyme::search('Martin')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Martin', $results->first()->nom);
    }

    public function test_can_filter_by_region()
    {
        $region1 = Region::factory()->create();
        $region2 = Region::factory()->create();

        Patronyme::factory()->create(['region_id' => $region1->id]);
        Patronyme::factory()->create(['region_id' => $region1->id]);
        Patronyme::factory()->create(['region_id' => $region2->id]);

        $results = Patronyme::byRegion($region1->id)->get();

        $this->assertCount(2, $results);
        $this->assertEquals($region1->id, $results->first()->region_id);
    }
}
