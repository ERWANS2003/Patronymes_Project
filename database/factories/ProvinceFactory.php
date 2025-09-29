<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Province>
 */
class ProvinceFactory extends Factory
{
    protected $model = Province::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->city(),
            'region_id' => Region::query()->inRandomOrder()->value('id') ?? Region::factory()->create()->id,
        ];
    }
}
