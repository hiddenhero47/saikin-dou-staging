<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Setting;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Setting::class, function (Faker $faker) {

    // Create a user without triggering any associated creation events
    $this->user = User::withoutEvents(function () {
        return factory(User::class)->create();
    });

    return [
        'user_id' => $this->user->id,
        'messages_before_pause' => $faker->randomDigitNot(0),
        'minutes_before_resume' => $faker->time(),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(Setting::class, function (Setting $setting, Faker $faker) {

    $setting->user = $this->user;
    return $setting;
});
