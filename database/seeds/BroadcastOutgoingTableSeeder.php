<?php

use App\Models\BroadcastOutgoing;
use Illuminate\Database\Seeder;

class BroadcastOutgoingTableSeeder extends Seeder
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
            factory(BroadcastOutgoing::class, 10)->create();
        }
    }
}
