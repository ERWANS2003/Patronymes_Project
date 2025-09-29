<?php

namespace Tests\Feature;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use App\Models\GroupeEthnique;
use App\Models\Langue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatronymeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer les données de référence nécessaires
        $this->artisan('db:seed', ['--class' => 'RegionProvinceCommuneSeeder']);
        $this->artisan('db:seed', ['--class' => 'EthnieSeeder']);
        $this->artisan('db:seed', ['--class' => 'GroupeEthniqueEthnieSeeder']);
        $this->artisan('db:seed', ['--class' => 'LangueModeTransmissionSeeder']);
    }

    public function test_can_create_patronyme_with_real_data()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $data = [
            'nom' => 'Ouédraogo',
            'origine' => 'Burkina Faso',
            'signification' => 'Nom traditionnel mossi',
            'histoire' => 'Patronyme ancestral des rois mossi',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'frequence' => 100,
        ];

        $patronyme = Patronyme::create($data);

        $this->assertDatabaseHas('patronymes', $data);
        $this->assertEquals('Ouédraogo', $patronyme->nom);
        $this->assertEquals('Burkina Faso', $patronyme->origine);
    }

    public function test_can_search_patronyme_with_real_data()
    {
        // Créer des patronymes avec des données réelles
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        Patronyme::create([
            'nom' => 'Ouédraogo',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        Patronyme::create([
            'nom' => 'Sawadogo',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        $results = Patronyme::where('nom', 'like', '%Ouédraogo%')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Ouédraogo', $results->first()->nom);
    }

    public function test_can_filter_by_region_with_real_data()
    {
        $region1 = Region::first();
        $region2 = Region::skip(1)->first();
        $departement1 = Departement::where('region_id', $region1->id)->first();
        $departement2 = Departement::where('region_id', $region2->id)->first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        Patronyme::create([
            'nom' => 'Patronyme1',
            'region_id' => $region1->id,
            'departement_id' => $departement1->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        Patronyme::create([
            'nom' => 'Patronyme2',
            'region_id' => $region1->id,
            'departement_id' => $departement1->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        Patronyme::create([
            'nom' => 'Patronyme3',
            'region_id' => $region2->id,
            'departement_id' => $departement2->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        $results = Patronyme::where('region_id', $region1->id)->get();

        $this->assertCount(2, $results);
        $this->assertEquals($region1->id, $results->first()->region_id);
    }
}
