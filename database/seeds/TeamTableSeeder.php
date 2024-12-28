<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TeamTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create new teams
        DB::table('teams')->insert([
            [
                'name' => 'administrator',
                'display_name' => 'Application Administrator',
                'description' => 'This is a team for the application with identification: administrator',
                'visibility' => config('constants.visibility.protected'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'management',
                'display_name' => 'Application Managers',
                'description' => 'This is a team for the application with identification: management',
                'visibility' => config('constants.visibility.private'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        ]);
    }
}
