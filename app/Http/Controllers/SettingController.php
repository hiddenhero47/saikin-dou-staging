<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Setting;
use App\Http\Requests\SettingControllerRequests\SettingDestroyRequest;
use App\Http\Requests\SettingControllerRequests\SettingFilterRequest;
use App\Http\Requests\SettingControllerRequests\SettingIndexRequest;
use App\Http\Requests\SettingControllerRequests\SettingMeRequest;
use App\Http\Requests\SettingControllerRequests\SettingSearchRequest;
use App\Http\Requests\SettingControllerRequests\SettingShowRequest;
use App\Http\Requests\SettingControllerRequests\SettingStoreRequest;
use App\Http\Requests\SettingControllerRequests\SettingUpdateRequest;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api')->except('store','me','update');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(SettingIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all settings with all their relations
            $settings = Setting::with([
                'user',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted settings with all their relations
            $settings = Setting::onlyTrashed()->with([
                'user',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all settings with out their relations
            $settings = Setting::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($settings) {
            
            if (count($settings) > 0) {
                return $this->success($settings);
            } else {
               return $this->noContent('No setting was found');
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
    public function filterIndex(SettingFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $messages_before_pause = is_null($request->input('messages_before_pause'))? false : Helper::escapeForLikeColumnQuery($request->input('messages_before_pause'));
        $minutes_before_resume = is_null($request->input('minutes_before_resume'))? false : Helper::escapeForLikeColumnQuery($request->input('minutes_before_resume'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $settings = Setting::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', 'like', '%'.$user_id.'%');
        })->when($messages_before_pause, function ($query, $messages_before_pause) {
            return $query->where('messages_before_pause', 'like', '%'.$messages_before_pause.'%');
        })->when($minutes_before_resume, function ($query, $minutes_before_resume) {
            return $query->where('minutes_before_resume', 'like', '%'.$minutes_before_resume.'%');
        });

        // Check if the builder has any where clause
        if (count($settings->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $settings = $settings->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $settings = $settings->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $settings = $settings->take(1000)->get();
        }

        // Return success
        if ($settings) {
            if (count($settings) > 0) {
                return $this->success($settings);
            } else {
               return $this->noContent('No setting was found for this range');
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
    public function searchIndex(SettingSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $settings = Setting::when($search_string, function ($query, $search_string) use ($search_date) {
            return $query->where('user_id', 'like', '%'.$search_string.'%')
            ->orWhere('messages_before_pause', 'like', '%'.$search_string.'%');
        })->when($search_date, function ($query, $search_date) {
            return $query->whereTime('minutes_before_resume', '=', $search_date->toTimeString());
        });

        // Check if the builder has any where clause
        if (count($settings->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $settings = $settings->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($settings) {
            if (count($settings) > 0) {
                return $this->success($settings);
            } else {
               return $this->noContent('No setting was found for this range');
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
    public function store(SettingStoreRequest $request)
    {
        // Check if user has an existing setting and validate user
        $setting = Setting::find(auth()->user()->id);
        if ($setting) {
            return $this->requestConflict('A setting already exists for this user');
        }

        // Fill the user setting model
        $setting = new Setting;
        $setting = $setting->fill($request->toArray());

        // Additional params
        $setting->user_id = auth()->user()->id;

        // Return success
        if ($setting->save()) {
            return $this->entityCreated($setting,'User setting was saved');
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
    public function show(SettingShowRequest $request)
    {
        // Use setting model passed in from request authorization
        $setting = $request->setting;

        // Return success
        if ($setting) {

            if ($request->input('properties')) {
                $setting = $setting->load('user');
            }

            return $this->success($setting);
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
    public function me(SettingMeRequest $request)
    {
        // Get a single user setting
        $setting = Setting::find(auth()->user()->id);

        // Return success
        if ($setting) {

            if ($request->input('properties')) {
                $setting = $setting->load('user');
            }

            return $this->success($setting);
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
    public function update(SettingUpdateRequest $request)
    {
        // Use setting model passed in from request authorization
        $setting = $request->setting;

        if ($setting) {

            // Fill requestor input
            $setting->fill($request->toArray());

            // Update user setting
            if ($setting->update()) {
                return $this->actionSuccess('User setting was updated');
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
    public function destroy(SettingDestroyRequest $request)
    {
        // Use setting model passed in from request authorization
        $setting = $request->setting;

        if ($setting) {

            // Delete user setting
            if ($setting->delete()) {
                return $this->actionSuccess('User Setting was deleted');
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
        $test = Setting::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
