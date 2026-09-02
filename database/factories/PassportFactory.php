<?php

namespace Database\Factories;

use App\Models\Passport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PassportFactory extends Factory
{
    protected $model = Passport::class;

    public function definition(): array
    {
        return [
            'citizen_id' => null,
            'passport_number' => fake()->unique()->numerify('#########'),
            'issue_date' => null,
            'expiry_date' => null,
            'type' => fake()->randomElement([
                'ordinary',
                'diplomatic',
                'official',
            ]),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
            'qr_code' => fake()->optional()->sha256(),
            'print_count' => 0,
            'issued_by' => User::inRandomOrder()->first()->id,
            'approved_by' => null,
            'approved_at' => null,
        ];
    }
}
