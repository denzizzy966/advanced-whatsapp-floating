<?php
/**
 * Plugin Name: Advanced WhatsApp Floating Button
 * Plugin URI: https://yoursite.com
 * Description: Advanced WhatsApp floating button with contact form, dashboard, and comprehensive settings for WordPress 6.8.2
 * Version: 2.0.0
 * Author: Your Name
 * Text Domain: advanced-whatsapp-floating
 * Requires at least: 5.0
 * Tested up to: 6.8.2
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('AWF_PLUGIN_VERSION', '2.0.0');
define('AWF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AWF_PLUGIN_PATH', plugin_dir_path(__FILE__));

class AdvancedWhatsAppFloating {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Frontend hooks
        add_action('wp_footer', array($this, 'display_floating_button'));
        add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
        
        // AJAX hooks
        add_action('wp_ajax_awf_save_settings', array($this, 'save_settings'));
        add_action('wp_ajax_awf_submit_contact', array($this, 'handle_contact_submission'));
        add_action('wp_ajax_nopriv_awf_submit_contact', array($this, 'handle_contact_submission'));
        add_action('wp_ajax_awf_export_contacts', array($this, 'export_contacts'));
        add_action('wp_ajax_awf_delete_contact', array($this, 'delete_contact'));
        add_action('wp_ajax_awf_update_contact_status', array($this, 'update_contact_status'));
        add_action('wp_ajax_awf_bulk_action', array($this, 'handle_bulk_action'));
        
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));

        // Add dashboard widget
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );

        // Register activity logging
        add_action( 'init', array( $this, 'register_activity_logging' ) );

    }
    
    public function activate() {
        // Create database tables
        $this->create_database_tables();
        $this->set_default_options();
    }
    
    public function deactivate() {
        // Clean up if needed
    }
    
    private function create_database_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(50),
            company varchar(200),
            message text NOT NULL,
            ip_address varchar(45),
            user_agent text,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
            status varchar(20) DEFAULT 'new',
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    private function set_default_options() {
        $defaults = array(
            'awf_phone_number' => '',
            'awf_background_color' => '#25D366',
            'awf_text_color' => '#ffffff',
            'awf_font_size' => '16',
            'awf_font_family' => 'Inter, sans-serif',
            'awf_position' => 'bottom-right',
            'awf_custom_icon' => '',
            'awf_animated' => '1',
            'awf_action_after_submit' => 'close_form',
            'awf_form_size' => 'medium',
            'awf_show_company_field' => '1',
            'awf_required_fields' => array('name', 'email', 'message'),
            'awf_custom_css' => '',
            'awf_enable_analytics' => '1',
            'awf_form_title' => 'Contact us via WhatsApp',
            'awf_submit_button_text' => 'Send to WhatsApp',
            'awf_success_message' => 'Message sent successfully!',
            'awf_button_size' => '60'
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('advanced-whatsapp-floating', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    public function add_admin_menu() {
        add_menu_page(
            __('WhatsApp Floating', 'advanced-whatsapp-floating'),
            __('WhatsApp Floating', 'advanced-whatsapp-floating'),
            'manage_options',
            'awf-main',
            array($this, 'dashboard_page'),
            'dashicons-whatsapp',
            30
        );
        
        add_submenu_page(
            'awf-main',
            __('Dashboard', 'advanced-whatsapp-floating'),
            __('Dashboard', 'advanced-whatsapp-floating'),
            'manage_options',
            'awf-main',
            array($this, 'dashboard_page')
        );
        
        add_submenu_page(
            'awf-main',
            __('Contacts', 'advanced-whatsapp-floating'),
            __('Contacts', 'advanced-whatsapp-floating'),
            'manage_options',
            'awf-contacts',
            array($this, 'contacts_page')
        );
        
        add_submenu_page(
            'awf-main',
            __('Settings', 'advanced-whatsapp-floating'),
            __('Settings', 'advanced-whatsapp-floating'),
            'manage_options',
            'awf-settings',
            array($this, 'settings_page')
        );
    }
    
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'awf-') !== false) {
            wp_enqueue_style('awf-admin-css', AWF_PLUGIN_URL . 'assets/admin.css', array(), AWF_PLUGIN_VERSION);
            wp_enqueue_script('awf-admin-js', AWF_PLUGIN_URL . 'assets/admin.js', array('jquery'), AWF_PLUGIN_VERSION, true);
            
            wp_localize_script('awf-admin-js', 'awf_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('awf_nonce')
            ));
            
            // Enqueue WordPress media uploader
            wp_enqueue_media();
        }
    }
    
    public function frontend_enqueue_scripts() {
        wp_enqueue_style('awf-frontend-css', AWF_PLUGIN_URL . 'assets/frontend.css', array(), AWF_PLUGIN_VERSION);
        wp_enqueue_script('awf-frontend-js', AWF_PLUGIN_URL . 'assets/frontend.js', array('jquery'), AWF_PLUGIN_VERSION, true);
        
        // Get required fields and ensure it's an array
        $required_fields = get_option('awf_required_fields', array('name', 'email', 'message'));
        if (!is_array($required_fields)) {
            $required_fields = array('name', 'email', 'message');
        }
        
        wp_localize_script('awf-frontend-js', 'awf_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('awf_nonce'),
            'phone_number' => get_option('awf_phone_number', ''),
            'action_after_submit' => get_option('awf_action_after_submit', 'close_form'),
            'success_message' => get_option('awf_success_message', 'Message sent successfully!'),
            'required_fields' => $required_fields
        ));
        
        // Add custom CSS
        $custom_css = get_option('awf_custom_css', '');
        if (!empty($custom_css)) {
            wp_add_inline_style('awf-frontend-css', $custom_css);
        }
        wp_localize_script('awf-frontend-js', 'awf_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('awf_nonce'),
            'phone_number' => get_option('awf_phone_number', ''),
            'action_after_submit' => get_option('awf_action_after_submit', 'close_form'),
            'success_message' => get_option('awf_success_message', 'Message sent successfully!')
        ));
        
        // Add custom CSS
        $custom_css = get_option('awf_custom_css', '');
        if (!empty($custom_css)) {
            wp_add_inline_style('awf-frontend-css', $custom_css);
        }
    }
    
    public function dashboard_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        // Get statistics
        $total_contacts = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $today_contacts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE DATE(submitted_at) = %s", current_time('Y-m-d')));
        $this_month_contacts = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE MONTH(submitted_at) = %d AND YEAR(submitted_at) = %d", 
            date('n'), date('Y')));
        
        // Get recent contacts
        $recent_contacts = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT 5");
        
        include AWF_PLUGIN_PATH . 'templates/admin-dashboard.php';
    }
    
    public function contacts_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        // Handle search and filters
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
        $date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
        $date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';
        
        // Build query
        $where_conditions = array();
        $where_values = array();
        
        if (!empty($search)) {
            $where_conditions[] = "(name LIKE %s OR email LIKE %s OR company LIKE %s OR message LIKE %s)";
            $search_term = '%' . $wpdb->esc_like($search) . '%';
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
            $where_values[] = $search_term;
        }
        
        if (!empty($status_filter)) {
            $where_conditions[] = "status = %s";
            $where_values[] = $status_filter;
        }
        
        if (!empty($date_from)) {
            $where_conditions[] = "DATE(submitted_at) >= %s";
            $where_values[] = $date_from;
        }
        
        if (!empty($date_to)) {
            $where_conditions[] = "DATE(submitted_at) <= %s";
            $where_values[] = $date_to;
        }
        
        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        }
        
        // Pagination
        $per_page = 20;
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($page - 1) * $per_page;
        
        // Get total count
        $count_query = "SELECT COUNT(*) FROM $table_name $where_clause";
        if (!empty($where_values)) {
            $total_items = $wpdb->get_var($wpdb->prepare($count_query, $where_values));
        } else {
            $total_items = $wpdb->get_var($count_query);
        }
        
        // Get contacts
        $contacts_query = "SELECT * FROM $table_name $where_clause ORDER BY submitted_at DESC LIMIT %d OFFSET %d";
        $query_values = array_merge($where_values, array($per_page, $offset));
        
        if (!empty($where_values)) {
            $contacts = $wpdb->get_results($wpdb->prepare($contacts_query, $query_values));
        } else {
            $contacts = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT %d OFFSET %d", $per_page, $offset));
        }
        
        $total_pages = ceil($total_items / $per_page);
        
        include AWF_PLUGIN_PATH . 'templates/admin-contacts.php';
    }
    
    public function settings_page() {
        if (isset($_POST['submit'])) {
            $this->save_settings();
        }
        
        include AWF_PLUGIN_PATH . 'templates/admin-settings.php';
    }
    
    public function save_settings() {
    // Check permissions
        if ( ! current_user_can('manage_options') ) {
            if ( wp_doing_ajax() ) {
                wp_send_json_error(__('You do not have sufficient permissions', 'advanced-whatsapp-floating'));
            } else {
                wp_die(__('You do not have sufficient permissions', 'advanced-whatsapp-floating'));
            }
            return;
        }
    
        // Check nonce (both AJAX & regular form)
        $nonce_valid = false;
        if ( isset($_POST['awf_nonce']) ) {
            $nonce_valid = wp_verify_nonce($_POST['awf_nonce'], 'awf_nonce');
        } elseif ( isset($_POST['nonce']) ) {
            $nonce_valid = wp_verify_nonce($_POST['nonce'], 'awf_nonce');
        }
    
        if ( ! $nonce_valid ) {
            if ( wp_doing_ajax() ) {
                wp_send_json_error(__('Security check failed', 'advanced-whatsapp-floating'));
            } else {
                wp_die(__('Security check failed', 'advanced-whatsapp-floating'));
            }
            return;
        }
    
        // Settings fields
        $settings = [
            'awf_phone_number',
            'awf_background_color',
            'awf_text_color',
            'awf_font_size',
            'awf_font_family',
            'awf_position',
            'awf_custom_icon',
            'awf_animated',
            'awf_action_after_submit',
            'awf_form_size',
            'awf_show_company_field',
            'awf_custom_css',
            'awf_enable_analytics',
            'awf_form_title',
            'awf_submit_button_text',
            'awf_success_message',
            'awf_button_size'
        ];
    
        // Save each setting
        foreach ( $settings as $setting ) {
            if ( isset($_POST[$setting]) ) {
                if ( $setting === 'awf_custom_css' ) {
                    update_option($setting, wp_strip_all_tags($_POST[$setting]));
                } else {
                    update_option($setting, sanitize_text_field($_POST[$setting]));
                }
            } elseif ( in_array($setting, ['awf_animated', 'awf_show_company_field', 'awf_enable_analytics'], true) ) {
                // Unchecked checkboxes
                update_option($setting, '0');
            }
        }
    
        // Save required fields array
        if ( isset($_POST['awf_required_fields']) && is_array($_POST['awf_required_fields']) ) {
            update_option('awf_required_fields', array_map('sanitize_text_field', $_POST['awf_required_fields']));
        } else {
            update_option('awf_required_fields', []);
        }
    
        // Return response
        if ( wp_doing_ajax() ) {
            wp_send_json_success(__('Settings saved successfully!', 'advanced-whatsapp-floating'));
        } else {
            wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
            exit;
        }
    }

    
    public function handle_contact_submission() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'awf_nonce')) {
            wp_send_json_error(__('Security check failed', 'advanced-whatsapp-floating'));
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        // Sanitize input data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $company = isset($_POST['company']) ? sanitize_text_field($_POST['company']) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        
        // Validation
        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error(__('Please fill in all required fields', 'advanced-whatsapp-floating'));
            return;
        }
        
        if (!is_email($email)) {
            wp_send_json_error(__('Please enter a valid email address', 'advanced-whatsapp-floating'));
            return;
        }
        
        // Save to database
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'company' => $company,
                'message' => $message,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'submitted_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error(__('Failed to save contact information', 'advanced-whatsapp-floating'));
            return;
        }
        
        // Format WhatsApp message
        $whatsapp_message = $this->format_whatsapp_message($name, $email, $phone, $company, $message);
        
        wp_send_json_success(array(
            'message' => get_option('awf_success_message', 'Message sent successfully!'),
            'whatsapp_url' => 'https://wa.me/' . get_option('awf_phone_number') . '?text=' . urlencode($whatsapp_message)
        ));
    }
    
    private function format_whatsapp_message($name, $email, $phone, $company, $message) {
        $formatted_message = "*New Contact from Website*\n\n";
        $formatted_message .= "*Name:* $name\n";
        $formatted_message .= "*Email:* $email\n";
        
        if (!empty($phone)) {
            $formatted_message .= "*Phone:* $phone\n";
        }
        
        if (!empty($company)) {
            $formatted_message .= "*Company:* $company\n";
        }
        
        $formatted_message .= "\n*Message:*\n$message";
        
        return $formatted_message;
    }
    
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    public function export_contacts() {
        // Check permissions and nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'awf_nonce') || !current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'advanced-whatsapp-floating'));
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        $contacts = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC", ARRAY_A);
        
        if (empty($contacts)) {
            wp_send_json_error(__('No contacts found', 'advanced-whatsapp-floating'));
            return;
        }
        
        // Generate CSV
        $filename = 'whatsapp-contacts-' . date('Y-m-d-H-i-s') . '.csv';
        
        ob_start();
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, array('ID', 'Name', 'Email', 'Phone', 'Company', 'Message', 'IP Address', 'Submitted At', 'Status'));
        
        // Add data rows
        foreach ($contacts as $contact) {
            fputcsv($output, $contact);
        }
        
        fclose($output);
        $csv_content = ob_get_clean();
        
        wp_send_json_success(array(
            'filename' => $filename,
            'content' => base64_encode($csv_content)
        ));
    }
    
    public function delete_contact() {
        // Check permissions and nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'awf_nonce') || !current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'advanced-whatsapp-floating'));
            return;
        }
        
        if (!isset($_POST['contact_id'])) {
            wp_send_json_error(__('Contact ID is required', 'advanced-whatsapp-floating'));
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        $contact_id = intval($_POST['contact_id']);
        
        $result = $wpdb->delete($table_name, array('id' => $contact_id), array('%d'));
        
        if ($result === false) {
            wp_send_json_error(__('Failed to delete contact', 'advanced-whatsapp-floating'));
            return;
        }
        
        wp_send_json_success(__('Contact deleted successfully', 'advanced-whatsapp-floating'));
    }
    
    public function update_contact_status() {
        // Check permissions and nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'awf_nonce') || !current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'advanced-whatsapp-floating'));
            return;
        }
        
        if (!isset($_POST['contact_id']) || !isset($_POST['status'])) {
            wp_send_json_error(__('Missing required parameters', 'advanced-whatsapp-floating'));
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        $contact_id = intval($_POST['contact_id']);
        $status = sanitize_text_field($_POST['status']);
        
        // Validate status
        $valid_statuses = array('new', 'contacted', 'closed');
        if (!in_array($status, $valid_statuses)) {
            wp_send_json_error(__('Invalid status', 'advanced-whatsapp-floating'));
            return;
        }
        
        $result = $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $contact_id),
            array('%s'),
            array('%d')
        );
        
        if ($result === false) {
            wp_send_json_error(__('Failed to update contact status', 'advanced-whatsapp-floating'));
            return;
        }
        
        wp_send_json_success(__('Contact status updated successfully', 'advanced-whatsapp-floating'));
    }
    
    public function handle_bulk_action() {
        // Check permissions and nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'awf_nonce') || !current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'advanced-whatsapp-floating'));
            return;
        }
        
        if (!isset($_POST['bulk_action']) || !isset($_POST['contact_ids'])) {
            wp_send_json_error(__('Missing required parameters', 'advanced-whatsapp-floating'));
            return;
        }
        
        $bulk_action = sanitize_text_field($_POST['bulk_action']);
        $contact_ids = array_map('intval', $_POST['contact_ids']);
        
        if (empty($contact_ids)) {
            wp_send_json_error(__('No contacts selected', 'advanced-whatsapp-floating'));
            return;
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'awf_contacts';
        
        switch ($bulk_action) {
            case 'delete':
                $placeholders = implode(',', array_fill(0, count($contact_ids), '%d'));
                $query = "DELETE FROM $table_name WHERE id IN ($placeholders)";
                $result = $wpdb->query($wpdb->prepare($query, $contact_ids));
                
                if ($result === false) {
                    wp_send_json_error(__('Failed to delete contacts', 'advanced-whatsapp-floating'));
                    return;
                }
                
                wp_send_json_success(sprintf(__('%d contacts deleted successfully', 'advanced-whatsapp-floating'), $result));
                break;
                
            case 'mark_contacted':
            case 'mark_closed':
                $status = $bulk_action === 'mark_contacted' ? 'contacted' : 'closed';
                $placeholders = implode(',', array_fill(0, count($contact_ids), '%d'));
                $query = "UPDATE $table_name SET status = %s WHERE id IN ($placeholders)";
                $query_params = array_merge(array($status), $contact_ids);
                $result = $wpdb->query($wpdb->prepare($query, $query_params));
                
                if ($result === false) {
                    wp_send_json_error(__('Failed to update contact status', 'advanced-whatsapp-floating'));
                    return;
                }
                
                wp_send_json_success(sprintf(__('%d contacts updated successfully', 'advanced-whatsapp-floating'), $result));
                break;
                
            case 'export_selected':
                $placeholders = implode(',', array_fill(0, count($contact_ids), '%d'));
                $query = "SELECT * FROM $table_name WHERE id IN ($placeholders) ORDER BY submitted_at DESC";
                $contacts = $wpdb->get_results($wpdb->prepare($query, $contact_ids), ARRAY_A);
                
                if (empty($contacts)) {
                    wp_send_json_error(__('No contacts found', 'advanced-whatsapp-floating'));
                    return;
                }
                
                // Generate CSV
                $filename = 'whatsapp-contacts-selected-' . date('Y-m-d-H-i-s') . '.csv';
                
                ob_start();
                $output = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($output, array('ID', 'Name', 'Email', 'Phone', 'Company', 'Message', 'IP Address', 'Submitted At', 'Status'));
                
                // Add data rows
                foreach ($contacts as $contact) {
                    fputcsv($output, $contact);
                }
                
                fclose($output);
                $csv_content = ob_get_clean();
                
                wp_send_json_success(array(
                    'filename' => $filename,
                    'content' => base64_encode($csv_content)
                ));
                break;
                
            default:
                wp_send_json_error(__('Invalid bulk action', 'advanced-whatsapp-floating'));
                return;
        }
    }
    
    public function display_floating_button() {
        $phone_number = get_option('awf_phone_number', '');
        if (empty($phone_number)) {
            return;
        }
        
        $settings = array(
            'background_color' => get_option('awf_background_color', '#25D366'),
            'text_color' => get_option('awf_text_color', '#ffffff'),
            'font_size' => get_option('awf_font_size', '16'),
            'font_family' => get_option('awf_font_family', 'Inter, sans-serif'),
            'position' => get_option('awf_position', 'bottom-right'),
            'custom_icon' => get_option('awf_custom_icon', ''),
            'animated' => get_option('awf_animated', '1'),
            'form_size' => get_option('awf_form_size', 'medium'),
            'show_company_field' => get_option('awf_show_company_field', '1'),
            'form_title' => get_option('awf_form_title', 'Contact us via WhatsApp'),
            'submit_button_text' => get_option('awf_submit_button_text', 'Send to WhatsApp'),
            'button_size' => get_option('awf_button_size', '60'),
            'required_fields' => get_option('awf_required_fields', array('name', 'email', 'message'))
        );
        
        include AWF_PLUGIN_PATH . 'templates/floating-button.php';
    }

    /**
    * Register dashboard widget
    */
    public function add_dashboard_widget() {
        // Check if user can manage options
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        wp_add_dashboard_widget(
            'awf_dashboard_widget', // Widget ID
            __( 'WhatsApp Floating Button', 'advanced-whatsapp-floating' ), // Widget Title
            array( $this, 'render_dashboard_widget' ), // Display callback
            null, // Control callback
            null, // Callback args
            'normal', // Context (normal, side, column3, column4)
            'high' // Priority
        );
    }

    /**
     * Render dashboard widget content
     */
    public function render_dashboard_widget() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'awf_contacts';

    // Get statistics
    $total_contacts     = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    $today_contacts     = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE DATE(submitted_at) = %s",
            current_time( 'Y-m-d' )
        )
    );
    $this_week_contacts = $wpdb->get_var(
        "SELECT COUNT(*) FROM $table_name WHERE YEARWEEK(submitted_at) = YEARWEEK(NOW())"
    );

    // Get recent contacts (last 5)
    $recent_contacts = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT 5"
    );

    // WhatsApp configuration status
    $phone_configured = ! empty( get_option( 'awf_phone_number' ) );

    ?>
    <style>
        .awf-dashboard-widget-content { margin: -12px -12px 0; }
        .awf-widget-stats {
            display: flex; justify-content: space-between;
            padding: 15px; background: #f0f0f1; border-bottom: 1px solid #c3c4c7;
        }
        .awf-stat-item { text-align: center; flex: 1; }
        .awf-stat-number {
            font-size: 24px; font-weight: 600; color: #2271b1;
            display: block; margin-bottom: 5px;
        }
        .awf-stat-label { font-size: 12px; color: #646970; text-transform: uppercase; }
        .awf-widget-content { padding: 15px; }
        .awf-widget-status {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: 4px; margin-bottom: 15px;
            background: <?php echo $phone_configured ? '#d4edda' : '#f8d7da'; ?>;
        }
        .awf-status-icon { font-size: 20px; }
        .awf-status-icon.success { color: #46b450; }
        .awf-status-icon.error { color: #dc3232; }
        .awf-recent-contacts-widget { margin-top: 15px; }
        .awf-recent-contacts-widget h4 {
            margin: 0 0 10px; font-size: 13px; font-weight: 600;
        }
        .awf-contact-item {
            display: flex; justify-content: space-between;
            padding: 8px 0; border-bottom: 1px solid #eee; font-size: 13px;
        }
        .awf-contact-item:last-child { border-bottom: none; }
        .awf-contact-name { font-weight: 500; color: #2271b1; }
        .awf-contact-time { color: #646970; font-size: 11px; }
        .awf-widget-actions {
            display: flex; gap: 10px; margin-top: 15px;
            padding-top: 15px; border-top: 1px solid #eee;
        }
        .awf-widget-actions .button { flex: 1; text-align: center; font-size: 12px; }
        .awf-no-contacts { text-align: center; padding: 20px; color: #646970; }
        .awf-no-contacts .dashicons {
            font-size: 40px; width: 40px; height: 40px; color: #ddd; margin-bottom: 10px;
        }
    </style>

    <div class="awf-dashboard-widget-content">
        <!-- Statistics -->
        <div class="awf-widget-stats">
            <div class="awf-stat-item">
                <span class="awf-stat-number"><?php echo number_format( $total_contacts ); ?></span>
                <span class="awf-stat-label"><?php _e( 'Total', 'advanced-whatsapp-floating' ); ?></span>
            </div>
            <div class="awf-stat-item">
                <span class="awf-stat-number"><?php echo number_format( $today_contacts ); ?></span>
                <span class="awf-stat-label"><?php _e( 'Today', 'advanced-whatsapp-floating' ); ?></span>
            </div>
            <div class="awf-stat-item">
                <span class="awf-stat-number"><?php echo number_format( $this_week_contacts ); ?></span>
                <span class="awf-stat-label"><?php _e( 'This Week', 'advanced-whatsapp-floating' ); ?></span>
            </div>
        </div>

        <div class="awf-widget-content">
            <!-- Status -->
            <div class="awf-widget-status">
                <?php if ( $phone_configured ) : ?>
                    <span class="dashicons dashicons-yes-alt awf-status-icon success"></span>
                    <div>
                        <strong><?php _e( 'WhatsApp is Active', 'advanced-whatsapp-floating' ); ?></strong><br>
                        <span style="font-size: 12px;"><?php echo get_option( 'awf_phone_number' ); ?></span>
                    </div>
                <?php else : ?>
                    <span class="dashicons dashicons-warning awf-status-icon error"></span>
                    <div>
                        <strong><?php _e( 'Setup Required', 'advanced-whatsapp-floating' ); ?></strong><br>
                        <span style="font-size: 12px;"><?php _e( 'Please configure WhatsApp number', 'advanced-whatsapp-floating' ); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Contacts -->
            <?php if ( ! empty( $recent_contacts ) ) : ?>
                <div class="awf-recent-contacts-widget">
                    <h4><?php _e( 'Recent Contacts', 'advanced-whatsapp-floating' ); ?></h4>
                    <?php foreach ( $recent_contacts as $contact ) : ?>
                        <div class="awf-contact-item">
                            <div>
                                <span class="awf-contact-name"><?php echo esc_html( $contact->name ); ?></span><br>
                                <span class="awf-contact-time">
                                    <?php
                                    echo human_time_diff( strtotime( $contact->submitted_at ), current_time( 'timestamp' ) ) . ' ' .
                                            __( 'ago', 'advanced-whatsapp-floating' );
                                    ?>
                                </span>
                            </div>
                            <div>
                                <a href="<?php echo admin_url( 'admin.php?page=awf-contacts' ); ?>" class="button button-small">
                                    <?php _e( 'View', 'advanced-whatsapp-floating' ); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div class="awf-no-contacts">
                    <span class="dashicons dashicons-format-chat"></span>
                    <p><?php _e( 'No contacts yet', 'advanced-whatsapp-floating' ); ?></p>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="awf-widget-actions">
                <a href="<?php echo admin_url( 'admin.php?page=awf-main' ); ?>" class="button">
                    <span class="dashicons dashicons-chart-bar" style="vertical-align: middle;"></span>
                    <?php _e( 'Dashboard', 'advanced-whatsapp-floating' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=awf-contacts' ); ?>" class="button">
                    <span class="dashicons dashicons-groups" style="vertical-align: middle;"></span>
                    <?php _e( 'Contacts', 'advanced-whatsapp-floating' ); ?>
                </a>
                <a href="<?php echo admin_url( 'admin.php?page=awf-settings' ); ?>" class="button button-primary">
                    <span class="dashicons dashicons-admin-settings" style="vertical-align: middle;"></span>
                    <?php _e( 'Settings', 'advanced-whatsapp-floating' ); ?>
                </a>
            </div>
        </div>
    </div>
    <?php
    }



    public function register_activity_logging() {
        // Hook untuk log activity saat ada contact baru
        add_action( 'awf_after_contact_save', array( $this, 'log_contact_activity' ), 10, 2 );
    }

    public function log_contact_activity( $contact_id, $data ) {
        if ( function_exists( 'wp_insert_comment' ) ) {
            $activity_message = sprintf(
                __( 'New WhatsApp contact from %s (%s)', 'advanced-whatsapp-floating' ),
                $data['name'],
                $data['email']
            );

            // Simpan sebagai transient untuk activity feed
            $activities = get_transient( 'awf_recent_activities' ) ?: array();
            array_unshift(
                $activities,
                array(
                    'message' => $activity_message,
                    'time'    => current_time( 'timestamp' ),
                    'type'    => 'contact'
                )
            );

            // Keep only last 10 activities
            $activities = array_slice( $activities, 0, 10 );
            set_transient( 'awf_recent_activities', $activities, DAY_IN_SECONDS );
        }
    }
}

// Initialize the plugin
new AdvancedWhatsAppFloating();