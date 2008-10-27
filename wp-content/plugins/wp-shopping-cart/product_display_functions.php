<?php
function wpsc_get_product_listing($product_list, $group_type, $group_sql = '', $search_sql = '') {
  global $wpdb, $wp_query;
  
  if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('wpsc_get_product_listing','start');}
  $siteurl = get_option('siteurl');
  $activated_widgets = get_option('sidebars_widgets');

//     echo("<pre>".print_r($wp_query->query_vars,true)."</pre>");
  if(get_option('permalink_structure') != '') {
    $seperator ="?";
	} else {
		$seperator ="&amp;";
	}
	if (isset($_GET['action']) && ($_GET['action']=='bfg') && isset($_GET['session']) && ($_GET['session']==$_SESSION['google_session'])) {
		$_SESSION['nzshpcrt_cart'] = '';
		$_SESSION['nzshpcrt_cart'] = Array();
		unset($_SESSION['coupon_num'], $_SESSION['google_session']);
	}

	
  if((isset($_GET['items_per_page'])) && ($_GET['items_per_page']!=0)){
  	update_option('use_pagination',1);
  }
	if((get_option('use_pagination') == 1)) {
		$products_per_page = get_option('wpsc_products_per_page');
		if (isset($_REQUEST['items_per_page'])){
			$products_per_page = $_REQUEST['items_per_page'];
		}
		if(($_GET['page_number'] > 0)) {
			$startnum = ($_GET['page_number']-1)*$products_per_page;
		} else {
			$startnum = 0;
		}
	} else {
		$startnum = 0;
	}
  
  if(is_numeric($wp_query->query_vars['product_category'])) {
    $category_id = $wp_query->query_vars['product_category'];
	} else if(is_numeric($_GET['category'])) {
    $category_id = $_GET['category'];
	} else if(is_numeric($GLOBALS['wpsc_category_id'])) {
	  $category_id = $GLOBALS['wpsc_category_id'];
	}
    
    //echo("<pre>".print_r($category_id,true)."</pre>");
  	if (is_numeric($_GET['range'])) {
	    $ranges = $_SESSION['price_range'];
// 	    exit("<pre>".print_r($ranges,1)."</pre>");
		switch($_GET['range']) {
			case 1:
				$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` < ".$ranges[1]." AND `active` IN ('1')";
				break;
			
			case 2: {
				if (array_key_exists(2,$ranges)) {
					$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[1]."' AND `price` < '".$ranges[2]."' AND `active` IN ('1')";
				} else {
					$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[1]."' AND `active` IN ('1')";
				}
			  break;
			} 
				
			
				case 3: {
					if (array_key_exists(3,$ranges)) {
						$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[2]."' AND `price` < '".$ranges[3]."' AND `active` IN ('1')";
					} else {
						$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[2]."' AND `active` IN ('1')";
					}
					break;
				}
			
			case 4: {
				if (array_key_exists(4,$ranges)) {
					$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[3]."' AND `price` < '".$ranges[4]."' AND `active` IN ('1')";
				} else {
					$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[3]."' AND `active` IN ('1')";
				}
				break;
			}
			
			case 5: {
				if (array_key_exists(5,$ranges)) {
					$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[4]."' AND `price` < '".$ranges[5]."' AND `active` IN ('1')";
				} else {
					$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[4]."' AND `active` IN ('1')";
				}
				break;
			}
			
			case 6: 
				$range_sql="SELECT * FROM ".$wpdb->prefix."product_list WHERE `price` >= '".$ranges[5]."' AND `active` IN ('1')";
			break;
		}
		//exit($range_sql);
		$product_list = $wpdb->get_results($range_sql,ARRAY_A);
		return array("product_list" => $product_list,"page_listing"=>'');
	}

   
  foreach((array)$activated_widgets as $widget_container) {
    if(is_array($widget_container) && array_search(TXT_WPSC_DONATIONS, $widget_container)) {
      $no_donations_sql = "AND `".$wpdb->prefix."product_list`.`donation` != '1'";
      break;
		}
	}  
  
  if(function_exists('gold_shpcrt_search_sql') && ($_GET['product_search'] != '')) {
    $search_sql = gold_shpcrt_search_sql();
    if($search_sql != '') {
      // this cannot currently list products that are associated with no categories
      $rowcount = $wpdb->get_var("SELECT DISTINCT COUNT(`".$wpdb->prefix."product_list`.`id`) AS `count` FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $no_donations_sql $search_sql");
      if (isset($_SESSION['item_per_page']))
	  	$products_per_page = $_SESSION['item_per_page'];
      //exit($products_per_page);
	  if(!is_numeric($products_per_page) || ($products_per_page < 1)) { $products_per_page = $rowcount; }
      if(($startnum >= $rowcount) && (($rowcount - $products_per_page) >= 0)) {
        $startnum = $rowcount - $products_per_page;
			}
      $sql = "SELECT DISTINCT `".$wpdb->prefix."product_list`.* FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $no_donations_sql $search_sql ORDER BY `".$wpdb->prefix."product_list`.`special` DESC LIMIT $startnum, $products_per_page";
		}
	} else if (($wp_query->query_vars['ptag'] != null) || ( $_GET['ptag']!=null)) {
    if($wp_query->query_vars['ptag'] != null) {
    	$tag = $wp_query->query_vars['ptag'];
   	} else {
   		$tag = $_GET['ptag'];
   	}
	
	
		$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."terms WHERE slug='$tag'");
		
		$term_id = $results[0]->term_id;
		
		$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."term_taxonomy WHERE term_id = '".$term_id."' AND taxonomy='product_tag'");
		
		$taxonomy_id = $results[0]->term_taxonomy_id;
		
		$results = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."term_relationships WHERE term_taxonomy_id = '".$taxonomy_id."'");
		
		foreach ($results as $result) {
			$product_ids[] = $result->object_id; 
		}
		$product_id = implode(",",$product_ids);
	
		$sql = "SELECT * FROM ".$wpdb->prefix."product_list WHERE id IN (".$product_id.")";
	} else {
		if(is_numeric($_GET['category']) || is_numeric($wp_query->query_vars['product_category']) || is_numeric(get_option('wpsc_default_category'))) {
			if($wp_query->query_vars['product_category'] != null) {
				$catid = $wp_query->query_vars['product_category'];
				} else if(is_numeric($_GET['category'])) {
					$catid = $_GET['category'];
				} else if(is_numeric($GLOBALS['wpsc_category_id'])) {
					$catid = $GLOBALS['wpsc_category_id'];
				} else {
					$catid = get_option('wpsc_default_category');
				}
			/*
				* The reason this is so complicated is because of the product ordering, it is done by category/product association
				* If you can see a way of simplifying it and speeding it up, then go for it.
				*/
				
				
			$rowcount = $wpdb->get_var("SELECT DISTINCT COUNT(`".$wpdb->prefix."product_list`.`id`) AS `count` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` WHERE `".$wpdb->prefix."product_list`.`active` = '1' AND `".$wpdb->prefix."item_category_associations`.`category_id` IN ('".$catid."') $no_donations_sql");
			
			if(!is_numeric($products_per_page) || ($products_per_page < 1)) { $products_per_page = $rowcount; }
			if(($startnum >= $rowcount) && (($rowcount - $products_per_page) >= 0)) {
				$startnum = $rowcount - $products_per_page;
			}
			if ($_REQUEST['order']==null) {
				$order = 'ASC';
			} elseif ($_REQUEST['order']=='DESC') {
				$order = 'DESC';
			}
			$sql = "SELECT DISTINCT `".$wpdb->prefix."product_list`.*, `".$wpdb->prefix."item_category_associations`.`category_id`,`".$wpdb->prefix."product_order`.`order`, IF(ISNULL(`".$wpdb->prefix."product_order`.`order`), 0, 1) AS `order_state` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` LEFT JOIN `".$wpdb->prefix."product_order` ON ( ( `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."product_order`.`product_id` ) AND ( `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_order`.`category_id` ) ) WHERE `".$wpdb->prefix."product_list`.`active` = '1' AND `".$wpdb->prefix."item_category_associations`.`category_id` IN ('".$catid."') $no_donations_sql ORDER BY `order_state` DESC,`".$wpdb->prefix."product_order`.`order` $order, `".$wpdb->prefix."product_list`.`id` DESC LIMIT $startnum, $products_per_page";
			//exit($sql);
		} else {
			$rowcount = $wpdb->get_var("SELECT DISTINCT COUNT(`".$wpdb->prefix."product_list`.`id`) AS `count` FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $no_donations_sql $group_sql");
			
			if(!is_numeric($products_per_page) || ($products_per_page < 1)) { $products_per_page = $rowcount; }
			if(($startnum >= $rowcount) && (($rowcount - $products_per_page) >= 0)) {
				$startnum = $rowcount - $products_per_page;
			}
			$sql = "SELECT DISTINCT `".$wpdb->prefix."product_list`.* FROM `".$wpdb->prefix."product_list`,`".$wpdb->prefix."item_category_associations` WHERE `".$wpdb->prefix."product_list`.`active`='1' AND `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` $no_donations_sql $group_sql ORDER BY `".$wpdb->prefix."product_list`.`special`, `".$wpdb->prefix."product_list`.`id`  DESC LIMIT $startnum, $products_per_page"; 
		}
	}
	

				
  // shows page numbers, probably fairly obviously
// exit($sql);
  $return_array['product_list'] = $wpdb->get_results($sql,ARRAY_A);
  $return_array['page_listing'] = "";
  
  if($rowcount > $products_per_page) 
    {
    if($products_per_page > 0) {
      $pages = ceil($rowcount/$products_per_page);
    } else {
      $pages = 1;
    }
    
    //$product_view_url = get_option('product_list_url').$seperator;
    $product_view_url = wpsc_category_url($category_id).$seperator;

    if(is_numeric($_GET['category'])) {
		} else if(is_numeric($_GET['brand'])) {
      $product_view_url .= "brand=".$_GET['brand']."&amp;";
		} else if($_GET['product_search'] != '') {
      $product_view_url .= "product_search=".$_GET['product_search']."&amp;"."view_type=".$_GET['view_type']."&amp;"."item_per_page=".$_GET['item_per_page']."&amp;";
		}
		
		if(isset($_GET['order']) && ($_GET['order'] == 'ASC') || ($_GET['order'] == 'DESC')  ) {
		  $product_view_url .= "order={$_GET['order']}&amp;";
		}
		
		if(isset($_GET['view_type']) && ($_GET['view_type'] == 'default') || ($_GET['view_type'] == 'grid')  ) {
		  $product_view_url .= "view_type={$_GET['view_type']}&amp;";
		}
    
    $return_array['page_listing'] .= "<div class='wpsc_page_numbers'>\n\r";
    $return_array['page_listing'] .= "Pages: ";
    for($i=1;$i<=$pages;++$i) {
      if(($_GET['page_number'] == $i) || (!is_numeric($_GET['page_number']) && ($i == 0))) {
        if($_GET['view_all'] != 'true') {
          $selected = "class='selected'";
				}
			} else {
        $selected = "class='notselected'";
			}
      $return_array['page_listing'] .= "  <a href='".$product_view_url."page_number=$i' $selected >$i</a>\n\r";
		}    
    $return_array['page_listing'] .= "</div>\n\r";
	}
  
  $return_array['category_id'] = $catid;
  if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('wpsc_get_product_listing','stop');}
  return $return_array;
  }

function product_display_default($product_list, $group_type, $group_sql = '', $search_sql = '') {
  
  global $wpdb, $wp_rewrite;
  if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('product_display_default','start');}
  $siteurl = get_option('siteurl');
  if($wp_rewrite->permalink_structure != '') {
    $seperator ="?";
	} else {
    $seperator ="&amp;";
	}
   
  $product_listing_data = wpsc_get_product_listing($product_list, $group_type, $group_sql, $search_sql);
  if ($product_list == '')
  $product_list = $product_listing_data['product_list'];
  
  if((get_option('wpsc_page_number_position') == 1) || (get_option('wpsc_page_number_position') == 3)) {
    $output .= $product_listing_data['page_listing'];
	}
  if($product_listing_data['category_id']) {
		$category_nice_name = $wpdb->get_var("SELECT `nice-name` FROM `".$wpdb->prefix."product_categories` WHERE `id` ='".(int)$product_listing_data['category_id']."' LIMIT 1");
  } else {
    $category_nice_name = '';
  }
  if($product_list != null) {
		// breadcrumbs start here
		if ((get_option("show_breadcrumbs") == '1') && is_numeric($product_listing_data['category_id'])) {
			$output .= "<div class='breadcrumb'>";
			$output .= "<a href='".get_option('siteurl')."'>".get_option('blogname')."</a> &raquo; ";
			
			$category = $product_listing_data['category_id'];
			
			$category_info =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}product_categories WHERE id='".$category."'",ARRAY_A);
			$category_name=  $wpdb->get_var("SELECT name FROM {$wpdb->prefix}product_categories WHERE id='".$category."'");
			while ($category_info[0]['category_parent']!=0) {
				$category_info =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}product_categories WHERE id='".$category_info[0]['category_parent']."'",ARRAY_A);
			
				$output .= "<a href='".wpsc_category_url($category_info[0]['id'])."'>".$category_info[0]['name']."</a> &raquo; ";
			}
			$output .= "".$category_name."";
// 			$output .= $product_list[0]['name'];
			$output .= "</div>";
		}
		// breadcrumbs end here
  
  
    foreach($product_list as $product) {      
			if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('product_start_loop','start', true);}
      $num++;
      if(function_exists('wpsc_theme_html')) {
        $wpsc_theme = wpsc_theme_html($product);
			}
			

			$output .= "<div class='productdisplay default_product_display product_view_{$product['id']} {$category_nice_name}'>";

      $output .= "      <div class='textcol'>";
      
       if($category_data[0]['fee'] == 0) {
				$output .= "      <div class='imagecol'>";
        if(get_option('show_thumbnails') == 1) {
          if($product['image'] !=null) {
            $image_size = @getimagesize(WPSC_IMAGE_DIR.$product['image']);
            $output .= "<a href='".WPSC_IMAGE_URL.$product['image']."' class='thickbox preview_link'  rel='".str_replace(" ", "_",$product['name'])."'>";

            if($product['thumbnail_image'] != null) {
              $image_file_name = $product['thumbnail_image'];
						} else {
              $image_file_name = $product['image'];
						}

            $output .= "<img src='".WPSC_THUMBNAIL_URL.$image_file_name."' title='".htmlentities($product['name'], ENT_QUOTES)."' alt='".htmlentities($product['name'], ENT_QUOTES)."' id='product_image_".$product['id']."' class='product_image'/>";
            $output .= "</a>";
            if(function_exists("gold_shpcrt_display_extra_images")) {
              $output .= gold_shpcrt_display_extra_images($product['id'],$product['name']);
						}
					} else {
						if(get_option('product_image_width') != '') {
							$output .= "<img src='".WPSC_URL."/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' width='".get_option('product_image_width')."' height='".get_option('product_image_height')."' id='product_image_".$product['id']."' class='product_image' />";
						} else {
							$output .= "<img src='".WPSC_URL."/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' id='product_image_".$product['id']."' class='product_image' />";
						}
					}
          
          if(function_exists('drag_and_drop_items')) {
            $output .= drag_and_drop_items("product_image_".$product['id']);
					}
				}
        $output .= "</div>";
			}
			
      if($product['special'] == 1) {
        $special = "<span class='special'>".TXT_WPSC_SPECIAL." - </span>";
			} else {
				$special = "";
			}

			$output .= "<form id='product_".$product['id']."' name='product_".$product['id']."' method='post' action='".get_option('product_list_url').$seperator."category=".$_GET['category']."' onsubmit='submitform(this);return false;' >";
			$output .= "<input type='hidden' name='prodid' value='".$product['id']."' />";
			$output .= "<div class='producttext'><h2 class='prodtitles'>";

			if (get_option('hide_name_link')!=1) {
				$output .= "<a href='".wpsc_product_url($product['id'])."'  class='wpsc_product_title' >$special" . stripslashes($product['name']) . "</a>";
			} else {
				$output .= "<a class='wpsc_product_title' >$special<strong>" . stripslashes($product['name']) . "</strong></a>";
			}
			$output .= "</h2>";


      ob_start();
      do_action('wpsc_product_addons', $product['id']);
      $output .= ob_get_contents();
      ob_end_clean();
      
      if(is_numeric($product['file']) && ($product['file'] > 0)) {
        $file_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."product_files` WHERE `id`='".$product['file']."' LIMIT 1",ARRAY_A);
        if(($file_data != null) && (function_exists('listen_button'))) {
          $output .= listen_button($file_data['idhash'], $file_data['id']);
				}
			}

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

			// print the custom fields here, if there are any
			$custom_fields =  $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpsc_productmeta` WHERE `product_id` IN('{$product['id']}') AND `custom` IN('1') ",ARRAY_A);
			if(count($custom_fields) > 0) {
			  $output .= "<div class='custom_meta'>";
			  foreach((array)$custom_fields as $custom_field) {
			    $output .= "<strong>{$custom_field['meta_key']}:</strong> {$custom_field['meta_value']} <br />";
			  }
			  $output .= "</div>";
			}
      

      ob_start();
      do_action('wpsc_product_addon_after_descr', $product['id']);
      $output .= ob_get_contents();
      ob_end_clean();

      $variations_procesor = new nzshpcrt_variations;

			if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('display_product_variations','start', true);}
      $variations_output = $variations_procesor->display_product_variations($product['id'],false, false, true);
			if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('display_product_variations','stop', true);}
			
      if($variations_output[0] != '') { //will always be set, may sometimes be an empty string
        $output .= "<p class='wpsc_variation_forms'>".$variations_output[0]."</p>";
			}
      if($variations_output[1] !== null) {
        $product['price'] = $variations_output[1];
			}
			
			$extras_processor = new extras();
			$extras_output = $extras_processor->display_product_extras($product['id'],false,false,true);
			
			$output.="<p class='wpsc_extras_forms'>".$extras_output."</p>";
			
			$output .= "<p class='wpsc_product_price'>";
      if($product['donation'] == 1) {
        $currency_sign_location = get_option('currency_sign_location');
        $currency_type = get_option('currency_type');
        $currency_symbol = $wpdb->get_var("SELECT `symbol_html` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".$currency_type."' LIMIT 1") ;
        $output .= "<label for='donation_price_".$product['id']."'>".TXT_WPSC_DONATION.":</label> $currency_symbol<input type='text' id='donation_price_".$product['id']."' name='donation_price' value='".number_format($product['price'],2)."' size='6' /><br />";
			} else {
        if(($product['special']==1) && ($variations_output[1] === null)) {
          $output .= "<span class='oldprice'>".TXT_WPSC_PRICE.": " . nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
          $output .= TXT_WPSC_PRICE.": " . nzshpcrt_currency_display(($product['price'] - $product['special_price']), $product['notax'],false,$product['id']) . "<br />";
				} else {
					$output .= TXT_WPSC_PRICE.": <span id='product_price_".$product['id']."'>" . nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
				}
        if(get_option('display_pnp') == 1) {
          $output .= TXT_WPSC_PNP.": " . nzshpcrt_currency_display($product['pnp'], 1) . "<br />";
				}
			}
      $output .= "</p>";
      
      $output .= "<input type='hidden' name='item' value='".$product['id']."' />";
      
			$updatelink_sql = "SELECT * FROM ".$wpdb->prefix."wpsc_productmeta WHERE product_id =". $product['id']." AND meta_key='external_link'";
			$updatelink_data = $wpdb->get_results($updatelink_sql, ARRAY_A);
			$updatelink = get_product_meta($product['id'], 'external_link', true);
			
			if(function_exists('wpsc_theme_html')) {
			  $wpsc_theme = wpsc_theme_html($product);
			}
      if(($product['quantity_limited'] == 1) && ($product['quantity'] < 1) && $variations_output[1] === null) {
        $output .= "<p class='soldout'>".TXT_WPSC_PRODUCTSOLDOUT."</p>";
			} else {
				if((get_option('hide_addtocart_button') != 1) && (get_option('payment_gateway') !='google')) {
					if ((get_option('addtocart_or_buynow') == 0)) {
						if(isset($wpsc_theme) && is_array($wpsc_theme) && ($wpsc_theme['html'] !='')) {
							$output .= $wpsc_theme['html'];
						} else {
							$output .= "<input type='submit' id='product_".$product['id']."_submit_button' class='wpsc_buy_button' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
						}
					}
				}
			}
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
      
      $output .= "</div>";
      
      $output .= "</form>";
			//exit("<pre>".print_r($updatelink_data,1)."</pre>");
			if ((count($updatelink_data)>0)&&($updatelink_data[0]['meta_value'] != '')) {
				$output .= external_link($product['id']);
			} else {
				if (get_option('addtocart_or_buynow')=='1')
					if (get_option('payment_gateway')=='google') {
						$output .= google_buynow($product['id']);
					} else if (get_option('payment_gateway') == 'paypal_multiple') {
					  $output .= "<form onsubmit='log_paypal_buynow(this)' target='paypal' action='".get_option('paypal_multiple_url')."' method='post'>
							<input type='hidden' name='business' value='".get_option('paypal_multiple_business')."'>
							<input type='hidden' name='cmd' value='_xclick'>
							<input type='hidden' name='item_name' value='".$product['name']."'>
							<input type='hidden' id='item_number' name='item_number' value='".$product['id']."'>
							<input type='hidden' id='amount' name='amount' value='".$product['price']."'>
							<input type='hidden' id='unit' name='unit' value='".$product['price']."'>
							<input type='hidden' id='shipping' name='ship11' value='".$shipping."'>
							<input type='hidden' name='handling' value='".get_option('base_local_shipping')."'>
							<input type='hidden' name='currency_code' value='".get_option('paypal_curcode')."'>
							<input type='hidden' name='undefined_quantity' value='0'>
							<input type='image' name='submit' border='0' src='https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif' alt='PayPal - The safer, easier way to pay online'>
							<img alt='' border='0' width='1' height='1' src='https://www.paypal.com/en_US/i/scr/pixel.gif' >
						</form>
					";						
					}
			}
			$output .= "      </div>\n\r";
			$output .= " <div class='clear'></div>\n\r";
			$output .= "</div>";
			if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('product_start_loop','stop', true);}
		}
	} else {
		if($_GET['product_search'] != null) {
			$output .= "<br /><strong class='cattitles'>".TXT_WPSC_YOUR_SEARCH_FOR." \"".$_GET['product_search']."\" ".TXT_WPSC_RETURNED_NO_RESULTS."</strong>";
		} else {
			$output .= "<p>".TXT_WPSC_NOITEMSINTHIS." ".$group_type.".</p>";
			if(get_option('wpsc_default_category') == $product_listing_data['category_id']) {
				$output .= wpsc_odd_category_setup();
			}
		}
	}
 
  if((get_option('wpsc_page_number_position') == 2) || (get_option('wpsc_page_number_position') == 3)) {
    $output .= $product_listing_data['page_listing'];
	}
  $output = str_replace('$', '&#036;', $output);
  if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('product_display_default','stop');}
  return $output;
}




function single_product_display($product_id) {
	global $wpdb;
  $siteurl = get_option('siteurl');
  if(get_option('permalink_structure') != '') { 
    $seperator ="?";
  } else {
		$seperator ="&amp;";
	}
	
	// what is our product?
  if(is_numeric($product_id)) {
    $product_list = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".(int)$product_id."' LIMIT 1",ARRAY_A);
	}
	
	// if we have a product
  if($product_list != null) {
    
    // show the breadcrumbs
  	if (get_option("show_breadcrumbs") == '1') {
			$output .= "<div class='breadcrumb'>\n\r";
			$output .= "  <a href='".get_option('siteurl')."'>".get_option('blogname')."</a> &raquo; ";
			$category = $wpdb->get_var("SELECT category_id FROM {$wpdb->prefix}item_category_associations WHERE product_id='".$product_id."' ORDER BY id ASC LIMIT 1");
			$category_info =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}product_categories WHERE id='".$category."'",ARRAY_A);
			$category_name=  $wpdb->get_var("SELECT name FROM {$wpdb->prefix}product_categories WHERE id='".$category."'");
			while ($category_info[0]['category_parent']!=0) {
				$category_info =  $wpdb->get_results("SELECT * FROM {$wpdb->prefix}product_categories WHERE id='".$category_info[0]['category_parent']."'",ARRAY_A);
			
				$output .= "<a href='".wpsc_category_url($category_info[0]['id'])."'>".$category_info[0]['name']."</a> &raquo; ";
			}
			$output .= "<a href='".wpsc_category_url($category)."'>".$category_name."</a> &raquo; ";
			$output .= $product_list[0]['name']."\n\r";
			$output .= "</div>\n\r";
		}
    
    $output .= "  <div class='productdisplay'>\n\r";
    
    foreach((array)$product_list as $product) {
      $num++;
      $output .= "    <div class='single_product_display product_view_{$product['id']} '>\n\r";
      $output .= "      <div class='textcol'>\n\r";
      
      
      // display the image
			$output .= "        <div class='imagecol'>\n\r";
			if(get_option('show_thumbnails') == 1) {
				if($product['image'] !=null) {
					if($product['thumbnail_image'] != null) {
						$image_file_name = $product['thumbnail_image'];
					} else {
						$image_file_name = $product['image'];
					}
					
					$output .= "<a href='".WPSC_IMAGE_URL.$product['image']."' class='thickbox preview_link'  rel='".str_replace(" ", "_",$product['name'])."'>\n\r";
					$src = WPSC_IMAGE_URL.$product['image'];
					if((get_option('single_view_image_width') >= 1) && (get_option('single_view_image_height') >= 1)) {
						$output .= "<img src='index.php?productid=".$product['id']."&amp;width=".get_option('single_view_image_width')."&amp;height=".get_option('single_view_image_height')."' title='".$product['name']."' alt='".$product['name']."' id='product_image_".$product['id']."' class='product_image'/>\n\r";
					} else {
						$output .= "<img src='".WPSC_THUMBNAIL_URL.$image_file_name."' title='".$product['name']."' alt='".$product['name']."' id='product_image_".$product['id']."' class='product_image'/>\n\r";
					}
					$output .= "</a>\n\r";
					if(function_exists("gold_shpcrt_display_extra_images")) {
						$output .= gold_shpcrt_display_extra_images($product['id'],$product['name']);
					}
				} else {
					if(get_option('product_image_width') != '') {
						$output .= "<img src='".WPSC_URL."/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' width='".get_option('product_image_width')."' height='".get_option('product_image_height')."' />\n\r";
					} else {
						$output .= "<img src='".WPSC_URL."/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' />\n\r";
					}
				}
			}        
			$output .= "        </div>\n\r";
  
  
      // if the product is special, say so
      if($product['special'] == 1) {
				$special = "        <span class='special'>".TXT_WPSC_SPECIAL." - </span>\n\r";
			} 
      
			$output .= "        <form id='product_".$product['id']."' name='$num' method='post' action='".get_option('product_list_url').$seperator."category=".$_GET['category']."' onsubmit='submitform(this);return false;' >\n\r";
      $output .= "<input type='hidden' name='prodid' value='".$product['id']."' />\n\r";
 
      $output .= "        <div class='producttext'>\n\r";
      $output .= "           <h2 class='prodtitles'>$special" . stripslashes($product['name'])."</h2>\n\r";
			if (get_option('wpsc_selected_theme') == 'market3') {
				$soldout=0;
				if (($product['quantity_limited']) && ($product['quantity']<1)) {
					$soldout=1;
				}
				if ($soldout) {
					$output .="           <span class='soldout'>Sold out</span>\n\r";
				} else {
					$output .="           <span class='price'>".nzshpcrt_currency_display($product['price'], $product['notax'])."</span>\n\r";
				}
			}
			
			
			
			ob_start();
      do_action('wpsc_product_addons', $product['id']);
      $output .= ob_get_contents();
      ob_end_clean();
      if(is_numeric($product['file']) && ($product['file'] > 0)) {
        $file_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."product_files` WHERE `id`='".$product['file']."' LIMIT 1",ARRAY_A);
        if(($file_data != null) && (function_exists('listen_button'))) {
          $output .= listen_button($file_data['idhash'], $file_data['id']);
				}
			}
            
      if($product['description'] != '') {
				$output .= "           <p  class='description'>".nl2br(stripslashes($product['description'])) . "</p>\n\r";
			}
			if (get_option('wpsc_selected_theme') == 'market3') {
	       $output .= "           <br />";
      }

        
      if($product['additional_description'] != '') {                
        $output .= "           <p class='single_additional_description' >\n\r";
        if (get_option('wpsc_selected_theme') == 'market3') {
					$output .= "           <span class='additional'>Additional Details: </span>\n\r";
				}

        $output .= nl2br(stripslashes($product['additional_description'])) . "";
        $output .= "           </p>\n\r";
			}
			
			
			// print the custom fields here, if there are any
			$custom_fields =  $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpsc_productmeta` WHERE `product_id` IN('{$product['id']}') AND `custom` IN('1') ",ARRAY_A);
			if(count($custom_fields) > 0) {
			  $output .= "           <div class='custom_meta'>\n\r";
			  foreach((array)$custom_fields as $custom_field) {
			    $output .= "             <strong>{$custom_field['meta_key']}:</strong> {$custom_field['meta_value']} <br />\n\r";
			  }
			  $output .= "           </div>\n\r";
			}
      

      
      ob_start();
      do_action('wpsc_product_addon_after_descr', $product['id']);
      $output .= ob_get_contents();
      ob_end_clean();
      
      if(function_exists('wpsc_akst_share_link') && (get_option('wpsc_share_this') == 1)) {
        $output .=  wpsc_akst_share_link('return');
			}
			
			
			$variations_procesor = new nzshpcrt_variations;
          
      $variations_output = $variations_procesor->display_product_variations($product['id'],false, false, true);
      if($variations_output[0] != '') { //will always be set, may sometimes be an empty string 
        $output .= "           <p class='wpsc_variation_forms'>".$variations_output[0]."</p>";
			}
      if($variations_output[1] !== null) {
        $product['price'] = $variations_output[1];
			}
			
			
			if (get_option('wpsc_selected_theme') != 'market3') {
				$output .= "           <p class='wpsc_product_price'>";
				if($product['donation'] == 1) {
					$currency_sign_location = get_option('currency_sign_location');
					$currency_type = get_option('currency_type');
					$currency_symbol = $wpdb->get_var("SELECT `symbol_html` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".$currency_type."' LIMIT 1") ;
					$output .= "           <label for='donation_price_".$product['id']."'>".TXT_WPSC_DONATION.":</label> $currency_symbol<input type='text' id='donation_price_".$product['id']."' name='donation_price' value='".number_format($product['price'],2)."' size='6' /><br />";
				} else {
					
					if (get_option('wpsc_selected_theme') != 'market3') {
						if(($product['special']==1) && ($variations_output[1] === null)) {
							$output .= "<span class='oldprice'>".TXT_WPSC_PRICE.": " . nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
							$output .= TXT_WPSC_PRICE.": " . nzshpcrt_currency_display(($product['price'] - $product['special_price']), $product['notax'],false,$product['id']) . "<br />";
						} else {
							$output .= TXT_WPSC_PRICE.": <span id='product_price_".$product['id']."'>" . nzshpcrt_currency_display($product['price'], $product['notax']) . "</span><br />";
						}
						if(get_option('display_pnp') == 1) {
							$output .= TXT_WPSC_PNP.": " . nzshpcrt_currency_display($product['pnp'], 1) . "<br />";
						}
					}
				}
				$output .= "</p>\n\r";	
      }
			
      
      
			if(function_exists('wpsc_theme_html')) {
			  $wpsc_theme = wpsc_theme_html($product);
			}
      $output .= "<input type='hidden' name='item' value='".$product['id']."' />";
      //AND (`quantity_limited` = '1' AND `quantity` > '0' OR `quantity_limited` = '0' )
      if(($product['quantity_limited'] == 1) && ($product['quantity'] < 1) && ($variations_output[1] === null)) {
        if (get_option("wpsc_selected_theme")!='market3') {
					$output .= "<p class='soldout'>".TXT_WPSC_PRODUCTSOLDOUT."</p>";
				}
			} else {
				if ((get_option('hide_addtocart_button') != 1) && (get_option('addtocart_or_buynow') == 0)) {
					if(isset($wpsc_theme) && is_array($wpsc_theme) && ($wpsc_theme['html'] !='')) {
						$output .= $wpsc_theme['html'];
					} else {
						$output .= "<input type='submit' id='product_".$product['id']."_submit_button' class='wpsc_buy_button' name='Buy' value='".TXT_WPSC_ADDTOCART."'  />";
					}
				}
			}

      if(function_exists('gold_shpcrt_display_gallery')) {
        $output .= gold_shpcrt_display_gallery($product['id']);
			}
      
      
      
      
      
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
      
      $output .= "          </div>\n\r";
			$output .= "        </form>\n\r";
			
			if ((count($updatelink_data)>0)&&($updatelink_data[0]['meta_value'] != '')) {
				$output .= external_link($product['id']);
			} else {
				if (get_option('addtocart_or_buynow')=='1')
					if (get_option('payment_gateway')=='google') {
						$output .= google_buynow($product['id']);
					} else if (get_option('payment_gateway') == 'paypal_multiple') {
					  $output .= "<form onsubmit='log_paypal_buynow(this)' target='paypal' action='".get_option('paypal_multiple_url')."' method='post'>
							<input type='hidden' name='business' value='".get_option('paypal_multiple_business')."'>
							<input type='hidden' name='cmd' value='_xclick'>
							<input type='hidden' name='item_name' value='".$product['name']."'>
							<input type='hidden' id='item_number' name='item_number' value='".$product['id']."'>
							<input type='hidden' id='amount' name='amount' value='".$product['price']."'>
							<input type='hidden' id='unit' name='unit' value='".$product['price']."'>
							<input type='hidden' id='shipping' name='ship11' value='".$shipping."'>
							<input type='hidden' name='handling' value='".get_option('base_local_shipping')."'>
							<input type='hidden' name='currency_code' value='".get_option('paypal_curcode')."'>
							<input type='hidden' name='undefined_quantity' value='0'>
							<input type='image' name='submit' border='0' src='https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif' alt='PayPal - The safer, easier way to pay online'>
							<img alt='' border='0' width='1' height='1' src='https://www.paypal.com/en_US/i/scr/pixel.gif' >
						</form>
					";						
					}
			}
			
			
			
			
			
			$output .= "        <form id='product_extra_".$product['id']."' name='product_".$product['id']."' method='post' action='".get_option('product_list_url').$seperator."category=".$_GET['category']."' onsubmit='submitform(this);return false;' >\n\r";
      $output .= "          <input type='hidden' name='prodid' value='".$product['id']."' />\n\r";
      $output .= "          <input type='hidden' name='item' value='".$product['id']."' />\n\r";
      $output .= "        </form>\n\r";
		
		
      
      $output .= "      </div>\n\r";
      $output .= "    </div>\n\r";
			$output .= " <div class='clear'></div>\n\r";
    }
    
		$output .= wpsc_also_bought($product_id);
    $output .= "  </div>";
    
    
	} else { // otherwise, we have no product
		$output .= "<p>".TXT_WPSC_NOITEMSINTHIS." ".$group_type.".</p>";
	}
	// replace dollar signs with the HTML code so that PHP doesn't try to interpret them as variables.
	$output = str_replace('$', '&#036;', $output);
	
  return $output; 
}



function wpsc_post_title_seo($title) {
	global $wpdb, $page_id, $wp_query;
	if($wp_query->query_vars['product_name'] != '') {
		$product_id = $wpdb->get_var("SELECT `product_id` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN ( 'url_name' ) AND `meta_value` IN ( '".$wpdb->escape($wp_query->query_vars['product_name'])."' ) LIMIT 1");			
    $title = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_list` WHERE `id` IN('".(int)$product_id."') LIMIT 1");
	} else if(is_numeric($_GET['product_id'])) {
		$title=$wpdb->get_var("SELECT `name` FROM ".$wpdb->prefix."product_list WHERE id IN ('".(int)$_GET['product_id']."') LIMIT 1" );
	}
	return stripslashes($title);
}



