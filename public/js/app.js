/**
 * Reusable JavaScript Utilities
 * Contains CRUD operations and DataTable helper functions
 */

(function(global) {
    'use strict';

    /**
     * SBR Application Namespace
     */
    const SBR = {
        /**
         * Configuration
         */
        config: {
            alertTimeout: 4000,
            csrfToken: null
        },

        /**
         * Initialize the application
         */
        init: function() {
            this.config.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            this.setupAjaxDefaults();
        },

        /**
         * Setup jQuery AJAX defaults with CSRF token
         */
        setupAjaxDefaults: function() {
            if (typeof $ !== 'undefined' && this.config.csrfToken) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': this.config.csrfToken
                    }
                });
            }
        },

        /**
         * ==========================================================================
         * Alert System
         * ==========================================================================
         */
        alert: {
            /**
             * Show an alert message
             * @param {string} type - Alert type: 'success', 'danger', 'warning', 'info'
             * @param {string} message - Alert message
             * @param {string} containerId - Container element ID (default: 'alertContainer')
             * @param {number} timeout - Auto-hide timeout in ms (0 to disable)
             */
            show: function(type, message, containerId = 'alertContainer', timeout = SBR.config.alertTimeout) {
                const container = document.getElementById(containerId);
                if (!container) {
                    console.warn(`Alert container '${containerId}' not found`);
                    return;
                }

                const iconMap = {
                    success: 'check-circle',
                    danger: 'exclamation-circle',
                    warning: 'exclamation-triangle',
                    info: 'info-circle'
                };

                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show">
                        <i class="bi bi-${iconMap[type] || 'info-circle'}"></i>
                        ${this.escapeHtml(message)}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                container.innerHTML = alertHtml;

                if (timeout > 0) {
                    setTimeout(() => {
                        const alert = container.querySelector('.alert');
                        if (alert) {
                            alert.classList.remove('show');
                            setTimeout(() => alert.remove(), 150);
                        }
                    }, timeout);
                }
            },

            /**
             * Show success alert
             */
            success: function(message, containerId, timeout) {
                this.show('success', message, containerId, timeout);
            },

            /**
             * Show error alert
             */
            error: function(message, containerId, timeout) {
                this.show('danger', message, containerId, timeout);
            },

            /**
             * Show warning alert
             */
            warning: function(message, containerId, timeout) {
                this.show('warning', message, containerId, timeout);
            },

            /**
             * Show info alert
             */
            info: function(message, containerId, timeout) {
                this.show('info', message, containerId, timeout);
            },

            /**
             * Clear all alerts in container
             */
            clear: function(containerId = 'alertContainer') {
                const container = document.getElementById(containerId);
                if (container) {
                    container.innerHTML = '';
                }
            },

            /**
             * Escape HTML to prevent XSS
             */
            escapeHtml: function(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        },

        /**
         * ==========================================================================
         * Form Utilities
         * ==========================================================================
         */
        form: {
            /**
             * Reset form and clear validation errors
             * @param {string} formId - Form element ID
             */
            reset: function(formId) {
                const form = document.getElementById(formId);
                if (form) {
                    form.reset();
                    this.clearErrors(formId);
                }
            },

            /**
             * Clear validation errors from form
             * @param {string} formId - Form element ID
             */
            clearErrors: function(formId) {
                const form = document.getElementById(formId);
                if (!form) return;

                // Remove is-invalid class from all inputs
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                // Clear invalid-feedback text
                form.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.textContent = '';
                });

                // Clear error-message text
                form.querySelectorAll('.error-message').forEach(el => {
                    el.textContent = '';
                });

                // Hide form error container
                const errorContainer = document.getElementById(`${formId}Errors`);
                if (errorContainer) {
                    errorContainer.classList.add('d-none');
                    errorContainer.innerHTML = '';
                }
            },

            /**
             * Display validation errors on form
             * @param {string} formId - Form element ID
             * @param {object|string} errors - Validation errors object or error message
             */
            showErrors: function(formId, errors) {
                this.clearErrors(formId);
                const form = document.getElementById(formId);
                if (!form) return;

                if (typeof errors === 'object' && errors !== null) {
                    for (const field in errors) {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');

                            // Try invalid-feedback first
                            let feedback = input.parentElement?.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                            }

                            // Try error-message (for form-group-modal style)
                            let errorMsg = document.getElementById(`${input.id}Error`);
                            if (errorMsg) {
                                errorMsg.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                            }
                        }
                    }
                } else {
                    // Show general error message
                    const errorContainer = document.getElementById(`${formId}Errors`);
                    if (errorContainer) {
                        errorContainer.classList.remove('d-none');
                        errorContainer.innerHTML = errors;
                    }
                }
            },

            /**
             * Get form data as object
             * @param {string} formId - Form element ID
             * @returns {object} Form data object
             */
            getData: function(formId) {
                const form = document.getElementById(formId);
                if (!form) return {};

                const formData = new FormData(form);
                const data = {};

                formData.forEach((value, key) => {
                    if (data[key]) {
                        // Handle multiple values (e.g., checkboxes)
                        if (!Array.isArray(data[key])) {
                            data[key] = [data[key]];
                        }
                        data[key].push(value);
                    } else {
                        data[key] = value;
                    }
                });

                return data;
            },

            /**
             * Populate form with data
             * @param {string} formId - Form element ID
             * @param {object} data - Data object
             */
            populate: function(formId, data) {
                const form = document.getElementById(formId);
                if (!form || !data) return;

                for (const key in data) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = Boolean(data[key]);
                        } else if (input.type === 'radio') {
                            const radio = form.querySelector(`[name="${key}"][value="${data[key]}"]`);
                            if (radio) radio.checked = true;
                        } else {
                            input.value = data[key] ?? '';
                        }
                    }
                }
            }
        },

        /**
         * ==========================================================================
         * CRUD Operations
         * ==========================================================================
         */
        crud: {
            /**
             * Fetch a single record
             * @param {string} url - API endpoint URL
             * @param {function} onSuccess - Success callback (receives data)
             * @param {function} onError - Error callback (receives error message)
             */
            get: function(url, onSuccess, onError) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (onSuccess) onSuccess(response.data || response);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Failed to load data.';
                        if (onError) onError(message, xhr);
                    }
                });
            },

            /**
             * Create a new record
             * @param {string} url - API endpoint URL
             * @param {object} data - Data to send
             * @param {function} onSuccess - Success callback (receives response)
             * @param {function} onError - Error callback (receives errors, xhr)
             */
            create: function(url, data, onSuccess, onError) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (onSuccess) onSuccess(response);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || xhr.responseJSON?.message || 'Create operation failed.';
                        if (onError) onError(errors, xhr);
                    }
                });
            },

            /**
             * Update an existing record
             * @param {string} url - API endpoint URL
             * @param {object} data - Data to send
             * @param {function} onSuccess - Success callback (receives response)
             * @param {function} onError - Error callback (receives errors, xhr)
             */
            update: function(url, data, onSuccess, onError) {
                // Add _method for Laravel PUT/PATCH
                data._method = 'PUT';

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (onSuccess) onSuccess(response);
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON?.errors || xhr.responseJSON?.message || 'Update operation failed.';
                        if (onError) onError(errors, xhr);
                    }
                });
            },

            /**
             * Delete a record
             * @param {string} url - API endpoint URL
             * @param {function} onSuccess - Success callback (receives response)
             * @param {function} onError - Error callback (receives error message, xhr)
             */
            delete: function(url, onSuccess, onError) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (onSuccess) onSuccess(response);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Delete operation failed.';
                        if (onError) onError(message, xhr);
                    }
                });
            },

            /**
             * Toggle status of a record
             * @param {string} url - API endpoint URL
             * @param {function} onSuccess - Success callback (receives response)
             * @param {function} onError - Error callback (receives error message, xhr)
             */
            toggleStatus: function(url, onSuccess, onError) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (onSuccess) onSuccess(response);
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Toggle status failed.';
                        if (onError) onError(message, xhr);
                    }
                });
            }
        },

        /**
         * ==========================================================================
         * DataTable Utilities
         * ==========================================================================
         */
        dataTable: {
            /**
             * Default DataTable configuration
             */
            defaults: {
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']],
                dom: '<"p-3"<"row"<"col-sm-6"l><"col-sm-6"f>>>t<"p-3"<"row"<"col-sm-6"i><"col-sm-6"p>>>',
                language: {
                    processing: '<div class="d-flex justify-content-center"><div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                    emptyTable: '<div class="empty-state"><i class="bi bi-inbox"></i><p>No records found</p></div>',
                    zeroRecords: '<div class="empty-state"><i class="bi bi-search"></i><p>No matching records found</p></div>',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    lengthMenu: 'Show _MENU_ entries',
                    search: 'Search:',
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    }
                }
            },

            /**
             * Initialize a DataTable with custom configuration
             * @param {string} tableId - Table element ID
             * @param {object} config - DataTable configuration
             * @returns {DataTable} DataTable instance
             */
            init: function(tableId, config = {}) {
                const tableElement = document.getElementById(tableId);
                if (!tableElement) {
                    console.error(`Table '${tableId}' not found`);
                    return null;
                }

                // Merge defaults with custom config
                const finalConfig = $.extend(true, {}, this.defaults, config);

                return $(`#${tableId}`).DataTable(finalConfig);
            },

            /**
             * Reload DataTable data
             * @param {DataTable} table - DataTable instance
             * @param {boolean} resetPaging - Reset to first page (default: false)
             */
            reload: function(table, resetPaging = false) {
                if (table && typeof table.ajax === 'object') {
                    table.ajax.reload(null, resetPaging);
                }
            },

            /**
             * Destroy DataTable instance
             * @param {DataTable} table - DataTable instance
             */
            destroy: function(table) {
                if (table && typeof table.destroy === 'function') {
                    table.destroy();
                }
            },

            /**
             * Render action buttons for table rows
             * @param {number|string|object} idOrRow - Row ID or row data object
             * @param {array|object} actions - Array of action types ['view', 'edit', 'delete'] or object config
             * @param {string} suffix - Optional suffix for button classes (e.g., 'province' creates 'view-btn-province')
             * @returns {string} HTML string of action buttons
             */
            renderActions: function(idOrRow, actions, suffix) {
                let html = '';
                let id, row;
                const btnSuffix = suffix ? `-${suffix}` : '';

                // Handle different parameter formats
                if (typeof idOrRow === 'object') {
                    row = idOrRow;
                    id = row.id;
                } else {
                    id = idOrRow;
                    row = { id: id };
                }

                // Convert array to object format
                if (Array.isArray(actions)) {
                    const actionsObj = {};
                    actions.forEach(action => actionsObj[action] = true);
                    actions = actionsObj;
                }

                if (actions.view) {
                    html += `<button class="action-icon view-btn${btnSuffix}" data-id="${id}" title="View">
                        <i class="bi bi-eye"></i>
                    </button>`;
                }

                if (actions.edit) {
                    html += `<button class="action-icon edit-btn${btnSuffix}" data-id="${id}" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>`;
                }

                if (actions.delete) {
                    const name = row.name || row.title || '';
                    html += `<button class="action-icon danger delete-btn${btnSuffix}" data-id="${id}" data-name="${SBR.utils.escapeHtml(name)}" title="Delete">
                        <i class="bi bi-trash3"></i>
                    </button>`;
                }

                if (actions.custom && typeof actions.custom === 'function') {
                    html += actions.custom(row);
                }

                return html;
            },

            /**
             * Render status badge
             * @param {boolean|number} isActive - Status value
             * @param {string} activeText - Text for active status
             * @param {string} inactiveText - Text for inactive status
             * @returns {string} HTML badge
             */
            renderStatus: function(isActive, activeText = 'Active', inactiveText = 'Inactive') {
                return isActive
                    ? `<span class="badge badge-active">${activeText}</span>`
                    : `<span class="badge badge-inactive">${inactiveText}</span>`;
            },

            /**
             * Render date in formatted string
             * @param {string} dateString - ISO date string
             * @param {object} options - Intl.DateTimeFormat options
             * @returns {string} Formatted date string
             */
            renderDate: function(dateString, options = { month: 'short', day: 'numeric', year: 'numeric' }) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleDateString('en-US', options);
            },

            /**
             * Render datetime in formatted string
             * @param {string} dateString - ISO date string
             * @returns {string} Formatted datetime string
             */
            renderDateTime: function(dateString) {
                if (!dateString) return '-';
                return new Date(dateString).toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            /**
             * Render truncated text with tooltip
             * @param {string} text - Text to truncate
             * @param {number} maxLength - Maximum length
             * @returns {string} HTML with truncated text
             */
            renderTruncated: function(text, maxLength = 50) {
                if (!text) return '-';
                if (text.length <= maxLength) return SBR.utils.escapeHtml(text);

                const truncated = text.substring(0, maxLength);
                return `<span title="${SBR.utils.escapeHtml(text)}">${SBR.utils.escapeHtml(truncated)}...</span>`;
            },

            /**
             * Render badges from array
             * @param {array} items - Array of items with name property
             * @param {string} badgeClass - CSS class for badge
             * @returns {string} HTML badges
             */
            renderBadges: function(items, badgeClass = 'badge-role') {
                if (!items || items.length === 0) {
                    return '<span class="text-muted">None</span>';
                }
                return items.map(item =>
                    `<span class="badge ${badgeClass}">${SBR.utils.escapeHtml(item.name || item)}</span>`
                ).join('');
            },

            /**
             * Render user info cell with avatar
             * @param {object} data - User data with name and username
             * @returns {string} HTML for user cell
             */
            renderUserCell: function(data) {
                const name = data.name || 'Unknown';
                const email = data.email || '-';
                const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) || '?';

                return `
                    <div class="user-info">
                        <div class="user-avatar">${initials}</div>
                        <div class="user-details">
                            <div class="user-name">${SBR.utils.escapeHtml(name)}</div>
                            <div class="user-email">${SBR.utils.escapeHtml(email)}</div>
                        </div>
                    </div>
                `;
            }
        },

        /**
         * ==========================================================================
         * Modal Utilities
         * ==========================================================================
         */
        modal: {
            /**
             * Show a Bootstrap modal
             * @param {string} modalId - Modal element ID
             */
            show: function(modalId) {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modal.show();
                }
            },

            /**
             * Hide a Bootstrap modal
             * @param {string} modalId - Modal element ID
             */
            hide: function(modalId) {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    const modal = bootstrap.Modal.getInstance(modalElement);
                    if (modal) modal.hide();
                }
            },

            /**
             * Setup confirm delete modal
             * @param {object} config - Configuration object
             */
            setupDelete: function(config) {
                const {
                    tableId,
                    modalId,
                    confirmBtnId,
                    deleteUrl,
                    onSuccess,
                    onError
                } = config;

                const table = document.getElementById(tableId);
                if (!table) return;

                // Show delete modal on click
                $(table).on('click', '.delete-btn', function() {
                    const id = $(this).data('id');
                    const name = $(this).data('name');

                    $(`#${modalId}`).find('input[type="hidden"]').val(id);
                    $(`#${modalId}`).find('strong').text(name);

                    SBR.modal.show(modalId);
                });

                // Confirm delete
                $(`#${confirmBtnId}`).on('click', function() {
                    const id = $(`#${modalId}`).find('input[type="hidden"]').val();
                    const btn = $(this);

                    SBR.ui.toggleLoading(confirmBtnId, true);

                    SBR.crud.delete(
                        deleteUrl.replace(':id', id),
                        function(response) {
                            SBR.modal.hide(modalId);
                            SBR.ui.toggleLoading(confirmBtnId, false);
                            if (onSuccess) onSuccess(response);
                        },
                        function(message) {
                            SBR.modal.hide(modalId);
                            SBR.ui.toggleLoading(confirmBtnId, false);
                            if (onError) onError(message);
                        }
                    );
                });
            }
        },

        /**
         * ==========================================================================
         * UI Utilities
         * ==========================================================================
         */
        ui: {
            /**
             * Toggle loading state on a button
             * @param {string} btnId - Button element ID
             * @param {boolean} loading - Loading state
             */
            toggleLoading: function(btnId, loading) {
                const btn = document.getElementById(btnId);
                if (!btn) return;

                btn.disabled = loading;
                const spinner = btn.querySelector('.spinner-border');
                if (spinner) {
                    spinner.classList.toggle('d-none', !loading);
                }
            },

            /**
             * Show/hide element
             * @param {string} elementId - Element ID
             * @param {boolean} show - Show or hide
             */
            toggle: function(elementId, show) {
                const element = document.getElementById(elementId);
                if (element) {
                    element.style.display = show ? '' : 'none';
                }
            },

            /**
             * Confirm action with browser dialog
             * @param {string} message - Confirmation message
             * @returns {boolean} User confirmation
             */
            confirm: function(message) {
                return window.confirm(message);
            },

            /**
             * Scroll to element
             * @param {string} elementId - Element ID
             * @param {string} behavior - Scroll behavior ('smooth' or 'auto')
             */
            scrollTo: function(elementId, behavior = 'smooth') {
                const element = document.getElementById(elementId);
                if (element) {
                    element.scrollIntoView({ behavior: behavior, block: 'start' });
                }
            }
        },

        /**
         * ==========================================================================
         * Utility Helpers
         * ==========================================================================
         */
        utils: {
            /**
             * Debounce function execution
             * @param {function} func - Function to debounce
             * @param {number} wait - Wait time in milliseconds
             * @returns {function} Debounced function
             */
            debounce: function(func, wait = 300) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            },

            /**
             * Format number with commas
             * @param {number} num - Number to format
             * @returns {string} Formatted number
             */
            formatNumber: function(num) {
                return num?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') || '0';
            },

            /**
             * Format currency
             * @param {number} amount - Amount to format
             * @param {string} currency - Currency code
             * @returns {string} Formatted currency
             */
            formatCurrency: function(amount, currency = 'PHP') {
                return new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: currency
                }).format(amount || 0);
            },

            /**
             * Format TIN
             * @param {string} number - Number to format
             * @param {string} separator - String separator
             * @returns {string} Formatted TIN
             */
            formatTinNo: function(number, separator = '-') {
                let value = number.replace(/\D/g, '');
                value = value.substring(0, 14);
                // Apply mask XXX-XXX-XXX-XXXXX
                let maskedValue = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 3 === 0 && i < 10) maskedValue += separator;
                    maskedValue += value[i];
                }
                return maskedValue;
            },

            /**
             * Format cellphone number
             * @param {string} number - Number to format
             * @param {string} separator - String separator
             * @returns {string} Formatted cellphone number
             */
            formatCellphoneNo: function(number, separator = '-') {
                let value = number.replace('+639', '09');
                let match = value.match(/^(\d{4})(\d{3})(\d{4})$/);
                if (match) {
                    match.shift();
                    // Apply mask 09XX-XXX-XXXX
                    return match.join(separator);
                }
                return number;
            },

            /**
             * Generate initials from name
             * @param {string} name - Full name
             * @returns {string} Initials (max 2 characters)
             */
            getInitials: function(name) {
                if (!name) return '?';
                return name.split(' ')
                    .map(n => n[0])
                    .join('')
                    .toUpperCase()
                    .substring(0, 2);
            },

            /**
             * Check if value is empty
             * @param {*} value - Value to check
             * @returns {boolean} Is empty
             */
            isEmpty: function(value) {
                return value === null ||
                       value === undefined ||
                       value === '' ||
                       (Array.isArray(value) && value.length === 0) ||
                       (typeof value === 'object' && Object.keys(value).length === 0);
            },

            /**
             * Escape HTML to prevent XSS
             * @param {string} text - Text to escape
             * @returns {string} Escaped HTML
             */
            escapeHtml: function(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => SBR.init());
    } else {
        SBR.init();
    }

    // Export to global namespace
    global.SBR = SBR;

})(window);
