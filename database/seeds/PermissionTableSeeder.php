<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Retrieve permissions from json file
        $contents = file_get_contents('dependencies/ApplicationPermissions/permission-list.json');
        $permission_list = collect(json_decode($contents, true));
        $permission_list = $permission_list->collapse();

        // Prepare data for db insert
        $permissions = $permission_list->map(function ($item, $key) {
            return  [
                'name' => $item['name'],
                'display_name' => $item['display_name'],
                'description' => $item['description'],
                'visibility' => config('constants.visibility.protected'),
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        });

        // Create new permissions
        DB::table('permissions')->insert($permissions->toArray());
    }
}
