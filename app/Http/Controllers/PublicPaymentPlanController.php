<?php

namespace App\Http\Controllers;


use App\Models\PaymentPlan;
use App\Models\Payment;
use App\Helpers\Helper;
use App\Http\Requests\PublicPaymentPlanControllerRequests\PublicPaymentPlanIndexRequest;
use App\Http\Requests\PublicPaymentPlanControllerRequests\PublicPaymentPlanFilterRequest;
use App\Http\Requests\PublicPaymentPlanControllerRequests\PublicPaymentPlanSearchRequest;
use App\Http\Requests\PublicPaymentPlanControllerRequests\PublicPaymentPlanShowRequest;
use App\Http\Requests\PublicPaymentPlanControllerRequests\PublicPaymentPlanPayRequest;
use Illuminate\Http\Request;

class PublicPaymentPlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('index','filterIndex','searchIndex','show');
        $this->middleware('team:api')->except('index','filterIndex','searchIndex','show','pay');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PublicPaymentPlanIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all payment plan with all their relations
            $payment_plans = PaymentPlan::with(['benefits'])
            ->isPublic()
            ->orderBy('level', 'asc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all payment plan with out their relations
            $payment_plans = PaymentPlan::isPublic()
            ->orderBy('level', 'asc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($payment_plans) {

            if (count($payment_plans) > 0) {
                return $this->success($payment_plans);
            } else {
               return $this->noContent('Payment plans were not found');
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
    public function filterIndex(PublicPaymentPlanFilterRequest $request)
    {
        $name = is_null($request->input('name'))? false : Helper::escapeForLikeColumnQuery($request->input('name'));
        $level = is_null($request->input('level'))? false : Helper::escapeForLikeColumnQuery($request->input('level'));
        $payment_plan_benefits = is_null($request->input('payment_plan_benefits'))? false : $request->input('payment_plan_benefits');
        $amount = is_null($request->input('amount'))? false : Helper::formatForNumericColumnQuery($request->input('amount'));
        $discount = is_null($request->input('discount'))? false : Helper::formatForNumericColumnQuery($request->input('discount'));
        $currency = is_null($request->input('currency'))? false : Helper::escapeForLikeColumnQuery($request->input('currency'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $visibility = is_null($request->input('visibility'))? false : Helper::escapeForLikeColumnQuery($request->input('visibility'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $payment_plans = PaymentPlan::when($name, function ($query, $name) {
            return $query->where('name', 'like', '%'.$name.'%');
        
        })->when($level, function ($query, $level) {
            return $query->where('level', 'like', '%'.$level.'%');

        })->when($payment_plan_benefits, function ($query, $payment_plan_benefits) {

            foreach($payment_plan_benefits as $payment_plan_benefits) {
                $query->whereJsonContains('payment_plan_benefits', $payment_plan_benefits);
            }
            return $query;

        })->when($amount, function ($query, $amount) {
            return $query->where('amount', 'like', '%'.$amount.'%');

        })->when($discount, function ($query, $discount) {
            return $query->where('discount', 'like', '%'.$discount.'%');

        })->when($currency, function ($query, $currency) {
            return $query->where('currency', 'like', '%'.$currency.'%');

        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        })->when($visibility, function ($query, $visibility) {
            return $query->where('visibility', 'like', '%'.$visibility.'%');

        });

        // Check if the builder has any where clause
        if (count($payment_plans->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $payment_plans = $payment_plans->isPublic()->orderBy('level', 'asc');

        // Execute with pagination required
        if ($pagination) {
            $payment_plans = $payment_plans->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $payment_plans = $payment_plans->take(1000)->get();
        }

        // Return success
        if ($payment_plans) {
            if (count($payment_plans) > 0) {
                return $this->success($payment_plans);
            } else {
               return $this->noContent('No payment plan was found for this range');
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
    public function searchIndex(PublicPaymentPlanSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $payment_plans = PaymentPlan::when($search_string, function ($query) use ($search_string, $search_date) {

            return $query->where('id', 'like', '%'.$search_string.'%')
            ->orWhere('name', 'like', '%'.$search_string.'%')
            ->orWhere('amount', 'like', '%'.$search_string.'%')
            ->when($search_date, function ($query, $search_date) {
                return $query->orWhere('created_at', '=', $search_date);
            });
        });

        // Check if the builder has any where clause
        if (count($payment_plans->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $payment_plans = $payment_plans->isPublic()->orderBy('level', 'asc')->limit(10)->get();

        // Return success
        if ($payment_plans) {
            if (count($payment_plans) > 0) {
                return $this->success($payment_plans);
            } else {
               return $this->noContent('No payment plan was found for this range');
            }
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
    public function show(PublicPaymentPlanShowRequest $request)
    {
        // Get a single payment plan
        $payment_plan = PaymentPlan::isPublic()->find($request->input('id'));

        // Return success
        if ($payment_plan) {

            if ($request->input('properties')) {
                $payment_plan = $payment_plan->load('benefits');
            }

            return $this->success($payment_plan);
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
    public function pay(PublicPaymentPlanPayRequest $request)
    {
        // Get a single payment plan
        $payment_plan = PaymentPlan::isPublic()->find($request->input('id'));

        if ($payment_plan) {

            // Create a payment
            $payment = new Payment;
            $payment->user_id = auth()->user()->id;
            $payment->account_id = $request->input('account_id');
            $payment->type = config('constants.payment.type.standard');
            $payment->currency = $payment_plan->currency;
            $payment->amount = $payment_plan->amount;

            // Update payment plan
            if ( $payment->save()) {
                return $this->entityCreated($payment,'Payment was created');
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
        $test = PaymentPlan::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
