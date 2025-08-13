<?php
// templates/admin-dashboard.php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap awf-admin">
    <h1><?php _e('WhatsApp Floating Button Dashboard', 'advanced-whatsapp-floating'); ?></h1>
    
    <div class="awf-dashboard-grid">
        <!-- Statistics Cards -->
        <div class="awf-stats-row">
            <div class="awf-stat-card">
                <div class="awf-stat-icon">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div class="awf-stat-content">
                    <h3><?php echo number_format($total_contacts); ?></h3>
                    <p><?php _e('Total Contacts', 'advanced-whatsapp-floating'); ?></p>
                </div>
            </div>
            
            <div class="awf-stat-card">
                <div class="awf-stat-icon">
                    <span class="dashicons dashicons-calendar-alt"></span>
                </div>
                <div class="awf-stat-content">
                    <h3><?php echo number_format($today_contacts); ?></h3>
                    <p><?php _e('Today\'s Contacts', 'advanced-whatsapp-floating'); ?></p>
                </div>
            </div>
            
            <div class="awf-stat-card">
                <div class="awf-stat-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <div class="awf-stat-content">
                    <h3><?php echo number_format($this_month_contacts); ?></h3>
                    <p><?php _e('This Month', 'advanced-whatsapp-floating'); ?></p>
                </div>
            </div>
            
            <div class="awf-stat-card">
                <div class="awf-stat-icon">
                    <span class="dashicons dashicons-whatsapp"></span>
                </div>
                <div class="awf-stat-content">
                    <h3><?php echo !empty(get_option('awf_phone_number')) ? '✓' : '✗'; ?></h3>
                    <p><?php _e('WhatsApp Setup', 'advanced-whatsapp-floating'); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="awf-quick-actions">
            <h2><?php _e('Quick Actions', 'advanced-whatsapp-floating'); ?></h2>
            <div class="awf-action-buttons">
                <a href="<?php echo admin_url('admin.php?page=awf-settings'); ?>" class="button button-primary">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e('Settings', 'advanced-whatsapp-floating'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=awf-contacts'); ?>" class="button button-secondary">
                    <span class="dashicons dashicons-list-view"></span>
                    <?php _e('View All Contacts', 'advanced-whatsapp-floating'); ?>
                </a>
                <button type="button" class="button awf-export-all">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Export Contacts', 'advanced-whatsapp-floating'); ?>
                </button>
            </div>
        </div>
        
        <!-- Recent Contacts -->
        <div class="awf-recent-contacts">
            <h2><?php _e('Recent Contacts', 'advanced-whatsapp-floating'); ?></h2>
            <?php if (!empty($recent_contacts)): ?>
                <div class="awf-contacts-table">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e('Name', 'advanced-whatsapp-floating'); ?></th>
                                <th><?php _e('Email', 'advanced-whatsapp-floating'); ?></th>
                                <th><?php _e('Company', 'advanced-whatsapp-floating'); ?></th>
                                <th><?php _e('Date', 'advanced-whatsapp-floating'); ?></th>
                                <th><?php _e('Actions', 'advanced-whatsapp-floating'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_contacts as $contact): ?>
                                <tr>
                                    <td><strong><?php echo esc_html($contact->name); ?></strong></td>
                                    <td><?php echo esc_html($contact->email); ?></td>
                                    <td><?php echo esc_html($contact->company); ?></td>
                                    <td><?php echo date_i18n(get_option('date_format'), strtotime($contact->submitted_at)); ?></td>
                                    <td>
                                        <button type="button" class="button button-small awf-view-contact" data-id="<?php echo $contact->id; ?>">
                                            <?php _e('View', 'advanced-whatsapp-floating'); ?>
                                        </button>
                                        <a href="https://wa.me/<?php echo get_option('awf_phone_number'); ?>" target="_blank" class="button button-small">
                                            <?php _e('WhatsApp', 'advanced-whatsapp-floating'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="awf-no-contacts">
                    <div class="awf-no-contacts-icon">
                        <span class="dashicons dashicons-groups"></span>
                    </div>
                    <h3><?php _e('No contacts yet', 'advanced-whatsapp-floating'); ?></h3>
                    <p><?php _e('When visitors submit the WhatsApp contact form, they will appear here.', 'advanced-whatsapp-floating'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=awf-settings'); ?>" class="button button-primary">
                        <?php _e('Configure Settings', 'advanced-whatsapp-floating'); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- System Status -->
        <div class="awf-system-status">
            <h2><?php _e('System Status', 'advanced-whatsapp-floating'); ?></h2>
            <div class="awf-status-items">
                <div class="awf-status-item">
                    <span class="awf-status-label"><?php _e('WhatsApp Number', 'advanced-whatsapp-floating'); ?></span>
                    <span class="awf-status-value <?php echo !empty(get_option('awf_phone_number')) ? 'awf-status-success' : 'awf-status-error'; ?>">
                        <?php echo !empty(get_option('awf_phone_number')) ? __('Configured', 'advanced-whatsapp-floating') : __('Not Configured', 'advanced-whatsapp-floating'); ?>
                    </span>
                </div>
                
                <div class="awf-status-item">
                    <span class="awf-status-label"><?php _e('Button Position', 'advanced-whatsapp-floating'); ?></span>
                    <span class="awf-status-value awf-status-info">
                        <?php echo ucfirst(str_replace('-', ' ', get_option('awf_position', 'bottom-right'))); ?>
                    </span>
                </div>
                
                <div class="awf-status-item">
                    <span class="awf-status-label"><?php _e('Animation', 'advanced-whatsapp-floating'); ?></span>
                    <span class="awf-status-value <?php echo get_option('awf_animated', '1') ? 'awf-status-success' : 'awf-status-warning'; ?>">
                        <?php echo get_option('awf_animated', '1') ? __('Enabled', 'advanced-whatsapp-floating') : __('Disabled', 'advanced-whatsapp-floating'); ?>
                    </span>
                </div>
                
                <div class="awf-status-item">
                    <span class="awf-status-label"><?php _e('Company Field', 'advanced-whatsapp-floating'); ?></span>
                    <span class="awf-status-value <?php echo get_option('awf_show_company_field', '1') ? 'awf-status-success' : 'awf-status-warning'; ?>">
                        <?php echo get_option('awf_show_company_field', '1') ? __('Visible', 'advanced-whatsapp-floating') : __('Hidden', 'advanced-whatsapp-floating'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Details Modal -->
<div id="awf-contact-modal" class="awf-modal" style="display: none;">
    <div class="awf-modal-content">
        <div class="awf-modal-header">
            <h2><?php _e('Contact Details', 'advanced-whatsapp-floating'); ?></h2>
            <button type="button" class="awf-modal-close">&times;</button>
        </div>
        <div class="awf-modal-body">
            <div class="awf-contact-details">
                <div class="awf-contact-field">
                    <label><?php _e('Name:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-name"></span>
                </div>
                <div class="awf-contact-field">
                    <label><?php _e('Email:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-email"></span>
                </div>
                <div class="awf-contact-field">
                    <label><?php _e('Phone:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-phone"></span>
                </div>
                <div class="awf-contact-field">
                    <label><?php _e('Company:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-company"></span>
                </div>
                <div class="awf-contact-field">
                    <label><?php _e('Message:', 'advanced-whatsapp-floating'); ?></label>
                    <div id="awf-contact-message"></div>
                </div>
                <div class="awf-contact-field">
                    <label><?php _e('Submitted:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-date"></span>
                </div>
                <div class="awf-contact-field">
                    <label><?php _e('IP Address:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-ip"></span>
                </div>
            </div>
        </div>
        <div class="awf-modal-footer">
            <button type="button" class="button button-primary" id="awf-contact-whatsapp">
                <span class="dashicons dashicons-whatsapp"></span>
                <?php _e('Open WhatsApp', 'advanced-whatsapp-floating'); ?>
            </button>
            <button type="button" class="button awf-modal-close">
                <?php _e('Close', 'advanced-whatsapp-floating'); ?>
            </button>
        </div>
    </div>
</div>