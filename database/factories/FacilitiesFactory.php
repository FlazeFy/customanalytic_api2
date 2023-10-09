<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facilities>
 */
class FacilitiesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ran = mt_rand(0, 1);
        $ctx = "facilities";

        return [
            'id' => Generator::getUUID(), 
            'name' => fake()->sentence(), 
            'type' => Generator::getRandomRoleType($ctx),
            'location' => fake()->address(), 
            'country' => Generator::getRandomCountry(),
            'coordinate' => Generator::getRandomCoordinate(), 

            // Properties
            'created_at' => Generator::getRandomDate(1, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran)
        ];
    }
}
