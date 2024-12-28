<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CacheTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(TeamTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SettingTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(ContactTableSeeder::class);
        $this->call(AccountTableSeeder::class);
        $this->call(GroupTableSeeder::class);
        $this->call(BroadcastTemplateTableSeeder::class);
        $this->call(BroadcastTableSeeder::class);
        $this->call(BroadcastOutgoingTableSeeder::class);
        $this->call(PaymentTableSeeder::class);
        $this->call(BenefitTableSeeder::class);
        $this->call(PaymentPlanTableSeeder::class);
        $this->call(EmbeddedFormTableSeeder::class);
    }
}
