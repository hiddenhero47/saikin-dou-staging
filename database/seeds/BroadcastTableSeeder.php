<?php

use App\Models\Broadcast;
use Illuminate\Database\Seeder;

class BroadcastTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run broadcast factory
        if (config('app.env') === 'local') {
            factory(Broadcast::class, 10)->create();
        }
    }
}
