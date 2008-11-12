<?php
/*
Plugin Name: Subscribe2 Uninstaller
Plugin URI: http://subscribe2.wordpress.com
Description: Uninstalls the Subscribe2 plugin from Manage->S2 Uninstaller.
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

$mys2uninst = new s2uninst;
$mys2uninst->s2uninst_init();

class s2uninst {
	function manage_page() {
		global $s2nonce;
		
		if (isset($_POST['s2_uninst'])) {
			check_admin_referer('s2uninst-uninstaller' . $s2nonce);
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Subscribe2 has been uninstalled.', 's2uninst') . "</strong></p></div>\n";
			$this->uninstall();
		}
		
		echo "<div class=\"wrap\">";
		echo "<h2>" . __('Subscribe2 Uninstaller', 's2uninst') . "</h2>\r\n";
		if (class_exists('s2class')) {
			echo "<p>" . __('The Subscribe2 plugin is still active on your system. Please deactivate the plugin before completing the uninstall process.', 's2uninst') . "</p>\r\n";
			echo "<p>" . __('You can deactivate the plugin by using the ', 's2uninst') . "<a href=\"" . get_option('siteurl') . "/wp-admin/plugins.php\">" . __('WordPress Plugin Page', 's2uninst') . "</a>.</p>";
			echo "</div>\r\n";
			include(ABSPATH . 'wp-admin/admin-footer.php');
			// just to be sure
			die;			
		} else {
			echo "<form method=\"post\" action=\"\">";
			if (function_exists('wp_nonce_field')) {
				wp_nonce_field('s2uninst-uninstaller' . $s2nonce);
			}
			echo "<p>" . __('Clicking on UNINSTALL will remove the following items from your WordPress install (these were installed by Subscribe2).', 's2uninst') . "</p>\r\n";
			echo "<ul>\r\n";
			echo "<li>" . __('Options Table Entries', 's2uninst') . "</li>\r\n";
			echo "<li>" . __('Usermeta Table Entries', 's2uninst') . "</li>\r\n";
			echo "<li>" . __('Subscribe2 Table', 's2uninst') . "</li>\r\n";
			echo "<li>" . __('Events Schedules by Subscribe2', 's2uninst') . "</li>\r\n";
			echo "</ul>\r\n";
			echo "<p align=\"center\"><span class=\"submit\">\r\n";
			echo "<input type=\"hidden\" name=\"s2_uninst\" value=\"RESET\" />\r\n";
			echo "<input type=\"submit\" id=\"deletepost\" name=\"submit\" value=\"" . __('UNINSTALL', 's2uninst') . "\" />\r\n";
			echo "</span></p></form></div>\r\n";
			include(ABSPATH . 'wp-admin/admin-footer.php');
			// just to be sure
			die;
		}
	}

	function uninstall() {
		global $wpdb, $table_prefix;
		// get name of subscribe2 table
		$this->public = $table_prefix . "subscribe2";
		// delete entry from wp_options table
		delete_option('subscribe2_options');
		// delete legacy entry from wp-options table
		delete_option('s2_future_posts');
		// remove and scheduled events
		wp_clear_scheduled_hook('s2_digest_cron');
		// delete usermeta data for registered users
		$users = $wpdb->get_col("SELECT ID FROM $wpdb->users");
		if (!empty($users)) {
			foreach ($users as $user) {
				$cats = explode(',', get_usermeta($user, 's2_subscribed'));
				if ($cats) {
					foreach ($cats as $cat) {
						delete_usermeta($user, "s2_cat" . $cat);
					}
				}
				delete_usermeta($user, 's2_subscribed');
			}
		}
		// drop the subscribe2 table
		$sql = "DROP TABLE IF EXISTS `" . $this->public . "`";
		mysql_query($sql);
	}

	function s2uninst_init() {
		load_plugin_textdomain('s2uninst', 'wp-content/plugins/');
		add_action('admin_menu', array(&$this, 's2uninst_page'));
	}

	function s2uninst_page() {
		add_management_page(__('S2 Uninstaller', 's2uninst'), __('S2 Uninstaller', 's2uninst'), "manage_options", __FILE__, array(&$this, 'manage_page'));
	}
}
?>