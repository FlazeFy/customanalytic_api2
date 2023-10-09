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
        $ctx = "aircraft";

        return [
            'id' => Generator::getUUID(), 
            'name' => fake()->sentence(), 
            'primary_role' => Generator::getRandomRoleType($ctx), 
            'manufacturer' => fake()->sentence(), 
            'country' => Generator::getRandomCountry(), 

            // Properties
            'created_at' => Generator::getRandomDate(1, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran)
        ];
    }
}
