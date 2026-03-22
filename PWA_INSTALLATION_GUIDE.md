# Progressive Web App (PWA) Installation Guide

## Overview

The SACCO Management System is now a Progressive Web App (PWA), allowing users to install it on their devices for quick, one-click access. The app works like a native application while running through your web browser.

## Benefits of Installing the PWA

✅ **One-Click Access** - Launch from desktop/home screen without typing URLs  
✅ **App-Like Experience** - Runs in standalone mode without browser UI  
✅ **Offline Support** - Access cached pages even without internet  
✅ **Faster Loading** - Cached resources load instantly  
✅ **Cross-Platform** - Works on Windows, Mac, Linux, Android, iOS  
✅ **Auto-Updates** - Always get the latest version automatically  

## Installation Instructions

### 🖥️ Desktop Installation (Chrome, Edge, Brave)

1. **Visit the SACCO System**
   - Open your browser and navigate to your SACCO system URL
   - Log in to your account

2. **Install Prompt**
   - You'll see an "Install SACCO App" banner in the bottom-right corner
   - OR click the install icon (⊕) in the browser address bar
   - OR go to browser menu → "Install SACCO Management System"

3. **Click Install**
   - Click the "Install" button on the banner
   - Confirm the installation in the popup dialog

4. **Launch the App**
   - The app icon will appear on your desktop
   - Double-click to launch
   - The app opens in its own window (no browser UI)

### 📱 Mobile Installation (Android)

1. **Open Chrome Browser**
   - Navigate to your SACCO system URL
   - Log in to your account

2. **Install Banner**
   - Tap "Install SACCO App" banner at the bottom
   - OR tap the menu (⋮) → "Add to Home screen"

3. **Add to Home Screen**
   - Tap "Install" or "Add"
   - The app icon appears on your home screen

4. **Launch**
   - Tap the SACCO icon on your home screen
   - App opens in fullscreen mode

### 🍎 iOS Installation (iPhone/iPad)

1. **Open Safari Browser**
   - Navigate to your SACCO system URL
   - Log in to your account

2. **Share Menu**
   - Tap the Share button (□↑) at the bottom
   - Scroll down and tap "Add to Home Screen"

3. **Customize & Add**
   - Edit the name if desired (default: "SACCO")
   - Tap "Add" in the top-right corner

4. **Launch**
   - Tap the SACCO icon on your home screen
   - App opens in fullscreen mode

## Setup Steps (For Administrators)

### 1. Generate App Icons

The system needs icons in multiple sizes for different devices:

```bash
# Option 1: Use the built-in icon generator
# Visit: http://your-sacco-url/images/icons/generate-icons.html
# Click "Download All Icons"
# Save all icons to public/images/icons/

# Option 2: Create custom icons
# Create PNG images in these sizes:
# 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512
# Name them: icon-72x72.png, icon-96x96.png, etc.
# Place in: public/images/icons/
```

### 2. Verify Files Are in Place

Ensure these files exist:
- ✅ `public/manifest.json` - PWA configuration
- ✅ `public/sw.js` - Service worker for offline support
- ✅ `public/offline.html` - Offline fallback page
- ✅ `public/images/icons/icon-*.png` - App icons (8 sizes)

### 3. Test the Installation

1. **Clear Browser Cache**
   - Press Ctrl+Shift+Delete (Windows/Linux)
   - Press Cmd+Shift+Delete (Mac)
   - Clear cached images and files

2. **Visit Your Site**
   - Navigate to your SACCO system
   - Open browser DevTools (F12)
   - Go to "Application" tab
   - Check "Manifest" - should show all icons
   - Check "Service Workers" - should show registered worker

3. **Test Install Prompt**
   - Wait a few seconds after page load
   - Install banner should appear
   - Click "Install" to test

4. **Verify Installation**
   - App should open in standalone window
   - Check desktop/home screen for icon
   - Launch app from icon

## Features

### Offline Support

