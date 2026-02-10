/**
 * Color Picker JavaScript
 * 
 * Handles custom theme creation with color picker interface
 * and real-time preview functionality.
 */

(function ($) {
    'use strict';

    // Color Picker Manager
    class DarkupColorPicker {
        constructor() {
            this.colors = {};
            this.init();
        }

        init() {
            // Initialize color pickers when on admin page
            if ($('.darkup-color-picker').length) {
                this.initializeColorPickers();
                this.bindEvents();
            }
        }

        initializeColorPickers() {
            // Initialize WordPress color picker
            $('.darkup-color-picker').wpColorPicker({
                change: (event, ui) => {
                    const colorKey = $(event.target).data('color-key');
                    this.colors[colorKey] = ui.color.toString();
                    this.updatePreview();
                },
                clear: () => {
                    this.updatePreview();
                }
            });
        }

        bindEvents() {
            const self = this;

            // Save custom theme button
            $('#darkup-save-custom-theme').on('click', function (e) {
                e.preventDefault();
                self.saveCustomTheme();
            });

            // Delete custom theme button
            $(document).on('click', '.darkup-delete-theme', function (e) {
                e.preventDefault();
                const themeId = $(this).data('theme-id');
                self.deleteCustomTheme(themeId);
            });

            // Theme name input
            $('#darkup-theme-name').on('input', function () {
                self.validateForm();
            });

            // Mode selector for custom theme
            $('.darkup-custom-mode-btn').on('click', function () {
                $('.darkup-custom-mode-btn').removeClass('active');
                $(this).addClass('active');
            });
        }

        updatePreview() {
            // Update preview area with current colors
            const previewArea = $('#darkup-theme-preview');

            if (previewArea.length) {
                const cssVars = Object.keys(this.colors).map(key => {
                    return `--darkup-${key}: ${this.colors[key]};`;
                }).join('\n');

                previewArea.attr('style', cssVars);
            }
        }

        validateForm() {
            const themeName = $('#darkup-theme-name').val().trim();
            const hasColors = Object.keys(this.colors).length > 0;

            const saveButton = $('#darkup-save-custom-theme');

            if (themeName && hasColors) {
                saveButton.prop('disabled', false);
            } else {
                saveButton.prop('disabled', true);
            }
        }

        saveCustomTheme() {
            const themeName = $('#darkup-theme-name').val().trim();
            const themeMode = $('.darkup-custom-mode-btn.active').data('mode') || 'dark';

            // Collect all color values
            const colors = {};
            $('.darkup-color-picker').each(function () {
                const colorKey = $(this).data('color-key');
                const colorValue = $(this).val();
                if (colorValue) {
                    colors[colorKey] = colorValue;
                }
            });

            if (!themeName) {
                alert('Please enter a theme name');
                return;
            }

            if (Object.keys(colors).length === 0) {
                alert('Please select at least one color');
                return;
            }

            // Show loading state
            $('#darkup-save-custom-theme').prop('disabled', true).text('Saving...');

            // Send AJAX request
            $.ajax({
                url: darkupAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'darkup_save_custom_theme',
                    nonce: darkupAjax.nonce,
                    theme_name: themeName,
                    theme_colors: colors,
                    theme_mode: themeMode
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Custom theme saved successfully!', 'success');
                        // Reset form
                        this.resetForm();
                        // Reload page to show new theme
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        this.showNotice(response.data.message || 'Failed to save theme', 'error');
                    }
                },
                error: (error) => {
                    console.error('AJAX error:', error);
                    this.showNotice('An error occurred while saving the theme', 'error');
                },
                complete: () => {
                    $('#darkup-save-custom-theme').prop('disabled', false).text('Save Custom Theme');
                }
            });
        }

        deleteCustomTheme(themeId) {
            if (!confirm('Are you sure you want to delete this custom theme?')) {
                return;
            }

            $.ajax({
                url: darkupAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'darkup_delete_custom_theme',
                    nonce: darkupAjax.nonce,
                    theme_id: themeId
                },
                success: (response) => {
                    if (response.success) {
                        this.showNotice('Custom theme deleted successfully!', 'success');
                        // Remove theme card from DOM
                        $(`.darkup-theme-card[data-theme-id="${themeId}"]`).fadeOut(300, function () {
                            $(this).remove();
                        });
                    } else {
                        this.showNotice(response.data.message || 'Failed to delete theme', 'error');
                    }
                },
                error: (error) => {
                    console.error('AJAX error:', error);
                    this.showNotice('An error occurred while deleting the theme', 'error');
                }
            });
        }

        resetForm() {
            $('#darkup-theme-name').val('');
            $('.darkup-color-picker').val('');
            this.colors = {};
            this.updatePreview();
        }

        showNotice(message, type = 'info') {
            const noticeClass = type === 'success' ? 'success' : (type === 'error' ? 'error' : '');
            const notice = $(`
                <div class="darkup-notice ${noticeClass}" style="animation: slideDown 0.3s ease;">
                    ${message}
                </div>
            `);

            $('.darkup-admin-page').prepend(notice);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                notice.fadeOut(300, function () {
                    $(this).remove();
                });
            }, 5000);
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function () {
        new DarkupColorPicker();
    });

})(jQuery);
