<?php

namespace NinjaErrorLogger\Classes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Ajax Handler Class
 * @since 1.0.0
 */
class Activator
{

    public function migrateDatabases($network_wide = false)
    {
        global $wpdb;
        if ($network_wide) {
            // Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
            if (function_exists('get_sites') && function_exists('get_current_network_id')) {
                $site_ids = get_sites(array('fields' => 'ids', 'network_id' => get_current_network_id()));
            } else {
                $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;");
            }
            // Install the plugin for all these sites.
            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                $this->migrate();
                restore_current_blog();
            }
        } else {
            $this->migrate();
        }
    }


    public function migrate()
    {
        $this->createLogTable();
    }

    public function createLogTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'nel_error_logs';

        $sql = "CREATE TABLE $table_name (
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id BIGINT(20) NULL,
				log_data longtext NULL,
				log_type varchar(255) NULL,
				log_url varchar(255) NULL,
				referrer varchar(255) NULL,
				log_file TINYTEXT NULL,
				log_line INT(11) NULL,
				provider varchar(255) default 'wp',
				status varchar(255) default 'new',
				request_method varchar(255) NULL,
				log_hash varchar(255) NULL,
				created_at timestamp NULL,
				updated_at timestamp NULL
			) $charset_collate;";


        return $this->runSQL($sql, $table_name);
    }


    private function runSQL($sql, $tableName)
    {
        global $wpdb;
        if ($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            return true;
        }
        return false;
    }

    private function runForceSQL($sql, $tableName)
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        return true;
    }

}