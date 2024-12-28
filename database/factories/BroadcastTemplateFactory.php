<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Account;
use App\Models\BroadcastTemplate;
use App\Models\Group;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(BroadcastTemplate::class, function (Faker $faker) {

    // Create an account
    $this->account = factory(Account::class)->create();

    // Create a group
    $this->group = factory(Group::class)->create(['user_id' => $this->account->user_id]);

    return [
        'id' => (string) Str::uuid(),
        'user_id' => $this->account->user_id,
        'account_id' => $this->account->id,
        'message' => $faker->sentence(9),
        'pictures' => [$faker->imageUrl(400, 400, 'food'),$faker->imageUrl(400, 400, 'food')],
        'videos' => [$faker->url(),$faker->url()],
        'preview_phone'=>  $faker->numerify('#############'),
        'contact_group_start_date' => $faker->dateTimeBetween('-03 days', 'now'),
        'contact_group_end_date' => $faker->dateTimeBetween('now', '+03 days'),
        'contact_group_id' => $this->group->id,
        'whatsapp_group_names' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(BroadcastTemplate::class, function (BroadcastTemplate $broadcast_template, Faker $faker) {

    $broadcast_template->user = $this->account->user;
    $broadcast_template->account = $this->account;
    $broadcast_template->group = $this->group;
    return $broadcast_template;
});
