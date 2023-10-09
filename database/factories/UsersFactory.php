<?php

namespace Database\Factories;

use App\Helpers\Generator;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UsersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ran = mt_rand(0, 1);
        $ctx = "users";

        return [
            'id' => Generator::getUUID(), 
            'username' => fake()->username(), 
            'fullname' => fake(),
            'role' => Generator::getRandomRoleType($ctx),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => Generator::getRandomDate($ran, 'datetime'), 
            'password' => fake()->password(), 

            // Properties
            'created_at' => Generator::getRandomDate(1, 'datetime'),
            'updated_at' => Generator::getRandomDate($ran, 'datetime'),
        ];
    }
}
