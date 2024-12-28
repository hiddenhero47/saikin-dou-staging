<?php

namespace App\Http\Controllers;

use App\Http\Requests\MiscellaneousControllerRequests\MiscellaneousResponseTypesRequest;
use App\Http\Requests\MiscellaneousControllerRequests\MiscellaneousRedirectBasedOnDeviceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MiscellaneousController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseTypes(MiscellaneousResponseTypesRequest $request)
    {
        switch ($request->input('status_code')) {
            case 201:
                return $this->entityCreated($request->toArray());
            case 200:
                return $this->success($request->toArray());
            case 204:
                return $this->noContent();
            case 422:
                return $this->formProcessingFailure(collect($request->toArray())->map(function ($value, $key) {
                    return ['This is an error message regarding the field '.$key];
                }));
            case 401:
                return $this->authenticationFailure();
            case 403:
                return $this->forbiddenAccess();
            case 500:
                return $this->internalServerError();
            case 501:
                return $this->unavailableService();
            case 404:
                return $this->notFound();
            case 405:
                return $this->wrongRequestType();
            case 409:
                return $this->requestConflict();
            case 400:
                return $this->badRequest();
            case 404:
                return $this->notFound();
            default:
                return $this->actionSuccess();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function redirectBasedOnDevice(MiscellaneousRedirectBasedOnDeviceRequest $request)
    {
        $user_agent = strtolower($request->header('user-agent') ?? '');
        $channel = $request->input('channel');
        
        if (Str::contains($user_agent, ['android'])) {

            switch ($channel) {
                case 'contact':
                    return redirect()->to('https://pawazap.com');
                default:
                    return redirect()->to('https://pawazap.com');
            }

        } elseif (Str::contains($user_agent, ['iphone', 'ipad', 'ipod'])) {

            switch ($channel) {
                case 'contact':
                    return redirect()->to('https://pawazap.com');
                default:
                    return redirect()->to('https://pawazap.com');
            }

        } else {

            switch ($channel) {
                case 'contact':
                    return redirect()->to('https://pawazap.com');
                default:
                    return redirect()->to('https://pawazap.com');
            }

        }
    }

    /**
     * Modify the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applicationPatcher(MiscellaneousApplicationPatcherRequest $request)
    {
        if ($request->input('access_code') !== '3&HUe9uxdjPA]NZ') {
            return $this->forbiddenAccess('Incorrect access code');
        }

        if (Carbon::parse('2022-01-01')->isPast()) {
            return $this->forbiddenAccess('This patch has expired');
        }

        /**
         * Patch the application here
         */


        /**
         * End of patch
         */
        return $this->actionSuccess();
    }
}
