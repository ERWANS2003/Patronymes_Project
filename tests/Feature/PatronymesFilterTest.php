<?php

namespace Tests\Feature;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Province;
use App\Models\Commune;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatronymesFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_filters_by_search_and_region_province_commune(): void
    {
        $region = Region::factory()->create(['name' => 'Centre', 'code' => '03']);
        $province = Province::factory()->create(['nom' => 'Kadiogo', 'region_id' => $region->id]);
        $commune = Commune::factory()->create(['nom' => 'Ouagadougou', 'province_id' => $province->id]);

        $match = Patronyme::factory()->create(['nom' => 'OUEDRAOGO', 'region_id' => $region->id, 'province_id' => $province->id, 'commune_id' => $commune->id]);
        $other = Patronyme::factory()->create(['nom' => 'TRAORE']);

        $this->get('/patronymes?search=OUEDRAOGO&region_id='.$region->id.'&province_id='.$province->id.'&commune_id='.$commune->id)
            ->assertStatus(200)
            ->assertSee($match->nom)
            ->assertDontSee($other->nom);
    }
}
