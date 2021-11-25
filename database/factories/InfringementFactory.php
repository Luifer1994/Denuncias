<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InfringementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'Tala en recorrido',
            "state" => 1
        ];
    }
}
