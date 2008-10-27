<?php
function widget_donations($args) {
  global $wpdb, $table_prefix;
  extract($args);
  $options = get_option('wpsc-nzshpcrt_donations');    
	$title = empty($options['title']) ? __(TXT_WPSC_DONATIONS) : $options['title'];
	$donation_count = $wpdb->get_var("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."product_list` WHERE `donation` IN ('1') AND `active` IN ('1')");   
	if($donation_count > 0) {
    echo $before_widget; 
    $full_title = $before_title . $title . $after_title;
    echo $full_title;
			nzshpcrt_donations();
    echo $after_widget;
    }
	}

function nzshpcrt_donations($input = null) {
	global $wpdb;
	$siteurl = get_option('siteurl');
	$sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `donation` IN ('1') AND `active` IN ('1')";
	$products = $wpdb->get_results($sql,ARRAY_A);
	if($products != null) {
		$output = "<div><div>";
		foreach($products as $product) {
			$output .= "<strong>".$product['name']."</strong><br />";
			if($product['image'] != null) {
				$output .= "<img src='".WPSC_THUMBNAIL_URL.$product['image']."' title='".$product['name']."' alt='".$product['name']."' /><br />";
			}
			$output .= $product['description']."<br />";
		
			$output .= "<form id='specials' name='$num' method='post' action='#' onsubmit='submitform(this);return false;' >";
			$variations_processor = new nzshpcrt_variations;
			$output .= $variations_processor->display_product_variations($product['id']);
			$output .= "<input type='hidden' name='prodid' value='".$product['id']."'/>";
			$output .= "<input type='hidden' name='item' value='".$product['id']."' />";
					
			$currency_sign_location = get_option('currency_sign_location');
			$currency_type = get_option('currency_type');
			$currency_symbol = $wpdb->get_var("SELECT `symbol_html` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".$currency_type."' LIMIT 1") ;
			$output .= "<label for='donation_price_".$product['id']."'>".TXT_WPSC_DONATION.":</label> $currency_symbol<input type='text' id='donation_price_".$product['id']."' name='donation_price' value='".number_format($product['price'],2)."' size='6' /><br />"; 
			$output .= "<input type='submit' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
			$output .= "</form>";
		}
		$output .= "</div></div>";
	} else {
		$output = '';
	}
	echo $input.$output;
}

function widget_donations_control() { 
  $option_name = 'wpsc-nzshpcrt_donations';  // because I want to only change this to reuse the code.
	$options = $newoptions = get_option($option_name);
	if ( isset($_POST[$option_name]) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST[$option_name]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option($option_name, $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
	
	echo "<p>\n\r";
	echo "  <label for='{$option_name}'>"._e('Title:')."<input class='widefat' id='{$option_name}' name='{$option_name}' type='text' value='{$title}' /></label>\n\r";
	echo "</p>\n\r";
}

function widget_donations_init() {
  if(function_exists('register_sidebar_widget')) {
    register_sidebar_widget(TXT_WPSC_DONATIONS, 'widget_donations');
    register_widget_control(TXT_WPSC_DONATIONS, 'widget_donations_control');
	}
  return;
}

add_action('plugins_loaded', 'widget_donations_init');
?>