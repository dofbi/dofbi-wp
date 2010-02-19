=== Simple Facebook Connect ===
Contributors: Otto
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom
Tags: facebook, connect, simple, otto, otto42, javascript, comments, share, status
Requires at least: 2.9.1
Tested up to: 2.9.1
Stable tag: 0.12

== Description ==

Simple Facebook Connect is a series of plugins that let you add any sort of Facebook Connect functionality you like to a WordPress blog. This lets you have an integrated site without a lot of coding, and still letting you customize it exactly the way you'd like.

First, you activate and set up the base plugin, which makes your site have basic Facebook Connect functionality. Then, each of the add-on plugins will let you add small pieces of specific Facebook-related functionality, one by one.

Requires WordPress 2.9.1 and PHP 5. 

Current add-ons:
* Comment using Facebook Identity (with FB avatar support)
* Automatically Publish new posts to Facebook Profile
* Manually Publish posts to FB Profile or Applicaton/Fan Page
* Login with your Facebook credentials
* New user registration with Facebook credentials
* Share button and Shortcode
* Connect Button Widget and Shortcode
* User Status Widget and Shortcode
* Live Stream Widget and Shortcode
* Bookmark Widget and Shortcode
* Find us on Facebook button Widget and Shortcode
* Fan Box Widget
* Fan Count Chicklet and Widget

Coming soon:
* Pull comments back from Facebook published posts into your site
* (Got more ideas? Tell me!)

If you have suggestions for a new add-on, feel free to email me at otto@ottodestruct.com .

Want regular updates? Become a fan of my site on Facebook!
http://www.facebook.com/apps/application.php?id=116002660893

== Installation ==

