<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class RolesPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:permissions {--detach} {--attach} {--refresh} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages application roles and permissions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Take application down
        $this->call('down');

        // Detach permissions from roles
        if ($this->option('detach') || $this->option('refresh')){

            $roles = Role::all();
            $roles->map(function($role){
                $role->detachPermissions($role->permissions()->get()->pluck('name')->toArray());
            });

            $this->info('Permissions have been detached form roles');
        }

        // Force delete all roles
        if ($this->option('refresh')) {

            $permissions = Permission::query()->forceDelete();

            $this->info('Permissions have been dropped');
        }

        // Seed all roles
        if ($this->option('refresh')) {

            $PermissionTableSeeder = new \PermissionTableSeeder;
            $PermissionTableSeeder->run();

            $this->info('Permissions have been seeded');
        }

        // Attach permissions to roles
        if ($this->option('attach') || $this->option('refresh')) {

            $PermissionRoleTableSeeder = new \PermissionRoleTableSeeder;
            $PermissionRoleTableSeeder->run();

            $this->info('Permissions have been attached to roles');
        }

        // Take application up
        $this->call('up');

        return ;
    }
}
