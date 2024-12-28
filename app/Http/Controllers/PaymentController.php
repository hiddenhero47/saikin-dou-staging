<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Helpers\Helper;
use App\Helpers\PayStackSuite;
use App\Helpers\FlutterWaveSuite;
use App\Jobs\ProcessPaymentWebHookForPayStack;
use App\Jobs\ProcessPaymentWebHookForFlutterWave;
use App\Jobs\ProcessPaymentVerificationForPayStack;
use App\Jobs\ProcessPaymentVerificationForFlutterWave;
use App\Http\Requests\PaymentControllerRequests\PaymentDestroyRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentIndexRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentFilterRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentSearchRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentShowRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentProvidersRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentMeRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentStoreRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentUpdateRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentWebhookRequest;
use App\Http\Requests\PaymentControllerRequests\PaymentVerifyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('providers','webhook');
        $this->middleware('team:api')->except('me','show','providers','webhook');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PaymentIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all payment with all their relations
            $payments = Payment::with([
               'user'
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } elseif ($request->input('deleted')){

            // Get all deleted payment with all their relations
            $payments = Payment::onlyTrashed()->with([
                'user'
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all payment with out their relations
            $payments = Payment::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($payments) {

            if (count($payments) > 0) {
                return $this->success($payments);
            } else {
               return $this->noContent('Payments were not found');
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
    public function filterIndex(PaymentFilterRequest $request)
    {
        $user_id = is_null($request->input('user_id'))? false : Helper::escapeForLikeColumnQuery($request->input('user_id'));
        $type = is_null($request->input('type'))? false : Helper::escapeForLikeColumnQuery($request->input('type'));
        $amount = is_null($request->input('amount'))? false : Helper::formatForNumericColumnQuery($request->input('amount'));
        $currency = is_null($request->input('currency'))? false : Helper::escapeForLikeColumnQuery($request->input('currency'));
        $start_date = is_null($request->input('start_date'))? false : Helper::stringToCarbonDate($request->input('start_date'));
        $end_date = is_null($request->input('end_date'))? false : Helper::stringToCarbonDate($request->input('end_date'));
        $paid = is_null($request->input('paid'))? false : Helper::formatForBooleanColumnQuery($request->input('paid'));
        $confirmed = is_null($request->input('confirmed'))? false : Helper::formatForBooleanColumnQuery($request->input('confirmed'));
        $method = is_null($request->input('method'))? false : Helper::escapeForLikeColumnQuery($request->input('method'));
        $pagination = is_null($request->input('pagination'))? true : (boolean) $request->input('pagination');

        // Build search query
        $payments = Payment::when($user_id, function ($query, $user_id) {
            return $query->where('user_id', $user_id);

        })->when($type, function ($query, $type) {
            return $query->where('type', $type);

        })->when($amount, function ($query, $amount) {
            return $query->where('amount', 'like', '%'.$amount.'%');

        })->when($currency, function ($query, $currency) {
            return $query->where('currency', 'like', '%'.$currency.'%');

        })->when($start_date, function ($query, $start_date) {
            return $query->whereDate('created_at', '>=', $start_date);

        })->when($end_date, function ($query, $end_date) {
            return $query->whereDate('created_at', '<=', $end_date);

        })->when($paid === '1', function ($query) {
            return $query->where('paid', true);

        })->when($paid === '0', function ($query) {
            return $query->where('paid', false);

        })->when($confirmed === '1', function ($query) {
            return $query->where('confirmed', true);

        })->when($confirmed === '0', function ($query) {
            return $query->where('confirmed', false);

        })->when($method, function ($query, $method) {
            return $query->where('method', 'like', '%'.$method.'%');

        });

        // Check if the builder has any where clause
        if (count($payments->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $payments = $payments->orderBy('created_at', 'desc');

        // Execute with pagination required
        if ($pagination) {
            $payments = $payments->take(1000)->paginate(25);
        }

        // Execute without pagination required
        if (!$pagination) {
            $payments = $payments->take(1000)->get();
        }

        // Return success
        if ($payments) {
            if (count($payments) > 0) {
                return $this->success($payments);
            } else {
               return $this->noContent('No payment was found for this range');
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
    public function searchIndex(PaymentSearchRequest $request)
    {
        $search_string = is_null($request->input('search'))? false : Helper::escapeForLikeColumnQuery($request->input('search'));
        $search_date = is_null($request->input('search'))? false : Helper::stringToCarbonDate($request->input('search'));

        // Build search query
        $payments = Payment::when($search_string, function ($query) use ($request, $search_string, $search_date) {

            return $query->where('id', 'like', '%'.$search_string.'%')
            ->orWhere('user_id', 'like', '%'.$search_string.'%')
            ->orWhere('pfm', 'like', '%'.$search_string.'%')
            ->orWhere('amount', 'like', '%'.$search_string.'%')
            ->orWhereJsonContains('details', $search_string)
            ->orWhere('reference', 'like', '%'.$search_string.'%')
            ->when($search_date, function ($query, $search_date) {
                return $query->orWhere('created_at', '=', $search_date);
            });
        });

        // Check if the builder has any where clause
        if (count($payments->getQuery()->wheres) < 1){
            // Return failure
            return $this->requestConflict('No value to filter by');
        }

        // Execute search query
        $payments = $payments->orderBy('created_at', 'desc')->limit(10)->get();

        // Return success
        if ($payments) {
            if (count($payments) > 0) {
                return $this->success($payments);
            } else {
               return $this->noContent('No payment was found for this range');
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
    public function store(PaymentStoreRequest $request)
    {
        // Fill the payment model
        $payment = new Payment;
        $payment = $payment->fill($request->toArray());

        // Additional params
        $payment->user_id = auth()->user()->id;
        $payment->type = $request->input('type') ?? config('constants.payment.type.standard');

        // Return success
        if ($payment->save()) {
            return $this->entityCreated($payment,'Payment was saved');
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
    public function show(PaymentShowRequest $request)
    {
        // Use payment model passed in from request authorization
        $payment = $request->payment;

        // Return success
        if ($payment) {

            if ($request->input('properties')) {
                $payment = $payment->load(
                    'user'
                );
            }

            return $this->success($payment);
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
    public function providers(PaymentProvidersRequest $request)
    {
        $providers = [
            "pay_stack_key" => config('ov.pay_stack_public_key'),
            "flutter_wave_key" => config('ov.flutter_wave_public_key'),
        ];

        // Return success
        if ($providers) {
            return $this->success($providers);
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
    public function webhook(PaymentWebhookRequest $request)
    {
        // Request
        $header = $request->header();
        $body = $request->input();
        $content = $request->getContent();
        $ip = Helper::getClientIpAddress();

        if ($request->provider === config('constants.payment.provider.paystack')) {

            $PayStackSuite = PayStackSuite::webhook()
            ->setRequestHeader($header)->setRequestBody($body)->setRequestContent($content)->setRequestIp($ip)
            ->setSecretKey(config('ov.pay_stack_secret_key'));

            if ($PayStackSuite->isAuthorized()) {

                if ($PayStackSuite->isEventType('charge.success')) {
                    ProcessPaymentWebHookForPayStack::dispatch($content);
                }

                // Return success
                return $this->actionSuccess();
            }
            return $this->forbiddenAccess();
        }

        if ($request->provider === config('constants.payment.provider.flutterwave')) {

            $FlutterWaveSuite = FlutterWaveSuite::webhook()
            ->setRequestHeader($header)->setRequestBody($body)->setRequestContent($content)->setRequestIp($ip)
            ->setSecretKey(config('ov.flutter_wave_secret_key'))
            ->setVerificationHash(config('ov.flutter_wave_hash'));

            if ($FlutterWaveSuite->isAuthorized()) {

                if ($FlutterWaveSuite->isEventType('charge.completed')) {
                    ProcessPaymentWebHookForFlutterWave::dispatch($content);
                }

                // Return success
                return $this->actionSuccess();
            }
            return $this->forbiddenAccess();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(PaymentVerifyRequest $request)
    {
        if ($request->provider === config('constants.payment.provider.paystack')) {

            return ProcessPaymentVerificationForPayStack::dispatch($request->input('id')) ? $this->actionSuccess() : $this->unavailableService();
        }

        if ($request->provider === config('constants.payment.provider.flutterwave')) {

            return ProcessPaymentVerificationForFlutterWave::dispatch($request->input('id')) ? $this->actionSuccess() : $this->unavailableService();
        }
    }

    /**
     * Display the authenticated user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(PaymentMeRequest $request)
    {
        // Get a user payments
        $payments = Payment::where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc')
        ->take(1000)
        ->paginate(25);

        // Return success
        if ($payments) {
            return $this->success($payments);
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
    public function update(PaymentUpdateRequest $request)
    {
        // Use payment model passed in from request authorization
        $payment = $request->payment;

        if ($payment) {

            // Fill requestor input
            $payment->fill($request->only('type','amount'));

            // Update payment
            if ($payment->update()) {
                return $this->actionSuccess('Payment was updated');
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
    public function destroy(PaymentDestroyRequest $request)
    {
        // Use payment model passed in from request authorization
        $payment = $request->payment;

        if ($payment) {

            // Delete payment
            if ($payment->delete()) {
                return $this->actionSuccess('Payment was deleted');
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
        $test = Payment::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
