<?php

namespace NinjaErrorLogger\Classes;

class AdminHandler
{
    public function register()
    {
        add_action('admin_menu', array($this, 'addLogMenu'));
        add_action('wp_ajax_ninja_error_log_ajax', array($this, 'ajaxHandler'));
    }

    public function ajaxHandler()
    {
        $validRoutes = [
            'get_logs'                   => 'getLogs',
            'delete_logs'                => 'deleteLogs',
            'save_notification_settings' => 'saveNotificationSettings',
            'get_notification_settings'  => 'getNotificationSettings',
            'get_search_data'            => 'getSearchData',
            'get_logs_pagination'        => 'getLogsPagination',
        ];

        if (current_user_can($this->getAccessRole())) {
            $route = sanitize_text_field($_REQUEST['route']);
            if (isset($validRoutes[$route])) {
                $ajaxHandler = new AjaxHandler();
                return $ajaxHandler->{$validRoutes[$route]}();
            }
        }
    }

    public function addLogMenu()
    {
        add_submenu_page(
            'tools.php',
            __('Ninja Error Logs', 'error_logger'),
            __('Error Logs', 'error_logger'),
            $this->getAccessRole(),
            'ninja_error_logs',
            array($this, 'renderLogs')
        );
    }

    public function renderLogs()
    {
        $this->addAssets();
        echo '<div class="wrap"><div id="ninja_error_log_app"><h1>Loading.... Please Wait</h1></div></div>';
    }

    private function addAssets()
    {
        wp_enqueue_script('ninja_error_logger', NINJA_ERROR_LOGGER_DIR_URL . 'assets/js/app.js', array('jquery'), NINJA_ERROR_LOGGER_VERSION);
        wp_enqueue_style('ninja_error_logger', NINJA_ERROR_LOGGER_DIR_URL . 'assets/css/app.css', array(), NINJA_ERROR_LOGGER_VERSION);

        wp_localize_script('ninja_error_logger', 'ninjaErrorAdminVars', [
            'ajax_url'         => admin_url('admin-ajax.php'),
            'ajax_action'      => 'ninja_error_log_ajax',
            'is_initial_setup' => !!get_option('nel_eror_email_log_types')
        ]);

    }

    public function getAccessRole()
    {
        return apply_filters('ninja_error_log_admin_access_role', 'manage_options');
    }
}
