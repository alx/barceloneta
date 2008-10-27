<?php
include_once('tagging_functions.php');
include_once('google_base_functions.php');
$category_data = null;

function topcategorylist() {
  global $wpdb,$category_data;
  $siteurl = get_option('siteurl'); 
  $url = $siteurl."/wp-admin/admin.php?page=".WPSC_DIR_NAME."/display-items.php";
  $options = "";
  $options .= "<option value='$url'>".TXT_WPSC_ALLCATEGORIES."</option>\r\n";
  $options .= top_category_options(null, 0, $_GET['catid']);
  $concat .= "<select name='category' onChange='categorylist(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
  return $concat;
}

function top_category_options($category_id = null, $iteration = 0, $selected_id = null) {
  /*
   * Displays the category forms for adding and editing products
   * Recurses to generate the branched view for subcategories
   */
  global $wpdb;
  $siteurl = get_option('siteurl'); 
  $url = $siteurl."/wp-admin/admin.php?page=".WPSC_DIR_NAME."/display-items.php";
  if(is_numeric($category_id)) {
    $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '$category_id'  ORDER BY `id` ASC",ARRAY_A);
	} else {
    $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '0'  ORDER BY `id` ASC",ARRAY_A);
	}
  foreach((array)$values as $option) {
    if($selected_id == $option['id']) {
      $selected = "selected='selected'";
    }
    $output .= "<option $selected value='$url&amp;catid=".$option['id']."'>".str_repeat("-", $iteration).stripslashes($option['name'])."</option>\r\n";
    $output .= top_category_options($option['id'], $iteration+1, $selected_id);
    $selected = "";
  }
  return $output;
}

function brandslist($current_brand = '') {
  global $wpdb;
  $options = "";
  $options .= "<option  $selected value='0'>".TXT_WPSC_SELECTABRAND."</option>\r\n";
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`='1' ORDER BY `id` ASC",ARRAY_A);
  foreach((array)$values as $option) {
    if($curent_category == $option['id']) {
      $selected = "selected='selected'";
    }
    $options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
    $selected = "";
  }
  $concat .= "<select name='brand'>".$options."</select>\r\n";
  return $concat;
}

function variationslist($current_variation = '') {
	global $wpdb;
	$options = "";
	$values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_variations` ORDER BY `id` ASC",ARRAY_A);
	$options .= "<option  $selected value='0'>".TXT_WPSC_PLEASECHOOSE."</option>\r\n";
	foreach((array)$values as $option) {
		if($current_brand == $option['id']) {
			$selected = "selected='selected'";
		}
		$options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
		$selected = "";
	}
	$concat .= "<select name='variations' onChange='add_variation_value_list(this.options[this.selectedIndex].value)'>".$options."</select>\r\n";
	return $concat;
}

/*
 * Makes the order changes
 */

