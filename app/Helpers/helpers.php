<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (!function_exists('currentUser')) {

    /**
     * If more then one guard is used in the app
     * return the correct user's instance
     */
    function currentUser()
    {
        $guard = currentGuard();

        if ($guard == config('auth')['defaults']['guard']) {
            if (Auth::check() && Auth::user() && Auth::user() instanceof User) {
                return Auth::user();
            }
        }
        else {
            $model = "\App\Models\\".capitalize($guard);
            if (!class_exists($model)) {
                $model = "\App\\".capitalize($guard);
                if (!class_exists($model)) {
                    return null;
                }
            }
            if (Auth::guard($guard)->check() && Auth::guard($guard)->user() instanceof $model) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }
}

if (!function_exists('currentGuard')) {

    /**
     * If more then one guard is used in the app return the current guard
     *
     * @return string
     */
    function currentGuard()
    {
        if (Auth::guard() instanceof \Illuminate\Auth\SessionGuard) {
            return explode("_", Auth::guard()->getName())[1];
        }

        return config('auth')['defaults']['guard'];
    }
}

if (!function_exists('databaseDate')) {

    /**
     * Return date in Y-m-d format for storing in database
     *
     * @param $date
     * @return false|string
     */
    function databaseDate($date)
    {
        return date("Y-m-d", strtotime($date));
    }
}

if (!function_exists('databaseDateTime')) {

    /**
     * Return datetime in Y-m-d H:i:s format for storing in database
     *
     * @param $date
     * @return false|string
     */
    function databaseDateTime($datetime)
    {
        if (!$datetime) {
            return null;
        }
        return date("Y-m-d H:i:s", strtotime($datetime));
    }
}

if (!function_exists('displayDate')) {

    /**
     * Return date from datetime string in d-m-Y format
     *
     * @param $datetime
     * @return false|string
     */
    function displayDate($datetime)
    {
        if (!$datetime) {
            return "";
        }
        return date('d-m-Y', strtotime($datetime));
    }
}

if (!function_exists('displayDateTime')) {

    /**
     * Return date from datetime string in d-m-Y H:i format
     *
     * @param $datetime
     * @return false|string
     */
    function displayDateTime($datetime)
    {
        if (!$datetime) {
            return "";
        }
        return date('d-m-Y H:i', strtotime($datetime));
    }
}

if (!function_exists('printDate')) {

    /**
     * Return date from datetime string in d-MMM-Y format in bg
     *
     * @param $datetime
     * @return false|string
     */
    function printDate()
    {
        $format = new IntlDateFormatter('bg_BG', IntlDateFormatter::NONE,
            IntlDateFormatter::NONE, NULL, NULL, 'd-MMM-Y');
        return datefmt_format($format, mktime('0','0','0'));
    }
}

if (!function_exists('capitalize')) {

    /**
     * Capitalize a given string
     *
     * @param $string
     * @return string
     */
    function capitalize($string)
    {
        $firstChar = mb_substr($string, 0, 1, "UTF-8");
        $then = mb_substr($string, 1, null, "UTF-8");

        return mb_strtoupper($firstChar, "UTF-8") . $then;
    }
}

if (!function_exists('u_trans')) {

    /**
     * Translates the string and converts its first letter to capital
     *
     * @method u_trans
     * @param string  $value string to translate
     * @param integer $count singular or plural
     *
     * @return string
     */
    function u_trans($value, $count = 1)
    {
        return capitalize(trans_choice($value, $count));
    }
}

if (!function_exists('l_trans')) {

    /**
     * Translates the string and converts its first letter to lower
     *
     * @method l_trans
     * @param string  $value string to translate
     * @param integer $count singular or plural
     *
     * @return string
     */
    function l_trans($value, $count = 1)
    {
        return mb_strtolower(trans_choice($value, $count));
    }
}

if (!function_exists('group_permissions')) {

    /**
     * Group all permissions
     *
     * @method group_permissions
     *
     * @param $permissions
     * @param bool $onlyGroups returns only main permissions as groups
     * @return array
     */
    function group_permissions($permissions, bool $onlyGroups = false): array
    {
        $permsGrouped = array();
        if( $permissions->count() ) {
            foreach ($permissions as $group) {
                if( str_contains($group->name, '.*') ) {
                    $groupName = str_replace('*', '', $group->name);
                    if( $onlyGroups ) {
                        if( $group->name != \App\Models\CustomRole::FULL_ACCESS_RULE ) {
                            $permsGrouped[] = $groupName;
                        }
                    } else {
                        $permsGrouped[$groupName] = array();

                        foreach ($permissions as $p) {
                            if( str_contains($p->name, $groupName) ) {
                                $p->is_main = (int)($p->name === $group->name);
                                $permsGrouped[$groupName][] = $p;
                            }
                        }
                    }
                }
            }
        }
        return $permsGrouped;
    }
}
