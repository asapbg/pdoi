<?php

use App\Enums\TimePeriodEnum;
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
        return date('d.m.Y', strtotime($datetime));
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
        return date('d.m.Y H:i', strtotime($datetime));
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

if (!function_exists('groupPermissions')) {

    /**
     * Group all permissions
     *
     * @method groupPermissions
     *
     * @param $permissions
     * @param bool $onlyGroups returns only main permissions as groups
     * @return array
     */
    function groupPermissions($permissions, bool $onlyGroups = false): array
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

if (!function_exists('optionsUserStatuses')) {

    /**
     * Get all users statuses and return options
     *
     * @method optionsUserStatuses
     *
     * @param bool $any
     * @param string|int $anyValue
     * @param string|int $anyName
     * @return array
     */
    function optionsUserStatuses(bool $any = false, string|int $anyValue = '', string|int $anyName=''): array
    {
        $options = User::getUserStatuses();
        if( $any ) {
            $options[$anyValue] = $anyName;
            ksort($options);
        }
        return $options;
    }
}

if (!function_exists('optionsUserTypes')) {

    /**
     * Get all users types and return options
     *
     * @method optionsUserTypes
     *
     * @param bool $any
     * @param string|int $anyValue
     * @param string|int $anyName
     * @return array
     */
    function optionsUserTypes(bool $any = false, string|int $anyValue = '', string|int $anyName=''): array
    {
        $options = User::getUserTypes();
        if( $any ) {
            $options[$anyValue] = $anyName;
            ksort($options);
        }
        return $options;
    }
}

if (!function_exists('optionsLanguages')) {

    /**
     * Get all available languages and return options
     *
     * @method optionsLanguages
     *
     * @param bool $any
     * @param string|int $anyValue
     * @param string|int $anyName
     * @return array
     */
    function optionsLanguages(bool $any = false, string|int $anyValue = '', string|int $anyName=''): array
    {
        $options = [];
        $availableLanguages = config('available_languages');
        if( sizeof($availableLanguages) ) {
            foreach ($availableLanguages as $id => $lang) {
                $options[$id] = $lang['name'];
            }
        }

        if( $any ) {
            $options[$anyValue] = $anyName;
            ksort($options);
        }
        return $options;
    }
}

if (!function_exists('optionsStatuses')) {

    /**
     * return regular status options
     *
     * @method optionsStatuses
     *
     * @param bool $any
     * @param string|int $anyValue
     * @param string|int $anyName
     * @return array
     */
    function optionsStatuses(bool $any = false, string|int $anyValue = '', string|int $anyName=''): array
    {
        $options = array(
            1 => trans_choice('custom.active', 1),
            0 => trans_choice('custom.inactive', 1),
        );
        if( $any ) {
            $options[$anyValue] = $anyName;
            ksort($options);
        }
        return $options;
    }
}

if (!function_exists('getLocaleId')) {

    /**
     * returns id of the current locale based on configuration
     *
     * @method getLocaleId
     * @param string $code
     * @return int
     */
    function getLocaleId(string $code): int
    {
        $id = 1; //by default get first language
        foreach (config('available_languages') as $key => $lang) {
            if( $code == $lang['code'] ) {
                $id = $key;
            }
        }
        return $id;
    }
}

if (!function_exists('logError')) {

    /**
     * Write to error log file
     *
     * @method logError
     * @param string $method
     * @param string $error
     */
    function logError(string $method, string $error): void
    {
        \Illuminate\Support\Facades\Log::error(\Illuminate\Support\Carbon::now().' '.$method.': '.$error );
    }
}

if (!function_exists('optionsTimePeriod')) {

    /**
     * return time interval options
     *
     * @method optionsTimePeriod
     *
     * @param bool $any
     * @param string|int $anyValue
     * @param string|int $anyName
     * @return array
     */
    function optionsTimePeriod(bool $any = false, string|int $anyValue = '', string|int $anyName=''): array
    {
        $options = [];
        if( $any ) {
            $options[] = ['value' => $anyValue, 'name' => $anyName];
        }
        foreach (TimePeriodEnum::options() as $option) {
            $options[] = ['value' => $option, 'name' => __('custom.period.'.$option)];
        }
        return $options;
    }
}

if (!function_exists('optionsApplicationStatus')) {

    /**
     * return application status options
     *
     * @method optionsApplicationStatus
     *
     * @param bool $any
     * @param string|int $anyValue
     * @param string|int $anyName
     * @return array
     */
    function optionsApplicationStatus(bool $any = false, string|int $anyValue = '', string|int $anyName=''): array
    {
        $options = [];
        if( $any ) {
            $options[] = ['value' => $anyValue, 'name' => $anyName];
        }
        foreach (\App\Enums\PdoiApplicationStatusesEnum::options() as $key => $value) {
            $options[] = ['value' => $value, 'name' => __('custom.application.status.'.$key)];
        }
        return $options;
    }

    if (!function_exists('optionsFromModel')) {

        /**
         * return prepared options for search form from standard model option
         *
         * @method optionsFromModel
         *
         * @param $dbOptions
         * @param bool $any
         * @param string|int $anyValue
         * @param string|int $anyName
         * @return array
         */
        function optionsFromModel($dbOptions, bool $any = false, string|int $anyValue = '', string|int $anyName = ''): array
        {
            $options = [];
            if ($any) {
                $options[] = ['value' => $anyValue, 'name' => $anyName];
            }
            foreach ($dbOptions as $option) {
                $options[] = ['value' => $option->id, 'name' => $option->name];
            }
            return $options;
        }
    }
}
