<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Helpers\Generator;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedbacks>
 */
class FeedbacksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ran = mt_rand(0, 1);
        $rate = mt_rand(0, 5);

        return [
            'id' => Generator::getUUID(), 
            'stories_id' => Generator::getRandomID("stories"), 
            'body' => fake()->sentence(),
            'rate' => $rate,

            // Properties
            'created_at' => Generator::getRandomDate(0, 'datetime'), 
            'created_by' => Generator::getRandomUser(0), 
        ];
    }
}
