<?php
/**
 * Admin Page Template
 * 
 * Renders the theme-changer settings page in WordPress admin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get all themes
// Get all themes
$theme_changer_default_themes = theme_changer_get_default_themes();
$theme_changer_custom_themes = theme_changer_get_custom_themes();
$theme_changer_active_theme = get_option('theme_changer_active_theme');

?>

<div class="wrap theme-changer-admin-page">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <p>Manage your site's theme settings, choose from predefined themes, or create your own custom themes.</p>
    
    <!-- Current Active Theme -->
    <div class="theme-changer-admin-section">
        <h2>Current Active Theme</h2>
        <?php if ($theme_changer_active_theme): ?>
            <p>
                <strong>Mode:</strong> <?php echo esc_html(ucfirst($theme_changer_active_theme['mode'])); ?><br>
                <strong>Type:</strong> <?php echo esc_html(ucfirst($theme_changer_active_theme['type'])); ?><br>
                <strong>Theme ID:</strong> <?php echo esc_html($theme_changer_active_theme['id']); ?>
            </p>
        <?php else: ?>
            <p>No active theme selected.</p>
        <?php endif; ?>
    </div>
    
    <!-- Default Themes -->
    <div class="theme-changer-admin-section">
        <h2>Default Themes</h2>
        <p>Choose from our predefined theme collection:</p>
        
        <div class="theme-changer-theme-grid">
            <?php foreach ($theme_changer_default_themes as $theme_changer_theme): ?>
                <div class="theme-changer-theme-card <?php echo ($theme_changer_active_theme && $theme_changer_active_theme['id'] === $theme_changer_theme['id']) ? 'active' : ''; ?>" 
                     data-theme-id="<?php echo esc_attr($theme_changer_theme['id']); ?>" 
                     data-theme-type="default"
                     onclick="selectTheme('<?php echo esc_js($theme_changer_theme['id']); ?>', 'default')">
                    
                    <div class="theme-changer-theme-option-name"><?php echo esc_html($theme_changer_theme['name']); ?></div>
                    <div style="font-size: 12px; color: #666; margin-bottom: 10px;">
                        Mode: <?php echo esc_html(ucfirst($theme_changer_theme['mode'])); ?>
                    </div>
                    
                    <div class="theme-changer-theme-option-colors">
                        <?php foreach (array_slice($theme_changer_theme['colors'], 0, 5) as $theme_changer_color): ?>
                            <div class="theme-changer-color-dot" style="background-color: <?php echo esc_attr($theme_changer_color); ?>;"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Custom Themes -->
    <div class="theme-changer-admin-section">
        <h2>Custom Themes</h2>
        
        <?php if (!empty($theme_changer_custom_themes)): ?>
            <p>Your custom themes:</p>
            <div class="theme-changer-theme-grid">
                <?php foreach ($theme_changer_custom_themes as $theme_changer_theme): ?>
                    <div class="theme-changer-theme-card <?php echo ($theme_changer_active_theme && $theme_changer_active_theme['id'] === $theme_changer_theme['id']) ? 'active' : ''; ?>" 
                         data-theme-id="<?php echo esc_attr($theme_changer_theme['id']); ?>" 
                         data-theme-type="custom">
                        
                        <div class="theme-changer-theme-option-name"><?php echo esc_html($theme_changer_theme['name']); ?></div>
                        <div style="font-size: 12px; color: #666; margin-bottom: 10px;">
                            Mode: <?php echo esc_html(ucfirst($theme_changer_theme['mode'])); ?>
                        </div>
                        
                        <div class="theme-changer-theme-option-colors" style="margin-bottom: 10px;">
                            <?php foreach (array_slice($theme_changer_theme['colors'], 0, 5) as $theme_changer_color): ?>
                                <div class="theme-changer-color-dot" style="background-color: <?php echo esc_attr($theme_changer_color); ?>;"></div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <button type="button" class="button button-small" 
                                    onclick="selectTheme('<?php echo esc_js($theme_changer_theme['id']); ?>', 'custom')">
                                Select
                            </button>
                            <button type="button" class="button button-small theme-changer-delete-theme" 
                                    data-theme-id="<?php echo esc_attr($theme_changer_theme['id']); ?>"
                                    style="background-color: #dc3232; color: white;">
                                Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No custom themes created yet.</p>
        <?php endif; ?>
    </div>
    
    <!-- Create Custom Theme -->
    <div class="theme-changer-admin-section">
        <h2>Create Custom Theme</h2>
        <p>Design your own theme by selecting custom colors:</p>
        
        <form id="theme-changer-custom-theme-form">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="theme-changer-theme-name">Theme Name</label></th>
                    <td>
                        <input type="text" id="theme-changer-theme-name" class="regular-text" placeholder="My Custom Theme" required>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label>Theme Mode</label></th>
                    <td>
                        <div style="display: flex; gap: 10px;">
                            <button type="button" class="button theme-changer-custom-mode-btn active" data-mode="dark">Dark</button>
                            <button type="button" class="button theme-changer-custom-mode-btn" data-mode="light">Light</button>
                            <button type="button" class="button theme-changer-custom-mode-btn" data-mode="auto">Auto</button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label>Colors</label></th>
                    <td>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                            <div class="theme-changer-color-picker-group">
                                <label>Background</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="background" value="#1a1a1a">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Surface</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="surface" value="#2d2d2d">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Text</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="text" value="#ffffff">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Text Secondary</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="text-secondary" value="#b0b0b0">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Primary</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="primary" value="#4a9eff">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Secondary</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="secondary" value="#ff6b6b">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Accent</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="accent" value="#ffd93d">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Border</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="border" value="#404040">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Success</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="success" value="#6bcf7f">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Warning</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="warning" value="#ffb84d">
                            </div>
                            
                            <div class="theme-changer-color-picker-group">
                                <label>Error</label>
                                <input type="text" class="theme-changer-color-picker" data-color-key="error" value="#ff5252">
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <button type="button" id="theme-changer-save-custom-theme" class="button button-primary">
                    Save Custom Theme
                </button>
            </p>
        </form>
    </div>
</div>

<script>
function selectTheme(themeId, themeType) {
    if (!confirm('Switch to this theme?')) {
        return;
    }
    
    // Get current mode
    const mode = '<?php echo esc_js($theme_changer_active_theme['mode'] ?? 'auto'); ?>';
    
    jQuery.ajax({
        url: themeChangerAjax.ajaxurl,
        type: 'POST',
        data: {
            action: 'theme_changer_save_theme',
            nonce: themeChangerAjax.nonce,
            theme_type: themeType,
            theme_id: themeId,
            mode: mode
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Failed to switch theme');
            }
        },
        error: function(error) {
            console.error('AJAX error:', error);
            alert('An error occurred');
        }
    });
}
</script>
