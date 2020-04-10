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

== Shortcode Usage ==

WP Zoom provides 4 shortcodes, a summary is provided in the list below.

1. [zoom_webinar] - Table list of upcoming webinars.
2. [zoom_calendar] - Calendar of upcoming webinars.
3. [zoom_recording] - List of webinar recordings with audio playback or download.
4. [zoom_register] - Registration form for a given webinar.

=== Shortcode [zoom_webinar] ===

The Zoom Webinar shortcode has 5 attributes available. No attributes are required so you can use the shortcode as [zoom_webinar].

Attribute "include"

This is a filtering attribute that will search the title of your webinars and only show webinars that have the string specified in the include attribute. All other webinars will be removed from the list.

Example: [zoom_webinar include="Weekly Scrum"]

In the example above only webinars with the string "Weekly Scrum" in the title will be shown on the list.

Attribute "exclude"

This is a filtering attribute that works in the opposite manner as "include", where Webinars with a matching string in the title will be removed from the list.

Example: [zoom_webinar exclude="Private Meetings"]

In the example above webinars with the string "Private Meetings" in the title will be removed from the list.

Attribute "hide"

This attribute allows you to change the title of the webinar shown by removing the matching part of the title.

Example: [zoom_webinar hide="Approval "]

In the example above webinars with a title such as "Q1 Budget Approval Meeting" would be displayed as "Q1 Budget Meeting". Note that if you're removing words in the midst of the title you may also want to remove extra spaces, which is why the example shows "Approval " instead of "Approval".

Attribute "days"

The days attribute allows you to specify how many days forward of the current date you want webinars to be listed. It allows you to setup for instance a 7-day or 30-day list of upcoming webinars, excluding any webinars beyond that time period.

Example: [zoom_webinar days="7"]

In the example above the list will show only webinars with a date in the upcoming 7-days.

Attribute "registration"

The registration attribute enables you to choose between "internal" or "external" registration for each webinar in the list. The default if you do not provide this attribute is "internal", which means that the WP Zoom webinar registration form will be linked from each webinar listing. Switching to external results in the register link pointing to the zoom.com registration form.

If using the external (zoom.com) registration form, make sure it is publicly accessible.

If using the internal (WP Zoom) registration form (default option), make sure you've set the "Registration Page" option under WP Zoom settings in the WP Admin at Settings > WP Zoom.

Example: [zoom_webinar registration="external"]

In the example above we switch to using external registration and the registration link will point to the webinar registration form at zoom.com.

=== Shortcode [zoom_calendar] ===

The Zoom Webinar shortcode has 4 attributes available. No attributes are required so you can use the shortcode as [zoom_webinar].

Attribute "include"

This is a filtering attribute that will search the title of your webinars and only show webinars that have the string specified in the include attribute. All other webinars will be removed from the list.

Example: [zoom_calendar include="Weekly Scrum"]

In the example above only webinars with the string "Weekly Scrum" in the title will be shown on the list.

Attribute "exclude"

This is a filtering attribute that works in the opposite manner as "include", where Webinars with a matching string in the title will be removed from the list.

Example: [zoom_calendar exclude="Private Meetings"]

In the example above webinars with the string "Private Meetings" in the title will be removed from the list.

Attribute "hide"

This attribute allows you to change the title of the webinar shown by removing the matching part of the title.

Example: [zoom_calendar hide="Approval "]

In the example above webinars with a title such as "Q1 Budget Approval Meeting" would be displayed as "Q1 Budget Meeting". Note that if you're removing words in the midst of the title you may also want to remove extra spaces, which is why the example shows "Approval " instead of "Approval".

Attribute "days"

The days attribute allows you to specify how many days forward of the current date you want webinars to be listed. It allows you to setup for instance a 7-day or 30-day list of upcoming webinars, excluding any webinars beyond that time period.

Example: [zoom_calendar days="7"]

In the example above the list will show only webinars with a date in the upcoming 7-days.

=== Shortcode [zoom_recording] ===

@TODO

=== Shortcode [zoom_register] ===

@TODO

== Changelog ==

# 1.0.0
Initial build of WP Zoom, includes 4 shortcodes for webinar API functions and a settings page for managing and testing JWT based API connection.