1. Upload the files to the `/wp-content/plugins/simple-facebook-connect/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Whoa, what's with all these plugins? =

The principle behind this plugin is to enable small pieces of Facebook Connect functionality, one at a time.

Thus, you have the base plugin, which does nothing except to enable your site for XFBML and Facebook Connect in general. It's required by all the other plugins.

Then you have individual plugins, one for each piece of functionality. One for enabling comments, one for adding a share button, etc. These are all smaller and simpler, for the most part, because they don't have to add all the Facebook Connect stuff that the base plugin adds.

= The comments plugin isn't working! =

You have to modify your theme to use the comments plugin.

In your comments.php file (or wherever your comments form is), you need to do the following.

1. Find the three inputs for the author, email, and url information. They need to have those ID's on the inputs (author, email, url). This is what the default theme and all standardized themes use, but some may be slightly different. You'll have to alter them to have these ID's in that case.

2. Just before the first input, add this code:
[div id="comment-user-details"]
[?php do_action('alt_comment_login'); ?]

(Replace the []'s with normal html greater/less than signs).

3. Just below the last input (not the comment text area, just the name/email/url inputs, add this:
[/div]

That will add the necessary pieces to allow the script to work.

Hopefully, a future version of WordPress will make this simpler.

= The plugin causes an error with a missing "json_encode"! =

I did say that this plugin was for WordPress 2.9 and up. 

If you are using PHP 5.2 and up, json_encode is built in. If not, then WordPress 2.9 contains a backwards compatible version of it. Either way, many of the plugins need this function in order to do the Facebook sharing functionality properly. 

Instead of defining the function itself, it's relying on your code already having it available, whether it's through PHP or WordPress. WordPress 2.8.5 does NOT have this function, so unless you're running PHP 5.2 or up, many of the add-on plugins will not work with that version of WordPress.

= The automatic "share" after the comments looks funky! Or, there's strange text showing up where it shouldn't be! =

I have encountered other WordPress plugins which mess with the output created by the_excerpt. Since I'm using this to create the content for this piece of the share part, it's possible that there is another plugin interfering.

To check this, view the source of the single post page. Near the bottom is a fair amount of javascript added by the comments plugin. One section in particular looks like this:
`var attachment = {
 'name':'Name of post',
 'href':'http://example.com/blog/2009/post-name/',
 'description':"Lorem ipsum dolor sit amet [...]",
 'caption':'{*actor*} left a comment on Name of post',
 'comments_xid':'http%3A%2F%2Fexample.com%2Fblog%2F2009%2Fpost-name%2F'
};`

If it looks messed up in any way, or if there's any extra text around that area, then you likely have some other plugin that breaks the usage of the_excerpt. You'll need to find and deactivate that plugin.

= The automatic "share" after the comments shows the wrong post! =

You have some plugin that is breaking the proper operation of The Loop. A lot of plugins that display some list of posts (like a list of most popular posts in the sidebar, or a recent comments list, etc) have been known to do this. These plugins were written incorrectly and need to be fixed. Look for updates to those plugins.

Note that you *must* fix these plugins, or remove them. They can cause other issues as well, like comments being attached to the wrong posts and other oddities along those lines. This has been a long standing problem with many WordPress plugins and even some themes. If you cannot find the culprit, try the WordPress support forums. Be prepared to post code from your theme so that somebody can solve the problem for you.

= Share doesn't work, Publish doesn't work =

Check the page source. If you have a message in there that looks like this: "Warning: Cannot modify header information - headers already sent by ... in .../wp-includes/class-json.php on line 238", then you need to upgrade WordPress to 2.9.1. WordPress 2.9 had a minor bug that manifested itself as this error in some cases.

= Facebook Avatars look wrong. =

Facebook avatars use slightly different code than other avatars. They should style the same, but not all themes will have this working properly, due to various theme designs and such. 

However, it is almost always possible to correct this with some simple CSS adjustments. For this reason, they are placed inside a div with an "fbavatar" class, for you to use to style them as you need. Just use .fbavatar in your CSS and add styling rules to correct those specific avatars.

= The login plugin won't let me connect my accounts! =

A new security feature in the login plugin is email validation. 

When you connect your account to Facebook, the plugin talks to Facebook behind the scenes and attempts to verify your email address. This means that your email on your WordPress account must match one of the email addresses attached to your Facebook account. If the matching process fails, you'll get an alert box telling you why it failed, and the accounts will not be linked.

This is a very alpha process and I can't be sure I've worked all the bugs out. If you have a problem and you know that your two accounts share the same email address, then email me directly with the problem and I'll try to help you out and fix the plugin. Please include screenshots of your email address in both WordPress and Facebook to prove you've checked that possible problem.

= Why does the comment plugin ask everybody to send them email? =

The comments plugin, as of version 0.10, asks for the Facebook user's permission to get their email address. Facebook uses a system called "proxy email", where it gives an email address back that is not the user's real email address, but which will forward emails to them. The comments plugin puts this information in the comment's email field. This is so that when you get the comment notification email, then the Reply-To section will actually work. You'll be able to reply back to the comment notification and the email will actually get to the user. This also lets plugins like Subscribe to Comments work.

Sometime soon, Facebook is planning on changing their Email API, so this functionality may change or break. The plugin will be kept up to date with any changes Facebook makes, however, the permissions dialog will very likely remain.

To disable this type permissions dialog, disable the option "Require Name and Email" in the WordPress Settings->Discussion screen.

= Why can't I automatically publish to my Application/Fan Page? =

Sorry, nothing I can do about it. There's a bug on Facebook preventing this: http://bugs.developers.facebook.com/show_bug.cgi?id=8184

When they fix it, I'll turn this feature on. The code is there and done, it just needs to have that bug fixed.

In the meantime, the manual publishing button for an Application or Fan Page works correctly. And automatic publishing of new posts to your personal profile works fine. Note: If you use a multi-user blog, this will publish to the user profile of whoever is publishing the post. That user must also have granted the extended permissions for it to work. Different people may see different settings there, so anybody doing publishing must have granted that permission if they want the post sent to Facebook automatically in any format.

= All the email addresses I get from this look like @proxy.facebook.com! =

Go to your FB Application, and edit the Settings. On the Advanced Page, there's a space for "email domain". Put your domain in there. Having that filled properly will give your users the ability to give you their real email addresses instead of the Facebook proxied ones.

= How do I use this Fanbox custom CSS option? =
Well, first you have to learn CSS.

Next, try starting with this code in the custom CSS box:
.connect_widget .connect_widget_facebook_logo_menubar {}
.fan_box .full_widget .connect_top {}
.fan_box .full_widget .page_stream {}
.fan_box .full_widget .connections {}

That should be enough to get you started.

== Screenshots ==

1. Simple Facebook Connect Main Admin Screen.
2. Facebook share button in action.
3. Facebook Comments login button.
4. Facebook Connect button on the Login Screen.
5. Facebook Publisher box in the Post editing screen.
6. Connecting a WordPress account to a Facebook Account on the Profiles Page.
7. Connected a WordPress account to a Facebook Account on the Profiles Page.
8. Share button configuration

== Changelog ==

= 0.12 =

* Fan Box custom CSS support.
* PHP 5 version checking as a base requirement. No way around this, Facebook's PHP libraries are PHP 5 and up only. PHP 4 is just dead.
* Login and Comments plugins add Facebook person extension data to Atom feeds, based on Friendfeed <a href="http://friendfeed.com/jessestay/0293c591/i-would-love-to-see-rss-and-or-atom-support">discussion</a>.
* Additional error checking to try to prevent odd PHP errors whenever Facebook's API goes wonky.
* Login now has an option to prevent people from disconnecting their WP and FB accounts. Add a "define('SFC_ALLOW_DISCONNECT',false); to your wp-config to prevent disconnection of accounts.
* Fixed logout bugs in Login plugin. Logout works correctly now.
* SSL Support. The base plugin now loads the scripts correctly for SSL connections. No guarantees, but it should work for SSL Admin users now.
* Added "Find us on Facebook" button in widget and shortcode form. Button links to your main Facebook App/Fan Page wall. Use [fb-find] in posts for shortcode.
* Automatic publishing to Fan Pages works now. Automatic publish to Application Walls does not work yet, due to Facebook bugs.
* Register plugin now has a "one-click" mode, to skip all prompting. Add "define ('SFC_REGISTER_TRANSPARENT', true);" to your wp-config to enable this mode. WARNING: May be buggy, not recommended for production sites.
* Minor speed enhancement that should fix some of the delays people see when logging in with FB on their sites.
* Height support in Fan Box shortcode.

= 0.11 =

* Fix html entities in publish dialogs.
* Publish plugin now supports automatic publishing! Look on the SFC settings page to grant permissions and enable automatic publishing.
* Real email address support in comments and register. You need to fill in the "Email Domain" on the FB Applications tab to be given a proper choice.
* Register plugin is now working. Requires login plugin to be enabled first.
* Publish plugin is now smarter and won't show you publishing buttons if you're not connected to Facebook.
* Published posts now also have a See Comments link on Facebook. 

= 0.10 =

* Fix quoting problems with publish and comments, for stream publishing (quote marks in titles and such shouldn't cause problems any more)
* Comment email improvement: If you have the "Comment author must fill out name and e-mail" checked in Settings->Discussion, the comments plugin will now ask the Facebook user for Permission to email that user. This will allow things like replying to the comment emails and Subscribe to Comments and similar plugins to work with Simple Facebook Connect. Yes, you can actually reply to the Facebook commenter when their comment gets emailed to you, and the reply *works*. Tested, proven.
* Comments plugin now uses comment meta table for storing FB user id, making for *much* quicker avatar generation. Avatars used to be built by getting FB UID from the email field, which took time for regex parsing. Old avatars will be auto-converted to new method when displayed. This also has an advantage in that there's now an 'fbuid' comment meta field on every facebook connected comment, to tie back to the author of the comment. 
* Comments now don't rely on Javascript quite so much. Facebook PHP code is used to get relevant data.
* Publish post-processing improvements, to try to get more images from the post content by using the_content filter.
* Publish button now shows "Fan Page" instead of "Application", if you're using a Fan Page.
* Made comment login button hook a bit more generic (anticipating a "Simple Twitter Connect" plugin).

= 0.9 = 

* Added share button type option.
* Improved login support. Now it verifies your users email address with Facebook before allowing them to connect their accounts. This ensures that at least they're using the same email on FB and on WP.
* Fixed problem with page reloading for no obvious reason (using different reload method for login plugin).
* Share button shortcode is now [fb-share] if you want to use that in a post.
* Added new Publisher button to publish to your own Facebook profile (this is the same as sharing the post with the share button, actually, but a few people requested it).
* Added Facebook logo checkbox to fanbox plugin.

= 0.8 =

* Added Fan Page support, for people who already have Fan Pages that they don't want to give up. I do not recommend using this option, but it's there if you really need it.
* Improved login capabilities. Now a Connect button shows on the login screen, and logging out actually logs you out properly.

= 0.7 =

* Added shortcode for fanbox widget. [fb-fanbox]. Optional parameters are stream (1 or 0), connections (int), and width (int).
* Added Application Secret field to main plugin. Login plugin will need it.
* Facebook login now partially working. If you connect your WP account to your FB account and you visit the wp-login page while logged into Facebook as well, you will get auto-logged into WordPress, without any prompting or intervention. This may not be 100% secure or safe, and I do not recommend using it at this point, it's for testing only. I would, however, appreciate feedback on the best way to implement this, sort of thing.

= 0.6 = 

* Added shortcode for live stream widget. [fb-livestream] will work in pages and posts. The width and height are optional parameters.
* Added shortcode for user status widget. [fb-userstatus profileid="12345"] will work similarly. The profileid is required.
* Added Connect button widget and shortcode [fb-connect].
* Added Bookmark button widget and shortcode [fb-bookmark].

= 0.5 =

* Live Stream widget
* Manual Publishing plugin. Lets you post links to your posts on the Facebook Application's Wall. These will show up as "updates" to Fans of your application (which makes the Fan Box widget more useful). Currently, this is manual in that it will only push posts to the Wall when you click the button on the Edit Post page and publish it there.

= 0.4 =

* Added Fan Box Widget
* Added new Application ID field to main plugin
* Minor internal reorganizing, for planned addons
* Decided to keep all the version numbers in sync

= 0.3 =

* Comment avatars working, beginnings of a Facebook login capability.

= 0.2.3 =

* Comments working now. Requires minor theme modifications to make it work.

= 0.2.2 =

* Support FBFoundations compatibility, to some extent (make it easier to switch)
* Correct minor errors

= 0.2.1 = 

* Add meta information to share button, so that stuff shows up nicely on Facebook.

= 0.2 =

* Functional enough to use. Barely. Comments still not working. Share button works. XFBML works.

= 0.1 =

* Pre-Alpha. DO NOT USE.