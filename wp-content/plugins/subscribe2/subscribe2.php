<?php
/*
Plugin Name: Subscribe2
Plugin URI: http://subscribe2.wordpress.com
Description: Notifies an email list when new entries are posted.
Version: 4.11
Author: Matthew Robinson
Author URI: http://subscribe2.wordpress.com
*/

/*
Copyright (C) 2006-8 Matthew Robinson
Based on the Original Subscribe2 plugin by 
Copyright (C) 2005 Scott Merrill (skippy@skippy.net)

This file is part of Subscribe2.

Subscribe2 is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Subscribe2 is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Subscribe2.  If not, see <http://www.gnu.org/licenses/>.
*/

// our version number. Don't touch this or any line below
// unless you know exacly what you are doing
define('S2VERSION', '4.11');
define('S2PATH', trailingslashit(dirname(__FILE__)));

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL')) {
	define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
}
if (!defined('WP_CONTENT_DIR')) {
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
}

// use Owen's excellent ButtonSnap library
if (!function_exists(buttonsnap_textbutton)) {
	require(WP_CONTENT_DIR . '/plugins/subscribe2/include/buttonsnap.php');
}

$mysubscribe2 = new s2class;
$mysubscribe2->s2init();

// start our class
class s2class {
// variables and constructor are declared at the end

	/**
	Load all our strings
	*/
	function load_strings() {
		// adjust the output of Subscribe2 here

		$this->please_log_in = "<p>" . __('To manage your subscription options please ', 'subscribe2') . "<a href=\"" . get_option('siteurl') . "/wp-login.php\">" . __('login', 'subscribe2') . "</a>.</p>";

		$this->use_profile_admin = "<p>" . __('You may manage your subscription options from your ', 'subscribe2') . "<a href=\"" . get_option('siteurl') . "/wp-admin/users.php?page=" . plugin_basename(__FILE__) . "\">" . __('profile', 'subscribe2') . "</a>.</p>";

		$this->use_profile_users = "<p>" . __('You may manage your subscription options from your ', 'subscribe2') . "<a href=\"" . get_option('siteurl') . "/wp-admin/profile.php?page=" . plugin_basename(__FILE__) . "\">" . __('profile', 'subscribe2') . "</a>.</p>";

		$this->confirmation_sent = "<p>" . __('A confirmation message is on its way!', 'subscribe2') . "</p>";

		$this->already_subscribed = "<p>" . __('That email address is already subscribed.', 'subscribe2') . "</p>";

		$this->not_subscribed = "<p>" . __('That email address is not subscribed.', 'subscribe2') . "</p>";

		$this->not_an_email = "<p>" . __('Sorry, but that does not look like an email address to me.', 'subscribe2') . "</p>";

		$this->barred_domain = "<p>" . __('Sorry, email addresses at that domain are currently barred due to spam, please use an alternative email address.', 'subscribe2') . "</p>";

		$this->error = "<p>" . __('Sorry, there seems to be an error on the server. Please try again later.', 'subscribe2') . "</p>";

		$this->mail_sent = "<p>" . __('Message sent!', 'subscribe2') . "</p>";

		$this->mail_failed = "<p>" . __('Message failed! Check your settings and check with your hosting provider', 'subscribe2') . "</p>";

		$this->form = "<form method=\"post\" action=\"\"><p>" . __('Your email:', 'subscribe2') . "&#160;<input type=\"text\" name=\"email\" value=\"\" size=\"20\" />&#160;<br /><input type=\"radio\" name=\"s2_action\" value=\"subscribe\" checked=\"checked\" /> " . __('Subscribe', 'subscribe2') . " <input type=\"radio\" name=\"s2_action\" value=\"unsubscribe\" /> " . __('Unsubscribe', 'subscribe2') . " &#160;<input type=\"submit\" value=\"" . __('Send', 'subscribe2') . "\" /></p></form>\r\n";

		// confirmation messages
		$this->no_such_email = "<p>" . __('No such email address is registered.', 'subscribe2') . "</p>";

		$this->added = "<p>" . __('You have successfully subscribed!', 'subscribe2') . "</p>";

		$this->deleted = "<p>" . __('You have successfully unsubscribed.', 'subscribe2') . "</p>";

		$this->confirm_subject = "[" . html_entity_decode(get_option('blogname')) . "] " . __('Please confirm your request', 'subscribe2');

		$this->remind_subject = "[" . html_entity_decode(get_option('blogname')) . "] " . __('Subscription Reminder', 'subscribe2');

		$this->subscribe = __('subscribe', 'subscribe2'); //ACTION replacement in subscribing confirmation email

		$this->unsubscribe = __('unsubscribe', 'subscribe2'); //ACTION replacement in unsubscribing in confirmation email

		// menu strings
		$this->options_saved = __('Options saved!', 'subscribe2');
		$this->options_reset = __('Options reset!', 'subscribe2');
	} // end load_strings()

/* ===== WordPress menu registration ===== */
	/**
	Hook the menu
	*/
	function admin_menu() {
		$s2management = add_management_page(__('Subscribers', 'subscribe2'), __('Subscribers', 'subscribe2'), "manage_options", __FILE__, array(&$this, 'manage_menu'));
		add_action("admin_print_scripts-$s2management", array(&$this, 'checkbox_form_js'));

		$s2options = add_options_page(__('Subscribe2 Options', 'subscribe2'), __('Subscribe2', 'subscribe2'), "manage_options", __FILE__, array(&$this, 'options_menu'));
		add_action("admin_print_scripts-$s2options", array(&$this, 'checkbox_form_js'));
		add_action("admin_print_scripts-$s2options", array(&$this, 'option_form_js'));
		add_filter('plugin_action_links', array(&$this, 'plugin_action'), -10, 2);

		$s2user = add_users_page(__('Your Subscriptions', 'subscribe2'), __('Your Subscriptions', 'subscribe2'), "read", __FILE__, array(&$this, 'user_menu'));
		add_action("admin_print_scripts-$s2user", array(&$this, 'checkbox_form_js'));

		add_submenu_page('post-new.php', __('Mail Subscribers', 'subscribe2'), __('Mail Subscribers', 'subscribe2'), "publish_posts", __FILE__, array(&$this, 'write_menu'));

		$s2nonce = md5('subscribe2');
	}

	/**
	Hook for Admin Drop Down Icons
	*/
	function ozh_s2_icon($hook) {
		if ($hook == plugin_basename(__FILE__)) {
			return WP_CONTENT_URL . '/plugins/subscribe2/include/email_edit.png';
		} else {
			return $hook;
		}
	}

	/**
	Insert Javascript into admin_header
	*/
	function checkbox_form_js() {
		wp_enqueue_script('s2_checkbox', WP_CONTENT_URL . '/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/include/s2_checkbox.js', array('jquery'), '1.0');
	}

	function option_form_js() {
		wp_enqueue_script('s2_edit', WP_CONTENT_URL . '/plugins/' . dirname( plugin_basename( __FILE__ ) ) . '/include/s2_edit.js', array('jquery'), '1.0');
	}

	function add_weekly_sched($sched) {
		$sched['weekly'] = array('interval' => 604800, 'display' => __('Once Weekly', 'subscribe2'));
		return $sched;
	}

/* ===== Install, upgrade, reset ===== */
	/**
	Install our table
	*/
	function install() {
		// include upgrade-functions for maybe_create_table;
		if (!function_exists('maybe_create_table')) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		$date = date('Y-m-d');
		$sql = "CREATE TABLE $this->public (
			id int(11) NOT NULL auto_increment,
			email varchar(64) NOT NULL default '',
			active tinyint(1) default 0,
			date DATE default '$date' NOT NULL,
			PRIMARY KEY (id) )";

		// create the table, as needed
		maybe_create_table($this->public, $sql);
		$this->reset();
	} // end install()

	/**
	Upgrade the database
	*/
	function upgrade() {
		global $wpdb;

		// include upgrade-functions for maybe_create_table;
		if (!function_exists('maybe_create_table')) {
			require_once(ABSPATH . 'wp-admin/install-helper.php');
		}
		$date = date('Y-m-d');
		maybe_add_column($this->public, 'date', "ALTER TABLE `$this->public` ADD `date` DATE DEFAULT '$date' NOT NULL AFTER `active`;");

		// let's take the time to check process registered users
		// existing public subscribers are subscribed to all categories
		$users = $wpdb->get_col("SELECT ID FROM $wpdb->users");
		if (!empty($users)) {
			foreach ($users as $user) {
				$this->register($user);
			}
		}
		// update the options table to serialized format
		$old_options = $wpdb->get_col("SELECT option_name from $wpdb->options where option_name LIKE 's2%' AND option_name != 's2_future_posts'");

		if (!empty($old_options)) {
			foreach ($old_options as $option) {
				$value = get_option($option);
				$option_array = substr($option, 3);
				$this->subscribe2_options[$option_array] = $value;
				delete_option($option);
			}
		}
		$this->subscribe2_options['version'] = S2VERSION;
		//double check that the options are in the database
		require(S2PATH . "include/options.php");
		update_option('subscribe2_options', $this->subscribe2_options);
	} // end upgrade()

	/**
	Reset our options
	*/
	function reset() {
		delete_option('subscribe2_options');
		wp_clear_scheduled_hook('s2_digest_cron');
		unset($this->subscribe2_options);
		require(S2PATH . "include/options.php");
		update_option('subscribe2_options', $this->subscribe2_options);
	} // end reset()

