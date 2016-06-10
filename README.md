# del.icio.us cached++

https://wordpress.org/plugins/delicious-cached/

del.icio.us cached++ uses the built-in MagpieRSS engine functions to create a list of the latest bookmarks on a del.icio.us account, from its RSS feed, keeping the feed cached in the database.

This plugin respects del.icio.us' ToS and will not to overload the feed with subsequent requests, since the default cache refresh period is 60 minutes.

Since version 1.3, the plugin is wigdet compatible. To take advantage of that, you'll need either

1. Wordpress 2.2 or higher; or
2. the Widgets plugin. 

You can still use the plugin without this feature (on WordPress 2.0/2.1 and/or along with a theme that's not widget compatible); just check the FAQ.

## Installation (Wordpress 2.2+ with widget-ready theme)

1. Go to the *Plugins* section of your Wordpress dashboard; click *Add New*
2. Search for `delicious-cached` and install the plugin
3. Activate the plugin
4. Go to *Presentation* > *Widgets*. Add the widget to the sidebar of your liking and/or setup any options you want to change.

# FAQ

## Can I use the plugin without widgets support?

Yes. The default invocation is:

```
<ul>
<?php delicious_pp('accountname'); ?>
</ul>
```

Advanced invocations will be properly documented soon, but can be learned of by looking at the plugin source code.

## Can I display only bookmarks that belong to a given tag?

Yes. To accomplish that, go to *Presentation* > *Widgets* and fill in `username`/`tag` 
instead of `username` in the Username field. E.g., if your username is `johndoe`
and you want to display your bookmarks tagged `Cooking`, you should fill in the 
username field with `johndoe/Cooking`.
