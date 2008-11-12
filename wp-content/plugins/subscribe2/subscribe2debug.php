<?php
/*
Plugin Name: Subscribe2 Debug
Plugin URI: http://subscribe2.wordpress.com
Description: Produces Debug Information for the Subscribe2 plugin.
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

function add_debug_page() {
	add_menu_page('Subscribe2 Debug', 'Subscribe2 Debug', 1, __FILE__, 'debug_menu');
}
add_action('admin_menu', 'add_debug_page');

function debug_menu() {
	global $wp_filter;
	 echo "<div class=\"wrap\">";
	$datetime = get_option('date_format') . ' @ ' . get_option('time_format');
	echo "<p>Current Server time is: \r\n";
	echo "<strong>" . gmdate($datetime, current_time('timestamp', 1)) . "</strong></p>\r\n";
	echo "<p>Current Blog time is: \r\n";
	echo "<strong>" . gmdate($datetime, current_time('timestamp')) . "</strong></p>\r\n";
	echo "<p>Current Blog offset is: \r\n";
	echo get_option('gmt_offset') . "\r\n";
	echo "<pre>";
	cron_jobs();
	echo "</pre>";
	echo "Current Subscribe 2 Options are:";
	echo "<pre>";
	print_r(get_option('subscribe2_options'));
	echo "</pre>";
	echo "</div>";
	include(ABSPATH . 'wp-admin/admin-footer.php');
	// just to be sure
	die;
}

function cron_jobs() {
	$datetime = get_option('date_format') . ' @ ' . get_option('time_format');
	$jobs = _get_cron_array();
	if (empty($jobs)) {
		echo "No cron jobs scheduled";
	} else {
		foreach ( $jobs as $job => $tasks ) {
			echo "Anytime after " . gmdate($datetime,$job) . " ";
			foreach ($tasks as $procname => $task) {
				echo $procname . " will execute\r\n";
			}
		}
	}
	return;
}
?>