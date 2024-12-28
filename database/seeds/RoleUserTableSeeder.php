<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Retrieve user
        $user = User::firstOrCreate(
            [
                'email' => 'blue@gmail.com'
            ],
            [
                'name' => 'blue',
                'email_verified_at' => now(),
                'password' => bcrypt('blueblue'),
            ]
        );

        // Assign the role of admin to user under team administrator
        $user->assignRole(['admin'],'administrator');
    }
}