if(is_numeric($_GET['catid']) && is_numeric($_GET['product_id']) && ($_GET['position_action'] != ''))
  {
  $position_cat_id = $_GET['catid'];
  $position_prod_id = $_GET['product_id'];
  $current_order_row = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_order` WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('$position_prod_id') LIMIT 1;",ARRAY_A);
  $current_order_row = $current_order_row[0];
  switch($_GET['position_action'])
    {
    case 'top':
    if($current_order_row['order'] > 0) 
      {
      $check_existing = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_order` WHERE `category_id` IN('$position_cat_id') AND `order` IN('0') LIMIT 1;",ARRAY_A);
      $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = '0' WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('$position_prod_id') LIMIT 1;");
      if($check_existing != null)
        {
        $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = (`order` + 1) WHERE `category_id` IN('$position_cat_id') AND `product_id` NOT IN('$position_prod_id') AND `order` < '".$current_order_row['order']."'");
        }
      }
    break;
    
    case 'up':
    if($current_order_row['order'] > 0) 
      {
      $target_rows = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_order` WHERE `category_id` IN ('".$position_cat_id."') AND `order` <= '".$current_order_row['order']."' ORDER BY `order` DESC LIMIT 2",ARRAY_A);
      //exit("<pre>".print_r($target_rows,true)."</pre>");
      if(count($target_rows) == 2)
        {
        $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = '".$target_rows[1]['order']."' WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('".$target_rows[0]['product_id']."') LIMIT 1");
        $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = '".$target_rows[0]['order']."' WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('".$target_rows[1]['product_id']."') LIMIT 1");
        }
      }
    break;
    
    case 'down':
    $target_rows = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_order` WHERE `category_id` IN ('".$position_cat_id."') AND `order` >= '".$current_order_row['order']."' ORDER BY `order` ASC LIMIT 2",ARRAY_A);
    //exit("<pre>".print_r($target_rows,true)."</pre>");
    if(count($target_rows) == 2)
      {
      $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = '".$target_rows[1]['order']."' WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('".$target_rows[0]['product_id']."') LIMIT 1");
      $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = '".$target_rows[0]['order']."' WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('".$target_rows[1]['product_id']."') LIMIT 1");
      }
    break;
    
    case 'bottom':
    $end_row = $wpdb->get_results("SELECT MAX( `order` ) AS `order` FROM `".$wpdb->prefix."product_order` WHERE `category_id` IN ('".$position_cat_id."') LIMIT 1",ARRAY_A);
    $end_order_number = $end_row[0]['order'];
    //exit($current_order_row['order'] . " | " . $end_order_number);
    if($current_order_row['order'] < $end_order_number)
      {
      $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = '$end_order_number' WHERE `category_id` IN('$position_cat_id') AND `product_id` IN('$position_prod_id') LIMIT 1;");      
      $wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order` = (`order` - 1) WHERE `category_id` IN('$position_cat_id') AND `product_id` NOT IN('$position_prod_id') AND `order` > '".$current_order_row['order']."'");
      }
    break;
    
    default:
    break;
    }
  }


/*
 * Adds new products
 */
if($_POST['submit_action'] == 'add') {
  // well, there is simply no way to do this other than the blatantly obvious, so here it is
  if(!is_callable('getshopped_item_limit') || (@getshopped_item_limit() !== false)) {
  
		//Allen's Change for Google base
		if (isset($_GET['token']) || isset($_SESSION['google_base_sessionToken'])) {
			$sessionToken=exchangeToken($_GET['token']);
			$_SESSION['google_base_sessionToken'] = $sessionToken;
			if (isset($_SESSION['google_base_sessionToken']))
				$sessionToken=$_SESSION['google_base_sessionToken'];
			postItem($_POST['name'], $_POST['price'], $_POST['description'], $sessionToken);
		}
	//Google base change ends here
	
		$file_name = null;
		if($_POST['file_url'] != null) {
			$url_array = array_reverse(explode("/",$_POST['file_url']));
			if(is_file(WPSC_FILE_DIR.$url_array[0])) {
				$file_name = $url_array[0];
			}
		}
		
		$thumbnail_image = '';
		
			
		$file = 0;  
		/* handle adding file uploads here */
		if(!empty($_FILES['file']['name'])) {
			$fileid = wpsc_item_process_file('add');
			$file = $fileid;
		} else if (($_POST['select_product_file'] != '')) {
			$fileid = wpsc_item_reassign_file($_POST['select_product_file'], 'add');
			$file = $fileid;
		}
				
		
	if(is_numeric($_POST['quantity']) && ($_POST['quantity_limited'] == "yes")) {
				$quantity_limited = 1;
				$quantity = (int)$_POST['quantity'];
			} else {
				$quantity_limited = 0;
				$quantity = 0;
			}
				
			if($_POST['special'] == 'yes') {
				$special = 1;
				if(is_numeric($_POST['special_price'])) {
					$special_price = $_POST['price'] - $_POST['special_price'];
				}
			} else {
				$special = 0;
				$special_price = '';
			}
			
			if($_POST['notax'] == 'yes') {
				$notax = 1;
			} else {
				$notax = 0;
			}
	
				
			if($_POST['display_frontpage'] == "yes") {
				$display_frontpage = 1;
			} else {
				$display_frontpage = 0;
			}
			
			if($_POST['donation'] == "yes") {
				$is_donation = 1;
			} else {
				$is_donation = 0;
			}
			
			if($_POST['no_shipping'] == "yes") {
				$no_shipping = 1;
			} else {
				$no_shipping = 0;
			}
	
				
	
				
			//modified for USPS
		$insertsql = "INSERT INTO `".$wpdb->prefix."product_list` ( `name` , `description` , `additional_description` , `price`, `weight`, `weight_unit`, `pnp`, `international_pnp`, `file` , `image` , `brand`, `quantity_limited`, `quantity`, `special`, `special_price`, `display_frontpage`,`notax`, `donation`, `no_shipping`, `thumbnail_image`, `thumbnail_state`) VALUES ('".$wpdb->escape($_POST['name'])."', '".$wpdb->escape($_POST['description'])."', '".$wpdb->escape($_POST['additional_description'])."','".(float)$wpdb->escape(str_replace(",","",$_POST['price']))."','".$wpdb->escape((float)$_POST['weight'])."','".$wpdb->escape($_POST['weight_unit'])."', '".$wpdb->escape((float)$_POST['pnp'])."', '".$wpdb->escape($_POST['international_pnp'])."', '".(int)$file."', '".$image."', '0', '$quantity_limited','$quantity','$special','$special_price', '$display_frontpage', '$notax', '$is_donation', '$no_shipping', '".$wpdb->escape($thumbnail_image)."', '" . $wpdb->escape($_POST['image_resize']) . "');";
		
		
		if($wpdb->query($insertsql)) {  
			$product_id_data = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `".$wpdb->prefix."product_list` LIMIT 1",ARRAY_A);
			$product_id = $product_id_data[0]['id'];
			if(function_exists('wp_insert_term')) {
				product_tag_init();
				$tags = $_POST['product_tag'];
				if ($tags!="") {
					$tags = explode(',',$tags);
					foreach($tags as $tag) {
						$tt = wp_insert_term((string)$tag, 'product_tag');
					}
					$return = wp_set_object_terms($product_id, $tags, 'product_tag');
				}
			}
		
				/* Handle new image uploads here */
			$image = wpsc_item_process_image($product_id);
	
	
			/* Process extra meta values */
			if($_POST['productmeta_values'] != null) {
				foreach((array)$_POST['productmeta_values'] as $key => $value) {
					if(get_product_meta($product_id, $key) != false) {
						update_product_meta($product_id, $key, $value);
					} else {
						add_product_meta($product_id, $key, $value);
					}
				}
			}
					
			if($_POST['new_custom_meta'] != null) {
				foreach((array)$_POST['new_custom_meta']['name'] as $key => $name) {
					$value = $_POST['new_custom_meta']['value'][(int)$key];
					if(($name != '') && ($value != '')) {
						add_product_meta($product_id, $name, $value, false, true);
					}
				}
			}
			
			do_action('wpsc_product_form_submit', $product_id);
			
			/* Add tidy url name */
			$tidied_name = trim($_POST['name']);
			$tidied_name = strtolower($tidied_name);
			$url_name = preg_replace(array("/(\s)+/","/[^\w-]+/i"), array("-", ''), $tidied_name);
			$similar_names = $wpdb->get_row("SELECT COUNT(*) AS `count`, MAX(REPLACE(`meta_value`, '$url_name', '')) AS `max_number` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN ('url_name') AND `meta_value` REGEXP '^($url_name){1}(\d)*$' ",ARRAY_A);
			$extension_number = '';
			if($similar_names['count'] > 0) {
				$extension_number = (int)$similar_names['max_number']+1;
				}
			$url_name .= $extension_number;
			add_product_meta($product_id, 'url_name', $url_name,true);
			
			if(($_FILES['extra_image'] != null) && function_exists('edit_submit_extra_images')) {
				$var = edit_submit_extra_images($product_id);
			}
			
			$variations_procesor = new nzshpcrt_variations;
			if($_POST['variation_values'] != null) {
				$variations_procesor->add_to_existing_product($product_id,$_POST['variation_values']);
			}
				
			if($_POST['variation_priceandstock'] != null) {
				$variations_procesor->update_variation_values($product_id, $_POST['variation_priceandstock']);
	// 			  exit("<pre>".print_r($_POST,true)."</pre>");
			}
			
			
				//$variations_procesor->edit_add_product_values($_POST['prodid'],$_POST['edit_add_variation_values']);
			$counter = 0;
			$item_list = '';
			if(count($_POST['category']) > 0) {
				foreach($_POST['category'] as $category_id) {
					$check_existing = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id` = ".$product_id." AND `category_id` = '$category_id' LIMIT 1");
					if($check_existing == null) {
						$wpdb->query("INSERT INTO `".$wpdb->prefix."item_category_associations` ( `product_id` , `category_id` ) VALUES ( '".$product_id."', '".$category_id."');");        
					}
				}
			}
			// send the pings out.
		 wpsc_ping();		
			
			$display_added_product = "filleditform(".$product_id.");";
			
			echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASBEENADDED."</p></div>";
		} else {
			echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASNOTBEENADDED."</p></div>";
		}
	} else {
		echo "<div class='updated'><p align='center'>".TXT_WPSC_MAX_PRODUCTS."</p></div>";
	}
}

if($_GET['submit_action'] == "remove_set")
  {
  if(is_numeric($_GET['product_id']) && is_numeric($_GET['variation_assoc_id']))
    {
    $product_id = $_GET['product_id'];
    $variation_assoc_id = $_GET['variation_assoc_id'];
    $check_association_id = $wpdb->get_var("SELECT `id` FROM `".$table_prefix."variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1");
    if(($variation_assoc_id > 0) && ($variation_assoc_id == $check_association_id))
      {
      $variation_association = $wpdb->get_row("SELECT * FROM `".$table_prefix."variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1",ARRAY_A);
      $delete_variation_sql = "DELETE FROM `".$table_prefix."variation_associations` WHERE `id` = '$variation_assoc_id' LIMIT 1";
      $wpdb->query($delete_variation_sql);
      //echo("<pre>".print_r($variation_association,true)."</pre>");
      if($variation_association != null)
        {
        $variation_id = $variation_association['variation_id'];
        $delete_value_sql = "DELETE FROM `".$table_prefix."variation_values_associations` WHERE `product_id` = '$product_id' AND `variation_id` = '$variation_id'";
        //exit($delete_value_sql);
        $wpdb->query($delete_value_sql);
        }
      echo "<div class='updated'><p align='center'>".TXT_WPSC_PRODUCTHASBEENEDITED."</p></div>";
      }
    } 
  }

