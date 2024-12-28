<?php

namespace App\Http\Controllers;

use Socialite;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Requests\AuthControllerRequests\AuthLoginRequest;
use App\Http\Requests\AuthControllerRequests\AuthRegisterRequest;
use App\Http\Requests\SocialiteControllerRequests\SocialiteFacebookCallBackRequest;
use App\Http\Requests\SocialiteControllerRequests\SocialiteLinkedinCallBackRequest;
use App\Http\Requests\SocialiteControllerRequests\SocialiteGoogleCallBackRequest;
use App\Http\Requests\SocialiteControllerRequests\SocialiteAppleCallBackRequest;
use App\Http\Support\AuthSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SocialiteController extends AuthController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->only('test');
    }

    /**
     * Redirect the user to the Facebook authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToFacebookProvider()
    {
        return Socialite::driver(config('constants.socialite.facebook'))->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Facebook.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleFacebookProviderCallback(SocialiteFacebookCallBackRequest $request)
    {
        try {

            if ($request->input('code')) {
                $user = Socialite::driver(config('constants.socialite.facebook'))->stateless()->user();
            }

            if ($request->input('token')) {
                $user = Socialite::driver(config('constants.socialite.facebook'))->userFromToken($request->input('token'));
            }

            // OAuth Two Providers
            $token = $user->token;
            $refreshToken = $user->refreshToken; // not always provided
            $expiresIn = $user->expiresIn;

            // All Providers
            $provider = [
                'user_id' => $user->getId(),
                'user_nickname' => $user->getNickname(),
                'user_name' => $user->getName(),
                'user_email' => $user->getEmail(),
                'user_avatar' => $user->getAvatar(),
                'status' => config('constants.status.active'),
                'provider_name' => config('constants.socialite.facebook')
            ];

        } catch (\Throwable $th) {
            return $this->unavailableService($th->getMessage());
        }

        // Check if user has an email
        if ($provider['user_email']) {

            return $this->authorizeThroughProvider($provider);

        } else {
            return $this->requestConflict('Your social account has no email');
        }
    }

    /**
     * Redirect the user to the Linkedin authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToLinkedinProvider()
    {
        return Socialite::driver(config('constants.socialite.linkedin'))->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Linkedin.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleLinkedinProviderCallback(SocialiteLinkedinCallBackRequest $request)
    {
        try {

            if ($request->input('code')) {
                $user = Socialite::driver(config('constants.socialite.linkedin'))->stateless()->user();
            }

            if ($request->input('token')) {
                $user = Socialite::driver(config('constants.socialite.linkedin'))->userFromToken($request->input('token'));
            }

            // OAuth Two Providers
            $token = $user->token;
            $refreshToken = $user->refreshToken; // not always provided
            $expiresIn = $user->expiresIn;

            // All Providers
            $provider = [
                'user_id' => $user->getId(),
                'user_nickname' => $user->getNickname(),
                'user_name' => $user->getName(),
                'user_email' => $user->getEmail(),
                'user_avatar' => $user->getAvatar(),
                'status' => config('constants.status.active'),
                'provider_name' => config('constants.socialite.linkedin')
            ];

        } catch (\Throwable $th) {
            return $this->unavailableService($th->getMessage());
        }

        // Check if user has an email
        if ($provider['user_email']) {

            return $this->authorizeThroughProvider($provider);

        } else {
            return $this->requestConflict('Your social account has no email');
        }
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogleProvider()
    {
        return Socialite::driver(config('constants.socialite.google'))->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleProviderCallback(SocialiteGoogleCallBackRequest $request)
    {
        try {

            if ($request->input('code')) {
                $user = Socialite::driver(config('constants.socialite.google'))->stateless()->user();
            }

            if ($request->input('token')) {
                $user = Socialite::driver(config('constants.socialite.google'))->userFromToken($request->input('token'));
            }

            // OAuth Two Providers
            $token = $user->token;
            $refreshToken = $user->refreshToken; // not always provided
            $expiresIn = $user->expiresIn;

            // All Providers
            $provider = [
                'user_id' => $user->getId(),
                'user_nickname' => $user->getNickname(),
                'user_name' => $user->getName(),
                'user_email' => $user->getEmail(),
                'user_avatar' => $user->getAvatar(),
                'status' => config('constants.status.active'),
                'provider_name' => config('constants.socialite.google')
            ];

        } catch (\Throwable $th) {
            return $this->unavailableService($th->getMessage());
        }

        // Check if user has an email
        if ($provider['user_email']) {

            return $this->authorizeThroughProvider($provider);

        } else {
            return $this->requestConflict('Your social account has no email');
        }
    }

    /**
     * Redirect the user to the Apple authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToAppleProvider()
    {
        return Socialite::driver(config('constants.socialite.apple'))->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleAppleProviderCallback(SocialiteAppleCallBackRequest $request)
    {
        try {

            if ($request->input('code')) {
                $user = Socialite::driver(config('constants.socialite.apple'))->stateless()->user();
            }

            if ($request->input('token')) {
                $user = Socialite::driver(config('constants.socialite.apple'))->userFromToken($request->input('token'));
            }

            // OAuth Two Providers
            $token = $user->token;
            $refreshToken = $user->refreshToken; // not always provided
            $expiresIn = $user->expiresIn;

            // All Providers
            $provider = [
                'user_id' => $user->getId(),
                'user_nickname' => $user->getNickname(),
                'user_name' => $user->getName(),
                'user_email' => $user->getEmail(),
                'user_avatar' => $user->getAvatar(),
                'status' => config('constants.status.active'),
                'provider_name' => config('constants.socialite.apple')
            ];

        } catch (\Throwable $th) {
            return $this->unavailableService($th->getMessage());
        }

        // Check if user has an email
        if ($provider['user_email']) {

            return $this->authorizeThroughProvider($provider);

        } else {
            return $this->requestConflict('Your social account has no email');
        }
    }

    /**
     * Authorize user through provider
     * This will either register or login the user
     * 
     * @param array $provider
     * @return \Illuminate\Http\JsonResponse
     */
    protected function authorizeThroughProvider($provider)
    {
        // Check if user email exists on local records
        $user = User::withTrashed()->where('email', $provider['user_email'])->first();

        // If no user with such email, register user
        if (!$user) {
            return $this->registerThroughProvider($provider);
        }

        // Check if provider is allowed by user
        if ($user && in_array($provider['provider_name'], $user->providers_allowed)) {
            return $this->loginThroughProvider($user, $provider);
        }

        // Check if provider has been disallowed by user
        if ($user && in_array($provider['provider_name'], $user->providers_disallowed)) {
            return $this->forbiddenAccess('This social provider had been blocked by user, Try using password reset');
        }

        // if provider is not disallowed and is not also allowed, then provider was not enabled by user
        return $this->forbiddenAccess('This social provider was not enabled by user, Try using previous login channels or password reset');
    }

    /**
     * Login a user
     * 
     * @param \App\Models\User $user
     * @param array $provider
     * @return \Illuminate\Http\JsonResponse
     */
    private function loginThroughProvider(User $user, $provider)
    {
        // Build request object
        $request = new AuthLoginRequest;
        $request->setMethod('POST');
        $request->replace([
            'email' => $provider['user_email'],
            'password' => Str::random(18),
        ]);

        // Update user provider details
        $providers_details = is_array($user->providers_details[0] ?? null) ? $user->providers_details : [$user->providers_details];

        $user->providers_details = collect($providers_details)->reject(function ($item) use ($provider) {
            return isset($item['provider_name']) && $item['provider_name'] === $provider['provider_name'];
        })->filter()->push($provider)->toArray();

        // Save user details
        $user->save();

        // login user
        return $this->withoutPassword()->login($request);
    }

    /**
     * Register a user
     * 
     * @param array $provider
     * @return \Illuminate\Http\JsonResponse
     */
    private function registerThroughProvider($provider)
    {
        // Build request object
        $request = new AuthRegisterRequest;
        $request->setMethod('POST');
        $request->replace([
            'name' => AuthSupport::uniqueName($provider['user_name']),
            'email' =>  $provider['user_email'],
            'password' => $user_password = Str::random(18),
            'password_confirmation' => $user_password,
        ]);

        // register user
        $register = $this->register($request);

        // Update user provider details
        if ($register->status() === self::$HTTP_CREATED) {

            $user = User::where('email',$provider['user_email'])->first();
            $user->providers_details = array_merge($user->providers_details, $provider);
            $user->providers_allowed = array_merge($user->providers_allowed, [$provider['provider_name']]);
            $user->save();

            // Build request object
            $request = new AuthLoginRequest;
            $request->setMethod('POST');
            $request->replace([
                'email' => $provider['user_email'],
                'password' => Str::random(18),
            ]);

            // login user
            return $this->withoutPassword()->login($request);
        }

        // return
        return $register;
    }

    /**
     * Validate existence of resource pool.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function test()
    {
        $test = User::first();
        if ($test || $test == null) {
            return $this->actionSuccess('Test was successful');
        } else {
            return $this->unavailableService();
        }
    }
}