/* ===== mail handling ===== */
	/**
	Performs string substitutions for subscribe2 mail texts
	*/
	function substitute($string = '') {
		if ('' == $string) {
			return;
		}
		$string = str_replace("BLOGNAME", get_option('blogname'), $string);
		$string = str_replace("BLOGLINK", get_bloginfo('url'), $string);
		$string = str_replace("TITLE", stripslashes($this->post_title), $string);
		$string = str_replace("PERMALINK", $this->permalink, $string);
		$string = str_replace("MYNAME", stripslashes($this->myname), $string);
		$string = str_replace("EMAIL", $this->myemail, $string);
		$string = str_replace("AUTHORNAME", $this->authorname, $string);
		return $string;
	} // end substitute()

	/**
	Delivers email to recipients in HTML or plaintext
	*/
	function mail ($recipients = array(), $subject = '', $message = '', $type='text') {
		if ( (empty($recipients)) || ('' == $message) ) { return; }
		
		// Set sender details
		if ('' == $this->myname) {
			$admin = $this->get_userdata();
			$this->myname = html_entity_decode($admin->display_name);
			$this->myemail = $admin->user_email;
		}
		$headers = "From: \"" . $this->myname . "\" <" . $this->myemail . ">\n";
		$headers .= "Return-path: <" . $this->myemail . ">\n";
		$headers .= "Reply-To: \"" . $this->myname . "\" <" . $this->myemail . ">\n";
		$headers .= "X-Mailer:PHP" . phpversion() . "\n";
		$headers .= "Precedence: list\nList-Id: " . get_option('blogname') . "\n";

		if ('html' == $type) {
				// To send HTML mail, the Content-Type header must be set
				$headers .= "MIME-Version: 1.0\n";
				$headers .= "Content-Type: " . get_bloginfo('html_type') . "; charset=\"". get_bloginfo('charset') . "\"\n";
				$mailtext = "<html><head><title>" . $subject . "</title></head><body>" . $message . "</body></html>";
		} else {
				$headers .= "MIME-Version: 1.0\n";
				$headers .= "Content-Type: text/plain; charset=\"". get_bloginfo('charset') . "\"\n";
				$message = preg_replace('|&[^a][^m][^p].{0,3};|', '', $message);
				$message = preg_replace('|&amp;|', '&', $message);
				$mailtext = wordwrap(strip_tags($message), 80, "\n");
		}

		// Replace any escaped html symbols in subject
		$subject = html_entity_decode($subject);

		//  Construct BCC headers for sending or send individual emails
		$bcc = '';
		if ($this->subscribe2_options['bcclimit'] == 1) {
			// BCCLimit is 1 so send individual emails
			foreach ($recipients as $recipient) {
				$recipient = trim($recipient);
				// sanity check -- make sure we have a valid email
				if (!is_email($recipient)) { continue; }
				$status = @wp_mail($recipient, $subject, $mailtext, $headers);
			}
			// Sending completed so return $status
			return $status;
		} elseif ($this->subscribe2_options['bcclimit'] == 0) {
			// we're not using BCCLimit
			foreach ($recipients as $recipient) {
				$recipient = trim($recipient);
				// sanity check -- make sure we have a valid email
				if (!is_email($recipient)) { continue; }
				// and NOT the sender's email, since they'll get a copy anyway
				if ( (!empty($recipient)) && ($this->myemail != $recipient) ) {
					('' == $bcc) ? $bcc = "Bcc: $recipient" : $bcc .= ", $recipient";
					// Bcc Headers now constructed by phpmailer class
				}
			}
			$headers .= "$bcc\r\n";
		} else {
			// we're using BCCLimit
			$count = 1;
			$batch = array();
			foreach ($recipients as $recipient) {
				// advance the array pointer by one, for use down below
				// the array pointer _is not_ advanced by the foreach() loop itself
				next($recipients);
				$recipient = trim($recipient);
				// sanity check -- make sure we have a valid email
				if (!is_email($recipient)) { continue; }
				// and NOT the sender's email, since they'll get a copy anyway
				if ( (! empty($recipient)) && ($this->myemail != $recipient) ) {
					('' == $bcc) ? $bcc = "Bcc: $recipient" : $bcc .= ", $recipient";
					// Bcc Headers now constructed by phpmailer class
				}
				if ($this->subscribe2_options['bcclimit'] == $count) {
					$count = 1;
					$batch[] = $bcc;
					$bcc = '';
				} else {
					if (false == current($recipients)) {
						// we've reached the end of the subscriber list
						// add what we have to the batch, and move on
						$batch[] = $bcc;
						break;
					} else {
						$count++;
					}
				}
			}
		}
		// rewind the array, just to be safe
		reset($recipients);

		// actually send mail
		if (isset($batch)) {
			foreach ($batch as $bcc) {
					$newheaders = $headers . "$bcc\r\n";
					$status = @wp_mail($this->myemail, $subject, $mailtext, $newheaders);
			}
		} else {
			$status = @wp_mail($this->myemail, $subject, $mailtext, $headers);
		}
		return $status;
	} // end mail()

	/**
	Sends an email notification of a new post
	*/
	function publish($post = 0) {
		if (!$post) { return $post; }
		$s2mail = get_post_meta($post->ID, 's2mail', true);
		if (strtolower(trim($s2mail)) == 'no') { return $post; }

		// are we doing daily digests? If so, don't send anything now
		if ($this->subscribe2_options['email_freq'] != 'never') { return $post; }

		// is the current post a page
		// and should this not generate a notification email?
		if ( ($this->subscribe2_options['pages'] == 'no') && ($post->post_type == 'page') ) {
			return $post;
		}

		// is this post set in the future?
		if ($post->post_date > current_time('mysql')) {
			// bail out
			return $post;
		}

		//Are we sending notifications for password protected posts?
		if ( ($this->subscribe2_options['password'] == "no") && ($post->post_password != '') ) {
				return $post;
		}

		$post_cats = wp_get_post_categories($post->ID);
		$post_cats_string = implode(',', $post_cats);
		$check = false;
		// is the current post assigned to any categories
		// which should not generate a notification email?
		foreach (explode(',', $this->subscribe2_options['exclude']) as $cat) {
			if (in_array($cat, $post_cats)) {
				$check = true;
			}
		}
		// if so, bail out
		if ($check) {
			// hang on -- can registered users subscribe to
			// excluded categories?
			if ('0' == $this->subscribe2_options['reg_override']) {
				// nope? okay, let's leave
				return $post;
			}
		}

		// Are we sending notifications for Private posts?
		if ($this->subscribe2_options['private'] == "yes") {
			// don't send notification to public users
			$check = true;
		}

		// lets collect our public subscribers
		// and all our registered subscribers for these categories
		if (!$check) {
			// if this post is assigned to an excluded
			// category, then this test will prevent
			// the public from receiving notification
			$public = $this->get_public();
		}
		$registered = $this->get_registered("cats=$post_cats_string");

		// do we have subscribers?
		if ( (empty($public)) && (empty($registered)) ) {
			// if not, no sense doing anything else
			return $post;
		}
		// we set these class variables so that we can avoid
		// passing them in function calls a little later
		$this->post_title = $post->post_title;
		$this->permalink = "<a href=\"" . get_permalink($post->ID) . "\">" . get_permalink($post->ID) . "</a>";
		
		$author = get_userdata($post->post_author);
		$this->authorname = $author->display_name;

		// do we send as admin, or post author?
		if ('author' == $this->subscribe2_options['sender']) {
		// get author details
			$user =& $author;
		} else {
			// get admin details
			$user = $this->get_userdata();
		}
		$this->myemail = $user->user_email;
		$this->myname = html_entity_decode($user->display_name);
		// Get email subject
		$subject = stripslashes(strip_tags($this->substitute($this->s2_subject)));
		// Get the message template
		$mailtext = stripslashes($this->substitute($this->subscribe2_options['mailtext']));

		$plaintext = $post->post_content;
		if (function_exists('strip_shortcodes')) {
			$plaintext = strip_shortcodes($plaintext);
		}
		$content = apply_filters('the_content', $post->post_content);
		$content = str_replace("]]>", "]]&gt", $content);
		$excerpt = $post->post_excerpt;
		if ('' == $excerpt) {
			// no excerpt, is there a <!--more--> ?
			if (false !== strpos($plaintext, '<!--more-->')) {
				list($excerpt, $more) = explode('<!--more-->', $plaintext, 2);
				// strip leading and trailing whitespace
				$excerpt = strip_tags($excerpt);
				$excerpt = trim($excerpt);
			} else {
				// no <!--more-->, so grab the first 55 words
				$excerpt = strip_tags($plaintext);
				$excerpt_length = 55;
				$words = explode(' ', $excerpt, $excerpt_length + 1);
				if (count($words) > $excerpt_length) {
					array_pop($words);
					array_push($words, '[...]');
					$excerpt = implode(' ', $words);
				}
			}
		}

		// first we send plaintext summary emails
		$body = str_replace("POST", $excerpt, $mailtext);
		$registered = $this->get_registered("cats=$post_cats_string&format=text&amount=excerpt");
		if (empty($registered)) {
			$recipients = (array)$public;
		}
		elseif (empty($public)) {
			$recipients = (array)$registered;
		} else {
		$recipients = array_merge((array)$public, (array)$registered);
		}
		$this->mail($recipients, $subject, $body);
		// next we send plaintext full content emails
		$body = str_replace("POST", strip_tags($plaintext), $mailtext);
		$this->mail($this->get_registered("cats=$post_cats_string&format=text&amount=post"), $subject, $body);
		// finally we send html full content emails
		$body = str_replace("\r\n", "<br />\r\n", $mailtext);
		$body = str_replace("POST", $content, $body);
		$this->mail($this->get_registered("cats=$post_cats_string&format=html"), $subject, $body, 'html');
	} // end publish()

	/**
	Hook Subscribe2 into posts published via email
	*/
	function publish_phone($id) {
		if (!$id) { return; }

		$post = get_post($id);
		$this->publish($post);
		return $post;
	}

	/**
	Send confirmation email to the user
	*/
	function send_confirm($what = '', $is_remind = FALSE) {
		if ($this->filtered == 1) { return; }
		if ( (!$this->email) || (!$what) ) {
			return false;
		}
		$id = $this->get_id($this->email);
		if (!$id) {
			return false;
		}

		// generate the URL "?s2=ACTION+HASH+ID"
		// ACTION = 1 to subscribe, 0 to unsubscribe
		// HASH = md5 hash of email address
		// ID = user's ID in the subscribe2 table
		//use home instead of siteurl incase index.php is not in core wordpress directory
		$link = get_option('home') . "/?s2=";

		if ('add' == $what) {
			$link .= '1';
		} elseif ('del' == $what) {
			$link .= '0';
		}
		$link .= md5($this->email);
		$link .= $id;

		$admin = $this->get_userdata();
		$this->myname = html_entity_decode($admin->display_name);

		if ($is_remind == TRUE) {
			$body = $this->substitute(stripslashes($this->subscribe2_options['remind_email']));
			$subject = stripslashes($this->remind_subject);
		} else {
			$body = $this->substitute(stripslashes($this->subscribe2_options['confirm_email']));
			if ('add' == $what) {
				$body = str_replace("ACTION", $this->subscribe, $body);
			} elseif ('del' == $what) {
				$body = str_replace("ACTION", $this->unsubscribe, $body);
			}
			$subject = stripslashes($this->confirm_subject);
		}

		$body = str_replace("LINK", $link, $body);

		$mailheaders .= "From: \"" . $admin->display_name . "\" <" . $admin->user_email . ">\n";
		$mailheaders .= "Reply-To: \"" . $admin->display_name . "\" <" . $admin->user_email . ">\n";
		$mailheaders .= "Return-path: <" . $admin->user_email . ">\n";
		$mailheaders .= "X-Mailer:PHP" . phpversion() . "\n";
		$mailheaders .= "Precedence: list\nList-Id: " . get_option('blogname') . "\n";
		$mailheaders .= "MIME-Version: 1.0\n";
		$mailheaders .= "Content-Type: text/plain; charset=\"". get_bloginfo('charset') . "\"\n";

		return @wp_mail($this->email, $subject, $body, $mailheaders);
	} // end send_confirm()

