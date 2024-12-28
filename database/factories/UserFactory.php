<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

// Define user model factory
$factory->define(User::class, function (Faker $faker) {

    return [
        'id' => (string) Str::uuid(),
        'name' => uniqid().$faker->unique()->name,
        'email' => uniqid().$faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'providers_allowed' => [],
        'providers_disallowed' => [],
        'providers_details' => [],
        'groups' => [],
        'created_at' => now(),
        'updated_at' => now(),
    ];
});