<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Benefit;
use App\Models\PaymentPlan;
use App\Http\Requests\BenefitControllerRequests\BenefitAssignRequest;
use App\Http\Requests\BenefitControllerRequests\BenefitDestroyRequest;
use App\Http\Requests\BenefitControllerRequests\BenefitIndexRequest;
use App\Http\Requests\BenefitControllerRequests\BenefitRetractRequest;
use App\Http\Requests\BenefitControllerRequests\BenefitShowRequest;
use App\Http\Requests\BenefitControllerRequests\BenefitStoreRequest;
use App\Http\Requests\BenefitControllerRequests\BenefitUpdateRequest;
use Illuminate\Http\Request;

class BenefitController extends Controller
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(BenefitIndexRequest $request)
    {
        if ($request->input('properties')){

            // Get all benefits with all their relations
            $benefits = Benefit::with([
                'paymentPlans',
            ])->orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);

        } else {

            // Get all benefits with out their relations
            $benefits = Benefit::orderBy('created_at', 'desc')
            ->take(1000)
            ->paginate(25);
        }

        // Return success
        if ($benefits) {

            if (count($benefits) > 0) {
                return $this->success($benefits);
            } else {
               return $this->noContent('No benefits were found');
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
    public function store(BenefitStoreRequest $request)
    {
        // Check if similar benefit exists
        $benefit = Benefit::where('name', $request->input('name'))->first();
        if ($benefit) {
            return $this->requestConflict('A similar benefit already exists');
        }

        // Fill the benefit model
        $benefit = new Benefit;
        $benefit = $benefit->fill($request->toArray());

        // Return success
        if ($benefit->save()) {
            return $this->entityCreated($benefit,'Benefit was saved');
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
    public function show(BenefitShowRequest $request)
    {
        // Get a single benefit
        $benefit = Benefit::find($request->input('benefit_id'));

        // Return success
        if ($benefit) {

            if ($request->input('properties')) {
                $benefit = $benefit->load('paymentPlans');
            }

            return $this->success($benefit);
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
    public function update(BenefitUpdateRequest $request)
    {
        // Find the supplied benefit
        $benefit = Benefit::find($request->input('benefit_id'));

        if ($benefit) {

            // Fill requestor input
            $benefit->fill($request->toArray());

            // Update benefit
            if ($benefit->update()) {
                return $this->actionSuccess('Benefit was updated');
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
     * Assign an benefit to a payment plan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function assign(BenefitAssignRequest $request)
    {
        // Find the supplied benefit and payment plan
        $benefit = Benefit::find($request->input('benefit_id'));
        $payment_plan = PaymentPlan::find($request->input('payment plan_id'));

        if (!$benefit) {
            // Return failure
            return $this->notFound('The specified benefit was not found');
        }

        if (!$payment_plan) {
            // Return failure
            return $this->notFound('The specified payment plan was not found');
        }

        // Check if payment plan already has benefit
        $benefits = $payment_plan->benefits()->get();
        if ($benefits && $benefits->contains('name', $benefit->name)) {
            // Return failure
            return $this->requestConflict('The specified payment plan currently has this benefit');
        }

        if ($benefit && $payment_plan) {

            // Assign benefit
            if ($payment_plan->attachBenefit($benefit)) {

                return $this->actionSuccess('Benefit was assigned');
            } else {
                return $this->unavailableService('Unable to assign benefit');
            }
        }

        // Return failure
        return $this->requestConflict();
    }

    /**
     * Update the specified resource in storage.
     * Retract an benefit from a payment plan
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function retract(BenefitRetractRequest $request)
    {
        // Find the supplied benefit and payment plan
        $benefit = Benefit::find($request->input('benefit_id'));
        $payment_plan = PaymentPlan::find($request->input('payment plan_id'));

        if (!$benefit) {
            // Return failure
            return $this->notFound('The specified benefit was not found');
        }

        if (!$payment_plan) {
            // Return failure
            return $this->notFound('The specified payment plan was not found');
        }

        // Check if payment plan already has benefit
        $benefits = $payment_plan->benefits()->get();
        if (!$benefits || !$benefits->contains('name', $benefit->name)) {
            // Return failure
            return $this->requestConflict('The specified payment plan currently does not have this benefit');
        }

        if ($benefit && $payment_plan) {

            // Unassign benefit
            if ($payment_plan->detachBenefit($benefit)) {

                return $this->actionSuccess('Benefit was retracted');
            } else {
                return $this->unavailableService('Unable to retract benefit');
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
    public function destroy(BenefitDestroyRequest $request)
    {
        // Find the supplied benefit
        $benefit = Benefit::find($request->input('benefit_id'));

        if ($benefit) {

            // Delete benefit
            if ($benefit->delete()) {
                return $this->actionSuccess('Benefit was deleted');
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
        $test = Benefit::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
