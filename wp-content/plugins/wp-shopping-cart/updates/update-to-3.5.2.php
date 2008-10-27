<?php
if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."wpsc_productmeta'") != ($wpdb->prefix."wpsc_productmeta")) {
  $wpsc_productmeta = "CREATE TABLE `".$wpdb->prefix."wpsc_productmeta` (
    `id` bigint(20) unsigned NOT NULL auto_increment,
    `product_id` bigint(20) unsigned NOT NULL default '0',
    `meta_key` varchar(255) default NULL,
    `meta_value` longtext,
    PRIMARY KEY  (`id`),
    KEY `product_id` (`product_id`),
    KEY `meta_key` (`meta_key`)
  ) TYPE=MyISAM;";
  $wpdb->query($wpsc_productmeta);
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
      if($current_url_name != $url_name) {
        $url_name .= $extension_number;
        update_product_meta($datarow['id'], 'url_name', $url_name);
			}
		} else {
      $url_name .= $extension_number;
      add_product_meta($datarow['id'], 'url_name', $url_name, true);
		}
	}
}
  
/* creates table to store data on what was bought with what however many times */
if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."also_bought_product'") != ($wpdb->prefix."also_bought_product")) {
   $wpsc_also_bought_product = "CREATE TABLE `".$wpdb->prefix."also_bought_product` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `selected_product` bigint(20) unsigned NOT NULL default '0',
  `associated_product` bigint(20) unsigned NOT NULL default '0',
  `quantity` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;";
  $wpdb->query($wpsc_also_bought_product);
  }
if(!$wpdb->get_results("SELECT `id` FROM `".$wpdb->prefix."also_bought_product`")) {
  /* inserts data on what was bought with what however many times */
  $product_ids = $wpdb->get_col("SELECT `id` FROM `".$wpdb->prefix."product_list` WHERE `active` IN('1')");
  foreach((array)$product_ids as $prodid) {
    $cart_ids = $wpdb->get_results("SELECT `purchaseid` FROM `".$wpdb->prefix."cart_contents` WHERE `prodid` IN ('$prodid')", ARRAY_A);
    $popular_array = array();
    foreach((array)$cart_ids as $cart_id) {
      $purchase_data = $wpdb->get_results("SELECT `prodid` FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid` IN ('".$cart_id['purchaseid']."') AND `prodid` NOT IN('$prodid')", ARRAY_A);
      foreach((array)$purchase_data as $purchase_row) {
        if(isset($popular_array[$purchase_row['prodid']])) {
          $popular_array[$purchase_row['prodid']]++;
          } else {
          $popular_array[$purchase_row['prodid']] = 1;
          }
        }      
      }
    foreach((array)$popular_array as $assoc_prodid => $quantity) {
      $wpdb->query("INSERT INTO `".$wpdb->prefix."also_bought_product` ( `id` , `selected_product` , `associated_product` , `quantity` ) VALUES ('', '$prodid', '".$assoc_prodid."', '".$quantity."' );");
      }
    }
  }

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."product_categories` LIKE 'nice-name';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."product_categories` ADD `nice-name` VARCHAR( 255 ) NOT NULL AFTER `name` ;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."product_categories`ADD INDEX ( `nice-name` ) ;");
  }
  
/* adds nice names for permalinks for categories */
$check_category_names = $wpdb->get_results("SELECT DISTINCT `nice-name` FROM `".$wpdb->prefix."product_categories` WHERE `nice-name` NOT IN ('')  AND `active` IN ('1')");
if($check_category_names == null) {
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
  }
  
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'base_shipping';",ARRAY_A)) {
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `base_shipping` MEDIUMINT NOT NULL DEFAULT '0' AFTER `shipping_country`;"); 
  //this line adds the base shipping, uses your current values, this is "harmless" because they would be used anyway in the old way, one line of SQL, yeah, whoot
  $base_country = get_option('base_country');
  $local_shipping = (float)get_option('base_local_shipping');
  $international_shipping = (float)get_option('base_international_shipping');
  $wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `base_shipping` = IF((`shipping_country` IN('$base_country')), $local_shipping, $international_shipping) WHERE `base_shipping` IN('0')");
  }
