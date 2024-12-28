<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Account;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(Account::class, function (Faker $faker) {

    // Create a user
    $this->user = factory(User::class)->create();

    return [
        'user_id' => $this->user->id,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'verified' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(Account::class, function (Account $account, Faker $faker) {

    $account->user = $this->user;
    return $account;
});
