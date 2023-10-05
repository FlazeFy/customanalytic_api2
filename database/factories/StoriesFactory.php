<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Helpers\Generator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class StoriesFactory extends Factory
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
            'main_title' => fake()->sentence(), 
            'is_finished' => $ran,
            'story_type' => Generator::getRandomRoleType(),
            'date_start' => fake()->dateTimeBetween('now', '+2 days'),
            'date_end' => fake()->dateTimeBetween('+7 days', '+7 years'),
            'story_result' => null,
            'story_location' => Generator::getRandomLocation(),
            'story_tag' => Generator::getRandomTag(),
            'story_detail' => fake()->paragraph(),
            'story_stats' => null, // For now
            'story_reference' => Generator::getRandomReference(),

            // Properties
            'created_at' => Generator::getRandomDate(0, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran),
            'deleted_at' => Generator::getRandomDate($ran, 'datetime'), 
            'deleted_by' => Generator::getRandomUser($ran)
        ];
    }
}
