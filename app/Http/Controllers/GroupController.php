<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Helpers\Helper;
use App\Http\Requests\GroupControllerRequests\GroupDestroyRequest;
use App\Http\Requests\GroupControllerRequests\GroupIndexRequest;
use App\Http\Requests\GroupControllerRequests\GroupFilterRequest;
use App\Http\Requests\GroupControllerRequests\GroupSearchRequest;
use App\Http\Requests\GroupControllerRequests\GroupShowRequest;
use App\Http\Requests\GroupControllerRequests\GroupMeRequest;
use App\Http\Requests\GroupControllerRequests\GroupStoreRequest;
use App\Http\Requests\GroupControllerRequests\GroupUpdateRequest;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->middleware('team:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(GroupIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all group with all their relations
            $groups = Group::with(['contacts'])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted group with all their relations
            $groups = Group::onlyTrashed()->with(['contacts'])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all group with out their relations
            $groups = Group::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($groups) {

            if (count($groups) > 0) {
                return $this->success($groups);
            } else {
               return $this->noContent('Groups were not found');
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
    public function filterIndex(GroupFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $title = is_null($request->input('title'))? false : Helper::escapeForLikeColumnQuery($request->input('title'));
        $group_contacts = is_null($request->input('group_contacts'))? false : $request->input('group_contacts');
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $group = Group::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);

        })->when($title, function ($query, $title) {
            return $query->where('title', 'like', '%'.$title.'%');

        })->when($contacts, function ($query, $contacts) {

            foreach($contacts as $contact) {
                $query->whereJsonContains('group_contacts', $contact);
            }
            return $query;

        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        });

        // Check if the builder has any where clause
        if (count($group->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $group = $group->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $group = $group->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $group = $group->take(1000)->get();
        }

        // Return success
        if ($group) {
            if (count($group) > 0) {
                return $this->success($group);
            } else {
               return $this->noContent('No group was found for this range');
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
    public function searchIndex(GroupSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $group = Group::when($search_string, function ($query) use ($request, $search_string, $search_date) {

            return $query->when($request->input('user_id'), function($query) use ($request) {

                return $query->where('user_id', $request->input('user_id'));

            })->where(function ($query) use ($search_string, $search_date) {

                return $query->where('id', 'like', '%'.$search_string.'%')
                ->orWhere('title', 'like', '%'.$search_string.'%')
                ->orWhereJsonContains('group_contacts', $search_string)
                ->when($search_date, function ($query, $search_date) {
                    return $query->orWhere('created_at', '=', $search_date);
                });
            });
        });

        // Check if the builder has any where clause
        if (count($group->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $group = $group->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($group) {
            if (count($group) > 0) {
                return $this->success($group);
            } else {
               return $this->noContent('No group was found for this range');
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
    public function store(GroupStoreRequest $request)
    {
        // Fill the group model
        $group = new Group;
        $group = $group->fill($request->toArray());

        // Additional params
        $group->user_id = auth()->user()->id;

        // Return success
        if ($group->save()) {
            return $this->entityCreated($group,'Group was saved');
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
    public function show(GroupShowRequest $request)
    {
        // Use group model passed in from request authorization
        $group = $request->group;

        // Return success
        if ($group) {

            if ($request->input('properties')) {
                $group = $group->load('contacts');
            }

            return $this->success($group);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Display the authenticated user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(GroupMeRequest $request)
    {
        // Get a user groups
        $group = Group::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($group) {
            return $this->success($group);
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
    public function update(GroupUpdateRequest $request)
    {
        // Use group model passed in from request authorization
        $group = $request->group;

        if ($group) {

            // Fill requestor input
            $group->fill($request->toArray());

            // Update group
            if ($group->update()) {
                return $this->actionSuccess('Group was updated');
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
    public function destroy(GroupDestroyRequest $request)
    {
        // Use group model passed in from request authorization
        $group = $request->group;

        if ($group) {

            // Delete group
            if ($group->delete()) {
                return $this->actionSuccess('Group was deleted');
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
        $test = Group::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
