<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Benefit;
use App\Models\PaymentPlan;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(PaymentPlan::class, function (Faker $faker) {

    // Create a contacts
    $this->benefits = factory(Benefit::class, 6)->create();

    return [
        'name' => $faker->unique()->word,
        'level' =>  $faker->numberBetween(1,99),
        'payment_plan_benefits' => $this->benefits->mapWithKeys(function($item){return [$item->id => ['value'=>rand(10,100)]];})->toArray(),
        'amount' => $faker->numberBetween(100,50000),
        'discount' => $faker->numberBetween(10,100),
        'currency' => substr($faker->currencyCode,0,3),
        'visibility' => 'public',
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(PaymentPlan::class, function (PaymentPlan $payment_plan, Faker $faker) {

    $payment_plan->benefits = $this->benefits;
    return $payment_plan;
});