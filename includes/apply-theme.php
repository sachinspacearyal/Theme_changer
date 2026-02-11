<?php
/**
 * Theme Application Logic
 * 
 * This file handles applying the selected theme (default or custom)
 * by injecting CSS into the page.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Apply the currently active theme
 * Generates and injects CSS custom properties
 */
function theme_changer_apply_theme() {
    $active_theme_option = get_option('theme_changer_active_theme');
    
    if (!$active_theme_option) {
        // No active theme, use default
        $active_theme_option = array(
            'type' => 'default',
            'id' => 'default-dark',
            'mode' => 'auto'
        );
    }
    
    $theme = theme_changer_get_active_theme_data($active_theme_option);
    
    if (!$theme) {
        return; // No valid theme found
    }
    
    // Generate CSS
    $css = theme_changer_generate_theme_css($theme, $active_theme_option['mode']);
    
    // Output inline styles
    echo '<style id="theme-changer-theme-styles">' . "\n";
    echo esc_html(wp_strip_all_tags($css));
    echo "\n" . '</style>' . "\n";
}

/**
 * Get the active theme data
 * 
 * @param array $active_theme_option Active theme option from database
 * @return array|null Theme data or null if not found
 */
function theme_changer_get_active_theme_data($active_theme_option) {
    $type = $active_theme_option['type'];
    $id = $active_theme_option['id'];
    
    if ($type === 'custom') {
        return theme_changer_get_custom_theme($id);
    } else {
        return theme_changer_get_default_theme($id);
    }
}

/**
 * Generate CSS from theme data
 * 
 * @param array $theme Theme configuration
 * @param string $mode Current mode (dark/light/auto)
 * @return string Generated CSS
 */
function theme_changer_generate_theme_css($theme, $mode) {
    $colors = $theme['colors'];
    
    $css = '';
    
    if ($mode === 'auto') {
        // Generate CSS for auto mode with media queries
        $css .= ":root {\n";
        $css .= theme_changer_generate_css_variables($colors);
        $css .= "}\n\n";
        
        // Add light mode overrides if available
        $light_theme = theme_changer_get_default_theme('default-light');
        if ($light_theme) {
            $css .= "@media (prefers-color-scheme: light) {\n";
            $css .= "  :root {\n";
            $css .= theme_changer_generate_css_variables($light_theme['colors'], '    ');
            $css .= "  }\n";
            $css .= "}\n";
        }
    } else {
        // Generate CSS for specific mode
        $css .= ":root {\n";
        $css .= theme_changer_generate_css_variables($colors);
        $css .= "}\n";
    }
    
    // Apply colors to body for immediate effect
    $css .= "\nbody {\n";
    $css .= "  background-color: var(--theme-changer-background) !important;\n";
    $css .= "  color: var(--theme-changer-text) !important;\n";
    $css .= "}\n";
    
    return $css;
}

/**
 * Generate CSS custom properties from color array
 * 
 * @param array $colors Array of colors
 * @param string $indent Indentation string
 * @return string CSS custom properties
 */
function theme_changer_generate_css_variables($colors, $indent = '  ') {
    $css = '';
    
    foreach ($colors as $key => $value) {
        $css .= $indent . '--theme-changer-' . $key . ': ' . $value . ";\n";
    }
    
    return $css;
}

/**
 * Get current theme info for JavaScript
 * This is used to sync the frontend theme switcher
 * 
 * @return array Current theme info
 */
function theme_changer_get_current_theme_info() {
    $active_theme_option = get_option('theme_changer_active_theme');
    $theme_data = theme_changer_get_active_theme_data($active_theme_option);
    
    return array(
        'type' => $active_theme_option['type'],
        'id' => $active_theme_option['id'],
        'mode' => $active_theme_option['mode'],
        'name' => $theme_data ? $theme_data['name'] : 'Unknown',
        'colors' => $theme_data ? $theme_data['colors'] : array()
    );
}
