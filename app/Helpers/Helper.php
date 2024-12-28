<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Reusable functionalities
 */
class Helper
{
    /**
     * Calculate resource rating
     * 
     * @param integer $total_voters
     * @param integer $total_votes
     * @param integer $max_stars
     * @return integer
     */
    public static function averageRating(int $total_voters, int $total_votes, ?int $max_stars = 5)
    {
        $capacity = $total_voters * $max_stars;
        $percentage = $total_votes / $capacity * 100;
        $rating = $percentage * $max_stars / 100;

        return ceil($rating);
    }

    /**
     * Escape special characters for a LIKE query.
     *
     * @param string $value
     * @param string $char
     * @return string
     */
    public static function escapeForLikeColumnQuery(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char.$char, $char.'%', $char.'_'],
            $value
        );
    }

    /**
     * Try to convert a string to carbon date time
     * 
     * @param string $string
     * @return object|boolean
     */
    public static function stringToCarbonDate(string $string)
    {
        // Parse string to date
        try {
            $date = new Carbon($string);
        } catch (\Throwable $th) {
            $date = false;
        }

        return $date;
    }

    /**
     * Get the current client ip address
     * 
     * @param void
     * @return string $ip
     */
    public static function getClientIpAddress(){
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
            if (array_key_exists($key, $_SERVER) === true){
                foreach (explode(',', $_SERVER[$key]) as $ip){
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                        return $ip;
                    }

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) !== false){
                       $reserved_range[] = $ip;
                    }

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) !== false){
                        $private_range[] = $ip;
                    }
                }
            }
        }

        return empty($private_range)? (empty($reserved_range[0])? '0.0.0.0' : $reserved_range[0]) : $private_range[0];
    }

    /**
     * Format a boolean value to the required string of '1' or '0'
     * 
     * @param mixed $value
     * @return string
     */
    public static function formatForBooleanColumnQuery(string $value)
    {
        $value = strtolower($value);
        if ($value === 'false' || $value === '0' || $value === '') {
           return '0';
        }

        if ($value === 'true' || $value === '1') {
            return '1';
        }

        return '0';
    }

    /**
     * Format an integer to the required string for where like query
     * 
     * @param integer $value
     * @return string
     */
    public static function formatForNumericColumnQuery(string $value)
    {
        // This will replace all characters of string except the first with _ 
        // eg: 5600 will result in 5___
        return $value[0] . str_repeat('_', strlen($value) - 1);
    }
}