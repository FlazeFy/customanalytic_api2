<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AdminsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ran = mt_rand(0, 1);

        return [
            'id' => Generator::getUUID(), 
            'username' => fake()->username(),
            'fullname' => fake()->name(), 
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(), 

            // Properties
            'created_at' => Generator::getRandomDate(0), 
            'updated_at' => Generator::getRandomDate($ran)
        ];
    }
}
