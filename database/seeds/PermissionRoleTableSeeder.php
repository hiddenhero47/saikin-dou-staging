<?php

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // List all permissions
        $admin_permissions = [
            "index_account",
            "filter_index_account",
            "search_index_account",
            "show_account",
            "update_account",
            "delete_account",

            "index_benefit",
            "store_benefit",
            "show_benefit",
            "update_benefit",
            "assign_benefit",
            "retract_benefit",
            "delete_benefit",

            "index_broadcast",
            "filter_index_broadcast",
            "search_index_broadcast",
            "show_broadcast",
            "update_broadcast",
            "broadcast_placeholder_index",
            "broadcast_placeholder_update",
            "delete_broadcast",

            "index_broadcast_template",
            "filter_index_broadcast_template",
            "search_index_broadcast_template",
            "show_broadcast_template",
            "update_broadcast_template",
            "delete_broadcast_template",

            "index_browser",
            "filter_index_browser",
            "search_index_browser",
            "store_browser",
            "show_browser",
            "update_browser",
            "close_browser_as_management",
            "open_browser_as_management",
            "delete_browser",

            "index_cache",
            "clear_cache",

            "index_contact",
            "filter_index_contact",
            "search_index_contact",
            "store_contact",
            "show_contact",
            "update_contact",
            "delete_contact",

            "index_embedded_form",
            "filter_index_embedded_form",
            "search_index_embedded_form",
            "store_embedded_form",
            "show_embedded_form",
            "update_embedded_form",
            "delete_embedded_form",

            "index_group",
            "filter_index_group",
            "search_index_group",
            "show_group",
            "store_group",
            "update_group",
            "delete_group",

            "index_payment",
            "filter_index_payment",
            "search_index_payment",
            "show_payment",
            "store_payment",
            "update_payment",
            "delete_payment",

            "index_payment_plan",
            "filter_index_payment_plan",
            "search_index_payment_plan",
            "show_payment_plan",
            "store_payment_plan",
            "unapprove_payment_plan_as_management",
            "approve_payment_plan_as_management",
            "update_payment_plan",
            "delete_payment_plan",

            "index_permission",
            "store_permission",
            "show_permission",
            "update_permission",
            "assign_permission",
            "retract_permission",
            "delete_permission",

            "index_role",
            "store_role",
            "show_role",
            "update_role",
            "assign_role",
            "retract_role",
            "delete_role",

            "general_statistics",
            "user_statistics",

            "index_setting",
            "filter_index_setting",
            "search_index_setting",
            "show_setting",
            "update_setting",
            "delete_setting",

            "index_user",
            "filter_index_user",
            "search_index_user",
            "show_user",
            "show_role_permission",
            "relation_user",
            "update_user",
            "block_user",
            "unblock_user",
            "delete_user",
        ];
        $management_permissions = [
            //
        ];
        $management_accountant_permissions = [
            //
        ];
        $management_supervisor_permissions = [
           //
        ];
        $management_employee_permissions = [
           //
        ];

        // Assign all permissions to the Admin role
        $admin_role = Role::where('name', 'admin')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($admin_permissions)
            )->get()
        );

        // Assign some permissions to the management role
        $management_role = Role::where('name', 'management')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_permissions,$management_accountant_permissions,$management_supervisor_permissions,$management_employee_permissions)
            )->get()
        );

        // Assign some permissions to the management accountant role
        $management_accountant_role = Role::where('name', 'management_accountant')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_accountant_permissions,$management_supervisor_permissions,$management_employee_permissions)
            )->get()
        );

        // Assign some permissions to the management supervisor role
        $management_supervisor_role = Role::where('name', 'management_supervisor')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_supervisor_permissions,$management_employee_permissions)
            )->get()
        );

        // Assign some permissions to the management employee role
        $management_employee_role = Role::where('name', 'management_employee')->first()->attachPermissions(
            Permission::whereIn('name',
                array_merge($management_employee_permissions)
            )->get()
        );
    }
}
