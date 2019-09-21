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

        $perPage = 10;

        $filter_data = $_POST['select_filter'];
        $error_levels = GeneralSettings::$error_levels;
        $searchInput = $_POST['search'];
        $total = count(ninja_error_logger_app()->db()->table('nel_error_logs')->get());

        if( $filter_data && $searchInput ){

        global $wpdb;
        $nel_error_logs = $wpdb->prefix . 'nel_error_logs';

        $logs = $wpdb->get_results(
            "SELECT * FROM $nel_error_logs 
            where (log_data LIKE '%$searchInput%' OR request_method LIKE '%$searchInput%' ) 
            AND log_type=$error_levels[$filter_data]"); 

        // $logs = ninja_error_logger_app()->db()->table('nel_error_logs')                  
        //         ->where('log_data', 'like', '%' . $searchInput . '%') 
        //         ->andWhere('log_type', '=',  $error_levels[$filter_data] ) 
        //         ->orWhere('request_method', 'like', '%' . $searchInput . '%')    
        //         ->orderBy('id', 'DESC')   
        //         ->paginate($perPage);  
                
                //->andWhere('log_type', '=',  $error_levels[$filter_data] ) 

                //$logs = $logs->whereIn('log_type', '=',  $error_levels[$filter_data]);

        $this->sendSuccess([
            'logs' => $logs,
            'selectinput' => $filter_data,
            'searchdata'  => $searchInput,
            'error_levels' => $error_levels[$filter_data],
            'errors' => $error_levels,
            'success' => 'two data find successfully'
        ]);

        }
        else if( $filter_data ){

            $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
            ->where('log_type', '=',  $error_levels[$filter_data] )
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
            
            $this->sendSuccess([
                'logs' => $logs['data'],
                'searchdata' => $filter_data,
                'error_levels' => $error_levels[$filter_data],
                'errors' => $error_levels,
                'success' => 'filter data find successfully'
            ]);

        }else if($searchInput){

            $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
            ->where('log_type', 'like', '%' . $searchInput . '%' )
            ->orWhere('log_data', 'like', '%' . $searchInput . '%')
            ->orWhere('request_method', 'like', '%' . $searchInput . '%')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);

            $this->sendSuccess([
                'logs' => $logs['data'],
                'errors' => $error_levels,
                'success' => 'search data find successfully'
            ]);
        
        }else{

            $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
            //->orderBy('id', 'DESC')
            ->paginate($perPage);

            

            $this->sendSuccess([
                'logs' => $logs['data'],
                'error_levels' => $error_levels,
                'total' => $total,
                'per_page' => $perPage,
            ]);
        }

       
    }

    public function getLogsPagination(){

        $value = $_POST['value'];
        $perPage = 10;
        $total = count(ninja_error_logger_app()->db()->table('nel_error_logs')->get());

        $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
            ->where('id','>',($value-1)*$perPage)
            //->orderBy('id', 'DESC')
            ->paginate($perPage);
        
        $this->sendSuccess([
            'logs' => $logs['data'],
            'error_levels' => $value,
            'total' => $total,
            'per_page' => $perPage
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