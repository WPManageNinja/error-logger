<?php

namespace NinjaErrorLogger\Classes;

class AjaxHandler
{
    public function getLogs()
    {
        // Response Your logs as Pagination
        // You may implement advanced filter and search
        // You will get the db as ninja_error_logger_app()->db()
        // the db() function is almost same as laravel query builder

        $perPage = 20;
        $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
                    ->paginate($perPage);

        $this->sendSuccess([
            'logs' => $logs
        ]);
    }

    public function deleteLogs()
    {
        // Delete Selected Logs here
    }

    public function saveNotificationSettings()
    {
        // Save Your Notification Settings here
    }

    public function getNotificationSettings()
    {
        $data = [
            'error_levels' => GeneralSettings::$error_levels,
            'email_log_types' => GeneralSettings::getEmailBroadCastErrorTypes(),
            'db_log_types' => GeneralSettings::getDbStropeErrorTypes(),
            'email_settings' => GeneralSettings::getEmailSettings()
        ];

        $this->sendSuccess($data);
    }

    private function sendSuccess($data)
    {
        wp_send_json_success($data, 200);
    }
}