/* ===== Subscriber functions ===== */
	/**
	Given a public subscriber ID, returns the email address
	*/
	function get_email ($id = 0) {
		global $wpdb;

		if (!$id) {
			return false;
		}
		return $wpdb->get_var("SELECT email FROM $this->public WHERE id=$id");
	} // end get_email

	/**
	Given a public subscriber email, returns the subscriber ID
	*/
	function get_id ($email = '') {
		global $wpdb;

		if (!$email) {
			return false;
		}
		return $wpdb->get_var("SELECT id FROM $this->public WHERE email='$email'");
	} // end get_id()

	/**
	Activate an email address
	If the address is not already present, it will be added
	*/
	function activate ($email = '') {
		global $wpdb;

		if ('' == $email) {
			if ('' != $this->email) {
				$email = $this->email;
			} else {
				return false;
			}
		}

		if (false !== $this->is_public($email)) {
			$check = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE user_email='$this->email'");
			if ($check) { return; }
			$wpdb->get_results("UPDATE $this->public SET active='1' WHERE email='$email'");
		} else {
			$wpdb->get_results("INSERT INTO $this->public (email, active, date) VALUES ('$email', '1', NOW())");
		}
	} // end activate()

	/**
	Add an unconfirmed email address to the subscriber list
	*/
	function add ($email = '') {
		if ($this->filtered ==1) { return; }
		global $wpdb;

		if ('' == $email) {
			if ('' != $this->email) {
				$email = $this->email;
			} else {
				return false;
			}
		}

		if (!is_email($email)) { return false; }

		if (false !== $this->is_public($email)) {
			$wpdb->get_results("UPDATE $this->public SET date=NOW() WHERE email='$email'");
		} else {
			$wpdb->get_results("INSERT INTO $this->public (email, active, date) VALUES ('$email', '0', NOW())");
		}
	} // end add()

	/**
	Remove a user from the subscription table
	*/
	function delete($email = '') {
		global $wpdb;

		if ('' == $email) {
			if ('' != $this->email) {
				$email = $this->email;
			} else {
				return false;
			}
		}

		if (!is_email($email)) { return false; }
		$wpdb->get_results("DELETE FROM $this->public WHERE email='$email'");
	} // end delete()

	/**
	Toggle a public subscriber's status
	*/
	function toggle($email = '') {
		global $wpdb;

		if ( ('' == $email) || (! is_email($email)) ) { return false; }

		// let's see if this is a public user
		$status = $this->is_public($email);
		if (false === $status) { return false; }

		if ('0' == $status) {
			$wpdb->get_results("UPDATE $this->public SET active='1' WHERE email='$email'");
		} else {
			$wpdb->get_results("UPDATE $this->public SET active='0' WHERE email='$email'");
		}
	} // end toggle()

	/**
	Send reminder email to unconfirmed public subscribers
	*/
	function remind($emails = '') {
		if ('' == $emails) { return false; }

		$admin = $this->get_userdata();
		$this->myname = $admin->display_name;
		
		$recipients = explode(",", $emails);
		if (!is_array($recipients)) { $recipients = (array)$recipients; }
		foreach ($recipients as $recipient) {
			$this->email = $recipient;
			$this->send_confirm('add', TRUE);
		}
	} //end remind()

	/**
	Check email is not from a barred domain
	*/
	function is_barred($email='') {
		$barred_option = $this->subscribe2_options['barred'];
		list($user, $domain) = split('@', $email);
		$bar_check = stristr($barred_option, $domain);
		
		if(!empty($bar_check)) {
			return true;
		} else {
			return false;
		}
	} //end is_barred()
	
	/**
	Confirm request from the link emailed to the user and email the admin
	*/
	function confirm($content = '') {
		global $wpdb;

		if (1 == $this->filtered) { return $content; }

		$code = $_GET['s2'];
		$action = intval(substr($code, 0, 1));
		$hash = substr($code, 1, 32);
		$code = str_replace($hash, '', $code);
		$id = intval(substr($code, 1));
		if ($id) {
			$this->email = $this->get_email($id);
			if ( (!$this->email) || ($hash !== md5($this->email)) ) {
				return $this->no_such_email;
			}
		} else {
			return $this->no_such_email;
		}

		if ('1' == $action) {
			// make this subscription active
			$this->activate();
			$this->message = $this->added;
			$subject = '[' . get_option('blogname') . '] ' . __('New subscriber', 'subscribe2');
			$message = $this->email . " " . __('subscribed to email notifications!', 'subscribe2');
			$recipients = $wpdb->get_col("SELECT DISTINCT(user_email) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key='" . $wpdb->prefix . "user_level' AND $wpdb->usermeta.meta_value='10'");
			$this->mail($recipients, $subject, $message);
			$this->filtered = 1;
		} elseif ('0' == $action) {
			// remove this subscriber
			$this->delete();
			$this->message = $this->deleted;
			$this->filtered = 1;
		}

		if ('' != $this->message) {
			return $this->message;
		}
	} // end confirm

	/**
	Is the supplied email address a public subscriber?
	*/
	function is_public($email = '') {
		global $wpdb;

		if ('' == $email) { return false; }

		$check = $wpdb->get_var("SELECT active FROM $this->public WHERE email='$email'");
		if ( ('0' == $check) || ('1' == $check) ) {
			return $check;
		} else {
			return false;
		}
	} // end is_public

	/**
	Is the supplied email address a registered user of the blog?
	*/
	function is_registered($email = '') {
		global $wpdb;

		if ('' == $email) { return false; }

		$check = $wpdb->get_var("SELECT email FROM $wpdb->users WHERE user_email='$email'");
		if ($check) {
			return true;
		} else {
			return false;
		}
	}

	/**
	Return an array of all the public subscribers
	*/
	function get_public ($confirmed = 1) {
		global $wpdb;
		if (1 == $confirmed) {
			if ('' == $this->all_public) {
				$this->all_public = $wpdb->get_col("SELECT email FROM $this->public WHERE active='1'");
			}
			return $this->all_public;
		} else {
			if ('' == $this->all_unconfirmed) {
				$this->all_unconfirmed = $wpdb->get_col("SELECT email FROM $this->public WHERE active='0'");
			}
			return $this->all_unconfirmed;
		}
	} // end get_public()

	/**
	Return an array of registered subscribers
	Collect all the registered users of the blog who are subscribed to the specified categories
	*/
	function get_registered ($args = '') {
		global $wpdb, $wp_version, $wpmu_version;

		// Is this WordPressMU or not?
		if  ( (isset($wpmu_version)) || (strpos($wp_version, 'wordpress-mu')) ) {
			$s2_mu = true;
		}

		$format = '';
		$amount = '';
		$cats = '';
		$subscribers = array();

		parse_str($args, $r);
		if (!isset($r['cats']))
			$r['cats'] = '';
		if (!isset($r['format']))
			$r['format'] = 'all';
		if (!isset($r['amount']))
			$r['amount'] = 'all';

		$JOIN = ''; $AND = '';
		// text or HTML subscribers
		if ('all' != $r['format']) {
			$JOIN .= "INNER JOIN $wpdb->usermeta AS b ON a.user_id = b.user_id ";
			$AND .= " AND b.meta_key='s2_format' AND b.meta_value=";
			if ('html' == $r['format']) {
				$AND .= "'html'";
			} elseif ('text' == $r['format']) {
				$AND .= "'text'";
			}
		}

		// full post or excerpt subscribers
		if ('all' != $r['amount']) {
			$JOIN .= "INNER JOIN $wpdb->usermeta AS c ON a.user_id = c.user_id ";
			$AND .= " AND c.meta_key='s2_excerpt' AND c.meta_value=";
			if ('excerpt' == $r['amount']) {
				$AND .= "'excerpt'";
			} elseif ('post' == $r['amount']) {
				$AND.= "'post'";
			}
		}

		// specific category subscribers
		if ('' != $r['cats']) {
			$JOIN .= "INNER JOIN $wpdb->usermeta AS d ON a.user_id = d.user_id ";
			foreach (explode(',', $r['cats']) as $cat) {
				('' == $and) ? $and = "d.meta_key='s2_cat$cat'" : $and .= " OR d.meta_key='s2_cat$cat'";
			}
			$AND .= " AND ($and)";
		}

		if ($s2_mu) {
			$sql = "SELECT a.user_id FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS e ON a.user_id = e.user_id " . $JOIN . "WHERE a.meta_key='" . $wpdb->prefix . "capabilities' AND e.meta_key='s2_subscribed'" . $AND;
		} else {
			$sql = "SELECT a.user_id FROM $wpdb->usermeta AS a " . $JOIN . "WHERE a.meta_key='s2_subscribed'" . $AND;
		}
		$result = $wpdb->get_col($sql);
		if ($result) {
			$ids = implode(',', $result);
			return $wpdb->get_col("SELECT user_email FROM $wpdb->users WHERE ID IN ($ids) AND user_activation_key = ''");
		}
	} // end get_registered()

	/**
	Collects the signup date for all public subscribers
	*/
	function signup_date($email = '') {
		if ('' == $email) { return false; }

		global $wpdb;
		if (!empty($this->signup_dates)) {
			return $this->signup_dates[$email];
		} else {
			$results = $wpdb->get_results("SELECT email, date FROM $this->public", ARRAY_N);
			foreach ($results as $result) {
				$this->signup_dates[$result[0]] = $result[1];
			}
			return $this->signup_dates[$email];
		}
	} // end signup_date()

	/**
	Create the appropriate usermeta values when a user registers
	If the registering user had previously subscribed to notifications, this function will delete them from the public subscriber list first
	*/
	function register ($user_id = 0, $wpreg = '') {
		global $wpdb;

		if (0 == $user_id) { return $user_id; }
		$user = get_userdata($user_id);
		$all_cats = get_categories(array('hide_empty' => false));

		if (0 == $this->subscribe2_options['reg_override']) {
			// registered users are not allowed to subscribe to
			// excluded categories
			$exclude = explode(',', $this->subscribe2_options['exclude']);
			foreach ($all_cats as $cat => $term_id) {
				if (in_array($all_cats[$cat]->term_id, $exclude)) {
					$cat = (int)$cat;
					unset($all_cats[$cat]);
				}
			}
		}

		foreach ($all_cats as $cat) {
			('' == $cats) ? $cats = "$cat->term_id" : $cats .= ",$cat->term_id";
		}

		if ('' == $cats) {
			// sanity check, might occur if all cats excluded and reg_override = 0
			return $user_id;
		}

		// has this user previously signed up for email notification?
		if (false !== $this->is_public($user->user_email)) {
			// delete this user from the public table, and subscribe them to all the categories
			$this->delete($user->user_email);
			update_usermeta($user_id, 's2_subscribed', $cats);
			foreach(explode(',', $cats) as $cat) {
				update_usermeta($user_id, 's2_cat' . $cat, "$cat");
			}
			update_usermeta($user_id, 's2_format', 'text');
			update_usermeta($user_id, 's2_excerpt', 'excerpt');
			update_usermeta($user_id, 's2_autosub', $this->subscribe2_options['autosub_def']);
		} else {
			// create post format entries for all users
			$check = get_usermeta($user_id, 's2_format');
			if (empty($check)) {
				if ('html' == $this->subscribe2_options['autoformat']) {
					update_usermeta($user_id, 's2_format', 'html');
					update_usermeta($user_id, 's2_excerpt', 'post');
				} elseif ('fulltext' == $this->subscribe2_options['autoformat']) {
					update_usermeta($user_id, 's2_format', 'text');
					update_usermeta($user_id, 's2_excerpt', 'post');
				} else {
					update_usermeta($user_id, 's2_format', 'text');
					update_usermeta($user_id, 's2_excerpt', 'excerpt');
				}
				update_usermeta($user_id, 's2_autosub', $this->subscribe2_options['autosub_def']);
			}
			// ensure existing subscriptions are not overwritten on upgrade
			$check = get_usermeta($user_id, 's2_subscribed');
			// if the are no existing subscriptions, create them based on admin options
			if (empty($check)) {
				// add entries by default if autosub is on
				if ( ('yes' == $this->subscribe2_options['autosub']) || (('wpreg' == $this->subscribe2_options['autosub']) && (1 == $wpreg)) ) {
					update_usermeta($user_id, 's2_subscribed', $cats);
						foreach(explode(',', $cats) as $cat) {
							update_usermeta($user_id, 's2_cat' . $cat, "$cat");
						}
				} else {
					update_usermeta($user_id, 's2_subscribed', '-1');
				}
			} else {
				update_usermeta($user_id, 's2_autosub', 'no');
			}
		}
		return $user_id;
	} // end register()

	/**
	Subscribe all registered users to category selected on Admin Manage Page
	*/
	function subscribe_registered_users ($emails = '', $cats = '') {
		if ( ('' == $emails) || ('' == $cats) ) { return false; }
		global $wpdb;
		
		$useremails = explode(",", $emails);
		$useremails = implode("', '", $useremails);

		$sql = "SELECT ID FROM $wpdb->users WHERE user_email IN ('$useremails')";
		$user_IDs = $wpdb->get_col($sql);
		if (!is_array($cats)) {
		 	$cats = array($cats);
		}
		
		foreach ($user_IDs as $user_ID) {	
			$old_cats = get_usermeta($user_ID, 's2_subscribed');
			if ($old_cats == '-1') {
				$old_cats = array();
			} else {
				$old_cats = explode(',', $old_cats);
			}
			if (!is_array($old_cats)) {
				$old_cats = array($old_cats);
			}
			$new = array_diff($cats, $old_cats);
			if (!empty($new)) {
				// add subscription to these cat IDs
				foreach ($new as $id) {
					update_usermeta($user_ID, 's2_cat' . $id, "$id");
				}
			}
			$newcats = array_merge($cats, $old_cats);
			update_usermeta($user_ID, 's2_subscribed', implode(',', $newcats));
		}
	} // end subscribe_registered_users

	/**
	Unsubscribe all registered users to category selected on Admin Manage Page
	*/
	function unsubscribe_registered_users ($emails = '', $cats = '') {
		if ( ('' == $emails) || ('' == $cats) ) { return false; }
		global $wpdb;
		
		$useremails = explode(",", $emails);
		$useremails = implode("', '", $useremails);

		$sql = "SELECT ID FROM $wpdb->users WHERE user_email IN ('$useremails')";
		$user_IDs = $wpdb->get_col($sql);
		if (!is_array($cats)) {
		 	$cats = array($cats);
		}
		
		foreach ($user_IDs as $user_ID) {	
			$old_cats = explode(',', get_usermeta($user_ID, 's2_subscribed'));
			if (!is_array($old_cats)) {
				$old_cats = array($old_cats);
			}
			$remain = array_diff($old_cats, $cats);
			if (!empty($remain)) {
				// remove subscription to these cat IDs and update s2_subscribed
				foreach ($cats as $id) {
					delete_usermeta($user_ID, 's2_cat' . $id);
				}
				update_usermeta($user_ID, 's2_subscribed', implode(',', $remain));
			} else {
				// remove subscription to these cat IDs and update s2_subscribed to ''
				foreach ($cats as $id) {
					delete_usermeta($user_ID, 's2_cat' . $id);
				}
				update_usermeta($user_ID, 's2_subscribed', '-1');
			}
		}
	} // end unsubscribe_registered_users

	/**
	Autosubscribe registered users to newly created categories
	if registered user has selected this option
	*/
	function autosub_new_category ($new_category='') {
		global $wpdb;

		$sql = "SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key='s2_autosub' AND $wpdb->usermeta.meta_value='yes'";
		$user_IDs = $wpdb->get_col($sql);
		if ('' == $user_IDs) { return; }

		foreach ($user_IDs as $user_ID) {	
			$old_cats = explode(',', get_usermeta($user_ID, 's2_subscribed'));
			if (!is_array($old_cats)) {
				$old_cats = array($old_cats);
			}
			// add subscription to these cat IDs
			update_usermeta($user_ID, 's2_cat' . $new_category, "$new_category");
			$newcats = array_merge($old_cats, (array)$new_category);
			update_usermeta($user_ID, 's2_subscribed', implode(',', $newcats));
		}
	} // end autosub_new_category

	/**
	Get admin data from record 1 or first user with admin rights
	*/
	function get_userdata() {
		global $wpdb, $userdata;

		// get details of current user
		$admin = &$userdata;

		// if current under is empty or isn't an administrator get user record 1
		if ( (empty($admin)) || $userdata->{$wpdb->prefix . "user_level"} != 10) {
			$admin = get_userdata(1);
		}

		// if user record 1 is empty grab the first admin from the database
		if (empty($admin)) {
			$sql = "SELECT DISTINCT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key='" . $wpdb->prefix . "user_level' AND $wpdb->usermeta.meta_value='10' LIMIT 1";
			$admin = get_userdata($wpdb->get_var($sql));
		}
		return $admin;
	} //end get_userdata
	
