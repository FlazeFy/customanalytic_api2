<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicles>
 */
class VehiclesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ran = mt_rand(0, 1);
        $ctx = "vehicles";

        return [
            'id' => Generator::getUUID(), 
            'name' => fake()->sentence(), 
            'primary_role' => Generator::GetRandomRoleType($ctx),
            'manufacturer' => fake()->sentence(),
            'country' => Generator::getRandomCountry(),

            // Properties
            'created_at' => Generator::getRandomDate(0, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran)
        ];
    }
}
