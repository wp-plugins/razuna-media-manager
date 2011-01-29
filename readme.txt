=== Plugin Name ===
Contributors: chdorner, Razuna Ltd.
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=CK3SHKRLZ9XAY&lc=CH&item_name=Razuna%20Wordpress%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: media, admin, razuna, mirror, uploads, images, photos, files
Requires at least: 2.8.0
Tested up to: 3.0.4
Stable tag: 0.8.0

Allows to add Files from your Razuna account into WordPress posts.

== Description ==

This WordPress Plugin allows you to use any hosted Razuna service to host your media for your WordPress powered blog.

Once installed and configured, this plugin transparently integrates with your WordPress blog.
You will find a "Razuna" Tab next to your regular "Upload" and "Media Library" tab, which allows you to easily browse and choose files which are hosted on the Razuna service.

For more information regarding Razuna checkout the Hosted (SaaS) Solution at http://www.razuna.com or check out the Open Source version at http://www.razuna.org

Supports WordPress, WordPress MU and BuddyPress

== Screenshots ==

1. Configuration page
2. Media Manager integration

== Changelog ==

= 0.8.0 =
* Improvement: Updated plugin to work with the latest Razuna 1.4.2 release
* Improvement: Removed the share functionality for it is not needed anymore
* Improvement: Updated the link within Plugin

= 0.7.0 =
* New Feature: Test Razuna credentials right on the configuration page
* New Feature: Media player (Flowplayer) integration for video and audio files
* New Feature: Widget, add Razuna assets to the sidebar
* Improvement: Suggesting title and description from the Razuna meta data

= 0.6.0 =
* New Features: Ability to upload files from the plugin
* Shows only files within the "My Folder" directory
* Changed url for API calls to the configured hostname
* Rewrite AJAX communication to use JSON
* Integrate official Razuna PHP API class

= 0.5.1 =
* Fixed a bug where the options page could not save the settings in WordPress MU

= 0.5.0 =
* Fixed an issue where some shared items were displayed as private
* New Feature: Automatic sharing of an asset when inserting into a blog post

= 0.4.2 =
* First public version
* Allows to add shared images, videos and documents which are hosted on a Razuna service to your posts

== Installation ==

1. Upload `razuna-media-media-manager` to the `/wp-content/plugins/` directory
1. Activate the plugin through the "Plugins" menu in WordPress (be sure to activate the plugin site-wide on WordPress MU installations)
1. Configure the plugin in the "Razuna Configuration" (or "Settings") screen by following the onscreen prompts.

Troubleshoot:
If you can not connect to your Razuna Server then make sure that your PHP is configured with the SOAP client!
