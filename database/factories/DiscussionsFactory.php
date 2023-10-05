<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Helpers\Generator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discussions>
 */
class DiscussionsFactory extends Factory
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
            'stories_id' => Generator::getRandomID("stories"), 
            'reply_id' => Generator::getRandomID("stories_discussion"),
            'body' => fake()->paragraph(),
            'attachment' => null,

            // Properties
            'created_at' => Generator::getRandomDate(0, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
            'updated_at' => Generator::getRandomDate($ran, 'datetime'), 
            'updated_by' => Generator::getRandomUser($ran)
        ];
    }
}
