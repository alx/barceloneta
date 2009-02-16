<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

//install stuf
function post_notification_install(){
	global $wpdb;
	$t_emails = $wpdb->prefix . 'post_notification_emails';
	$t_posts = $wpdb->prefix . 'post_notification_posts';
	$t_cats = $wpdb->prefix . 'post_notification_cats';
	
	//******************************************//
	//**  Create WPPRAEFIX_post_notification table   **// 
	//******************************************//
	if(!function_exists('maybe_create_table') )
		require_once(ABSPATH . 'wp-admin/install-helper.php');
	
	$sql = "CREATE TABLE $t_emails (
				  id int( 11 ) NOT NULL auto_increment,
				  email_addr varchar( 255 ) default NULL,
				  gets_mail int( 11 ) default NULL,
				  last_modified timestamp( 14 ) NOT NULL,
				  date_subscribed datetime default NULL,
				  act_code varchar( 32 ) default NULL,
				  PRIMARY KEY  ( id )   
		   )";
		
	maybe_create_table($t_posts, $sql);
	
	$sql = "ALTER TABLE $t_emails ADD subscribe_ip INT UNSIGNED NOT NULL default 0";
	maybe_add_column($t_emails , 'subscribe_ip', $sql);

	
	//************************************************//
	//**  Create WPPRAEFIX_post_notification_posts table **// 
	//************************************************//
	
	
		# Add new table
	$sql = "CREATE TABLE $t_posts (
				post_id bigint(20) NOT NULL default '0',
				notification_sent int NOT NULL default '-1',
				PRIMARY KEY  (post_ID)
		   )"; 
		   
	maybe_create_table($t_posts, $sql);
	
	//Always adding this later keeps the code simple..... (Added in 2.2)
	$sql = "ALTER TABLE $t_posts ADD date_saved datetime NOT NULL default  '0000-00-00 00:00:00'";
	maybe_add_column($t_posts , 'date_saved', $sql);
	
	//This is in an if-statement, because auf the insert-statement.
	if($wpdb->get_var("SHOW TABLES LIKE '$t_cats'") == NULL){
		$sql = "CREATE TABLE $t_cats (
					id int( 11 ) NOT NULL,
					cat_id bigint(20) NOT NULL 
			   )"; 
		maybe_create_table($t_cats, $sql);
		
		// Thanks to Karsten Tinnefeld for this nice query
		$wpdb->query("INSERT
					INTO    $t_cats (id, cat_id)
					SELECT  id, 0
					FROM    $t_emails e
					WHERE   NOT EXISTS (SELECT 1 
										FROM   $t_cats c 
										WHERE c.id = e.id )");
	}
	
	
	//This actually belongs into the create statement but it's easyer to maintain this way
	$index = array();
	$indexlist = $wpdb->get_results("SHOW INDEX FROM $t_cats");
	foreach($indexlist as $indexrow) $index[] = $indexrow->Column_name;	
	if(!in_array('id',		$index)) $wpdb->query(" ALTER TABLE $t_cats ADD INDEX ( id )");
	if(!in_array('cat_id',	$index)) $wpdb->query(" ALTER TABLE $t_cats ADD INDEX ( cat_id )");

	
	$index = array();
	$indexlist = $wpdb->get_results("SHOW INDEX FROM $t_emails");
	foreach($indexlist as $indexrow) $index[] = $indexrow->Column_name;
	if(!in_array('gets_mail',	$index)) 
		$wpdb->query(" ALTER TABLE $t_emails ADD INDEX ( id , gets_mail )");
	if(!in_array('email_addr',	$index)) 
		$wpdb->query(" ALTER TABLE $t_emails ADD INDEX ( email_addr )");

	$index = array();
	$indexlist = $wpdb->get_results("SHOW INDEX FROM $t_posts");
	foreach($indexlist as $indexrow) $index[] = $indexrow->Column_name;	
	if(!in_array('notification_sent',	$index)) 
		$wpdb->query(" ALTER TABLE $t_posts ADD INDEX ( notification_sent )");
	
	
	//}
	//************************************************//
	//**         Add Options
	//************************************************//
	load_plugin_textdomain('post_notification', POST_NOTIFICATION_PATH_REL);
	
	add_option('post_notification_show_content', 'no', 'Whether to mail the content', 'no');
	add_option('post_notification_read_more', '...', 'What to put in more-tag-text', 'no');
	add_option('post_notification_send_default', 'yes', 'Whether to send normal posts', 'no');
	add_option('post_notification_send_private', 'no', 'Whether to send private posts', 'no');
	add_option('post_notification_send_page', 'no', 'Whether to send private posts', 'no');
	add_option('post_notification_hdr_nl', "n", 'What kind of header', 'no'); 
	add_option('post_notification_from_email', get_option('admin_email'), 'The adress used as sender', 'no');
	add_option('post_notification_from_name', '@@blogname', 'The name used as sender', 'no');
	add_option('post_notification_subject', '@@blogname: @@title', 'The subject of the mail', 'no');
	add_option('post_notification_url', '', 'The URl to the main page', 'no');
	add_option('post_notification_template', "email_template.txt", 'The Template to use', 'no');
	add_option('post_notification_maxsend', "20" , 'Number of Mails to send at once.', 'no');
	add_option('post_notification_pause', "10" , 'Time between bursts of Mails', 'no');
	add_option('post_notification_nervous', "360", 'Nervous finger option');
	add_option('post_notification_nextsend', time(), 'When to send the next mail', 'yes');
	add_option('post_notification_lastsend', time(),  'When to send the last mail was sent.', 'no');
	add_option('post_notification_lastpost', time(),  'When the last post was published.', 'no');
	add_option('post_notification_page_meta', 'no' , 'Whether to add a link to Meta', 'yes');
	//autoload is set to yes, because meta might need it.
	add_option('post_notification_page_name', __('Subscribe to Posts', 'post_notification') ,'Name of the Post Notification page.', 'yes');
	add_option('post_notification_uninstall', 'no' ,'Uninstall on deaktivation', 'no');
	add_option('post_notification_captcha', '0','Number of captcha-cahars', 'no');
	add_option('post_notification_lock', 'file', 'Lockingtype', 'yes');
	add_option('post_notification_filter_include', 'yes','Include PN via filters', 'yes');
	add_option('post_notification_selected_cats', '0', 'The category preselection list.', 'no');
	add_option('post_notification_debug', 'no', 'Turn debugging on.', 'no');
	add_option('post_notification_the_content_exclude', serialize(array()) ,'Include PN via filters', 'no');
	add_option('post_notification_empty_cats', 'no' , 'Whether to show empty cats', 'no');
	add_option('post_notification_show_cats', 'yes' , 'Whether to show cats', 'no');
	add_option('post_notification_subscribers', '0','Number of Subscibers', 'yes');
	
	if(is_dir(POST_NOTIFICATION_PATH . WPLANG))
		$profile = WPLANG;
	else
		$profile = 'en_US';
	add_option('post_notification_profile', $profile, 'The Profile-dir to use'); 
}

function post_notification_uninstall(){
	global $wpdb;
	
	$t_emails = $wpdb->prefix . 'post_notification_emails';
	$t_posts = $wpdb->prefix . 'post_notification_posts';
	$t_cats = $wpdb->prefix . 'post_notification_cats';
	
	if(get_option('post_notification_uninstall') != 'yes') return; //We do not want to delte anything.
	
	//
	$wpdb->query("DROP TABLE $t_emails");
	$wpdb->query("DROP TABLE $t_posts");
	$wpdb->query("DROP TABLE $t_cats");
	
	//delete all options
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'post_notification%'");
}
?>