/**
 * Theme Switcher JavaScript
 * 
 * Handles theme switching functionality, mode detection,
 * and real-time theme application.
 */

(function ($) {
    'use strict';

    // Theme Switcher Class
    class DarkupThemeSwitcher {
        constructor() {
            this.currentMode = 'auto';
            this.currentTheme = null;
            this.init();
        }

        init() {
            // Load saved preferences
            this.loadPreferences();

            // Detect system theme preference
            this.detectSystemTheme();

            // Create theme switcher widget (frontend only)
            if (!document.body.classList.contains('wp-admin')) {
                this.createWidget();
            }

            // Bind events
            this.bindEvents();
        }

        loadPreferences() {
            // Prefer server-side data if available
            if (typeof darkupAjax !== 'undefined' && darkupAjax.currentTheme) {
                this.currentMode = darkupAjax.currentTheme.mode || 'auto';
                this.currentTheme = {
                    id: darkupAjax.currentTheme.id,
                    type: darkupAjax.currentTheme.type
                };

                // Sync to localStorage
                localStorage.setItem('darkup_mode', this.currentMode);
                localStorage.setItem('darkup_theme', JSON.stringify(this.currentTheme));
            } else {
                // Fallback to localStorage
                const savedMode = localStorage.getItem('darkup_mode');
                const savedTheme = localStorage.getItem('darkup_theme');

                if (savedMode) {
                    this.currentMode = savedMode;
                }

                if (savedTheme) {
                    try {
                        this.currentTheme = JSON.parse(savedTheme);
                    } catch (e) {
                        console.error('Failed to parse saved theme:', e);
                    }
                }
            }
        }

        detectSystemTheme() {
            if (this.currentMode === 'auto') {
                // Listen for system theme changes
                if (window.matchMedia) {
                    const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

                    darkModeQuery.addEventListener('change', (e) => {
                        if (this.currentMode === 'auto') {
                            this.applyAutoTheme(e.matches);
                        }
                    });
                }
            }
        }

        applyAutoTheme(isDark) {
            // This is handled by CSS media queries
            // We just need to ensure we're in auto mode
            console.log('System theme changed to:', isDark ? 'dark' : 'light');
        }

        createWidget() {
            const widget = `
                <div class="darkup-theme-switcher">
                    <button class="darkup-theme-toggle-btn" aria-label="Toggle theme">
                        <span class="dashicons dashicons-admin-appearance"></span>
                    </button>
                    <div class="darkup-theme-panel">
                        <h3>Theme Settings</h3>
                        <div class="darkup-mode-selector">
                            <button class="darkup-mode-btn" data-mode="auto">Auto</button>
                            <button class="darkup-mode-btn" data-mode="light">Light</button>
                            <button class="darkup-mode-btn" data-mode="dark">Dark</button>
                        </div>
                        <div class="darkup-theme-list" id="darkup-theme-list">
                            <!-- Themes will be loaded here -->
                        </div>
                    </div>
                </div>
            `;

            $('body').append(widget);
            this.loadThemes();
        }

        loadThemes() {
            const themeList = $('#darkup-theme-list');

            if (typeof darkupAjax === 'undefined' || !darkupAjax.defaultThemes) {
                themeList.html('<p style="color: var(--darkup-text-secondary); font-size: 14px;">No themes available</p>');
                return;
            }

            let html = '';

            // Add default themes
            if (darkupAjax.defaultThemes) {
                html += '<h4 style="color: var(--darkup-text); font-size: 14px; margin: 15px 0 10px 0;">Default Themes</h4>';

                Object.values(darkupAjax.defaultThemes).forEach(theme => {
                    const isActive = darkupAjax.currentTheme && darkupAjax.currentTheme.id === theme.id;
                    const activeClass = isActive ? 'active' : '';

                    // Get first 5 colors for preview
                    const colors = Object.values(theme.colors).slice(0, 5);
                    const colorDots = colors.map(color =>
                        `<div class="darkup-color-dot" style="background-color: ${color};"></div>`
                    ).join('');

                    html += `
                        <div class="darkup-theme-option ${activeClass}" 
                             data-theme-id="${theme.id}" 
                             data-theme-type="default">
                            <div class="darkup-theme-option-name">${theme.name}</div>
                            <div class="darkup-theme-option-colors">${colorDots}</div>
                        </div>
                    `;
                });
            }

            // Add custom themes
            if (darkupAjax.customThemes && Object.keys(darkupAjax.customThemes).length > 0) {
                html += '<h4 style="color: var(--darkup-text); font-size: 14px; margin: 15px 0 10px 0;">Custom Themes</h4>';

                Object.values(darkupAjax.customThemes).forEach(theme => {
                    const isActive = darkupAjax.currentTheme && darkupAjax.currentTheme.id === theme.id;
                    const activeClass = isActive ? 'active' : '';

                    // Get first 5 colors for preview
                    const colors = Object.values(theme.colors).slice(0, 5);
                    const colorDots = colors.map(color =>
                        `<div class="darkup-color-dot" style="background-color: ${color};"></div>`
                    ).join('');

                    html += `
                        <div class="darkup-theme-option ${activeClass}" 
                             data-theme-id="${theme.id}" 
                             data-theme-type="custom">
                            <div class="darkup-theme-option-name">${theme.name}</div>
                            <div class="darkup-theme-option-colors">${colorDots}</div>
                        </div>
                    `;
                });
            }

            if (!html) {
                html = '<p style="color: var(--darkup-text-secondary); font-size: 14px;">No themes available</p>';
            }

            themeList.html(html);
        }

        bindEvents() {
            const self = this;

            // Toggle theme panel
            $(document).on('click', '.darkup-theme-toggle-btn', function (e) {
                e.stopPropagation();
                $('.darkup-theme-panel').toggleClass('active');
            });

            // Close panel when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.darkup-theme-switcher').length) {
                    $('.darkup-theme-panel').removeClass('active');
                }
            });

            // Mode selection
            $(document).on('click', '.darkup-mode-btn', function () {
                const mode = $(this).data('mode');
                self.changeMode(mode);
            });

            // Theme selection
            $(document).on('click', '.darkup-theme-option', function () {
                const themeId = $(this).data('theme-id');
                const themeType = $(this).data('theme-type');
                self.selectTheme(themeId, themeType);
            });

            // Set initial active states
            this.updateActiveStates();
        }

        changeMode(mode) {
            this.currentMode = mode;
            localStorage.setItem('darkup_mode', mode);

            // Save to server
            this.saveThemePreference();

            // Update UI
            this.updateActiveStates();
        }

        selectTheme(themeId, themeType) {
            this.currentTheme = {
                id: themeId,
                type: themeType
            };

            localStorage.setItem('darkup_theme', JSON.stringify(this.currentTheme));

            // Save to server
            this.saveThemePreference();

            // Update UI
            this.updateActiveStates();

            // Reload page to apply theme (in production, this would be done via AJAX)
            location.reload();
        }

        saveThemePreference() {
            if (typeof darkupAjax === 'undefined') {
                console.warn('darkupAjax not defined');
                return;
            }

            $.ajax({
                url: darkupAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'darkup_save_theme',
                    nonce: darkupAjax.nonce,
                    theme_type: this.currentTheme ? this.currentTheme.type : 'default',
                    theme_id: this.currentTheme ? this.currentTheme.id : 'default-dark',
                    mode: this.currentMode
                },
                success: function (response) {
                    console.log('Theme preference saved:', response);
                },
                error: function (error) {
                    console.error('Failed to save theme preference:', error);
                }
            });
        }

        updateActiveStates() {
            // Update mode buttons
            $('.darkup-mode-btn').removeClass('active');
            $(`.darkup-mode-btn[data-mode="${this.currentMode}"]`).addClass('active');

            // Update theme options
            $('.darkup-theme-option').removeClass('active');
            if (this.currentTheme) {
                $(`.darkup-theme-option[data-theme-id="${this.currentTheme.id}"]`).addClass('active');
            }
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function () {
        new DarkupThemeSwitcher();
    });

})(jQuery);
