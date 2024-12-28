<?php

use App\Models\Benefit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BenefitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create new benefits
        DB::table('benefits')->insert([
            [
                'name' => 'accounts',
                'display_name' => 'Accounts',
                'description' => 'Accounts a user can create',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'contacts',
                'display_name' => 'Contacts',
                'description' => 'Max contact a user can save',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'auto_contacts',
                'display_name' => 'Automatic Contacts',
                'description' => 'Max contacts a user can save daily',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'broadcast_list',
                'display_name' => 'Broadcast List',
                'description' => 'Max broadcast list a user can create',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'form',
                'display_name' => 'Form',
                'description' => 'Max forms a user can create',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'automation_broadcast_group_contact',
                'display_name' => 'Automated Broadcast For Contacts',
                'description' => 'Automation messages for each broadcast list',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'automation_broadcast_whatsapp_group',
                'display_name' => 'Automated Broadcast For Whatsapp Groups',
                'description' => 'Automation messages for each whatsapp group',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'name' => 'media_support',
                'display_name' => 'Media Support',
                'description' => 'Media support (mp4, images, pdf, audio)',
                'visibility' => config('constants.visibility.public'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ],
        ]);

        // Run benefit factory
        if (config('app.env') === 'local') {
            factory(Benefit::class, 10)->create();
        }
    }
}
