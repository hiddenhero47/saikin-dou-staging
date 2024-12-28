<?php

namespace App\Helpers;

use App\Helpers\DiscordMessenger;

/**
 * Use discord applications 
 */
class DiscordSuite
{
    /**
     * Use discord communication channels
     * @param void
     * @return DiscordMessenger
     */
    public static function messenger()
    {
        return new DiscordMessenger();
    }
}
