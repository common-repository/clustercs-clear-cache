=== ClusterCS Clear Cache ===
Contributors: clustercs
Tags: clustercs, clear cache, nginx cache, clear nginx clear
Requires at least: 4.0
Tested up to: 5.2.2
Stable tag: trunk
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin lets you delete NGINX cache directly from your WordPress website, if you are using the ClusterCS server control panel. 

== Description ==

This plugin lets you delete NGINX cache directly from your WordPress website, if you are using the [ClusterCS](https://www.clustercs.com/) server control panel. 

It can also be set to automatically clear cache each time you add a new post or page, or edit an existing post or page, this way ensuring your visitors can see the latest changes right after you add them.

== After activation ==

1. Just go to CCS Clear Cache in the WordPress admin menu and paste the path you get from the clear cache rule on ClusterCS.
1. After pasting the path, the plugin will check if the URL is valid and will notice you by showing a green check or an error (case in which you should check if the pasted path coresponds with the one shown in the ClusterCS panel).


== Installation ==

== From WordPress ==

1. Visit "Plugins > Add New"
1. Search for "ClusterCS Clear Cache"
1. Activate ClusterCS Clear Cache from your Plugins page.

== Manually ==

1. Upload the "clustercs-clear-cache" folder to the "/wp-content/plugins/" directory
1. Activate the ClusterCS Clear Cache from your Plugins page in WordPress

== Frequently Asked Questions ==

You'll find answers to many of your questions on [clustercs.com/kb](https://clustercs.com/kb/).

= How to setup NGINX cache on WordPress from ClusterCS? =

Please follow the tutorial on our [KB](https://clustercs.com/kb/article/speed-optimizations/actions/caching-on-wordpress-using-nginx/)

= How to create a speed rule on ClusterCS for clear cache? =
Please follow the tutorial on our [KB](https://clustercs.com/kb/article/speed-optimizations/actions/speed-engine-clear-cache/)

== Screenshots ==
1. Setting up the clear cache URL
1. Activate automated clear cache on post/page creation or edit
1. Manual clear cache

== Changelog ==

= 1.0.1 =
*Improved help sections on ClusterCS Clear Cache
*Fixed certain cases when "-1" is displayed when trying to create a post/page

= 1.0.0 =
Initial plugin release.