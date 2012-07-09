=== Facebook Wordpress Social Reader ===
Contributors: partydroid
Donate link: http://lolblog.net/
Tags: facebook, social
Requires at least: 3.3.1
Tested up to: 3.4.1
Stable tag: 3.4.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Facebook, the world's most popular social network, offers the Open Graph API a way of representing actions, objects and friendships online.  Readers who chose to do so will publish their reading activity on your blog to their Facebook timeline, a public log of activity, visible to all their friends.  

== Description ==

Facebook, the world's most popular social network, offers the Open Graph API a way of representing actions, objects and friendships online.  Readers who chose to do so will publish their reading activity on your blog to their Facebook timeline, a public log of activity, visible to all their friends.  

The system users both Facebook JS and Facebook PHP SDKs to publish this activity.  If you already use Facebook apps on your site, this should not alter their behavior.  However, you will need to use the same app-id.  

Users who do not have an existing Facebook app will need create one for the purpose of running this plugin. See [here](http://lolblog.net/creating-a-facebook-app) for guidance.

The plugin creates both a filter and a widget for your blog.  The filter creates an xfbml object at the begiining of a post on the single post view page.  This is optional, but a nice way to prompt users to log in.  The altertanative is the Widget which must sit in a sidebar on the view single post page.  Facebook platform policies require you to provide a 'log out' URL, this is done in the Widget.  It is highly recommended you include this on your view single post page.  The widget can be alterred and styled to preference. If your theme does not have a Sidebar for view single post page, you should the include the inline sidebar `wpfbplugin` where appropriate.  More details on this can be found on the wordpress developer site.  

== Prerequisits ==

* PHP Curl Extension and JSON extension
* Facebook Account


== Installation ==

1. Upload the `wpfb` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a Facebook App
4. Enter App Id and App Secret in settings page
5. Place widget in sidebar on 'view post' page

== Frequently Asked Questions ==

= How do I create a facebook app suitable for this =
See [here](http://lolblog.net/creating-a-facebook-app) for guidance

== Screenshots ==

1. [Facebook timeline activity](http://snip.so/UJ2G.png)

== Changelog ==

= 0.0.1 =
* Initial Release
