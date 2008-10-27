<?php
function wpsc_auto_update() {
  global $wpdb;
  if(get_option('wpsc_version') <= 3.5) {
    include_once('updates/update-to-3.5.0.php');
	}
  
  if((get_option('wpsc_version') < 3.5 ) || ((get_option('wpsc_version') == 3.5 ) && (get_option('wpsc_minor_version') < 2))) {
    include_once('updates/update-to-3.5.2.php');
	}

 if((get_option('wpsc_version') < 3.6 ) || ((get_option('wpsc_version') == 3.6 ) && (get_option('wpsc_minor_version') < 68))) {
    include_once('updates/update-to-3.6.0.php');
    include_once('updates/update-to-3.6.4.php');
	}

 if((get_option('wpsc_version') < 3.6 ) || ((get_option('wpsc_version') == 3.6 ) && ((int)get_option('wpsc_minor_version') < 80))) {
    include_once('updates/update-to-3.6.8.php');
	}

  wpsc_create_upload_directories();

  wpsc_product_files_htaccess();  
  wpsc_check_and_copy_files();
  
  if((get_option('wpsc_version') < WPSC_VERSION) || (get_option('wpsc_version') == WPSC_VERSION) && (get_option('wpsc_minor_version') < WPSC_MINOR_VERSION)) {
    update_option('wpsc_version', WPSC_VERSION);
    update_option('wpsc_minor_version', WPSC_MINOR_VERSION);
	}
}

