<?php
//Field for USPS or other tracking system
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'track_id';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `track_id` VARCHAR( 50 ) NULL;");
  }
//Field for digital or hardcopy stuff
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."variation_priceandstock` LIKE 'file';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."variation_priceandstock` ADD `file` VARCHAR( 1 ) NOT NULL DEFAULT '0';");
  }
//Field for shipping regions
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'shipping_region';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `shipping_region` char(6) NOT NULL;");
  }

//Add table logged_subscription
if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."wpsc_logged_subscriptions'") != ($wpdb->prefix."wpsc_logged_subscriptions")) {
   $wpsc_also_bought_product = "CREATE TABLE `".$wpdb->prefix."wpsc_logged_subscriptions` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`cart_id` bigint(20) unsigned NOT NULL default '0',
	`user_id` bigint(20) unsigned NOT NULL default '0',
	`length` varchar(64) NOT NULL default '0',
	`start_time` varchar(64) NOT NULL default '0',
	`active` varchar(1) NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `cart_id` (`cart_id`),
	KEY `user_id` (`user_id`),
	KEY `start_time` (`start_time`)
) TYPE=MyISAM;";
  $wpdb->query($wpsc_also_bought_product);
  }

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'find_us';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `find_us` varchar(255) NOT NULL");
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `engravetext` varchar(255) default NULL");
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `closest_store` varchar(255) default NULL");
}

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'google_order_number';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `google_order_number` varchar(20) NOT NULL default '';");
  }
  
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'google_user_marketing_preference';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `google_user_marketing_preference` varchar(10) NOT NULL default '';");
  }
  
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'google_status';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `google_status` longtext NOT NULL;");
  }
?>