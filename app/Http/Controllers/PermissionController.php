<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Requests\PermissionControllerRequests\PermissionAssignRequest;
use App\Http\Requests\PermissionControllerRequests\PermissionDestroyRequest;
use App\Http\Requests\PermissionControllerRequests\PermissionIndexRequest;
use App\Http\Requests\PermissionControllerRequests\PermissionRetractRequest;
use App\Http\Requests\PermissionControllerRequests\PermissionShowRequest;
use App\Http\Requests\PermissionControllerRequests\PermissionStoreRequest;
use App\Http\Requests\PermissionControllerRequests\PermissionUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PermissionIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all permissions with all their relations
            $permissions = Permission::with([
                'roles',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all permissions with out their relations
            $permissions = Permission::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($permissions) {

            if (count($permissions) > 0) {
                return $this->success($permissions);
            } else {
               return $this->noContent('No permissions were found');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PermissionStoreRequest $request)
    {
        // Check if similar permission exists
        $permission = Permission::where('name', $request->input('name'))->first();
        if ($permission) {
            return $this->requestConflict('A similar permission already exists');
        }

        // Fill the permission model
        $permission = new Permission;
        $permission = $permission->fill($request->toArray());

        // Return success
        if ($permission->save()) {
            return $this->entityCreated($permission,'Permission was saved');
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(PermissionShowRequest $request)
    {
        // Get a single permission
        $permission = Permission::find($request->input('permission_id'));

        // Return success
        if ($permission) {

            if ($request->input('properties')) {
                $permission = $permission->load('roles');
            }

            return $this->success($permission);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PermissionUpdateRequest $request)
    {
        // Find the supplied permission
        $permission = Permission::find($request->input('permission_id'));

        if ($permission) {

            // Fill requestor input
            $permission->fill($request->toArray());

            // Update permission
            if ($permission->update()) {
                return $this->actionSuccess('Permission was updated');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Update the specified resource in storage.
     * Assign an permission to a role
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(PermissionAssignRequest $request)
    {
        // Find the supplied permission and role
        $permission = Permission::find($request->input('permission_id'));
        $role = Role::find($request->input('role_id'));

        if (!$permission) {
            // Return failure
            return $this->notFound('The specified permission was not found');
        }

        if (!$role) {
            // Return failure
            return $this->notFound('The specified role was not found');
        }

        // Check if role already has permission
        $permissions = $role->permissions()->get();
        if ($permissions && $permissions->contains('name', $permission->name)) {
            // Return failure
            return $this->requestConflict('The specified role currently has this permission');
        }

        if ($permission && $role) {

            // Assign permission
            if ($role->attachPermission($permission)) {

                return $this->actionSuccess('Permission was assigned');
            } else {
                return $this->unavailableService('Unable to assign permission');
            }
        }

        // Return failure
        return $this->requestConflict();
    }

    /**
     * Update the specified resource in storage.
     * Retract an permission from a role
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function retract(PermissionRetractRequest $request)
    {
        // Find the supplied permission and role
        $permission = Permission::find($request->input('permission_id'));
        $role = Role::find($request->input('role_id'));

        if (!$permission) {
            // Return failure
            return $this->notFound('The specified permission was not found');
        }

        if (!$role) {
            // Return failure
            return $this->notFound('The specified role was not found');
        }

        // Check if role already has permission
        $permissions = $role->permissions()->get();
        if (!$permissions || !$permissions->contains('name', $permission->name)) {
            // Return failure
            return $this->requestConflict('The specified role currently does not have this permission');
        }

        if ($permission && $role) {

            // Unassign permission
            if ($role->detachPermission($permission)) {

                return $this->actionSuccess('Permission was retracted');
            } else {
                return $this->unavailableService('Unable to retract permission');
            }
        }

        // Return failure
        return $this->requestConflict();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PermissionDestroyRequest $request)
    {
        // Find the supplied permission
        $permission = Permission::find($request->input('permission_id'));

        if ($permission) {

            // Delete permission
            if ($permission->delete()) {
                return $this->actionSuccess('Permission was deleted');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Validate existence of resource pool.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        $test = Permission::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
