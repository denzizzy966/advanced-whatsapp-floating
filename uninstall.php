<?php
/**
 * Uninstall Advanced WhatsApp Floating Button Plugin
 * 
 * This file is executed when the plugin is deleted from WordPress admin.
 * It removes all plugin data including database tables, options, and transients.
 *
 * @package AdvancedWhatsAppFloating
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

/**
 * Clean up plugin data on uninstall
 */
class AWF_Uninstaller {
    
    /**
     * Run uninstall process
     */
    public static function uninstall() {
        // Check if user has permission to delete plugins
        if (!current_user_can('delete_plugins')) {
            return;
        }
        
        // Remove database tables
        self::remove_database_tables();
        
        // Remove plugin options
        self::remove_plugin_options();
        
        // Remove transients
        self::remove_transients();
        
        // Remove user meta
        self::remove_user_meta();
        
        // Clear any cached data
        self::clear_cache();
        
        // Remove uploaded files
        self::remove_uploaded_files();
        
        // Log uninstall (if debug is enabled)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Advanced WhatsApp Floating Button plugin uninstalled successfully');
        }
    }
    
    /**
     * Remove database tables created by the plugin
     */
    private static function remove_database_tables() {
        global $wpdb;
        
        // List of tables to remove
        $tables = array(
            $wpdb->prefix . 'awf_contacts',
            $wpdb->prefix . 'awf_analytics',
            $wpdb->prefix . 'awf_form_submissions'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
        }
    }
    
    /**
     * Remove all plugin options from wp_options table
     */
    private static function remove_plugin_options() {
        // Plugin options to remove
        $options = array(
            // Basic settings
            'awf_phone_number',
            'awf_form_title',
            'awf_submit_button_text',
            'awf_success_message',
            'awf_action_after_submit',
            
            // Appearance settings
            'awf_background_color',
            'awf_text_color',
            'awf_font_size',
            'awf_font_family',
            'awf_position',
            'awf_custom_icon',
            'awf_animated',
            'awf_button_size',
            
            // Form settings
            'awf_form_size',
            'awf_show_company_field',
            'awf_required_fields',
            
            // Advanced settings
            'awf_custom_css',
            'awf_enable_analytics',
            
            // Internal options
            'awf_version',
            'awf_db_version',
            'awf_first_install',
            'awf_activation_time',
            'awf_settings_backup',
            
            // Analytics options
            'awf_total_submissions',
            'awf_monthly_stats',
            'awf_popular_fields',
            
            // Feature flags
            'awf_beta_features',
            'awf_experimental_mode'
        );
        
        // Remove each option
        foreach ($options as $option) {
            delete_option($option);
            
            // Also remove from multisite if applicable
            if (is_multisite()) {
                delete_site_option($option);
            }
        }
        
        // Remove any options with awf_ prefix that might have been added dynamically
        global $wpdb;
        
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'awf_%'");
        
        if (is_multisite()) {
            $wpdb->query("DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE 'awf_%'");
        }
    }
    
    /**
     * Remove plugin transients
     */
    private static function remove_transients() {
        // Transients to remove
        $transients = array(
            'awf_dashboard_stats',
            'awf_contact_counts',
            'awf_export_cache',
            'awf_analytics_data',
            'awf_system_status',
            'awf_update_check',
            'awf_license_check'
        );
        
        foreach ($transients as $transient) {
            delete_transient($transient);
            delete_site_transient($transient);
        }
        
        // Remove any transients with awf_ prefix
        global $wpdb;
        
        $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_awf_%' OR option_name LIKE '_transient_timeout_awf_%'");
        
        if (is_multisite()) {
            $wpdb->query("DELETE FROM {$wpdb->sitemeta} WHERE meta_key LIKE '_site_transient_awf_%' OR meta_key LIKE '_site_transient_timeout_awf_%'");
        }
    }
    
    /**
     * Remove user meta data related to the plugin
     */
    private static function remove_user_meta() {
        global $wpdb;
        
        // User meta keys to remove
        $meta_keys = array(
            'awf_user_preferences',
            'awf_dashboard_widgets',
            'awf_dismissed_notices',
            'awf_tour_completed',
            'awf_last_login_check'
        );
        
        foreach ($meta_keys as $meta_key) {
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s", $meta_key));
        }
        
        // Remove any user meta with awf_ prefix
        $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'awf_%'");
    }
    
    /**
     * Clear any cached data
     */
    private static function clear_cache() {
        // Clear WordPress object cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }
        
        // Clear popular caching plugins
        
        // W3 Total Cache
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        
        // WP Super Cache
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        
        // WP Rocket
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
        
        // Litespeed Cache
        if (class_exists('LiteSpeed_Cache_API')) {
            LiteSpeed_Cache_API::purge_all();
        }
        
        // Autoptimize
        if (class_exists('autoptimizeCache')) {
            autoptimizeCache::clearall();
        }
    }
    
    /**
     * Remove uploaded files related to the plugin
     */
    private static function remove_uploaded_files() {
        $upload_dir = wp_upload_dir();
        $awf_folder = $upload_dir['basedir'] . '/awf-uploads/';
        
        // Remove custom icons and other uploaded files
        if (is_dir($awf_folder)) {
            self::remove_directory($awf_folder);
        }
        
        // Remove any media library files with awf_ prefix
        global $wpdb;
        
        $attachments = $wpdb->get_results("
            SELECT ID FROM {$wpdb->posts} 
            WHERE post_type = 'attachment' 
            AND post_title LIKE 'awf_%'
        ");
        
        foreach ($attachments as $attachment) {
            wp_delete_attachment($attachment->ID, true);
        }
    }
    
    /**
     * Recursively remove directory and its contents
     *
     * @param string $dir Directory path
     * @return bool Success status
     */
    private static function remove_directory($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        
        $files = array_diff(scandir($dir), array('.', '..'));
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            
            if (is_dir($path)) {
                self::remove_directory($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    /**
     * Remove scheduled cron jobs
     */
    private static function remove_cron_jobs() {
        // Remove scheduled events
        $cron_jobs = array(
            'awf_daily_cleanup',
            'awf_weekly_stats',
            'awf_monthly_report',
            'awf_backup_contacts'
        );
        
        foreach ($cron_jobs as $job) {
            wp_clear_scheduled_hook($job);
        }
    }
    
    /**
     * Remove custom capabilities
     */
    private static function remove_capabilities() {
        // Custom capabilities added by the plugin
        $capabilities = array(
            'manage_awf_contacts',
            'export_awf_data',
            'view_awf_analytics'
        );
        
        // Remove from all roles
        global $wp_roles;
        
        if (isset($wp_roles)) {
            foreach ($wp_roles->roles as $role_name => $role_info) {
                $role = get_role($role_name);
                
                if ($role) {
                    foreach ($capabilities as $cap) {
                        $role->remove_cap($cap);
                    }
                }
            }
        }
    }
    
    /**
     * Clean up multisite installations
     */
    private static function cleanup_multisite() {
        if (!is_multisite()) {
            return;
        }
        
        global $wpdb;
        
        // Get all blog IDs
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
        
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            
            // Remove plugin data for each site
            self::remove_plugin_options();
            self::remove_transients();
            self::remove_user_meta();
            
            restore_current_blog();
        }
    }
    
    /**
     * Log uninstall details
     */
    private static function log_uninstall() {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $log_data = array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id(),
            'site_url' => site_url(),
            'plugin_version' => get_option('awf_version', 'unknown'),
            'contacts_count' => self::get_contacts_count(),
            'settings_backup' => get_option('awf_settings_backup', array())
        );
        
        error_log('AWF Plugin Uninstall: ' . json_encode($log_data));
    }
    
    /**
     * Get total contacts count before deletion
     */
    private static function get_contacts_count() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            return 0;
        }
        
        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
    }
    
    /**
     * Final cleanup verification
     */
    private static function verify_cleanup() {
        global $wpdb;
        
        // Verify tables are removed
        $remaining_tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}awf_%'");
        
        if (!empty($remaining_tables)) {
            error_log('AWF Uninstall Warning: Some database tables were not removed');
        }
        
        // Verify options are removed
        $remaining_options = $wpdb->get_results("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'awf_%'");
        
        if (!empty($remaining_options)) {
            error_log('AWF Uninstall Warning: Some options were not removed');
        }
    }
}

// Run the uninstall process
AWF_Uninstaller::uninstall();

// Optional: Show confirmation message in debug mode
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Advanced WhatsApp Floating Button plugin has been completely uninstalled');
}