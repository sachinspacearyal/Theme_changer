=== theme-changer ===
Contributors: sachinspacearyal
Tags: dark mode, light mode, theme switcher, color schemes
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

# Theme Changer

A seamless dark/light theme switcher for WordPress with custom color schemes and auto-mode detection.


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
25:    git clone https://github.com/sachinspacearyal/theme-changer.git
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

**Sachin Aryal**


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
**Tested up to:** WordPress 6.9  
**Stable tag:** 1.0.0


# theme-changer Plugin - Frontend Theme Switching Guide

## 🎨 How Users Can Change Themes on the Frontend

The theme-changer plugin now allows **all users** (not just admins) to change themes from the frontend of your website.

## Theme Switcher Widget

A floating theme switcher button is automatically added to the bottom-right corner of every page on your site (frontend only).

### How Users Can Use It:

1. **Click the floating button** (🎨 icon) in the bottom-right corner
2. **Theme panel opens** showing:
   - Mode selector (Auto / Light / Dark)
   - All available default themes
   - All custom themes created by admins
3. **Select a mode** to switch between auto-detection, light, or dark preference
4. **Click any theme** to instantly apply it
5. The selected theme is **saved automatically** and persists across page loads

## Alternative: Shortcode

You can also place a theme switcher button anywhere in your content using a shortcode.

### Usage:

Add this shortcode to any page, post, or widget:

```
[theme_changer_theme_switcher]
```

Or for an inline button style:

```
[theme_changer_theme_switcher style="inline"]
```

### Example:

Place this in a WordPress page:

```
Welcome to our site!

[theme_changer_theme_switcher style="inline"]

You can customize the appearance using the button above.
```

## What Changed

### Before:
- Theme data was not passed to frontend JavaScript
- Theme list showed "Themes loaded from server" placeholder
- Users couldn't actually select themes

### After:
- ✅ All default themes are loaded and displayed
- ✅ All custom themes are loaded and displayed
- ✅ Current active theme is highlighted
- ✅ Theme selection works via AJAX
- ✅ Theme preference is saved for each user
- ✅ Themes apply instantly without page reload (via AJAX)

## Technical Details

The following changes were made:

1. **dark.php** - Theme data now passed to JavaScript via `wp_localize_script`
2. **theme-switcher.js** - Properly loads and renders all available themes
3. **style.css** - Enhanced styling for theme list headings
4. Added **shortcode support** for flexible placement

## Testing

1. **As a non-admin user**, visit your site's frontend
2. Look for the **floating button** in the bottom-right corner
3. Click it to open the theme panel
4. Try switching modes and themes
5. Refresh the page - your selection should persist

That's it! Users can now fully control the site's appearance from the frontend. 🎉
