<?php

namespace Database\Factories;

use App\Couple;
use App\User;
use App\UserMetadata;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name;

        return [
            'id'         => $this->faker->uuid,
            'name'       => $name,
            'nickname'   => $name,
            'gender_id'  => rand(1, 2),
            'manager_id' => $this->faker->uuid,
        ];
    }

    /**
     * Get the state for male gender.
     */
    public function male(): static
    {
        return $this->state(fn () => ['gender_id' => 1]);
    }

    /**
     * Get the state for female gender.
     */
    public function female(): static
    {
        return $this->state(fn () => ['gender_id' => 2]);
    }
}

class CoupleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'         => $this->faker->uuid,
            'husband_id' => function () {
                return User::factory()->male()->create()->id;
            },
            'wife_id'    => function () {
                return User::factory()->female()->create()->id;
            },
            'manager_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}

class UserMetadataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'      => $this->faker->uuid,
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'key'     => $this->faker->name,
            'value'   => $this->faker->sentence,
        ];
    }
}
