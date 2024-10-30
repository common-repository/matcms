=== MatCMS ===
Contributors: matmattia
Donate link: https://www.paypal.com/donate/?hosted_button_id=MQVMRTV6PW4AQ
Tags: matcms, developers
Requires at least: 4.0
Tested up to: 6.6
Stable tag: 1.4.0
Requires PHP: 7.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin adds to WordPress some utilities for developers.

== Description ==
MatCMS adds to WordPress some utilities for developers: it registers more scripts and stylesheets handlers, add useful functions for posts and themes, and much more.

== Installation ==

1. Upload `matcms` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where I can find the italian support? =

[Here!](https://www.matriz.it/projects/matcms-wordpress/)

== Screenshots ==
1. Enqueue scripts and stylesheets
2. Bootstrap 5 navigation menu

== Changelog ==

= 1.4.0 =
* Added the redirect for 404 Not Found errors. Use \MatCMS\redirect404()

= 1.3.0 =
* Updated Bootstrap Lightbox to latest version of 1.1. Added "bootstrap-lightbox-init-images" to continue to auto-initialize the lightbox to images.

= 1.2.9 =
* Updated Bootstrap Icons to latest version of 1.11.

= 1.2.8 =
* Updated Bootstrap Icons to latest version of 1.10.

= 1.2.7 =
* Updated `\MatCMS\Post::checkBootstrapBlock` method to add Bootstrap ratio classes to the Embed block.

= 1.2.6 =
* Updated `\MatCMS\Post::checkBootstrapBlock` method to add Bootstrap button classes to the File block.
* Updated Bootstrap Icons to latest version of 1.10.

= 1.2.5 =
* Updated Bootstrap Icons to latest version of 1.9.
* Added Bootstrap Lightbox script.

= 1.2.4 =
* Updated Bootstrap Icons to version 1.9.0.

= 1.2.3 =
* Updated Bootstrap Icons to version 1.8.2.
* Updated `\MatCMS\Post::checkBootstrapBlock` method to print Bootstrap blockquotes classes.

= 1.2.2 =
* Updated Bootstrap Icons to version 1.8.1.

= 1.2.1 =
* Updated Bootstrap Icons to version 1.8.0.

= 1.2.0 =
* Added `\MatCMS\Post::checkBootstrapBlock` method to be used as `render_block` filter to print Bootstrap classes in blocks.
* Changed `\MatCMS\Post::getImages` method to return all embed images (not only the attached images).

= 1.1.1 =
* Added PHP 7.3 support.

= 1.1.0 =
* Added Open Graph and Twitter Cards support.

= 1.0.0 =
* First stable version.

== Upgrade Notice ==

= 1.3.0 =
Use "bootstrap-lightbox-init-images" instead of "bootstrap-lightbox" to auto-initialize the lightbox to images.

= 1.0.0 =
This is the first stable version.