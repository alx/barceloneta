<?php
// pe.{
// To stick this in sidebar, main page (calling products_page.php) must be called before sidebar.php in the loop (think)
  
function display_subcategories($id) {
  global $wpdb;
  
  if(get_option('permalink_structure') != '') {
    $seperator ="?";
  } else {
    $seperator ="&amp;";
	}   
  $subcategory_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '".$id."' ORDER BY `id`";
  $subcategories = $wpdb->get_results($subcategory_sql,ARRAY_A);
  if($subcategories != null) {
    $output .= "<ul class='SubCategories'>";
    foreach($subcategories as $subcategory) {
      $output .= "<li><a class='categorylink' href='".wpsc_category_url($subcategory['id'])."'>".stripslashes($subcategory['name'])."</a>".display_subcategories($subcategory['id'])."</li>";
		} 
    $output .= "</ul>";
	} else {
		return '';
	}
  return $output;
  }
 
function show_cats_brands($category_group = null , $display_method = null, $order_by = 'name', $image = null) {
  global $wpdb; 
  
  if($category_group == null) {
		$category_group =  $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."wpsc_categorisation_groups` WHERE `active` IN ('1') AND `default` IN ('1') LIMIT 1 ");
  } else {
    $category_group = (int)$category_group;
  }
  
  // Show cats & brands list if displaying on every page or if on a shop page (bit hacky but out of time).
  if (get_option('cat_brand_loc') != 3 && !function_exists("nzshpcrt_display_categories_groups") && ($display_method != 'sidebar')) {
    return;
  }
  
  if(get_option('permalink_structure') != '') {
    $seperator ="?";
	} else {
    $seperator ="&amp;";
	}

  $output = "<div class='PeSwitcher'>";

  switch(get_option('show_categorybrands')) {
    case 1:
      $output .= "<ul id='PeCatsBrandsBoth' class='category_brand_header'><li id='PeSwitcherFirst'><a href='' onclick='return prodgroupswitch(\"categories\");'>".TXT_WPSC_CATEGORIES."</a> | <a href='' onclick='return prodgroupswitch(\"brands\");'>".TXT_WPSC_BRANDS."</a></li></ul>";
      break;
  }
  $output .= "</div>";
  
  $output .= "<div class='PeCatsBrands'>";
  
  
  if((get_option('show_categorybrands') == 1 ) || (get_option('show_categorybrands') == 2))
    {
    $output .= "<div class='PeCategories categorydisplay'>";
    $categories = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `group_id` IN ('$category_group') AND `active`='1' AND `category_parent` = '0' ORDER BY `".$wpdb->escape($order_by)."` ASC",ARRAY_A);
    if($categories != null)
    {
      $output .= "<ul class='PeCategories'>";
      foreach($categories as $option)
      {
        // Adrian - check option for category count
        if (get_option('show_category_count') == 1){
          //show product count for each category
          $count_sql = "SELECT count(*) FROM `".$wpdb->prefix."item_category_associations` WHERE `category_id` = '".$option['id']."'";
          $count = $wpdb->get_var($count_sql);
          $addCount =  " [".$count."]";
        } //end get_option
        // Adrian - if sliding category type selected, NO link for category text, mootools.js creates the linkable sliders onDomReady.
        if (get_option('catsprods_display_type') == 1){ 
          $output .= "<li class='MainCategory'><strong class='category'>".stripslashes($option['name']).$addCount."</strong>";
        }else{
        // Adrian - otherwise create normal category text with or without product count
		if (!$image) {
			$output .= "<li class='MainCategory'><strong class='category'><a class='productlink' href='".wpsc_category_url($option['id'])."'>".stripslashes($option['name']).$addCount."</a></strong>";
		} else {
			$output .= "<li class='MainCategory'><img src='".get_option('siteurl')."/wp-content/uploads/wpsc/category_images/".$option['image']."'><br><strong class='category'><a class='productlink' href='".wpsc_category_url($option['id'])."'>".stripslashes($option['name']).$addCount."</a></strong>";
		}
	}//end get_option
        $subcategory_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `group_id` IN ('$category_group') AND `active`='1' AND `category_parent` = '".$option['id']."' ORDER BY `id`";
        $subcategories = $wpdb->get_results($subcategory_sql,ARRAY_A);
        if($subcategories != null)
        {
        $output .= display_subcategories($option['id']);
        } else {
          // Adrian - check if the user wants categories only or sliding categories
          if (get_option('catsprods_display_type') == 1){     
          // Adrian - display all products for that category          
            $product_sql = "SELECT product_id FROM `".$wpdb->prefix."item_category_associations` WHERE `category_id` = '".$option['id']."'";
            $productIDs = $wpdb->get_results($product_sql,ARRAY_A);
            if($productIDs != null){
              $output .= "<ul>";
              foreach($productIDs as $productID){
                $ID = $productID['product_id'];
                $productName_sql = "SELECT name FROM `".$wpdb->prefix."product_list` WHERE `id` = '".$ID."'";
                $productName = $wpdb->get_var($productName_sql);
                $output .= "<li><a class='productlink' href='".wpsc_product_url($ID,$option['id'])."'>".$productName."</a></li>";
              }//end foreach            
            $output .= "</ul>";         
            }//end if productsIDs
          }//end if get_option        
        }//end else
      $output .= "</li>";
      }
      $output .= "</ul>";
    }
    $output .= "</div>";
  }
  
  if((get_option('show_categorybrands') == 1 ) || (get_option('show_categorybrands') == 3))
  {
    if(get_option('show_categorybrands')  == 1)
      {
      $output .= "<ul class='PeBrands branddisplay' style='display: none;'>";
      }
      else
      {
      $output .= "<ul class='PeBrands branddisplay'>";
      }
    //$output ='';
    $brands = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`='1' ORDER BY `order` ASC",ARRAY_A);
    if($brands != null)
    {
      foreach($brands as $option)
      {
        $output .= "<li><a class='categorylink' href='".get_option('product_list_url').$seperator."brand=".$option['id']."'>".stripslashes($option['name'])."</a></li>";
      }
    }
    //$output .= $output;
    $output .= "</ul>";
  }
  
  $output .= "</div>";
  echo $output;
}

function wpsc_category_url($category_id) {
  global $wpdb, $wp_rewrite;
  $category_data = $wpdb->get_row("SELECT `nice-name`,`category_parent` FROM `".$wpdb->prefix."product_categories` WHERE `id` IN ('".(int)$category_id."') AND `active` IN('1') LIMIT 1", ARRAY_A);
  $category_name[] = $category_data['nice-name'];
  if($category_data['category_parent'] > 0) {
    $num = 0;
    while($category_data['category_parent'] > 0) {
			$category_data = $wpdb->get_row("SELECT `nice-name`,`category_parent` FROM `".$wpdb->prefix."product_categories` WHERE `id` IN ('".(int)$category_data['category_parent']."') AND `active` IN('1') LIMIT 1", ARRAY_A);
			$category_name[] = $category_data['nice-name'];			
			if($num > 10) { break; }
			$num++;			
      }
  }
  $category_name = array_reverse($category_name);
  if((($wp_rewrite->rules != null) && ($wp_rewrite != null)) || (get_option('rewrite_rules') != null)) {
    $url_name = get_product_meta($product_id, 'url_name', true);
    if(!empty($category_name)) {
			if(substr(get_option('product_list_url'), -1, 1) == '/') {
				$category_url = get_option('product_list_url').implode($category_name,"/")."/";
				} else {
				$category_url = get_option('product_list_url')."/".implode($category_name,"/")."/";
				}
      } else {
      $category_url = get_option('product_list_url');
      }
    } else {
    $category_url = get_option('product_list_url')."&amp;category=".$category_id;
    }
  return $category_url;
  }

function wpsc_category_description($category_id = null) {
  global $wpdb, $wp_query;
  /*<?php echo wpsc_category_description(); ?> */
  if($category_id ==  null) {
    if($wp_query->query_vars['product_category'] != null) {
      $category_id = $wp_query->query_vars['product_category'];
      } else if(is_numeric($_GET['category'])) {
      $category_id = $_GET['category'];
      }
    }
  
  $category_description = "<p>";
  $category_description .= $wpdb->get_var("SELECT `description` FROM `".$wpdb->prefix."product_categories` WHERE `id` IN ('".(int)$category_id."') AND `active` IN('1') LIMIT 1");
  $category_description .= "</p>";
  return $category_description;
  }
?>