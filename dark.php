<?php
/**
 * Plugin Name: Dark
 * Plugin URI: https://example.com/darkup
 * Description: A WordPress plugin that provides default and custom dark/light theme functionality with automatic mode detection and user-customizable color schemes.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: darkup
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('DARKUP_VERSION', '1.0.0');
define('DARKUP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('DARKUP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Include required files
 */
require_once DARKUP_PLUGIN_DIR . 'includes/default-themes.php';
require_once DARKUP_PLUGIN_DIR . 'includes/custom-themes.php';
require_once DARKUP_PLUGIN_DIR . 'includes/apply-theme.php';

/**
 * Plugin activation hook
 */
function darkup_activate() {
    // Set default options on activation
    if (!get_option('darkup_active_theme')) {
        update_option('darkup_active_theme', array(
            'type' => 'default',
            'id' => 'default-dark',
            'mode' => 'auto'
        ));
    }
    
    // Initialize custom themes option if it doesn't exist
    if (!get_option('darkup_custom_themes')) {
        update_option('darkup_custom_themes', array());
    }
}
register_activation_hook(__FILE__, 'darkup_activate');

/**
 * Plugin deactivation hook
 */
function darkup_deactivate() {
    // Clean up if needed (optional)
    // We'll keep the options for now so users don't lose their themes
}
register_deactivation_hook(__FILE__, 'darkup_deactivate');

/**
 * Enqueue frontend styles and scripts
 */
function darkup_enqueue_assets() {
    // Enqueue base CSS
    wp_enqueue_style(
        'darkup-style',
        DARKUP_PLUGIN_URL . 'assets/css/style.css',
        array(),
        DARKUP_VERSION
    );
    
    // Enqueue theme switcher script
    wp_enqueue_script(
        'darkup-theme-switcher',
        DARKUP_PLUGIN_URL . 'assets/js/theme-switcher.js',
        array('jquery'),
        DARKUP_VERSION,
        true
    );
    
    // Enqueue color picker script (only on admin page)
    if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'darkup-settings') {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script(
            'darkup-color-picker',
            DARKUP_PLUGIN_URL . 'assets/js/color-picker.js',
            array('jquery', 'wp-color-picker'),
            DARKUP_VERSION,
            true
        );
    }
    
    // Pass AJAX URL, nonce, and theme data to JavaScript
    wp_localize_script('darkup-theme-switcher', 'darkupAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('darkup_nonce'),
        'defaultThemes' => darkup_get_default_themes(),
        'customThemes' => darkup_get_custom_themes(),
        'currentTheme' => darkup_get_current_theme_info()
    ));
}
add_action('wp_enqueue_scripts', 'darkup_enqueue_assets');
add_action('admin_enqueue_scripts', 'darkup_enqueue_assets');

/**
 * Apply the active theme
 */
function darkup_apply_active_theme() {
    darkup_apply_theme();
}
add_action('wp_head', 'darkup_apply_active_theme');
add_action('admin_head', 'darkup_apply_active_theme');

/**
 * Register AJAX handlers for saving theme preferences
 */
function darkup_save_theme_preference() {
    check_ajax_referer('darkup_nonce', 'nonce');
    
    $theme_type = sanitize_text_field($_POST['theme_type']);
    $theme_id = sanitize_text_field($_POST['theme_id']);
    $mode = sanitize_text_field($_POST['mode']);
    
    $active_theme = array(
        'type' => $theme_type,
        'id' => $theme_id,
        'mode' => $mode
    );
    
    update_option('darkup_active_theme', $active_theme);
    
    wp_send_json_success(array('message' => 'Theme preference saved successfully'));
}
add_action('wp_ajax_darkup_save_theme', 'darkup_save_theme_preference');
add_action('wp_ajax_nopriv_darkup_save_theme', 'darkup_save_theme_preference');

/**
 * AJAX handler for saving custom themes
 */
function darkup_save_custom_theme() {
    check_ajax_referer('darkup_nonce', 'nonce');
    
    $theme_name = sanitize_text_field($_POST['theme_name']);
    $theme_colors = $_POST['theme_colors']; // Array of colors
    $theme_mode = sanitize_text_field($_POST['theme_mode']);
    
    // Sanitize colors
    $sanitized_colors = array();
    foreach ($theme_colors as $key => $color) {
        $sanitized_colors[sanitize_key($key)] = sanitize_hex_color($color);
    }
    
    $result = darkup_add_custom_theme($theme_name, $sanitized_colors, $theme_mode);
    
    if ($result) {
        wp_send_json_success(array('message' => 'Custom theme saved successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to save custom theme'));
    }
}
add_action('wp_ajax_darkup_save_custom_theme', 'darkup_save_custom_theme');

/**
 * AJAX handler for deleting custom themes
 */
function darkup_delete_custom_theme() {
    check_ajax_referer('darkup_nonce', 'nonce');
    
    $theme_id = sanitize_text_field($_POST['theme_id']);
    
    $result = darkup_remove_custom_theme($theme_id);
    
    if ($result) {
        wp_send_json_success(array('message' => 'Custom theme deleted successfully'));
    } else {
        wp_send_json_error(array('message' => 'Failed to delete custom theme'));
    }
}
add_action('wp_ajax_darkup_delete_custom_theme', 'darkup_delete_custom_theme');

/**
 * Add admin menu page
 */
function darkup_add_admin_menu() {
    add_menu_page(
        'Darkup Settings',
        'Darkup',
        'manage_options',
        'darkup-settings',
        'darkup_render_admin_page',
        'dashicons-admin-customizer',
        30
    );
}
add_action('admin_menu', 'darkup_add_admin_menu');

/**
 * Render admin page
 */
function darkup_render_admin_page() {
    require_once DARKUP_PLUGIN_DIR . 'frontend/admin-page.php';
}

/**
 * Shortcode to display theme switcher anywhere on the site
 * Usage: [darkup_theme_switcher]
 */
function darkup_theme_switcher_shortcode($atts) {
    $atts = shortcode_atts(array(
        'style' => 'floating', // floating or inline
    ), $atts);
    
    ob_start();
    
    if ($atts['style'] === 'inline') {
        // Inline style - embedded in content
        ?>
        <div class="darkup-theme-switcher-inline" style="margin: 20px 0;">
            <button class="darkup-theme-toggle-btn-inline" style="
                background-color: var(--darkup-primary);
                color: #ffffff;
                border: none;
                border-radius: 8px;
                padding: 12px 24px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            " onclick="jQuery('.darkup-theme-panel').toggleClass('active');">
                🎨 Change Theme
            </button>
        </div>
        <?php
    } else {
        // Default floating style is already created by JavaScript
        echo '<!-- Darkup theme switcher widget is automatically displayed -->';
    }
    
    return ob_get_clean();
}
add_shortcode('darkup_theme_switcher', 'darkup_theme_switcher_shortcode');

