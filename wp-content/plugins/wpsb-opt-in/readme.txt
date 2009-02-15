=== Newsletter subscription optin module ===
Contributors: nonletter
Donate link: http://www.sendblaster.com/
Tags: sidebar, widget, newsletter, email, bulk email, management
Requires at least: 2.0.0
Tested up to: 2.5
Stable tag: 1.1.5

Widget ready sidebar form for newsletter management, single and double opt-in. Stores subscribed email addresses, bulk email softwares compatible.

== Description ==

Enables widget ready sidebar forms for newsletter subscription, single opt-in and double opt-in options. Stores subscribed email addresses, purges old email addresses. Customizable sidebar appearance, customizable texts and labels. Subscribe and Unsubscribe to newsletter. Add up to 15 custom fields to gather all the informations about your users that you need for your email marketing campaigns. 

= Emailing software related features =

Complatible with SendBlaster Pro [bulk email software](http://www.sendblaster.com "bulk email software") :

* [bulk email software download](http://www.sendblaster.com/free-bulk-emailer-download/ "bulk email software download")

This widget sidebar form for newsletter subscription is also compatible with SendBlaster Free [Newsletter software](http://www.sendblaster.com/newsletter-software-no-recurring-fees/ "newsletter software") and enables mailing list [Email merge](http://www.sendblaster.com/email-merge-personalized/ "email merge personalization").

= Plugin Features =

* Subscribe new members
* Unsubscribe old members
* Stores subscribed email addresses
* Purges old email addresses. 
* Customizable sidebar appearance, 
* Customizable texts and labels. 
* Adds up to 15 custom fields 

= Plugin Options =

* Mailbox setup for managing subscriptions
* Message to subscriber - subject
* Message to subscriber - content
* Double Opt-in
* Link Love (enable and disable)
* Front side messages
* Front side appearance and custom fields
* Full customizable language setup
* Temporary database storing for newly subscribed members
* Automatic temporary database cleanup


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `wpsb-opt-in.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the **Plugins** menu in WordPress Admin
3. You will find a new Section under 'Options' menu called 'Wp SendBlaster Opt-in'.
4. go to `Wp SendBlaster Opt-in` and set the same **managing email address** that you have inside your [bulk email software Manage subscriptions](http://www.sendblaster.com/bulk-email-software/wp-content/manage-subscriptions.gif "manage subscriptions help screenshot") menu 
5. inside `Presentation` menu in WordPress Admin, drag to your sidebar the newly added plugin

== Screenshots ==

1. This is the newsletter subscription plugin running inside WordPress sidebar
2. This is the mailing list widget options Admin Panel

== Frequently Asked Questions ==

= What's new in 1.1.5? Why should I upgrade? =

Man! We got the subscribe/UNSUBSCRIBE new feature! Let's give to your website a touch of class.

= I am unable to delete the temporary opted in users. I get this message: Cannot load wpsb-opt-in.php =

The wpsb-opt-in.php file must be in the plugins folder and not inside its own folder.

= Is it possible to have the newsletter inscription on a page and not on the sidebar? =

yes: to have the newsletter widget inside a post you must enable PHP execution inside post,

first install this PHPexec plugin

http://wordpress.org/extend/plugins/exec-php/

then write this code in your post:

`<? php wpsb_opt_in(); ?>`

