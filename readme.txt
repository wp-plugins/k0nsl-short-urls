=== k0nsl Short URLs ===
Contributors: k0nsl, GentleSource
Donate link: http://k0nsl.org/blog/
Tags: short url, short, url, shortener, url shortener, url shortening, urls, links, tinyurl, twitter, microblogging, k0nsl
Requires at least: 3.0
Tested up to: 3.6
Stable tag: trunk
License: GPLv3

Automatically shortens the blog post URL via knsl.net.

== Description ==

This plugin creates a short URL from the blog post permalink and stores it
in the database. The URL is displayed below the blog post along with a link
to twitter that passes the short URL on, if defined in the settings page.
The plugin currently use knsl.net as the only service and stores the data in the database of knsl.net.

Plugin homepage:
http://k0nsl.org/blog/k0nsl-short-urls-plugin/

For support use WordPress.org or devNET community forums:
http://devnet-software.org/

Thanks!

== Installation ==

1. Upload the `k0nsl_shorturl` folder to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You can change the default short URL service in 'Settings' -> 'Short URLs'

== Changelog ==

= 0.3a =

* Addd pre_get_shortlink(); and did some minor cleanups. Thanks to Rob Allen for code contributions.

= 0.2 =

* Added function k0nsl_show_url(); for placement in template files, i.e k0nsl_show_url(); will result in "http://knsl.net/453" when echoed.

= 0.1 =

* Replaced hardcoded plugin directory path with a defined path via "k0nsl_plugin_path".

== Frequently Asked Questions ==

= How do I use the k0nsl_show_url() function? =
This function can be used in your theme files. For example, we echo k0nsl_show_url() in post.php and this will show "http://knsl.net/453".

== Screenshots ==

1. k0nsl Short URL settings page
2. k0nsl_show_url() in action.

== Upgrade Notice ==
= None. =
