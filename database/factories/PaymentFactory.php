<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Account;
use App\Models\Payment;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

// Define restaurant order factory
$factory->define(Payment::class, function (Faker $faker) {

    // Create a user
    $this->user = factory(User::class)->create();

    // Create a account
    $this->account = factory(Account::class)->create(['user_id' => $this->user->id]);

    return [
        'id' => (string) Str::uuid(),
        'user_id' => $this->user->id,
        'account_id' => $this->account->id,
        'pfm' => (string) Str::random(15),
        'type' => ['standard','collect'][array_rand(['standard','collect'])],
        'currency' => substr($faker->currencyCode,0,3),
        'amount' => $faker->numberBetween(100,50000),
        'paid' => '1',
        'confirmed' => '1',
        'method' => 'paypal',
        'status' => 'success',
    ];
});

$factory->afterCreating(Food::class, function (Payment $payment, Faker $faker) {

    $payment->account = $this->account;
    $payment->user = $this->user;

    return $payment;
});