if($_POST['submit_action'] == "edit") {
//   exit("<pre>".print_r($_POST,true)."</pre>");
  $id = $_POST['prodid'];
  if(function_exists('edit_submit_extra_images'))
    {
    if(($_FILES['extra_image'] != null))
      {
      $var = edit_submit_extra_images($id);
      }
    }
  if(function_exists('edit_extra_images'))
    {
    $var = edit_extra_images($id);
    } 
    
	$file_name = null;
	if($_POST['file_url'] != null) {
	$url_array = array_reverse(explode("/",$_POST['file_url']));
	//exit("<pre>".print_r($url_array,true)."</pre>");
	if(is_file(WPSC_FILE_DIR.$url_array[0])) {
		$file_name = $url_array[0];
		}
	}
  
	//written by allen
	if(isset($_POST['product_tags'])) {
		$imtags = $_POST['product_tags'];
		$tags = explode(',',$imtags);
		product_tag_init();
		if(is_array($tags)) {
			foreach((array)$tags as $tag){
				$tt = wp_insert_term((string)$tag, 'product_tag');
			}
		}
		wp_set_object_terms($id, $tags, 'product_tag');
	}
	//end of written by allen

	if (isset($_POST['external_link'])) {
		add_product_meta($_POST['prodid'], 'external_link', $_POST['external_link'],true);
	}
	
	if (isset($_POST['merchant_notes'])) {
		$id = (int)$_POST['prodid'];
		$notes = $_POST['merchant_notes'];
		$updatelink_sql = "SELECT * FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `product_id` = '$id' AND `meta_key`='merchant_notes'";
		$updatelink_data = $wpdb->get_results($updatelink_sql, ARRAY_A);
		if (count($updatelink_data)>0){
			$updatelink_sql = "UPDATE `".$wpdb->prefix."wpsc_productmeta` SET `meta_value` = '$notes' WHERE `product_id` = '$id' AND `meta_key`='merchant_notes'";
			$updatelink_data = $wpdb->query($updatelink_sql);
		} else {
			$updatelink_sql = "INSERT INTO `".$wpdb->prefix."wpsc_productmeta` (`product_id`,`meta_key`,`meta_value`) VALUES('$id','merchant_notes' ,'$notes')";
			$updatelink_data = $wpdb->query($updatelink_sql);
		}
	}
	
// 	if (isset($_POST['engrave'])) {
// 		$id = $_POST['prodid'];
// 		$engrave = $_POST['engrave'];
// 		$updatelink_sql = "SELECT * FROM ".$wpdb->prefix."wpsc_productmeta WHERE product_id = $id AND meta_key='merchant_notes'";
// 		$updatelink_data = $wpdb->get_results($updatelink_sql, ARRAY_A);
// 		if (count($updatelink_data)>0){
// 			$updatelink_sql = "UPDATE ".$wpdb->prefix."wpsc_productmeta SET meta_value = '$notes' WHERE product_id = $id AND meta_key='merchant_notes'";
// 			$updatelink_data = $wpdb->query($updatelink_sql);
// 		} else {
// 			$updatelink_sql = "INSERT INTO ".$wpdb->prefix."wpsc_productmeta VALUES('',$id,'merchant_notes' ,'$notes')";
// 			$updatelink_data = $wpdb->query($updatelink_sql);
// 		}
// 	}
  
  /* handle editing file uploads here */
  if(!empty($_FILES['file']['name'])) {
		$fileid = wpsc_item_process_file('edit');
		$file = $fileid;
	} else if (($_POST['select_product_file'] != '')) {
		$fileid = wpsc_item_reassign_file($_POST['select_product_file'], 'edit');
		$file = $fileid;
	}

  
	if(file_exists($_FILES['preview_file']['tmp_name'])) {
		$fileid = $wpdb->get_var("SELECT `file` FROM `".$wpdb->prefix."product_list` WHERE `id` = '$id' LIMIT 1");
		copy($_FILES['preview_file']['tmp_name'], (WPSC_PREVIEW_DIR.basename($_FILES['preview_file']['name'])));
		$mimetype = wpsc_get_mimetype(WPSC_PREVIEW_DIR.basename($_FILES['preview_file']['name']));
		$wpdb->query("UPDATE `".$wpdb->prefix."product_files` SET `preview` = '".$wpdb->escape(basename($_FILES['preview_file']['name']))."', `preview_mimetype` = '".$mimetype."' WHERE `id` = '$fileid' LIMIT 1");
		}

  /* Handle new image uploads here */
  $image = wpsc_item_process_image();


  if(is_numeric($_POST['prodid'])) {
		if(($_POST['image_resize'] == 1 || $_POST['image_resize'] == 2) && ($image == '')) {
      /*  resize the image if directed to do so and no new image is supplied  */
      $image_data = $wpdb->get_row("SELECT `id`,`image` FROM `".$wpdb->prefix."product_list` WHERE `id`=".$_POST['prodid']." LIMIT 1",ARRAY_A);      
      // prevent images from being replaced by those from other products
      $check_multiple_use = $wpdb->get_var("SELECT COUNT(`image`) AS `count` FROM `".$wpdb->prefix."product_list` WHERE `image`='".$image_data['image']."'");
      if($check_multiple_use > 1) {
        $new_filename = $image_data['id']."_".$image_data['image'];
        if(file_exists(WPSC_THUMBNAIL_DIR.$image_data['image']) && ($image_data['image'] != null)) {
          copy(WPSC_THUMBNAIL_DIR.$image_data['image'], WPSC_THUMBNAIL_DIR.$new_filename);
          }
        if(file_exists(WPSC_IMAGE_DIR.$image_data['image']) && ($image_data['image'] != null)) {
          copy(WPSC_IMAGE_DIR.$image_data['image'], WPSC_IMAGE_DIR.$new_filename);
          }
        $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `image` = '".$new_filename."' WHERE `id`='".$image_data['id']."' LIMIT 1");
        $image_data = $wpdb->get_row("SELECT `id`,`image` FROM `".$wpdb->prefix."product_list` WHERE `id`=".$_POST['prodid']." LIMIT 1",ARRAY_A);
        }
        
        
      if(file_exists(WPSC_THUMBNAIL_DIR.$image_data['image']) && ($image_data['image'] != '')) {
        $imagepath = WPSC_IMAGE_DIR . $image_data['image'];
        $image_output = WPSC_THUMBNAIL_DIR . $image_data['image'];
        switch($_POST['image_resize']) {
          case 1:
          $height = get_option('product_image_height');
          $width  = get_option('product_image_width');
          break;
  
          case 2:
          $height = $_POST['height'];
          $width  = $_POST['width'];
          break;
          }
        image_processing($imagepath, $image_output, $width, $height);
        }
    }
    
    if(is_numeric($_POST['prodid'])) {
      $counter = 0;
      $item_list = '';
      if(count($_POST['category']) > 0) {
        foreach($_POST['category'] as $category_id) {
          $category_id = (int)$category_id; // force it to be an integer rather than check if it is one
          $check_existing = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id` = ".$id." AND `category_id` = '$category_id' LIMIT 1");
          if($check_existing == null) {
            $wpdb->query("INSERT INTO `".$wpdb->prefix."item_category_associations` ( `product_id` , `category_id` ) VALUES ('".$id."', '".$category_id."');");        
					}
          if($counter > 0) {
            $item_list .= ", ";
					}
          $item_list .= "'".$category_id."'";
          $counter++;
				}
			} else {
				$item_list = "'0'";
			}
      $wpdb->query("DELETE FROM `".$wpdb->prefix."item_category_associations` WHERE `product_id`= '$id' AND `category_id` NOT IN (".$item_list.")"); 
		}
      
		$key = Array();
		
         
		if(is_numeric($_POST['quantity']) && ($_POST['quantity_limited'] == "yes")){
			$quantity_limited = 1;
			$quantity = $_POST['quantity'];
		} else {
			$quantity_limited = 0;
			$quantity = 0;
		}
       
    if($_POST['special'] == 'yes') {
      $special = 1;
			if(is_numeric($_POST['special_price'])) {
				$special_price = $_POST['price'] - $_POST['special_price'];
				}
      } else {
        $special = 0;
        $special_price = '';
			}
  
    if($_POST['notax'] == 'yes') {
      $notax = 1;
		} else {
			$notax = 0;
		}

      
		if($_POST['display_frontpage'] == "yes") {
			$display_frontpage = 1;
		} else {
			$display_frontpage = 0;
		}
   
		if($_POST['donation'] == "yes") {
			$is_donation = 1;
		} else {
			$is_donation = 0;
		}
   
		if($_POST['no_shipping'] == "yes") {
			$no_shipping = 1;
		} else {
			$no_shipping = 0;
		}
		
		$updatesql = "UPDATE `".$wpdb->prefix."product_list` SET `name` = '".$wpdb->escape($_POST['title'])."', `description` = '".$wpdb->escape($_POST['description'])."', `additional_description` = '".$wpdb->escape($_POST['additional_description'])."', `price` = '".$wpdb->escape(str_replace(",","",$_POST['price']))."', `pnp` = '".(float)$wpdb->escape($_POST['pnp'])."', `international_pnp` = '".(float)$wpdb->escape($_POST['international_pnp'])."', `brand` = '0', quantity_limited = '".$quantity_limited."', `quantity` = '".(int)$quantity."', `special`='$special', `special_price`='$special_price', `display_frontpage`='$display_frontpage', `notax`='$notax', `donation`='$is_donation', `no_shipping` = '$no_shipping', `weight` = '".$wpdb->escape($_POST['weight'])."', `weight_unit` = '".$wpdb->escape($_POST['weight_unit'])."'  WHERE `id`='".$_POST['prodid']."' LIMIT 1";

		$wpdb->query($updatesql);
		if(($_FILES['image']['name'] != null) && ($image != null)) {
			$wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `image` = '".$image."' WHERE `id`='".$_POST['prodid']."' LIMIT 1");
		}
    
    if($_POST['productmeta_values'] != null) {
      foreach((array)$_POST['productmeta_values'] as $key => $value) {
        if(get_product_meta($_POST['prodid'], $key) != false) {
          update_product_meta($_POST['prodid'], $key, $value);
          } else {
          add_product_meta($_POST['prodid'], $key, $value);
          }
        }
      }

    if($_POST['new_custom_meta'] != null) {
      foreach((array)$_POST['new_custom_meta']['name'] as $key => $name) {
				$value = $_POST['new_custom_meta']['value'][(int)$key];
        if(($name != '') && ($value != '')) {
					add_product_meta($_POST['prodid'], $name, $value, false, true);
        }
			}
		}
		
		
    if($_POST['custom_meta'] != null) {
      foreach((array)$_POST['custom_meta'] as $key => $values) {
        if(($values['name'] != '') && ($values['value'] != '')) {
          $wpdb->query("UPDATE `".$wpdb->prefix."wpsc_productmeta` SET `meta_key` = '".$wpdb->escape($values['name'])."', `meta_value` = '".$wpdb->escape($values['value'])."' WHERE `id` IN ('".(int)$key."')LIMIT 1 ;");
         // echo "UPDATE `".$wpdb->prefix."wpsc_productmeta` SET `meta_key` = '".$wpdb->escape($values['name'])."', `meta_value` = '".$wpdb->escape($values['value'])."' WHERE `id` IN ('".(int)$key."') LIMIT 1 ;";
					//add_product_meta($_POST['prodid'], $values['name'], $values['value'], false, true);
        }
			}
		}




    do_action('wpsc_product_form_submit', $product_id);
    
    /* Add or edit tidy url name */
    $tidied_name = trim($_POST['title']);
    $tidied_name = strtolower($tidied_name);
    $url_name = preg_replace(array("/(\s)+/","/[^\w-]+/i"), array("-", ''), $tidied_name);
    $similar_names = $wpdb->get_row("SELECT COUNT(*) AS `count`, MAX(REPLACE(`meta_value`, '$url_name', '')) AS `max_number` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN ('url_name') AND `meta_value` REGEXP '^($url_name){1}(\d)*$' ",ARRAY_A);
    $extension_number = '';
    if($similar_names['count'] > 0) {
      $extension_number = (int)$similar_names['max_number']+1;
		}
		
    $stored_name = get_product_meta($_POST['prodid'], 'url_name', true);
    if(get_product_meta($_POST['prodid'], 'url_name', true) != false) {
      $current_url_name = get_product_meta($_POST['prodid'], 'url_name');
      if($current_url_name[0] != $url_name) {
        $url_name .= $extension_number;
        update_product_meta($_POST['prodid'], 'url_name', $url_name);
			}
		} else {
      $url_name .= $extension_number;
      add_product_meta($_POST['prodid'], 'url_name', $url_name, true);
		}
    
    /* update thumbnail images */
    if(!($thumbnail_image == null && $_POST['image_resize'] == 3 && $_POST['current_thumbnail_image'] != null)) {
      if($thumbnail_image != null) {
        $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `thumbnail_image` = '".$thumbnail_image."' WHERE `id`='".$_POST['prodid']."' LIMIT 1");
			}
		}
      
		$image_resize = $_POST['image_resize'];
		if(!is_numeric($image_resize) || ($image_resize < 1)) {
			$image_resize = 0;
		}
      
    $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `thumbnail_state` = '".$image_resize."' WHERE `id`='".$_POST['prodid']."' LIMIT 1");
    
    if($_POST['deleteimage'] == 1) {
			$wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `image` = ''  WHERE `id`='".$_POST['prodid']."' LIMIT 1");
		}
     
		$variations_procesor = new nzshpcrt_variations;
		if($_POST['variation_values'] != null) {
			//$variations_procesor->add_to_existing_product($_POST['prodid'],$_POST['variation_values']);
		}
		
		if($_POST['edit_variation_values'] != null) {
			$variations_procesor->edit_product_values($_POST['prodid'],$_POST['edit_variation_values']);
		}
		
		if($_POST['edit_add_variation_values'] != null) {
			$variations_procesor->edit_add_product_values($_POST['prodid'],$_POST['edit_add_variation_values']);
		}
			
		if($_POST['variation_priceandstock'] != null) {
			$variations_procesor->update_variation_values($_POST['prodid'], $_POST['variation_priceandstock']);
		}     
		
		// send the pings out.
		wpsc_ping();
		
		echo "<div class='updated'><p align='center'>".TXT_WPSC_PRODUCTHASBEENEDITED."</p></div>";
	}
}

if(is_numeric($_GET['deleteid'])) { 
  $wpdb->query("DELETE FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `product_id` = '".$_GET['deleteid']."' AND `meta_key` IN ('url_name')");  
  $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET  `active` = '0' WHERE `id`='".$_GET['deleteid']."' LIMIT 1");
}



/*
 * Sort out the searching of the products
 */
if($_GET['search_products']) {
	$search_string_title = "%".$wpdb->escape(stripslashes($_GET['search_products']))."%";
	$search_string_description = "% ".$wpdb->escape(stripslashes($_GET['search_products']))."%";
	
	$search_sql = "AND (`".$wpdb->prefix."product_list`.`name` LIKE '".$search_string_title."' OR `".$wpdb->prefix."product_list`.`description` LIKE '".$search_string_description."')";
	
	$search_string = $_GET['search_products'];
} else {
  $search_sql = '';
  $search_string = '';
}



/*
 * Gets the product list, commented to make it stick out more, as it is hard to notice 
 */
if(is_numeric($_GET['catid'])) {    // if we are getting items from only one category, this is a monster SQL query to do this with the product order
  $sql = "SELECT `".$wpdb->prefix."product_list`.`id` , `".$wpdb->prefix."product_list`.`name` , `".$wpdb->prefix."product_list`.`price` , `".$wpdb->prefix."product_list`.`image`, `".$wpdb->prefix."item_category_associations`.`category_id`,`".$wpdb->prefix."product_order`.`order`, IF(ISNULL(`".$wpdb->prefix."product_order`.`order`), 0, 1) AS `order_state`
FROM `".$wpdb->prefix."product_list` 
LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` 
LEFT JOIN `".$wpdb->prefix."product_order` ON ( (
`".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."product_order`.`product_id` 
)
AND (
`".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_order`.`category_id` 
) ) 
WHERE `".$wpdb->prefix."product_list`.`active` = '1' $search_sql
AND `".$wpdb->prefix."item_category_associations`.`category_id` 
IN (
'".$_GET['catid']."'
)
ORDER BY `order_state` DESC,`".$wpdb->prefix."product_order`.`order` ASC,  `".$wpdb->prefix."product_list`.`id` DESC";

  } else {
		$itempp = 20;
		if ($_GET['pnum']!='all') {
			$page = (int)$_GET['pnum'];
			
			$start = $page * $itempp;
			$sql = "SELECT DISTINCT * FROM `{$wpdb->prefix}product_list` WHERE `active`='1' $search_sql LIMIT $start,$itempp";
		} else {
			$sql = "SELECT DISTINCT * FROM `{$wpdb->prefix}product_list` WHERE `active`='1' $search_sql";
		}
	}  
    
$product_list = $wpdb->get_results($sql,ARRAY_A);
$num_products = $wpdb->get_var("SELECT COUNT(DISTINCT `id`) FROM `".$wpdb->prefix."product_list` WHERE `active`='1' $search_sql");

/*
 * The product list is stored in $product_list now
 */
 
 /*
  * Detects if the directories for images, thumbnails and files are writeable, if they are not, tells the user to make them writeable.
 */
 
  $unwriteable_directories = Array();
  
  if(!is_writable(WPSC_FILE_DIR)) {
    $unwriteable_directories[] = WPSC_FILE_DIR;
	}
  
  if(!is_writable(WPSC_PREVIEW_DIR)) {
    $unwriteable_directories[] = WPSC_PREVIEW_DIR;
	}
 
  if(!is_writable(WPSC_IMAGE_DIR)) {
    $unwriteable_directories[] = WPSC_IMAGE_DIR;
	}
  
  if(!is_writable(WPSC_THUMBNAIL_DIR)) {
    $unwriteable_directories[] = WPSC_THUMBNAIL_DIR;
	}
  
  if(!is_writable(WPSC_CATEGORY_DIR)) {
    $unwriteable_directories[] = WPSC_CATEGORY_DIR;
	}
    
  if(count($unwriteable_directories) > 0)
    {
    echo "<div class='error'>".str_replace(":directory:","<ul><li>".implode($unwriteable_directories, "</li><li>")."</li></ul>",TXT_WPSC_WRONG_FILE_PERMS)."</div>";
    }
?>


<div class="wrap">
  <h2><?php echo TXT_WPSC_DISPLAYPRODUCTS;?></h2>

  <?php
  ?>


  <script language='javascript' type='text/javascript'>
function conf() {
  var check = confirm("<?php echo TXT_WPSC_SURETODELETEPRODUCT;?>");
  if(check) {
    return true;
  } else  {
    return false;
	}
}
<?php
if(is_numeric($_POST['prodid'])) {
		echo "filleditform(".$_POST['prodid'].");";
  }
else if(is_numeric($_GET['product_id'])) {
    echo "filleditform(".$_GET['product_id'].");";
  }
  
echo $display_added_product ;
?>
</script>
<div class="tablenav wpsc_products_nav">
	<div style="width: 500px;" class="alignleft">
		<a href='' onclick='return showaddform()' class='add_item_link'><img src='<?php echo WPSC_URL; ?>/images/package_add.png' alt='<?php echo TXT_WPSC_ADD; ?>' title='<?php echo TXT_WPSC_ADD; ?>' />&nbsp;<span><?php echo TXT_WPSC_ADDPRODUCT;?></span></a>
	</div>
	
	
	<div class="alignright">
		<?php echo setting_button(); ?>
		<a target="_blank" href='http://www.instinct.co.nz/e-commerce/products/' class='about_this_page'><span><?php echo TXT_WPSC_ABOUT_THIS_PAGE;?></span>&nbsp;</a>
	
	</div>

	
	<br class="clear"/>
</div>


  <?php
$num = 0;


echo "    <table id='productpage'>\n\r";
echo "      <tr><td style='padding: 0px;'>\n\r";
echo "        <table id='itemlist'>\n\r";
echo "          <tr class='firstrowth'>\n\r";
echo "            <td colspan='4' style='text-align: left;'>\n\r";
echo "<span id='loadingindicator_span' class='product_loadingindicator'><img id='loadingimage' src='".WPSC_URL."/images/grey-loader.gif' alt='Loading' title='Loading' /></span>";
echo "<strong class='form_group'>".TXT_WPSC_SELECT_PRODUCT."</strong>";
echo "            </td>\n\r";
echo "          </tr>\n\r";
if(($num_products > 20) || ($search_string != '')) {
	echo "          <tr class='selectcategory'>\n\r";
	echo "            <td colspan='3'>\n\r";
	echo TXT_WPSC_ADMIN_SEARCH_PRODUCTS.": ";
	echo "            </td>\n\r";
	echo "            <td colspan='1'>\n\r";
	echo "<div>\n\r";
	echo "  <form method='GET' action=''>\n\r";
	echo "<input type='hidden' value='{$_GET['page']}' name='page'>";
	echo "<input type='text' value='{$search_string}' name='search_products' style='width: 115px; padding: 1px;'>";
	echo "  </form>\n\r";
	echo "</div>\n\r";
	echo "            </td>\n\r";
	echo "          </tr>\n\r";
}


echo "          <tr class='selectcategory'>\n\r";
echo "            <td colspan='3'>\n\r";
echo TXT_WPSC_PLEASESELECTACATEGORY.": ";
echo "            </td>\n\r";
echo "            <td colspan='1'>\n\r";
echo "<div>\n\r";
echo topcategorylist();
//echo "<div style='float: right; width: 160px;'>". topcategorylist() ."</div>";
echo "</div>\n\r";

echo "            </td>\n\r";
echo "          </tr>\n\r";

if(is_numeric($_GET['catid'])) {
	$name_style = 'class="pli_name"';
	$price_style = 'class="pli_price"';
	$edit_style = 'class="pli_edit"';
} else {
	$name_style = '';
	$price_style = '';
	$edit_style = '';
}


echo "          <tr class='firstrow'>\n\r";

echo "            <td width='45px'>";
echo "</td>\n\r";

echo "            <td ".$name_style.">";
echo TXT_WPSC_NAME;
echo "</td>\n\r";

echo "            <td ".$price_style.">";
echo TXT_WPSC_PRICE;
echo "</td>\n\r";

if(!is_numeric($_GET['catid'])) {
	echo "            <td>";
	echo TXT_WPSC_CATEGORIES;
	echo "</td>\n\r";
}

echo "          </tr>\n\r";
if(is_numeric($_GET['catid'])) {
	echo "<tr><td colspan='4'  class='category_item_container'>\n\r";
} 

if($product_list != null)
  {
  $order_number = 0;
	if(is_numeric($_GET['catid'])){
	  echo "   <form><input type='hidden' name='category_id' id='item_list_category_id' value='".(int)$_GET['catid']."'/></form>";
    echo "   <div id='sort1' class='groupWrapper'>\n\r";
  }
  $tablei=1;
  foreach($product_list as $product)
    {
    /*
     * Creates order table entries if they are not already present
     * No need for extra database queries to determine the highest order number
     * anything without one is automatically at the bottom
     * so anything with an order number is already processed by the time it starts adding rows
     */
	if(is_numeric($_GET['catid'])){
		echo "    <div id='".$product['id']."' class='groupItem'>\n\r";
		//echo "    <div class='itemHeader'></div>\n\r";
		echo "    <div class='itemContent'>\n\r";
	} else {
		if ($tablei==1) {
			echo "<tr class='products'>";
		} else {
			echo "<tr class='productsalt'>";
		}
		$tablei*=-1;
	}
	
	if(is_numeric($_GET['catid'])) {
		if($product['order_state'] > 0) {
			if($product['order'] > $order_number) {
				$order_number = $product['order'];
				$order_number++;
			}      
		} else {
			$wpdb->query("INSERT INTO `".$wpdb->prefix."product_order` (  `category_id` , `product_id` , `order` ) VALUES ( '".$product['category_id']."', '".$product['id']."', '$order_number');");
			$order_number++;
		}
	}
	if(is_numeric($_GET['catid'])) {
    	echo "	<div class='itemHeader pli_img'>\n\r";
		echo "<a class='noline' title='Drag to a new position'>";
	} else {
		echo "	<td style='width: 40px;' class='imagecol'>\r\n";
	}
	
	if(($product['thumbnail_image'] != null) && file_exists(WPSC_THUMBNAIL_DIR.$product['thumbnail_image'])) { // check for custom thumbnail images
		echo "<img title='Drag to a new position' src='".WPSC_THUMBNAIL_URL.$product['thumbnail_image']."' title='".$product['name']."' alt='".$product['name']."' width='35' height='35'  />";
  } else if(($product['image'] != null) && file_exists(WPSC_THUMBNAIL_DIR.$product['image'])) { // check for automatic thumbnail images
		echo "<img title='Drag to a new position' src='".WPSC_THUMBNAIL_URL.$product['image']."' title='".$product['name']."' alt='".$product['name']."' width='35' height='35'  />";
	} else { // no image, display this fact
		echo "<img title='Drag to a new position' src='".WPSC_URL."/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' width='35' height='35' />";
	}

	echo "</a>";
  if(is_numeric($_GET['catid'])){ 
    echo "	</div>\n\r";
	} else {
	echo "</td><td width='25%'>";
	}
    
	if(is_numeric($_GET['catid'])) { 
    echo "            <div class='pli_name'>\n\r";
   }
   
	echo "<a href='#' onclick='filleditform(".$product['id'].");return false;'>";
	if ($product['name']=='') {
		echo "(".TXT_WPSC_NONAME.")";
	} else {
		echo htmlentities(stripslashes($product['name']), ENT_QUOTES, 'UTF-8');
	}
	echo "</a>";

	
	
	if(is_numeric($_GET['catid'])){
		echo "            </div>\n\r";    
	} else {
		echo "</td><td>";
	}
		if(is_numeric($_GET['catid'])){ 
			echo "            <div class='pli_price'>\n\r";
    }
    echo nzshpcrt_currency_display($product['price'], 1);
    if(is_numeric($_GET['catid'])){ 
			echo "            </div>\n\r";
    }
    
    if(!is_numeric($_GET['catid'])) {
			echo "            <td>\n\r";
	$category_list = $wpdb->get_results("SELECT `".$wpdb->prefix."product_categories`.`id`,`".$wpdb->prefix."product_categories`.`name` FROM `".$wpdb->prefix."item_category_associations` , `".$wpdb->prefix."product_categories` WHERE `".$wpdb->prefix."item_category_associations`.`product_id` IN ('".$product['id']."') AND `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_categories`.`id` AND `".$wpdb->prefix."product_categories`.`active` IN('1')",ARRAY_A);
			$i = 0;
			foreach((array)$category_list as $category_row) {
				if($i > 0) {
					echo "<br />";
				}
				echo "<a href='?page=".$_GET['page']."&amp;catid=".$category_row['id']."'>".stripslashes($category_row['name'])."</a>";
				$i++;
			}        
		}
		if(!is_numeric($_GET['catid'])){
			echo "</td>";
		}    
		
   // echo "<a href='#' title='sth' onclick='filleditform(".$product['id'].");return false;'>".TXT_WPSC_EDIT."</a>";
    echo "				</div>\n\r";
		echo "            </div>\n\r";
		if(!is_numeric($_GET['catid'])){
			echo "</tr>";
		}
	}
	echo "    </div>\n\r";
	echo "</td></tr>";
	if(is_numeric($_GET['catid'])){
		//echo "<tr><td>&nbsp;&nbsp;&nbsp;<a href='#' onClick='serialize();return false;'>".TXT_WPSC_SAVE_PRODUCT_ORDER."</a></td><td></td></tr>";
	} else {
		if (isset($itempp)) {
		$num_pages = floor($num_products/$itempp);
		}
		if (!isset($_GET['pnum'])) {
			$_GET['pnum']=0;
		}
		echo "<tr class='selectcategory' style='border: none;'><td style='text-align:right;' colspan='4' width='70%'>";
		
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pnum', '%#%' ),
			'format' => '',
			'total' => $num_pages,
			'current' => $_GET['pnum'],
			'end_size' => 2, // How many numbers on either end including the end
			'mid_size' => 2, // How many numbers to either side of current not including current
		));
		
			echo "<div class='tablenav-pages'>";
			
			echo $page_links;
			
// 		for ($i=0;$i<$num_pages;$i++) {
// 			$newpage=$_GET['pnum']+1;
// 			$pagenumber=$i+1;
// 			if (($i==$_GET['pnum']) && is_numeric($_GET['pnum'] )) {
// 				echo '<span class="page-numbers current">'.$pagenumber.'</span>';
// 			} else {
// 				echo "<a style='text-decoration:none;' class='page-numbers' href='?page=".$_GET['page']."&pnum=".$i."'>".$pagenumber."</a>";
// 			}
// 		}
// 		
		
		if (!isset($_GET['catid'])) {
			if ($_GET['pnum']==='all') {
				echo '<span class="page-numbers current">'.TXT_WPSC_SHOWALL.'</span>';
			} else {
				echo "<a style='text-decoration:none;' class='page-numbers' href='?page=".$_GET['page']."&pnum=all'>".TXT_WPSC_SHOWALL."</a>";
			}
			echo "</div>";
		}
		echo "</td></tr>";
	}
	
	
  }

echo "        </table>\n\r";
echo "      </td><td class='secondcol'>\n\r";
echo "        <div id='productform'>";
echo   "<div class='categorisation_title'><strong class='form_group'>". TXT_WPSC_PRODUCTDETAILS." <span>".TXT_WPSC_ENTERPRODUCTDETAILSHERE."</span></strong></div>";
echo "<form method='POST'  enctype='multipart/form-data' name='editproduct$num'>";
echo "        <table class='producttext'>\n\r";;    


echo "        </table>\n\r";
echo "        <div id='formcontent' style='width:100%;'>\n\r";
echo "        </div>\n\r";
echo "</form>";
echo "        </div>";
?>
<div id='additem'>
<div class="categorisation_title"><strong class="form_group"><?php echo TXT_WPSC_PRODUCTDETAILS;?> <span><?php echo TXT_WPSC_ENTERPRODUCTDETAILSHERE;?></span></strong></div>

  <form method='POST' enctype='multipart/form-data'>
  <table class='additem'>
    <tr>
      <td class='itemfirstcol'>
        <?php echo TXT_WPSC_PRODUCTNAME;?>:
      </td>
      <td class='itemformcol'>
      
        <div class='admin_product_name'>
					<input size='30' type='text' name='name' value='' class='text' />
					<a href='#' class='shorttag_toggle'></a>					
					<div class='admin_product_shorttags'>
					<?php echo TXT_WPSC_NO_SHORTCODE;?>
					</div>        
        </div>
        
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
        <?php echo TXT_WPSC_SKU;?>:
      </td>
      <td class='itemformcol'>
        <input size='30' type='text' name='productmeta_values[sku]' value='' class='text' />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
        <?php echo TXT_WPSC_PRODUCTDESCRIPTION;?>:
      </td>
      <td class='itemformcol'>
        <textarea name='description' cols='40' rows='8'></textarea><br />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
       <?php echo TXT_WPSC_ADDITIONALDESCRIPTION;?>:
      </td>
      <td class='itemformcol'>
        <textarea name='additional_description' cols='40' rows='8'></textarea><br />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
       <?php echo TXT_WPSC_PRODUCT_TAGS;?>:
      </td>
      <td class='itemformcol'>
        <input type='text' class='text' name='product_tag' id='product_tag'><br /><span class='small_italic'>Seperate with commas</span>
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
			<?php echo TXT_WPSC_CATEGORISATION; ?>
      </td>
      <td>
        <?php
         $categorisation_groups =  $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpsc_categorisation_groups` WHERE `active` IN ('1')", ARRAY_A);
					foreach($categorisation_groups as $categorisation_group){
					  $category_count = $wpdb->get_var("SELECT COUNT(*) FROM `{$wpdb->prefix}product_categories` WHERE `group_id` IN ('{$categorisation_group['id']}')");
					  if($category_count > 0) {
							echo "<p>";
						  $category_group_name = str_replace("[categorisation]", $categorisation_group['name'], TXT_WPSC_PRODUCT_CATEGORIES);
						  echo "<strong>".$category_group_name.":</strong><br>";
						  echo categorylist($categorisation_group['id'], false, 'add_');
						  echo "</p>";
						}						
					}
				?>
      </td>
    </tr>
   

   
    
<tr><td  colspan='2'><div id='price_and_stock' class='postbox'>
	<h3>
		<a class="togbox">+</a>
		<?php echo TXT_WPSC_PRICE_AND_STOCK_CONTROL;?>
	</h3>
    <div class='inside'>
    <table>
    <tr>
      <td>
       <?php echo TXT_WPSC_PRICE;?>:&nbsp;<input type='text' size='10' name='price' value='0.00' />
      </td>
    </tr>
    <tr>
       <td>
          <input id='add_form_tax' type='checkbox' name='notax' value='yes' />&nbsp;<label for='add_form_tax'><?php echo TXT_WPSC_TAXALREADYINCLUDED;?></label>
       </td>
    </tr>
    <tr>

       <td>
          <input id='add_form_donation' type='checkbox' name='donation' value='yes' />&nbsp;<label for='add_form_donation'><?php echo TXT_WPSC_IS_DONATION;?></label>
       </td>
    </tr>
    <tr>

       <td>
          <input id='add_form_no_shipping' type='checkbox' name='no_shipping' value='yes' />&nbsp;<label for='add_form_no_shipping'><?php echo TXT_WPSC_NO_SHIPPING;?></label>
       </td>
    </tr>
    <tr>
      <td>
        <input type="checkbox" onclick="hideelement('add_special')" value="yes" name="special" id="add_form_special"/>
        <label for="add_form_special"><?php echo TXT_WPSC_SPECIAL;?></label>
        <div style="display: none;" id="add_special">
          <input type="text" size="10" value="0.00" name="special_price"/>
        </div>
      </td>
    </tr>
    <tr>
      <td style='width:430px;'>
        <input id='add_form_quantity_limited' type="checkbox" onclick="hideelement('add_stock')" value="yes" name="quantity_limited"/>
      <label for='add_form_quantity_limited' class='small'><?php echo TXT_WPSC_UNTICKBOX;?></label>
        <div style="display: none;" id="add_stock">
          <input type='text' name='quantity' value='0' size='10' />
        </div>
      </td>
    </tr>
  </table></div></div></TD></tr>
  <?php   
  do_action('wpsc_product_form', array('product_id' => 0, 'state' => 'add'));
  ?>
    <tr>
    <td colspan="2">
	<div id='variation' class='postbox closed'>
        <h3>
		<a class="togbox">+</a>
		<?php echo TXT_WPSC_VARIATION_CONTROL; ?>
	</h3>
	<div class='inside'>
    <table>
    <tr>
      <td>
        <?php echo TXT_WPSC_ADD_VAR; ?>
      </td>
      <td>
        <?php echo variationslist(); ?>
        <div id='add_product_variations'>
		
        </div>
        <div id='add_product_variation_details'>
		
        </div>
      </td>
    </tr> 
	</table></div></div></td></tr>
    <tr>
      <td colspan='2'>
	      <div class='postbox closed' id='shipping'>
	     <h3>
		     <a class="togbox">+</a>
		     <?php echo TXT_WPSC_SHIPPING_DETAILS; ?>
		</h3>
      <div class='inside'>
  <table>
  
  	  <!--USPS shipping changes-->
	<tr>
		<td>
			<?php echo TXT_WPSC_WEIGHT; ?>
		</td>
		<td>
			<input type="text" size='5' name='weight' value=''>
			<select name='weight_unit'>
				<option value="pound">Pounds</option>
				<option value="once">Ounce</option>
			</select>
		</td>
    </tr>
    <!--USPS shipping changes ends-->

    <tr>
      <td>
      <?php echo TXT_WPSC_LOCAL_PNP; ?> 
      </td>
      <td>
        <input type='text' size='10' name='pnp' value='0.00' />
      </td>
    </tr>
  
    <tr>
      <td>
      <?php echo TXT_WPSC_INTERNATIONAL_PNP; ?>
      </td>
      <td>
        <input type='text' size='10' name='international_pnp' value='0.00' />
      </td>
    </tr>
    </table></div></div></td></tr>
    <tr><td colspan='2'><div id='advanced' class='postbox closed'>
	    <h3>
		    <a class="togbox">+</a>
		    <?php echo TXT_WPSC_ADVANCED_OPTIONS;?>
	    </h3>
	    <div class='inside'>
	    <table>
	    <tr>
      <td class='itemfirstcol'>
       <?php echo TXT_WPSC_ADMINNOTES;?>:
      </td>
      <td>
        <textarea cols="40" rows="3" type='text' name='merchant_notes' id='merchant_notes'></textarea> 
      </td>
    </tr>
	 <tr>
      <td class='itemfirstcol'>
      </td>
      <td>
        <input type="checkbox" value="yes" id="add_form_display_frontpage" name="display_frontpage"/> 
        <label for='add_form_display_frontpage'><?php echo TXT_WPSC_DISPLAY_FRONT_PAGE;?></label>
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
      </td>
      <td>
        <input type='checkbox' name="productmeta_values[engraved]" id='add_engrave_text'>
        <label for='add_engrave_text'><?php echo TXT_WPSC_ENGRAVE;?></label>
        <br />
      </td>
    </tr>
    <tr>
      <td class='itemfirstcol'>
      </td>
      <td>
        <input type='checkbox' name="productmeta_values[can_have_uploaded_image]" id='can_have_uploaded_image'>
        <label for='can_have_uploaded_image'><?php echo TXT_WPSC_ALLOW_UPLOADING_IMAGE;?></label>
        <br />
      </td>
    </tr>
	
    
    <?php if(get_option('payment_gateway') == 'google') { ?>
		<tr>
      <td class='itemfirstcol'>
      </td>
      <td>
        <input type='checkbox' name="productmeta_values['google_prohibited']" id='add_google_prohibited' /> <label for='add_google_prohibited'>
       <?php echo TXT_WPSC_PROHIBITED;?></label><br />
	Prohibited <a href='http://checkout.google.com/support/sell/bin/answer.py?answer=75724'>by Google?</a>
      </td>
    </tr>
    <?php } ?>
 <tr>
      <td class='itemfirstcol'>
       <?php echo TXT_WPSC_EXTERNALLINK;?>:
      </td>
      <td>
        <input type='text' class='text' name='external_link' id='external_link' size='40'> 
      </td>
    </tr>

     <tr>
	<td></td>
      <td>
      <?php echo TXT_WPSC_USEONLYEXTERNALLINK;?></strong>
      </td>
    </tr>
    
	<tr>
		<td>
			<?php echo TXT_WPSC_ADD_CUSTOM_FIELD;?>:
		</td>
		<td>
  <div class="product_custom_meta">
		<label >
		<?php echo TXT_WPSC_NAME;?>:
		<input type="text" name="new_custom_meta[name][]" value="" class="text"/>
		</label>
		
		<label>
		<?php echo TXT_WPSC_VALUE;?>:
		<input type="text" name="new_custom_meta[value][]" value="" class="text"/>
		
		</label>
		<a href='#' class='add_more_meta' onclick='return add_more_meta(this)'>+</a>
		<br />
  </div>
  
	</td>
</tr>

    
    
    </table></div></div></td></tr>
    <tr>
      <td colspan='2'>
        <div id='product_image' class='postbox'>
        <h3> 
		<a class="togbox">+</a>
		<?php echo TXT_WPSC_PRODUCTIMAGES;?>
	</h3>
	<div class='inside'>
	<table>
    <tr>
      <td>
        <?php echo TXT_WPSC_PRODUCTIMAGE;?>:
      </td>
      <td>
        <input type='file' name='image' value='' />
      </td>
    </tr>
    
    <tr>
      <td></td><td>
      <table>
  <?php
  // pe.{ & table opening above
  if(function_exists("getimagesize") && is_numeric(get_option('product_image_height')) && is_numeric(get_option('product_image_width')))
    {
    ?>
      <tr>
        <td>
      <input type='radio' name='image_resize' value='0' id='add_image_resize0' class='image_resize' onclick='hideOptionElement(null, "image_resize0");' /> <label for='add_image_resize0'><?php echo TXT_WPSC_DONOTRESIZEIMAGE; ?></label>
        </td>
      </tr>
      <tr>
        <td>
          <input type='radio' checked='true' name='image_resize' value='1' id='add_image_resize1' class='image_resize' onclick='hideOptionElement(null, "image_resize1");' /> <label for='add_image_resize1'><?php echo TXT_WPSC_USEDEFAULTSIZE;?> (<?php echo get_option('product_image_height') ."x".get_option('product_image_width'); ?>)</label>
        </td>
      </tr>
    <?php  
    $default_size_set = true;
    }
  
  if(function_exists("getimagesize"))
    {
    ?>
      <tr>
        <td>
          <input type='radio' name='image_resize' value='2'id='add_image_resize2' class='image_resize'  onclick='hideOptionElement("heightWidth", "image_resize2");' />
      <label for='add_image_resize2'><?php echo TXT_WPSC_USESPECIFICSIZE; ?> </label>        
          <div id='heightWidth' style='display: none;'>
        <input type='text' size='4' name='width' value='' /><label for='add_image_resize2'><?php echo TXT_WPSC_PXWIDTH;?></label>
        <input type='text' size='4' name='height' value='' /><label for='add_image_resize2'><?php echo TXT_WPSC_PXHEIGHT; ?> </label>
      </div>
        </td>
      </tr>
      <tr>
      <td>
        <input type='radio' name='image_resize' value='3' id='add_image_resize3' class='image_resize' onclick='hideOptionElement("browseThumb", "image_resize3");' />
        <label for='add_image_resize3'><?php echo TXT_WPSC_SEPARATETHUMBNAIL; ?></label><br />
        <div id='browseThumb' style='display: none;'>
          <input type='file' name='thumbnailImage' value='' />
        </div>
      </td>
    </tr>
    
    <?php
    }
    
    if(function_exists('add_multiple_image_form')) {
      echo add_multiple_image_form("add_");
      }
  ?>
        </table>
      </td>
    </tr>
    </table>
   </div></div></td></tr>
    
    <tr>
      <td colspan='2'>
        <div id='product_download' class='postbox closed'>
        <h3>
		<a class='togbox'>+</a>
		<?php echo TXT_WPSC_PRODUCTDOWNLOAD;?>
	</h3>
	<div class='inside'>
	<table>
    <tr>
      <td>
        <?php echo TXT_WPSC_DOWNLOADABLEPRODUCT;?>:
      </td>
      <td>
        <input type='file' name='file' value='' /><br />
        <?php echo wpsc_select_product_file(); ?>
        <br />
      </td>
    </tr>

<?php
if(function_exists("make_mp3_preview") || function_exists("wpsc_media_player"))
  {    
  echo "    <tr>\n\r";
  echo "      <td>\n\r";
  echo TXT_WPSC_PREVIEW_FILE.": ";
  echo "      </td>\n\r";
  echo "      <td>\n\r";
  echo "<input type='file' name='preview_file' value='' /><br />";
  //echo "<span class='admin_product_notes'>".TXT_WPSC_PREVIEW_FILE_NOTE."</span>";
  echo "<br />";
  echo "<br />";
  echo "      </td>\n\r";
  echo "    </tr>\n\r";
  }
    ?>
    </table></div></div></td></tr>
    <tr>
      <td>
      </td>
      <td>
      <br />
        <input type='hidden' name='submit_action' value='add' />
        <input class='button' type='submit' name='submit' value='<?php echo TXT_WPSC_ADD_PRODUCT;?>' />
      </td>
    </tr>
  </table>
  </form>
  </div>
<?php
echo "      </td></tr>\n\r";
echo "     </table>\n\r"

  ?>
</div>