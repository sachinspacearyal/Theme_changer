<?php
/**
 * Plugin Name: theme-changer
 * Description: A WordPress plugin that provides default and custom dark/light theme functionality with automatic mode detection and user-customizable color schemes.
 * Version: 1.0.0
 * Author: Sachin Aryal 
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('THEME_CHANGER_VERSION', '1.0.0');
define('THEME_CHANGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('THEME_CHANGER_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Include required files
 */
require_once THEME_CHANGER_PLUGIN_DIR . 'includes/default-themes.php';
require_once THEME_CHANGER_PLUGIN_DIR . 'includes/custom-themes.php';
require_once THEME_CHANGER_PLUGIN_DIR . 'includes/apply-theme.php';

/**
 * Plugin activation hook
 */
function theme_changer_activate() {
    // Set default options on activation
    if (!get_option('theme_changer_active_theme')) {
        update_option('theme_changer_active_theme', array(
            'type' => 'default',
            'id' => 'default-dark',
            'mode' => 'auto'
        ));
    }
    
    // Initialize custom themes option if it doesn't exist
    if (!get_option('theme_changer_custom_themes')) {
        update_option('theme_changer_custom_themes', array());
    }
}
register_activation_hook(__FILE__, 'theme_changer_activate');

/**
 * Plugin deactivation hook
 */
function theme_changer_deactivate() {
    // Clean up if needed (optional)
    // We'll keep the options for now so users don't lose their themes
}
register_deactivation_hook(__FILE__, 'theme_changer_deactivate');

/**
 * Enqueue frontend styles and scripts
 */
function theme_changer_enqueue_assets() {
    // Enqueue base CSS
    wp_enqueue_style(
        'theme-changer-style',
        THEME_CHANGER_PLUGIN_URL . 'assets/css/style.css',
        array(),
        THEME_CHANGER_VERSION
    );
    
    // Enqueue theme switcher script
    wp_enqueue_script(
        'theme-changer-theme-switcher',
        THEME_CHANGER_PLUGIN_URL . 'assets/js/theme-switcher.js',
        array('jquery'),
        THEME_CHANGER_VERSION,
        true
    );
    
    // Enqueue color picker script (only on admin page)
    if (is_admin() && filter_input(INPUT_GET, 'page') === 'theme-changer-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script(
            'theme-changer-color-picker',
            THEME_CHANGER_PLUGIN_URL . 'assets/js/color-picker.js',
            array('jquery', 'wp-color-picker'),
            THEME_CHANGER_VERSION,
            true
        );
    }
    
    // Pass AJAX URL, nonce, and theme data to JavaScript
    wp_localize_script('theme-changer-theme-switcher', 'themeChangerAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('theme_changer_nonce'),
        'defaultThemes' => theme_changer_get_default_themes(),
        'customThemes' => theme_changer_get_custom_themes(),
        'currentTheme' => theme_changer_get_current_theme_info()
    ));
}
add_action('wp_enqueue_scripts', 'theme_changer_enqueue_assets');
add_action('admin_enqueue_scripts', 'theme_changer_enqueue_assets');

/**
 * Apply the active theme
 */
function theme_changer_apply_active_theme() {
    theme_changer_apply_theme();
}
add_action('wp_head', 'theme_changer_apply_active_theme');
add_action('admin_head', 'theme_changer_apply_active_theme');

/**
 * Register AJAX handlers for saving theme preferences
 */
function theme_changer_save_theme_preference() {
    check_ajax_referer('theme_changer_nonce', 'nonce');
    
    if (!isset($_POST['theme_type'], $_POST['theme_id'], $_POST['mode'])) {
        wp_send_json_error(array('message' => 'Missing required data'));
    }

    $theme_type = sanitize_text_field(wp_unslash($_POST['theme_type']));
    $theme_id = sanitize_text_field(wp_unslash($_POST['theme_id']));
    $mode = sanitize_text_field(wp_unslash($_POST['mode']));
    
    $active_theme = array(
        'type' => $theme_type,
        'id' => $theme_id,
        'mode' => $mode
    );
    
    update_option('theme_changer_active_theme', $active_theme);
    
    wp_send_json_success(array('message' => 'Theme preference saved successfully'));
}
add_action('wp_ajax_theme_changer_save_theme', 'theme_changer_save_theme_preference');
add_action('wp_ajax_nopriv_theme_changer_save_theme', 'theme_changer_save_theme_preference');

