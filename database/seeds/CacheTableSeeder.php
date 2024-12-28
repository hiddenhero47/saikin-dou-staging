<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class CacheTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear cached roles and abilities
        Cache::forget('EARTH_REGIONS');
    }
}
