=== JD Event Listing ===
Contributors: jaydeep-rami
Tags: event, google calendar, map, google map, create calendar event
Requires at least: 4.9.8
Tested up to: 4.9.8
Stable tag: 1.0
Donate link:
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple Event listing with Google Map and Google Calendar integrate for Create Event.

Welcome to the JD Event Listing repository on GitHub. Here you can browse the source, look at open issues and keep track of development

## Features
* Create Event with fields Event URL, Event Location (With Google Map), Event URL (For Set Third Party URL), Event date (Start and End date with time)
* Event listing
* Google Map integrate (Google Map show on Backend as well as Fron-end for show Location of Events)
* Google Calendar integrate (You can insert listed Event in your Google Calendar by just one Click)

## Documentation
* For Create Event in Google Calendar, You should have a Client ID and API Key. Once You have both then enter into the Plugin Setting under the General WordPress setting section called `Event Setting`.

For Google Calendar oAuth, it's required to add Redirect URL like `[YOUR_SITE_URL]/events` which is archive page of Events. Please set this link into the Google Calendar oAuth Settings.

## Minimum Requirements
* PHP version 5.4 or greater (PHP 5.6 or greater is recommended)
* MySQL version 5.0 or greater (MySQL 5.6 or greater is recommended)
* WordPress 4.9+
* Google Map API Key (<a href="https://console.cloud.google.com/apis/library/maps-backend.googleapis.com"> Get Google Map API Key by following this link</a>)
* Google Calendar API Key and Client ID  (<a href="https://console.cloud.google.com/apis/library/calendar-json.googleapis.com"> Get Google Calendar API Key and Client ID by following this link</a>)

## Contributing to JD Event Listing
If you have a patch or have stumbled upon an issue with JD Event Listing, you can contribute this back to the code.
== Changelog ==

= 1.0 =
* Initial plugin release!