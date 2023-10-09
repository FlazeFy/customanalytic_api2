<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Weapons>
 */
class WeaponsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ran = mt_rand(0, 1);
        $ctx = "weapons";

        return [
            'id' => Generator::getUUID(), 
            'name' => fake()->sentence(), 
            'type' => Generator::getRandomRoleType($ctx), 
            'location' => fake()->address(), 
            'country' => Generator::getRandomCountry(),

            // Properties
            // Properties
            'created_at' => Generator::getRandomDate(0, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran)
        ];
    }
}
