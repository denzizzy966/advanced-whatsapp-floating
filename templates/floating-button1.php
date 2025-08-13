<?php
// templates/floating-button.php
if (!defined('ABSPATH')) {
    exit;
}

$position_class = 'awf-position-' . str_replace('-', '-', $settings['position']);
$animated_class = $settings['animated'] ? 'awf-animated' : '';
$form_size_class = 'awf-form-' . $settings['form_size'];
?>

<div id="awf-floating-container" class="awf-floating-container <?php echo $position_class; ?>" style="
    --awf-bg-color: <?php echo esc_attr($settings['background_color']); ?>;
    --awf-text-color: <?php echo esc_attr($settings['text_color']); ?>;
    --awf-font-size: <?php echo esc_attr($settings['font_size']); ?>px;
    --awf-font-family: <?php echo esc_attr($settings['font_family']); ?>;
    --awf-button-size: <?php echo esc_attr($settings['button_size']); ?>px;
">
    <!-- Floating Button -->
    <div id="awf-floating-button" class="awf-floating-button <?php echo $animated_class; ?>">
        <div class="awf-button-content">
            <?php if (!empty($settings['custom_icon'])): ?>
                <img src="<?php echo esc_url($settings['custom_icon']); ?>" alt="WhatsApp" class="awf-custom-icon">
            <?php else: ?>
                <svg class="awf-whatsapp-icon" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516"/>
                </svg>
            <?php endif; ?>
        </div>
        
        <?php if ($settings['animated']): ?>
            <div class="awf-pulse-ring"></div>
            <div class="awf-pulse-ring awf-pulse-ring-delay"></div>
        <?php endif; ?>
    </div>
    
    <!-- Contact Form Modal -->
    <div id="awf-contact-form" class="awf-contact-form <?php echo $form_size_class; ?>" style="display: none;">
        <div class="awf-form-header">
            <h3 class="awf-form-title"><?php echo esc_html($settings['form_title']); ?></h3>
            <button type="button" class="awf-close-button" aria-label="<?php _e('Close', 'advanced-whatsapp-floating'); ?>">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M12.5 3.5L8 8l4.5 4.5-1 1L7 9l-4.5 4.5-1-1L6 8 1.5 3.5l1-1L7 7l4.5-4.5z"/>
                </svg>
            </button>
        </div>
        
        <div class="awf-form-body">
            <form id="awf-contact-form-element" class="awf-form">
                <div class="awf-form-group">
                    <input type="text" 
                           id="awf-name" 
                           name="name" 
                           placeholder="<?php _e('Your Name', 'advanced-whatsapp-floating'); ?>"
                           class="awf-form-input <?php echo in_array('name', $settings['required_fields']) ? 'awf-required' : ''; ?>"
                           <?php echo in_array('name', $settings['required_fields']) ? 'required' : ''; ?>>
                    <label for="awf-name" class="awf-form-label">
                        <?php _e('Name', 'advanced-whatsapp-floating'); ?>
                        <?php if (in_array('name', $settings['required_fields'])): ?>
                            <span class="awf-required-mark">*</span>
                        <?php endif; ?>
                    </label>
                </div>
                
                <div class="awf-form-group">
                    <input type="email" 
                           id="awf-email" 
                           name="email" 
                           placeholder="<?php _e('Your Email', 'advanced-whatsapp-floating'); ?>"
                           class="awf-form-input <?php echo in_array('email', $settings['required_fields']) ? 'awf-required' : ''; ?>"
                           <?php echo in_array('email', $settings['required_fields']) ? 'required' : ''; ?>>
                    <label for="awf-email" class="awf-form-label">
                        <?php _e('Email', 'advanced-whatsapp-floating'); ?>
                        <?php if (in_array('email', $settings['required_fields'])): ?>
                            <span class="awf-required-mark">*</span>
                        <?php endif; ?>
                    </label>
                </div>
                
                <div class="awf-form-group">
                    <input type="tel" 
                           id="awf-phone" 
                           name="phone" 
                           placeholder="<?php _e('Your Phone Number', 'advanced-whatsapp-floating'); ?>"
                           class="awf-form-input <?php echo in_array('phone', $settings['required_fields']) ? 'awf-required' : ''; ?>"
                           <?php echo in_array('phone', $settings['required_fields']) ? 'required' : ''; ?>>
                    <label for="awf-phone" class="awf-form-label">
                        <?php _e('Phone', 'advanced-whatsapp-floating'); ?>
                        <?php if (in_array('phone', $settings['required_fields'])): ?>
                            <span class="awf-required-mark">*</span>
                        <?php endif; ?>
                    </label>
                </div>
                
                <?php if ($settings['show_company_field']): ?>
                <div class="awf-form-group">
                    <input type="text" 
                           id="awf-company" 
                           name="company" 
                           placeholder="<?php _e('Your Company', 'advanced-whatsapp-floating'); ?>"
                           class="awf-form-input <?php echo in_array('company', $settings['required_fields']) ? 'awf-required' : ''; ?>"
                           <?php echo in_array('company', $settings['required_fields']) ? 'required' : ''; ?>>
                    <label for="awf-company" class="awf-form-label">
                        <?php _e('Company', 'advanced-whatsapp-floating'); ?>
                        <?php if (in_array('company', $settings['required_fields'])): ?>
                            <span class="awf-required-mark">*</span>
                        <?php endif; ?>
                    </label>
                </div>
                <?php endif; ?>
                
                <div class="awf-form-group">
                    <textarea id="awf-message" 
                              name="message" 
                              rows="4"
                              placeholder="<?php _e('Your Message', 'advanced-whatsapp-floating'); ?>"
                              class="awf-form-textarea <?php echo in_array('message', $settings['required_fields']) ? 'awf-required' : ''; ?>"
                              <?php echo in_array('message', $settings['required_fields']) ? 'required' : ''; ?>></textarea>
                    <label for="awf-message" class="awf-form-label">
                        <?php _e('Message', 'advanced-whatsapp-floating'); ?>
                        <?php if (in_array('message', $settings['required_fields'])): ?>
                            <span class="awf-required-mark">*</span>
                        <?php endif; ?>
                    </label>
                </div>
                
                <div class="awf-form-group">
                    <button type="submit" class="awf-submit-button">
                        <span class="awf-submit-text"><?php echo esc_html($settings['submit_button_text']); ?></span>
                        <span class="awf-submit-loading" style="display: none;">
                            <svg class="awf-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                                    <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/>
                                    <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/>
                                </circle>
                            </svg>
                            <?php _e('Sending...', 'advanced-whatsapp-floating'); ?>
                        </span>
                        <svg class="awf-whatsapp-icon-small" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.516"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="awf-form-footer">
            <div class="awf-privacy-notice">
                <small><?php _e('Your information is secure and will only be used to contact you via WhatsApp.', 'advanced-whatsapp-floating'); ?></small>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="awf-message-container" class="awf-message-container" style="display: none;">
        <div class="awf-message-content">
            <div class="awf-message-icon">
                <svg class="awf-success-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="awf-error-icon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <path d="m15 9-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="m9 9 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="awf-message-text"></div>
            <button type="button" class="awf-message-close" aria-label="<?php _e('Close', 'advanced-whatsapp-floating'); ?>">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M12.5 3.5L8 8l4.5 4.5-1 1L7 9l-4.5 4.5-1-1L6 8 1.5 3.5l1-1L7 7l4.5-4.5z"/>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Overlay for mobile -->
    <div id="awf-overlay" class="awf-overlay" style="display: none;"></div>
