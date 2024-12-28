<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create new roles
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'display_name' => 'Application Administrator',
                'description' => 'User is the administrator of this application',
                'visibility' => config('constants.visibility.protected'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'management',
                'display_name' => 'Application Manager',
                'description' => 'User is the manager of this application',
                'visibility' => config('constants.visibility.private'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'management_accountant',
                'display_name' => 'Application Accountant',
                'description' => 'User is the accountant of this application',
                'visibility' => config('constants.visibility.private'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'management_supervisor',
                'display_name' => 'Application Supervisor',
                'description' => 'User is the supervisor of this application',
                'visibility' => config('constants.visibility.private'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'management_employee',
                'display_name' => 'Application Employee',
                'description' => 'User is the employee of this application',
                'visibility' => config('constants.visibility.private'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'basic',
                'display_name' => 'Basic',
                'description' => 'User has an account in this application',
                'visibility' => config('constants.visibility.private'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        ]);
    }
}