/* ===== Menus ===== */
	/**
	Our management page
	*/
	function manage_menu() {
		global $wpdb, $s2nonce;

		//Get Registered Subscribers for bulk management
		$registered = $this->get_registered();
		if (!empty($registered)) {
			$emails = implode(",", $registered);
		}

		// was anything POSTed ?
		if (isset($_POST['s2_admin'])) {
			check_admin_referer('subscribe2-manage_subscribers' . $s2nonce);
			if ($_POST['addresses']) {
				foreach (preg_split ("/[\s,]+/", $_POST['addresses']) as $email) {
						if (is_email($email)) {
						$this->activate($email);
					}
				}
				$_POST['what'] = 'confirmed';
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) subscribed!', 'subscribe2') . "</strong></p></div>";
			} elseif ($_POST['process']) {
				if ($_POST['delete']) {
					foreach ($_POST['delete'] as $address) {
						$this->delete($address);
					}
					echo "<div id=\"message\" class=\"updated fade\"><p><strong>" .  __('Address(es) deleted!', 'subscribe2') . "</strong></p></div>";
				}
				if ($_POST['confirm']) {
					foreach ($_POST['confirm'] as $address) {
						$this->toggle($address);
					}
					$message =  "<div id=\"message\" class=\"updated fade\"><p><strong>" .  __('Status changed!', 'subscribe2') . "</strong></p></div>";
				}
				if ($_POST['unconfirm']) {
					foreach ($_POST['unconfirm'] as $address) {
						$this->toggle($address);
					}
					$message =  "<div id=\"message\" class=\"updated fade\"><p><strong>" .  __('Status changed!', 'subscribe2') . "</strong></p></div>";
				}
				echo $message;
			} elseif ($_POST['searchterm']) {
				$confirmed = $this->get_public();
				$unconfirmed = $this->get_public(0);
				$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$registered);
				foreach ($subscribers as $subscriber) {
					if (is_numeric(stripos($subscriber, $_POST['searchterm']))) {
						$result[] = $subscriber;
					}
				}
			} elseif ($_POST['remind']) {
				$this->remind($_POST['reminderemails']);
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Reminder Email(s) Sent!', 'subscribe2') . "</strong></p></div>"; 
			} elseif ( ($_POST['register']) && ('subscribe' == $_POST['manage']) ) {
				$this->subscribe_registered_users($_POST['emails'], $_POST['category']);
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Registered Users Subscribed!', 'subscribe2') . "</strong></p></div>";
			} elseif ( ($_POST['register']) && ('unsubscribe' == $_POST['manage']) ) {
				$this->unsubscribe_registered_users($_POST['emails'], $_POST['category']);
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Registered Users Unsubscribed!', 'subscribe2') . "</strong></p></div>";
			}
		}

		//Get Public Subscribers once for filter
		$confirmed = $this->get_public();
		$unconfirmed = $this->get_public(0);
		// safety check for our arrays
		if ('' == $confirmed) { $confirmed = array(); }
		if ('' == $unconfirmed) { $unconfirmed = array(); }
		if ('' == $registered) { $registered = array(); }

		$reminderform = false;
		$urlpath = str_replace("\\", "/", S2PATH);
		$urlpath = trailingslashit(get_option('siteurl')) . substr($urlpath,strpos($urlpath, "wp-content/"));
		if (isset($_GET['s2page'])) {
			$page = (int) $_GET['s2page'];
		} else {
			$page = 1;
		}

		if (isset($_POST['what'])) {
			$page = 1;
			if ('all' == $_POST['what']) {
				$what = 'all';
				$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$registered);
			} elseif ('public' == $_POST['what']) {
				$what = 'public';
				$subscribers = array_merge((array)$confirmed, (array)$unconfirmed);
			} elseif ('confirmed' == $_POST['what']) {
				$what = 'confirmed';
				$subscribers = $confirmed;
			} elseif ('unconfirmed' == $_POST['what']) {
				$what = 'unconfirmed';
				$subscribers = $unconfirmed;
				if (!empty($subscribers)) {
					$reminderemails = implode(",", $subscribers);
					$reminderform = true;
				}
			} elseif (is_numeric($_POST['what'])) {
				$what = intval($_POST['what']);
				$subscribers = $this->get_registered("cats=$what");
			} elseif ('registered' == $_POST['what']) {
				$what = 'registered';
				$subscribers = $registered;
			}
		} elseif (isset($_GET['what'])) {
			if ('all' == $_GET['what']) {
				$what = 'all';
				$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$registered);
			} elseif ('public' == $_GET['what']) {
				$what = 'public';
				$subscribers = array_merge((array)$confirmed, (array)$unconfirmed);
			} elseif ('confirmed' == $_GET['what']) {
				$what = 'confirmed';
				$subscribers = $confirmed;
			} elseif ('unconfirmed' == $_GET['what']) {
				$what = 'unconfirmed';
				$subscribers = $unconfirmed;				
				if (!empty($subscribers)) {
					$reminderemails = implode(",", $subscribers);
					$reminderform = true;
				}
			} elseif (is_numeric($_GET['what'])) {
				$what = intval($_GET['what']);
				$subscribers = $this->get_registered("cats=$what");
			} elseif ('registered' == $_GET['what']) {
				$what = 'registered';
				$subscribers = $registered;
			}
		} else {
			$what = 'registered';
			$subscribers = $registered;
			if (empty($subscribers)) {
				$subscribers = $confirmed;
				$what = 'confirmed';
				if (empty($subscribers)) {
					$subscribers = $unconfirmed;
					$what = 'unconfirmed';
					if (empty($subscribers)) {
						$what = 'all';
					}
				}
			}
		}
		if ($_POST['searchterm']) {
			$subscribers = &$result;
		}

		if (!empty($subscribers)) {
			natcasesort($subscribers);
			// Displays a page number strip - adapted from code in Akismet
			$args['what'] = $what;
			$total_subscribers = count($subscribers);
			$total_pages = ceil($total_subscribers / 25);
			$strip = '';
			if ( $page > 1 ) {
				$args['s2page'] = $page - 1;
				$strip .=  '<a class="prev" href="' . clean_url(add_query_arg( $args )) . '">&laquo; '. __('Previous Page', 'subscribe2') .'</a>' . "\n";
			}
			if ( $total_pages > 1 ) {
				for ( $page_num = 1; $page_num <= $total_pages; $page_num++ ) {
					if ( $page == $page_num ) {
						$strip .=  "<strong>" . $page_num . "</strong>\n";
					} else {
						if ( $page_num < 3 || ( $page_num >= $page - 2 && $page_num <= $page + 2 ) || $page_num > $total_pages - 2 ) {
							$args['s2page'] = $page_num;
							$strip .= "<a class=\"page-numbers\" href=\"" . clean_url(add_query_arg($args)) . "\">" . $page_num . "</a>\n";
							$trunc = true;
						} elseif ( $trunc == true ) {
							$strip .= "...\n";
							$trunc = false;
						}
					}
				}
			}
			if ( ( $page ) * 25 < $total_subscribers ) {
				$args['s2page'] = $page + 1;
				$strip .=  "<a class=\"next\" href=\"" . clean_url(add_query_arg($args)) . "\">". __('Next Page', 'subscribe2') . " &raquo;</a>\n";
			}
		}

		// show our form
		echo "<form method=\"post\" action=\"\">\r\n";
		echo "<div class=\"wrap\">";
		echo "<h2>" . __('Subscribe Addresses', 'subscribe2') . "</h2>\r\n";
		if (function_exists('wp_nonce_field')) {
			wp_nonce_field('subscribe2-manage_subscribers' . $s2nonce);
		}
		echo "<p>" . __('Enter addresses, one per line or comma-separated', 'subscribe2') . "<br />\r\n";
		echo "<textarea rows=\"2\" cols=\"80\" name=\"addresses\"></textarea></p>\r\n";
		echo "<input type=\"hidden\" name=\"s2_admin\" />\r\n";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"subscribe\" value=\"" . __('Subscribe', 'subscribe2') . "\"/></p>\r\n";

		// subscriber lists
		echo "<h2>" . __('Subscribers', 'subscribe2') . "</h2>\r\n";
		echo "<br />";
		$this->display_subscriber_dropdown($what, __('Filter', 'subscribe2'));
		// show the selected subscribers
		$alternate = 'alternate';
		if (!empty($subscribers)) {
			echo "<p align=\"center\"><b>" . __('Registered on the left, confirmed in the middle, unconfirmed on the right', 'subscribe2') . "</b></p>\r\n";
			$exportcsv = implode(",\r\n", $subscribers);
			echo "<table cellpadding=\"2\" cellspacing=\"2\" width=\"100%\">";
			echo "<tr><td width=\"50%\"><input type=\"text\" name=\"searchterm\" value=\"\" />&nbsp;\r\n";
			echo "<input type=\"submit\" name=\"search\" value=\"" . __('Search Subscribers', 'subscribe2') . "\" class=\"button\" /></td>\r\n";
			echo "<td width=\"50%\" align=\"right\"><input type=\"hidden\" name=\"exportcsv\" value=\"" . $exportcsv . "\" />\r\n";
			echo "<input type=\"submit\" name=\"csv\" value=\"" . __('Save Emails to CSV File', 'subscribe2') . "\" class=\"button\" /></td></tr></table>\r\n";
			if ($reminderform) {
				echo "<input type=\"hidden\" name=\"reminderemails\" value=\"" . $reminderemails . "\" />\r\n";
				echo "<p class=\"submit\" align=\"right\" style=\"border-top:none;\"><input type=\"submit\" name=\"reminder\" value=\"" . __('Send Reminder Email', 'subscribe2') . "\" /></p>\r\n";
			}
			echo "<table width=\"100%\"><tr><td valign=\"bottom\">" . $strip . "</td>\r\n";
			echo "<td align=\"right\"><p class=\"submit\" align=\"right\" style=\"border-top: none;\"><input type=\"submit\" name=\"process\" value=\"" . __('Process', 'subscribe2') . "\" /></p>\r\n";
			echo "</td></tr></table>\r\n";
		}

		echo "<table cellpadding=\"2\" cellspacing=\"2\" width=\"100%\">";
		if (!empty($subscribers)) {
			$subscriber_chunks = array_chunk($subscribers, 25);
			$chunk = $page - 1;
			$subscribers = $subscriber_chunks[$chunk];
			echo "<tr class=\"$alternate\">\r\n";
			echo "<td width=\"88%\"></td>\r\n";
			echo "<td width=\"4%\" align=\"center\">";
			echo "<img src=\"" . $urlpath . "include/arrow_left.png\" alt=\"&lt;\" title=\"" . __('Confirm this email address', 'subscribe2') . "\" /></td>\r\n";
			echo "<td width=\"4%\" align=\"center\">";
			echo "<img src=\"" . $urlpath . "include/arrow_right.png\" alt=\"&gt;\" title=\"" . __('Unconfirm this email address', 'subscribe2') . "\" /></td>\r\n";
			echo "<td width=\"4%\" align=\"center\">";
			echo "<img src=\"" . $urlpath . "include/cross.png\" alt=\"X\" title=\"" . __('Delete this email address', 'subscribe2') . "\" /></td></tr>\r\n";
			echo "<tr><td align=\"right\"><strong>" . __('Select / Unselect All', 'subscribe2') . "</strong></td>\r\n";
			echo "<td align=\"center\"><input type=\"checkbox\" name=\"checkall\" value=\"confirm_checkall\" /></td>\r\n";
			echo "<td align=\"center\"><input type=\"checkbox\" name=\"checkall\" value=\"unconfirm_checkall\" /></td>\r\n";
			echo "<td align=\"center\"><input type=\"checkbox\" name=\"checkall\" value=\"delete_checkall\" /></td></tr>\r\n";
			foreach ($subscribers as $subscriber) {
				echo "<tr class=\"$alternate\" style=\"height:50px;\">";
				echo "<td";
				if (in_array($subscriber, $unconfirmed)) {
					echo " align=\"right\">";
				} elseif (in_array($subscriber, $confirmed)) {
					echo " align=\"center\">";
				} else {
					echo " align=\"left\" colspan=\"4\">";
				}
				echo "<a href=\"mailto:" . $subscriber . "\">" . $subscriber . "</a>\r\n";
				if (in_array($subscriber, $unconfirmed) || in_array($subscriber, $confirmed) ) {
					echo "(" . $this->signup_date($subscriber) . ")</td>\r\n";
					echo "<td align=\"center\">\r\n";
					if (in_array($subscriber, $confirmed)) {
						echo "</td><td align=\"center\">\r\n";
						echo "<input class=\"unconfirm_checkall\" title=\"" . __('Unconfirm this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"unconfirm[]\" value=\"" . $subscriber . "\" /></td>\r\n";
					} elseif (in_array($subscriber, $unconfirmed)) {
						echo "<input class=\"confirm_checkall\" title=\"" . __('Confirm this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"confirm[]\" value=\"" . $subscriber . "\" /></td>\r\n";
						echo "<td align=\"center\"></td>\r\n";
					}
					echo "<td align=\"center\">\r\n";
					echo "<p class=\"delete\">\r\n";					
					echo "<input  class=\"delete_checkall\" title=\"" . __('Delete this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"delete[]\" value=\"" . $subscriber . "\" />\r\n";
					echo "</p>";
				}
				echo "</td></tr>\r\n";
				('alternate' == $alternate) ? $alternate = '' : $alternate = 'alternate';
			}
		} else {
			if ($_POST['search']) {
				echo "<tr><td align=\"center\"><b>" . __('No matching subscribers found', 'subscribe2') . "</b></td></tr>\r\n";
			} else {
				echo "<tr><td align=\"center\"><b>" . __('NONE', 'subscribe2') . "</b></td></tr>\r\n";
			}
		}
		echo "</table>\r\n";
		if (!empty($subscribers)) {
			echo "<table width=\"100%\"><tr><td valign=\"bottom\">" . $strip . "</td>\r\n";
			echo "<td align=\"right\"><p class=\"submit\" align=\"right\" style=\"border-top: none;\"><input type=\"submit\" name=\"process\" value=\"" . __('Process', 'subscribe2') . "\" /></p>\r\n";
			echo "</td></tr></table>\r\n";
		}

		//show bulk managment form
		echo "<h2>" . __('Categories', 'subscribe2') . "</h2>\r\n";
		echo "<p>";
		echo __('Existing Registered Users can be automatically (un)subscribed to categories using this section.', 'subscribe2') . "<br />\r\n";
		echo "<strong><em style=\"color: red\">" . __('Consider User Privacy as changes cannot be undone', 'subscribe2') . "</em></strong><br />\r\n";
		echo "</p>";
		echo "<br />" . __('Action to perform', 'subscribe2') . ":\r\n";
		echo "<input type=\"radio\" name=\"manage\" value=\"subscribe\" checked=\"checked\" />" . __('Subscribe', 'subscribe2') . "\r\n";
		echo "<input type=\"radio\" name=\"manage\" value=\"unsubscribe\" />" . __('Unsubscribe', 'subscribe2') . "<br /><br />\r\n";
		echo "<input type=\"hidden\" name=\"emails\" value=\"$emails\" />\r\n";
		$this->display_category_form();
		echo "<p class=\"submit\"><input type=\"submit\" id=\"deletepost\" name=\"register\" value=\"" . __('Submit', 'subscribe2') . "\" /></p>";
		echo "</div></form>\r\n";

		include(ABSPATH . 'wp-admin/admin-footer.php');
		// just to be sure
		die;
	} // end manage_menu()

	/**
	Our options page
	*/
	function options_menu() {
		global $s2nonce;

		// was anything POSTed?
		if (isset($_POST['s2_admin'])) {
			check_admin_referer('subscribe2-options_subscribers' . $s2nonce);
			if ($_POST['reset']) {
				$this->reset();
				echo "<div id=\"message\" class=\"updated fade\"><p><strong>$this->options_reset</strong></p></div>";
			} elseif ($_POST['submit']) {
				// excluded categories
				if (!empty($_POST['category'])) {
					$exclude_cats = implode(',', $_POST['category']);
				} else {
					$exclude_cats = '';
				}
				$this->subscribe2_options['exclude'] = $exclude_cats;
				// allow override?
				(isset($_POST['reg_override'])) ? $override = '1' : $override = '0';
				$this->subscribe2_options['reg_override'] = $override;

				// show button?
				($_POST['show_button'] == '1') ? $showbutton = '1' : $showbutton = '0';
				$this->subscribe2_options['show_button'] = $showbutton;

				// show widget in Presentation->Widgets
				($_POST['widget'] == '1') ? $showwidget = '1' : $showwidget = '0';
				$this->subscribe2_options['widget'] = $showwidget;

				// BCClimit
				$this->subscribe2_options['bcclimit'] = $_POST['bcc'];
				$this->subscribe2_options['s2page'] = $_POST['page'];

				// send as author or admin?
				$sender = 'author';
				if ('admin' == $_POST['sender']) {
					$sender = 'admin';
				}
				$this->subscribe2_options['sender'] = $sender;

				// send email for pages, private and password protected posts
				$this->subscribe2_options['pages']= $_POST['pages'];
				$this->subscribe2_options['password']= $_POST['password'];
				$this->subscribe2_options['private'] = $_POST['private'];

				// send per-post or digest emails
				$email_freq = $_POST['email_freq'];
				$scheduled_time = wp_next_scheduled('s2_digest_cron');
				if ( ($email_freq != $this->subscribe2_options['email_freq']) || ($_POST['hour'] != gmdate('H', $scheduled_time)) ) {
					$this->subscribe2_options['email_freq'] = $email_freq;
					wp_clear_scheduled_hook('s2_digest_cron');
					$scheds = (array)wp_get_schedules();
					$interval = ( isset($scheds[$email_freq]['interval']) ) ? (int) $scheds[$email_freq]['interval'] : 0;
					if ($interval == 0) {
						// if we are on per-post emails remove last_cron entry
						unset($this->subscribe2_options['last_s2cron']);
					} else {
						// if we are using digest schedule the event and prime last_cron as now
						$time =  current_time('timestamp') + $interval;
						if ($interval < 86400) {
							// Schedule CRON events occurring less than daily starting now and periodically thereafter
							$timestamp = &$time;
						} else {
							// Schedule other CRON events starting at user defined hour and periodically thereafter
							$timestamp = gmmktime($_POST['hour'], 0, 0, gmdate('m', $time), gmdate('d', $time), gmdate('Y', $time));
						}
						wp_schedule_event($timestamp, $email_freq, 's2_digest_cron');
						if (!isset($this->subscribe2_options['last_s2cron'])) {
							$this->subscribe2_options['last_s2cron'] = current_time('mysql');
						}
					}
				}

				// email templates
				$this->subscribe2_options['mailtext'] = $_POST['mailtext'];
				$this->subscribe2_options['confirm_email'] = $_POST['confirm_email'];
				$this->subscribe2_options['remind_email'] = $_POST['remind_email'];

				//automatic subscription
				$this->subscribe2_options['autosub'] = $_POST['autosub'];
				$this->subscribe2_options['wpregdef'] = $_POST['wpregdef'];
				$this->subscribe2_options['autoformat'] = $_POST['autoformat'];
				$this->subscribe2_options['autosub_def'] = $_POST['autosub_def'];
				
				//barred domains
				$this->subscribe2_options['barred'] = $_POST['barred'];

				echo "<div id=\"message\" class=\"updated fade\"><p><strong>$this->options_saved</strong></p></div>";
				update_option('subscribe2_options', $this->subscribe2_options);
			}
		}
		// show our form
		echo "<div class=\"wrap\">";
		echo "<form method=\"post\" action=\"\">\r\n";
		if (function_exists('wp_nonce_field')) {
			wp_nonce_field('subscribe2-options_subscribers' . $s2nonce);
		}
		echo "<input type=\"hidden\" name=\"s2_admin\" value=\"options\" />\r\n";
		echo "<h2>" . __('Delivery Options', 'subscribe2') . ":</h2>\r\n";
		echo "<p>";
		echo "<input type=\"hidden\" id=\"jsbcc\" value=\"" . $this->subscribe2_options['bcclimit'] . "\" />";
		echo "<input type=\"hidden\" id=\"jspage\" value=\"" . $this->subscribe2_options['s2page'] . "\" />";

		echo __('Restrict the number of recpients per email to (0 for unlimited)', 'subscribe2') . ': ';
		echo "<span id=\"s2bcc_1\"><span id=\"s2bcc\" style=\"background-color: #FFFBCC\">" . $this->subscribe2_options['bcclimit'] . "</span> ";
		echo "<a href=\"#\" onclick=\"s2_show('bcc')\">" . __('Edit', 'subscribe2') . "</a></span>\n";
		echo "<span id=\"s2bcc_2\">\r\n";
		echo "<input type=\"text\" name=\"bcc\" value=\"" . $this->subscribe2_options['bcclimit'] . "\" size=\"3\" />\r\n";
		echo "<a href=\"#\" onclick=\"s2_update('bcc');\">". __('Update', 'subscribe2') . "</a>\n";
		echo "<a href=\"#\" onclick=\"s2_revert('bcc');\">". __('Revert', 'subscribe2') . "</a></span>\n";

		echo "<br /><br />" . __('Set default Subscribe2 page as ID', 'subscribe2') . ': ';
		echo "<span id=\"s2page_1\"><span id=\"s2page\" style=\"background-color: #FFFBCC\">" . $this->subscribe2_options['s2page'] . "</span> ";
		echo "<a href=\"#\" onclick=\"s2_show('page')\">" . __('Edit', 'subscribe2') . "</a></span>\n";
		echo "<span id=\"s2page_2\">\r\n";
		echo "<input type=\"text\" name=\"page\" value=\"" . $this->subscribe2_options['s2page'] . "\" size=\"3\" />\r\n";
		echo "<a href=\"#\" onclick=\"s2_update('page');\">". __('Update', 'subscribe2') . "</a>\n";
		echo "<a href=\"#\" onclick=\"s2_revert('page');\">". __('Revert', 'subscribe2') . "</a></span>\n";

		echo "<br /><br />" . __('Send Emails for Pages', 'subscribe2') . ': ';
		echo "<input type=\"radio\" name=\"pages\" value=\"yes\"";
		if ('yes' == $this->subscribe2_options['pages']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Yes', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"pages\" value=\"no\"";
		if ('no' == $this->subscribe2_options['pages']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('No', 'subscribe2') . "<br /><br />\r\n";
		echo __('Send Emails for Password Protected Posts', 'subscribe2') . ': ';
		echo "<input type=\"radio\" name=\"password\" value=\"yes\"";
		if ('yes' == $this->subscribe2_options['password']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Yes', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"password\" value=\"no\"";
		if ('no' == $this->subscribe2_options['password']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('No', 'subscribe2') . "<br /><br />\r\n";
		echo __('Send Emails for Private Posts', 'subscribe2') . ': ';
		echo "<input type=\"radio\" name=\"private\" value=\"yes\"";
		if ('yes' == $this->subscribe2_options['private']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Yes', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"private\" value=\"no\"";
		if ('no' == $this->subscribe2_options['private']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('No', 'subscribe2') . "<br /><br />\r\n";
		echo __('Send Email From', 'subscribe2') . ': ';
		echo "<input type=\"radio\" name=\"sender\" value=\"author\"";
		if ('author' == $this->subscribe2_options['sender']) {
			echo " checked=\"checked\" ";
		}
		echo " /> " . __('Author of the post', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"sender\" value=\"admin\"";
		if ('admin' == $this->subscribe2_options['sender']) {
			echo " checked=\"checked\" ";
		}
		echo " /> " . __('Blog Admin', 'subscribe2') . "<br /><br />\r\n";
		if (function_exists('wp_schedule_event')) {
			echo __('Send Email as Digest', 'subscribe2') . ": <br /><br />\r\n";
			$this->display_digest_choices();
		}
		echo "</p>";
		echo "<h2>" . __('Email Templates', 'subscribe2') . "</h2>\r\n";
		echo"<br />";
		echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"1\" class=\"editform\">\r\n";
		echo "<tr><td>";
		echo __('New Post email (must not be empty)', 'subscribe2') . ":";
		echo "<br />\r\n";
		echo "<textarea rows=\"9\" cols=\"60\" name=\"mailtext\">" . stripslashes($this->subscribe2_options['mailtext']) . "</textarea><br /><br />\r\n";
		echo "</td><td valign=\"top\" rowspan=\"3\">";
		echo "<h3>" . __('Message substitions', 'subscribe2') . "</h3>\r\n";
		echo "<dl>";
		echo "<dt><b>BLOGNAME</b></dt><dd>" . get_bloginfo('name') . "</dd>\r\n";
		echo "<dt><b>BLOGLINK</b></dt><dd>" . get_bloginfo('url') . "</dd>\r\n";
		echo "<dt><b>TITLE</b></dt><dd>" . __("the post's title<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>POST</b></dt><dd>" . __("the excerpt or the entire post<br />(<i>based on the subscriber's preferences</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>TABLE</b></dt><dd>" . __("a list of post titles<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>PERMALINK</b></dt><dd>" . __("the post's permalink<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>MYNAME</b></dt><dd>" . __("the admin or post author's name", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>EMAIL</b></dt><dd>" . __("the admin or post author's email", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>AUTHORNAME</b></dt><dd>" . __("the post author's name", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>LINK</b></dt><dd>" . __("the generated link to confirm a request<br />(<i>only used in the confirmation email template</i>)", 'subscribe2') . "</dd>\r\n";
		echo "<dt><b>ACTION</b></dt><dd>" . __("Action performed by LINK in confirmation email<br />(<i>only used in the confirmation email template</i>)", 'subscribe2') . "</dd>\r\n";
		echo "</dl></td></tr><tr><td>";
		echo __('Subscribe / Unsubscribe confirmation email', 'subscribe2') . ":<br />\r\n";
		echo "<textarea rows=\"9\" cols=\"60\" name=\"confirm_email\">" . stripslashes($this->subscribe2_options['confirm_email']) . "</textarea><br /><br />\r\n";
		echo "</td></tr><tr valign=\"top\"><td>";
		echo __('Reminder email to Unconfirmed Subscribers', 'subscribe2') . ":<br />\r\n";
		echo "<textarea rows=\"9\" cols=\"60\" name=\"remind_email\">" . stripslashes($this->subscribe2_options['remind_email']) . "</textarea><br /><br />\r\n";
		echo "</td></tr></table><br />\r\n";

		// excluded categories
		echo "<h2>" . __('Excluded Categories', 'subscribe2') . "</h2>\r\n";
		echo"<p>";
		echo "<strong><em style=\"color: red\">" . __('Posts assigned to any Excluded Category do not generate notifications and are not included in digest notifications', 'subscribe2') . "</em></strong><br />\r\n";
		echo"</p>";
		$this->display_category_form(explode(',', $this->subscribe2_options['exclude']));
		echo "<center><input type=\"checkbox\" name=\"reg_override\" value=\"1\"";
		if ('1' == $this->subscribe2_options['reg_override']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Allow registered users to subscribe to excluded categories?', 'subscribe2') . "</center><br />\r\n";

		// show button in QuickTags
		echo "<h2>" . __('Writing Options', 'subscribe2') . "</h2>\r\n";
		echo"<p>";
		echo "<input type=\"checkbox\" name=\"show_button\" value=\"1\"";
		if ('1' == $this->subscribe2_options['show_button']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Show the Subscribe2 button on the Write toolbar?', 'subscribe2') . "<br /><br />\r\n";

		// show Widget
		echo "<input type=\"checkbox\" name=\"widget\" value=\"1\"";
		if ('1' == $this->subscribe2_options['widget']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Enable Subscribe2 Widget?', 'subscribe2') . "<br /><br />\r\n";
		echo"</p>";

		//Auto Subscription for new registrations
		echo "<h2>" . __('Auto Subscribe', 'subscribe2') . "</h2>\r\n";
		echo"<p>";
		echo __('Subscribe new users registering with your blog', 'subscribe2') . ":<br />\r\n";
		echo "<input type=\"radio\" name=\"autosub\" value=\"yes\"";
		if ('yes' == $this->subscribe2_options['autosub']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Automatically', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"autosub\" value=\"wpreg\"";
		if ('wpreg' == $this->subscribe2_options['autosub']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('Display option on Registration Form', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"autosub\" value=\"no\"";
		if ('no' == $this->subscribe2_options['autosub']) {
			echo " checked=\"checked\"";
		}
		echo " /> " . __('No', 'subscribe2') . "<br /><br />\r\n";
		echo __('Registration Form option is checked by default', 'subscribe2') . ": &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"wpregdef\" value=\"yes\"";
		if ('yes' == $this->subscribe2_options['wpregdef']) {
			echo " checked=\"checked\"";
		}
		echo " />" . __('Yes', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"wpregdef\" value=\"no\"";
		if ('no' == $this->subscribe2_options['wpregdef']) {
			echo " checked=\"checked\"";
		}
		echo " />" . __('No', 'subscribe2') . "<br /><br />\r\n";
		echo __('Auto-subscribe users to receive email as', 'subscribe2') . ": <br />\r\n";
		echo "<input type=\"radio\" name=\"autoformat\" value=\"html\"";
		if ('html' == $this->subscribe2_options['autoformat']) {
			echo "checked=\"checked\" ";
		}
		echo "/> " . __('HTML', 'subscribe2') ." &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"autoformat\" value=\"fulltext\" ";
		if ('fulltext' == $this->subscribe2_options['autoformat']) {
			echo "checked=\"checked\" ";
		}
		echo "/> " . __('Plain Text - Full', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"autoformat\" value=\"text\" ";
		if ('text' == $this->subscribe2_options['autoformat']) {
			echo "checked=\"checked\" ";
		}
		echo "/> " . __('Plain Text - Excerpt', 'subscribe2') . " <br /><br />";
		echo __('Auto Subscribe me to new categories is checked by default', 'subscribe2') . ": <br />\r\n";
		echo "<input type=\"radio\" name=\"autosub_def\" value=\"yes\"";
		if ('yes' == $this->subscribe2_options['autosub_def']) {
			echo " checked=\"checked\"";
		}
		echo " />" . __('Yes', 'subscribe2') . " &nbsp;&nbsp;";
		echo "<input type=\"radio\" name=\"autosub_def\" value=\"no\"";
		if ('no' == $this->subscribe2_options['autosub_def']) {
			echo " checked=\"checked\"";
		}
		echo " />" . __('No', 'subscribe2');
		echo"</p>";

		//barred domains
		echo "<h2>" . __('Barred Domains', 'subscribe2') . "</h2>\r\n";
		echo"<p>";
		echo __('Enter domains to bar from public subscriptions: <br /> (Use a new line for each entry and omit the "@" symbol, for example email.com)', 'subscribe2');
		echo "<br />\r\n<textarea style=\"width: 98%;\" rows=\"4\" cols=\"60\" name=\"barred\">" . $this->subscribe2_options['barred'] . "</textarea>";
		echo"</p>";

		// submit
		echo "<p class=\"submit\" align=\"center\"><input type=\"submit\" id=\"save\" name=\"submit\" value=\"" . __('Submit', 'subscribe2') . "\" /></p>";

		// reset
		echo "<h2>" . __('Reset Default', 'subscribe2') . "</h2>\r\n";
		echo "<p>" . __('Use this to reset all options to their defaults. This <strong><em>will not</em></strong> modify your list of subscribers.', 'subscribe2') . "</p>\r\n";
		echo "<p class=\"submit\" align=\"center\">";
		echo "<input type=\"submit\" id=\"deletepost\" name=\"reset\" value=\"" . __('RESET', 'subscribe2') .
		"\" />";
		echo "</p></form></div>\r\n";

		include(ABSPATH . 'wp-admin/admin-footer.php');
		// just to be sure
		die;
	} // end options_menu()

	/**
	Our profile menu
	*/
	function user_menu() {
		global $user_ID, $s2nonce, $wp_version, $wpmu_version;

		get_currentuserinfo();

		// was anything POSTed?
		if ( (isset($_POST['s2_admin'])) && ('user' == $_POST['s2_admin']) ) {
			check_admin_referer('subscribe2-user_subscribers' . $s2nonce);

			// Is this WordPressMU or not?
			if  ( (isset($wpmu_version)) || (strpos($wp_version, 'wordpress-mu')) ) {
				$s2_mu = true;
			}

			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Subscription preferences updated.', 'subscribe2') . "</strong></p></div>\n";
			$format = 'text';
			$post = 'post';
			if ('html' == $_POST['s2_format']) {
				$format = 'html';
			}
			if ('excerpt' == $_POST['s2_excerpt']) {
				$post = 'excerpt';
			}
			update_usermeta($user_ID, 's2_excerpt', $post);
			update_usermeta($user_ID, 's2_format', $format);
			update_usermeta($user_ID, 's2_autosub', $_POST['new_category']);

			if ($s2_mu) {
				$posted_cats = $_POST['category'];
				$other_blogs = get_usermeta($user_ID, 's2_subscribed');
				if ($other_blogs == '-1') {
					$other_blogs = array();
				} else {
					$other_blogs = array_diff(explode(',', $other_blogs), get_all_category_ids());
				}
				if (empty($posted_cats)) {
					$cats = $other_blogs;
				} else {
					$cats = array_merge($posted_cats, $other_blogs);
				}
			} else {
				$cats = $_POST['category'];
			}

			if ( (empty($cats)) || ($cats == '-1') ) {
				$oldcats = explode(',', get_usermeta($user_ID, 's2_subscribed'));
				if ($oldcats) {
					foreach ($oldcats as $cat) {
						delete_usermeta($user_ID, "s2_cat" . $cat);
					}
				}
				update_usermeta($user_ID, 's2_subscribed', '-1');
			} elseif ($cats == 'digest') {
				$all_cats = get_categories(array('hide_empty' => false));
				foreach ($all_cats as $cat) {
					('' == $catids) ? $catids = "$cat->term_id" : $catids .= ",$cat->term_id";
					update_usermeta($user_ID, 's2_cat' . $cat->term_id, $cat->term_id);
				}
				update_usermeta($user_ID, 's2_subscribed', $catids);
			} else {
				 if (!is_array($cats)) {
				 	$cats = array($_POST['category']);
				}
				$old_cats = explode(',', get_usermeta($user_ID, 's2_subscribed'));
				$remove = array_diff($old_cats, $cats);
				$new = array_diff($cats, $old_cats);
				if (!empty($remove)) {
					// remove subscription to these cat IDs
					foreach ($remove as $id) {
						delete_usermeta($user_ID, "s2_cat" . $id);
					}
				}
				if (!empty($new)) {
					// add subscription to these cat IDs
					foreach ($new as $id) {
						update_usermeta($user_ID, 's2_cat' . $id, $id);
					}
				}
				update_usermeta($user_ID, 's2_subscribed', implode(',', $cats));
			}
		}

		// show our form
		echo "<div class=\"wrap\">";
		echo "<h2>" . __('Notification Settings', 'subscribe2') . "</h2>\r\n";
		echo "<form method=\"post\" action=\"\">";
		echo "<p>";
		if (function_exists('wp_nonce_field')) {
			wp_nonce_field('subscribe2-user_subscribers' . $s2nonce);
		}
		echo "<input type=\"hidden\" name=\"s2_admin\" value=\"user\" />";
		if ($this->subscribe2_options['email_freq'] == 'never') {
			echo __('Receive email as', 'subscribe2') . ": &nbsp;&nbsp;";
			echo "<input type=\"radio\" name=\"s2_format\" value=\"html\"";
			if ('html' == get_usermeta($user_ID, 's2_format')) {
				echo "checked=\"checked\" ";
			}
			echo "/> " . __('HTML', 'subscribe2') ." &nbsp;&nbsp;";
			echo "<input type=\"radio\" name=\"s2_format\" value=\"text\" ";
			if ('text' == get_usermeta($user_ID, 's2_format')) {
				echo "checked=\"checked\" ";
			}
			echo "/> " . __('Plain Text', 'subscribe2') . "<br /><br />\r\n";

			echo __('Email contains', 'subscribe2') . ": &nbsp;&nbsp;";
			$amount = array('excerpt' => __('Excerpt Only', 'subscribe2'), 'post' => __('Full Post', 'subscribe2'));
			foreach ($amount as $key => $value) {
				echo "<input type=\"radio\" name=\"s2_excerpt\" value=\"" . $key . "\"";
				if ($key == get_usermeta($user_ID, 's2_excerpt')) {
					echo " checked=\"checked\"";
				}
				echo " /> " . $value . "&nbsp;&nbsp;";
			}
			echo "<br /><span style=\"color: red;line-height: 300%;\">" . __('Note: HTML format will always deliver the full post', 'subscribe2') . ".</span><br />\r\n";
			echo __('Automatically subscribe me to newly created categories', 'subscribe2') . ': &nbsp;&nbsp;';
			echo "<input type=\"radio\" name=\"new_category\" value=\"yes\" ";
			if ('yes' == get_usermeta($user_ID, 's2_autosub')) {
				echo "checked=\"checked\" ";
			}
			echo "/> " . __('Yes', 'subscribe2') . "&nbsp;&nbsp;";
			echo "<input type=\"radio\" name=\"new_category\" value=\"no\" ";
			if ('no' == get_usermeta($user_ID, 's2_autosub')) {
				echo "checked=\"checked\" ";
			}
			echo "/> " . __('No', 'subscribe2') . "<br /><br />";
			echo "</p>";

			// subscribed categories
			echo "<h2>" . __('Subscribed Categories', 'subscribe2') . "</h2>\r\n";
			$this->display_category_form(explode(',', get_usermeta($user_ID, 's2_subscribed')), $this->subscribe2_options['reg_override']);
		} else {
			// we're doing daily digests, so just show
			// subscribe / unnsubscribe
			echo __('Receive daily summary of new posts?', 'subscribe2') . ': &nbsp;&nbsp;';
			echo "<p>";
			echo "<input type=\"radio\" name=\"category\" value=\"digest\" ";
			if (get_usermeta($user_ID, 's2_subscribed') != '-1') {
				echo "checked=\"yes\" ";
			}
			echo "/> " . __('Yes', 'subscribe2') . "<input type=\"radio\" name=\"category\" value=\"-1\" ";
			if (get_usermeta($user_ID, 's2_subscribed') == '-1') {
				echo "checked=\"yes\" ";
			}
			echo "/> " . __('No', 'subscribe2');
			echo "</p>";
		}


		// submit
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" value=\"" . __("Update Preferences", 'subscribe2') . " &raquo;\" /></p>";
		echo "</form></div>\r\n";

		include(ABSPATH . 'wp-admin/admin-footer.php');
		// just to be sure
		die;
	} // end user_menu()

	/**
	Display the Write sub-menu
	*/
	function write_menu() {
		global $wpdb, $s2nonce;

		// was anything POSTed?
		if (isset($_POST['s2_admin']) && ('mail' == $_POST['s2_admin']) ) {
			check_admin_referer('subscribe2-write_subscribers' . $s2nonce);
			if ('confirmed' == $_POST['what']) {
				$recipients = $this->get_public();
			} elseif ('unconfirmed' == $_POST['what']) {
				$recipients = $this->get_public(0);
			} elseif ('public' == $_POST['what']) {
				$confirmed = $this->get_public();
				$unconfirmed = $this->get_public(0);
				$recipients = array_merge((array)$confirmed, (array)$unconfirmed);
			} elseif (is_numeric($_POST['what'])) {
				$cat = intval($_POST['what']);
				$recipients = $this->get_registered("cats=$cat");
			} else {
				$recipients = $this->get_registered();
			}
			$subject = stripslashes(strip_tags($_POST['subject']));
			$body = stripslashes($_POST['message']);
			$status = $this->mail($recipients, $subject, $body, 'text');
			if ($status) {
				$message = $this->mail_sent;
			} else {
				global $phpmailer;
				$message = $this->mail_failed . $phpmailer->ErrorInfo;
			}
		}

		if ('' != $message) {
			echo "<div id=\"message\" class=\"updated\"><strong><p>" . $message . "</p></strong></div>\r\n";
		}
		// show our form
		echo "<div class=\"wrap\"><h2>" . __('Send email to all subscribers', 'subscribe2') . "</h2>\r\n";
		echo "<form method=\"post\" action=\"\">\r\n";
		echo "<p>";
		if (function_exists('wp_nonce_field')) {
			wp_nonce_field('subscribe2-write_subscribers' . $s2nonce);
		}
		echo __('Subject', 'subscribe2') . ": <input type=\"text\" size=\"69\" name=\"subject\" value=\"" . __('A message from ', 'subscribe2') . get_option('blogname') . "\" /> <br /><br />";
		echo "<textarea rows=\"12\" cols=\"75\" name=\"message\"></textarea>";
		echo "<br /><br />\r\n";
		echo __('Recipients: ', 'subscribe2');
		$this->display_subscriber_dropdown('registered', false, array('all'));
		echo "<input type=\"hidden\" name=\"s2_admin\" value=\"mail\" />";
		echo "</p>";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" value=\"" . __('Send', 'subscribe2') . "\" /></p>";
		echo "</form></div>\r\n";
		echo "<div style=\"clear: both;\"><p>&nbsp;</p></div>";

		include(ABSPATH . 'wp-admin/admin-footer.php');
		// just to be sure
		die;
	} // end write_menu()

/* ===== helper functions: forms and stuff ===== */
	/**
	Display a table of categories with checkboxes
	Optionally pre-select those categories specified
	*/
	function display_category_form($selected = array(), $override = 1) {
		global $wpdb;

		$all_cats = get_categories(array('hide_empty' => false));
		$exclude = explode(',', $this->subscribe2_options['exclude']);

		if (0 == $override) {
			// registered users are not allowed to subscribe to
			// excluded categories
			foreach ($all_cats as $cat => $term_id) {
				if (in_array($all_cats[$cat]->term_id, $exclude)) {
					$cat = (int)$cat;
					unset($all_cats[$cat]);
				}
			}
		}
		
		$half = (count($all_cats) / 2);
		$i = 0;
		$j = 0;
		echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"5\" class=\"editform\">\r\n";
		echo "<tr valign=\"top\"><td width=\"50%\" align=\"left\">\r\n";
		foreach ($all_cats as $cat) {
			 if ( ($i >= $half) && (0 == $j) ){
						echo "</td><td width=\"50%\" align=\"left\">\r\n";
						$j++;
				}
				if (0 == $j) {
						echo "<input class=\"cat_checkall\" type=\"checkbox\" name=\"category[]\" value=\"" . $cat->term_id . "\"";
						if (in_array($cat->term_id, $selected)) {
								echo " checked=\"checked\" ";
						}
						echo " /> " . $cat->name . "<br />\r\n";
					} else {

						echo "<input class=\"cat_checkall\" type=\"checkbox\" name=\"category[]\" value=\"" . $cat->term_id . "\"";
						if (in_array($cat->term_id, $selected)) {
									echo " checked=\"checked\" ";
						}
						echo " /> " . $cat->name . "<br />\r\n";
				}
				$i++;
		}
		echo "</td></tr>\r\n";
		echo "<tr><td align=\"left\" colspan=\"2\">\r\n";
		echo "<input type=\"checkbox\" name=\"checkall\" value=\"cat_checkall\" /> " . __('Select / Unselect All', 'subscribe2') . "\r\n";
		echo "</td></tr>\r\n";
		echo "</table>\r\n";
	} // end display_category_form()

	/**
	Display a drop-down form to select subscribers
	$selected is the option to select
	$submit is the text to use on the Submit button
	*/
	function display_subscriber_dropdown ($selected = 'registered', $submit = '', $exclude = array()) {
		global $wpdb, $wp_version, $wpmu_version;

		// Is this WordPressMU or not?
		if  ( (isset($wpmu_version)) || (strpos($wp_version, 'wordpress-mu')) ) {
			$s2_mu = true;
		}

		$who = array('all' => __('All Subscribers', 'subscribe2'),
			'public' => __('Public Subscribers', 'subscribe2'),
			'confirmed' => ' &nbsp;&nbsp;' . __('Confirmed', 'subscribe2'),
			'unconfirmed' => ' &nbsp;&nbsp;' . __('Unconfirmed', 'subscribe2'),
			'registered' => __('Registered Users', 'subscribe2'));

		$all_cats = get_categories(array('hide_empty' => false));

		// count the number of subscribers
		$count['confirmed'] = $wpdb->get_var("SELECT COUNT(id) FROM $this->public WHERE active='1'");
		$count['unconfirmed'] = $wpdb->get_var("SELECT COUNT(id) FROM $this->public WHERE active='0'");
		if (in_array('unconfirmed', $exclude)) {
			$count['public'] = $count['confirmed'];
		} elseif (in_array('confirmed', $exclude)) {
			$count['public'] = $count['unconfirmed'];
		} else {
			$count['public'] = ($count['confirmed'] + $count['unconfirmed']);
		}
		if ($s2_mu) {
			$count['registered'] = $wpdb->get_var("SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key='" . $wpdb->prefix . "capabilities'");
		} else {
			$count['registered'] = $wpdb->get_var("SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key='s2_subscribed'");
		}
		$count['all'] = ($count['confirmed'] + $count['unconfirmed'] + $count['registered']);
		if ($s2_mu) {
			foreach ($all_cats as $cat) {
				$count[$cat->name] = $wpdb->get_var("SELECT COUNT(a.meta_key) FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b ON a.user_id = b.user_id WHERE a.meta_key='" . $wpdb->prefix . "capabilities' AND b.meta_key=('s2_cat$cat->term_id')");
			}
		} else {
			foreach ($all_cats as $cat) {
				$count[$cat->name] = $wpdb->get_var("SELECT COUNT(meta_value) FROM $wpdb->usermeta WHERE meta_key='s2_cat$cat->term_id'");
			}
		}

		// do have actually have some subscribers?
		if ( (0 == $count['confirmed']) && (0 == $count['unconfirmed']) && (0 == $count['registered']) ) {
			// no? bail out
			return;
		}

		echo "<select name=\"what\">\r\n";
		foreach ($who as $whom => $display) {
			if (in_array($whom, $exclude)) { continue; }
			if (0 == $count[$whom]) { continue; }

			echo "<option value=\"" . $whom . "\"";
			if ($whom == $selected) { echo " selected=\"selected\" "; }
			echo ">$display (" . ($count[$whom]) . ")</option>\r\n";
		}

		if ($count['registered'] > 0) {
			foreach ($all_cats as $cat) {
				if (in_array($cat->term_id, $exclude)) { continue; }
				echo "<option value=\"" . $cat->term_id . "\"";
				if ($cat->term_id == $selected) { echo " selected=\"selected\" "; }
				echo "> &nbsp;&nbsp;" . $cat->name . "&nbsp;(" . $count[$cat->name] . ") </option>\r\n";
			}
		}
		echo "</select>";
		if (false !== $submit) {
			echo "&nbsp;<input type=\"submit\" value=\"$submit\" class=\"button\"/>\r\n";
		}
	} // end display_subscriber_dropdown()

	function display_digest_choices() {
		global $wpdb;
		$scheduled_time = wp_next_scheduled('s2_digest_cron');
		$schedule = (array)wp_get_schedules();
		$schedule = array_merge(array('never' => array('interval' => 0, 'display' => __('Per Post Email', 'subscribe2'))), $schedule);
		$sort = array();
		foreach ( (array)$schedule as $key => $value ) $sort[$key] = $value['interval'];
		asort($sort);
		$schedule_sorted = array();
		foreach ($sort as $key => $value) {
			$schedule_sorted[$key] = $schedule[$key];
		}
		foreach ($schedule_sorted as $key => $value) {
			echo "<input type=\"radio\" name=\"email_freq\" value=\"" . $key . "\"";
			if ($key == $this->subscribe2_options['email_freq']) {
				echo " checked=\"checked\" ";
			}
			echo " /> " . $value['display'] . "<br />\r\n";
		}
		echo "<br />" . __('Send Digest Notification at', 'subscribe2') . ": \r\n";
		$hours = array('12am', '1am', '2am', '3am', '4am', '5am', '6am', '7am', '8am', '9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm', '8pm', '9pm', '10pm', '11pm');
		echo "<select name=\"hour\">\r\n";
		while ($hour = current($hours)) {
			echo "<option value=\"" . key($hours) . "\"";
			if (key($hours) == gmdate('H', $scheduled_time)) {
				echo " selected=\"selected\"";
			}
			echo ">" . $hour . "</option>\r\n";
			next($hours);
		}
		echo "</select>\r\n";
		echo "<strong><em style=\"color: red\">" . __('This option will work for digest notification sent daily or less frequently', 'subscribe2') . "</em></strong>\r\n";
		if ($scheduled_time) {
			$datetime = get_option('date_format') . ' @ ' . get_option('time_format');
			echo "<p>" . __('Current UTC time is', 'subscribe2') . ": \r\n";
			echo "<strong>" . gmdate($datetime, current_time('timestamp', 1)) . "</strong></p>\r\n";
			echo "<p>" . __('Current blog time is', 'subscribe2') . ": \r\n";
			echo "<strong>" . gmdate($datetime, current_time('timestamp')) . "</strong></p>\r\n";
			echo "<p>" . __('Next email notification will be sent when your blog time is after', 'subscribe2') . ": \r\n";
			echo "<strong>" . gmdate($datetime, wp_next_scheduled('s2_digest_cron')) . "</strong></p>\r\n";
		} else {
			echo "<br />";
		}
	} // end display_digest_choices()

	/**
	Adds information to the WordPress registration screen for new users
	*/
	function register_form() {
		if ('wpreg' == $this->subscribe2_options['autosub']) {
			echo "<p>\r\n<label>";
			echo __('Check here to Subscribe to email notifications for new posts') . ":<br />\r\n";
			echo "<input type=\"checkbox\" name=\"subscribe\"";
			if ('yes' == $this->subscribe2_options['wpregdef']) {
				echo " checked=\"checked\"";
			}
			echo " /></label>\r\n";
			echo "</p>\r\n";
		} elseif ('yes' == $this->subscribe2_options['autosub']) {
			echo "<p>\r\n<center>\r\n";
			echo __('By registering with this blog you are also agreeing to receive email notifications for new posts but you can unsubscribe at anytime', 'subscribe2') . ".<br />\r\n";
			echo "</center></p>\r\n";
		}
	}

	/**
	Process function to add action if user selects to subscribe to posts during registration
	*/
	function register_post() {
		if ('on' == $_POST['subscribe']) {
			add_action('user_register', array(&$this, 'register_action'));
		}
	}

	/**
	Action to process Subscribe2 registration from WordPress registration
	*/
	function register_action() {
		$user_id = get_userdatabylogin($_POST['user_login']);
		if (0 == $user_id->ID) { return; }
		$this->register($user_id->ID, 1);
	}

/* ===== template and filter functions ===== */
	/**
	Display our form; also handles (un)subscribe requests
	*/
	function filter($content = '') {
		if ( ('' == $content) || (1 == $this->filtered) || (!strstr($content, '<!--subscribe2-->')) ) { return $content; }
		$this->s2form = $this->form;

		global $user_ID;
		get_currentuserinfo();
		if ($user_ID) {
			if (current_user_can('manage_options')) {
				$this->s2form = $this->use_profile_admin;
			} else {
				$this->s2form = $this->use_profile_users;
			}
		}
		if (isset($_POST['s2_action'])) {
			global $wpdb, $user_email;
			if (!is_email($_POST['email'])) {
				$this->s2form = $this->form . $this->not_an_email;
			} elseif ($this->is_barred($_POST['email'])) {
				$this->s2form = $this->form . $this->barred_domain;
			} else {
				$this->email = $_POST['email'];
				// does the supplied email belong to a registered user?
				$check = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE user_email = '$this->email'");
				if ('' != $check) {
					// this is a registered email
					$this->s2form = $this->please_log_in;
				} else {
					// this is not a registered email
					// what should we do?
					if ('subscribe' == $_POST['s2_action']) {
						// someone is trying to subscribe
						// lets see if they've tried to subscribe previously
						if ('1' !== $this->is_public($this->email)) {
							// the user is unknown or inactive
							$this->add();
							$status = $this->send_confirm('add');
							// set a variable to denote that we've already run, and shouldn't run again
							$this->filtered = 1; //set this to not send duplicate emails
							if ($status) {
								$this->s2form = $this->confirmation_sent;
							} else {
								$this->s2form = $this->error;
							}
						} else {
							// they're already subscribed
							$this->s2form = $this->already_subscribed;
						}
						$this->action = 'subscribe';
					} elseif ('unsubscribe' == $_POST['s2_action']) {
						// is this email a subscriber?
						if (false == $this->is_public($this->email)) {
							$this->s2form = $this->form . $this->not_subscribed;
						} else {
							$status = $this->send_confirm('del');
							// set a variable to denote that we've already run, and shouldn't run again
							$this->filtered = 1;
							if ($status) {
								$this->s2form = $this->confirmation_sent;
							} else {
								$this->s2form = $this->error;
							}
						}
						$this->action='unsubscribe';
					}
				}
			}
		}
		return preg_replace('|(<p>)?(\n)*<!--subscribe2-->(\n)*(</p>)?|', $this->s2form, $content);
	} // end filter()

	/**
	Overrides the default query when handling a (un)subscription confirmation
	This is basically a trick: if the s2 variable is in the query string, just grab the first
	static page and override it's contents later with title_filter()
	*/
	function query_filter() {
		// don't interfere if we've already done our thing
		if (1 == $this->filtered) { return; }

		global $wpdb;

		if ( 0 != $this->subscribe2_options['s2page'] ) {
			return "page_id=" . $this->subscribe2_options['s2page'];
		} else {
			$id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' LIMIT 1");
			if ($id) {
				return "page_id=$id";
			} else {
				return "showposts=1";
			}
		}
	} // end query_filter()

	/**
	Overrides the page title
	*/
	function title_filter($title) {
		// don't interfere if we've already done our thing
		if (1 == $this->filtered) { return; }
		if (in_the_loop()) {
			return __('Subscription Confirmation', 'subscribe2');
		} else {
			return $title;
		}
	} // end title_filter()

/* ===== widget functions ===== */
	/**
	Registers our widget so it appears with the other available
	widgets and can be dragged and dropped into any active sidebars
	*/
	function widget_subscribe2widget($args) {
		extract($args);
		$options = get_option('widget_subscribe2widget');
		$title = empty($options['title']) ? __('Subscribe2', 'subscribe2') : $options['title'];
		echo $before_widget;
		echo $before_title . $title . $after_title;
		echo "<div class=\"search\">";
		$content = s2class::filter('<!--subscribe2-->');
		echo $content;
		echo "</div>";
		echo $after_widget;
	}

	/**
	Register the optional widget control form
	*/
	function widget_subscribe2widget_control() {
		$options = $newoptions = get_option('widget_subscribe2widget');
		if ($_POST["s2w-submit"]) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["s2w-title"]));
		}
		if ($options != $newoptions) {
			$options = $newoptions;
			update_option('widget_subscribe2widget', $options);
		}
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		echo "<p><label for=\"s2w-title\">" . __('Title:');
		echo "<input style=\"width: 250px;\" id=\"s2w-title\" name=\"s2w-title\" type=\"text\" value=\"" . $title . "\" /></label></p>";
		echo "<input type=\"hidden\" id=\"s2w-submit\" name=\"s2w-submit\" value=\"1\" />";
	}

	/**
	Actually register the Widget into the WordPress Widget API
	*/
	function register_subscribe2widget() {
		//Check Sidebar Widget and Subscribe2 plugins are activated
		if ( !function_exists('register_sidebar_widget') || !class_exists('s2class')) {
			return;
		} else {
			register_sidebar_widget('Subscribe2', array(&$this, 'widget_subscribe2widget'));
			register_widget_control('Subscribe2', array(&$this, 'widget_subscribe2widget_control'));
		}
	}

	function plugin_action($links, $file) {
		if ($file == plugin_basename(dirname(__FILE__).'/subscribe2.php'))
			$links[] = "<a href='options-general.php?page=subscribe2/subscribe2.php'><b>" . __('Settings', 'subscribe2') . "</b></a>";
		return $links;
	}

/* ===== Write Toolbar Button Functions ===== */

	/**
	Register our button in the QuickTags bar
	*/
	function button_init() {
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) return;
			if ( 'true' == get_user_option('rich_editing') ) {
				global $wp_db_version;
				if ($wp_db_version >= 7098) {
					//check if we are using WordPress 2.5+
					add_filter('mce_external_plugins', array(&$this, 'mce3_plugin'));
					add_filter('mce_buttons', array(&$this, 'mce3_button'));
				} else {
					// Load and append our TinyMCE external plugin
					add_filter('mce_plugins', array(&$this, 'mce2_plugin'));
					add_filter('mce_buttons', array(&$this, 'mce2_button'));
					add_filter('tinymce_before_init', array(&$this, 'tinymce2_before_init'));
				}
			} else {
				//use buttonsnap to add button is not using RTE
				buttonsnap_separator();
				buttonsnap_jsbutton(WP_CONTENT_URL . '/plugins/subscribe2/include/s2_button.png', __('Subscribe2', 'subscribe2'), 's2_insert_token();');
			}
	}

	/**
	Add buttons for WordPress 2.5+ using built in hooks
	*/
	function mce3_plugin($arr) {
		$path = WP_CONTENT_URL . '/plugins/subscribe2/tinymce3/editor_plugin.js';
		$arr['subscribe2'] = $path;
		return $arr;
	}
	
	function mce3_button($arr) {
		$arr[] = 'subscribe2';
		return $arr;
	}

	// Add buttons in WordPress v2.1+, thanks to An-archos
	function mce2_plugin($plugins) {
		array_push($plugins, '-subscribe2quicktags');
		return $plugins;
	}

	function mce2_button($buttons) {
		array_push($buttons, 'separator');
		array_push($buttons, 'subscribe2quicktags');
		return $buttons;
	}

	function tinymce2_before_init() {
		$this->fullpath = WP_CONTENT_URL . '/plugins/subscribe2/tinymce/';
		echo "tinyMCE.loadPlugin('subscribe2quicktags', '" . $this->fullpath . "');\n"; 
	}

	function s2_edit_form() { 
		echo "<!-- Start Subscribe2 Quicktags Javascript -->\r\n";
		echo "<script type=\"text/javascript\">\r\n";
		echo "//<![CDATA[\r\n";
		echo "function s2_insert_token() {
			buttonsnap_settext('<!--subscribe2-->');
		}\r\n";
		echo "//]]>\r\n";
		echo "</script>\r\n";
		echo "<!-- End Subscribe2 Quicktags Javascript -->\r\n";
	}

/* ===== wp-cron functions ===== */
	/**
	Send a daily digest of today's new posts
	*/
	function subscribe2_cron() {
		global $wpdb;

		// update last_s2cron execution time before completing or bailing
		$now = current_time('mysql');
		$prev = $this->subscribe2_options['last_s2cron'];
		$this->subscribe2_options['last_s2cron'] = $now;
		update_option('subscribe2_options', $this->subscribe2_options);

		//set up SQL query based on options
		if ($this->subscribe2_options['private'] == 'yes' ) {
			$status	= "'publish', 'private'";
		} else {
			$status = "'publish'";
		}

		if ($this->subscribe2_options['page'] == 'yes' ) {
			$type	= "'post', 'page'";
		} else {
			$type = "'post'";
		}

		// collect posts
		$posts = $wpdb->get_results("SELECT ID, post_title, post_excerpt, post_content, post_type, post_password, post_date FROM $wpdb->posts WHERE post_date >= '$prev' AND post_date < '$now' AND post_status IN ($status) AND post_type IN ($type) ORDER BY post_date");

		// do we have any posts?
		if (empty($posts)) { return; }

		// if we have posts, let's prepare the digest
		$datetime = get_option('date_format') . ' @ ' . get_option('time_format');
		$all_post_cats = array();
		foreach ($posts as $post) {
			$post_cats = wp_get_post_categories($post->ID);
			$post_cats_string = implode(',', $post_cats);
			$all_post_cats = array_unique(array_merge($all_post_cats, $post_cats));
			$check = false;
			// is the current post assigned to any categories
			// which should not generate a notification email?
			foreach (explode(',', $this->subscribe2_options['exclude']) as $cat) {
				if (in_array($cat, $post_cats)) {
					$check = true;
				}
			}
			//is the current post set by the user to
			// not generate a notification email?
			$s2mail = get_post_meta($post->ID, 's2mail', true);
			if (strtolower(trim($s2mail)) == 'no') {
				$check = true;
			}
			// is the current post private
			// and should this not generate a notification email?
			if ( ($this->subscribe2_options['password'] == "no") && ($post->post_password != '') ) {
				$check = true;
			}
			// if this post is excluded
			// don't include it in the digest
			if ($check) {
				continue;
			}
			$table .= $post->post_title . "\r\n";
			$message .= $post->post_title . "\r\n";
			$message .= __('Posted on', 'subscribe2') . ": " . mysql2date($datetime, $post->post_date) . "\r\n";
			$message .= get_permalink($post->ID) . "\r\n";
			$excerpt = $post->post_excerpt;
			if ('' == $excerpt) {
				 // no excerpt, is there a <!--more--> ?
				 if (false !== strpos($post->post_content, '<!--more-->')) {
				 	list($excerpt, $more) = explode('<!--more-->', $post->post_content, 2);
				 	$excerpt = strip_tags($excerpt);
				 	if (function_exists('strip_shortcodes')) {
						$excerpt = strip_shortcodes($excerpt);
					}
					// strip leading and trailing whitespace
					$excerpt = trim($excerpt);
				} else {
					$excerpt = strip_tags($post->post_content);
					if (function_exists('strip_shortcodes')) {
						$excerpt = strip_shortcodes($excerpt);
					}
					$words = explode(' ', $excerpt, 56);
					if (count($words) > 55) {
						array_pop($words);
						array_push($words, '[...]');
						$excerpt = implode(' ', $words);
					}
				}
			}
			$message .= $excerpt . "\r\n\r\n";
		}

		//sanity check - don't send a mail if the content is empty
		if (!$message && !$table) {
			return;
		}

		// get admin details
		$user = $this->get_userdata();
		$this->myemail = $user->user_email;
		$this->myname = html_entity_decode($user->display_name);

		$scheds = (array)wp_get_schedules();
		$email_freq = $this->subscribe2_options['email_freq'];
		$display = $scheds[$email_freq]['display'];
		$subject = '[' . stripslashes(get_option('blogname')) . '] ' . $display . ' ' . __('Digest Email', 'subscribe2');
		$public = $this->get_public();
		$all_post_cats_string = implode(',', $all_post_cats);
		$registered = $this->get_registered("cats=$all_post_cats_string");
		$recipients = array_merge((array)$public, (array)$registered);
		$mailtext = $this->substitute(stripslashes($this->subscribe2_options['mailtext']));
		$mailtext = str_replace("TABLE", $table, $mailtext);
		$mailtext = str_replace("POST", $message, $mailtext);
		$this->mail($recipients, $subject, $mailtext);
	} // end subscribe2_cron

/* ===== Our constructor ===== */
	/**
	Subscribe2 constructor
	*/
	function s2init() {
		// load the options
		$this->subscribe2_options = get_option('subscribe2_options');

		add_action('init', array(&$this, 'subscribe2'));
		if ('1' == $this->subscribe2_options['show_button']) {
			add_action('init', array(&$this, 'button_init'));
		}
		// add action to display widget if option is enabled
		if ('1' == $this->subscribe2_options['widget']) {
			add_action('plugins_loaded', array(&$this, 'register_subscribe2widget'));
		}
	}

	function subscribe2() {
		global $table_prefix;

		load_plugin_textdomain('subscribe2', PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));

		// do we need to install anything?
		$this->public = $table_prefix . "subscribe2";
		if (!mysql_query("DESCRIBE " . $this->public)) { $this->install(); }
		//do we need to upgrade anything?
		if ($this->subscribe2_options['version'] !== S2VERSION) {
			add_action('shutdown', array(&$this, 'upgrade'));
		}

		if (isset($_GET['s2'])) {
			// someone is confirming a request
			add_filter('query_string', array(&$this, 'query_filter'));
			add_filter('the_title', array(&$this, 'title_filter'));
			add_filter('the_content', array(&$this, 'confirm'));
		}
		
		if ( (isset($_POST['s2_admin'])) && ($_POST['csv']) ) {
			$date = date('Y-m-d');
			header("Content-Description: File Transfer");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=subscribe2_users_$date.csv");
			header("Pragma: no-cache");
			header("Expires: 0");
			echo $_POST['exportcsv'];

			exit(0);
		}
		
		//add regular actions and filters
		add_action('admin_menu', array(&$this, 'admin_menu'));
		add_filter('ozh_adminmenu_icon', array(&$this, 'ozh_s2_icon'));
		add_action('create_category', array(&$this, 'autosub_new_category'));
		add_filter('the_content', array(&$this, 'filter'), 10);
		add_filter('cron_schedules', array(&$this, 'add_weekly_sched'));

		// add action to display editor buttons if option is enabled
		if ('1' == $this->subscribe2_options['show_button']) {
			add_action('edit_page_form', array(&$this, 's2_edit_form'));
			add_action('edit_form_advanced', array(&$this, 's2_edit_form'));
		}

		// add actions for automatic subscription based on option settings
		add_action('register_form', array(&$this, 'register_form'));
		if ('yes' == $this->subscribe2_options['autosub']) {
			add_action('user_register', array(&$this, 'register'));
		}
		if ('wpreg' == $this->subscribe2_options['autosub']) {
			add_action('register_post', array(&$this, 'register_post'));
		}
		
		// add actions for processing posts based on per-post or cron email settings
		if ($this->subscribe2_options['email_freq'] != 'never') {
			add_action('s2_digest_cron', array(&$this, 'subscribe2_cron'));
		} else {
			add_action('new_to_publish', array(&$this, 'publish'));
			add_action('draft_to_publish', array(&$this, 'publish'));
			add_action('pending_to_publish', array(&$this, 'publish'));
			add_action('private_to_publish', array(&$this, 'publish'));
			add_action('future_to_publish', array(&$this, 'publish'));
			add_action('publish_phone', array(&$this, 'publish_phone'));
			if ($this->subscribe2_options['private'] == "yes") {
				add_action('new_to_private', array(&$this, 'publish'));
				add_action('draft_to_private', array(&$this, 'publish'));
				add_action('pending_to_private', array(&$this, 'publish'));
			}
		}

		// load our strings
		$this->load_strings();
	} // end subscribe2()

/* ===== our variables ===== */
	// cache variables
	var $subscribe2_options = array();
	var $all_public = '';
	var $all_unconfirmed = '';
	var $excluded_cats = '';
	var $post_title = '';
	var $permalink = '';
	var $myname = '';
	var $myemail = '';
	var $s2_subject = '[BLOGNAME] TITLE';
	var $signup_dates = array();
	var $private = false;
	var $filtered = 0;

	// state variables used to affect processing
	var $action = '';
	var $email = '';
	var $message = '';
	var $error = '';

	// some messages
	var $please_log_in = '';
	var $use_profile = '';
	var $confirmation_sent = '';
	var $already_subscribed = '';
	var $not_subscribed ='';
	var $not_an_email = '';
	var $barred_domain = '';
	var $mail_sent = '';
	var $form = '';
	var $no_such_email = '';
	var $added = '';
	var $deleted = '';
	var $confirm_subject = '';
	var $options_saved = '';
	var $options_reset = '';
} // end class subscribe2
?>