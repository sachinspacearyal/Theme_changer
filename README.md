# Theme Changer - WordPress Theme Switcher Plugin

A powerful WordPress plugin that provides seamless dark/light theme functionality with automatic mode detection and user-customizable color schemes.

## 📋 Description

**Theme Changer** is a comprehensive theme management plugin for WordPress that allows users to switch between dark and light modes with customizable color schemes. The plugin features automatic system preference detection, default themes, and the ability to create custom themes with a user-friendly color picker interface.

## ✨ Features

- 🌓 **Dark/Light Mode Toggle** - Seamless switching between themes with a single click
- 🎨 **Custom Theme Creator** - Build your own color schemes using an integrated color picker
- 🔄 **Auto Mode Detection** - Automatically adapts to system preferences
- 💾 **Persistent Preferences** - Saves user theme choices across sessions
- 🎯 **Default Themes** - Pre-configured professional dark and light themes
- 🛠️ **Admin Panel** - Easy-to-use settings interface
- 📱 **Responsive Design** - Works perfectly on all devices
- ⚡ **Shortcode Support** - Display theme switcher anywhere with `[theme_changer_theme_switcher]`
- 🔒 **Secure** - AJAX requests protected with WordPress nonces

## 📦 Installation

1. **Download or Clone**
   ```bash
   git clone https://github.com/sachinspacearyal/Theme_changer.git
   ```

2. **Upload to WordPress**
   - Upload the `theme-changer` folder to `/wp-content/plugins/` directory
   - Or install via WordPress admin panel: Plugins → Add New → Upload Plugin

3. **Activate**
   - Go to WordPress admin panel → Plugins
   - Find "Theme Changer" in the list
   - Click "Activate"

4. **Configure**
   - Navigate to **Theme Changer** in the admin sidebar
   - Configure your default theme and create custom themes

## 🚀 Usage

### Automatic Theme Switcher

The plugin automatically displays a floating theme switcher widget on your website. Users can click it to toggle between themes.

### Using Shortcode

Display the theme switcher anywhere in your content:

**Floating Button (Default):**
```
[theme_changer_theme_switcher]
```

**Inline Button:**
```
[theme_changer_theme_switcher style="inline"]
```

### Admin Settings

Access the admin panel at **Theme Changer** in the WordPress dashboard to:
- Select default themes
- Create custom color schemes
- Configure theme behavior
- Manage saved themes

## 📁 File Structure

```
theme-changer/
├── assets/
│   ├── css/
│   │   └── style.css           # Main stylesheet
│   └── js/
│       ├── theme-switcher.js   # Frontend theme switching logic
│       └── color-picker.js     # Admin color picker functionality
├── frontend/
│   └── admin-page.php          # Admin settings page UI
├── includes/
│   ├── default-themes.php      # Default theme definitions
│   ├── custom-themes.php       # Custom theme management
│   └── apply-theme.php         # Theme application logic
├── theme-changer.php            # Main plugin file
├── README.md                    # This file
└── FRONTEND-GUIDE.md           # Frontend development guide
```

## ⚙️ Technical Details

### Constants

- `THEME_CHANGER_VERSION` - Plugin version (1.0.0)
- `THEME_CHANGER_PLUGIN_DIR` - Absolute path to plugin directory
- `THEME_CHANGER_PLUGIN_URL` - URL to plugin directory

### Hooks & Filters

**Actions:**
- `theme_changer_activate` - Runs on plugin activation
- `theme_changer_deactivate` - Runs on plugin deactivation
- `wp_enqueue_scripts` - Enqueues frontend assets
- `admin_enqueue_scripts` - Enqueues admin assets
- `wp_head` - Applies theme to frontend
- `admin_head` - Applies theme to admin panel

**AJAX Actions:**
- `wp_ajax_theme_changer_save_theme` - Saves user theme preference
- `wp_ajax_nopriv_theme_changer_save_theme` - Saves theme (non-logged users)
- `wp_ajax_theme_changer_save_custom_theme` - Saves custom theme
- `wp_ajax_theme_changer_delete_custom_theme` - Deletes custom theme

### Shortcodes

- `[theme_changer_theme_switcher]` - Displays theme switcher
  - **Attributes:**
    - `style` - "floating" (default) or "inline"

## 🔧 Development

### Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- jQuery (included with WordPress)

### Adding Custom Themes Programmatically

```php
// Get current custom themes
$themes = theme_changer_get_custom_themes();

// Add a new theme
theme_changer_add_custom_theme('My Theme', array(
    'primary' => '#3498db',
    'background' => '#2c3e50',
    'text' => '#ecf0f1'
), 'dark');
```

## 🐛 Bug Fixes & Updates

### Version 1.0.0
- ✅ Fixed theme switching requiring two clicks (now works with single click)
- ✅ Implemented user preference persistence
- ✅ Added automatic mode detection
- ✅ Created custom theme builder

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
```

## 👤 Author

**Your Name**
- Website: [https://example.com](https://example.com)
- Plugin URI: [https://example.com/theme-changer](https://example.com/theme-changer)

## 📞 Support

For support, please create an issue in the GitHub repository or contact through the plugin support forum.

## 🙏 Acknowledgments

- WordPress community for excellent documentation
- Contributors and testers

---

**Plugin Name:** Theme Changer  
**Version:** 1.0.0  
**Text Domain:** theme-changer  
**Requires at least:** WordPress 5.0  
**Tested up to:** WordPress 6.4  
**Stable tag:** 1.0.0