/**
 * AJAX handler for saving custom themes
 */
function theme_changer_save_custom_theme() {
    check_ajax_referer('theme_changer_nonce', 'nonce');
    
    if (!isset($_POST['theme_name'], $_POST['theme_colors'], $_POST['theme_mode'])) {
        wp_send_json_error(array('message' => 'Missing required data'));
    }

    $theme_name = sanitize_text_field(wp_unslash($_POST['theme_name']));
    $raw_theme_colors = map_deep(wp_unslash($_POST['theme_colors']), 'sanitize_hex_color'); 
    $theme_mode = sanitize_text_field(wp_unslash($_POST['theme_mode']));

    if (!is_array($raw_theme_colors)) {
        wp_send_json_error(array('message' => 'Invalid color data'));
    }
    
    // Sanitize colors
    $sanitized_colors = array();
    foreach ($raw_theme_colors as $key => $color) {
        $sanitized_colors[sanitize_key($key)] = sanitize_hex_color($color);
    }
    
    $result = theme_changer_add_custom_theme($theme_name, $sanitized_colors, $theme_mode);
    
    if ($result) {
        wp_send_json_success(array('message' => 'Custom theme saved successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to save custom theme'));
    }
}
add_action('wp_ajax_theme_changer_save_custom_theme', 'theme_changer_save_custom_theme');

/**
 * AJAX handler for deleting custom themes
 */
function theme_changer_delete_custom_theme() {
    check_ajax_referer('theme_changer_nonce', 'nonce');
    
    if (!isset($_POST['theme_id'])) {
        wp_send_json_error(array('message' => 'Missing theme ID'));
    }

    $theme_id = sanitize_text_field(wp_unslash($_POST['theme_id']));
    
    $result = theme_changer_remove_custom_theme($theme_id);
    
    if ($result) {
        wp_send_json_success(array('message' => 'Custom theme deleted successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to delete custom theme'));
    }
}
add_action('wp_ajax_theme_changer_delete_custom_theme', 'theme_changer_delete_custom_theme');

/**
 * Add admin menu page
 */
function theme_changer_add_admin_menu() {
    add_menu_page(
        'Theme Changer Settings',
        'Theme Changer',
        'manage_options',
        'theme-changer-settings',
        'theme_changer_render_admin_page',
        'dashicons-admin-customizer',
        30
    );
}
add_action('admin_menu', 'theme_changer_add_admin_menu');

/**
 * Render admin page
 */
function theme_changer_render_admin_page() {
    require_once THEME_CHANGER_PLUGIN_DIR . 'frontend/admin-page.php';
}

/**
 * Shortcode to display theme switcher anywhere on the site
 * Usage: [theme_changer_theme_switcher]
 */
function theme_changer_theme_switcher_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'floating', // floating or inline
    ), $atts);
    
    ob_start();
    
    if ($atts['style'] === 'inline') {
        // Inline style - embedded in content
        ?>
        <div class="theme-changer-theme-switcher-inline" style="margin: 20px 0;">
            <button class="theme-changer-theme-toggle-btn-inline" style="
                background-color: var(--theme-changer-primary);
                color: #ffffff;
                border: none;
                border-radius: 8px;
                padding: 12px 24px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            " onclick="jQuery('.theme-changer-theme-panel').toggleClass('active');">
                🎨 Change Theme
            </button>
        </div>
        <?php
    } else {
        // Default floating style is already created by JavaScript
        echo '<!-- Theme Changer theme switcher widget is automatically displayed -->';
    }
    
    return ob_get_clean();
}
add_shortcode('theme_changer_theme_switcher', 'theme_changer_theme_switcher_shortcode');

