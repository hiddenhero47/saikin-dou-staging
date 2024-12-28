<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use App\Helpers\DiscordSuite;
use App\Helpers\Helper;
use App\Traits\ApiResponderTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    use ApiResponderTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        // Get an ov config value
        $api_exception_report = config('ov.api_exception_report', false);

        if ($api_exception_report) {

            // Thrown when processing HTTP requests is unsuccessful
            if ($exception instanceof HttpException) {
                $DiscordSuite = DiscordSuite::messenger();
                $DiscordSuite->setWebhook(config('ov.discord_webhook_url'))->sendWebhook([
                    'Message' => $exception->getMessage(),
                    'Route' => request()->url(),
                    'File' => $exception->getFile(),
                    'Line' => $exception->getLine(),
                    'Code' => $exception->getCode(),
                    'User' => auth() && auth()->user() ? auth()->user()->name : null,
                    'ip' => Helper::getClientIpAddress(),
                ]);
            }

            // Thrown when an exception occurs.
            if ($exception instanceof Exception) {
                $DiscordSuite = DiscordSuite::messenger();
                $DiscordSuite->setWebhook(config('ov.discord_webhook_url'))->sendWebhook([
                    'Message' => $exception->getMessage(),
                    'Route' => request()->url(),
                    'File' => $exception->getFile(),
                    'Line' => $exception->getLine(),
                    'Code' => $exception->getCode(),
                    'User' => auth() && auth()->user() ? auth()->user()->name : null,
                    'ip' => Helper::getClientIpAddress(),
                ]);
            }
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Get an ov config value
        $api_exception_handler = config('ov.api_exception_handler', false);

        if ($request->expectsJson() && $api_exception_handler) {

            // Thrown when an error occurs when a user makes an unauthenticated request
            if ($exception instanceof AuthenticationException) {
                return $this->authenticationFailure();
            }

            // Thrown when a user makes requests that Auth service does not validated
            if ($exception instanceof AuthorizationException) {
                return $this->forbiddenAccess();
            }

            // Thrown when the request fails Laravel FormValidator validation.
            if ($exception instanceof ValidationException) {
                return $this->formProcessingFailure($exception->errors(),'Inappropriate input');
            }

            // Thrown when HTTP Method is incorrect when requesting routing
            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->wrongRequestType($exception->getMessage());
            }

            // Thrown when the HTTP requested route can not be found
            if ($exception instanceof NotFoundHttpException) {
                return $this->notFound();
            }

            // Thrown when processing HTTP requests is unsuccessful
            if ($exception instanceof HttpException) {
                return $this->unavailableService();
            }

            // Thrown when an exception occurs.
            if ($exception instanceof Exception) {
                return $this->internalServerError($exception->getMessage());
            }
        }

        return parent::render($request, $exception);
    }
}
