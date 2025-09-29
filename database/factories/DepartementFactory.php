<?php

namespace Database\Factories;

use App\Models\Departement;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartementFactory extends Factory
{
    protected $model = Departement::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'code' => $this->faker->unique()->regexify('[A-Z]{3}'),
            'region_id' => Region::factory(),
        ];
    }
}
