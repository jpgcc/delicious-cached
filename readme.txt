=== del.icio.us cached++ ===
Contributors: jcraveiro
Donate link: http://example.com/
Tags: delicious, bookmarks
Requires at least: 2.0
Tested up to: 2.3
Stable tag: trunk

Here is a short description of the plugin.  This should be no more than 150 chars.  No markup here.

== Description ==

Delicious Cached++ uses the built-in MagpieRSS engine functions to create a list of the latest bookmarks on a del.icio.us account, from its RSS feed, keeping the feed cached in the database.

This plugin respects del.icio.us' will not to overload the feed with subsequent request, since the default cache refresh period is 60 minutes.

== Installation ==

1. Download the source code
2. Extract the file delicious_cached_pp.php from the ZIP archive
3. Upload the file delicious_cached_pp.php into your Wordpress install wp-content/plugins folder
4. Activate the from Wordpress administration

== Frequently Asked Questions ==

= Can I use the plugin without widgets support? =

Yes. The default invocation is:

`<ul>`
`<?php delicious_pp('accountname'); ?>`
`</ul>`

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

...