function wpsc_also_bought($product_id) {
  /*
   * Displays products that were bought aling with the product defined by $product_id
   * most of it scarcely needs describing
   */
  global $wpdb;
  $siteurl = get_option('siteurl');
  
  if(get_option('wpsc_also_bought') == 0) {
    //returns nothing if this is off
    return '';
	}
  
  // to be made customiseable in a future release
  $also_bought_limit = 3;
  $element_widths = 96; 
  $image_display_height = 96; 
  $image_display_width = 96; 
  
  $also_bought = $wpdb->get_results("SELECT `".$wpdb->prefix."product_list`.* FROM `".$wpdb->prefix."also_bought_product`, `".$wpdb->prefix."product_list` WHERE `selected_product`='".$product_id."' AND `".$wpdb->prefix."also_bought_product`.`associated_product` = `".$wpdb->prefix."product_list`.`id` AND `".$wpdb->prefix."product_list`.`active` IN('1') ORDER BY `wp_also_bought_product`.`quantity` DESC LIMIT $also_bought_limit",ARRAY_A);
  if(count($also_bought) > 0) {
    $output = "<p class='wpsc_also_bought_header'>".TXT_WPSC_ALSO_BOUGHT."</p>";
    $output .= "<div class='wpsc_also_bought'>";
    foreach((array)$also_bought as $also_bought_data) {
      $output .= "<p class='wpsc_also_bought' style='width: ".$element_widths."px;'>";
      if(get_option('show_thumbnails') == 1) {
        if($also_bought_data['image'] !=null) {
          $image_size = @getimagesize(WPSC_THUMBNAIL_DIR.$also_bought_data['image']);
          $largest_dimension  = ($image_size[1] >= $image_size[0]) ? $image_size[1] : $image_size[0];
          $size_multiplier = ($image_display_height / $largest_dimension);
          // to only make images smaller, scaling up is ugly, also, if one is scaled, so must the other be scaled
          if(($image_size[0] >= $image_display_width) || ($image_size[1] >= $image_display_height)) {
            $resized_width  = $image_size[0]*$size_multiplier;
            $resized_height =$image_size[1]*$size_multiplier;
					} else {
            $resized_width  = $image_size[0];
            $resized_height =$image_size[1];
					}            
          $margin_top = floor((96 - $resized_height) / 2);
          $margin_top = 0;
          
          $image_link = WPSC_IMAGE_URL.$also_bought_data['image'];          
          if($also_bought_data['thumbnail_image'] != null) {
            $image_file_name = $also_bought_data['thumbnail_image'];
					} else {
            $image_file_name = $also_bought_data['image'];
					}           
          
          $output .= "<a href='".wpsc_product_url($also_bought_data['id'])."' class='preview_link'  rel='".str_replace(" ", "_",$also_bought_data['name'])."'>";          
          $image_url = "index.php?productid=".$also_bought_data['id']."&amp;thumbnail=true&amp;width=".$resized_width."&amp;height=".$resized_height."";        
          $output .= "<img src='$siteurl/$image_url' id='product_image_".$also_bought_data['id']."' class='product_image' style='margin-top: ".$margin_top."px'/>";
          $output .= "</a>";
				} else {
          if(get_option('product_image_width') != '') {
            $output .= "<img src='".WPSC_URL."/no-image-uploaded.gif' title='".$also_bought_data['name']."' alt='".$also_bought_data['name']."' width='$image_display_height' height='$image_display_height' id='product_image_".$also_bought_data['id']."' class='product_image' />";
					} else {
            $output .= "<img src='".WPSC_URL."/no-image-uploaded.gif' title='".$also_bought_data['name']."' alt='".$product['name']."' id='product_image_".$also_bought_data['id']."' class='product_image' />";
					}
				}
			}
      $output .= "<a class='wpsc_product_name' href='".wpsc_product_url($also_bought_data['id'])."'>".$also_bought_data['name']."</a>";
      //$output .= "<a href='".wpsc_product_url($also_bought_data['id'])."'>".$also_bought_data['name']."</a>";
      $output .= "</p>";
		}
    $output .= "</div>";
    $output .= "<br clear='all' />";
	}
  return $output;
}  