add_option('wpsc_category_description', 'false', "", 'yes');

  
/* because we have MySQL 3.23 on the work server, I have to use this, if we had a newer version, all that would be here would be one line of SQL
 * If you want to update it, just change the select statement into an update statment
 */
add_option('wpsc_use_pnp_cols', '', "", 'yes');
if(get_option('wpsc_use_pnp_cols') != 'true') {
  $base_country = get_option('base_country');
  $results = $wpdb->get_results("SELECT `wp_cart_contents`.`id`, `wp_cart_contents`.`pnp`, IF((`wp_purchase_logs`.`shipping_country` IN('$base_country')), `wp_product_list`.`pnp`, `wp_product_list`.`international_pnp`) AS `new_pnp` FROM `wp_cart_contents`, `wp_purchase_logs`, `wp_product_list` WHERE `wp_cart_contents`.`purchaseid` IN(`wp_purchase_logs`.`id`) AND `wp_cart_contents`.`prodid` IN(`wp_product_list`.`id`) ",ARRAY_A);
  foreach((array)$results as $row) {
    if((float)$row['pnp'] != (float)$row['new_pnp']) {
      $wpdb->query("UPDATE `wp_cart_contents` SET `pnp` = '".((float)$row['new_pnp'])."' WHERE `id` = '".$row['id']."' AND `pnp` IN('0');");
      //echo "UPDATE `wp_cart_contents` SET `pnp` = '".((float)$row['new_pnp'])."' WHERE `id` = '".$row['id']."' LIMIT 1 ;"."<br />";
      }      
    }  
    //exit();
  update_option('wpsc_use_pnp_cols','true');
  }
  
$wpsc_pageurl_option['product_list_url'] = '[productspage]';
$wpsc_pageurl_option['shopping_cart_url'] = '[shoppingcart]';
$check_chekout = $wpdb->get_var("SELECT `guid` FROM `".$wpdb->prefix."posts` WHERE `post_content` LIKE '%[checkout]%' LIMIT 1");
if($check_chekout != null) {
  $wpsc_pageurl_option['checkout_url'] = '[checkout]';
  } else {
  $wpsc_pageurl_option['checkout_url'] = '[checkout]';
  }
$wpsc_pageurl_option['transact_url'] = '[transactionresults]';
$wpsc_pageurl_option['user_account_url'] = '[userlog]';
$changes_made = false;
foreach($wpsc_pageurl_option as $option_key => $page_string)
  {
  $post_id = $wpdb->get_var("SELECT `ID` FROM `".$wpdb->prefix."posts` WHERE `post_content` LIKE '%$page_string%' LIMIT 1");
  update_option($option_key, get_permalink($post_id));
  $changes_made = true;
  }
  

if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."wpsc_coupon_codes'") != ($wpdb->prefix."wpsc_coupon_codes")) {
  $wpsc_productmeta = "CREATE TABLE `".$wpdb->prefix."wpsc_coupon_codes` (
    `id` bigint(20) unsigned NOT NULL auto_increment,
    `coupon_code` varchar(255) default NULL,
    `value` bigint(20) unsigned NOT NULL default '0',
    `is-percentage` char(1) NOT NULL default 0,
    `use-once` char(1) NOT NULL default 0,
    `is-used` char(1) NOT NULL default 0,
    `active` char(1) NOT NULL default 1,
    `start` DATETIME NOT NULL,
    `expiry` DATETIME NOT NULL,
    PRIMARY KEY  (`id`),
    KEY `coupon_code` (`coupon_code`),
    KEY `active` (`active`),
    KEY `start` (`start`),
    KEY `expiry` (`expiry`)
  ) TYPE=MyISAM;";
  $wpdb->query($wpsc_productmeta);
  }

if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'discount_value';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `discount_value` VARCHAR( 32 ) DEFAULT '0' NOT NULL AFTER `email_sent`;");
}
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `".$wpdb->prefix."purchase_logs` LIKE 'discount_data';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."purchase_logs` ADD `discount_data` TEXT NOT NULL AFTER `discount_value` ;");
}
//
//
/**/
?>