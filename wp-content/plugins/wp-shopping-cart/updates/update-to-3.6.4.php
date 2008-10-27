<?php
//Add table logged_subscription
if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."cart_item_extras'") != ($wpdb->prefix."cart_item_extras")) {
   $wpsc_cart_item_extras = "CREATE TABLE `".$wpdb->prefix."cart_item_extras` (
  `id` int(11) NOT NULL auto_increment,
  `cart_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
  $wpdb->query($wpsc_cart_item_extras);
  }


if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."extras_values'") != ($wpdb->prefix."extras_values")) {
   $wpsc_extras_values= "CREATE TABLE `".$wpdb->prefix."extras_values` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `extras_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
  $wpdb->query($wpsc_extras_values);
  }


if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."item_category_associations'") != ($wpdb->prefix."item_category_associations")) {
   $wpsc_extras_values= "CREATE TABLE `".$wpdb->prefix."item_category_associations` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `product_id` bigint(20) unsigned NOT NULL default '0',
  `category_id` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `product_id` (`product_id`,`category_id`)
) ENGINE=MyISAM;";
  $wpdb->query($wpsc_extras_values);
  }

if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."extras_values_associations'") != ($wpdb->prefix."extras_values_associations")) {
   $wpsc_extras_values= "CREATE TABLE `".$wpdb->prefix."extras_values_associations` (
  `id` int(11) NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `price` varchar(20) NOT NULL,
  `visible` varchar(1) NOT NULL,
  `extras_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
  $wpdb->query($wpsc_extras_values);
  }

if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."product_extra'") != ($wpdb->prefix."product_extra")) {
   $wpsc_extras_values= "CREATE TABLE `".$wpdb->prefix."product_extra` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";
  $wpdb->query($wpsc_extras_values);
  }
  

if($wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."cart_item_variations` LIKE 'venue_id';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."cart_item_variations` CHANGE `venue_id` `value_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0' ");
}


if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'find_us';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `find_us` varchar(255) NOT NULL");
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `engravetext` varchar(255) default NULL");
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `closest_store` varchar(255) default NULL");
}


if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."download_status` LIKE 'uniqueid';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."download_status` ADD `uniqueid` VARCHAR( 64 ) NULL AFTER `purchid`;");
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."download_status` ADD UNIQUE (`uniqueid`);");
}


if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."region_tax` LIKE 'code';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."region_tax` ADD `code` char(2) NOT NULL default '' AFTER `name`;");    
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


$coldata  = $wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'totalprice'",ARRAY_A);
if($coldata[0]['Type'] != "varchar(128)")	{
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` CHANGE `totalprice` `totalprice` VARCHAR( 128 ) DEFAULT '0' NOT NULL");
	}
$coldata  = $wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'base_shipping'",ARRAY_A);
if($coldata[0]['Type'] != "varchar(128)")	{
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` CHANGE `base_shipping` `base_shipping` VARCHAR( 128 ) DEFAULT '0' NOT NULL");
	}
		
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."product_list` LIKE 'no_shipping';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."product_list` ADD `no_shipping` varchar(1) NOT NULL DEFAULT '0' AFTER `donation`;");
}

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."wpsc_coupon_codes` LIKE 'every_product';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."wpsc_coupon_codes` ADD `every_product` varchar(255) NOT NULL AFTER `active`");
}


  
  
if($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}wpsc_categorisation_groups'") != ($wpdb->prefix."wpsc_categorisation_groups")) {
   $wpsc_categorisation_groups= "CREATE TABLE `{$wpdb->prefix}wpsc_categorisation_groups` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `active` varchar(1) NOT NULL default '1',
  `default` varchar(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `group_name` (`name`)
) ENGINE=MyISAM ; 
";
  $wpdb->query($wpsc_categorisation_groups);
  
  $wpdb->query("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` (`id`, `name`, `description`, `active`, `default`) VALUES (1, 'Categories', 'Product Categories', '1', '1')");
  $wpdb->query("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` (`id`, `name`, `description`, `active`, `default`) VALUES (2, 'Brands', 'Product Brands', '1', '0')");
}

if($wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}wpsc_categorisation_groups`") < 1) {
  $wpdb->query("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` (`id`, `name`, `description`, `active`, `default`) VALUES (1, 'Categories', 'Product Categories', '1', '1')");
  $wpdb->query("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` (`id`, `name`, `description`, `active`, `default`) VALUES (2, 'Brands', 'Product Brands', '1', '0')");
}


if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `{$wpdb->prefix}product_categories` LIKE 'group_id';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}product_categories` ADD `group_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `id`");
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}product_categories` ADD INDEX ( `group_id` ) ;");
}


$brand_group = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}wpsc_categorisation_groups` WHERE `name` IN ( 'Brands' ) ",ARRAY_A);
if($brand_group == null) {
	$wpdb->get_row("INSERT INTO `{$wpdb->prefix}wpsc_categorisation_groups` ( `name`, `description`, `active`, `default`) VALUES ( 'Brands', 'Product Brands', '1', '0');", ARRAY_A);
	$brand_group = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}wpsc_categorisation_groups` WHERE `name` IN ( 'Brands' ) ",ARRAY_A);
}
	
$converted_brand_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}product_categories` WHERE `group_id` IN({$brand_group['id']}) AND `active` IN('1') ");
if($converted_brand_count <= 0) {
	$brands = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}product_brands` ",ARRAY_A);
	if(count($brands) > 0 ) {
		foreach($brands as $brand) {
			
			$tidied_name = trim($brand['name']);
			$tidied_name = strtolower($tidied_name);
			$url_name = preg_replace(array("/(\s)+/","/[^\w-]+/"), array("-", ''), $tidied_name);
			if($url_name != $category_data['nice-name']) {
				$similar_names = $wpdb->get_row("SELECT COUNT(*) AS `count`, MAX(REPLACE(`nice-name`, '$url_name', '')) AS `max_number` FROM `".$wpdb->prefix."product_categories` WHERE `nice-name` REGEXP '^($url_name){1}(0-9)*$' AND `id` NOT IN ('".(int)$category_data['id']."') ",ARRAY_A);
				//exit("<pre>".print_r($similar_names,true)."</pre>");
				$extension_number = '';
				if($similar_names['count'] > 0) {
					$extension_number = (int)$similar_names['max_number']+1;
				}
				$url_name .= $extension_number;   
			}
			
			$wpdb->query( "INSERT INTO `{$wpdb->prefix}product_categories` ( `group_id`, `name`, `nice-name`, `description`, `image`, `fee`, `active`, `category_parent`, `order`) VALUES ( {$brand_group['id']}, '{$brand['name']}', '{$url_name}', '{$brand['description']}', '', '0', '1', 0, 0)");
		}  
	}
}


if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `{$wpdb->prefix}wpsc_productmeta` LIKE 'custom';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}wpsc_productmeta` ADD `custom` VARCHAR( 1 ) NOT NULL DEFAULT '0' AFTER `meta_value`;");
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}wpsc_productmeta` ADD INDEX ( `custom` ) ;");
}

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `{$wpdb->prefix}download_status` LIKE 'ip_number';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}download_status` ADD `ip_number` varchar(255) NOT NULL AFTER `downloads`;");
}

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `{$wpdb->prefix}product_list` LIKE 'weight';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}product_list` ADD `weight` INT( 11 ) NOT NULL DEFAULT 0 AFTER `price`;");
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}product_list` ADD `weight_unit` VARCHAR( 10 ) NOT NULL AFTER `weight`;");
}
?>