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
function darkup_apply_theme() {
    $active_theme_option = get_option('darkup_active_theme');
    
    if (!$active_theme_option) {
        // No active theme, use default
        $active_theme_option = array(
            'type' => 'default',
            'id' => 'default-dark',
            'mode' => 'auto'
        );
    }
    
    $theme = darkup_get_active_theme_data($active_theme_option);
    
    if (!$theme) {
        return; // No valid theme found
    }
    
    // Generate CSS
    $css = darkup_generate_theme_css($theme, $active_theme_option['mode']);
    
    // Output inline styles
    echo '<style id="darkup-theme-styles">' . "\n";
    echo $css;
    echo "\n" . '</style>' . "\n";
}

/**
 * Get the active theme data
 * 
 * @param array $active_theme_option Active theme option from database
 * @return array|null Theme data or null if not found
 */
function darkup_get_active_theme_data($active_theme_option) {
    $type = $active_theme_option['type'];
    $id = $active_theme_option['id'];
    
    if ($type === 'custom') {
        return darkup_get_custom_theme($id);
    } else {
        return darkup_get_default_theme($id);
    }
}

/**
 * Generate CSS from theme data
 * 
 * @param array $theme Theme configuration
 * @param string $mode Current mode (dark/light/auto)
 * @return string Generated CSS
 */
function darkup_generate_theme_css($theme, $mode) {
    $colors = $theme['colors'];
    
    $css = '';
    
    if ($mode === 'auto') {
        // Generate CSS for auto mode with media queries
        $css .= ":root {\n";
        $css .= darkup_generate_css_variables($colors);
        $css .= "}\n\n";
        
        // Add light mode overrides if available
        $light_theme = darkup_get_default_theme('default-light');
        if ($light_theme) {
            $css .= "@media (prefers-color-scheme: light) {\n";
            $css .= "  :root {\n";
            $css .= darkup_generate_css_variables($light_theme['colors'], '    ');
            $css .= "  }\n";
            $css .= "}\n";
        }
    } else {
        // Generate CSS for specific mode
        $css .= ":root {\n";
        $css .= darkup_generate_css_variables($colors);
        $css .= "}\n";
    }
    
    // Apply colors to body for immediate effect
    $css .= "\nbody {\n";
    $css .= "  background-color: var(--darkup-background) !important;\n";
    $css .= "  color: var(--darkup-text) !important;\n";
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
function darkup_generate_css_variables($colors, $indent = '  ') {
    $css = '';
    
    foreach ($colors as $key => $value) {
        $css .= $indent . '--darkup-' . $key . ': ' . $value . ";\n";
    }
    
    return $css;
}

/**
 * Get current theme info for JavaScript
 * This is used to sync the frontend theme switcher
 * 
 * @return array Current theme info
 */
function darkup_get_current_theme_info() {
    $active_theme_option = get_option('darkup_active_theme');
    $theme_data = darkup_get_active_theme_data($active_theme_option);
    
    return array(
        'type' => $active_theme_option['type'],
        'id' => $active_theme_option['id'],
        'mode' => $active_theme_option['mode'],
        'name' => $theme_data ? $theme_data['name'] : 'Unknown',
        'colors' => $theme_data ? $theme_data['colors'] : array()
    );
}
