<?php
function nszhpcrt_homepage_products($content = '') {
  global $wpdb;
  $siteurl = get_option('siteurl');
  if(get_option('permalink_structure') != '') {
    $seperator ="?";
	} else {
		$seperator ="&amp;";
	}
  $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `display_frontpage` IN('1') AND `active` IN('1')";
  $product_list = $wpdb->get_results($sql,ARRAY_A);
    
  $output = "<div id='homepage_products'>\n\r";
  foreach((array)$product_list as $product) {
    $output .= "<div class='frontpage_product'>\n\r";
    $output .= "<a href='".wpsc_product_url($product['id'])."'>";
    if($product['image'] != '') {
      $output .= "<img src='".WPSC_THUMBNAIL_URL.$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
      $output .= "<p>\n\r";
      $output .= stripslashes($product['name']);
      $output .= "<span class='front_page_price'>\n\r";
      if($product['special']==1) {
        $output .= "<span class='oldprice'>".nzshpcrt_currency_display($product['price'], $product['notax'])."</span><br />\n\r";
        $output .= nzshpcrt_currency_display(($product['price'] - $product['special_price']), $product['notax'],false,$product['id']);
			} else {
				$output .= "".nzshpcrt_currency_display($product['price'], $product['notax']);
			}
      $output .= "</span>\n\r";
      $output .= "</p>\n\r";
		}
    $output .= "</a>";
    $output .= "</div>\n\r";
	}
  $output .= "</div>\n\r";
  $output .= "<br style='clear: left;'>\n\r";
  return preg_replace("/\[homepage_products\]/", $output, $content);
}
  
  