The PWA caches essential resources for offline access:
- Dashboard page
- CSS stylesheets
- JavaScript files
- Bootstrap and FontAwesome libraries

When offline, users see a friendly offline page with retry option.

### App Shortcuts

Right-click the app icon (desktop) or long-press (mobile) to access quick shortcuts:
- 📊 Dashboard
- 👥 Members
- 💾 Backups

### Install Prompt Behavior

- **Auto-Display**: Banner appears automatically on first visit
- **Dismissible**: Users can dismiss for 7 days
- **Auto-Dismiss**: Banner auto-hides after 30 seconds
- **Smart Detection**: Only shows if app is not already installed

## Troubleshooting

### Install Button Not Showing

**Possible Causes:**
1. App already installed
2. Not using HTTPS (required for PWA)
3. Browser doesn't support PWA
4. Service worker registration failed

**Solutions:**
- Check browser console for errors
- Ensure site is served over HTTPS
- Use Chrome, Edge, or Safari
- Verify `sw.js` is accessible at `/sw.js`

### Icons Not Displaying

**Solutions:**
1. Generate icons using the icon generator
2. Verify icons exist in `public/images/icons/`
3. Check file permissions (should be readable)
4. Clear browser cache and reload

### Service Worker Not Registering

**Solutions:**
1. Check browser console for errors
2. Verify `sw.js` is at `public/sw.js`
3. Ensure HTTPS is enabled
4. Check file permissions

### Offline Page Not Working

**Solutions:**
1. Verify `offline.html` exists at `public/offline.html`
2. Service worker must be registered first
3. Test by going offline (airplane mode)

## Updating the PWA

When you update the SACCO system:

1. **Service Worker Auto-Updates**
   - Service worker checks for updates every minute
   - New version downloads in background
   - Users get update on next app launch

2. **Force Update**
   - Users can close and reopen the app
   - Or refresh the page (if in browser)

3. **Cache Version**
   - Update `CACHE_NAME` in `sw.js` to force cache refresh
   - Example: Change `'sacco-v1'` to `'sacco-v2'`

## Uninstalling the PWA

### Desktop
1. Right-click app icon → "Uninstall"
2. OR: Open app → Menu (⋮) → "Uninstall SACCO Management System"

### Android
1. Long-press app icon → "Uninstall"
2. OR: Settings → Apps → SACCO → Uninstall

### iOS
1. Long-press app icon → "Remove App"
2. Confirm deletion

## Security Notes

- ✅ PWA requires HTTPS (secure connection)
- ✅ Service worker only caches public resources
- ✅ User authentication still required
- ✅ Sensitive data not cached offline
- ✅ Session management unchanged

## Browser Support

| Browser | Desktop | Mobile | Install Support |
|---------|---------|--------|-----------------|
| Chrome | ✅ | ✅ | ✅ |
| Edge | ✅ | ✅ | ✅ |
| Safari | ✅ | ✅ | ✅ (Add to Home) |
| Firefox | ✅ | ✅ | ⚠️ Limited |
| Opera | ✅ | ✅ | ✅ |

## Technical Details

### Manifest Configuration
- **Name**: SACCO Management System
- **Short Name**: SACCO
- **Display**: Standalone (no browser UI)
- **Theme Color**: #3b82f6 (Blue)
- **Background**: #1e40af (Dark Blue)
- **Start URL**: /dashboard

### Service Worker Features
- **Caching Strategy**: Cache-first with network fallback
- **Cache Version**: sacco-v1
- **Update Check**: Every 60 seconds
- **Offline Fallback**: Custom offline page
- **Push Notifications**: Supported (optional)

## Support

For issues or questions:
1. Check browser console for errors
2. Review this guide
3. Contact system administrator
4. Check Laravel logs: `storage/logs/laravel.log`

## Changelog

### Version 1.0 (March 2026)
- Initial PWA implementation
- Offline support
- Install prompts
- App shortcuts
- Service worker caching
- Custom offline page
