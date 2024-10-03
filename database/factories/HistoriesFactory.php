<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Histories>
 */
class HistoriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ctx = "histories";

        return [
            'id' => Generator::getUUID(), 
            'history_type' => Generator::getRandomRoleType($ctx), 
            'body' => fake()->sentence(),

            // Properties
            'created_at' => Generator::getRandomDate(0, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
        ];
    }
}
