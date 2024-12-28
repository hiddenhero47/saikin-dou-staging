<?php

namespace App\Http\Controllers;

use App\Mail\AccountVerificationMail;
use App\Mail\NotificationMail;
use App\Mail\PasswordResetMail;
use App\Mail\RegistrationMail;
use App\Models\User;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthControllerRequests\AuthLoginRequest;
use App\Http\Requests\AuthControllerRequests\AuthRegisterRequest;
use App\Http\Requests\AuthControllerRequests\AuthForgotPasswordRequest;
use App\Http\Requests\AuthControllerRequests\AuthResetPasswordRequest;
use App\Http\Requests\AuthControllerRequests\AuthChangePasswordRequest;
use App\Http\Requests\AuthControllerRequests\AuthVerifyEmailRequest;
use App\Http\Requests\AuthControllerRequests\AuthRefreshRequest;
use App\Http\Requests\AuthControllerRequests\AuthLogoutRequest;
use App\Http\Requests\AuthControllerRequests\AuthMeRequest;
use App\Http\Support\AuthSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class AuthController extends Controller
{
    /**
     * Permits login without password 
     *
     * @var boolean
     */
    protected $withoutPassword = false;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api')->except('login','register','refresh','forgotPassword','resetPassword','verifyEmail');
    }

    /**
     * Enable login without password
     * 
     * @param void
     * @return object
     */
    protected function withoutPassword()
    {
        $this->withoutPassword = true;
        return $this;
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     * @return array
     */
    protected function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    /**
     * Get a JWT via given credentials.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthLoginRequest $request)
    {
        // Validate User
        $user = User::where('email', $request->input('email'))->first();

        // Check if user has been banned
        if ($user->blocked == true) {
            return $this->forbiddenAccess('You are temporary banned, please contact support');
        }

        // Check if user has validated email
        if (!AuthSupport::checkEmailVerification($user)) {

            // Send an email to user containing email validation link
            Mail::to($user->email)->queue(new AccountVerificationMail(AuthSupport::createVerificationLink($user->email)));

            return $this->forbiddenAccess('Your email is not yet verified, check your mailbox for the verification link');
        }

        // Check if password is valid or login without password
        if ($this->withoutPassword || Hash::check($request->input('password'), $user->password)) {

            // Get JWT token
            $token = auth()->claims([])->login($user);

            // Return success
            $user = array_merge($user->toArray(), ['team_roles' => $user->teamsRoles()], $this->respondWithToken($token));
            return $this->success($user);
        }

        // Return Failure
        return $this->badRequest('Incorrect login details');
    }

    /**
     * Create a new user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(AuthRegisterRequest $request)
    {
        // Fill the user model
        $user = new User;
        $user->fill($request->toArray());

        // Additional params
        $user->name = AuthSupport::uniqueName($request->input('first_name'));
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->referrer_code = $request->input('referrer_code');

        // Save new user
        if (!$user->save()) {
            return $this->requestConflict('Failed to save details');
        }

        // Send a welcome email to the user
        Mail::to($user->email)->queue(new RegistrationMail($user->first_name.' '.$user->last_name));

        // Send a verification email to the user
        Mail::to($user->email)->queue(new AccountVerificationMail(AuthSupport::createVerificationLink($user->email)));

        // Return success
        return $this->entityCreated($user,'Registration successful');
    }

    /**
     * Creates a reset user password token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(AuthForgotPasswordRequest $request)
    {
        // Make a new token
        $password_reset = new PasswordReset;
        $id = Str::random(10);
        $token = Str::random(10);
        $password_reset->id = $id;
        $password_reset->token = Hash::make($token);
        $password_reset->email = $request->input('email');
        $password_reset->reset_form_link = $request->input('reset_form_link').'?token='.$token.$id;

        // Save new token
        if (!$password_reset->save()){
            return $this->unavailableService('Failed to reset password');
        }

        /**
         * Send an email to user containing reset link
         * Note that in order to make the search more precise and faster
         * The id of the password reset row has been appended to the token
         * that is being sent to the user.
         */
        Mail::to($password_reset->email)->queue(new PasswordResetMail($password_reset->reset_form_link));

        // Return success
        return $this->actionSuccess('Reset successful, please check email for link to reset password');
    }

    /**
     * Reset a user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(AuthResetPasswordRequest $request)
    {
        /**
         * Id of the password reset order is extracted from the user input using substr
         * check if email and id combination exists in password reset table
         */
        $password_reset = PasswordReset::where('email', $request->input('email'))
        ->where('id', substr($request->input('token'),-10))
        ->first();

        if (!$password_reset) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        // Get an ov config value
        $token_exp_time = config('ov.reset_password_token_exp_time', 60);

        // Check if token has expired
        if ((time() + ($token_exp_time*60)) <  strtotime($password_reset->created_at)) {
            return $this->forbiddenAccess('Token has expired');
        }

        // Make a new password
        $user = User::where('email', $request->input('email'))->first();
        $user->password = Hash::make($request->input('new_password'));

        // Save new password
        if (!$user->save()){
            return $this->unavailableService('Failed to reset password');
        }

        // Send an email to user informing about password change
        Mail::to($user->email)->queue(new NotificationMail('Your password was changed on '.date('Y-m-d H:i:s',time())));

        // Return success
        return $this->actionSuccess('Reset successful, please login');
    }

    /**
     * Change a user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(AuthChangePasswordRequest $request)
    {
        // Check if user exists
        $user = User::find(auth()->user()->id);

        // Exit if user was not found
        if (!$user) {
            return $this->notFound('Unable to identify account');
        }

        // Check if old password input matches password record
        if (!Hash::check($request->input('old_password'), $user->password)) {
            return $this->requestConflict('Old password is incorrect');
        }

        // Make a new password
        $user->password = Hash::make($request->input('new_password'));

        // Save new password
        if (!$user->save()){
            return $this->unavailableService('Failed to save password');
        }

        // Send an email to user informing about password change
        Mail::to($user->email)->queue(new NotificationMail('Your password was changed on '.date('Y-m-d H:i:s',time())));

        // Logout user
        auth()->logout();

        // Return success
        return $this->actionSuccess('Reset successful, please login again');
    }

    /**
     * Get the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(AuthMeRequest $request)
    {
        // Return the request sender details 
        return $this->success(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(AuthLogoutRequest $request)
    {
        if (auth()) {

            // Invalidate request sender token
            auth()->logout();
            return $this->actionSuccess('Successfully logged out');
        }

        // Return success
        return $this->actionSuccess('Token is already invalid');
    }

    /**
     * Refresh a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(AuthRefreshRequest $request)
    {
        try {

            // Return a new token to request sender and invalidate original token
            $refreshed_token = auth(false)->refresh();
            return $this->success($this->respondWithToken($refreshed_token));

        } catch (\Throwable $th) {

            // Return failure
            return $this->badRequest('Token is already invalid');
        }
    }

    /**
     * Verify a user email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyEmail(AuthVerifyEmailRequest $request)
    {
        // Check email
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return $this->notFound('Ensure that the email belongs to you');
        }

        // Validate token
        if (!AuthSupport::checkVerificationToken($request->input('email'), $request->input('token'))) {
            return $this->requestConflict('Currently unable to validate email');
        }

        // Additional params
        $user->email_verified_at = date('Y-m-d H:i:s', time());

        // Return success
        if ($user->save()) {
            return view('pages.email_verification')->with('status','success');
        } else {
            return view('pages.email_verification')->with('status','failure');
        }
    }

    /**
     * Validate existence of resource pool.
     *
     * @param  \Illuminate\Http\Request  $request
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
