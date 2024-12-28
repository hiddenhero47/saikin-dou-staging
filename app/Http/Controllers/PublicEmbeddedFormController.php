<?php

namespace App\Http\Controllers;

use App\Models\EmbeddedForm;
use App\Helpers\Helper;
use App\Http\Requests\PublicEmbeddedFormControllerRequests\PublicEmbeddedFormUrlRequest;
use App\Http\Requests\PublicEmbeddedFormControllerRequests\PublicEmbeddedFormCustomUrlRequest;
use Illuminate\Http\Request;

class PublicEmbeddedFormController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('formUrl','customUrl');
        $this->middleware('team:api')->except('formUrl','customUrl');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function formUrl(PublicEmbeddedFormUrlRequest $request)
    {
        $embedded_form = EmbeddedForm::where('form_url',$request->input('form_url'))->first();

        // Return success
        if ($embedded_form) {

            if ($request->input('properties')) {

                $embedded_form = $embedded_form->load([
                    'user' => function ($query) { 
                        return $query->select(['id', 'name']); 
                    }
                ]);
            }

            return $this->success($embedded_form);

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
    public function customUrl(PublicEmbeddedFormCustomUrlRequest $request)
    {
        $embedded_form = EmbeddedForm::where('custom_url',$request->input('custom_url'))->first();

        // Return success
        if ($embedded_form) {

            if ($request->input('properties')) {

                $embedded_form = $embedded_form->load([
                    'user' => function ($query) {
                        return $query->select(['id', 'name']);
                    }
                ]);
            }

            return $this->success($embedded_form);

        } else {

            // Return Failure
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
