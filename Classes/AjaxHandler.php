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

        $searchInput  = isset($_REQUEST['search'])?$_REQUEST['search']:''; 
        $searchInput  = sanitize_text_field($searchInput);

        $filter_data  = isset($_REQUEST['select_filter'])?$_REQUEST['select_filter']:''; 
        $filter_data  = sanitize_text_field($filter_data);

        $value        = isset($_REQUEST['value'])?absint($_REQUEST['value']):0;
        $perPage      = isset($_REQUEST['per_page_total'])?absint($_REQUEST['per_page_total']):5;

        $error_levels = GeneralSettings::$error_levels;
        
        global $wpdb;
        $nel_error_logs = $wpdb->prefix . 'nel_error_logs';

        if( $value == 0 ){
            $OFFSET = 0;
        }else{
            $OFFSET = ($value-1)*$perPage;
        }

        if( $filter_data && $searchInput ){

        $total = $wpdb->get_results(
            "SELECT * FROM $nel_error_logs 
            where (log_data LIKE '%$searchInput%' OR request_method LIKE '%$searchInput%' ) 
            AND log_type=$error_levels[$filter_data]"); 

        $total = count($total);

        $logs = $wpdb->get_results(
            "SELECT * FROM $nel_error_logs 
            where (log_data LIKE '%$searchInput%' OR request_method LIKE '%$searchInput%' ) 
            AND log_type=$error_levels[$filter_data] ORDER BY id DESC LIMIT $perPage OFFSET $OFFSET"); 

        $this->sendSuccess([
            'logs' => $logs,
            'selectinput' => $filter_data,
            'searchdata'  => $searchInput,
            'error_levels' => $error_levels,
            'errors' => $error_levels,
            'success' => 'two data find successfully',
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $value
        ]);

        }
        else if( $filter_data ){

            // $logs = ninja_error_logger_app()->db()->table('nel_error_logs')
            // ->where('log_type', '=',  $error_levels[$filter_data])
            // ->orderBy('id', 'DESC')
            // ->paginate($perPage);

            $total = ninja_error_logger_app()->db()->table('nel_error_logs')
            ->where('log_type', '=',  $error_levels[$filter_data])
            ->orderBy('id', 'DESC')
            ->get();
            
            
            
            $logs = $wpdb->get_results("SELECT * FROM $nel_error_logs where  log_type=$error_levels[$filter_data] ORDER BY id DESC LIMIT $perPage OFFSET $OFFSET");

            $total = count($total);
            
            $this->sendSuccess([
                'logs' => $logs,
                'searchdata' => $filter_data,
                'error_levels' => $error_levels,
                'errors' => $error_levels,
                'success' => 'filter data find successfully',
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $value
            ]);

        }else if($searchInput){

            $total = ninja_error_logger_app()->db()->table('nel_error_logs')
            ->where('log_type', 'like', '%' . $searchInput . '%' )
            ->orWhere('log_data', 'like', '%' . $searchInput . '%')
            ->orWhere('request_method', 'like', '%' . $searchInput . '%')
            ->get();

            $logs = $wpdb->get_results("SELECT * FROM $nel_error_logs where (log_type LIKE '%$searchInput%' OR log_data LIKE '%$searchInput%' OR request_method LIKE '%$searchInput%') ORDER BY id DESC LIMIT $perPage OFFSET $OFFSET");

            $total = count($total);

            $this->sendSuccess([
                'logs' => $logs,
                'errors' => $error_levels,
                'success' => 'search data find successfully',
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $value
            ]);
        
        }else{

            $logs = $wpdb->get_results("SELECT * FROM $nel_error_logs ORDER BY id DESC LIMIT $perPage OFFSET $OFFSET");

            $total = count(ninja_error_logger_app()->db()->table('nel_error_logs')->get());

            $this->sendSuccess([
                'logs' => $logs,
                'error_levels' => $error_levels,
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $value
            ]);
        }

       
    }

    public function deleteLogs()
    {
        // Delete Selected Logs here
        $rowId = isset($_POST['row_id'])?$_POST['row_id']:null;

        ninja_error_logger_app()->db()->table('nel_error_logs')->where('id',$rowId)->delete();

        $this->sendSuccess([
            'row_id' => $rowId,
            'success' => 'deleted row',
        ]);
    }

    public function deleteBulkLogs()
    {
        // Delete Selected Logs here
        $rowIds = isset($_POST['row_ids'])?$_POST['row_ids']:null;

        foreach($rowIds as $row){
            ninja_error_logger_app()->db()->table('nel_error_logs')->where('id',$row['id'])->delete();
        }

        $this->sendSuccess([
            'row_id' => $rowIds,
            'success' => 'deleted selected row',
        ]);
    }

    public function saveNotificationSettings()
    {
        // Save Your Notification Settings here
        $email                      = isset($_POST['email'])?$_POST['email']:null;
        $notification_type_settings = isset($_POST['notification_type_settings'])?$_POST['notification_type_settings']:null;
        $database_logs_settings     = isset($_POST['database_logs_settings'])?$_POST['database_logs_settings']:null;

        $email = sanitize_email($email);

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