function fancy_notifications() {
  global $wpdb;
  if(get_option('fancy_notifications') == 1) {
    $output = "";
    $output .= "<div id='fancy_notification'>\n\r";
    $output .= "  <div id='loading_animation'>\n\r";
    $output .= '<img id="fancy_notificationimage" title="Loading" alt="Loading" src="'.WPSC_URL.'/images/indicator.gif" />'.TXT_WPSC_UPDATING."...\n\r";
    $output .= "  </div>\n\r";
    $output .= "  <div id='fancy_notification_content'>\n\r";
    $output .= "  </div>\n\r";
    $output .= "</div>\n\r";
	}
  return $output;
}

function fancy_notification_content($product_id, $quantity_limit = false) {
  global $wpdb;
  $siteurl = get_option('siteurl');
  $instock = true;
  if(is_numeric($product_id)) {
    $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$product_id."' LIMIT 1";
    $product = $wpdb->get_row($sql,ARRAY_A);
    //if($product['quantity_limited'] == 1) { }
    $output = "";
    if($quantity_limit == false) {
      $output .= "<span>".str_replace("[product_name]", stripslashes($product['name']), TXT_WPSC_YOU_JUST_ADDED)."</span>";
		} else {
			$output .= "<span>".str_replace("[product_name]", $product['name'], TXT_WPSC_SORRY_NONE_LEFT)."</span>";
		}
    $output .= "<a href='".get_option('shopping_cart_url')."' class='go_to_checkout'>".TXT_WPSC_GOTOCHECKOUT."</a>";
    $output .= "<a href='#' onclick='jQuery(\"#fancy_notification\").css(\"display\", \"none\"); return false;' class='continue_shopping'>".TXT_WPSC_CONTINUE_SHOPPING."</a>";
	}
  return $output;
}


