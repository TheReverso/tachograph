<?php

namespace Database\Factories;

use App\Models\Freight;
use Illuminate\Database\Eloquent\Factories\Factory;

class FreightFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Freight::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'freight_name' => $this->faker->name,
            'freight_speditor_name' => $this->faker->name,
            'freight_weights' => $this->faker->randomNumber()
        ];
    }
}