</div>

<script type="text/javascript">
// Inline critical JavaScript to prevent FOUC and ensure immediate functionality
(function() {
    'use strict';
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAWF);
    } else {
        initAWF();
    }
    
    function initAWF() {
        var container = document.getElementById('awf-floating-container');
        var button = document.getElementById('awf-floating-button');
        var form = document.getElementById('awf-contact-form');
        var overlay = document.getElementById('awf-overlay');
        var closeBtn = form.querySelector('.awf-close-button');
        var messageContainer = document.getElementById('awf-message-container');
        
        if (!container || !button || !form) return;
        
        // Toggle form visibility
        function toggleForm() {
            var isVisible = form.style.display !== 'none';
            
            if (isVisible) {
                hideForm();
            } else {
                showForm();
            }
        }
        
        function showForm() {
            form.style.display = 'block';
            if (overlay) overlay.style.display = 'block';
            document.body.style.overflow = window.innerWidth <= 768 ? 'hidden' : '';
            
            // Focus first input
            setTimeout(function() {
                var firstInput = form.querySelector('input:not([type="hidden"])');
                if (firstInput) firstInput.focus();
            }, 100);
        }
        
        function hideForm() {
            form.style.display = 'none';
            if (overlay) overlay.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        function showMessage(text, type) {
            if (!messageContainer) return;
            
            var messageText = messageContainer.querySelector('.awf-message-text');
            var messageContent = messageContainer.querySelector('.awf-message-content');
            
            if (messageText) messageText.textContent = text;
            if (messageContent) {
                messageContent.className = 'awf-message-content awf-message-' + type;
            }
            
            messageContainer.style.display = 'block';
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                messageContainer.style.display = 'none';
            }, 5000);
        }
        
        // Event listeners
        if (button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleForm();
            });
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                hideForm();
            });
        }
        
        if (overlay) {
            overlay.addEventListener('click', function() {
                hideForm();
            });
        }
        
        // Close message
        var messageClose = messageContainer ? messageContainer.querySelector('.awf-message-close') : null;
        if (messageClose) {
            messageClose.addEventListener('click', function() {
                messageContainer.style.display = 'none';
            });
        }
        
        // Handle form submission
        var contactForm = document.getElementById('awf-contact-form-element');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                var submitButton = this.querySelector('.awf-submit-button');
                var submitText = submitButton.querySelector('.awf-submit-text');
                var submitLoading = submitButton.querySelector('.awf-submit-loading');
                
                // Show loading state
                submitText.style.display = 'none';
                submitLoading.style.display = 'inline-flex';
                submitButton.disabled = true;
                
                // Get form data
                var formData = new FormData(this);
                formData.append('action', 'awf_submit_contact');
                formData.append('nonce', awf_ajax.nonce);
                
                // Submit via fetch or XMLHttpRequest
                var xhr = new XMLHttpRequest();
                xhr.open('POST', awf_ajax.ajax_url);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        // Reset button state
                        submitText.style.display = 'inline';
                        submitLoading.style.display = 'none';
                        submitButton.disabled = false;
                        
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                showMessage(response.data.message || awf_ajax.success_message, 'success');
                                
                                // Open WhatsApp
                                if (response.data.whatsapp_url) {
                                    window.open(response.data.whatsapp_url, '_blank');
                                }
                                
                                // Handle post-submit action
                                if (awf_ajax.action_after_submit === 'close_form') {
                                    setTimeout(hideForm, 2000);
                                } else if (awf_ajax.action_after_submit === 'reset_form') {
                                    contactForm.reset();
                                }
                            } else {
                                showMessage(response.data || 'An error occurred. Please try again.', 'error');
                            }
                        } catch (e) {
                            showMessage('An error occurred. Please try again.', 'error');
                        }
                    }
                };
                xhr.send(formData);
            });
        }
        
        // Close form when clicking outside
        document.addEventListener('click', function(e) {
            if (form.style.display !== 'none' && 
                !container.contains(e.target) && 
                !form.contains(e.target)) {
                hideForm();
            }
        });
        
        // Prevent form click from closing
        form.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Handle escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && form.style.display !== 'none') {
                hideForm();
            }
        });
    }
})();
</script>