function wpsc_product_url($product_id, $category_id = null) {
  global $wpdb, $wp_rewrite, $wp_query;
  
  if(!is_numeric($category_id) || ($category_id < 1)) {
		if(is_numeric($wp_query->query_vars['product_category'])) {
		  $category_id = $wp_query->query_vars['product_category'];
		} else {
			$category_list = $wpdb->get_row("SELECT `".$wpdb->prefix."product_categories`.`id`, IF((`".$wpdb->prefix."product_categories`.`id` = '".get_option('wpsc_default_category')."'), 0, 1) AS `order_state` FROM `".$wpdb->prefix."item_category_associations` , `".$wpdb->prefix."product_categories` WHERE `".$wpdb->prefix."item_category_associations`.`product_id` IN ('".$product_id."') AND `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_categories`.`id` AND `".$wpdb->prefix."product_categories`.`active` IN('1') LIMIT 1",ARRAY_A);
			$category_id = $category_list['id'];		
		}
  }
  

  
  if((($wp_rewrite->rules != null) && ($wp_rewrite != null)) || (get_option('rewrite_rules') != null)) {
    $url_name = get_product_meta($product_id, 'url_name', true);	
		$product_url =wpsc_category_url($category_id).$url_name[0]."/";
  } else {    
    if(!stristr(get_option('product_list_url'), "?")) {
      $initial_seperator = "?";
    } else {
      $initial_seperator = "&amp;";
    }
    if(is_numeric($category_id) && ($category_id > 0)) {
      $product_url = get_option('product_list_url').$initial_seperator."category=".$category_id."&amp;product_id=".$product_id;
    } else {
      $product_url = get_option('product_list_url').$initial_seperator."product_id=".$product_id;
    }
  }
  return $product_url;
}

