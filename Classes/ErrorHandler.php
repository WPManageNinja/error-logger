<?php

namespace NinjaErrorLogger\Classes;

class ErrorHandler
{
    public function register()
    {
        register_shutdown_function( array($this, 'checkForError') );
    }

    public function checkForError()
    {
        $error = error_get_last();
        if (is_null($error)) {
            return;
        }
        $ignore = apply_filters('ninja_error_log_will_by_pass', false, $error);

        if ($ignore) {
         //   return;
        }

        $errorType = $error['type'];

        $emailLogTypes = GeneralSettings::getEmailBroadCastErrorTypes();
        $dbLogErrorTypes = GeneralSettings::getDbStropeErrorTypes();

        $willSendEmail = in_array($errorType, $emailLogTypes);
        $willLogInDB = in_array($errorType, $dbLogErrorTypes);

        $willLogInDB = true; // For dev purpose now

        if(!$willSendEmail && !$willLogInDB) {
            return;
        }
        $logData = [
            'log_type'       => GeneralSettings::getErrorName($error['type']),
            'log_data'       => $error['message'],
            'log_url'        => $_SERVER['REQUEST_URI'],
            'referrer'       => $_SERVER['HTTP_REFERER'],
            'log_file'       => $error['file'],
            'log_line'       => intval($error['line']),
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'log_hash'       => md5($error['message']),
            'user_id'        => get_current_user_id(),
            'created_at'     => gmdate('Y-m-d H:i:s'),
            'updated_at'     => gmdate('Y-m-d H:i:s')
        ];

        $logId = false;
        if($willLogInDB) {
            $logId = $this->logData($logData, HOUR_IN_SECONDS);
        }

        if ($willSendEmail) {
            $this->broadCastEmail($logData);
        }

        return $logId;
    }

    public function logData($data, $duplicateTime = HOUR_IN_SECONDS)
    {
        global $wpdb;
        if ($duplicateTime) {
            $timestamp = time() + date("Z") - $duplicateTime;
            $datTime = gmdate('Y-m-d H:i:s', $timestamp);
            $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}nel_error_logs WHERE log_hash = %s AND created_at >= %s LIMIT 1", $data['log_hash'], $datTime);
            $exist = $wpdb->get_row($sql);
            if ($exist) {
                // Data already exists
                return false;
            }
        }
        $wpdb->insert($wpdb->prefix . 'nel_error_logs', $data);
        return $wpdb->insert_id;
    }

    private function broadCastEmail($logData)
    {
        $emailTo = get_option('admin_email');
        $emailSubject = 'Error notification for ' . get_home_url().' ('.$logData['log_type'].')';

        $output = '<h2>Error notification</h2>For site <a href="' . get_home_url() . '" target="_blank">' . get_home_url() . '</a><br />';
        $output .= '<ul>';
        $output .= '<li><strong>Error Level:</strong> ' . $logData['log_type'] . '</li>';
        $output .= '<li><strong>Message:</strong> ' . nl2br($logData['log_data']) . '</li>';
        $output .= '<li><strong>File:</strong> ' . $logData['log_file'] . '</li>';
        $output .= '<li><strong>Line:</strong> ' . $logData['log_line'] . '</li>';
        $output .= '<li><strong>Request:</strong> ' . $logData['log_url'] . '</li>';
        $output .= '<li><strong>Referrer:</strong> ' . $logData['referrer'] . '</li>';

        $user_id = get_current_user_id();

        if (!empty($user_id)) {
            $output .= '<li><strong>User ID</strong>: ' . $user_id . '</li>';
        }

        $output .= '</ul><br />';

        if (function_exists('wp_mail')) {
            $headers = [
                'Content-Type: text/html; charset=UTF-8'
            ];
            return wp_mail($emailTo, $emailSubject, $output, $headers);
        } else {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            return mail($emailTo, $emailSubject, $output, $headers);
        }
    }
}
