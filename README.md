# del.icio.us cached++

https://wordpress.org/plugins/delicious-cached/

del.icio.us cached++ uses the built-in MagpieRSS engine functions to create a list of the latest bookmarks on a del.icio.us account, from its RSS feed, keeping the feed cached in the database.

This plugin respects del.icio.us' ToS and will not to overload the feed with subsequent requests, since the default cache refresh period is 60 minutes.

The plugin is wigdet compatible, and allows multiple widgets with independent options.

## Installation (Wordpress 2.2+ with widget-ready theme)

1. Go to the *Plugins* section of your Wordpress dashboard; click *Add New*
2. Search for `delicious-cached` and install the plugin
3. Activate the plugin
4. Go to *Presentation* > *Widgets*. Add the widget to the sidebar of your liking and/or setup any options you want to change.

# FAQ

## Can I use the plugin without widgets support?

Not anymore (since version 2.0).

## Can I display only bookmarks that belong to a given tag?

Yes, and since version 2.0 this is made explicit. To accomplish that, go to *Presentation* > *Widgets* and fill in the optional Tag field.
