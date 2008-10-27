<?php

function widget_latest_products($args)
{
	global $wpdb, $table_prefix;
	extract($args);
	$title = empty($options['title']) ? __(TXT_WPSC_LATEST_PRODUCTS) : $options['title'];
	echo $before_widget."<br>";
	$full_title = $before_title . $title . $after_title;
	echo $full_title."<br>";
	
	nzshpcrt_latest_product();
	echo $after_widget;
}

function widget_latest_products_control() { return null; }

 function widget_latest_products_init()
 {
	 if(function_exists('register_sidebar_widget'))
	 {
		 register_sidebar_widget('Latest Products', 'widget_latest_products');
#register_widget_control('Product', 'widget_product_tag', 300, 90);
	 }
	 return;
 }
 
function nzshpcrt_latest_product($input = null) {
	global $wpdb;
	$siteurl = get_option('siteurl');
	$latest_product = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `active` IN ('1') ORDER BY `id` DESC LIMIT 5", ARRAY_A);
	if($latest_product != null) {
		$output = "<div><div>";
		foreach($latest_product as $special){
			$output.="<a href='".wpsc_product_url($special['id'],$special['category'])."'><div>";
			//$output .= "<a href='".wpsc_product_url($special['id'])."'>";
			
			//$output .= "<a href='".wpsc_product_url($special['id'])."'>";
			if($special['image'] != null) {
				$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/product_images/thumbnails/".$special['image']."' title='".$special['name']."' alt='".$special['name']."' /><br />";
			} else {
				$output .= "<img src='$siteurl/wp-content/plugins/wp-shopping-cart/no-image-uploaded.gif' title='".$special['name']."' alt='".$special['name']."' /><br />";
			}
			//$output .= "</a>";
			$output .= "<strong>".$special['name']."</strong></a><br><br /> ";
			//$output .= $special['description']."<br /></div><br>";
//       $output .= $special['price'];
/*
       $output .= "<span id='special_product_price_".$special['id']."'><span class='pricedisplay'>";       
       $output .= nzshpcrt_currency_display(($special['price'] - $special['special_price']), $special['notax'],false,$product['id']);
       $output .= "</span></span><br /><br>";*/
       
//        $output .= "<form id='specials_".$special['id']."' name='$num' method='post' action='#' onsubmit='submitform(this);return false;' >";
//        $output .= "<input type='hidden' name='prodid' value='".$special['id']."'/>";
//        $output .= "<input type='hidden' name='item' value='".$special['id']."' />";
//               
//        if(($special['quantity_limited'] == 1) && ($special['quantity'] < 1))
//        {
// 	       $output .= TXT_WPSC_PRODUCTSOLDOUT."";
//        }
//        else
//        {
//            //$output .= $variations_processor->display_product_variations($special['id'],true);
// 	   $output .= "<input type='submit' name='".TXT_WPSC_ADDTOCART."' value='".TXT_WPSC_ADDTOCART."'  />";
//        }
//        $output .= "</form>";
		}
		$output .= "</div></div>";
	} else {
		$output = '';
	}
	echo $input.$output;
 }
 add_action('plugins_loaded', 'widget_latest_products_init');
 ?>