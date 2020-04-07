=== WP Zoom ===

Contributors: eatbuildplay
Tags: zoom, webinars
Requires at least: 5.0
Tested up to: 5.4.0
Requires PHP: 7.3
Stable tag: 1.0
License: GPL3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

WP Zoom integrates the Zoom API. The plugin provides 4 shortcodes to display and register for webinars.

== Description ==

WP Zoom integrates the Zoop API for webinars. Currently it lacks support for meetings, and is only used for webinars which are a premium upgrade to your Zoom account. The plugin provides 4 shortcodes.

Two of the shortcodes, [zoom_webinar] and [zoom_calendar] display your upcoming webinars either in a table list or calendar format. The calendar integration uses FullCalendar a jQuery plugin for calendar rendering.

The shortcode [zoom_recording] is provided for rendering a list of recordings from your webinars. Technically this shortcode should work for any recordings in your Zoom account, but it has only been tested for Webinar recordings. The same API is used for meetings in general.

The shortcode [zoom_register] provides a registration form to register for a given webinar.

The WP Zoom plugin provides a settings page in the WP Admin under Settings > WP Zoom where you will enter your Zoom API credentials. WP Zoom requires the JWT token and secret combination that can be acquired through your Zoom account.

== Installation ==

1. Upload plugin to wp-content/plugins/ and activate from the WP Plugins management page.

2. Visit the API key settings page at Settings > WP Zoom Settings and enter your JWT token keys. Link is yourdomain.com/wp-admin/options-general.php?page=wp-zoom-settings. Visit Zoom Marketplace for instructions on generating your JWT keys at https://marketplace.zoom.us/docs/guides/auth/jwt.

== Changelog ==

# 1.0.0
Initial build of WP Zoom, includes 4 shortcodes for webinar API functions and a settings page for managing and testing JWT based API connection.
