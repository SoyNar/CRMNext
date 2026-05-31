<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->company(),
            'nit'        => fake()->numerify('#########-#'),
            'phone'      => fake()->phoneNumber(),
            'url_page'   => fake()->url(),
            'status'     => fake()->randomElement(['active', 'inactive', 'prospect']),
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
