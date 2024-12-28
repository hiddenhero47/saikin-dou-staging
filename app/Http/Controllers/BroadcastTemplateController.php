<?php

namespace App\Http\Controllers;

use App\Models\BroadcastTemplate;
use App\Helpers\Helper;
use App\Helpers\MediaImages;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateDestroyRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateFilterRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateIndexRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateMeRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateSearchRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateStoreRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateShowRequest;
use App\Http\Requests\BroadcastTemplateControllerRequests\BroadcastTemplateUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BroadcastTemplateController extends Controller
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
    public function index(BroadcastTemplateIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all broadcasts with all their relations
            $broadcast_templates = BroadcastTemplate::with([
                'user',
                'account',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted broadcasts with all their relations
            $broadcast_templates = BroadcastTemplate::onlyTrashed()->with([
                'user',
                'account',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all broadcasts with out their relations
            $broadcast_templates = BroadcastTemplate::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($broadcast_templates) {

            if (count($broadcast_templates) > 0) {
                return $this->success($broadcast_templates);
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
    public function filterIndex(BroadcastTemplateFilterRequest $request)
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
        $broadcast_templates = BroadcastTemplate::when($user_id, function ($query, $user_id) {
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
        if (count($broadcast_templates->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $broadcast_templates = $broadcast_templates->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $broadcast_templates = $broadcast_templates->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $broadcast_templates = $broadcast_templates->take(1000)->get();
        }

        // Return success
        if ($broadcast_templates) {
            if (count($broadcast_templates) > 0) {
                return $this->success($broadcast_templates);
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
    public function searchIndex(BroadcastTemplateSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $broadcast_templates = BroadcastTemplate::when($search_string, function ($query) use ($request, $search_string, $search_date) {

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
        if (count($broadcast_templates->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $broadcast_templates = $broadcast_templates->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($broadcast_templates) {
            if (count($broadcast_templates) > 0) {
                return $this->success($broadcast_templates);
            } else {
               return $this->noContent('No broadcast was found for this range');
            }
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * BroadcastTemplate a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BroadcastTemplateStoreRequest $request)
    {
        // Fill the broadcast model
        $broadcast_template = new BroadcastTemplate;
        $broadcast_template = $broadcast_template->fill($request->toArray());

        // Additional params
        $broadcast_template->user_id = auth()->user()->id;

        // Store new images to server or cloud service
        $stored_images = MediaImages::images($request->file('photos'))
        ->base64Images($request->input('base64_photos'))
        ->imageUrls($request->input('url_photos'))
        ->path('public/images/broadcast')->limit(9)->store()->pluck('image_url');

        // Add images to model
        $broadcast_template->pictures = $stored_images->isNotEmpty() ? $stored_images : $broadcast_template->pictures;

        // Return success
        if ($broadcast_template->save()) {
            return $this->entityCreated($broadcast_template,'broadcast was saved');
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
    public function show(BroadcastTemplateShowRequest $request)
    {
        // Use broadcast model passed in from request authorization
        $broadcast_template = $request->broadcast_template;

        // Return success
        if ($broadcast_template) {

            if ($request->input('properties')) {
                $broadcast_template = $broadcast_template->load('user','account');
            }

            return $this->success($broadcast_template);
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
    public function me(BroadcastTemplateMeRequest $request)
    {
        // Get a user broadcasts
        $broadcast_templates = BroadcastTemplate::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($broadcast_templates) {
            return $this->success($broadcast_templates);
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
    public function update(BroadcastTemplateUpdateRequest $request)
    {
        // Use broadcast model passed in from request authorization
        $broadcast_template = $request->broadcast_template;

        if ($broadcast_template) {

            // Fill requestor input
            $broadcast_template->fill($request->except('user_id','account_id'));

            // Store new images to server or cloud service
            $stored_images = MediaImages::images($request->file('photos'))
            ->base64Images($request->input('base64_photos'))
            ->imageUrls($request->input('url_photos'))
            ->path('public/images/broadcast')->limit(9)->replace($broadcast_template->pictures)->pluck('image_url');

            // Add images to model
            $broadcast_template->pictures = $stored_images->isNotEmpty() ? $stored_images : $broadcast_template->pictures;

            // Update broadcast
            if ($broadcast_template->update()) {
                return $this->actionSuccess('Broadcast template was updated');
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
    public function destroy(BroadcastTemplateDestroyRequest $request)
    {
        // Use broadcast model passed in from request authorization
        $broadcast_template = $request->broadcast_template;

        if ($broadcast_template) {

            // Delete broadcast
            if ($broadcast_template->delete()) {
                return $this->actionSuccess('Broadcast template was deleted');
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
        $test = BroadcastTemplate::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
