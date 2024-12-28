<?php

namespace App\Helpers;

use App\Helpers\PayStackWebHook;
use App\Helpers\PayStackClient;

/**
 * Use pay stack applications 
 */
class PayStackSuite
{
    /**
     * Handle pay stack webhook response
     * @param void
     * @return PayStackWebHook
     */
    public static function webhook()
    {
        return new PayStackWebHook();
    }

    /**
     * Handle pay stack application programming interface.
     * @param void
     * @return PayStackClient
     */
    public static function client()
    {
        return new PayStackClient();
    }
}