# Darkup Plugin - Frontend Theme Switching Guide

## 🎨 How Users Can Change Themes on the Frontend

The Darkup plugin now allows **all users** (not just admins) to change themes from the frontend of your website.

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
[darkup_theme_switcher]
```

Or for an inline button style:

```
[darkup_theme_switcher style="inline"]
```

### Example:

Place this in a WordPress page:

```
Welcome to our site!

[darkup_theme_switcher style="inline"]

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

1. **darkup.php** - Theme data now passed to JavaScript via `wp_localize_script`
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
