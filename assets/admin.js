/* Advanced WhatsApp Floating Button - Admin JavaScript */
/* assets/admin.js */

(function($) {
    'use strict';
    
    // Admin class
    class AdvancedWhatsAppAdmin {
        constructor() {
            this.init();
        }
        
        init() {
            $(document).ready(() => {
                this.initTabs();
                this.initPreview();
                this.initContactsPage();
                this.initIconUpload();
                this.initModals();
                this.initFormHandlers();
                this.initBulkActions();
                this.initExportImport();
                this.bindEvents();
            });
        }
        
        initTabs() {
            $('.awf-tab-button').on('click', function() {
                const tabId = $(this).data('tab');
                
                // Update active tab button
                $('.awf-tab-button').removeClass('active');
                $(this).addClass('active');
                
                // Update active tab panel
                $('.awf-tab-panel').removeClass('active');
                $('#' + tabId + '-panel').addClass('active');
            });
        }
        
        initPreview() {
            // Update preview when settings change
            const previewElements = {
                button: $('#awf-preview-button .awf-preview-btn'),
                form: $('#awf-preview-form'),
                title: $('#awf-preview-title'),
                submitBtn: $('#awf-preview-submit'),
                companyField: $('#awf-preview-company')
            };
            
            // Background color
            $('#awf_background_color').on('change', function() {
                previewElements.button.css('background-color', $(this).val());
            });
            
            // Text color
            $('#awf_text_color').on('change', function() {
                previewElements.button.css('color', $(this).val());
            });
            
            // Button size
            $('#awf_button_size').on('input', function() {
                const size = $(this).val() + 'px';
                previewElements.button.css({
                    'width': size,
                    'height': size
                });
            });
            
            // Form title
            $('#awf_form_title').on('input', function() {
                previewElements.title.text($(this).val());
            });
            
            // Submit button text
            $('#awf_submit_button_text').on('input', function() {
                previewElements.submitBtn.text($(this).val());
            });
            
            // Company field visibility
            $('#awf_show_company_field').on('change', function() {
                if ($(this).is(':checked')) {
                    previewElements.companyField.show();
                } else {
                    previewElements.companyField.hide();
                }
            });
            
            // Form size
            $('#awf_form_size').on('change', function() {
                const size = $(this).val();
                previewElements.form.removeClass('awf-form-small awf-form-medium awf-form-large')
                                  .addClass('awf-form-' + size);
            });
            
            // Toggle preview form
            $('#awf-toggle-preview-form').on('click', function() {
                previewElements.form.toggle();
            });
            
            // Refresh preview
            $('#awf-refresh-preview').on('click', function() {
                location.reload();
            });
        }
        
        initContactsPage() {
            // Select all checkbox
            $('#awf-select-all').on('change', function() {
                $('input[name="contact_ids[]"]').prop('checked', $(this).is(':checked'));
            });
            
            // Individual checkbox change
            $(document).on('change', 'input[name="contact_ids[]"]', function() {
                const totalCheckboxes = $('input[name="contact_ids[]"]').length;
                const checkedCheckboxes = $('input[name="contact_ids[]"]:checked').length;
                
                $('#awf-select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
            });
            
            // Status change
            $('.awf-status-select').on('change', function() {
                const contactId = $(this).data('contact-id');
                const newStatus = $(this).val();
                const $select = $(this);
                
                $.ajax({
                    url: awf_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'awf_update_contact_status',
                        nonce: awf_ajax.nonce,
                        contact_id: contactId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.success) {
                            $select.closest('tr').addClass('awf-status-updated');
                            setTimeout(() => {
                                $select.closest('tr').removeClass('awf-status-updated');
                            }, 2000);
                        } else {
                            alert('Failed to update status: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Failed to update status. Please try again.');
                    }
                });
            });
            
            // Delete contact
            $(document).on('click', '.awf-delete-contact', function() {
                const contactId = $(this).data('contact-id');
                const $row = $(this).closest('tr');
                
                if (confirm('Are you sure you want to delete this contact? This action cannot be undone.')) {
                    $.ajax({
                        url: awf_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'awf_delete_contact',
                            nonce: awf_ajax.nonce,
                            contact_id: contactId
                        },
                        success: function(response) {
                            if (response.success) {
                                $row.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            } else {
                                alert('Failed to delete contact: ' + response.data);
                            }
                        },
                        error: function() {
                            alert('Failed to delete contact. Please try again.');
                        }
                    });
                }
            });
        }
        
        initIconUpload() {
            // Check if wp.media is available
            if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
                console.warn('WordPress media library is not available');
                return;
            }
            
            // Icon upload
            $('.awf-upload-icon').on('click', function() {
                const mediaUploader = wp.media({
                    title: 'Choose Icon',
                    button: {
                        text: 'Use this icon'
                    },
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                
                mediaUploader.on('select', function() {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#awf_custom_icon').val(attachment.url);
                    $('.awf-icon-preview').html('<img src="' + attachment.url + '" alt="Custom Icon" style="max-width: 50px; max-height: 50px;">');
                    $('.awf-remove-icon').show();
                });
                
                mediaUploader.open();
            });
            
            // Remove icon
            $('.awf-remove-icon').on('click', function() {
                $('#awf_custom_icon').val('');
                $('.awf-icon-preview').html('<span class="awf-no-icon">No custom icon selected</span>');
                $(this).hide();
            });
        }
        
        initModals() {
            // View contact modal
            $(document).on('click', '.awf-view-contact', function() {
                const contactData = $(this).data('contact');
                
                if (contactData) {
                    this.showContactModal(contactData);
                }
            });
            
            // Close modal
            $(document).on('click', '.awf-modal-close', function() {
                $(this).closest('.awf-modal').hide();
            });
            
            // Close modal on background click
            $(document).on('click', '.awf-modal', function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });
            
            // Escape key to close modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $('.awf-modal:visible').hide();
                }
            });
        }
        
        showContactModal(contact) {
            $('#awf-contact-name').text(contact.name || '—');
            $('#awf-contact-email').text(contact.email || '—');
            $('#awf-contact-phone').text(contact.phone || '—');
            $('#awf-contact-company').text(contact.company || '—');
            $('#awf-contact-message').text(contact.message || '—');
            $('#awf-contact-date').text(new Date(contact.submitted_at).toLocaleString());
            $('#awf-contact-ip').text(contact.ip_address || '—');
            $('#awf-contact-user-agent').text(contact.user_agent || '—');
            
            // Update WhatsApp button
            $('#awf-contact-whatsapp').off('click').on('click', function() {
                const phoneNumber = $('#awf_phone_number').val() || awf_ajax.phone_number;
                if (phoneNumber) {
                    const message = encodeURIComponent(`Hello ${contact.name}, thank you for contacting us!`);
                    window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
                } else {
                    alert('WhatsApp number not configured.');
                }
            });
            
            $('#awf-contact-modal').show();
        }
        
        initFormHandlers() {
        const settingsForm = document.querySelector('#awf-settings-form');
        const saveBtn      = document.querySelector('#awf-save-settings');
        const resetBtn     = document.querySelector('#awf-reset-settings');
    
        // Save Settings
        if (settingsForm) {
            settingsForm.addEventListener('submit', async (e) => {
                e.preventDefault();
    
                const originalText = saveBtn.textContent;
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="dashicons dashicons-update-alt spin"></span> Saving...';
    
                try {
                    const formData = new FormData(settingsForm);
                    formData.append('action', 'awf_save_settings');
    
                    // Make sure nonce is included
                    if (!formData.has('awf_nonce')) {
                        formData.append('awf_nonce', awf_ajax.nonce);
                    }
    
                    const response = await fetch(awf_ajax.ajax_url, {
                        method: 'POST',
                        body: formData
                    });
    
                    const data = await response.json();
    
                    if (data.success) {
                        this.showNotice('Settings saved successfully!', 'success');
    
                        const notice = document.createElement('div');
                        notice.className = 'notice notice-success is-dismissible';
                        notice.innerHTML = '<p><strong>Success!</strong> Settings have been saved.</p>';
    
                        const wrap = document.querySelector('.wrap');
                        if (wrap) wrap.prepend(notice);
    
                        setTimeout(() => notice.remove(), 3000);
                    } else {
                        this.showNotice('Failed to save settings: ' + (data.data || 'Unknown error'), 'error');
                    }
                } catch (error) {
                    this.showNotice('Failed to save settings. Error: ' + error.message, 'error');
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.textContent = originalText;
                }
            });
        }
    
        // Save button direct click (backup)
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                if (!saveBtn.disabled) {
                    settingsForm?.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            });
        }
    
        // Reset Settings
        if (resetBtn) {
            resetBtn.addEventListener('click', async () => {
                if (confirm('Are you sure you want to reset all settings to default values? This action cannot be undone.')) {
                    try {
                        const formData = new FormData();
                        formData.append('action', 'awf_reset_settings');
                        formData.append('nonce', awf_ajax.nonce);
    
                        const response = await fetch(awf_ajax.ajax_url, {
                            method: 'POST',
                            body: formData
                        });
    
                        const data = await response.json();
    
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Failed to reset settings: ' + data.data);
                        }
                    } catch (error) {
                        alert('Failed to reset settings. Error: ' + error.message);
                    }
                }
            });
        }
    }

        
        initBulkActions() {
            $('#awf-apply-bulk-action').on('click', function() {
                const action = $('#awf-bulk-action').val();
                const selectedIds = $('input[name="contact_ids[]"]:checked').map(function() {
                    return $(this).val();
                }).get();
                
                if (!action) {
                    alert('Please select an action.');
                    return;
                }
                
                if (selectedIds.length === 0) {
                    alert('Please select at least one contact.');
                    return;
                }
                
                if (action === 'delete' && !confirm('Are you sure you want to delete the selected contacts? This action cannot be undone.')) {
                    return;
                }
                
                const $button = $(this);
                const originalText = $button.text();
                $button.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: awf_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'awf_bulk_action',
                        nonce: awf_ajax.nonce,
                        bulk_action: action,
                        contact_ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            if (action === 'export_selected') {
                                this.downloadFile(response.data.filename, response.data.content);
                            } else {
                                location.reload();
                            }
                        } else {
                            alert('Action failed: ' + response.data);
                        }
                    }.bind(this),
                    error: function() {
                        alert('Action failed. Please try again.');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text(originalText);
                    }
                });
            });
        }
        
        initExportImport() {
            // Export all contacts
            $('.awf-export-contacts, .awf-export-all').on('click', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const originalText = $button.text();
                $button.prop('disabled', true).text('Exporting...');
                
                $.ajax({
                    url: awf_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'awf_export_contacts',
                        nonce: awf_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            this.downloadFile(response.data.filename, response.data.content);
                        } else {
                            alert('Export failed: ' + response.data);
                        }
                    }.bind(this),
                    error: function() {
                        alert('Export failed. Please try again.');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text(originalText);
                    }
                });
            });
            
            // Export settings
            $('#awf-export-settings').on('click', function() {
                $.ajax({
                    url: awf_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'awf_export_settings',
                        nonce: awf_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            this.downloadFile(response.data.filename, response.data.content);
                        } else {
                            alert('Export failed: ' + response.data);
                        }
                    }.bind(this),
                    error: function() {
                        alert('Export failed. Please try again.');
                    }
                });
            });
            
            // Import settings
            $('#awf-import-settings-btn').on('click', function() {
                const fileInput = $('#awf_import_settings')[0];
                const file = fileInput.files[0];
                
                if (!file) {
                    alert('Please select a JSON file to import.');
                    return;
                }
                
                if (file.type !== 'application/json') {
                    alert('Please select a valid JSON file.');
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        const settings = JSON.parse(e.target.result);
                        
                        if (confirm('Are you sure you want to import these settings? This will overwrite your current settings.')) {
                            $.ajax({
                                url: awf_ajax.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'awf_import_settings',
                                    nonce: awf_ajax.nonce,
                                    settings: JSON.stringify(settings)
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert('Settings imported successfully!');
                                        location.reload();
                                    } else {
                                        alert('Import failed: ' + response.data);
                                    }
                                },
                                error: function() {
                                    alert('Import failed. Please try again.');
                                }
                            });
                        }
                    } catch (error) {
                        alert('Invalid JSON file. Please check the file format.');
                    }
                };
                reader.readAsText(file);
            });
        }
        
        bindEvents() {
            // Auto-save draft settings
            let saveTimeout;
            $('input, select, textarea').on('change input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    this.saveDraft();
                }, 2000);
            });
            
            // Phone number validation
            $('#awf_phone_number').on('blur', function() {
                const phone = $(this).val().trim();
                if (phone && !/^\d{10,15}$/.test(phone)) {
                    alert('Please enter a valid phone number (10-15 digits, no spaces or special characters).');
                    $(this).focus();
                }
            });
            
            // Form size preview update
            $('#awf_form_size').on('change', function() {
                $('.awf-preview-form').removeClass('awf-form-small awf-form-medium awf-form-large')
                                     .addClass('awf-form-' + $(this).val());
            });
        }
        
        downloadFile(filename, content) {
            const blob = new Blob([atob(content)], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }
        
        showNotice(message, type = 'info') {
            const noticeClass = type === 'error' ? 'notice-error' : 'notice-success';
            const iconClass   = type === 'error' ? 'dashicons-warning' : 'dashicons-yes';
        
            // Create notice container
            const notice = document.createElement('div');
            notice.className = `notice ${noticeClass} is-dismissible`;
            notice.innerHTML = `
                <p><span class="dashicons ${iconClass}"></span> ${message}</p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text">Dismiss this notice.</span>
                </button>
            `;
        
            // Insert notice after the first <h1> inside .wrap
            const wrap = document.querySelector('.wrap');
            if (wrap) {
                const firstH1 = wrap.querySelector('h1');
                if (firstH1) {
                    firstH1.insertAdjacentElement('afterend', notice);
                } else {
                    wrap.prepend(notice);
                }
            } else {
                document.body.prepend(notice);
            }
        
            // Handle dismiss click
            const dismissBtn = notice.querySelector('.notice-dismiss');
            if (dismissBtn) {
                dismissBtn.addEventListener('click', () => {
                    notice.classList.add('fade-out');
                    setTimeout(() => notice.remove(), 500); // tunggu animasi selesai
                });
            }
        
            // Auto-remove after 5 seconds with fade-out effect
            setTimeout(() => {
                notice.classList.add('fade-out');
                setTimeout(() => notice.remove(), 500); // tunggu animasi selesai
            }, 5000);
        }


        
        saveDraft() {
            // Save form data to localStorage as draft
            const formData = $('#awf-settings-form').serializeArray();
            const draftData = {};
            
            formData.forEach(item => {
                draftData[item.name] = item.value;
            });
            
            localStorage.setItem('awf_settings_draft', JSON.stringify(draftData));
        }
        
        loadDraft() {
            // Load draft data from localStorage
            const draftData = localStorage.getItem('awf_settings_draft');
            if (draftData) {
                try {
                    const settings = JSON.parse(draftData);
                    Object.keys(settings).forEach(key => {
                        const $field = $(`[name="${key}"]`);
                        if ($field.length) {
                            if ($field.is(':checkbox') || $field.is(':radio')) {
                                $field.prop('checked', $field.val() === settings[key]);
                            } else {
                                $field.val(settings[key]);
                            }
                        }
                    });
                } catch (e) {
                    console.error('Failed to load draft settings:', e);
                }
            }
        }
    }
    
    // Initialize admin
    new AdvancedWhatsAppAdmin();
    
})(jQuery);