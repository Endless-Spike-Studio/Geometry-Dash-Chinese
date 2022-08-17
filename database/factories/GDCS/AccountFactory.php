<?php

namespace Database\Factories\GDCS;

use App\Models\GDCS\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $faker = $this->faker;
        $unique = $faker->unique();

        return [
            'name' => $unique->userName,
            'password' => Hash::make($faker->password),
            'email' => $unique->safeEmail,
            'email_verified_at' => now(),
        ];
    }

    public function withPassword(string $password): AccountFactory
    {
        return $this->state([
            'password' => Hash::make($password),
        ]);
    }

    public function unverified(): AccountFactory
    {
        return $this->state([
            'email_verified_at' => null,
        ]);
    }
}
