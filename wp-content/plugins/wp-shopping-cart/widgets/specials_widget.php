<?php
function widget_specials($args) {
  global $wpdb, $table_prefix;
  extract($args);
  $options = get_option('wpsc-widget_specials');
  $special_count = $wpdb->get_var("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."product_list` WHERE `special` = '1'  AND `active` IN ('1')");   
  if($special_count > 0) {
    $title = empty($options['title']) ? __(TXT_WPSC_PRODUCT_SPECIALS) : $options['title'];
    echo $before_widget; 
    $full_title = $before_title . $title . $after_title;
    echo $full_title;
    nzshpcrt_specials();
    echo $after_widget;
	}
}



 function nzshpcrt_specials($input = null) {
   global $wpdb;
   $siteurl = get_option('siteurl');
   $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `special` = '1'  AND `active` IN ('1')  ORDER BY RAND() LIMIT 1";
   $product = $wpdb->get_results($sql,ARRAY_A) ;
		if($product != null) {
			$output = "<div><div>";
			foreach($product as $special) {
				$output .= "<strong>".$special['name']."</strong><br /> ";
				if($special['image'] != null) {
					$output .= "<img src='".WPSC_THUMBNAIL_URL.$special['image']."' title='".$special['name']."' alt='".$special['name']."' /><br />";
				}
				$output .= $special['description']."<br />";
		
				$variations_processor = new nzshpcrt_variations;
				$variations_output = $variations_processor->display_product_variations($special['id'],true, false, true);
				$output .= $variations_output[0];
				if($variations_output[1] !== null) {
					$special['price'] = $variations_output[1];
				}
				if($variations_output[1] == null) {
					$output .= "<span class='oldprice'>".nzshpcrt_currency_display($special['price'], $special['notax'],false)."</span><br />";
				}
				
				$output .= "<span id='special_product_price_".$special['id']."'><span class='pricedisplay'>";       
				$output .= nzshpcrt_currency_display(($special['price'] - $special['special_price']), $special['notax'],false,$product['id']);
				$output .= "</span></span><br />";
				
				$output .= "<form id='specials_".$special['id']."' name='$num' method='post' action='#' onsubmit='submitform(this);return false;' >";
				$output .= "<input type='hidden' name='prodid' value='".$special['id']."'/>";
				$output .= "<input type='hidden' name='item' value='".$special['id']."' />";
							
				if(($special['quantity_limited'] == 1) && ($special['quantity'] < 1)) {
					$output .= TXT_WPSC_PRODUCTSOLDOUT."";
				} else {
					//$output .= $variations_processor->display_product_variations($special['id'],true);
					$output .= "<input type='submit' name='".TXT_WPSC_ADDTOCART."' value='".TXT_WPSC_ADDTOCART."'  />";
				}
				$output .= "</form>";
			}
			$output .= "</div></div>";
		} else {
			$output = '';
		}
		echo $input.$output;
	}

function widget_specials_control() {
  $option_name = 'wpsc-widget_specials';  // because I want to only change this to reuse the code.
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

function widget_specials_init() {
  if(function_exists('register_sidebar_widget')) {
    register_sidebar_widget(TXT_WPSC_PRODUCT_SPECIALS, 'widget_specials');
    register_widget_control(TXT_WPSC_PRODUCT_SPECIALS, 'widget_specials_control');
	}
  return;
}
add_action('plugins_loaded', 'widget_specials_init');
?>