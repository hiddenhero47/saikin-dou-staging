<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contact;
use App\Models\Group;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(Group::class, function (Faker $faker) {

    // Create a user
    $this->user = factory(User::class)->create();

    // Create a contacts
    $this->contacts = factory(Contact::class, 6)->create();

    return [
        'user_id' => $this->user->id,
        'title' => $faker->word,
        'group_contacts' => $this->contacts->pluck('id')->toArray(),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(Group::class, function (Group $group, Faker $faker) {

    $group->user = $this->user;
    $group->contacts = $this->contacts;
    return $group;
});
