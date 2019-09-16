<?php

/*
Plugin Name: Error Logger
Description: Log and Review your WordPress Errors.
Plugin URI: https://wpmanageninja.com/
Version: 1.0
Author: WPManageNinja LLC
Author URI: https://wpmanageninja.com/
Text Domain: error_logger
*/

/**
 * @copyright Copyright (c) 2017. All rights reserved.
 *
 * @license   Released under the GPL license http://www.opensource.org/licenses/gpl-license.php
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

define( 'NINJA_ERROR_LOGGER_VERSION', '1.0' );

if( ! class_exists( 'NinjaErrorLogger' ) ) {
    class NinjaErrorLogger
    {

        /**
         * @var NinjaErrorLogger The only instance
         * @since 1.0
         */
        private static $instance;

        public static function instance() {

            if ( ! isset( self::$instance ) && ! ( self::$instance instanceof NinjaErrorLogger ) ) {
                self::$instance = new NinjaErrorLogger;
                self::$instance->loadDependecies();
                self::$instance->boot();
            }

            return self::$instance;
        }

        public function boot()
        {
            if(is_admin()) {
                $this->loadAdminHooks();
            }
            $this->loadPublicHooks();
        }

        public function loadAdminHooks()
        {
            $adminHandlder = new \NinjaErrorLogger\Classes\AdminHandler();
            $adminHandlder->register();
        }

        public function loadPublicHooks()
        {
            $errorHandler = new \NinjaErrorLogger\Classes\ErrorHandler();
            $errorHandler->register();

            $notificationHandler = new \NinjaErrorLogger\Classes\NotificationHandler();
            $notificationHandler->register();
        }

        public function loadDependecies()
        {
            if(!defined('NINJA_ERROR_LOGGER_DIR_PATH')) {
                define('NINJA_ERROR_LOGGER_DIR_PATH', plugin_dir_path(__FILE__));
            }
            if(!defined('NINJA_ERROR_LOGGER_DIR_URL')) {
                define('NINJA_ERROR_LOGGER_DIR_URL', plugin_dir_url(__FILE__));
            }

            include_once NINJA_ERROR_LOGGER_DIR_PATH.'Classes/GeneralSettings.php';
            include_once NINJA_ERROR_LOGGER_DIR_PATH.'Classes/AdminHandler.php';
            include_once NINJA_ERROR_LOGGER_DIR_PATH.'Classes/AjaxHandler.php';
            include_once NINJA_ERROR_LOGGER_DIR_PATH.'Classes/ErrorHandler.php';
            include_once NINJA_ERROR_LOGGER_DIR_PATH.'Classes/NotificationHandler.php';
        }

        public function db()
        {
            if(function_exists('wpFluent')) {
                return wpFluent();
            }
            include_once NINJA_ERROR_LOGGER_DIR_PATH.'Classes/libs/wp-fluent/wp-fluent.php';
            return wpFluent();
        }

    }

    function ninja_error_logger_app() {
        return NinjaErrorLogger::instance();
    }

    ninja_error_logger_app();

    register_activation_hook(__FILE__, function ($newWorkWide) {
        require_once(plugin_dir_path(__FILE__) . 'Classes/Activator.php');
        $activator = new \NinjaErrorLogger\Classes\Activator();
        $activator->migrateDatabases($newWorkWide);
    });

    // Handle Newtwork new Site Activation
    add_action( 'wpmu_new_blog', function ($blogId) {
        require_once(plugin_dir_path(__FILE__) . 'Classes/Activator.php');
        switch_to_blog( $blogId );
        $activator = new \NinjaErrorLogger\Classes\Activator();
        $activator->migrate();
        restore_current_blog();
    } );

}

