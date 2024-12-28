<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\EmbeddedForm;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormDestroyRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormFilterRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormIndexRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormMeRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormSearchRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormShowRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormStoreRequest;
use App\Http\Requests\EmbeddedFormControllerRequests\EmbeddedFormUpdateRequest;
use Illuminate\Http\Request;

class EmbeddedFormController extends Controller
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
    public function index(EmbeddedFormIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all embedded forms with all their relations
            $embedded_form = EmbeddedForm::with([
                'user',
                'group'
            ]) ->when($request->input('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));

            })->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted embedded forms with all their relations
            $embedded_form = EmbeddedForm::onlyTrashed()->with([
                'user',
                'group'
            ]) ->when($request->input('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));

            })->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all embedded forms with out their relations
            $embedded_form = EmbeddedForm::when($request->input('user_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->input('user_id'));

            })->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($embedded_form) {
            
            if (count($embedded_form) > 0) {
                return $this->success($embedded_form);
            } else {
               return $this->noContent('No embedded form was found');
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
    public function filterIndex(EmbeddedFormFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $group_id = is_null($request->input('group_id'))? false : Helper::escapeForLikeColumnQuery($request->input('group_id'));
        $title = is_null($request->input('title'))? false : Helper::escapeForLikeColumnQuery($request->input('title'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $embedded_form = EmbeddedForm::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);
        })->when($group_id, function ($query, $group_id) {
            return $query->where('group_id', 'like', '%'.$group_id.'%');
        })->when($title, function ($query, $title) {
            return $query->where('title', 'like', '%'.$title.'%');
        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);
        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);
        });

        // Check if the builder has any where clause
        if (count($embedded_form->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $embedded_form = $embedded_form->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $embedded_form = $embedded_form->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $embedded_form = $embedded_form->take(1000)->get();
        }

        // Return success
        if ($embedded_form) {
            if (count($embedded_form) > 0) {
                return $this->success($embedded_form);
            } else {
               return $this->noContent('No embedded form was found for this range');
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
    public function searchIndex(EmbeddedFormSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));

        // Build search query
        $embedded_form = EmbeddedForm::when($search_string, function ($query) use ($request, $search_string) {

            return $query->when($request->input('user_id'), function($query) use ($request) {

                return $query->where('user_id', $request->input('user_id'));

            })->where(function ($query) use ($search_string) {

                return $query->where('user_id', 'like', '%'.$search_string.'%')
                ->orWhere('group_id', 'like', '%'.$search_string.'%')
                ->orWhere('title', 'like', '%'.$search_string.'%');
            });
        });

        // Check if the builder has any where clause
        if (count($embedded_form->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $embedded_form = $embedded_form->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($embedded_form) {
            if (count($embedded_form) > 0) {
                return $this->success($embedded_form);
            } else {
               return $this->noContent('No embedded form was found for this range');
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
    public function store(EmbeddedFormStoreRequest $request)
    {
        // Check if user has an exceeded embedded form limit embedded form and validate user
        $embedded_form = EmbeddedForm::where('user_id', auth()->user()->id)->count();
        if ($embedded_form > 999) {
            return $this->requestConflict('Embedded form limit exceeded, please delete or edit existing embedded forms');
        }

        // Fill the user embedded form model
        $embedded_form = new EmbeddedForm;
        $embedded_form = $embedded_form->fill($request->toArray());

        // Additional params
        $embedded_form->user_id = auth()->user()->id;

        // Return success
        if ($embedded_form->save()) {
            return $this->entityCreated($embedded_form,'User embedded form was saved');
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
    public function show(EmbeddedFormShowRequest $request)
    {
        // Use embedded form model passed in from request authorization
        $embedded_form = $request->embedded_form;

        // Return success
        if ($embedded_form) {

            if ($request->input('properties')) {
                $embedded_form = $embedded_form->load('user');
            }

            return $this->success($embedded_form);
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
    public function me(EmbeddedFormMeRequest $request)
    {
        // Get all user embedded form
        $embedded_form = EmbeddedForm::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($embedded_form) {

            if ($request->input('properties')) {
                $embedded_form = $embedded_form->load('user');
            }

            return $this->success($embedded_form);
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
    public function update(EmbeddedFormUpdateRequest $request)
    {
        // Use embedded form model passed in from request authorization
        $embedded_form = $request->embedded_form;

        if ($embedded_form) {

            // Fill requestor input
            $embedded_form->fill($request->toArray());

            // Update user embedded form
            if ($embedded_form->update()) {
                return $this->actionSuccess('User embedded form was updated');
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
    public function destroy(EmbeddedFormDestroyRequest $request)
    {
        // Use embedded form model passed in from request authorization
        $embedded_form = $request->embedded_form;

        if ($embedded_form) {

            // Delete user embedded form
            if ($embedded_form->delete()) {
                return $this->actionSuccess('User embedded form was deleted');
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
        $test = EmbeddedForm::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