function google_buynow($product_id) {
	global $wpdb;
	$output = "";
	if ($product_id > 0){
		$product_sql = "SELECT * FROM ".$wpdb->prefix."product_list WHERE id = ".$product_id." LIMIT 1";
		$product_info = $wpdb->get_results($product_sql, ARRAY_A);
		$variation_sql = "SELECT * FROM ".$wpdb->prefix."variation_priceandstock WHERE product_id = ".$product_id;
		$variation_info = $wpdb->get_results($variation_sql, ARRAY_A);
		if (count($variation_info) > 0) {
			$variation = 1;
			$price = $variation_info[0]['price'];
		}
		if (get_option('google_server_type')=='production') {
			$action_target = "https://checkout.google.com/cws/v2/Merchant/".get_option('google_id')."/checkoutForm";
		} else {
			$action_target = "https://sandbox.google.com/checkout/cws/v2/Merchant/".get_option('google_id')."/checkoutForm";
		}

	
		$product_info = $product_info[0];
		$output .= "<form id='BB_BuyButtonForm".$product_id."' onsubmit='log_buynow(this);return true;' action= 'https://sandbox.google.com/checkout/cws/v2/Merchant/".get_option('google_id')."/checkoutForm'c method='post' name='BB_BuyButtonForm".$product_id."'>";
		$output .= "<input name='product_id' type='hidden' value='".$product_id."'>";
		$output .= "<input name='item_name_1' type='hidden' value='".$product_info['name']."'>";
		$output .= "<input name='item_description_1' type='hidden' value='".$product_info['description']."'>";
		$output .= "<input name='item_quantity_1' type='hidden' value='1'>";
		if ($variation == 1) {
			$output .= "<input id='item_price' name='item_price_1' type='hidden' value='".$price."'>";
		} else {
			if ($product_info['special']=='0') {
				$output .= "<input id='item_price' name='item_price_1' type='hidden' value='".$product_info['price']."'>";
			} else {
				$output .= "<input name='item_price_1' type='hidden' value='".$product_info['special_price']."'>";
			}
		}
		$output .= "<input name='item_currency_1' type='hidden' value='".get_option('google_cur')."'>";
		$output .= "<input type='hidden' name='checkout-flow-support.merchant-checkout-flow-support.continue-shopping-url' value='".get_option('product_list_url')."'>";
		$output .= "<input type='hidden' name='checkout-flow-support.merchant-checkout-flow-support.edit-cart-url' value='".get_option('shopping_cart_url')."'>";
		$output .= "<input alt='' src=' https://checkout.google.com/buttons/buy.gif?merchant_id=".get_option('google_id')."&w=117&h=48&style=white&variant=text&loc=en_US' type='image'/>";
		$output .="</form>";
	}
	return $output;
}

