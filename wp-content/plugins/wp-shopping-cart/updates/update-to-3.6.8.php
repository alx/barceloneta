<?php
/*
 * Updates to 3.6.8
*/

// here isthe code to create the database column for weight
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `{$wpdb->prefix}product_list` LIKE 'weight';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}product_list` ADD `weight` INT( 11 ) NOT NULL DEFAULT 0 AFTER `price`;");
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}product_list` ADD `weight_unit` VARCHAR( 10 ) NOT NULL AFTER `weight`;");
}

// here isthe code to create the database column for user uploaded files
if(!$wpdb->get_results("SHOW FULL COLUMNS FROM `{$wpdb->prefix}cart_contents` LIKE 'files';",ARRAY_A)) {
	$wpdb->query("ALTER TABLE `{$wpdb->prefix}cart_contents` ADD `files` TEXT NOT NULL AFTER `no_shipping`");
}



// here isthe code to update the payment gateway options.
$selected_gateways = array();
$current_gateway = get_option('payment_gateway');
$selected_gateways = get_option('custom_gateway_options');
if($current_gateway == '') {
  // set the gateway to Manual Payment if it is not set.
  $current_gateway = 'testmode';
}
if(get_option('payment_method') != null) {
	switch(get_option('payment_method')) {
		case 2:
		// mode 2 is credit card and manual payment / test mode
		if($current_gateway == 'testmode') {
			$current_gateway = 'paypal_multiple';
		}
		$selected_gateways[] = 'testmode';
		$selected_gateways[] = $current_gateway;
		break;
		
		case 3;
		// mode 3 is manual payment / test mode
		$current_gateway = 'testmode';
		case 1:
		// mode 1 is whatever gateway is currently selected.
		default:
		$selected_gateways[] = $current_gateway;
		break;
	}
	update_option('custom_gateway_options', $selected_gateways);
	update_option('payment_method', null);
}


// switch this variable over to our own option name, seems default_category was used by wordpress
if(get_option('wpsc_default_category') == null) {
  update_option('wpsc_default_category', get_option('default_category'));
}


?>