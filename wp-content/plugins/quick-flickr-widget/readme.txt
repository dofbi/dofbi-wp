=== Quick Flickr Widget ===
Contributors: kovshenin
Donate link: http://kovshenin.com/wordpress/plugins/quick-flickr-widget/
Tags: flickr, photos, photo, gallery, widget, widgets, sidebar
Requires at least: 1.5
Tested up to: 2.8
Stable tag: 1.2.10

Display Flickr photos (by Flickr RSS or Flickr screen name) in your sidebar. Fully customizable HTML. Empowered by Thickbox!

== Description ==

As said in the description before, this plugin is a widget that can display up to 10 of your latest Flickr photos. The plugin is still in development, here are the features:

* Easy to setup and configure (feed with Flickr RSS or Screen name)
* Up to 20 Flickr photos in your sidebar or any other widgetized area
* Fully customizable widget (editable before.widget, after.widget, before.item, after.item, etc.)
* You can pick the photos display size: thumbnail, square, small or medium
* You can choose the _blank target to the flickr links
* Images are displayed with the Flickr photo description in ALT and TITLE attributes
* Ability to show titles next to your images
* Very easy to customize CSS
* Supports Thickbox!!
* Filter by tags now available!
* Ability to pick photos randomly
* Ability to use javascript instead of php (for those who had hosting issues, read the faq)

If you'd like to participate in the plugin development feel free to contact me, I'll be glad to share some thoughts and guide you into the current development stage..

And YES, I do consider feature requests, and that is what makes this plugin work. The discussions are here: [Quick Flickr Widget](http://kovshenin.com/wordpress/plugins/quick-flickr-widget/ "Quick Flickr Widget")

Oh, and a big shout out to [Donncha O Caoimh](http://ocaoimh.ie/ "Donncha O Caoimh") for his [Flickr Widget](http://wordpress.org/extend/plugins/flickr-widget/ "Flickr Widget"). Thanks mate!

== Installation ==

1. Upload archive contents to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Check out your sidebar widgets configuration

== Frequently Asked Questions ==

= PHP or JavaScript? =

If you haven't got any problems with PHP, then use PHP. JavaScript is for those who have hosting issues disallowing them the usage of the file_get_contents function.

= I entered my username but the Flickr images aren't showing up! =

Okay. Let's start off by saying that it's not your Flickr username that I need (unless you're using an RSS feed, in which case go ahead and bug me). And it's not your Yahoo ID! It's your Flickr screen name (in the [Your Account](http://www.flickr.com/account/ "Your Account") section). Next. Make sure that safe_mode is turned off on your hosting account, cause as far as I know, that disables cross-domain usage, and the only way out is to use javascript or frames, which is rediculous. Now, if that didn't help, then feel free to bug me on this page: [Quick Flickr Widget](http://kovshenin.com/wordpress/plugins/quick-flickr-widget/ "Quick Flickr Widget") ;) Cheers!

== Screenshots ==

1. This is the widget configuration options. As you can see it's HTML is fully customizable!
2. This is the output from my Flickr RSS feed in middle size.
3. Here's a screenshot of how it looks with the standard wordpress theme and small-sized flickr photos.
4. Oh. Did I mention that you can switch on the Thickbox effects? ;)

== Change log ==

= 1.2.10 =
16.06.2009. Minor js bugfix.

= 1.2.9 =
15.06.2009. Compatible with WordPress 2.8 (Thickbox, jQuery) and 1.5. Fixed some Javascript bugs. Photos titles in divs instead of spans.

= 1.2.8 =
20.05.2009. Whoops! Fixed the javascript problem ;)

= 1.2.7 =
27.04.2009. Increased maximum number of photos from 10 to 20. You can now use javascript instead of php! Under beta though ;)

= 1.2.6 =
13.04.2009. Added the "Random pick" ability using shuffle.

= 1.2.5 =
7.04.2009. Minor bugfixes (including the Thickbox siteurl issue). Added the ability to filter images by tags. Licensed under GPL v3.

= 1.2.4 =
2.04.2009. Now supports Thickbox effects! +minor bugfixes. Does not require JSON functions anymore, therefore works on PHP 4.

= 1.2.3 =
30.03.2009. Minor bug fixes. Using php format and eval() instaed of json and json_decode().

= 1.2.2 =
26.03.2009. Now supporthing both Flickr screen name and RSS feed in the widget configuration. Please note, that if you are using a Flickr RSS feed, then it SHOULD start with `http://api.flickr.com/services/feeds`.

= 1.2.1 =
24.03.2009. Took me a while to figure out the difference between username and screenname in the Flickr API. There was a bug in 1.2 when using screen names with spaces. Here's a fixed version. Special thanks to Tung Nguyen Thanh ;)

= 1.2 =
23.03.2009. Considered some feature requests. Okay, so I don't use RSS anymore, cause that sucks. Flickr has got an open API, so I use the REST interface to send requests and retrieve data in JSON format. It's much easier this way - no more useless regular expressions. In this version I don't even require you to go get your RSS feed link, all you need is a Flickr username and you're done. I make a Flickr API call to convert your username into an ID during widget configuration, then the requests from the widget are made by another API call using that ID. Does not require a Flickr API key.

= 1.1 =
13.03.2009. Yeah I know _blank targets totally suck and that it's very unkind of opening a new browser window (or tab) without users' permission, but anyways, somebody requested this so here you go.

= 1.0 =
6.03.2009. We got hosted at WordPress.org! Wohooo! Minor changes to the php file and restructured the readme.txt (and I actually renamed it to readme.txt, duh!)

= 1.0b =
2.03.2009. This is the start, so good luck to me and you all too. You may browse the source by the way, it's not too complicated yet.