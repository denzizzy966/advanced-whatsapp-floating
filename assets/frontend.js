/* Advanced WhatsApp Floating Button - Frontend JavaScript */
/* assets/frontend.js */

(function($) {
    'use strict';
    
    // Plugin Class
    class AdvancedWhatsAppFloating {
        constructor() {
            this.container = null;
            this.button = null;
            this.form = null;
            this.overlay = null;
            this.messageContainer = null;
            this.isFormVisible = false;
            this.isSubmitting = false;
            
            this.init();
        }
        
        init() {
            $(document).ready(() => {
                this.setupElements();
                this.bindEvents();
                this.setupValidation();
                this.setupAccessibility();
            });
        }
        
        setupElements() {
            this.container = $('#awf-floating-container');
            this.button = $('#awf-floating-button');
            this.form = $('#awf-contact-form');
            this.overlay = $('#awf-overlay');
            this.messageContainer = $('#awf-message-container');
            this.contactForm = $('#awf-contact-form-element');
            this.submitButton = this.form.find('.awf-submit-button');
            
            if (!this.container.length || !this.button.length || !this.form.length) {
                return false;
            }
            
            // Add initial classes
            this.container.addClass('awf-initialized');
            
            return true;
        }
        
        bindEvents() {
            // Button click to toggle form
            this.button.on('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleForm();
            });
            
            // Close button
            this.form.find('.awf-close-button').on('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.hideForm();
            });
            
            // Overlay click
            this.overlay.on('click', () => {
                this.hideForm();
            });
            
            // Form submission
            this.contactForm.on('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmission();
            });
            
            // Close message
            $(document).on('click', '.awf-message-close', () => {
                this.hideMessage();
            });
            
            // Click outside to close
            $(document).on('click', (e) => {
                if (this.isFormVisible && 
                    !this.container.is(e.target) && 
                    this.container.has(e.target).length === 0 &&
                    !this.form.is(e.target) && 
                    this.form.has(e.target).length === 0) {
                    this.hideForm();
                }
            });
            
            // Prevent form clicks from closing
            this.form.on('click', (e) => {
                e.stopPropagation();
            });
            
            // Keyboard events
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.isFormVisible) {
                    this.hideForm();
                }
            });
            
            // Form input events for validation
            this.form.find('input, textarea').on('blur', (e) => {
                this.validateField($(e.target));
            });
            
            this.form.find('input, textarea').on('input', (e) => {
                this.clearFieldError($(e.target));
            });
        }
        
        setupValidation() {
            // Real-time validation setup
            this.validationRules = {
                name: {
                    required: true,
                    minLength: 2,
                    pattern: /^[a-zA-Z\s\u00C0-\u017F]+$/
                },
                email: {
                    required: true,
                    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
                },
                phone: {
                    pattern: /^[\+]?[0-9\s\-\(\)]+$/
                },
                company: {
                    minLength: 2
                },
                message: {
                    required: true,
                    minLength: 10,
                    maxLength: 1000
                }
            };
        }
        
        setupAccessibility() {
            // Add ARIA attributes
            this.button.attr({
                'role': 'button',
                'aria-label': 'Open WhatsApp contact form',
                'aria-expanded': 'false'
            });
            
            this.form.attr({
                'role': 'dialog',
                'aria-modal': 'true',
                'aria-labelledby': 'awf-form-title'
            });
            
            // Focus management
            this.form.find('.awf-form-title').attr('id', 'awf-form-title');
        }
        
        toggleForm() {
            if (this.isFormVisible) {
                this.hideForm();
            } else {
                this.showForm();
            }
        }
        
        showForm() {
            if (this.isFormVisible) return;
            
            this.form.show();
            this.overlay.show();
            this.isFormVisible = true;
            
            // Update accessibility
            this.button.attr('aria-expanded', 'true');
            
            // Prevent body scroll on mobile
            if (window.innerWidth <= 768) {
                $('body').addClass('awf-no-scroll');
            }
            
            // Focus first input
            setTimeout(() => {
                const firstInput = this.form.find('input:not([type="hidden"]):first');
                if (firstInput.length) {
                    firstInput.focus();
                }
            }, 100);
            
            // Trigger custom event
            $(document).trigger('awf:form:shown');
        }
        
        hideForm() {
            if (!this.isFormVisible) return;
            
            this.form.hide();
            this.overlay.hide();
            this.isFormVisible = false;
            
            // Update accessibility
            this.button.attr('aria-expanded', 'false');
            
            // Restore body scroll
            $('body').removeClass('awf-no-scroll');
            
            // Return focus to button
            this.button.focus();
            
            // Trigger custom event
            $(document).trigger('awf:form:hidden');
        }
        
        async handleFormSubmission() {
            if (this.isSubmitting) return;
            
            const formData = this.getFormData();
            const validation = this.validateForm(formData);
            
            if (!validation.isValid) {
                this.showValidationErrors(validation.errors);
                return;
            }
            
            this.setSubmittingState(true);
            
            try {
                const response = await this.submitForm(formData);
                this.handleSubmissionSuccess(response);
            } catch (error) {
                this.handleSubmissionError(error);
            } finally {
                this.setSubmittingState(false);
            }
        }
        
        getFormData() {
            return {
                name: this.form.find('[name="name"]').val().trim(),
                email: this.form.find('[name="email"]').val().trim(),
                phone: this.form.find('[name="phone"]').val().trim(),
                company: this.form.find('[name="company"]').val().trim(),
                message: this.form.find('[name="message"]').val().trim()
            };
        }
        
        validateForm(data) {
            const errors = [];
            const requiredFields = awf_ajax.required_fields || ['name', 'email', 'message'];
            
            // Check required fields
            requiredFields.forEach(field => {
                if (!data[field] || data[field].length === 0) {
                    errors.push({
                        field: field,
                        message: this.getFieldLabel(field) + ' is required'
                    });
                }
            });
            
            // Validate email format
            if (data.email && !this.validationRules.email.pattern.test(data.email)) {
                errors.push({
                    field: 'email',
                    message: 'Please enter a valid email address'
                });
            }
            
            // Validate phone format
            if (data.phone && !this.validationRules.phone.pattern.test(data.phone)) {
                errors.push({
                    field: 'phone',
                    message: 'Please enter a valid phone number'
                });
            }
            
            // Validate name format
            if (data.name && !this.validationRules.name.pattern.test(data.name)) {
                errors.push({
                    field: 'name',
                    message: 'Please enter a valid name (letters and spaces only)'
                });
            }
            
            // Validate message length
            if (data.message && data.message.length < 10) {
                errors.push({
                    field: 'message',
                    message: 'Message must be at least 10 characters long'
                });
            }
            
            if (data.message && data.message.length > 1000) {
                errors.push({
                    field: 'message',
                    message: 'Message must be less than 1000 characters'
                });
            }
            
            return {
                isValid: errors.length === 0,
                errors: errors
            };
        }
        
        validateField($field) {
            const fieldName = $field.attr('name');
            const value = $field.val().trim();
            const rules = this.validationRules[fieldName];
            
            if (!rules) return true;
            
            let isValid = true;
            let errorMessage = '';
            
            // Required check
            if (rules.required && !value) {
                isValid = false;
                errorMessage = this.getFieldLabel(fieldName) + ' is required';
            }
            // Pattern check
            else if (value && rules.pattern && !rules.pattern.test(value)) {
                isValid = false;
                errorMessage = 'Please enter a valid ' + this.getFieldLabel(fieldName).toLowerCase();
            }
            // Length checks
            else if (value && rules.minLength && value.length < rules.minLength) {
                isValid = false;
                errorMessage = this.getFieldLabel(fieldName) + ' must be at least ' + rules.minLength + ' characters';
            }
            else if (value && rules.maxLength && value.length > rules.maxLength) {
                isValid = false;
                errorMessage = this.getFieldLabel(fieldName) + ' must be less than ' + rules.maxLength + ' characters';
            }
            
            if (!isValid) {
                this.showFieldError($field, errorMessage);
            } else {
                this.clearFieldError($field);
            }
            
            return isValid;
        }
        
        showFieldError($field, message) {
            $field.addClass('awf-error');
            
            // Remove existing error message
            $field.siblings('.awf-error-message').remove();
            
            // Add error message
            $field.after(`<div class="awf-error-message" style="color: #dc3545; font-size: 0.85em; margin-top: 4px;">${message}</div>`);
            
            // Add shake animation
            $field.addClass('awf-shake');
            setTimeout(() => {
                $field.removeClass('awf-shake');
            }, 500);
        }
        
        clearFieldError($field) {
            $field.removeClass('awf-error');
            $field.siblings('.awf-error-message').remove();
        }
        
        showValidationErrors(errors) {
            errors.forEach(error => {
                const $field = this.form.find(`[name="${error.field}"]`);
                this.showFieldError($field, error.message);
            });
            
            // Focus first error field
            if (errors.length > 0) {
                const $firstErrorField = this.form.find(`[name="${errors[0].field}"]`);
                $firstErrorField.focus();
            }
            
            this.showMessage('Please correct the errors below', 'error');
        }
        
        getFieldLabel(fieldName) {
            const labels = {
                name: 'Name',
                email: 'Email',
                phone: 'Phone',
                company: 'Company',
                message: 'Message'
            };
            return labels[fieldName] || fieldName;
        }
        
        async submitForm(formData) {
            const data = new FormData();
            data.append('action', 'awf_submit_contact');
            data.append('nonce', awf_ajax.nonce);
            
            Object.keys(formData).forEach(key => {
                data.append(key, formData[key]);
            });
            
            const response = await fetch(awf_ajax.ajax_url, {
                method: 'POST',
                body: data
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.data || 'Submission failed');
            }
            
            return result.data;
        }
        
        setSubmittingState(isSubmitting) {
            this.isSubmitting = isSubmitting;
            
            const $submitText = this.submitButton.find('.awf-submit-text');
            const $submitLoading = this.submitButton.find('.awf-submit-loading');
            
            if (isSubmitting) {
                this.submitButton.prop('disabled', true);
                this.submitButton.addClass('awf-loading');
                $submitText.hide();
                $submitLoading.show();
            } else {
                this.submitButton.prop('disabled', false);
                this.submitButton.removeClass('awf-loading');
                $submitText.show();
                $submitLoading.hide();
            }
        }
        
        handleSubmissionSuccess(response) {
            this.showMessage(response.message || awf_ajax.success_message, 'success');
            
            // Open WhatsApp
            if (response.whatsapp_url) {
                window.open(response.whatsapp_url, '_blank', 'noopener,noreferrer');
            }
            
            // Handle post-submit action
            const action = awf_ajax.action_after_submit;
            
            if (action === 'close_form') {
                setTimeout(() => {
                    this.hideForm();
                }, 2000);
            } else if (action === 'reset_form') {
                this.resetForm();
            }
            
            // Add success animation to button
            this.submitButton.addClass('awf-success');
            setTimeout(() => {
                this.submitButton.removeClass('awf-success');
            }, 600);
            
            // Trigger custom event
            $(document).trigger('awf:form:submitted', [response]);
        }
        
        handleSubmissionError(error) {
            console.error('Form submission error:', error);
            this.showMessage(error.message || 'An error occurred. Please try again.', 'error');
            
            // Trigger custom event
            $(document).trigger('awf:form:error', [error]);
        }
        
        resetForm() {
            this.contactForm[0].reset();
            this.form.find('.awf-error').removeClass('awf-error');
            this.form.find('.awf-error-message').remove();
        }
        
        showMessage(text, type = 'info') {
            if (!this.messageContainer.length) return;
            
            const $messageText = this.messageContainer.find('.awf-message-text');
            const $messageContent = this.messageContainer.find('.awf-message-content');
            
            $messageText.text(text);
            $messageContent.removeClass('awf-message-success awf-message-error awf-message-info')
                           .addClass('awf-message-' + type);
            
            this.messageContainer.show();
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                this.hideMessage();
            }, 5000);
        }
        
        hideMessage() {
            this.messageContainer.hide();
        }
        
        // Public API methods
        show() {
            this.showForm();
        }
        
        hide() {
            this.hideForm();
        }
        
        toggle() {
            this.toggleForm();
        }
        
        setPhone(phoneNumber) {
            awf_ajax.phone_number = phoneNumber;
        }
        
        getFormData() {
            return this.getFormData();
        }
        
        isVisible() {
            return this.isFormVisible;
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        // Check if AWF elements exist
        if ($('#awf-floating-container').length) {
            window.AdvancedWhatsAppFloating = new AdvancedWhatsAppFloating();
            
            // Add global CSS for no-scroll
            $('<style>')
                .text('.awf-no-scroll { overflow: hidden !important; }')
                .appendTo('head');
        }
    });
    
    // Expose to global scope
    window.AWF = {
        show: function() {
            if (window.AdvancedWhatsAppFloating) {
                window.AdvancedWhatsAppFloating.show();
            }
        },
        hide: function() {
            if (window.AdvancedWhatsAppFloating) {
                window.AdvancedWhatsAppFloating.hide();
            }
        },
        toggle: function() {
            if (window.AdvancedWhatsAppFloating) {
                window.AdvancedWhatsAppFloating.toggle();
            }
        },
        setPhone: function(phoneNumber) {
            if (window.AdvancedWhatsAppFloating) {
                window.AdvancedWhatsAppFloating.setPhone(phoneNumber);
            }
        },
        isVisible: function() {
            if (window.AdvancedWhatsAppFloating) {
                return window.AdvancedWhatsAppFloating.isVisible();
            }
            return false;
        }
    };
    
})(jQuery);