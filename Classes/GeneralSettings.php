<?php
namespace NinjaErrorLogger\Classes;

class GeneralSettings
{
    public static $error_levels = array(
        'E_ERROR' => E_ERROR,
        'E_WARNING' => E_WARNING,
        'E_PARSE' => E_PARSE,
        'E_NOTICE' => E_NOTICE,
        'E_CORE_ERROR' => E_CORE_ERROR,
        'E_CORE_WARNING' => E_CORE_WARNING,
        'E_COMPILE_ERROR' => E_COMPILE_ERROR,
        'E_COMPILE_WARNING' => E_COMPILE_WARNING,
        'E_USER_ERROR' => E_USER_ERROR,
        'E_USER_WARNING' => E_USER_WARNING,
        'E_USER_NOTICE' => E_USER_NOTICE,
        'E_STRICT' => E_STRICT,
        'E_RECOVERABLE_ERROR' => E_RECOVERABLE_ERROR,
        'E_DEPRECATED' => E_DEPRECATED,
        'E_USER_DEPRECATED' => E_USER_DEPRECATED
    );

    public static function getLogErrorTypes()
    {
        return self::$error_levels;
    }

    public static function getErrorName($code)
    {
        $errors = self::getLogErrorTypes();
        if(isset($errors[$code])) {
            return $errors[$code];
        }
        return $code;
    }

    public static function getEmailBroadCastErrorTypes()
    {
        $logTypes = get_option('nel_eror_email_log_types');

        if(!is_array($logTypes)) {
            return [
                'E_ERROR'
            ];
        }

        if(!$logTypes) {
            return [];
        }
        return $logTypes;
    }

    public static function getDbStropeErrorTypes()
    {
        $logTypes = get_option('nel_eror_db_log_types');

        if(!is_array($logTypes)) {
           return self::getLogErrorTypes();
        }

        if(!$logTypes) {
            return [];
        }
        return $logTypes;
    }

    public static function getEmailSettings()
    {
        $defaults = [
            'email_to' => get_option('admin_email')
        ];
        $emailSettings = get_option('nel_email_settings');
        if(!$emailSettings) {
            $emailSettings = [];
        }else{
            $emailSettings = [
                'db_email_to' => $emailSettings
            ];
        }

        return wp_parse_args($emailSettings, $defaults);
    }

}