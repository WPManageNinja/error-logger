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
                    ->orderBy('id', 'DESC')
                    ->paginate($perPage);

        $this->sendSuccess([
            'logs' => $logs,
            'error_levels' => GeneralSettings::$error_levels,
        ]);
    }

    public function deleteLogs()
    {
        // Delete Selected Logs here
    }

    public function saveNotificationSettings()
    {
        // Save Your Notification Settings here
        $email = sanitize_email($_POST['email']);
        $notification_type_settings = $_POST['notification_type_settings'];
        $database_logs_settings     = $_POST['database_logs_settings'];

        update_option('nel_email_settings',$email);  
        update_option('ninja_notification_type_settings',$notification_type_settings);      
        update_option('ninja_database_logs_settings',$database_logs_settings);      

        $this->sendSuccess([
            'success'                => 'successfully save data',
            'email'                  => $email,  
            'notification_types_settings'=> $notification_type_settings,
            'database_logs_settings' => $database_logs_settings,
        ]);

    }

    public function getSearchData(){

        //get search data here
        $searchInput = $_POST['search'];
        $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
                    ->where('log_data', 'like', '%' . $searchInput . '%')
                    ->orWhere('log_type', 'like', '%' . $searchInput . '%')
                    ->orWhere('request_method', 'like', '%' . $searchInput . '%')
                    ->orderBy('id', 'DESC')
                    ->get();

        $this->sendSuccess([
            'success'       => 'data found successfully',
            'searchInput'   => $searchInput,
            'logs'          => $logs
        ]);
    }

    public function getNotificationSettings()
    {
        $data = [
            'error_levels' => GeneralSettings::$error_levels,
            'email_log_types' => GeneralSettings::getEmailBroadCastErrorTypes(),
            'db_log_types' => GeneralSettings::getDbStropeErrorTypes(),
            'email_settings' => GeneralSettings::getEmailSettings(),
            'email' => $email,
            'notification_settings' => get_option('ninja_notification_type_settings'),
            'database_settings'     => get_option('ninja_database_logs_settings')
        ];

        $this->sendSuccess($data);
    }

    private function sendSuccess($data)
    {
        wp_send_json_success($data, 200);
    }
}