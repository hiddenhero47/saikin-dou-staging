<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['api'], 'prefix' => 'account'], function ($router) {

    // Account Routes
    Route::get('index', 'AccountController@index');
    Route::post('filter/index', 'AccountController@filterIndex');
    Route::post('search/index', 'AccountController@searchIndex');
    Route::post('store', 'AccountController@store');
    Route::get('show', 'AccountController@show');
    Route::get('link-wa-qr-code', 'AccountController@linkWhatsAppQRCode');
    Route::get('poll-wa-qr-code', 'AccountController@pollWhatsAppQRCode');
    Route::get('link-wa-phone-number', 'AccountController@linkWhatsAppPhoneNumber');
    Route::get('fetch-wa-groups', 'AccountController@fetchWhatsAppGroups');
    Route::get('me', 'AccountController@me');
    Route::post('update', 'AccountController@update');
    Route::post('delete', 'AccountController@destroy');
    Route::get('test', 'AccountController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'auth'], function ($router) {

    // Auth Routes
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('forgot', 'AuthController@forgotPassword');
    Route::post('reset', 'AuthController@resetPassword');
    Route::post('change', 'AuthController@changePassword');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::get('verify', 'AuthController@verifyEmail');
    Route::get('test', 'AuthController@test');

    // Socialite Routes
    Route::get('socialite/facebook', 'SocialiteController@redirectToFacebookProvider');
    Route::get('socialite/facebook/callback', 'SocialiteController@handleFacebookProviderCallback');
    Route::get('socialite/linkedin', 'SocialiteController@redirectToLinkedinProvider');
    Route::get('socialite/linkedin/callback', 'SocialiteController@handleLinkedinProviderCallback');
    Route::get('socialite/google', 'SocialiteController@redirectToGoogleProvider');
    Route::get('socialite/google/callback', 'SocialiteController@handleGoogleProviderCallback');
    Route::get('socialite/apple', 'SocialiteController@redirectToAppleProvider');
    Route::get('socialite/apple/callback', 'SocialiteController@handleAppleProviderCallback');
    Route::get('socialite/test', 'SocialiteController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'benefit'], function ($router) {

    // Benefit Routes
    Route::get('index', 'BenefitController@index');
    Route::post('store', 'BenefitController@store');
    Route::get('show', 'BenefitController@show');
    Route::post('update', 'BenefitController@update');
    Route::post('assign', 'BenefitController@assign');
    Route::post('retract', 'BenefitController@retract');
    Route::post('delete', 'BenefitController@destroy');
    Route::get('test', 'BenefitController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'broadcast'], function ($router) {

    // Broadcast Routes
    Route::get('index', 'BroadcastController@index');
    Route::post('filter/index', 'BroadcastController@filterIndex');
    Route::post('search/index', 'BroadcastController@searchIndex');
    Route::post('store', 'BroadcastController@store');
    Route::get('show', 'BroadcastController@show');
    Route::get('me', 'BroadcastController@me');
    Route::post('update', 'BroadcastController@update');
    Route::post('preview', 'BroadcastController@preview');
    Route::get('placeholder/index', 'BroadcastController@placeHolderIndex');
    Route::post('placeholder/update', 'BroadcastController@placeHoldersUpdate');
    Route::post('delete', 'BroadcastController@destroy');
    Route::get('test', 'BroadcastController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'broadcast-template'], function ($router) {

    // Broadcast Template Routes
    Route::get('index', 'BroadcastTemplateController@index');
    Route::post('filter/index', 'BroadcastTemplateController@filterIndex');
    Route::post('search/index', 'BroadcastTemplateController@searchIndex');
    Route::post('store', 'BroadcastTemplateController@store');
    Route::get('show', 'BroadcastTemplateController@show');
    Route::get('me', 'BroadcastTemplateController@me');
    Route::post('update', 'BroadcastTemplateController@update');
    Route::post('delete', 'BroadcastTemplateController@destroy');
    Route::get('test', 'BroadcastTemplateController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'browser'], function ($router) {

    // Browser Routes
    Route::get('index', 'BrowserController@index');
    Route::post('filter/index', 'BrowserController@filterIndex');
    Route::post('search/index', 'BrowserController@searchIndex');
    Route::post('store', 'BrowserController@store');
    Route::get('me', 'BrowserController@me');
    Route::get('show', 'BrowserController@show');
    Route::post('update', 'BrowserController@update');
    Route::post('open/management', 'BrowserController@openByManagement');
    Route::post('close/management', 'BrowserController@closeByManagement');
    Route::post('delete', 'BrowserController@destroy');
    Route::get('test', 'BrowserController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'cache'], function ($router) {

    // Cache Routes
    Route::get('index', 'CacheController@index');
    Route::post('clear', 'CacheController@clear');
    Route::get('test', 'CacheController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'contact'], function ($router) {

    // Contact Routes
    Route::get('index', 'ContactController@index');
    Route::post('filter/index', 'ContactController@filterIndex');
    Route::post('search/index', 'ContactController@searchIndex');
    Route::post('store', 'ContactController@store');
    Route::get('show', 'ContactController@show');
    Route::get('me', 'ContactController@me');
    Route::post('update', 'ContactController@update');
    Route::post('delete', 'ContactController@destroy');
    Route::get('socialite/google', 'ContactController@redirectToGoogleProvider');
    Route::get('socialite/google/callback', 'ContactController@handleGoogleProviderCallback');
    Route::get('test', 'ContactController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'embedded-form'], function ($router) {

    // Embedded Form Routes
    Route::get('index', 'EmbeddedFormController@index');
    Route::post('filter/index', 'EmbeddedFormController@filterIndex');
    Route::post('search/index', 'EmbeddedFormController@searchIndex');
    Route::post('store', 'EmbeddedFormController@store');
    Route::get('show', 'EmbeddedFormController@show');
    Route::get('me', 'EmbeddedFormController@me');
    Route::post('update', 'EmbeddedFormController@update');
    Route::post('delete', 'EmbeddedFormController@destroy');
    Route::get('test', 'EmbeddedFormController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'group'], function ($router) {

    // Group Routes
    Route::get('index', 'GroupController@index');
    Route::post('filter/index', 'GroupController@filterIndex');
    Route::post('search/index', 'GroupController@searchIndex');
    Route::post('store', 'GroupController@store');
    Route::get('show', 'GroupController@show');
    Route::get('me', 'GroupController@me');
    Route::post('update', 'GroupController@update');
    Route::post('delete', 'GroupController@destroy');
    Route::get('test', 'GroupController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'payment'], function ($router) {

    // Payment Routes
    Route::get('index', 'PaymentController@index');
    Route::post('filter/index', 'PaymentController@filterIndex');
    Route::post('search/index', 'PaymentController@searchIndex');
    Route::post('store', 'PaymentController@store');
    Route::get('me', 'PaymentController@me');
    Route::get('show', 'PaymentController@show');
    Route::get('show/provider', 'PaymentController@providers');
    Route::post('webhook/{provider}', 'PaymentController@webhook');
    Route::post('verify/{provider}', 'PaymentController@verify');
    Route::post('update', 'PaymentController@update');
    Route::post('delete', 'PaymentController@destroy');
    Route::get('test', 'PaymentController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'misc'], function ($router) {

    // Miscellaneous Routes
    Route::post('response', 'MiscellaneousController@responseTypes');
    Route::post('patcher', 'MiscellaneousController@applicationPatcher');
});

Route::group(['middleware' => ['api'], 'prefix' => 'permission'], function ($router) {

    // Permission Routes
    Route::get('index', 'PermissionController@index');
    Route::post('store', 'PermissionController@store');
    Route::get('show', 'PermissionController@show');
    Route::post('update', 'PermissionController@update');
    Route::post('assign', 'PermissionController@assign');
    Route::post('retract', 'PermissionController@retract');
    Route::post('delete', 'PermissionController@destroy');
    Route::get('test', 'PermissionController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'payment-plan'], function ($router) {

    // Payment Plan Routes
    Route::get('index', 'PaymentPlanController@index');
    Route::post('filter/index', 'PaymentPlanController@filterIndex');
    Route::post('search/index', 'PaymentPlanController@searchIndex');
    Route::post('store', 'PaymentPlanController@store');
    Route::get('show', 'PaymentPlanController@show');
    Route::post('update', 'PaymentPlanController@update');
    Route::post('approve/management', 'PaymentPlanController@approveByManagement');
    Route::post('unapprove/management', 'PaymentPlanController@unapproveByManagement');
    Route::post('delete', 'PaymentPlanController@destroy');
    Route::get('test', 'PaymentPlanController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'public/payment-plan'], function ($router) {

    // Public Payment Plan Routes
    Route::get('index', 'PublicPaymentPlanController@index');
    Route::post('filter/index', 'PublicPaymentPlanController@filterIndex');
    Route::post('search/index', 'PublicPaymentPlanController@searchIndex');
    Route::get('show', 'PublicPaymentPlanController@show');
    Route::post('pay', 'PublicPaymentPlanController@pay');
    Route::get('test', 'PublicPaymentPlanController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'public/embedded-form'], function ($router) {

    // Public Embedded Form Routes
    Route::get('form-url', 'PublicEmbeddedFormController@formUrl');
    Route::get('custom-url', 'PublicEmbeddedFormController@customUrl');
    Route::get('test', 'PublicEmbeddedFormController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'role'], function ($router) {

    // Role Routes
    Route::get('index', 'RoleController@index');
    Route::post('store', 'RoleController@store');
    Route::get('show', 'RoleController@show');
    Route::post('update', 'RoleController@update');
    Route::post('assign', 'RoleController@assign');
    Route::post('retract', 'RoleController@retract');
    Route::post('delete', 'RoleController@destroy');
    Route::get('test', 'RoleController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'stats'], function ($router) {

    // Statistics Routes
    Route::post('general', 'StatisticsController@general');
    Route::post('user', 'StatisticsController@user');
});

Route::group(['middleware' => ['api'], 'prefix' => 'setting'], function ($router) {

    // Setting Routes
    Route::get('index', 'SettingController@index');
    Route::post('filter/index', 'SettingController@filterIndex');
    Route::post('search/index', 'SettingController@searchIndex');
    Route::post('store', 'SettingController@store');
    Route::get('show', 'SettingController@show');
    Route::get('me', 'SettingController@me');
    Route::post('update', 'SettingController@update');
    Route::post('delete', 'SettingController@destroy');
    Route::get('test', 'SettingController@test');
});

Route::group(['middleware' => ['api'], 'prefix' => 'user'], function ($router) {

    // User Routes
    Route::get('index', 'UserController@index');
    Route::post('filter/index', 'UserController@filterIndex');
    Route::post('search/index', 'UserController@searchIndex');
    Route::post('store', 'UserController@store');
    Route::get('show', 'UserController@show');
    Route::get('me', 'UserController@me');
    Route::get('roles', 'UserController@showRolePermission');
    Route::post('relation', 'UserController@relation');
    Route::post('update', 'UserController@update');
    Route::post('block', 'UserController@block');
    Route::post('unblock', 'UserController@unblock');
    Route::post('delete', 'UserController@destroy');
    Route::get('test', 'UserController@test');
});