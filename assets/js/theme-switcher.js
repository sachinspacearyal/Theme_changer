/**
 * Theme Switcher JavaScript
 * 
 * Handles theme switching functionality, mode detection,
 * and real-time theme application.
 */

(function ($) {
    'use strict';

    // Theme Switcher Class
    class ThemeChangerThemeSwitcher {
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
            if (typeof themeChangerAjax !== 'undefined' && themeChangerAjax.currentTheme) {
                this.currentMode = themeChangerAjax.currentTheme.mode || 'auto';
                this.currentTheme = {
                    id: themeChangerAjax.currentTheme.id,
                    type: themeChangerAjax.currentTheme.type
                };

                // Sync to localStorage
                localStorage.setItem('theme_changer_mode', this.currentMode);
                localStorage.setItem('theme_changer_theme', JSON.stringify(this.currentTheme));
            } else {
                // Fallback to localStorage
                const savedMode = localStorage.getItem('theme_changer_mode');
                const savedTheme = localStorage.getItem('theme_changer_theme');

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
                <div class="theme-changer-theme-switcher">
                    <button class="theme-changer-theme-toggle-btn" aria-label="Toggle theme">
                        <span class="dashicons dashicons-admin-appearance"></span>
                    </button>
                    <div class="theme-changer-theme-panel">
                        <h3>Theme Settings</h3>
                        <div class="theme-changer-mode-selector">
                            <button class="theme-changer-mode-btn" data-mode="auto">Auto</button>
                            <button class="theme-changer-mode-btn" data-mode="light">Light</button>
                            <button class="theme-changer-mode-btn" data-mode="dark">Dark</button>
                        </div>
                        <div class="theme-changer-theme-list" id="theme-changer-theme-list">
                            <!-- Themes will be loaded here -->
                        </div>
                    </div>
                </div>
            `;

            $('body').append(widget);
            this.loadThemes();
        }

        loadThemes() {
            const themeList = $('#theme-changer-theme-list');

            if (typeof themeChangerAjax === 'undefined' || !themeChangerAjax.defaultThemes) {
                themeList.html('<p style="color: var(--theme-changer-text-secondary); font-size: 14px;">No themes available</p>');
                return;
            }

            let html = '';

            // Add default themes
            if (themeChangerAjax.defaultThemes) {
                html += '<h4 style="color: var(--theme-changer-text); font-size: 14px; margin: 15px 0 10px 0;">Default Themes</h4>';

                Object.values(themeChangerAjax.defaultThemes).forEach(theme => {
                    const isActive = themeChangerAjax.currentTheme && themeChangerAjax.currentTheme.id === theme.id;
                    const activeClass = isActive ? 'active' : '';

                    // Get first 5 colors for preview
                    const colors = Object.values(theme.colors).slice(0, 5);
                    const colorDots = colors.map(color =>
                        `<div class="theme-changer-color-dot" style="background-color: ${color};"></div>`
                    ).join('');

                    html += `
                        <div class="theme-changer-theme-option ${activeClass}" 
                             data-theme-id="${theme.id}" 
                             data-theme-type="default">
                            <div class="theme-changer-theme-option-name">${theme.name}</div>
                            <div class="theme-changer-theme-option-colors">${colorDots}</div>
                        </div>
                    `;
                });
            }

            // Add custom themes
            if (themeChangerAjax.customThemes && Object.keys(themeChangerAjax.customThemes).length > 0) {
                html += '<h4 style="color: var(--theme-changer-text); font-size: 14px; margin: 15px 0 10px 0;">Custom Themes</h4>';

                Object.values(themeChangerAjax.customThemes).forEach(theme => {
                    const isActive = themeChangerAjax.currentTheme && themeChangerAjax.currentTheme.id === theme.id;
                    const activeClass = isActive ? 'active' : '';

                    // Get first 5 colors for preview
                    const colors = Object.values(theme.colors).slice(0, 5);
                    const colorDots = colors.map(color =>
                        `<div class="theme-changer-color-dot" style="background-color: ${color};"></div>`
                    ).join('');

                    html += `
                        <div class="theme-changer-theme-option ${activeClass}" 
                             data-theme-id="${theme.id}" 
                             data-theme-type="custom">
                            <div class="theme-changer-theme-option-name">${theme.name}</div>
                            <div class="theme-changer-theme-option-colors">${colorDots}</div>
                        </div>
                    `;
                });
            }

            if (!html) {
                html = '<p style="color: var(--theme-changer-text-secondary); font-size: 14px;">No themes available</p>';
            }

            themeList.html(html);
        }

        bindEvents() {
            const self = this;

            // Toggle theme panel
            $(document).on('click', '.theme-changer-theme-toggle-btn', function (e) {
                e.stopPropagation();
                $('.theme-changer-theme-panel').toggleClass('active');
            });

            // Close panel when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.theme-changer-theme-switcher').length) {
                    $('.theme-changer-theme-panel').removeClass('active');
                }
            });

            // Mode selection
            $(document).on('click', '.theme-changer-mode-btn', function () {
                const mode = $(this).data('mode');
                self.changeMode(mode);
            });

            // Theme selection
            $(document).on('click', '.theme-changer-theme-option', function () {
                const themeId = $(this).data('theme-id');
                const themeType = $(this).data('theme-type');
                self.selectTheme(themeId, themeType);
            });

            // Set initial active states
            this.updateActiveStates();
        }

        changeMode(mode) {
            this.currentMode = mode;
            localStorage.setItem('theme_changer_mode', mode);

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

            localStorage.setItem('theme_changer_theme', JSON.stringify(this.currentTheme));

            // Update UI
            this.updateActiveStates();

            // Save to server and reload after successful save
            this.saveThemePreference(true);
        }

        saveThemePreference(reloadOnSuccess = false) {
            if (typeof themeChangerAjax === 'undefined') {
                console.warn('themeChangerAjax not defined');
                return;
            }

            $.ajax({
                url: themeChangerAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'theme_changer_save_theme',
                    nonce: themeChangerAjax.nonce,
                    theme_type: this.currentTheme ? this.currentTheme.type : 'default',
                    theme_id: this.currentTheme ? this.currentTheme.id : 'default-dark',
                    mode: this.currentMode
                },
                success: function (response) {
                    console.log('Theme preference saved:', response);
                    // Reload page only after successful save
                    if (reloadOnSuccess) {
                        location.reload();
                    }
                },
                error: function (error) {
                    console.error('Failed to save theme preference:', error);
                    // Still reload on error to attempt to apply theme
                    if (reloadOnSuccess) {
                        location.reload();
                    }
                }
            });
        }

        updateActiveStates() {
            // Update mode buttons
            $('.theme-changer-mode-btn').removeClass('active');
            $(`.theme-changer-mode-btn[data-mode="${this.currentMode}"]`).addClass('active');

            // Update theme options
            $('.theme-changer-theme-option').removeClass('active');
            if (this.currentTheme) {
                $(`.theme-changer-theme-option[data-theme-id="${this.currentTheme.id}"]`).addClass('active');
            }
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function () {
        new ThemeChangerThemeSwitcher();
    });

})(jQuery);
