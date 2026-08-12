<?php

namespace Database\Factories;

use App\Models\Passport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Passport>
 */
class PassportFactory extends Factory
{
    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'citizen_id' => null,
            'passport_number' => fake()->unique()->numerify('##########'),
            'issue_date' => $issueDate,
            'expiry_date' => fake()->dateTimeBetween($issueDate, '+10 years'),
            'type' => fake()->randomElement([
                'ordinary',
                'diplomatic',
                'official',
            ]),
            'status' => fake()->randomElement([
                'pending',
                'approved',
                'rejected',
                'active',
                'expired',
                'cancelled',
                'lost',
                'damaged',
            ]),
            'notes' => fake()->optional()->sentence(),
            'qr_code' => fake()->optional()->sha256(),
            'print_count' => fake()->numberBetween(0, 5),
            'issued_by' => User::inRandomOrder()->first()->id,
            'approved_by' => fake()->boolean(70)
                ? User::inRandomOrder()->first()?->id
                : null,
            'approved_at' => fake()->boolean(70)
                ? fake()->dateTimeBetween($issueDate, 'now')
                : null,
        ];
    }
}
