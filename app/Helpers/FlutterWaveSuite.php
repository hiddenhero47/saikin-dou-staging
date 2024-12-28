<?php

namespace App\Helpers;

use App\Helpers\FlutterWaveWebHook;
use App\Helpers\FlutterWaveClient;

/**
 * Use flutter wave applications 
 */
class FlutterWaveSuite
{
    /**
     * Handle flutter wave webhook response
     * @param void
     * @return FlutterWaveWebHook
     */
    public static function webhook()
    {
        return new FlutterWaveWebHook();
    }

    /**
     * Handle flutter wave application programming interface.
     * @param void
     * @return FlutterWaveClient
     */
    public static function client()
    {
        return new FlutterWaveClient();
    }
}