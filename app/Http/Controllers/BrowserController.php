<?php

namespace App\Http\Controllers;

use App\Models\Browser;
use App\Helpers\Helper;
use App\Http\Requests\BrowserControllerRequests\BrowserDestroyRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserFilterRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserIndexRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserMeRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserSearchRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserStoreRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserShowRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserUpdateRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserCloseByManagementRequest;
use App\Http\Requests\BrowserControllerRequests\BrowserOpenByManagementRequest;
use Illuminate\Http\Request;

class BrowserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('team:api')->except('me');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(BrowserIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all browsers with all their relations
            $browsers = Browser::with([
                'user',
                'account',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted browsers with all their relations
            $browsers = Browser::onlyTrashed()->with([
                'user',
                'account',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all browsers with out their relations
            $browsers = Browser::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($browsers) {

            if (count($browsers) > 0) {
                return $this->success($browsers);
            } else {
               return $this->noContent('No browsers was found');
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
    public function filterIndex(BrowserFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $account_id = is_null($request->input('account_id'))? false : Helper::escapeForLikeColumnQuery($request->input('account_id'));
        $session_id = is_null($request->input('session_id'))? false : Helper::escapeForLikeColumnQuery($request->input('session_id'));
        $status = is_null($request->input('status'))? false : Helper::escapeForLikeColumnQuery($request->input('status'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $browsers = Browser::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', 'like', '%'.$user_id.'%');

        })->when($account_id, function ($query, $account_id) {
            return $query->where('account_id', 'like', '%'.$account_id.'%');

        })->when($session_id, function ($query, $session_id) {
            return $query->where('session_id', 'like', '%'.$session_id.'%');

        })->when($status, function ($query, $status) {
            return $query->where('status', $status);

        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        });

        // Check if the builder has any where clause
        if (count($browsers->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $browsers = $browsers->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $browsers = $browsers->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $browsers = $browsers->take(1000)->get();
        }

        // Return success
        if ($browsers) {
            if (count($browsers) > 0) {
                return $this->success($browsers);
            } else {
               return $this->noContent('No browsers was found for this range');
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
    public function searchIndex(BrowserSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $browsers = Browser::when($search_string, function ($query) use ($request, $search_string, $search_date) {

            return $query->where('user_id', 'like', '%'.$search_string.'%')
            ->orWhere('account_id', 'like', '%'.$search_string.'%')
            ->orWhere('session_id', 'like', '%'.$search_string.'%')
            ->when($search_date, function ($query, $search_date) {
                return $query->orWhere('created_at', '=', $search_date);
            });
        });

        // Check if the builder has any where clause
        if (count($browsers->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $browsers = $browsers->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($browsers) {
            if (count($browsers) > 0) {
                return $this->success($browsers);
            } else {
               return $this->noContent('No browsers was found for this range');
            }
        } else {
            // Return failure
            return $this->unavailableService();
        }
    }

    /**
     * Browser a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BrowserStoreRequest $request)
    {
        // Fill the browser model
        $browser = new Browser;
        $browser = $browser->fill($request->toArray());

        // Additional params
        $browser->status = config('constants.browser.status.closed');

        // Return success
        if ($browser->save()) {
            return $this->entityCreated($browser,'Browser was saved');
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
    public function show(BrowserShowRequest $request)
    {
        // Use browser model passed in from request authorization
        $browser = $request->browser;

        // Return success
        if ($browser) {

            if ($request->input('properties')) {
                $browser = $browser->load('user','account');
            }

            return $this->success($browser);
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
    public function me(BrowserMeRequest $request)
    {
        // Get a user browsers
        $browsers = Browser::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($browsers) {
            return $this->success($browsers);
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
    public function update(BrowserUpdateRequest $request)
    {
        // Use browser model passed in from request authorization
        $browser = $request->browser;

        if ($browser) {

            // Fill requestor input
            $browser->fill($request->toArray());

            // Update browser
            if ($browser->update()) {
                return $this->actionSuccess('Browser was updated');
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
    public function closeByManagement(BrowserCloseByManagementRequest $request)
    {
        // Find the supplied browser
        $browser = Browser::find($request->input('id'));

        if ($browser) {

            if ($browser->status === config('constants.browser.status.closed')) {
                return $this->requestConflict('Browser is currently closed');
            }

            // Update browser
            if ($browser->close()) {
                return $this->actionSuccess('Browser was closed');
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
    public function openByManagement(BrowserOpenByManagementRequest $request)
    {
        // Find the supplied browser
        $browser = Browser::find($request->input('id'));

        if ($browser) {

            if ($browser->status === config('constants.browser.status.open')) {
                return $this->requestConflict('Browser is currently open');
            }

            // Update browser
            if ($browser->open()) {
                return $this->actionSuccess('Browser was opened');
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
    public function destroy(BrowserDestroyRequest $request)
    {
        // Use browser model passed in from request authorization
        $browser = $request->browser;

        if ($browser) {

            if ($browser->status === config('constants.browser.status.open')) {
                $browser->close();
            }

            // Delete browser
            if ($browser->delete()) {
                return $this->actionSuccess('Browser was deleted');
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
        $test = Browser::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