function nszhpcrt_category_tag($content = '') {
	require_once('themes/iShop/iShop.php');
	global $wpdb;
	if(preg_match_all("/\[wpsc_category_exclude=([\d]+),*(full)?\]/", $content, $matches)) {
		foreach($matches[1] as $key => $category_id) {
			$categories[$key]['id'] = $category_id;
			$categories[$key]['display'] = $matches[2][$key];
			$categories[$key]['original_string'] = $matches[0][$key];
		}
		foreach ($categories as $category) {
			$sql1 = "SELECT DISTINCT `".$wpdb->prefix."product_list`.*, `".$wpdb->prefix."item_category_associations`.`category_id`,`".$wpdb->prefix."product_order`.`order`, IF(ISNULL(`".$wpdb->prefix."product_order`.`order`), 0, 1) AS `order_state` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` LEFT JOIN `".$wpdb->prefix."product_order` ON ( ( `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."product_order`.`product_id` ) AND ( `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_order`.`category_id` ) ) WHERE `".$wpdb->prefix."product_list`.`active` = '1' AND `".$wpdb->prefix."item_category_associations`.`category_id` NOT IN ('".$category['id']."') $no_donations_sql ORDER BY `order_state` DESC,`".$wpdb->prefix."product_order`.`order` ASC";
			$product_list1 = $wpdb->get_results($sql1,ARRAY_A);
			if(function_exists('product_display_list') && (get_option('product_view') == 'list')) {
				$output1= product_display_list($product_list1, $group_type, $group_sql, $search_sql);
			} else if(function_exists('product_display_grid') && (get_option('product_view') == 'grid')) {
				$output1= product_display_grid($product_list1, $group_type, $group_sql, $search_sql);
			} else {
				$output1= product_display_default($product_list1,'');
			}
		}
		$content = str_replace($category['original_string'], $output1, $content);
	}
	if(preg_match_all("/\[wpsc_category=([\d]+),*(full)?\]/", $content, $matches)) {
		foreach($matches[1] as $key => $category_id) {
			$categories[$key]['id'] = $category_id;
			$categories[$key]['display'] = $matches[2][$key];
			$categories[$key]['original_string'] = $matches[0][$key];
		}

	//echo("<pre>".print_r($categories,true)."</pre>");
	$siteurl = get_option('siteurl');
	if(get_option('permalink_structure') != '') {
		$seperator ="?";
	} else {
		$seperator ="&amp;";
	}

		foreach((array)$activated_widgets as $widget_container) {
			if(is_array($widget_container) && array_search(TXT_WPSC_DONATIONS, $widget_container)) {
				$no_donations_sql = "AND `".$wpdb->prefix."product_list`.`donation` != '1'";
				break;
			}
		}
		foreach((array)$categories as $category) {
		  $full_view = null;
		  if($category['display'] == 'full') {
				$sql = "SELECT DISTINCT `".$wpdb->prefix."product_list`.*, `".$wpdb->prefix."item_category_associations`.`category_id`,`".$wpdb->prefix."product_order`.`order`, IF(ISNULL(`".$wpdb->prefix."product_order`.`order`), 0, 1) AS `order_state` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` LEFT JOIN `".$wpdb->prefix."product_order` ON ( ( `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."product_order`.`product_id` ) AND ( `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_order`.`category_id` ) ) WHERE `".$wpdb->prefix."product_list`.`active` = '1' AND `".$wpdb->prefix."item_category_associations`.`category_id` IN ('".$category['id']."') $no_donations_sql ORDER BY `order_state` DESC,`".$wpdb->prefix."product_order`.`order` ASC";
			
				$product_list = $wpdb->get_results($sql,ARRAY_A);
				// sorry about the global variable, but it was the best way I could think of to avoid people having to upgrade the gold cart	
				$GLOBALS['wpsc_category_id'] = $category['id'];
				if(function_exists('product_display_list') && (get_option('product_view') == 'list')) {
					$output .= product_display_list($product_list, $group_type, $group_sql, $search_sql);
				} else if(function_exists('product_display_grid') && (get_option('product_view') == 'grid')) {
					$output .= product_display_grid($product_list, $group_type, $group_sql, $search_sql);
				} else {
					$output .= product_display_default($product_list, $group_type, $group_sql, $search_sql);
				}
		  
		  } else {
				$sql = "SELECT DISTINCT `".$wpdb->prefix."product_list`.*, `".$wpdb->prefix."item_category_associations`.`category_id`,`".$wpdb->prefix."product_order`.`order`, IF(ISNULL(`".$wpdb->prefix."product_order`.`order`), 0, 1) AS `order_state` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` LEFT JOIN `".$wpdb->prefix."product_order` ON ( ( `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."product_order`.`product_id` ) AND ( `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_order`.`category_id` ) ) WHERE `".$wpdb->prefix."product_list`.`active` = '1' AND `".$wpdb->prefix."item_category_associations`.`category_id` IN ('".$category['id']."') $no_donations_sql ORDER BY `order_state` DESC,`".$wpdb->prefix."product_order`.`order` ASC";
			
				$product_list = $wpdb->get_results($sql,ARRAY_A);
			  $output = "<div id='products_page_container' class='wrap wpsc_container'>\n\r";
				$output .= "<div id='homepage_products'>\n\r";
				if ($full_view != null){
					$output .= "<table class='productdisplay'>";
				}
				foreach((array)$product_list as $product) {
					$wpsc_theme = wpsc_theme_html($product);
					if ($full_view == null) {
						$output .= "<div class='category_view_product'>\n\r";
					} else {
							/* product image is here */
					$output .= "<tr>";
					$output .= "<td class='imagecol'>";
					}
					$output .="<a href='".WPSC_IMAGE_URL.$product['image']."' class='thickbox preview_link'  rel='".str_replace(" ", "_",$product['name'])."'>";
					if($product['image'] != '') {
						$output .= "<img class='product_image' src='".WPSC_THUMBNAIL_URL.$product['image']."' title='".$product['name']."' alt='".$product['name']."' />\n\r";
					}
					$output .= "</a>";
					if ($full_view != null) {
						$output .= "</td><td class='textcol'>";
					} else {
						$output .= "<div class='product_details'>";
					}
					if (get_option('hide_name_link')!=1) {
						if(($product['special']==1) && ($variations_output[1] === null)) {
							$output .= "<a href='".wpsc_product_url($product['id'])."' class='wpsc_product_title' >$special<strong class='special'>Special / Sale Price - </strong><strong>" . stripslashes($product['name']) . "</strong></a>";
						} else {
							$output .= "<a href='".wpsc_product_url($product['id'])."' class='wpsc_product_title' >$special<strong>" . stripslashes($product['name']) . "</strong></a>";
						}
					} else {
						if(($product['special']==1) && ($variations_output[1] === null)) {
							$output .= "<a class='wpsc_product_title' >$special<strong class='special'>Special / Sale Price - </strong><strong>" . stripslashes($product['name']) . "</strong></a>";
						} else {
							$output .= "<a class='wpsc_product_title' >$special<strong>" . stripslashes($product['name']) . "</strong></a>";
						}
					}
					if ($full_view !=null) {
						if($product['description'] != '') {
							$output .= "<p class='wpsc_description'>".nl2br(stripslashes($product['description'])) . "</p>";
						}
				
						if($product['additional_description'] != '') {
							$output .= "<a href='#' class='additional_description_link' onclick='return show_additional_description(\"additionaldescription".$product['id']."\",\"link_icon".$product['id']."\");'>";
							$output .= "<img id='link_icon".$product['id']."' class='additional_description_button'  src='".WPSC_URL."/images/icon_window_expand.gif' title='".$product['name']."' alt='".$product['name']."' />";
							$output .= TXT_WPSC_MOREDETAILS."</a>";
						
							$output .= "<span class='additional_description' id='additionaldescription".$product['id']."'><br />";
							$output .= nl2br(stripslashes($product['additional_description'])) . "";
							$output .= "</span><br />";
						}
					}
					/*
					adding to cart stuff
					*/
					$output .= "<form id='product_".$product['id']."' name='product_".$product['id']."' method='post' action='".get_option('product_list_url').$seperator."category=".$_GET['category']."' onsubmit='submitform(this);return false;' >";
					$output .= "<input type='hidden' name='prodid' value='".$product['id']."' />";
					$output .= "<input type='hidden' name='item' value='".$product['id']."' />";
					
					$variations_procesor = new nzshpcrt_variations;
							
					$variations_output = $variations_procesor->display_product_variations($product['id'],false, false, true);
					$output .= $variations_output[0];
					if($variations_output[1] !== null) {
						$product['price'] = $variations_output[1];
					}
						
					if(($product['special']==1) && ($variations_output[1] === null)) {
						$output .= "<span class='oldprice'>".nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
						$output .= nzshpcrt_currency_display(($product['price'] - $product['special_price']), $product['notax'],false,$product['id']) . "<br />";
					} else {
						$output .= "<span id='product_price_".$product['id']."'>" . nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
					}
					if(((get_option('hide_addtocart_button') !='1') || (get_option('payment_gateway') !='google'))) {
						if(isset($wpsc_theme) && is_array($wpsc_theme) && ($wpsc_theme['html'] !='')) {
							$output .= $wpsc_theme['html'];
						} else {
							$output .= "<input type='submit' id='product_".$product['id']."_submit_button' class='wpsc_buy_button' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
						}
					}
					$output .= "</form>";
					if (get_option('addtocart_or_buynow')=='1') {
						if (get_option('payment_gateway')=='google') {
							$output .= google_buynow($product['id']);
						}
					}
							
							
					if ($full_view != null) {
						if(get_option('product_ratings') == 1) {
							$output .= "<div class='product_footer'>";
							
							$output .= "<div class='product_average_vote'>";
							$output .= "<strong>".TXT_WPSC_AVGCUSTREVIEW.":</strong>";
							$output .= nzshpcrt_product_rating($product['id']);
							$output .= "</div>";
							
							$output .= "<div class='product_user_vote'>";
							$vote_output = nzshpcrt_product_vote($product['id'],"onmouseover='hide_save_indicator(\"saved_".$product['id']."_text\");'");
							if($vote_output[1] == 'voted') {
								$output .= "<strong><span id='rating_".$product['id']."_text'>".TXT_WPSC_YOURRATING.":</span>";
								$output .= "<span class='rating_saved' id='saved_".$product['id']."_text'> ".TXT_WPSC_RATING_SAVED."</span>";
								$output .= "</strong>";
							} else if($vote_output[1] == 'voting') {
								$output .= "<strong><span id='rating_".$product['id']."_text'>".TXT_WPSC_RATETHISITEM.":</span>";
								$output .= "<span class='rating_saved' id='saved_".$product['id']."_text'> ".TXT_WPSC_RATING_SAVED."</span>";
								$output .= "</strong>";
							}
							$output .= $vote_output[0];
							$output .= "</div>";
							$output .= "</div>";
						}
					
						$output .="</td>";
						$output .="</tr>";
					} else {
						$output .= "</div>\n\r";
						$output .= "</div>";
					}
				}
				if ($full_view != null) {
					$output .= "</table>";
				}
				$output .= "</div>\n\r";
				$output .= "<br style='clear: left;'>\n\r";
			  $output .= "</div>\n\r";
			}
		$content = str_replace($category['original_string'], $output, $content);
		}
	}
	return $content;
}
?>