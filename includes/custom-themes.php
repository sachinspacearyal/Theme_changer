<?php
/**
 * Custom Themes Management
 * 
 * This file handles custom user-created themes with database operations.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all custom themes
 * 
 * @return array Array of custom themes
 */
function theme_changer_get_custom_themes() {
    $custom_themes = get_option('theme_changer_custom_themes', array());
    return is_array($custom_themes) ? $custom_themes : array();
}

/**
 * Get a specific custom theme by ID
 * 
 * @param string $theme_id Theme ID
 * @return array|null Theme configuration or null if not found
 */
function theme_changer_get_custom_theme($theme_id) {
    $custom_themes = theme_changer_get_custom_themes();
    return isset($custom_themes[$theme_id]) ? $custom_themes[$theme_id] : null;
}

/**
 * Add a new custom theme
 * 
 * @param string $name Theme name
 * @param array $colors Array of color values
 * @param string $mode Theme mode (dark/light/auto)
 * @return bool True on success, false on failure
 */
function theme_changer_add_custom_theme($name, $colors, $mode = 'dark') {
    // Validate inputs
    if (empty($name) || empty($colors)) {
        return false;
    }
    
    // Generate a unique ID
    $theme_id = 'custom-' . sanitize_title($name) . '-' . time();
    
    // Get existing custom themes
    $custom_themes = theme_changer_get_custom_themes();
    
    // Create new theme
    $new_theme = array(
        'id' => $theme_id,
        'name' => sanitize_text_field($name),
        'type' => 'custom',
        'mode' => sanitize_text_field($mode),
        'colors' => theme_changer_validate_theme_colors($colors),
        'created_at' => current_time('mysql')
    );
    
    // Add to custom themes
    $custom_themes[$theme_id] = $new_theme;
    
    // Save to database
    return update_option('theme_changer_custom_themes', $custom_themes);
}

/**
 * Update an existing custom theme
 * 
 * @param string $theme_id Theme ID
 * @param string $name Theme name
 * @param array $colors Array of color values
 * @param string $mode Theme mode
 * @return bool True on success, false on failure
 */
function theme_changer_update_custom_theme($theme_id, $name, $colors, $mode) {
    $custom_themes = theme_changer_get_custom_themes();
    
    if (!isset($custom_themes[$theme_id])) {
        return false;
    }
    
    // Update theme
    $custom_themes[$theme_id]['name'] = sanitize_text_field($name);
    $custom_themes[$theme_id]['colors'] = theme_changer_validate_theme_colors($colors);
    $custom_themes[$theme_id]['mode'] = sanitize_text_field($mode);
    $custom_themes[$theme_id]['updated_at'] = current_time('mysql');
    
    return update_option('theme_changer_custom_themes', $custom_themes);
}

/**
 * Delete a custom theme
 * 
 * @param string $theme_id Theme ID
 * @return bool True on success, false on failure
 */
function theme_changer_remove_custom_theme($theme_id) {
    $custom_themes = theme_changer_get_custom_themes();
    
    if (!isset($custom_themes[$theme_id])) {
        return false;
    }
    
    // Remove theme
    unset($custom_themes[$theme_id]);
    
    // Check if this was the active theme
    $active_theme = get_option('theme_changer_active_theme');
    if ($active_theme && $active_theme['type'] === 'custom' && $active_theme['id'] === $theme_id) {
        // Reset to default theme
        update_option('theme_changer_active_theme', array(
            'type' => 'default',
            'id' => 'default-dark',
            'mode' => 'auto'
        ));
    }
    
    return update_option('theme_changer_custom_themes', $custom_themes);
}

/**
 * Validate and sanitize theme colors
 * 
 * @param array $colors Array of colors to validate
 * @return array Validated and sanitized colors
 */
function theme_changer_validate_theme_colors($colors) {
    $default_colors = array(
        'background' => '#1a1a1a',
        'surface' => '#2d2d2d',
        'text' => '#ffffff',
        'text-secondary' => '#b0b0b0',
        'primary' => '#4a9eff',
        'secondary' => '#ff6b6b',
        'accent' => '#ffd93d',
        'border' => '#404040',
        'success' => '#6bcf7f',
        'warning' => '#ffb84d',
        'error' => '#ff5252'
    );
    
    $validated_colors = array();
    
    foreach ($default_colors as $key => $default_value) {
        if (isset($colors[$key]) && !empty($colors[$key])) {
            // Validate hex color
            $color = sanitize_hex_color($colors[$key]);
            $validated_colors[$key] = $color ? $color : $default_value;
        } else {
            $validated_colors[$key] = $default_value;
        }
    }
    
    return $validated_colors;
}

/**
 * Check if a theme ID exists (default or custom)
 * 
 * @param string $theme_id Theme ID
 * @param string $type Theme type (default/custom)
 * @return bool True if exists, false otherwise
 */
function theme_changer_theme_exists($theme_id, $type = 'default') {
    if ($type === 'default') {
        $theme = theme_changer_get_default_theme($theme_id);
        return $theme !== null;
    } else {
        $theme = theme_changer_get_custom_theme($theme_id);
        return $theme !== null;
    }
}
