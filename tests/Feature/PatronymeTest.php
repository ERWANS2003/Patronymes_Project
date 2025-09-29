<?php

namespace Tests\Feature;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use App\Models\GroupeEthnique;
use App\Models\Langue;
use App\Models\User;
use App\Models\Commentaire;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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

    public function test_patronyme_soft_delete()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $patronyme = Patronyme::create([
            'nom' => 'Test Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        $patronyme->delete();

        $this->assertSoftDeleted('patronymes', ['id' => $patronyme->id]);
        $this->assertDatabaseHas('patronymes', ['id' => $patronyme->id]);
    }

    public function test_patronyme_views_increment()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $patronyme = Patronyme::create([
            'nom' => 'Test Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'views_count' => 0,
        ]);

        $patronyme->incrementViews();

        $this->assertEquals(1, $patronyme->fresh()->views_count);
    }

    public function test_patronyme_search_scopes()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        Patronyme::create([
            'nom' => 'Popular Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'views_count' => 100,
            'is_featured' => true,
        ]);

        Patronyme::create([
            'nom' => 'Regular Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'views_count' => 10,
            'is_featured' => false,
        ]);

        // Test popular scope
        $popular = Patronyme::popular()->get();
        $this->assertCount(2, $popular);
        $this->assertEquals('Popular Patronyme', $popular->first()->nom);

        // Test featured scope
        $featured = Patronyme::featured()->get();
        $this->assertCount(1, $featured);
        $this->assertEquals('Popular Patronyme', $featured->first()->nom);
    }

    public function test_patronyme_advanced_search()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        Patronyme::create([
            'nom' => 'Male Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'patronyme_sexe' => 'M',
            'transmission' => 'pere',
            'frequence' => 50,
        ]);

        Patronyme::create([
            'nom' => 'Female Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'patronyme_sexe' => 'F',
            'transmission' => 'mere',
            'frequence' => 30,
        ]);

        // Test advanced search with filters
        $filters = [
            'patronyme_sexe' => 'M',
            'transmission' => 'pere',
            'min_frequence' => 40,
        ];

        $results = Patronyme::advancedSearch($filters)->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Male Patronyme', $results->first()->nom);
    }

    public function test_patronyme_cache_methods()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        Patronyme::create([
            'nom' => 'Cached Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'views_count' => 100,
        ]);

        // Test cache methods
        $popular = Patronyme::getCachedPopular(5);
        $this->assertCount(1, $popular);

        $recent = Patronyme::getCachedRecent(5);
        $this->assertCount(1, $recent);

        $featured = Patronyme::getCachedFeatured(5);
        $this->assertCount(0, $featured);
    }

    public function test_patronyme_relationships()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $patronyme = Patronyme::create([
            'nom' => 'Test Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        // Test relationships
        $this->assertInstanceOf(Region::class, $patronyme->region);
        $this->assertInstanceOf(Departement::class, $patronyme->departement);
        $this->assertInstanceOf(GroupeEthnique::class, $patronyme->groupeEthnique);
        $this->assertInstanceOf(Langue::class, $patronyme->langue);
    }

    public function test_patronyme_accessors()
    {
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $patronyme = Patronyme::create([
            'nom' => 'Test Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
            'views_count' => 100,
            'frequence' => 50,
        ]);

        // Test accessors
        $this->assertIsString($patronyme->full_location);
        $this->assertIsFloat($patronyme->search_score);
        $this->assertIsBool($patronyme->is_popular);
    }

    public function test_patronyme_comment_relationship()
    {
        $user = User::factory()->create();
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $patronyme = Patronyme::create([
            'nom' => 'Test Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        Commentaire::create([
            'contenu' => 'Test comment',
            'patronyme_id' => $patronyme->id,
            'utilisateur_id' => $user->id,
            'date_commentaire' => now(),
        ]);

        $this->assertCount(1, $patronyme->commentaires);
        $this->assertEquals('Test comment', $patronyme->commentaires->first()->contenu);
    }

    public function test_patronyme_favorite_relationship()
    {
        $user = User::factory()->create();
        $region = Region::first();
        $departement = Departement::first();
        $groupeEthnique = GroupeEthnique::first();
        $langue = Langue::first();

        $patronyme = Patronyme::create([
            'nom' => 'Test Patronyme',
            'region_id' => $region->id,
            'departement_id' => $departement->id,
            'groupe_ethnique_id' => $groupeEthnique->id,
            'langue_id' => $langue->id,
        ]);

        Favorite::create([
            'user_id' => $user->id,
            'patronyme_id' => $patronyme->id,
        ]);

        $this->assertTrue($patronyme->isFavoritedBy($user->id));
        $this->assertCount(1, $patronyme->favorites);
    }
}
