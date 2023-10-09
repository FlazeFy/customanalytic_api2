<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Books>
 */
class BooksFactory extends Factory
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
            'title' => fake()->sentence(), 
            'author' => fake()->name(),
            'reviewer' => fake()->name(), 
            'review_date' => Generator::getRandomDate($ran, 'date'), 

            // Properties
            'created_at' => Generator::getRandomDate(1, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran)
        ];
    }
}
