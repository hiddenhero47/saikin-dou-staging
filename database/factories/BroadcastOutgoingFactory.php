<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Broadcast;
use App\Models\BroadcastOutgoing;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Login adam, as an authenticated user is require for creation of some models
if (!Auth::user() && !Auth::attempt(['email' => 'blue@gmail.com', 'password' => 'blueblue'])) {
    exit();
}

$factory->define(BroadcastOutgoing::class, function (Faker $faker) {

    // Create a broadcast
    $this->broadcast = factory(Broadcast::class)->create();

    // Retrieve created contacts
    $contacts = $this->broadcast->group->contacts;

    return [
        'user_id' => $this->broadcast->user_id,
        'account_id' => $this->broadcast->account_id,
        'broadcast_id' => $this->broadcast->id,
        'reference' => (string) Str::uuid(),
        'contact_id' => $contacts[0]['id'],
        'whatsapp_group_name' => null,
        'batch' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});

$factory->afterCreating(BroadcastOutgoing::class, function (BroadcastOutgoing $broadcast_outgoing, Faker $faker) {

    $broadcast_outgoing->broadcast = $this->broadcast;
    return $broadcast_outgoing;
});