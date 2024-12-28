<?php

namespace App\Traits;

trait ApiResponderTrait
{
    // HTTP status codes
    static $HTTP_OK = 200;
    static $HTTP_CREATED = 201;
    static $HTTP_NO_CONTENT = 204;
    static $HTTP_BAD_REQUEST = 400;
    static $HTTP_UNAUTHORIZED = 401;
    static $HTTP_FORBIDDEN = 403;
    static $HTTP_NOT_FOUND = 404;
    static $HTTP_METHOD_NOT_ALLOWED = 405;
    static $HTTP_CONFLICT= 409;
    static $HTTP_UNPROCESSABLE_ENTITY = 422;
    static $HTTP_INTERNAL_SERVER_ERROR = 500;
    static $HTTP_SERVICE_UNAVAILABLE = 503;


    // This format is used when an entity is created.
    public function entityCreated($data, $message='Success')
    {
        $info = [
            "data" => $data, // retrieved data
            "status" => 'success',
            "status_code" => self::$HTTP_CREATED,
            "code" => 'Z9E3K2', // agreed code by the business that describes an information
            "message" => $message  // optional though
        ];
        return response()->json($info, self::$HTTP_CREATED);
    }

    // This format is used when a single object is being returned as response.
    public function actionSuccess($message='Success')
    {
        $info = [
            "status" => 'success',
            "status_code" => self::$HTTP_OK,
            "code" => 'R6Y1H8', // agreed code by the business that describes an information
            "message" => $message  // optional though
        ];
        return response()->json($info, self::$HTTP_OK);
    }

    // This format is used when a list of objects are returned.
    public function success($data, $message='Success')
    {
        $info = [
            "data" => $data, // retrieved data
            "status" => 'success',
            "status_code" => self::$HTTP_OK,
            "code" => 'P2A4N9', // agreed code by the business that describes an information
            "message" => $message  // optional though
        ];
        return response()->json($info, self::$HTTP_OK);
    }

    // This format is used when a empty list of objects or array are returned.
    public function noContent($message='No content found')
    {
        $info = [
            "status" => 'success',
            "status_code" => self::$HTTP_NO_CONTENT,
            "code" => 'Q7L6T5', // agreed code by the business that describes an information
            "message" => $message  // optional though
        ];
        return response()->json($info, self::$HTTP_NO_CONTENT);
    }

    // This format is used as response for failed form processing.
    public function formProcessingFailure($data, $message='Failure')
    {
        $info = [
            "errors" => $data, // array of errors [bvn => 'Bvn Length should be more than 9', 'Bvn does not exist']
            "status" => 'failure',
            "status_code" => self::$HTTP_UNPROCESSABLE_ENTITY,
            "code" => 'X3C9J4', // agreed code by the business that describes an information
            "message" => $message  // optional though
        ];
        return response()->json($info, self::$HTTP_UNPROCESSABLE_ENTITY);
    }

    // This format is used as response for non authentication.
    public function authenticationFailure($message='You need to be authenticated to access this feature')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_UNAUTHORIZED,
            "code" => 'V8B2M6', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_UNAUTHORIZED);
    }

    // This format is used as response for forbidden Access.
    public function forbiddenAccess($message='You do not have authority to carry out this action')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_FORBIDDEN,
            "code" => 'F5G1D9', // agreed code by the business that describes an information
            "message" => (is_null($message)?:$message) // optional though
        ];
        return response()->json($info, self::$HTTP_FORBIDDEN);
    }

    // This format is used as response for internal server error.
    public function internalServerError($message='Server error')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_INTERNAL_SERVER_ERROR,
            "code" => 'W4R7S2', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_INTERNAL_SERVER_ERROR);
    }

    // This format is used as response for unavailable service.
    public function unavailableService($message='Service unavailable')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_SERVICE_UNAVAILABLE,
            "code" => 'K1N8P4', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_SERVICE_UNAVAILABLE);
    }

    // This format is used as response for not found.
    public function notFound($message='We cant find what you are looking for :(')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_NOT_FOUND,
            "code" => 'H6T9L2', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_NOT_FOUND);
    }

    // This format is used as response for request using wrong request type.
    public function wrongRequestType($message='Method not allowed')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_METHOD_NOT_ALLOWED,
            "code" => 'D7S4R6', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_METHOD_NOT_ALLOWED);
    }

    // This format is used for some endpoints that fails a login that is usually not form validation.
    public function requestConflict($message='Could not carry out operation')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_CONFLICT,
            "code" => 'L3M9H7', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_CONFLICT);
    }

    // This format is used for other general responses.
    public function badRequest($message='Could not carry out operation')
    {
        $info = [
            "status" => 'failure',
            "status_code" => self::$HTTP_BAD_REQUEST,
            "code" => 'N2C8V6', // agreed code by the business that describes an information
            "message" => $message // optional though
        ];
        return response()->json($info, self::$HTTP_BAD_REQUEST);
    }

}
