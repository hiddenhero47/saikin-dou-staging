<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Team;
use App\Http\Requests\RoleControllerRequests\RoleAssignRequest;
use App\Http\Requests\RoleControllerRequests\RoleDestroyRequest;
use App\Http\Requests\RoleControllerRequests\RoleIndexRequest;
use App\Http\Requests\RoleControllerRequests\RoleRetractRequest;
use App\Http\Requests\RoleControllerRequests\RoleShowRequest;
use App\Http\Requests\RoleControllerRequests\RoleStoreRequest;
use App\Http\Requests\RoleControllerRequests\RoleUpdateRequest;
use Illuminate\Http\Request;

class RoleController extends Controller
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(RoleIndexRequest $request)
    {
        $team = Team::where('name',$request->header('Team'))->first();
        if (!$team) {
            return $this->requestConflict('Unable to determine current team');
        }

        if ($request->input('properties')){

            // Get all roles with all their relations
            $roles = Role::with(['permissions'])->where('visibility',$team->visibility)
            ->orderBy('created_at','desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all roles with out their relations
            $roles = Role::where('visibility',$team->visibility)
            ->orderBy('created_at','desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($roles) {

            if (count($roles) > 0) {
                return $this->success($roles);
            } else {
               return $this->noContent('No roles were found');
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
    public function store(RoleStoreRequest $request)
    {
        // Fill the role model
        $role = new Role;
        $role = $role->fill($request->toArray());

        // Return success
        if ($role->save()) {
            return $this->entityCreated($role,'Role was saved');
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
    public function show(RoleShowRequest $request)
    {
        // Get a single role
        $role = Role::find($request->input('role_id'));

        // Return success
        if ($role) {

            if ($request->input('properties')) {
                $role = $role->load('permissions');
            }

            return $this->success($role);
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
    public function update(RoleUpdateRequest $request)
    {
        // Find the supplied role
        $role = Role::find($request->input('role_id'));

        if ($role) {

            // Fill requestor input
            $role->fill($request->toArray());

            // Update role
            if ($role->update()) {
                return $this->actionSuccess('Role was updated');
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
     * Assign a role to a user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(RoleAssignRequest $request)
    {
        // Find the supplied role and user
        $role = Role::find($request->input('role_id'));
        $user = User::find($request->input('user_id'));
        $team = Team::where('name',$request->input('team_name'))->first();

        if (!$role) {
            // Return failure
            return $this->notFound('The specified role was not found');
        }

        if (!$user) {
            // Return failure
            return $this->notFound('The specified user was not found');
        }

        if (!$team) {
            // Return failure
            return $this->notFound('The specified team was not found');
        }

        if ($role && $user) {

            // Assign role
            if ($user->assignRole([$role], $team)) {
                return $this->actionSuccess('Role was assigned');
            } else {
                return $this->unavailableService('Unable to assign role');
            }
        }

        // Return failure
        return $this->requestConflict();
    }

    /**
     * Update the specified resource in storage.
     * Retract a role from a user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function retract(RoleRetractRequest $request)
    {
        // Find the supplied role and user
        $role = Role::find($request->input('role_id'));
        $user = User::find($request->input('user_id'));
        $team = Team::where('name',$request->input('team_name'))->first();

        if (!$role) {
            // Return failure
            return $this->notFound('The specified role was not found');
        }

        if (!$user) {
            // Return failure
            return $this->notFound('The specified user was not found');
        }

        if (!$team) {
            // Return failure
            return $this->notFound('The specified team was not found');
        }

        if ($role && $user) {
            // Retract role
            if ($user->retractRole([$role], $team)) {
                return $this->actionSuccess('Role was retracted');
            } else {
                return $this->unavailableService('Unable to retract role');
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
    public function destroy(RoleDestroyRequest $request)
    {
        // Find the supplied role
        $role = Role::find($request->input('role_id'));

        if ($role) {

            // Delete role
            if ($role->delete()) {
                return $this->actionSuccess('Role was deleted');
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
        $test = Role::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
