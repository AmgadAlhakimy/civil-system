<?php

namespace Database\Factories;

use App\Models\Citizen;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class CitizenFactory extends Factory
{
    public function definition(): array
    {
        $faker = FakerFactory::create('ar_SA');

        $gender = $this->faker->randomElement([
            'male',
            'female',
        ]);

        return [
            'national_id' => $this->faker->unique()->numerify('###########'),
            'first_name' => $faker->firstName(),
            'middle_name' => $faker->firstNameMale(),
            'last_name' => $faker->lastName(),
            'father_name' => $faker->firstNameMale(),
            'mother_name' => $faker->firstNameFemale(),
            'photo' => 'citizens/photos/01KZRJ6N0ZCQ1B69ZRDCEHX4FF.jpeg',
            'birth_date' => $this->faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'birth_place' => $faker->city(),
            'gender' => $gender,
            'marital_status' => $this->faker->randomElement([
                'single',
                'married',
                'divorced',
                'widowed',
            ]),
            'occupation' => $faker->jobTitle(),
            'address' => $faker->address(),
            'phone' => '7' . $this->faker->unique()->numerify('########'),
            'email' => $this->faker->unique()->safeEmail(),
            'is_active' => $this->faker->boolean(90),
            'verified_at' => now(),
            'verified_by' => null,
        ];
    }
}
