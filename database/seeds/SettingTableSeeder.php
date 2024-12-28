<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run setting factory
        if (config('app.env') === 'local') {
            factory(Setting::class, 10)->create();
        }
    }
}
