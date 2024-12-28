<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Role;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

// Define role model factory
$factory->define(Role::class, function (Faker $faker) {

    return [
        'name' => uniqid().$faker->unique()->word,
        'display_name' =>  $faker->sentence(3),
        'description' => $faker->sentence(6),
        'visibility' => 'public',
        'created_at' => now(),
        'updated_at' => now(),
    ];
});