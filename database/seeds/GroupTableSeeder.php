<?php

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run group factory
        if (config('app.env') === 'local') {
            factory(Group::class, 10)->create();
        }
    }
}
