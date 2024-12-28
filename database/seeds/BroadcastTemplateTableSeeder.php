<?php

use App\Models\BroadcastTemplate;
use Illuminate\Database\Seeder;

class BroadcastTemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run broadcast template factory
        if (config('app.env') === 'local') {
            factory(BroadcastTemplate::class, 10)->create();
        }
    }
}
