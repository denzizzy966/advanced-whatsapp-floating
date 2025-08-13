<?php
// templates/admin-contacts.php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap awf-admin">
    <h1 class="wp-heading-inline"><?php _e('WhatsApp Contacts', 'advanced-whatsapp-floating'); ?></h1>
    <a href="#" class="page-title-action awf-export-contacts"><?php _e('Export All', 'advanced-whatsapp-floating'); ?></a>
    <hr class="wp-header-end">
    
    <!-- Filters and Search -->
    <div class="awf-filters-section">
        <form method="get" class="awf-filters-form">
            <input type="hidden" name="page" value="awf-contacts">
            
            <div class="awf-filter-group">
                <label for="awf-search"><?php _e('Search:', 'advanced-whatsapp-floating'); ?></label>
                <input type="text" id="awf-search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Search by name, email, company or message...', 'advanced-whatsapp-floating'); ?>">
            </div>
            
            <div class="awf-filter-group">
                <label for="awf-status-filter"><?php _e('Status:', 'advanced-whatsapp-floating'); ?></label>
                <select id="awf-status-filter" name="status">
                    <option value=""><?php _e('All Statuses', 'advanced-whatsapp-floating'); ?></option>
                    <option value="new" <?php selected($status_filter, 'new'); ?>><?php _e('New', 'advanced-whatsapp-floating'); ?></option>
                    <option value="contacted" <?php selected($status_filter, 'contacted'); ?>><?php _e('Contacted', 'advanced-whatsapp-floating'); ?></option>
                    <option value="closed" <?php selected($status_filter, 'closed'); ?>><?php _e('Closed', 'advanced-whatsapp-floating'); ?></option>
                </select>
            </div>
            
            <div class="awf-filter-group">
                <label for="awf-date-from"><?php _e('Date From:', 'advanced-whatsapp-floating'); ?></label>
                <input type="date" id="awf-date-from" name="date_from" value="<?php echo esc_attr($date_from); ?>">
            </div>
            
            <div class="awf-filter-group">
                <label for="awf-date-to"><?php _e('Date To:', 'advanced-whatsapp-floating'); ?></label>
                <input type="date" id="awf-date-to" name="date_to" value="<?php echo esc_attr($date_to); ?>">
            </div>
            
            <div class="awf-filter-group">
                <button type="submit" class="button button-primary">
                    <span class="dashicons dashicons-search"></span>
                    <?php _e('Filter', 'advanced-whatsapp-floating'); ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=awf-contacts'); ?>" class="button">
                    <?php _e('Clear', 'advanced-whatsapp-floating'); ?>
                </a>
            </div>
        </form>
    </div>
    
    <!-- Results Summary -->
    <div class="awf-results-summary">
        <p>
            <?php
            if (!empty($search) || !empty($status_filter) || !empty($date_from) || !empty($date_to)) {
                printf(__('Showing %d of %d contacts (filtered)', 'advanced-whatsapp-floating'), count($contacts), $total_items);
            } else {
                printf(__('Showing %d contacts', 'advanced-whatsapp-floating'), $total_items);
            }
            ?>
        </p>
    </div>
    
    <!-- Contacts Table -->
    <?php if (!empty($contacts)): ?>
        <div class="awf-table-wrapper">
            <table class="wp-list-table widefat fixed striped awf-contacts-table">
                <thead>
                    <tr>
                        <th class="manage-column column-cb check-column">
                            <input type="checkbox" id="awf-select-all">
                        </th>
                        <th class="manage-column column-name"><?php _e('Name', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-email"><?php _e('Email', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-phone"><?php _e('Phone', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-company"><?php _e('Company', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-message"><?php _e('Message', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-date"><?php _e('Date', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-status"><?php _e('Status', 'advanced-whatsapp-floating'); ?></th>
                        <th class="manage-column column-actions"><?php _e('Actions', 'advanced-whatsapp-floating'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                        <tr data-contact-id="<?php echo $contact->id; ?>">
                            <td class="check-column">
                                <input type="checkbox" name="contact_ids[]" value="<?php echo $contact->id; ?>">
                            </td>
                            <td class="column-name">
                                <strong><?php echo esc_html($contact->name); ?></strong>
                            </td>
                            <td class="column-email">
                                <a href="mailto:<?php echo esc_attr($contact->email); ?>"><?php echo esc_html($contact->email); ?></a>
                            </td>
                            <td class="column-phone">
                                <?php if (!empty($contact->phone)): ?>
                                    <a href="tel:<?php echo esc_attr($contact->phone); ?>"><?php echo esc_html($contact->phone); ?></a>
                                <?php else: ?>
                                    <span class="awf-no-data">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="column-company">
                                <?php echo !empty($contact->company) ? esc_html($contact->company) : '<span class="awf-no-data">—</span>'; ?>
                            </td>
                            <td class="column-message">
                                <div class="awf-message-preview">
                                    <?php echo esc_html(wp_trim_words($contact->message, 10, '...')); ?>
                                </div>
                            </td>
                            <td class="column-date">
                                <div class="awf-date-info">
                                    <div class="awf-date"><?php echo date_i18n(get_option('date_format'), strtotime($contact->submitted_at)); ?></div>
                                    <div class="awf-time"><?php echo date_i18n(get_option('time_format'), strtotime($contact->submitted_at)); ?></div>
                                </div>
                            </td>
                            <td class="column-status">
                                <select class="awf-status-select" data-contact-id="<?php echo $contact->id; ?>">
                                    <option value="new" <?php selected($contact->status, 'new'); ?>><?php _e('New', 'advanced-whatsapp-floating'); ?></option>
                                    <option value="contacted" <?php selected($contact->status, 'contacted'); ?>><?php _e('Contacted', 'advanced-whatsapp-floating'); ?></option>
                                    <option value="closed" <?php selected($contact->status, 'closed'); ?>><?php _e('Closed', 'advanced-whatsapp-floating'); ?></option>
                                </select>
                            </td>
                            <td class="column-actions">
                                <div class="awf-action-buttons">
                                    <button type="button" class="button button-small awf-view-contact" data-contact='<?php echo esc_attr(json_encode($contact)); ?>'>
                                        <span class="dashicons dashicons-visibility"></span>
                                        <?php _e('View', 'advanced-whatsapp-floating'); ?>
                                    </button>
                                    <?php $phone = preg_replace('/\D/', '', $contact->phone); // buang karakter non-angka ?> 
                                    <?php if (substr($phone, 0, 1) === '0') {
                                        $phone = '62' . substr($phone, 1);
                                    }
                                    ?>
                                    <a href="https://wa.me/<?php echo esc_attr($phone); ?>?text=<?php echo urlencode("Hello {$contact->name}, thank you for contacting us!"); ?>" 
                                       target="_blank" class="button button-small awf-whatsapp-btn">
                                        <span class="dashicons dashicons-whatsapp"></span>
                                        <?php _e('WhatsApp', 'advanced-whatsapp-floating'); ?>
                                    </a>
                                    <button type="button" class="button button-small button-link-delete awf-delete-contact" data-contact-id="<?php echo $contact->id; ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                        <?php _e('Delete', 'advanced-whatsapp-floating'); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Bulk Actions -->
        <div class="awf-bulk-actions">
            <select id="awf-bulk-action">
                <option value=""><?php _e('Bulk Actions', 'advanced-whatsapp-floating'); ?></option>
                <option value="delete"><?php _e('Delete', 'advanced-whatsapp-floating'); ?></option>
                <option value="mark_contacted"><?php _e('Mark as Contacted', 'advanced-whatsapp-floating'); ?></option>
                <option value="mark_closed"><?php _e('Mark as Closed', 'advanced-whatsapp-floating'); ?></option>
                <option value="export_selected"><?php _e('Export Selected', 'advanced-whatsapp-floating'); ?></option>
            </select>
            <button type="button" class="button" id="awf-apply-bulk-action">
                <?php _e('Apply', 'advanced-whatsapp-floating'); ?>
            </button>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="awf-pagination">
                <?php
                $pagination_args = array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '&laquo; ' . __('Previous', 'advanced-whatsapp-floating'),
                    'next_text' => __('Next', 'advanced-whatsapp-floating') . ' &raquo;',
                    'total' => $total_pages,
                    'current' => $page,
                    'add_args' => array(
                        's' => $search,
                        'status' => $status_filter,
                        'date_from' => $date_from,
                        'date_to' => $date_to
                    )
                );
                echo paginate_links($pagination_args);
                ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="awf-no-contacts">
            <div class="awf-no-contacts-icon">
                <span class="dashicons dashicons-search"></span>
            </div>
            <?php if (!empty($search) || !empty($status_filter) || !empty($date_from) || !empty($date_to)): ?>
                <h3><?php _e('No contacts found', 'advanced-whatsapp-floating'); ?></h3>
                <p><?php _e('No contacts match your current filter criteria.', 'advanced-whatsapp-floating'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=awf-contacts'); ?>" class="button button-primary">
                    <?php _e('Clear Filters', 'advanced-whatsapp-floating'); ?>
                </a>
            <?php else: ?>
                <h3><?php _e('No contacts yet', 'advanced-whatsapp-floating'); ?></h3>
                <p><?php _e('When visitors submit the WhatsApp contact form, they will appear here.', 'advanced-whatsapp-floating'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=awf-settings'); ?>" class="button button-primary">
                    <?php _e('Configure Settings', 'advanced-whatsapp-floating'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
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
                <div class="awf-contact-field awf-message-field">
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
                <div class="awf-contact-field">
                    <label><?php _e('User Agent:', 'advanced-whatsapp-floating'); ?></label>
                    <span id="awf-contact-user-agent"></span>
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