function nzshpcrt_install()
   {
   global $wpdb, $user_level, $wp_rewrite, $wp_version;
   $table_name = $wpdb->prefix . "product_list";
   //$log_table_name = $wpdb->prefix . "sms_log";
   if($wp_version < 2.1) {
     get_currentuserinfo();
     if($user_level < 8) {
       return;
			}
    }
  $first_install = false;
  $result = mysql_list_tables(DB_NAME);
  $tables = array();
  while($row = mysql_fetch_row($result)) {
    $tables[] = $row[0];
	}
  if(!in_array($table_name, $tables)) {
    $first_install = true;
	}    

  if(get_option('wpsc_version') == null) {
    add_option('wpsc_version', WPSC_VERSION, 'wpsc_version', 'yes');
	}
        

	// Table structure for table `".$wpdb->prefix."also_bought_product`      
	
	$num = 0;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix."also_bought_product";
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."also_bought_product` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`selected_product` bigint(20) unsigned NOT NULL default '0',
		`associated_product` bigint(20) unsigned NOT NULL default '0',
		`quantity` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."cart_contents`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'cart_contents';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."cart_contents` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`prodid` bigint(20) unsigned NOT NULL default '0',
		`purchaseid` bigint(20) unsigned NOT NULL default '0',
		`price` varchar(128) NOT NULL default '0',
		`pnp` varchar(128) NOT NULL default '0',
		`gst` varchar(128) NOT NULL default '0',
		`quantity` int(10) unsigned NOT NULL default '0',
		`donation` varchar(1) NOT NULL default '0',
		`no_shipping` varchar(1) NOT NULL default '0',
		`files` TEXT NOT NULL default '',
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."cart_item_extras`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'cart_item_extras';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."cart_item_extras` (
		`id` int(11) NOT NULL auto_increment,
		`cart_id` int(11) NOT NULL,
		`extra_id` int(11) NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM;
	";
	
	// Table structure for table `".$wpdb->prefix."cart_item_variations`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'cart_item_variations';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."cart_item_variations` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`cart_id` bigint(20) unsigned NOT NULL default '0',
		`variation_id` bigint(20) unsigned NOT NULL default '0',
		`value_id` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."collect_data_forms`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'collect_data_forms';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."collect_data_forms` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`name` varchar(255) NOT NULL default '',
		`type` varchar(64) NOT NULL default '',
		`mandatory` varchar(1) NOT NULL default '0',
		`display_log` char(1) NOT NULL default '0',
		`default` varchar(128) NOT NULL default '0',
		`active` varchar(1) NOT NULL default '1',
		`order` int(10) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `order` (`order`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."currency_list`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'currency_list';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."currency_list` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`country` varchar(255) NOT NULL default '',
		`isocode` char(2) default NULL,
		`currency` varchar(255) NOT NULL default '',
		`symbol` varchar(10) NOT NULL default '',
		`symbol_html` varchar(10) NOT NULL default '',
		`code` char(3) NOT NULL default '',
		`has_regions` char(1) NOT NULL default '0',
		`tax` varchar(8) NOT NULL default '',
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."download_status`
		
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'download_status';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."download_status` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`fileid` bigint(20) unsigned NOT NULL default '0',
		`purchid` bigint(20) unsigned NOT NULL default '0',
		`uniqueid` varchar(64) default NULL,
		`downloads` int(11) NOT NULL default '0',
		`ip_number` varchar(255) NOT NULL default '',
		`active` varchar(1) NOT NULL default '0',
		`datetime` datetime NOT NULL,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `uniqueid` (`uniqueid`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."extras_values`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'extras_values';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."extras_values` (
		`id` int(11) NOT NULL auto_increment,
		`name` varchar(128) NOT NULL,
		`extras_id` int(11) NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."extras_values_associations`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'extras_values_associations';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."extras_values_associations` (
		`id` int(11) NOT NULL auto_increment,
		`product_id` int(11) NOT NULL,
		`value_id` int(11) NOT NULL,
		`price` varchar(20) NOT NULL,
		`visible` varchar(1) NOT NULL,
		`extras_id` int(11) NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."item_category_associations`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'item_category_associations';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."item_category_associations` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`product_id` bigint(20) unsigned NOT NULL default '0',
		`category_id` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `product_id` (`product_id`,`category_id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_brands`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_brands';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_brands` (
		`id` bigint(20) NOT NULL auto_increment,
		`name` text NOT NULL,
		`description` text NOT NULL,
		`active` varchar(1) NOT NULL default '1',
		`order` bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_categories`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_categories';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_categories` (
		`id` bigint(20) NOT NULL auto_increment,
		`group_id` BIGINT( 20 ) UNSIGNED NOT NULL,
		`name` text NOT NULL,
		`nice-name` varchar(255) NOT NULL,
		`description` text NOT NULL,
		`image` text NOT NULL,
		`fee` varchar(1) NOT NULL default '0',
		`active` varchar(1) NOT NULL default '1',
		`category_parent` bigint(20) unsigned default '0',
		`order` bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (`id`),
		KEY `group_id` (`group_id`),
		KEY `nice-name` (`nice-name`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_extra`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_extra';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_extra` (
		`id` int(11) NOT NULL auto_increment,
		`name` varchar(128) NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_files`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_files';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_files` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`filename` varchar(255) NOT NULL default '',
		`mimetype` varchar(128) NOT NULL default '',
		`idhash` varchar(45) NOT NULL default '',
		`preview` varchar(255) NOT NULL default '',
		`preview_mimetype` varchar(128) NOT NULL default '',
		`date` varchar(255) NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_images`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_images';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_images` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`product_id` bigint(20) unsigned NOT NULL,
		`image` varchar(255) NOT NULL,
		`width` mediumint(8) unsigned NOT NULL,
		`height` mediumint(8) unsigned NOT NULL,
		PRIMARY KEY  (`id`),
		KEY `product_id` (`product_id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_list`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_list';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_list` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`name` text NOT NULL,
		`description` longtext NOT NULL,
		`additional_description` longtext NOT NULL,
		`price` varchar(20) NOT NULL default '0',
		`weight` int(11) NOT NULL default '0',
		`weight_unit` varchar(10) NOT NULL,
		`pnp` varchar(20) NOT NULL default '0',
		`international_pnp` varchar(20) NOT NULL default '0',
		`file` bigint(20) unsigned NOT NULL,
		`image` text NOT NULL,
		`category` bigint(20) unsigned NOT NULL default '0',
		`brand` bigint(20) unsigned NOT NULL default '0',
		`quantity_limited` varchar(1) NOT NULL,
		`quantity` int(10) unsigned NOT NULL default '0',
		`special` varchar(1) NOT NULL default '0',
		`special_price` varchar(20) NOT NULL default '0',
		`display_frontpage` varchar(1) NOT NULL default '0',
		`notax` varchar(1) NOT NULL default '0',
		`active` varchar(1) NOT NULL default '1',
		`donation` varchar(1) NOT NULL default '0',
		`no_shipping` varchar(1) NOT NULL default '0',
		`thumbnail_image` text,
		`thumbnail_state` int(11) NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_order`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_order';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_order` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`category_id` bigint(20) unsigned NOT NULL default '0',
		`product_id` bigint(20) unsigned NOT NULL default '0',
		`order` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		UNIQUE KEY `category_id` (`category_id`,`product_id`),
		KEY `order` (`order`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_rating`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_rating';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_rating` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`ipnum` varchar(30) NOT NULL default '',
		`productid` bigint(20) unsigned NOT NULL default '0',
		`rated` tinyint(1) NOT NULL default '0',
		`time` bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."product_variations`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'product_variations';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."product_variations` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`name` varchar(128) NOT NULL default '',
		`variation_association` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `variation_association` (`variation_association`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."purchase_logs`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'purchase_logs';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."purchase_logs` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`totalprice` varchar(128) NOT NULL default '0',
		`statusno` smallint(6) NOT NULL default '0',
		`sessionid` varchar(255) NOT NULL default '',
		`transactid` varchar(255) NOT NULL default '',
		`authcode` varchar(255) NOT NULL default '',
		`downloadid` bigint(20) unsigned NOT NULL default '0',
		`processed` bigint(20) unsigned NOT NULL default '1',
		`user_ID` bigint(20) unsigned default NULL,
		`date` varchar(255) NOT NULL default '',
		`gateway` varchar(64) NOT NULL default '',
		`billing_country` char(6) NOT NULL default '',
		`shipping_country` char(6) NOT NULL default '',
		`base_shipping` varchar(128) NOT NULL default '0',
		`email_sent` char(1) NOT NULL default '0',
		`discount_value` varchar(32) NOT NULL default '0',
		`discount_data` text NOT NULL,
		`track_id` varchar(50) default NULL default '',
		`shipping_region` char(6) NOT NULL default '',
		`find_us` varchar(255) NOT NULL  default '',
		`engravetext` varchar(255) default NULL,
		`closest_store` varchar(255) default NULL,
		`google_order_number` varchar(20) NOT NULL default '',
		`google_user_marketing_preference` varchar(10) NOT NULL default '',
		`google_status` longtext NOT NULL,
		PRIMARY KEY  (`id`),
		UNIQUE KEY `sessionid` (`sessionid`),
		KEY `gateway` (`gateway`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."purchase_statuses`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'purchase_statuses';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."purchase_statuses` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`name` varchar(128) NOT NULL default '',
		`active` varchar(1) NOT NULL default '0',
		`colour` varchar(6) NOT NULL default '',
		PRIMARY KEY  (`id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."region_tax`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'region_tax';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."region_tax` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`country_id` bigint(20) unsigned NOT NULL default '0',
		`name` varchar(64) NOT NULL default '',
		`code` char(2) NOT NULL default '',
		`tax` float NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `country_id` (`country_id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."submited_form_data`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'submited_form_data';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."submited_form_data` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`log_id` bigint(20) unsigned NOT NULL default '0',
		`form_id` bigint(20) unsigned NOT NULL default '0',
		`value` varchar(255) NOT NULL default '',
		PRIMARY KEY  (`id`),
		KEY `log_id` (`log_id`,`form_id`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."variation_associations`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'variation_associations';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."variation_associations` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`type` varchar(64) NOT NULL default '',
		`name` varchar(128) NOT NULL default '',
		`associated_id` bigint(20) unsigned NOT NULL default '0',
		`variation_id` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `associated_id` (`associated_id`),
		KEY `variation_id` (`variation_id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."variation_priceandstock`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'variation_priceandstock';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."variation_priceandstock` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`product_id` bigint(20) unsigned NOT NULL default '0',
		`variation_id_1` bigint(20) unsigned NOT NULL default '0',
		`variation_id_2` bigint(20) unsigned NOT NULL default '0',
		`stock` bigint(20) unsigned NOT NULL default '0',
		`price` varchar(32) NOT NULL default '0',
		`file` varchar(1) NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `product_id` (`product_id`),
		KEY `variation_id_1` (`variation_id_1`,`variation_id_2`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."variation_values`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'variation_values';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."variation_values` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`name` varchar(128) NOT NULL default '',
		`variation_id` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `variation_id` (`variation_id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."variation_values_associations`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'variation_values_associations';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."variation_values_associations` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`product_id` bigint(20) unsigned NOT NULL default '0',
		`value_id` bigint(20) unsigned NOT NULL default '0',
		`quantity` int(11) NOT NULL default '0',
		`price` varchar(32) NOT NULL default '0',
		`visible` varchar(1) NOT NULL default '0',
		`variation_id` bigint(20) unsigned NOT NULL default '0',
		PRIMARY KEY  (`id`),
		KEY `product_id` (`product_id`,`value_id`,`variation_id`)
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."wpsc_coupon_codes`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'wpsc_coupon_codes';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."wpsc_coupon_codes` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`coupon_code` varchar(255) default NULL,
		`value` bigint(20) unsigned NOT NULL default '0',
		`is-percentage` char(1) NOT NULL default '0',
		`use-once` char(1) NOT NULL default '0',
		`is-used` char(1) NOT NULL default '0',
		`active` char(1) NOT NULL default '1',
		`every_product` varchar(255) NOT NULL,
		`start` datetime NOT NULL,
		`expiry` datetime NOT NULL,
		PRIMARY KEY  (`id`),
		KEY `coupon_code` (`coupon_code`),
		KEY `active` (`active`),
		KEY `start` (`start`),
		KEY `expiry` (`expiry`)
	) TYPE=MyISAM ;
	";
	
	
	// Table structure for table `".$wpdb->prefix."wpsc_logged_subscriptions`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'wpsc_logged_subscriptions';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."wpsc_logged_subscriptions` (
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
	) TYPE=MyISAM;
	";
	
	
	// Table structure for table `".$wpdb->prefix."wpsc_productmeta`
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'wpsc_productmeta';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."wpsc_productmeta` (
		`id` bigint(20) unsigned NOT NULL auto_increment,
		`product_id` bigint(20) unsigned NOT NULL default '0',
		`meta_key` varchar(255) default NULL,
		`meta_value` longtext,
		PRIMARY KEY  (`id`),
		KEY `product_id` (`product_id`),
		KEY `meta_key` (`meta_key`)
	) TYPE=MyISAM ;
	";	
	
	$num++;
	$wpsc_tables[$num]['table_name'] = $wpdb->prefix.'wpsc_categorisation_groups';
	$wpsc_tables[$num]['table_sql'] = "CREATE TABLE `".$wpdb->prefix."wpsc_categorisation_groups` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` varchar(1) NOT NULL default '1',
  `default` varchar(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `group_name` (`name`)
) ENGINE=MyISAM ;
	";


	// and here is where the tables are added to the database, fairly simple, if it doesnt find the table, it makes it
	foreach($wpsc_tables as $wpsc_table) {
		if(!$wpdb->get_var("SHOW TABLES LIKE '{$wpsc_table['table_name']}'")) {
			$wpdb->query($wpsc_table['table_sql']);
		}
	}
 
  wpsc_create_upload_directories();
  
 
	require dirname(__FILE__) . "/currency_list.php";
  /*
  Updates from old versions, 
  */  
	if(get_option('wpsc_version') <= 3.5) {
		include_once('updates/update-to-3.5.0.php');
	}
//   
//   if((get_option('wpsc_version') < 3.5 ) || ((get_option('wpsc_version') == 3.5 ) && (get_option('wpsc_minor_version') <= 2))) {
    include_once('updates/update-to-3.5.2.php');
//     }
    
    include_once('updates/update-to-3.5.2.php');
    include_once('updates/update-to-3.6.0.php');
    include_once('updates/update-to-3.6.4.php');
  /* all code to add new database tables and columns must be above here */  
  if((get_option('wpsc_version') < WPSC_VERSION) || (get_option('wpsc_version') == WPSC_VERSION) && (get_option('wpsc_minor_version') < WPSC_MINOR_VERSION)) {
    update_option('wpsc_version', WPSC_VERSION);
    update_option('wpsc_minor_version', WPSC_MINOR_VERSION);
	}

  $currency_data  = $wpdb->get_var("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."currency_list`");
  if($currency_data == 0) {
    $currency_array = explode("\n",$currency_sql);
    foreach($currency_array as $currency_row) {
      $wpdb->query($currency_row);
		}
	}

  $add_initial_category = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."product_categories`;",ARRAY_A);
  if($add_initial_category[0]['count'] == 0) {
		$wpdb->query("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` (`id`, `name`, `description`, `active`, `default`) VALUES (1, 'Categories', 'Product Categories', '1', '1')");
		$wpdb->query("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` (`id`, `name`, `description`, `active`, `default`) VALUES (2, 'Brands', 'Product Brands', '1', '0')");	
		
    $wpdb->query("INSERT INTO `".$wpdb->prefix."product_categories` (`group_id`, `name` , `description`, `active`) VALUES ('1', '".TXT_WPSC_EXAMPLECATEGORY."', '".TXT_WPSC_EXAMPLEDETAILS."', '1');");    
    $wpdb->query("INSERT INTO `".$wpdb->prefix."product_categories` (`group_id`, `name` , `description`, `active`) VALUES ('2', '".TXT_WPSC_EXAMPLEBRAND."', '".TXT_WPSC_EXAMPLEDETAILS."', '1');");
	}
  

  $purchase_statuses_data  = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."purchase_statuses`",ARRAY_A);
  if($purchase_statuses_data[0]['count'] == 0) {
    $wpdb->query("INSERT INTO `".$wpdb->prefix."purchase_statuses` (`name` , `active` , `colour` ) 
    VALUES
    ('".TXT_WPSC_RECEIVED."', '1', ''),
    ('".TXT_WPSC_ACCEPTED_PAYMENT."', '1', ''),
    ('".TXT_WPSC_JOB_DISPATCHED."', '1', ''),
    ('".TXT_WPSC_PROCESSED."', '1', '');");
	}

  $check_category_assoc = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."item_category_associations`;",ARRAY_A);
  if($check_category_assoc[0]['count'] == 0) {
    $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `active`=1";
    $product_list = $wpdb->get_results($sql,ARRAY_A);
    foreach((array)$product_list as $product) {
      $results = $wpdb->query("INSERT INTO `".$wpdb->prefix."item_category_associations` (`product_id` , `category_id` ) VALUES ('".$product['id']."', '".$product['category']."');");
		}
	}
  
  
  $add_regions = $wpdb->get_var("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."region_tax`");
  // exit($add_regions);
  if($add_regions < 1) {
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Alberta', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'British Columbia', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Manitoba', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'New Brunswick', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Newfoundland', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Northwest Territories', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Nova Scotia', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Nunavut', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Ontario', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Prince Edward Island', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Quebec', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Saskatchewan', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '100', 'Yukon', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Alabama', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Alaska', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Arizona', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Arkansas', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'California', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Colorado', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Connecticut', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Delaware', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Florida', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Georgia', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Hawaii', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Idaho', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Illinois', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Indiana', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Iowa', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Kansas', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Kentucky', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Louisiana', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Maine', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Maryland', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Massachusetts', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Michigan', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Minnesota', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Mississippi', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Missouri', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Montana', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Nebraska', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Nevada', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'New Hampshire', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'New Jersey', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'New Mexico', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'New York', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'North Carolina', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'North Dakota', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Ohio', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Oklahoma', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Oregon', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Pennsylvania', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Rhode Island', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'South Carolina', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'South Dakota', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Tennessee', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Texas', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Utah', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Vermont', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Virginia', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Washington', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Washington DC', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'West Virginia', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Wisconsin', '0.00')");
    $wpdb->query("INSERT INTO `".$wpdb->prefix."region_tax` ( `country_id` , `name` , `tax` ) VALUES ( '136', 'Wyoming', '0.00')");
    
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'AL' WHERE `name` IN('Alabama')LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'AK' WHERE `name` IN('Alaska') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'AZ' WHERE `name` IN('Arizona') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'AR' WHERE `name` IN('Arkansas') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'CA' WHERE `name` IN('California') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'CO' WHERE `name` IN('Colorado') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'CT' WHERE `name` IN('Connecticut') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'DE' WHERE `name` IN('Delaware') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'FL' WHERE `name` IN('Florida') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'GA' WHERE `name` IN('Georgia')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'HI' WHERE `name` IN('Hawaii')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'ID' WHERE`name` IN('Idaho')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'IL' WHERE `name` IN('Illinois')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'IN' WHERE `name` IN('Indiana')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'IA' WHERE `name` IN('Iowa')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'KS' WHERE `name` IN('Kansas')  LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'KY' WHERE `name` IN('Kentucky') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'LA' WHERE `name` IN('Louisiana') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'ME' WHERE `name` IN('Maine') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MD' WHERE `name` IN('Maryland') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MA' WHERE `name` IN('Massachusetts') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MI' WHERE `name` IN('Michigan') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MN' WHERE `name` IN('Minnesota') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MS' WHERE `name` IN('Mississippi') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MO' WHERE `name` IN('Missouri') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'MT' WHERE `name` IN('Montana') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NE' WHERE `name` IN('Nebraska') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NV' WHERE `name` IN('Nevada') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NH' WHERE `name` IN('New Hampshire') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NJ' WHERE `name` IN('New Jersey') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NM' WHERE `name` IN('New Mexico') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NY' WHERE `name` IN('New York') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'NC' WHERE `name` IN('North Carolina') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'ND' WHERE `name` IN('North Dakota') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'OH' WHERE `name` IN('Ohio') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'OK' WHERE `name` IN('Oklahoma') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'OR' WHERE `name` IN('Oregon') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'PA' WHERE `name` IN('Pennsylvania') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'RI' WHERE `name` IN('Rhode Island') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'SC' WHERE `name` IN('South Carolina') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'SD' WHERE `name` IN('South Dakota') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'TN' WHERE `name` IN('Tennessee') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'TX' WHERE `name` IN('Texas') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'UT' WHERE `name` IN('Utah') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'VT' WHERE `name` IN('Vermont') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'VA' WHERE `name` IN('Virginia') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'WA' WHERE `name` IN('Washington') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'DC' WHERE `name` IN('Washington DC') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'WV' WHERE `name` IN('West Virginia') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'WI' WHERE `name` IN('Wisconsin') LIMIT 1 ;");
		$wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `code` = 'WY' WHERE `name` IN('Wyoming') LIMIT 1 ;");    
	}
    
    
	$data_forms = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."collect_data_forms`",ARRAY_A);
	if($data_forms[0]['count'] == 0) { 
	$wpdb->query("INSERT INTO `".$wpdb->prefix."collect_data_forms` ( `name`, `type`, `mandatory`, `display_log`, `default`, `active`, `order`) VALUES ( '".TXT_WPSC_YOUR_BILLING_CONTACT_DETAILS."', 'heading', '0', '0', '', '1', 1),
	( '".TXT_WPSC_FIRSTNAME."', 'text', '1', '1', '', '1', 2),
	( '".TXT_WPSC_LASTNAME."', 'text', '1', '1', '', '1', 3),
	( '".TXT_WPSC_ADDRESS."', 'address', '1', '0', '', '1', 4),
	( '".TXT_WPSC_CITY."', 'city', '1', '0', '', '1', 5),
	( '".TXT_WPSC_COUNTRY."', 'country', '1', '0', '', '1', 7),
	( '".TXT_WPSC_POSTAL_CODE."', 'text', '0', '0', '', '1', 8),
	( '".TXT_WPSC_EMAIL."', 'email', '1', '1', '', '1', 9),
	( '".TXT_WPSC_DELIVER_TO_A_FRIEND."', 'heading', '0', '0', '', '1', 10),
	( '".TXT_WPSC_FIRSTNAME."', 'text', '0', '0', '', '1', 11),
	( '".TXT_WPSC_LASTNAME."', 'text', '0', '0', '', '1', 12),
	( '".TXT_WPSC_ADDRESS."', 'address', '0', '0', '', '1', 13),
	( '".TXT_WPSC_CITY."', 'city', '0', '0', '', '1', 14),
	( '".TXT_WPSC_STATE."', 'text', '0', '0', '', '1', 15),
	( '".TXT_WPSC_COUNTRY."', 'delivery_country', '0', '0', '', '1', 16),
	( '".TXT_WPSC_POSTAL_CODE."', 'text', '0', '0', '', '1', 17);");  
		update_option('country_form_field', $country_form_id[0]['id']);
		update_option('email_form_field', $email_form_id[0]['id']);
		$wpdb->query("INSERT INTO `".$wpdb->prefix."collect_data_forms` ( `name`, `type`, `mandatory`, `display_log`, `default`, `active`, `order` ) VALUES ( '".TXT_WPSC_PHONE."', 'text', '1', '0', '', '1', '8');");
	}
		
		
	$product_brands_data  = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."product_brands`",ARRAY_A);
	if($product_brands_data[0]['count'] == 0) {
		$wpdb->query("INSERT INTO `".$wpdb->prefix."product_brands` ( `name`, `description`, `active`, `order`) VALUES ( '".TXT_WPSC_EXAMPLEBRAND."','".TXT_WPSC_EXAMPLEDETAILS."', '1', '0');");
	}
  
  add_option('show_thumbnails', 1, TXT_WPSC_SHOWTHUMBNAILS, "yes");

  add_option('product_image_width', '', TXT_WPSC_PRODUCTIMAGEWIDTH, 'yes');
  add_option('product_image_height', '', TXT_WPSC_PRODUCTIMAGEHEIGHT, 'yes');

  add_option('category_image_width', '', TXT_WPSC_CATEGORYIMAGEWIDTH, 'yes');
  add_option('category_image_height', '', TXT_WPSC_CATEGORYIMAGEHEIGHT, 'yes');

  add_option('product_list_url', '', TXT_WPSC_PRODUCTLISTURL, 'yes');
  add_option('shopping_cart_url', '', TXT_WPSC_SHOPPINGCARTURL, 'yes');
  add_option('checkout_url', '', TXT_WPSC_CHECKOUTURL, 'yes');
  add_option('transact_url', '', TXT_WPSC_TRANSACTURL, 'yes');
  add_option('payment_gateway', '', TXT_WPSC_PAYMENTGATEWAY, 'yes');
  if(function_exists('register_sidebar') ) {
    add_option('cart_location', '4', TXT_WPSC_CARTLOCATION, 'yes');
	} else {
    add_option('cart_location', '1', TXT_WPSC_CARTLOCATION, 'yes');
	}

  if ( function_exists('register_sidebar') ) {
    add_option('cart_location', '4', TXT_WPSC_CARTLOCATION, 'yes');
  } else {
		add_option('cart_location', '1', TXT_WPSC_CARTLOCATION, 'yes');
  }

  //add_option('show_categorybrands', '0', TXT_WPSC_SHOWCATEGORYBRANDS, 'yes');

  add_option('currency_type', '156', TXT_WPSC_CURRENCYTYPE, 'yes');
  add_option('currency_sign_location', '3', TXT_WPSC_CURRENCYSIGNLOCATION, 'yes');

  add_option('gst_rate', '1', TXT_WPSC_GSTRATE, 'yes');

  add_option('max_downloads', '1', TXT_WPSC_MAXDOWNLOADS, 'yes');

  add_option('display_pnp', '1', TXT_WPSC_DISPLAYPNP, 'yes');

  add_option('display_specials', '1', TXT_WPSC_DISPLAYSPECIALS, 'yes');
  add_option('do_not_use_shipping', '0', 'do_not_use_shipping', 'yes');

  add_option('postage_and_packaging', '0', TXT_WPSC_POSTAGEAND_PACKAGING, 'yes');
  
  add_option('purch_log_email', '', TXT_WPSC_PURCHLOGEMAIL, 'yes');
  add_option('return_email', '', TXT_WPSC_RETURNEMAIL, 'yes');
  add_option('terms_and_conditions', '', TXT_WPSC_TERMSANDCONDITIONS, 'yes');

	add_option('google_key', 'none', TXT_WPSC_GOOGLEMECHANTKEY, 'yes');
	add_option('google_id', 'none', TXT_WPSC_GOOGLEMECHANTID, 'yes');
 
   add_option('default_brand', 'none', TXT_WPSC_DEFAULTBRAND, 'yes');
   add_option('wpsc_default_category', 'none', TXT_WPSC_DEFAULTCATEGORY, 'yes');
   
   add_option('product_view', 'default', "", 'yes');
   add_option('add_plustax', 'default', "", '1');
    
   
	add_option('nzshpcrt_first_load', '0', "", 'yes');
  
  if(!((get_option('show_categorybrands') > 0) && (get_option('show_categorybrands') < 3))) {
    update_option('show_categorybrands', 2);
	}
  //add_option('show_categorybrands', '0', TXT_WPSC_SHOWCATEGORYBRANDS, 'yes');
  /* PayPal options */
  add_option('paypal_business', '', TXT_WPSC_PAYPALBUSINESS, 'yes');
  add_option('paypal_url', '', TXT_WPSC_PAYPALURL, 'yes');
  //update_option('paypal_url', "https://www.sandbox.paypal.com/xclick");
  
  
  add_option('paypal_multiple_business', '', TXT_WPSC_PAYPALBUSINESS, 'yes');
  
  if(get_option('paypal_multiple_url') == null) {
    add_option('paypal_multiple_url', '', TXT_WPSC_PAYPALURL, 'yes');
    update_option('paypal_multiple_url', "https://www.paypal.com/cgi-bin/webscr");
	}

  add_option('product_ratings', '0', TXT_WPSC_SHOWPRODUCTRATINGS, 'yes');
  
  if(get_option('wpsc_selected_theme') == '') {
    add_option('wpsc_selected_theme', 'default', 'Selected Theme', 'yes');
    update_option('wpsc_selected_theme', "default");
	}


	
	if(!get_option('product_image_height')) {
		update_option('product_image_height', '96');
		update_option('product_image_width', '96');
	}
		
		
	if(!get_option('category_image_height')) {
		update_option('category_image_height', '96');
		update_option('category_image_width', '96');
	}
		
		
	if(!get_option('single_view_image_height')) {
		update_option('single_view_image_height', '128');
		update_option('single_view_image_width', '128');
	}
  
  wpsc_product_files_htaccess();
  
	/*
	* This part creates the pages and automatically puts their URLs into the options page.
	* As you can probably see, it is very easily extendable, just pop in your page and the deafult content in the array and you are good to go.
	*/
  $post_date =date("Y-m-d H:i:s");
  $post_date_gmt =gmdate("Y-m-d H:i:s");
  
  $num=0;
  $pages[$num]['name'] = 'products-page';
  $pages[$num]['title'] = TXT_WPSC_PRODUCTSPAGE;
  $pages[$num]['tag'] = '[productspage]';
  $pages[$num]['option'] = 'product_list_url';
  
  $num++;
  $pages[$num]['name'] = 'checkout';
  $pages[$num]['title'] = TXT_WPSC_CHECKOUT;
  $pages[$num]['tag'] = '[shoppingcart]';
  $pages[$num]['option'] = 'shopping_cart_url';
  
//   $num++;
//   $pages[$num]['name'] = 'enter-details';
//   $pages[$num]['title'] = TXT_WPSC_ENTERDETAILS;
//   $pages[$num]['tag'] = '[checkout]';
//   $pages[2$num]['option'] = 'checkout_url';

  $num++;
  $pages[$num]['name'] = 'transaction-results';
  $pages[$num]['title'] = TXT_WPSC_TRANSACTIONRESULTS;
  $pages[$num]['tag'] = '[transactionresults]';
  $pages[$num]['option'] = 'transact_url';
  
  $num++;
  $pages[$num]['name'] = 'your-account';
  $pages[$num]['title'] = TXT_WPSC_YOUR_ACCOUNT;
  $pages[$num]['tag'] = '[userlog]';
  $pages[$num]['option'] = 'user_account_url';
  
  $newpages = false;
  $i = 0;
  $post_parent = 0;
  foreach($pages as $page) {
    $check_page = $wpdb->get_row("SELECT * FROM `".$wpdb->posts."` WHERE `post_content` LIKE '%".$page['tag']."%' LIMIT 1",ARRAY_A);
    if($check_page == null) {
      if($i == 0) {
        $post_parent = 0;
			} else {
        $post_parent = $first_id;
			}
      
      if($wp_version >= 2.1) {
        $sql ="INSERT INTO ".$wpdb->posts."
        (post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_type)
        VALUES
        ('1', '$post_date', '$post_date_gmt', '".$page['tag']."', '', '".$page['title']."', '', 'publish', 'closed', 'closed', '', '".$page['name']."', '', '', '$post_date', '$post_date_gmt', '$post_parent', '0', 'page')";
			} else {      
        $sql ="INSERT INTO ".$wpdb->posts."
        (post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order)
        VALUES
        ('1', '$post_date', '$post_date_gmt', '".$page['tag']."', '', '".$page['title']."', '', 'static', 'closed', 'closed', '', '".$page['name']."', '', '', '$post_date', '$post_date_gmt', '$post_parent', '0')";
			}
      $wpdb->query($sql);
      $post_id = $wpdb->insert_id;
      if($i == 0) {
        $first_id = $post_id;
        }
      $wpdb->query("UPDATE $wpdb->posts SET guid = '" . get_permalink($post_id) . "' WHERE ID = '$post_id'");
      update_option($page['option'],  get_permalink($post_id));
      if($page['option'] == 'shopping_cart_url') {
        update_option('checkout_url',  get_permalink($post_id));
			}
      $newpages = true;
      $i++;
		}
	}
  if($newpages == true) {
    wp_cache_delete('all_page_ids', 'pages');
    $wp_rewrite->flush_rules();
	}
   
   
   /* adds nice names for permalinks for products */
   $check_product_names = $wpdb->get_results("SELECT `".$wpdb->prefix."product_list`.`id`, `".$wpdb->prefix."product_list`.`name`, `".$wpdb->prefix."wpsc_productmeta`.`meta_key` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."wpsc_productmeta` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."wpsc_productmeta`.`product_id` WHERE (`".$wpdb->prefix."wpsc_productmeta`.`meta_key` IN ('url_name') AND  `".$wpdb->prefix."wpsc_productmeta`.`meta_value` IN (''))  OR ISNULL(`".$wpdb->prefix."wpsc_productmeta`.`meta_key`)");  
  if($check_product_names != null) {
    $sql_query = "SELECT `id`, `name` FROM `".$wpdb->prefix."product_list` WHERE `active` IN('1')";
    $sql_data = $wpdb->get_results($sql_query,ARRAY_A);    
    foreach((array)$sql_data as $datarow) {
      $tidied_name = trim($datarow['name']);
      $tidied_name = strtolower($tidied_name);
      $url_name = preg_replace(array("/(\s)+/","/[^\w-]+/"), array("-", ''), $tidied_name);            
      $similar_names = $wpdb->get_row("SELECT COUNT(*) AS `count`, MAX(REPLACE(`meta_value`, '$url_name', '')) AS `max_number` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` LIKE 'url_name' AND `meta_value` REGEXP '^($url_name){1}(\d)*$' ",ARRAY_A);
      $extension_number = '';
      if($similar_names['count'] > 0) {
        $extension_number = (int)$similar_names['max_number']+1;
			}      
      if(get_product_meta($datarow['id'], 'url_name') != false) {
        $current_url_name = get_product_meta($datarow['id'], 'url_name');
        if($current_url_name[0] != $url_name) {
          $url_name .= $extension_number;
          update_product_meta($datarow['id'], 'url_name', $url_name);
				}
			} else {
        $url_name .= $extension_number;
        add_product_meta($datarow['id'], 'url_name', $url_name, true);
			}
		}
	}
    
  
  /* adds nice names for permalinks for categories */
  $check_category_names = $wpdb->get_results("SELECT DISTINCT `nice-name` FROM `".$wpdb->prefix."product_categories` WHERE `nice-name` IN ('') AND `active` IN ('1')");
  if($check_category_names != null) {
    $sql_query = "SELECT `id`, `name` FROM `".$wpdb->prefix."product_categories` WHERE `active` IN('1')";
    $sql_data = $wpdb->get_results($sql_query,ARRAY_A);    
    foreach((array)$sql_data as $datarow) {
      $tidied_name = trim($datarow['name']);
      $tidied_name = strtolower($tidied_name);
      $url_name = preg_replace(array("/(\s)+/","/[^\w-]+/"), array("-", ''), $tidied_name);            
      $similar_names = $wpdb->get_row("SELECT COUNT(*) AS `count`, MAX(REPLACE(`nice-name`, '$url_name', '')) AS `max_number` FROM `".$wpdb->prefix."product_categories` WHERE `nice-name` REGEXP '^($url_name){1}(\d)*$' ",ARRAY_A);
      $extension_number = '';
      if($similar_names['count'] > 0) {
        $extension_number = (int)$similar_names['max_number']+1;
			}
      $url_name .= $extension_number;
      $wpdb->query("UPDATE `".$wpdb->prefix."product_categories` SET `nice-name` = '$url_name' WHERE `id` = '".$datarow['id']."' LIMIT 1 ;");
		}
		$wp_rewrite->flush_rules();
	}
    
    
  
  /* Moves images to thumbnails directory */
   // this code should no longer be needed, as most people will be using a sufficiently new version
  $image_dir = WPSC_FILE_PATH."/images/";
  $product_images = WPSC_IMAGE_DIR;
  $product_thumbnails = WPSC_THUMBNAIL_DIR;
  if(!is_dir($product_thumbnails)) {
    @ mkdir($product_thumbnails, 0775);
	}
  $product_list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `image` != ''",ARRAY_A);
  foreach((array)$product_list as $product) {
    if(!glob($product_thumbnails.$product['image'])) {
      $new_filename = $product['id']."_".$product['image'];
      if(file_exists($image_dir.$product['image'])) {
        copy($image_dir.$product['image'], $product_thumbnails.$new_filename);
        if(file_exists($product_images.$product['image'])) {
          copy($product_images.$product['image'], $product_images.$new_filename);
				}
        $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `image` = '".$new_filename."' WHERE `id`='".$product['id']."' LIMIT 1");
			} else {
        $imagedir = $product_thumbnails;
        $name = $new_filename;
        $new_image_path = $product_images.$product['image'];
        $imagepath = $product['image'];
        $height = get_option('product_image_height');
        $width  = get_option('product_image_width');
        if(file_exists($product_images.$product['image'])) {
          include("extra_image_processing.php");
          copy($product_images.$product['image'], $product_images.$new_filename);
          $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `image` = '".$new_filename."' WHERE `id`='".$product['id']."' LIMIT 1");
				}
			}
		}
	}	// */
   
}
  
function wpsc_uninstall_plugin() {
  global $wpdb;
  if(current_user_can('edit_plugins')) {
		$option_list[] = 'addtocart_or_buynow ';
		$option_list[] = 'add_plustax ';
		$option_list[] = 'base_country ';
		$option_list[] = 'base_international_shipping ';
		$option_list[] = 'base_local_shipping ';
		$option_list[] = 'cart_location ';
		$option_list[] = 'category_image_height ';
		$option_list[] = 'category_image_width ';
		$option_list[] = 'catsprods_display_type ';
		$option_list[] = 'cat_brand_loc ';
		$option_list[] = 'checkbox_variations ';
		$option_list[] = 'checkout_url ';
		$option_list[] = 'country_form_field ';
		$option_list[] = 'currency_sign_location ';
		$option_list[] = 'currency_type ';
		$option_list[] = 'default_brand ';
		$option_list[] = 'display_pnp ';
		$option_list[] = 'display_specials ';
		$option_list[] = 'do_not_use_shipping ';
		$option_list[] = 'email_form_field ';
		$option_list[] = 'fancy_notifications ';
		$option_list[] = 'googleStoreLocator ';
		$option_list[] = 'google_button_bg ';
		$option_list[] = 'google_button_size ';
		$option_list[] = 'google_cur ';
		$option_list[] = 'google_id ';
		$option_list[] = 'google_key ';
		$option_list[] = 'google_server_type ';
		$option_list[] = 'google_shipping_country ';
		$option_list[] = 'gst_rate ';
		$option_list[] = 'hide_addtocart_button ';
		$option_list[] = 'hide_name_link ';
		$option_list[] = 'language_setting ';
		$option_list[] = 'list_view_quantity ';
		$option_list[] = 'max_downloads ';
		$option_list[] = 'nzshpcrt_first_load ';
		$option_list[] = 'payment_gateway ';
		$option_list[] = 'paypal_business ';
		$option_list[] = 'paypal_curcode ';
		$option_list[] = 'paypal_ipn ';
		$option_list[] = 'paypal_multiple_business ';
		$option_list[] = 'paypal_multiple_url ';
		$option_list[] = 'paypal_url ';
		$option_list[] = 'postage_and_packaging ';
		$option_list[] = 'product_image_height ';
		$option_list[] = 'product_image_width ';
		$option_list[] = 'product_list_url ';
		$option_list[] = 'product_ratings ';
		$option_list[] = 'product_view ';
		$option_list[] = 'purch_log_email ';
		$option_list[] = 'require_register ';
		$option_list[] = 'return_email ';
		$option_list[] = 'shopping_cart_url ';
		$option_list[] = 'show_advanced_search ';
		$option_list[] = 'show_categorybrands ';
		$option_list[] = 'show_category_count ';
		$option_list[] = 'show_category_thumbnails ';
		$option_list[] = 'show_images_only ';
		$option_list[] = 'show_live_search ';
		$option_list[] = 'show_search ';
		$option_list[] = 'show_sliding_cart ';
		$option_list[] = 'show_thumbnails ';
		$option_list[] = 'single_view_image_height ';
		$option_list[] = 'single_view_image_width ';
		$option_list[] = 'terms_and_conditions ';
		$option_list[] = 'transact_url ';
		$option_list[] = 'user_account_url ';
		$option_list[] = 'use_pagination ';
		$option_list[] = 'usps_user_id ';
		$option_list[] = 'wpsc_also_bought ';
		$option_list[] = 'wpsc_category_description ';
		$option_list[] = 'wpsc_dropshop_theme ';
		$option_list[] = 'wpsc_minor_version ';
		$option_list[] = 'wpsc_page_number_position ';
		$option_list[] = 'wpsc_products_per_page ';
		$option_list[] = 'wpsc_selected_theme ';
		$option_list[] = 'wpsc_use_pnp_cols ';
		$option_list[] = 'wpsc_version'; 
		$option_list[] = 'wpsc_incomplete_file_transfer'; 
		$option_list[] = 'wpsc_ip_lock_downloads'; 
		
		foreach($option_list as $wpsc_option) {
			delete_option($wpsc_option);
		}
		
		
		$wpsc_table_list[] = $wpdb->prefix.'also_bought_product';
		$wpsc_table_list[] = $wpdb->prefix.'cart_contents';
		$wpsc_table_list[] = $wpdb->prefix.'cart_item_extras';
		$wpsc_table_list[] = $wpdb->prefix.'cart_item_variations';
		$wpsc_table_list[] = $wpdb->prefix.'collect_data_forms';
		$wpsc_table_list[] = $wpdb->prefix.'currency_list';
		$wpsc_table_list[] = $wpdb->prefix.'download_status';
		$wpsc_table_list[] = $wpdb->prefix.'extras_values';
		$wpsc_table_list[] = $wpdb->prefix.'extras_values_associations';
		$wpsc_table_list[] = $wpdb->prefix.'item_category_associations';
		$wpsc_table_list[] = $wpdb->prefix.'product_brands';
		$wpsc_table_list[] = $wpdb->prefix.'product_categories';
		$wpsc_table_list[] = $wpdb->prefix.'product_extra';
		$wpsc_table_list[] = $wpdb->prefix.'product_files';
		$wpsc_table_list[] = $wpdb->prefix.'product_images';
		$wpsc_table_list[] = $wpdb->prefix.'product_list';
		$wpsc_table_list[] = $wpdb->prefix.'product_order';
		$wpsc_table_list[] = $wpdb->prefix.'product_rating';
		$wpsc_table_list[] = $wpdb->prefix.'product_variations';
		$wpsc_table_list[] = $wpdb->prefix.'purchase_logs';
		$wpsc_table_list[] = $wpdb->prefix.'purchase_statuses';
		$wpsc_table_list[] = $wpdb->prefix.'region_tax';
		$wpsc_table_list[] = $wpdb->prefix.'submited_form_data';
		$wpsc_table_list[] = $wpdb->prefix.'variation_associations';
		$wpsc_table_list[] = $wpdb->prefix.'variation_priceandstock';
		$wpsc_table_list[] = $wpdb->prefix.'variation_values';
		$wpsc_table_list[] = $wpdb->prefix.'variation_values_associations';
		$wpsc_table_list[] = $wpdb->prefix.'wpsc_coupon_codes';
		$wpsc_table_list[] = $wpdb->prefix.'wpsc_logged_subscriptions';
		$wpsc_table_list[] = $wpdb->prefix.'wpsc_productmeta'; 
		$wpsc_table_list[] = $wpdb->prefix.'wpsc_categorisation_groups'; 
		
		foreach($wpsc_table_list as $wpsc_table_name) {
			$wpdb->query("DROP TABLE `{$wpsc_table_name}`");
		}
		$active_plugins = get_option('active_plugins');
		unset($active_plugins[array_search(WPSC_DIR_NAME.'/wp-shopping-cart.php', $active_plugins)]);
		update_option('active_plugins', $active_plugins);
		header('Location: '.get_option('siteurl').'/wp-admin/plugins.php');
		exit();
	}
}

function wpsc_uninstall_plugin_link($plugin) {
	if(($plugin == WPSC_DIR_NAME.'/wp-shopping-cart.php') && current_user_can('edit_plugins')) {
		echo "<td class='plugin-update' colspan='5' style='background: #ff7777;'>";
		echo "Are you sure, uninstalling will permanently delete all your wp-e-commerce settings: <a href='?wpsc_uninstall=verified'>Yes</a> or <a href='plugins.php'>No</a>";
		echo "</td>";
	}
}
if($_GET['wpsc_uninstall'] === 'verified') {
  add_action( 'init', 'wpsc_uninstall_plugin' );
}

if($_GET['wpsc_uninstall'] === 'ask') {
  add_action( 'after_plugin_row', 'wpsc_uninstall_plugin_link' );
}
// add_action( 'after_plugin_row', 'wpsc_uninstall_plugin_link' );

function wpsc_product_files_htaccess() {
  if(!is_file(WPSC_FILE_DIR.".htaccess")) {
		$htaccess = "order deny,allow\n\r";
		$htaccess .= "deny from all\n\r";
		$htaccess .= "allow from none\n\r";
		$filename = WPSC_FILE_DIR.".htaccess";
		$file_handle = @ fopen($filename, 'w+');
		@ fwrite($file_handle, $htaccess);
		@ fclose($file_handle);
		@ chmod($file_handle, 665);
  }
}



function wpsc_check_and_copy_files() {
  $upload_path = 'wp-content/plugins/'.WPSC_DIR_NAME;
  
	$wpsc_dirs['files']['old'] = ABSPATH."{$upload_path}/files/";
	$wpsc_dirs['files']['new'] = WPSC_FILE_DIR;
	
	$wpsc_dirs['previews']['old'] = ABSPATH."{$upload_path}/preview_clips/";
	$wpsc_dirs['previews']['new'] = WPSC_PREVIEW_DIR;
	
	// I don't include the thumbnails directory in this list, as it is a subdirectory of the images directory and is moved along with everything else
	$wpsc_dirs['images']['old'] = ABSPATH."{$upload_path}/product_images/";
	$wpsc_dirs['images']['new'] = WPSC_IMAGE_DIR;
	
	$wpsc_dirs['categories']['old'] = ABSPATH."{$upload_path}/category_images/";
	$wpsc_dirs['categories']['new'] = WPSC_CATEGORY_DIR;
	$incomplete_file_transfer = false;
	foreach($wpsc_dirs as $wpsc_dir) {
	  if(is_dir($wpsc_dir['old'])) {
	    $files_in_dir = glob($wpsc_dir['old']."*");
			$stat = stat($wpsc_dir['new']);
			
	    if(count($files_in_dir) > 0) {
	      foreach($files_in_dir as $file_in_dir) {
	        $file_name = str_replace($wpsc_dir['old'], '', $file_in_dir);
	        if( @ rename($wpsc_dir['old'].$file_name, $wpsc_dir['new'].$file_name) ) {
	          if(is_dir($wpsc_dir['new'].$file_name)) {
							$perms = $stat['mode'] & 0000775;
	          } else { $perms = $stat['mode'] & 0000665; }
	          
	          @ chmod( ($wpsc_dir['new'].$file_name), $perms );	
	        } else {
	          $incomplete_file_transfer = true;
	        }
	      }
	    }
	  }
	}
	if($incomplete_file_transfer == true) {
		add_option('wpsc_incomplete_file_transfer', 'default', "", 'true');
	}
}


function wpsc_create_upload_directories() {
  $wpsc_files_directory = WP_CONTENT_DIR.'/uploads/wpsc/';
  
  if(!is_dir(WP_CONTENT_DIR.'/uploads')) {
	  @ mkdir(WP_CONTENT_DIR.'/uploads', 0775);
  }

  if(!is_dir($wpsc_files_directory)) {
	  @ mkdir($wpsc_files_directory, 0775);
  }
  
  if(!is_dir(WPSC_FILE_DIR)) {
	  @ mkdir(WPSC_FILE_DIR, 0775);
		wpsc_product_files_htaccess();  
  }
  
	if(!is_dir(WPSC_PREVIEW_DIR)) {
		@ mkdir(WPSC_PREVIEW_DIR, 0775);
	}
		
	if(!is_dir(WPSC_IMAGE_DIR)) {
		@ mkdir(WPSC_IMAGE_DIR, 0775);
	}
		
	if(!is_dir(WPSC_THUMBNAIL_DIR)) {
		@ mkdir(WPSC_THUMBNAIL_DIR, 0775);
	}
		
	if(!is_dir(WPSC_CATEGORY_DIR)) {
		@ mkdir(WPSC_CATEGORY_DIR, 0775);
	}
		
	if(!is_dir(WPSC_USER_UPLOADS_DIR)) {
		@ mkdir(WPSC_USER_UPLOADS_DIR, 0775);
	}
	
	
	$wpsc_file_directory = ABSPATH.get_option('upload_path').'/wpsc/';
	if(is_dir($wpsc_file_directory)) {
	  // sort the permissions out in case they are not already sorted out.	  
		@ chmod( ABSPATH.get_option('upload_path'), 0775 );			
		@ chmod( $wpsc_file_directory, 0775 );			
		@ chmod( WPSC_FILE_DIR, 0775 );			
		@ chmod( WPSC_PREVIEW_DIR, 0775 );			
		@ chmod( WPSC_IMAGE_DIR, 0775 );	
		@ chmod( WPSC_CATEGORY_DIR, 0775 );	
		@ chmod( WPSC_USER_UPLOADS_DIR, 0775 );	
	}
}

?>