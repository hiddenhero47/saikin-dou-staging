<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reset Password Token Expiration Time
    |--------------------------------------------------------------------------
    |
    | This value is the reset password token expiration time of your application,
    | This value is used by the jwt package to set the application's reset
    | password token expiration time, the value is stated in minutes by default
    | is set at 30 which is equal to half an hour this value can not be less than
    | 1 minute and should be kept below 60 minutes (1 hour) for better security.
    */
    'reset_password_token_exp_time' => 30,

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | This value is used by the email verification functions and can only be  
    | set to boolean (true) or (false) setting this to true will activate email 
    | validation checks, this can be changed at any point in the life of the 
    | of the application but it is recommended to set it this before deploying
    */
    'email_verification' => env('OV_EMAIL_VERIFICATION',true),

    /*
    |--------------------------------------------------------------------------
    | Grace Period For Email Verification
    |--------------------------------------------------------------------------
    |
    | This value is used by the isEmailVerified function and should be set to an
    | unsigned integer, this value can be set to (int) 0 to ensure instant  
    | verification before access to app, please note that value is specified in 
    | minutes for this value to be effective email_verification must be set to true
    |
    */
    'grace_period' => 0,

    /*
    |--------------------------------------------------------------------------
    | Api Exception Handler
    |--------------------------------------------------------------------------
    |
    | This is used by the exception handler located at App\Exceptions\Handler.php
    | to set exception handling to use json responses from the ApiResponderTrait
    | located at App\Traits\ApiResponderTrait.php as return values for all laravel
    | and custom exceptions. Set to false to return laravel exception render.
    |
    */
    'api_exception_handler' => env('OV_EXCEPTION_HANDLER',true),

    /*
    |--------------------------------------------------------------------------
    | Api Exception Report
    |--------------------------------------------------------------------------
    |
    | This is used by the exception handler located at App\Exceptions\Handler.php
    | to report all laravel and custom exceptions, that are specified within the
    | report method, Set to false to avoid exception reporting. Please note that
    | this does not affect exception logging.
    |
    */
    'api_exception_report' => env('OV_EXCEPTION_REPORT',false),

    /*
    |--------------------------------------------------------------------------
    | Discord Service Account
    |--------------------------------------------------------------------------
    |
    | This is used by the discord wrapper located at App\Helpers\DiscordSuite.php
    | to interact with discord services, that are specified within the
    | suit method, Set to the corresponding values provided by discord.
    |
    */
    'discord_webhook_url' => env('DISCORD_WEBHOOK_URL', 'https://discord.com/api/webhooks/1234567890/abcdefghijklmnopqrstuvwxyz'),

    /*
    |--------------------------------------------------------------------------
    | Pay Stack Account Live Key, Test Key
    |--------------------------------------------------------------------------
    |
    | This is used by various functions which includes the showProviders 
    | method located at App\Http\Controllers/PaymentController.php
    | to interact with pay stack services, that are specified within the
    | pay stack service, Set to the corresponding values provided by pay stack.
    |
    */
    'pay_stack_public_key' => env('PAY_STACK_PUBLIC_KEY', 'abcdefghijklmnopqrstuvwxyz'),
    'pay_stack_secret_key' => env('PAY_STACK_SECRET_KEY', 'abcdefghijklmnopqrstuvwxyz'),
    'pay_stack_client_url_payment_verification' => env('PAY_STACK_CLIENT_URL_PAYMENT_VERIFICATION', 'https://api.paystack.co/transaction/verify/:reference'),

    /*
    |--------------------------------------------------------------------------
    | Flutter Wave Account Live Key, Test Key
    |--------------------------------------------------------------------------
    |
    | This is used by various functions which includes the showProviders 
    | method located at App\Http\Controllers/PaymentController.php
    | to interact with flutter wave services, that are specified within the
    | flutter wave service, Set to the corresponding values provided by flutter wave.
    |
    */
    'flutter_wave_public_key' => env('FLUTTER_WAVE_PUBLIC_KEY', 'abcdefghijklmnopqrstuvwxyz'),
    'flutter_wave_secret_key' => env('FLUTTER_WAVE_SECRET_KEY', 'abcdefghijklmnopqrstuvwxyz'),
    'flutter_wave_hash' => env('FLUTTER_WAVE_HASH', 'abcdefghijklmnopqrstuvwxyz'),
    'flutter_wave_client_url_payment_verification' => env('FLUTTER_WAVE_CLIENT_URL_PAYMENT_VERIFICATION', 'https://api.flutterwave.com/v3/transactions/:id/verify'),
];