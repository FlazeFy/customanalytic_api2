<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Helpers\Generator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Aircraft>
 */
class AircraftFactory extends Factory
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
            'name' => fake()->sentence(), 
            'primary_role' => Generator::getRandomRoleType(), 
            'manufacturer' => fake()->sentence(), 
            'country' => Generator::getRandomCountry(), 

            // Properties
            'created_at' => Generator::getRandomDate(0), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran), 
            'updated_by' => Generator::getRandomUser($ran) 
        ];
    }
}
