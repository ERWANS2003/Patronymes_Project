<?php

namespace Database\Factories;

use App\Models\Commune;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Commune>
 */
class CommuneFactory extends Factory
{
    protected $model = Commune::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->citySuffix(),
            'province_id' => Province::query()->inRandomOrder()->value('id') ?? Province::factory()->create()->id,
        ];
    }
}
