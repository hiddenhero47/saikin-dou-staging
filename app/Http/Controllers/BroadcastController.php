<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\Setting;
use App\Helpers\Helper;
use App\Helpers\MediaImages;
use App\Http\Requests\BroadcastControllerRequests\BroadcastDestroyRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastFilterRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastIndexRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastMeRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastSearchRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastStoreRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastShowRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastUpdateRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastPreviewRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastPlaceHolderIndexRequest;
use App\Http\Requests\BroadcastControllerRequests\BroadcastPlaceHolderUpdateRequest;
use App\Jobs\ProcessBroadcastForPreview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BroadcastController extends Controller
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(BroadcastIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all broadcasts with all their relations
            $broadcasts = Broadcast::with([
                'user',
                'account',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted broadcasts with all their relations
            $broadcasts = Broadcast::onlyTrashed()->with([
                'user',
                'account',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all broadcasts with out their relations
            $broadcasts = Broadcast::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($broadcasts) {

            if (count($broadcasts) > 0) {
                return $this->success($broadcasts);
            } else {
               return $this->noContent('No broadcast was found');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterIndex(BroadcastFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $account_id = is_null($request->input('account_id'))? false : Helper::escapeForLikeColumnQuery($request->input('account_id'));
        $title = is_null($request->input('title'))? false : Helper::escapeForLikeColumnQuery($request->input('title'));
        $preview_phone = is_null($request->input('preview_phone'))? false : Helper::escapeForLikeColumnQuery($request->input('preview_phone'));
        $status = is_null($request->input('status'))? false : Helper::escapeForLikeColumnQuery($request->input('status'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $broadcasts = Broadcast::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);

        })->when($account_id, function ($query, $account_id) {
            return $query->where('account_id', $account_id);

        })->when($title, function ($query, $title) {
            return $query->where('title', 'like', '%'.$title.'%');

        })->when($preview_phone, function ($query, $preview_phone) {
            return $query->where('preview_phone', 'like', '%'.$preview_phone.'%');

        })->when($status, function ($query, $status) {
            return $query->where('status', $status);

        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        // Check if the builder has any where clause
        if (count($broadcasts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $broadcasts = $broadcasts->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $broadcasts = $broadcasts->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $broadcasts = $broadcasts->take(1000)->get();
        }

        // Return success
        if ($broadcasts) {
            if (count($broadcasts) > 0) {
                return $this->success($broadcasts);
            } else {
               return $this->noContent('No broadcast was found for this range');
            }

        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchIndex(BroadcastSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $broadcasts = Broadcast::when($search_string, function ($query) use ($request, $search_string, $search_date) {

            return $query->when($request->input('user_id'), function($query) use ($request) {

                return $query->where('user_id', $request->input('user_id'));

            })->when($request->input('account_id'), function($query) use ($request) {

                return $query->where('account_id', $request->input('account_id'));

            })->where(function ($query) use ($search_string, $search_date) {

                return $query->where('id', 'like', '%'.$search_string.'%')
                ->orWhere('title', 'like', '%'.$search_string.'%')
                ->orWhere('preview_phone', 'like', '%'.$search_string.'%')
                ->when($search_date, function ($query, $search_date) {
                    return $query->orWhere('created_at', '=', $search_date);
                });
            });
        });

        // Check if the builder has any where clause
        if (count($broadcasts->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $broadcasts = $broadcasts->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($broadcasts) {
            if (count($broadcasts) > 0) {
                return $this->success($broadcasts);
            } else {
               return $this->noContent('No broadcast was found for this range');
            }
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Broadcast a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BroadcastStoreRequest $request)
    {
        // Find user settings
        $setting = Setting::find(auth()->user()->id);

        // Fill the broadcast model
        $broadcast = new Broadcast;
        $broadcast = $broadcast->fill($request->toArray());

        // Additional params
        $broadcast->user_id = auth()->user()->id;
        $broadcast->messages_before_pause = $setting->messages_before_pause;
        $broadcast->minutes_before_resume = $setting->minutes_before_resume;

        // Store new images to server or cloud service
        $stored_images = MediaImages::images($request->file('photos'))
        ->base64Images($request->input('base64_photos'))
        ->imageUrls($request->input('url_photos'))
        ->path('public/images/broadcast')->limit(9)->store()->pluck('image_url');

        // Add images to model
        $broadcast->pictures = $stored_images->isNotEmpty() ? $stored_images : $broadcast->pictures;

        // Return success
        if ($broadcast->save()) {
            return $this->entityCreated($broadcast,'broadcast was saved');
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BroadcastShowRequest $request)
    {
        // Use broadcast model passed in from request authorization
        $broadcast = $request->broadcast;

        // Return success
        if ($broadcast) {

            if ($request->input('properties')) {
                $broadcast = $broadcast->load('user','account');
            }

            return $this->success($broadcast);
        } else {
            // Return Failure
            return $this->notFound();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(BroadcastMeRequest $request)
    {
        // Get a user broadcasts
        $broadcasts = Broadcast::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($broadcasts) {
            return $this->success($broadcasts);
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
    public function update(BroadcastUpdateRequest $request)
    {
        // Use broadcast model passed in from request authorization
        $broadcast = $request->broadcast;

        if ($broadcast) {

            // Check if the broadcast has been canceled
            if ($broadcast->status !== config('constants.status.canceled')) {
                return $this->requestConflict('Only canceled broadcast can be edited');
            }

            // Fill requestor input
            $broadcast->fill($request->except('user_id','account_id'));

            // Store new images to server or cloud service
            $stored_images = MediaImages::images($request->file('photos'))
            ->base64Images($request->input('base64_photos'))
            ->imageUrls($request->input('url_photos'))
            ->path('public/images/broadcast')->limit(9)->replace($broadcast->pictures)->pluck('image_url');

            // Add images to model
            $broadcast->pictures = $stored_images->isNotEmpty() ? $stored_images : $broadcast->pictures;

            // Update broadcast
            if ($broadcast->update()) {
                return $this->actionSuccess('Broadcast was updated');
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(BroadcastPreviewRequest $request)
    {
        // Find user settings
        $setting = Setting::find(auth()->user()->id);

        // Fill the broadcast model
        $broadcast = new Broadcast;
        $broadcast = $broadcast->fill($request->toArray());

        // Additional params
        $broadcast->user_id = auth()->user()->id;
        $broadcast->messages_before_pause = $setting->messages_before_pause;
        $broadcast->minutes_before_resume = $setting->minutes_before_resume;

        // Store new images to server or cloud service
        $stored_images = MediaImages::images($request->file('photos'))
        ->base64Images($request->input('base64_photos'))
        ->imageUrls($request->input('url_photos'))
        ->path('public/images/broadcast')->limit(9)->store()->pluck('image_url');

        // Add images to model
        $broadcast->pictures = $stored_images->isNotEmpty() ? $stored_images : $broadcast->pictures;

        // Return success
        if (ProcessBroadcastForPreview::dispatch($broadcast)) {
            return $this->entityCreated($broadcast,'broadcast preview was sent');
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeHolderIndex(BroadcastPlaceHolderIndexRequest $request)
    {
        // Get cache value if it exists by key
        $placeholders = Cache::get('BROADCAST_PLACE_HOLDER');

        // Return success
        if ($placeholders) {
            return $this->success($placeholders);
        }

        // Return failure
        return $this->noContent('No app defined data in cache');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function placeHoldersUpdate(BroadcastPlaceHolderUpdateRequest $request)
    {
        // Store application settings in cache
        $placeholders = Cache::put('BROADCAST_PLACE_HOLDER', $request->input('placeholders'));

        // Return success
        if ($placeholders) {
            $placeholders = Cache::get('BROADCAST_PLACE_HOLDER');
            return $this->entityCreated($placeholders,'Application setting was saved');
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BroadcastDestroyRequest $request)
    {
        // Use broadcast model passed in from request authorization
        $broadcast = $request->broadcast;

        if ($broadcast) {

            // Check if the broadcast has been canceled
            if ($broadcast->status !== config('constants.status.canceled')) {
                return $this->requestConflict('Only canceled broadcast can be deleted');
            }

            // Delete broadcast
            if ($broadcast->delete()) {
                return $this->actionSuccess('Broadcast was deleted');
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
        $test = Broadcast::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
