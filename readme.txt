=== Plugin Name ===
Contributors: chdorner, Razuna Ltd.
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=CK3SHKRLZ9XAY&lc=CH&item_name=Razuna%20Wordpress%20Plugin&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: media, admin, razuna, mirror, uploads, images, photos, files
Requires at least: 2.8.0
Tested up to: 3.2.1
Stable tag: 0.9.1

This plugin allows to transparently load images, videos, audios and documents from the Razuna Digital Asset Management to your Wordpress site.

== Description ==

This WordPress Plugin allows you to use any hosted Razuna service to host your media for your WordPress powered blog.

Once installed and configured, this plugin transparently integrates with your WordPress blog.
You will find a "Razuna" Tab next to your regular "Upload" and "Media Library" tab, which allows you to easily browse and choose files which are hosted on the Razuna service.

For more information regarding Razuna checkout the Hosted (SaaS) Solution at http://www.razuna.com or check out the Open Source version at http://www.razuna.org

Supports WordPress, WordPress MU and BuddyPress

== Installation ==

1. Upload `razuna-media-media-manager` to the `/wp-content/plugins/` directory
1. Activate the plugin through the "Plugins" menu in WordPress (be sure to activate the plugin site-wide on WordPress MU installations)
1. Configure the plugin in the "Razuna Configuration" (or "Settings") screen by following the onscreen prompts.

== Frequently Asked Questions ==

= What are the system requirements? =

You can use any PHP version, but we recommend a 5.x one. Furthermorem make sure that you have the SOAP client libraries installed! Without it, the Razuna plugin will not be able to load.

= Which user shall I use to connect to Razuna? =

Currently, you need to be a user in the administration group in order to access the Razuna assets.

== Screenshots ==

1. Configuration page
2. Media Manager integration

== Changelog ==

= 0.9.1 =
* Bug Fix: Razuna Media Player was not displayed properly. Thanks to Vadim Lozko for supplying the fix.

= 0.9.0 =
* New Feature: Option to insert converted formats for image, video and audio assets into posts and pages.

= 0.8.5 =
* Improvement: Showing the UploadBin folder
* Improvement: Showing all folders for Administrators
* Improvement: Small Design improvements
* Bug Fix: Plugin loaded unnecessary jQuery file
* Bug Fix: Sometimes only certain folders were shown
* Bug Fix: Folders are not being refreshed properly
* Bug Fix: Fix for some functions to work with the new string ID system introduced in Razuna 1.4.2

= 0.8.1 =
* Bug Fix: Could not log in to Razuna properly
* Bug Fix: Upload link did not work as expected

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



