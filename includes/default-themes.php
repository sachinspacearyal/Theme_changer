<?php
/**
 * Default Themes Configuration
 * 
 * This file contains predefined theme configurations with color schemes
 * for dark, light, and auto modes.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all default themes
 * 
 * @return array Array of default theme configurations
 */
function theme_changer_get_default_themes() {
    return array(
        'default-dark' => array(
            'id' => 'default-dark',
            'name' => 'Default Dark',
            'type' => 'default',
            'mode' => 'dark',
            'colors' => array(
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
            )
        ),
        'default-light' => array(
            'id' => 'default-light',
            'name' => 'Default Light',
            'type' => 'default',
            'mode' => 'light',
            'colors' => array(
                'background' => '#ffffff',
                'surface' => '#f5f5f5',
                'text' => '#1a1a1a',
                'text-secondary' => '#666666',
                'primary' => '#2196f3',
                'secondary' => '#f44336',
                'accent' => '#ffc107',
                'border' => '#e0e0e0',
                'success' => '#4caf50',
                'warning' => '#ff9800',
                'error' => '#f44336'
            )
        ),
        'midnight-blue' => array(
            'id' => 'midnight-blue',
            'name' => 'Midnight Blue',
            'type' => 'default',
            'mode' => 'dark',
            'colors' => array(
                'background' => '#0a1929',
                'surface' => '#132f4c',
                'text' => '#e7edf4',
                'text-secondary' => '#b2bac2',
                'primary' => '#3399ff',
                'secondary' => '#66b2ff',
                'accent' => '#5090d3',
                'border' => '#1e3a5f',
                'success' => '#1db45a',
                'warning' => '#f57c00',
                'error' => '#d32f2f'
            )
        ),
        'sunset' => array(
            'id' => 'sunset',
            'name' => 'Sunset',
            'type' => 'default',
            'mode' => 'dark',
            'colors' => array(
                'background' => '#2d1b2e',
                'surface' => '#422e42',
                'text' => '#fef4f4',
                'text-secondary' => '#d1c4c4',
                'primary' => '#ff6b9d',
                'secondary' => '#c94277',
                'accent' => '#ffa07a',
                'border' => '#553d55',
                'success' => '#4ecdc4',
                'warning' => '#ffe66d',
                'error' => '#ff6b6b'
            )
        ),
        'forest' => array(
            'id' => 'forest',
            'name' => 'Forest',
            'type' => 'default',
            'mode' => 'dark',
            'colors' => array(
                'background' => '#1a2f23',
                'surface' => '#2d4a36',
                'text' => '#e8f5e9',
                'text-secondary' => '#c8e6c9',
                'primary' => '#66bb6a',
                'secondary' => '#43a047',
                'accent' => '#8bc34a',
                'border' => '#3d5a47',
                'success' => '#4caf50',
                'warning' => '#ff9800',
                'error' => '#f44336'
            )
        ),
        'minimal-light' => array(
            'id' => 'minimal-light',
            'name' => 'Minimal Light',
            'type' => 'default',
            'mode' => 'light',
            'colors' => array(
                'background' => '#fafafa',
                'surface' => '#ffffff',
                'text' => '#212121',
                'text-secondary' => '#757575',
                'primary' => '#000000',
                'secondary' => '#424242',
                'accent' => '#9e9e9e',
                'border' => '#e0e0e0',
                'success' => '#43a047',
                'warning' => '#fb8c00',
                'error' => '#e53935'
            )
        ),
        'ocean' => array(
            'id' => 'ocean',
            'name' => 'Ocean',
            'type' => 'default',
            'mode' => 'light',
            'colors' => array(
                'background' => '#e0f2f1',
                'surface' => '#ffffff',
                'text' => '#004d40',
                'text-secondary' => '#00796b',
                'primary' => '#00897b',
                'secondary' => '#26a69a',
                'accent' => '#80cbc4',
                'border' => '#b2dfdb',
                'success' => '#66bb6a',
                'warning' => '#ffa726',
                'error' => '#ef5350'
            )
        )
    );
}

/**
 * Get a specific default theme by ID
 * 
 * @param string $theme_id Theme ID
 * @return array|null Theme configuration or null if not found
 */
function theme_changer_get_default_theme($theme_id) {
    $themes = theme_changer_get_default_themes();
    return isset($themes[$theme_id]) ? $themes[$theme_id] : null;
}

/**
 * Detect the current mode (dark/light/auto)
 * This is primarily handled by JavaScript, but we provide a server-side default
 * 
 * @return string The detected or default mode
 */
function theme_changer_detect_mode() {
    $active_theme = get_option('theme_changer_active_theme');
    
    if ($active_theme && isset($active_theme['mode'])) {
        return $active_theme['mode'];
    }
    
    // Default to auto mode
    return 'auto';
}

/**
 * Get the appropriate theme based on mode
 * 
 * @param string $mode The mode (dark/light/auto)
 * @return array Theme configuration
 */
function theme_changer_get_theme_by_mode($mode) {
    $themes = theme_changer_get_default_themes();
    
    switch ($mode) {
        case 'dark':
            return $themes['default-dark'];
        case 'light':
            return $themes['default-light'];
        case 'auto':
        default:
            // For auto mode, we'll apply both and let CSS media queries handle it
            // Return dark as default, CSS will override for light preference
            return $themes['default-dark'];
    }
}