function external_link($product_id) { 
	global $wpdb;
	$product_sql = "SELECT * FROM ".$wpdb->prefix."product_list WHERE id = ".$product_id." LIMIT 1";
	$product_info = $wpdb->get_results($product_sql, ARRAY_A);
	$product_info = $product_info[0];
	$link = $product_info['external_link'];
	$output .= "<input type='button' value='".TXT_WPSC_BUYNOW."' onclick='gotoexternallink(\"$link\")'>";
	return $output;
}


// displays error messages if the category setup is odd in some way
// needs to be in a function because there are at least three places where this code must be used.
function wpsc_odd_category_setup() {
	get_currentuserinfo();
  global $userdata;  
  $output = '';
  if(($userdata->wp_capabilities['administrator'] ==1) || ($userdata->user_level >=9)) {
    if(get_option('wpsc_default_category') == 1) {
			$output = "<p>".TXT_WPSC_USING_EXAMPLE_CATEGORY."</p>";
		} else {
		  $output = "<p>".TXT_WPSC_ADMIN_EMPTY_CATEGORY."</p>";
		}
  }
  return $output;
}

function wpsc_buy_now_button($product_id, $replaced_shortcode = false) {
  global $wpdb;
  $selected_gateways = get_option('custom_gateway_options');

  if (in_array('google', (array)$selected_gateways)) {
		$output .= google_buynow($product['id']);
	} else if (in_array('paypal_multiple', (array)$selected_gateways)) {
		if ($product_id > 0){
			$product_sql = "SELECT * FROM ".$wpdb->prefix."product_list WHERE id = ".$product_id." LIMIT 1";
			$product = $wpdb->get_row($product_sql, ARRAY_A);
			$output .= "<form onsubmit='log_paypal_buynow(this)' target='paypal' action='".get_option('paypal_multiple_url')."' method='post'>
				<input type='hidden' name='business' value='".get_option('paypal_multiple_business')."'>
				<input type='hidden' name='cmd' value='_xclick'>
				<input type='hidden' name='item_name' value='".$product['name']."'>
				<input type='hidden' id='item_number' name='item_number' value='".$product['id']."'>
				<input type='hidden' id='amount' name='amount' value='".$product['price']."'>
				<input type='hidden' id='unit' name='unit' value='".$product['price']."'>
				<input type='hidden' id='shipping' name='ship11' value='".$shipping."'>
				<input type='hidden' name='handling' value='".get_option('base_local_shipping')."'>
				<input type='hidden' name='currency_code' value='".get_option('paypal_curcode')."'>
				<input type='hidden' name='undefined_quantity' value='0'>
				<input type='image' name='submit' border='0' src='https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif' alt='PayPal - The safer, easier way to pay online'>
				<img alt='' border='0' width='1' height='1' src='https://www.paypal.com/en_US/i/scr/pixel.gif' >
			</form>
		";
		}
	}
	if($replaced_shortcode == true) {
		return $output;
	} else {
		echo $output;
  }
}
?>