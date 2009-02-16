<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

function post_notification_admin_sub(){
	global $wpdb;
	$t_emails = $wpdb->prefix . 'post_notification_emails';
	
	echo '<h3>' . __('Export Emails', 'post_notification') . '</h3>';
	
	
	$emails = $wpdb->get_results("SELECT email_addr FROM " . $t_emails . " WHERE gets_mail = 1");
	
	if (!$emails){
	   echo '<p>' . __('No entries found.', 'post_notification') . '</p>';
	   return;
	}
	
	foreach($emails as $email) {
	  echo $email->email_addr . '<br />';
	}	
}
?>