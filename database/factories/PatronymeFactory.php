<?php

namespace Database\Factories;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatronymeFactory extends Factory
{
    protected $model = Patronyme::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'origine' => $this->faker->country,
            'signification' => $this->faker->sentence,
            'histoire' => $this->faker->paragraph,
            'region_id' => Region::factory(),
            'departement_id' => Departement::factory(),
            'frequence' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
