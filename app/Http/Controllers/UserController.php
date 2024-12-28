<?php

namespace App\Http\Controllers;

use App\Casts\GenderCast;
use App\Models\User;
use App\Helpers\Helper;
use App\Helpers\MediaImages;
use App\Http\Requests\UserControllerRequests\UserBlockRequest;
use App\Http\Requests\UserControllerRequests\UserDestroyRequest;
use App\Http\Requests\UserControllerRequests\UserFilterRequest;
use App\Http\Requests\UserControllerRequests\UserIndexRequest;
use App\Http\Requests\UserControllerRequests\UserSearchRequest;
use App\Http\Requests\UserControllerRequests\UserShowRequest;
use App\Http\Requests\UserControllerRequests\UserShowRolePermissionRequest;
use App\Http\Requests\UserControllerRequests\UserRelationRequest;
use App\Http\Requests\UserControllerRequests\UserMeRequest;
use App\Http\Requests\UserControllerRequests\UserStoreRequest;
use App\Http\Requests\UserControllerRequests\UserUnblockRequest;
use App\Http\Requests\UserControllerRequests\UserUpdateRequest;
use Illuminate\Http\Request;
use Laratrust\Traits\LaratrustUserTrait;

class UserController extends Controller
{
    use LaratrustUserTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api')->except('show', 'showRolePermission', 'me', 'update', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(UserIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all users with all their relations
            $users = User::with([
                'accounts',
                'browsers',
                'roles'
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted users with all their relations
            $users = User::onlyTrashed()->with([
                'accounts',
                'browsers',
                'roles'
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all users with out their relations
            $users = User::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($users) {
            
            if (count($users) > 0) {
                return $this->success($users);
            } else {
               return $this->noContent('No user was found');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterIndex(UserFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $email = is_null($request->input('email'))? false : Helper::escapeForLikeColumnQuery($request->input('email'));
        $first_name = is_null($request->input('first_name'))? false : Helper::escapeForLikeColumnQuery($request->input('first_name'));
        $last_name = is_null($request->input('last_name'))? false : Helper::escapeForLikeColumnQuery($request->input('last_name'));
        $phone = is_null($request->input('phone'))? false : Helper::escapeForLikeColumnQuery($request->input('phone'));
        $gender = is_null($request->input('gender'))? false : GenderCast::atSet($request->input('gender'));
        $birth_date = is_null($request->input('birth_date'))? false : Helper::stringToCarbonDate($request->input('birth_date'));
        $birth_year = is_null($request->input('birth_year'))? false : Helper::stringToCarbonDate($request->input('birth_year'));
        $email_verified_at = is_null($request->input('email_verified_at'))? false : Helper::stringToCarbonDate($request->input('email_verified_at'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $users = User::when($user_id, function ($query, $user_id) {
            return $query->where('id', 'like', '%'.$user_id.'%');
        })->when($email, function ($query, $email) {
            return $query->where('email', 'like', '%'.$email.'%');
        })->when($first_name, function ($query, $first_name) {
            return $query->where('first_name', 'like', '%'.$first_name.'%');
        })->when($last_name, function ($query, $last_name) {
            return $query->where('last_name', 'like', '%'.$last_name.'%');
        })->when($phone, function ($query, $phone) {
            return $query->where('phone', 'like', '%'.$phone.'%');
        })->when($gender, function ($query, $gender) {
            return $query->where('gender', 'like', '%'.$gender.'%');
        })->when($birth_date, function ($query, $birth_date) {
            return $query->whereDate('birth_date', '=', $birth_date);
        })->when($birth_year, function ($query, $birth_year) {
            return $query->whereYear('birth_year', '=', $birth_year);
        })->when($email_verified_at, function ($query, $email_verified_at) {
            return $query->whereDate('email_verified_at', '=', $email_verified_at);
        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        });

        // Check if the builder has any where clause
        if (count($users->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $users = $users->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $users = $users->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $users = $users->take(1000)->get();
        }

        // Return success
        if ($users) {
            if (count($users) > 0) {
                return $this->success($users);
            } else {
               return $this->noContent('No user was found for this range');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchIndex(UserSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $users = User::when($search_string, function ($query, $search_string) {
            return $query->where('id', 'like', '%'.$search_string.'%')
            ->orWhere('first_name', 'like', '%'.$search_string.'%')
            ->orWhere('last_name', 'like', '%'.$search_string.'%')
            ->orWhere('email', 'like', '%'.$search_string.'%')
            ->orWhere('phone', 'like', '%'.$search_string.'%');
        })->when($search_date, function ($query, $search_date) {
            return $query->whereDate('birth_date', '=', $search_date)
            ->orWhereDate('birth_year', '=', $search_date)
            ->orWhereDate('email_verified_at', '=', $search_date);
        });

        // Check if the builder has any where clause
        if (count($users->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $users = $users->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($users) {
            if (count($users) > 0) {
                return $this->success($users);
            } else {
               return $this->noContent('No user was found for this range');
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
    public function store(UserStoreRequest $request)
    {
        // Already Handled By The App\Http\Controllers\AuthController
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(UserShowRequest $request)
    {
        // Use user model passed in from request authorization
        $user = $request->user;

        // Return success
        if ($user) {

            if ($request->input('properties')) {
                $user = $user->load([
                    'accounts',
                    'browsers',
                    'roles',
                ]);
            }

            return $this->success($user);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRolePermission(UserShowRolePermissionRequest $request)
    {
        // Use user model passed in from request authorization
        $user = $request->user;

        // Return success
        if ($user) {

            $user_permissions = $user->allPermissions();
            $user = $user->load('roles');

            // This was done because "$user->allPermissions()" is not an eloquent relationship and hence
            // can not be lazy loaded but to maintain the data structure a temporal relationship is created
            $user->setRelation('permissions', $user_permissions);

            return $this->success($user);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function relation(UserRelationRequest $request)
    {
        $relations = [
            'accounts' => 'accounts',
            'browsers' => 'browsers',
            'roles' => 'roles'
        ];

        $users = new User;
        foreach ($relations as $key => $relation) {
            if ($request->input($key)) {
                $users = $users->has($relation);
            }
        }

        // Check if the builder has any where clause
        if (count($users->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $users = $users->orderBy('created_at', 'desc')->take(1000)->paginate(25);

        // Return success
        if ($users) {
            if (count($users) > 0) {
                return $this->success($users);
            } else {
               return $this->noContent('No user was found for this range');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display the authenticated user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(UserMeRequest $request)
    {
        // Get a single user
        $user = User::find(auth()->user()->id);

        // Return success
        if ($user) {

            if ($request->input('properties')) {
                $user = $user->load([
                    'accounts',
                    'browsers',
                    'roles'
                ]);
            }

            return $this->success($user);

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
    public function update(UserUpdateRequest $request)
    {
        // Use user model passed in from request authorization
        $user = $request->user;

        // Return success
        if ($user) {

            // Fill requestor input
            $user->fill($request->toArray());

            // Store new images to server or cloud service
            $stored_images = MediaImages::images($request->file('photos'))
            ->base64Images($request->input('base64_photos'))
            ->imageUrls($request->input('url_photos'))
            ->path('public/images/user')->limit(1)->replace([$user->picture])->pluck('image_url');

            // Add images to model
            $user->picture = $stored_images->isNotEmpty() ? $stored_images->first() : $user->picture;

            // Update user
            if ($user->update()) {
                return $this->actionSuccess('User details was updated');
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function block(UserBlockRequest $request)
    {
        // Use user model passed in from request authorization
        $user = $request->user;

        // Return success
        if ($user) {

            // Check if user is currently blocked
            if ($user->blocked == true) {
                return $this->requestConflict('User account is currently blocked');
            }

            // Set status
            $user->blocked = true;

            // Update user
            if ($user->update()) {
                return $this->actionSuccess('User account was blocked');
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblock(UserUnblockRequest $request)
    {
        // Use user model passed in from request authorization
        $user = $request->user;

        // Return success
        if ($user) {

            // Check if user is currently active
            if ($user->blocked == false) {
                return $this->requestConflict('User account is currently active');
            }

            // Set status
            $user->blocked = false;

            // Update user
            if ($user->update()) {
                return $this->actionSuccess('User account was activated');
            } else {
                return $this->unavailableService();
            }
        } else {
            // Return failure
            return $this->notFound();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserDestroyRequest $request)
    {
        // Use user model passed in from request authorization
        $user = $request->user;

        // Return success
        if ($user) {

            // Delete user
            if ($user->delete()) {
                return $this->actionSuccess('User was deleted');
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
        $test = User::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
