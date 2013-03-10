=== k0nsl Short URLs ===
Contributors: k0nsl
Tags: short url, short, url, shortener, url shortener, url shortening, urls, links, tinyurl, twitter, microblogging, k0nsl
Requires at least: 2.5
Tested up to: 3.6

Automatically shortens the blog post URL via knsl.net.

== Description ==

This plugin creates a short URL from the blog post permalink and stores it
in the database. The URL is displayed below the blog post along with a link
to twitter that passes the short URL on.
The plugin currently use knsl.net as the only service and stores the data in the database of knsl.net

== Installation ==

1. Upload the `k0nsl_shorturl` folder to `/wp-content/plugins/`.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You can change the default short URL service in 'Settings' -> 'Short URLs'

== Changelog ==

= 0.2 =

* Added function k0nsl_show_url(); for placement in template files, i.e k0nsl_show_url(); will echo "http://knsl.net/453" for your long URL in the permalink of your post.

= 0.1 =

* Replaced hardcoded plugin directory path with a defined path via "k0nsl_plugin_path".

== Frequently Asked Questions ==

No questions yet.

== Screenshots ==

1. Short URL settings page