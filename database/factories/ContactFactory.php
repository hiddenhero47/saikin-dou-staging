<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contact;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(Contact::class, function (Faker $faker) {

    // Create a user
    $this->user = factory(User::class)->create();

    return [
        'user_id' => $this->user->id,
        'title' => $faker->title,
        'first_name' => $this->user->name,
        'last_name' => $this->user->name,
        'email' => $this->user->email,
        'phone' => $faker->numerify('#############'),
        'address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => Str::limit($faker->country, 50, ''),
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
        'zip' => $faker->postcode,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
