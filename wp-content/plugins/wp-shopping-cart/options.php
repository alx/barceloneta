<?php
if(preg_match("/[a-zA-Z]{2,4}/",$_GET['isocode'])) {
  include('tax_and_shipping.php');
} else {
  if($_POST != null) {
    if($_POST['product_list_url'] != null) {
      update_option('product_list_url', $_POST['product_list_url']);
    }
  
    if($_POST['shopping_cart_url'] != null) {
      update_option('shopping_cart_url', $_POST['shopping_cart_url']);
    }
  
    if($_POST['checkout_url'] != null) {
      update_option('checkout_url', $_POST['checkout_url']);
    }
  
    if($_POST['transact_url'] != null) {
      update_option('transact_url', $_POST['transact_url']);
    }
  
    if($_POST['user_account_url'] != null) {
      update_option('user_account_url', $_POST['user_account_url']);
    }
  
    if($_POST['gst_rate'] != null) {
      $gst_rate = ($_POST['gst_rate']/100) + 1;
      update_option('gst_rate', $gst_rate);
    }
  
    if($_POST['wpsc_theme_list'] != '') {
      update_option('wpsc_selected_theme', $_POST['wpsc_theme_list']);
    }
  
    if($_POST['purch_log_email'] != null) {
      update_option('purch_log_email', $_POST['purch_log_email']);
    }
  
    if($_POST['return_email'] != null) {
      update_option('return_email', $_POST['return_email']);
    }
  
    if($_POST['terms_and_conditions'] != get_option('terms_and_conditions')) {
      update_option('terms_and_conditions', $_POST['terms_and_conditions']);
    }
  
    if($_POST['product_image_height'] != get_option('product_image_height')) {
      update_option('product_image_height', $_POST['product_image_height']);
    }
      
    if($_POST['product_image_width'] != get_option('product_image_width')) {
      update_option('product_image_width', $_POST['product_image_width']);
    }
  
    if($_POST['category_image_height'] != get_option('category_image_height')) {
      update_option('category_image_height', $_POST['category_image_height']);
    }
  
    if($_POST['category_image_width'] != get_option('category_image_width')) {
      update_option('category_image_width', $_POST['category_image_width']);
    }
  
    if($_POST['single_view_image_height'] != get_option('single_view_image_height')) {
      update_option('single_view_image_height', $_POST['single_view_image_height']);
    }
  
    if($_POST['single_view_image_width'] != get_option('single_view_image_width')) {
      update_option('single_view_image_width', $_POST['single_view_image_width']);
		}
  
    if(is_numeric($_POST['max_downloads'])) {
      update_option('max_downloads', $_POST['max_downloads']);
		}
  
    if(is_numeric($_POST['postage_and_packaging'])) {
      update_option('postage_and_packaging', $_POST['postage_and_packaging']);
		}
  
    if(is_numeric($_POST['currency_type'])) {
      update_option('currency_type', $_POST['currency_type']);
		}
  
    if(is_numeric($_POST['currency_sign_location'])) {
      update_option('currency_sign_location', $_POST['currency_sign_location']);
		}
  
    if(is_numeric($_POST['cart_location'])) {
      update_option('cart_location', $_POST['cart_location']);
		}

    if(is_numeric($_POST['show_gallery'])) {
      update_option('show_gallery', $_POST['show_gallery']);
		}

    // pe.{
    if(is_numeric($_POST['cat_brand_loc'])) {
      update_option('cat_brand_loc', $_POST['cat_brand_loc']);
		}

    if(is_numeric($_POST['show_categorybrands'])) {
      update_option('show_categorybrands', $_POST['show_categorybrands']);
		}
  
    if( isset($_POST['wpsc_default_category']) && ($_POST['wpsc_default_category'] != get_option('wpsc_default_category'))) {
      //echo $_POST['wpsc_default_category'];
     delete_option('wpsc_default_category');
      update_option('wpsc_default_category', (string)$_POST['wpsc_default_category']);
      //echo get_option('wpsc_default_category');
		}


    if($_POST['product_view'] != get_option('product_view')) {
      update_option('product_view', $_POST['product_view']);
		}

    if($_POST['show_thumbnails'] == 1) {
      update_option('show_thumbnails', 1);
		} else {
			update_option('show_thumbnails', 0);
		}
    
    if($_POST['wpsc_also_bought'] == 1) {
      update_option('wpsc_also_bought', 1);
		} else {
			update_option('wpsc_also_bought', 0);
		}


    if($_POST['show_category_thumbnails'] == 1) {
      update_option('show_category_thumbnails', 1);
		} else {
			update_option('show_category_thumbnails', 0);
		}
		
		if($_POST['addtocart_or_buynow'] == 1) {
      update_option('addtocart_or_buynow', 1);
		} else {
			update_option('addtocart_or_buynow', 0);
		}

		if($_POST['hide_name_link'] == 1) {
      update_option('hide_name_link', 1);
		} else {
			update_option('hide_name_link', 0);
		}
  
    if($_POST['display_pnp'] == 1) {
      update_option('display_pnp', 1);
		} else {
			update_option('display_pnp', 0);
		}
    
    if($_POST['wpsc_dropshop_display'] != get_option('dropshop_display')) {
      update_option('dropshop_display', $_POST['wpsc_dropshop_display']);
    }
    
    if($_POST['wpsc_dropshop_theme'] != get_option('wpsc_dropshop_theme')) {
      update_option('wpsc_dropshop_theme', $_POST['wpsc_dropshop_theme']);
    }
      
		if($_POST['hide_addtocart_button'] == 1) {
			update_option('hide_addtocart_button', 1);
		} else {
			update_option('hide_addtocart_button', 0);
		}
	
		
		if($_POST['checkbox_variations'] == 1) {
			update_option('checkbox_variations', 1);
		} else {
			update_option('checkbox_variations', 0);
		}
		
		if(isset($_POST['usps_user_id'])) {
			update_option('usps_user_id', $_POST['usps_user_id']);
		}
		
		if(isset($_POST['usps_user_password'])) {
			update_option('usps_user_password', $_POST['usps_user_password']);
		}

    if($_POST['use_pagination'] == 1) {
      update_option('use_pagination', $_POST['use_pagination']);
		} else {
			update_option('use_pagination', 0);
		}
  
    if(is_numeric($_POST['wpsc_products_per_page'])) {
      update_option('wpsc_products_per_page', $_POST['wpsc_products_per_page']);
		} else {
			update_option('wpsc_products_per_page', 0);
		}
  
    if($_POST['show_sliding_cart'] == 1) {
      update_option('show_sliding_cart', 1);
		} else {
			update_option('show_sliding_cart', 0);
		}
  
    if($_POST['fancy_notifications'] == 1) {
      update_option('fancy_notifications', 1);
		} else {
			update_option('fancy_notifications', 0);
		}
		
	if($_POST['add_plustax'] == 1) {
      update_option('add_plustax', 1);
		} else {
			update_option('add_plustax', 0);
		}
        
    // Adrian - used for displaying product count next to categories
    if($_POST['show_category_count'] == 1) {
      update_option('show_category_count', 1);
		} else {
			update_option('show_category_count', 0);
		}
    // Adrian - used for storing the category display type, just categories and multiple products per page, or a sliding menu with all products listed under each category and one (1) product per page    
    if($_POST['catsprods_display_type'] == 0) {
      update_option('catsprods_display_type', 0);
		} else {
			update_option('catsprods_display_type', 1);
		}
    
    if($_POST['require_register'] == 1) {
      update_option('require_register', 1);
		} else {
			update_option('require_register', 0);
		}
        
    
    if($_POST['do_not_use_shipping'] == 1) {
      update_option('do_not_use_shipping', 1);
		} else {
			update_option('do_not_use_shipping', 0);
		}
    // End of Adrian's additions - more further down page
    
    if(is_numeric($_POST['product_ratings'])) {
      update_option('product_ratings', $_POST['product_ratings']);
    }
        
    if(isset($_POST['language_setting'])) {
      update_option('language_setting', $_POST['language_setting']);
    }
      
    if(isset($_POST['base_local_shipping'])) {
      update_option('base_local_shipping', $_POST['base_local_shipping']);
    }
      
    if(isset($_POST['base_international_shipping'])) {
      update_option('base_international_shipping', $_POST['base_international_shipping']);
    }
      
    if(isset($_POST['base_country'])) {
      update_option('base_country', $_POST['base_country']);
    }
        
    if(is_numeric($_POST['country_id']) && is_numeric($_POST['country_tax'])) {
      $wpdb->query("UPDATE `".$wpdb->prefix."currency_list` SET `tax` = '".$_POST['country_tax']."' WHERE `id` = '".$_POST['country_id']."' LIMIT 1 ;");
    }
        
    if(isset($_POST['base_region'])) {
      update_option('base_region', $_POST['base_region']);
    }
	
		if(isset($_POST['usps_user_id'])) {
			update_option('usps_user_id', $_POST['usps_user_id']);
		}
      
    if(is_numeric($_POST['country_form_field'])) {
      update_option('country_form_field', $_POST['country_form_field']);
    }
      
    if(is_numeric($_POST['email_form_field'])) {
      update_option('email_form_field', $_POST['email_form_field']);
    }
    
    if($_POST['list_view_quantity'] == 1) {
      update_option('list_view_quantity', 1);
    } else {
      update_option('list_view_quantity', 0);
    }
    
    if($_POST['show_breadcrumbs'] == 1) {
      update_option('show_breadcrumbs', 1);
    } else {
      update_option('show_breadcrumbs', 0);
    }
    
    if($_POST['display_variations'] == 1) {
      update_option('display_variations', 1);
		} else {
			update_option('display_variations', 0);
    }
    
    if($_POST['show_images_only'] == 1) {
      update_option('show_images_only', 1);
		} else {
			update_option('show_images_only', 0);
    }
    
    if($_POST['show_search'] == 1) {
      update_option('show_search', 1);
		} else {
			update_option('show_search', 0);
		}
		
	if($_POST['show_advanced_search'] == 'on') {
		update_option('show_advanced_search', 1);
	} else {
		update_option('show_advanced_search', 0);
  }
  
	if($_POST['show_live_search'] == 'on') {
		update_option('show_live_search', 1);
	} else {
		update_option('show_live_search', 0);
	}

	if($_POST['googleStoreLocator'] == 1) {
		update_option('googleStoreLocator', 1);
	} else {
		update_option('googleStoreLocator', 0);
	}
    
	if($_POST['wpsc_category_description'] == 1) {
		update_option('wpsc_category_description', 'true');
	} else {
		update_option('wpsc_category_description', 'false');
	}
	
	if(is_numeric($_POST['wpsc_page_number_position'])) {
		update_option('wpsc_page_number_position', (int)$_POST['wpsc_page_number_position']);
	} else {
		update_option('wpsc_page_number_position', 1);
	}
	
	if($_POST['shipwire'] == 1) {
		update_option('shipwire', 1);
	} else {
	update_option('shipwire', 0);
	}
	
	if($_POST['wpsc_ip_lock_downloads'] == 1) {
		update_option('wpsc_ip_lock_downloads', 1);
	} else {
		update_option('wpsc_ip_lock_downloads', 0);
	}
	
	
	if($_POST['shipwireemail'] != null) {
		update_option('shipwireemail', $_POST['shipwireemail']);
	}
	
	if($_POST['shipwirepassword'] != null) {
		update_option('shipwirepassword', $_POST['shipwirepassword']);
	}

	
	
	echo "<div class='updated'><p align='center'>".TXT_WPSC_THANKSAPPLIED."</p></div>";
}
    
  if(get_option('nzshpcrt_first_load') == 0) {
    echo "<div class='updated'><p align='center'>".TXT_WPSC_INITIAL_SETUP."</p></div>";
    update_option('nzshpcrt_first_load', 1);
	}



if($_GET['update_page_urls'] == 'true') {
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
  foreach($wpsc_pageurl_option as $option_key => $page_string) {
    $post_id = $wpdb->get_var("SELECT `ID` FROM `".$wpdb->prefix."posts` WHERE `post_type` IN('page','post') AND `post_content` LIKE '%$page_string%' LIMIT 1");
    $the_new_link = get_permalink($post_id);
    if(stristr(get_option($option_key), "https://")) {
      $the_new_link = str_replace('http://', "https://",$the_new_link);
    }    
    update_option($option_key, $the_new_link);
	}
  if($changes_made === true)  {
    echo "<div class='updated'><p align='center'>".TXT_WPSC_THANKSAPPLIED."</p></div>";    
	}
}




if($_GET['clean_categories'] == 'true') {
  //exit("<pre>".print_r($check_category_names,true)."</pre>");
  $sql_query = "SELECT `id`, `name`, `active` FROM `".$wpdb->prefix."product_categories`";
	$sql_data = $wpdb->get_results($sql_query,ARRAY_A);
	foreach((array)$sql_data as $datarow) {
	
	  if($datarow['active'] == 1) {
	    $tidied_name = trim($datarow['name']);
			$tidied_name = strtolower($tidied_name);
			$url_name = preg_replace(array("/(\s)+/","/[^\w-]+/"), array("-", ''), $tidied_name);            
			$similar_names = $wpdb->get_row("SELECT COUNT(*) AS `count`, MAX(REPLACE(`nice-name`, '$url_name', '')) AS `max_number` FROM `{$wpdb->prefix}product_categories` WHERE `nice-name` REGEXP '^($url_name){1}(\d)*$' AND `id` NOT IN ('{$datarow['id']}') ",ARRAY_A);
			$extension_number = '';
			if($similar_names['count'] > 0) {
				$extension_number = (int)$similar_names['max_number']+2;
			}
			$url_name .= $extension_number;
			$wpdb->query("UPDATE `{$wpdb->prefix}product_categories` SET `nice-name` = '$url_name' WHERE `id` = '{$datarow['id']}' LIMIT 1 ;");
	  } else if($datarow['active'] == 0) {
		  $wpdb->query("UPDATE `{$wpdb->prefix}product_categories` SET `nice-name` = '' WHERE `id` = '{$datarow['id']}' LIMIT 1 ;");
	  }
	}
	$wp_rewrite->flush_rules();
}



  function options_categorylist() {
    global $wpdb;
    $current_default = get_option('wpsc_default_category');
    $group_sql = "SELECT * FROM `".$wpdb->prefix."wpsc_categorisation_groups` WHERE `active`='1'";
    $group_data = $wpdb->get_results($group_sql,ARRAY_A);
    $categorylist .= "<select name='wpsc_default_category'>";
    $categorylist .= "<option value='none' ".$selected." >".TXT_WPSC_SELECTACATEGORY."</option>";
    
		if(get_option('wpsc_default_category') == 'all')  {
				$selected = "selected='true'";
			}
    
    $categorylist .= "<option value='all' ".$selected." >".TXT_WPSC_SELECTALLCATEGORIES."</option>";
    foreach($group_data as $group) {
			$cat_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `group_id` IN ({$group['id']}) AND `active`='1'";
			$category_data = $wpdb->get_results($cat_sql,ARRAY_A);
			if($category_data != null) {
		    	  			
				
				$categorylist .= "<optgroup label='{$group['name']}'>";;
				foreach((array)$category_data as $category)  {
					if(get_option('wpsc_default_category') == $category['id'])  {
						$selected = "selected='true'";
					} else {
						$selected = "";
					}
					$categorylist .= "<option value='".$category['id']."' ".$selected." >".$category['name']."</option>";
				}
				$categorylist .= "</optgroup>";
			}
		}

    $categorylist .= "</select>";
    return $categorylist;
	}
    
  function country_list($selected_country = null) {
      global $wpdb;
      $output = "";
      $output .= "<option value=''></option>";
      $country_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."currency_list` ORDER BY `country` ASC",ARRAY_A);
      foreach ((array)$country_data as $country) {
        $selected ='';
        if($selected_country == $country['isocode']) {
          $selected = "selected='true'";
				}
        $output .= "<option value='".$country['isocode']."' $selected>".$country['country']."</option>";
			}
      return $output;
		}
  ?>
				<form name='cart_options' id='cart_options' method='POST' action='admin.php?page=<?php echo WPSC_DIR_NAME; ?>/options.php'>
					<div id="wpsc_options" class="wrap">
            <ul id="tabs">
                <li><a href="#options_general"><?php echo TXT_WPSC_OPTIONS_GENERAL_TAB; ?></a></li>
                <li><a href="#options_presentation"><?php echo TXT_WPSC_OPTIONS_PRESENTATION_TAB; ?></a></li>
								<li><a href="#options_shipping"><?php echo TXT_WPSC_OPTIONS_SHIPPING_TAB; ?></a></li>
								<!-- <li><a href="#wpsc_options_payment"><?php echo TXT_WPSC_OPTIONS_PAYMENT_TAB; ?></a></li> -->
                <li><a href="#options_admin"><?php echo TXT_WPSC_OPTIONS_ADMIN_TAB; ?></a></li>
								<!-- <li><a href="#wpsc_options_marketing"><?php echo TXT_WPSC_OPTIONS_MARKETING_TAB; ?></a></li> -->
            </ul>
            
            
						<div id="options_general">
						  <h2><?php echo TXT_WPSC_OPTIONS_GENERAL_HEADER; ?></h2>
						  <?php
						  /* here start the general options */						  
						  ?>
							<table class='wpsc_options form-table'>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_BASE_COUNTRY;?>:
									</th>
									<td>
									<select name='base_country' onChange='submit_change_country();'>
									<?php echo country_list(get_option('base_country')); ?>
									</select>
									<span id='options_region'>
									<?php
									$region_list = $wpdb->get_results("SELECT `".$wpdb->prefix."region_tax`.* FROM `".$wpdb->prefix."region_tax`, `".$wpdb->prefix."currency_list`  WHERE `".$wpdb->prefix."currency_list`.`isocode` IN('".get_option('base_country')."') AND `".$wpdb->prefix."currency_list`.`id` = `".$wpdb->prefix."region_tax`.`country_id`",ARRAY_A) ;
									if($region_list != null) {
										echo "<select name='base_region'>\n\r";
										foreach($region_list as $region) {
											if(get_option('base_region')  == $region['id']) {
												$selected = "selected='true'";
											} else {
												$selected = "";
											}
											echo "<option value='".$region['id']."' $selected>".$region['name']."</option>\n\r";
										}
										echo "</select>\n\r";    
									}
									?>
									</span>
									</td>
								</tr>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_TAX_SETTINGS;?>:
									</th>
									<td>
									<span id='options_region'>
									<?php
									$country_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."currency_list` WHERE `isocode`='".get_option('base_country')."' LIMIT 1",ARRAY_A);
									echo $country_data['country'];
									
									$region_count = $wpdb->get_var("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."region_tax`, `".$wpdb->prefix."currency_list`  WHERE `".$wpdb->prefix."currency_list`.`isocode` IN('".get_option('base_country')."') AND `".$wpdb->prefix."currency_list`.`id` = `".$wpdb->prefix."region_tax`.`country_id`") ;
									
									if($country_data['has_regions'] == 1) {
										echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href='?page=".WPSC_DIR_NAME."/options.php&isocode=".get_option('base_country')."'>".$region_count." Regions</a>";
									} else {
										echo "<input type='hidden' name='country_id' value='".$country_data['id']."'>";
										echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type='text' name='country_tax' class='tax_forms' maxlength='5' size='5' value='".$country_data['tax']."'>%";
									}
									?>
									</span>
									</td>
								</tr>
								
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_LANGUAGE;?>:
									</th>
									<td>
									<select name='language_setting'>
									<?php
									if(get_option('language_setting') != '') {
										$language_setting = get_option('language_setting');
									} else {
										$language_setting = "EN_en.php";
									}
									$languages_directory = WPSC_FILE_PATH.'/languages';
									$language_files = nzshpcrt_listdir($languages_directory);
									//echo "<pre>".print_r($language_files,true)."</pre>";
									foreach($language_files as $language_file) {
										switch($language_file) {
											case "EN_en.php";
											$language = "English";
											break;
											
											case "DE_de.php";
											$language = "Deutsch";
											break;
											
											case "FR_fr.php";
											$language = "Français";
											break;
											
											case "IT_it.php";
											$language = "Italian";
											break;
											
											case "BG_bg.php";
											$language = 'български';
											break;
											
											case "JP_jp.php";
											$language = "日本語";
											break;
											
											case "pt_BR.php";
											$language = "Brazilian Portuguese";
											break;
																
											case "RU_ru.php";
											$language = "Russian";
											break;
											
											case "SP_sp.php";
											$language = "Spanish";
											break;
											
											case "HU_hu.php";
											$language = "Hungarian";
											break;
											
											case "SV_sv.php";
											$language = "Swedish";
											break;
															
											case "TR_tr.php";
											$language = "Türkçe";
											break; 
						
											case "EL_el.php";
											$language = "Ελληνικά";
											break;
						
											case "KO_ko.php";
											$language = "Korean";
											break;
											
											case "ZH_zh.php";
											$language = "Chinese";
											break;
											
											case "DK_da.php";
											$language = "Danish";
											break;
											
											case "DK_da.php";
											$language = "Danish";
											break;
											
											case "nn_NO.php";
											$language = "Norwegian";
											break;
																	
											default:
											continue 2;
											break;
										}
										if($language_setting == $language_file) {
											echo "<option selected='true' value='".$language_file."'>".$language."</option>";
										} else {
											echo "<option value='".$language_file."'>".$language."</option>";            
										}
									}
									?>
									</select>
									</td>
								</tr>
								
					
								<tr>      
									<th scope="row">
									<?php echo TXT_WPSC_HIDEADDTOCARTBUTTON;?>:
									</th>
									<td>
									<?php
								$hide_addtocart_button = get_option('hide_addtocart_button');
								$hide_addtocart_button1 = "";
								$hide_addtocart_button2 = "";
								switch($hide_addtocart_button) {
									case 0:
									$hide_addtocart_button2 = "checked ='checked'";
									break;
									
									case 1:
									$hide_addtocart_button1 = "checked ='checked'";
									break;
								}
						
									?>
									<input type='radio' value='1' name='hide_addtocart_button' id='hide_addtocart_button1' <?php echo $hide_addtocart_button1; ?> /> <label for='hide_addtocart_button1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='hide_addtocart_button' id='hide_addtocart_button2' <?php echo $hide_addtocart_button2; ?> /> <label for='hide_addtocart_button2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								<tr>      
									<th scope="row">
									<?php echo TXT_WPSC_HIDEADDNAMELINK;?>:
									</th>
									<td>
									<?php
								$hide_name_link = get_option('hide_name_link');
								$hide_name_link1 = "";
								$hide_name_link2 = "";
								switch($hide_name_link) {
									case 0:
									$hide_name_link2 = "checked ='checked'";
									break;
									
									case 1:
									$hide_name_link1 = "checked ='checked'";
									break;
								}
						
									?>
									<input type='radio' value='1' name='hide_name_link' id='hide_name_link1' <?php echo $hide_name_link1; ?> /> <label for='hide_name_link1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='hide_name_link' id='hide_name_link2' <?php echo $hide_name_link2; ?> /> <label for='hide_name_link2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_BUTTONTYPE;?>:
									</th>
									<td>
									<?php
								$addtocart_or_buynow = get_option('addtocart_or_buynow');
								$addtocart_or_buynow1 = "";
								$addtocart_or_buynow2 = "";
								switch($addtocart_or_buynow) {
									case 0:
									$addtocart_or_buynow1 = "checked ='checked'";
									break;
									
									case 1:
									$addtocart_or_buynow2 = "checked ='checked'";
									break;
								}
						
									?>
									<input type='radio' value='0' name='addtocart_or_buynow' id='addtocart_or_buynow1' <?php echo $addtocart_or_buynow1; ?> /> <label for='addtocart_or_buynow1'><?php echo TXT_WPSC_ADDTOCART;?></label> &nbsp;
									<input type='radio' value='1' name='addtocart_or_buynow' id='addtocart_or_buynow2' <?php echo $addtocart_or_buynow2; ?> /> <label for='addtocart_or_buynow2'><?php echo TXT_WPSC_BUYNOW;?></label>
									</td>
								</tr>
							</table> 
							
							<h3 class="form_group"><?php echo TXT_WPSC_CURRENCYSETTINGS;?>:</h3>
							<table class='wpsc_options form-table'>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_CURRENCYTYPE;?>:
									</th>
									<td>
									<select name='currency_type' onChange='getcurrency(this.options[this.selectedIndex].value);'>
									<?php
									
									$currency_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."currency_list` ORDER BY `country` ASC",ARRAY_A);
									foreach($currency_data as $currency) {
										if(get_option('currency_type') == $currency['id']) {
											$selected = "selected='true'";
										} else {
											$selected = "";
										}
										echo "        <option value='".$currency['id']."' ".$selected." >".$currency['country']." (".$currency['currency'].")</option>";
									}
									
									$currency_data = $wpdb->get_row("SELECT `symbol`,`symbol_html`,`code` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".get_option('currency_type')."' LIMIT 1",ARRAY_A) ;
									if($currency_data['symbol'] != '') {
										$currency_sign = $currency_data['symbol_html'];
									} else {
										$currency_sign = $currency_data['code'];
									}
									?>
									</select>
									</td>
								</tr>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_CURRENCYSIGNLOCATION;?>:
									</th>
									<td>
									<?php
									$currency_sign_location = get_option('currency_sign_location');
									$csl1 = "";
									$csl2 = "";
									$csl3 = "";
									$csl4 = "";
									switch($currency_sign_location) {
										case 1:
										$csl1 = "checked ='true'";
										break;
									
										case 2:
										$csl2 = "checked ='true'";
										break;
									
										case 3:
										$csl3 = "checked ='true'";
										break;
									
										case 4:
										$csl4 = "checked ='true'";
										break;
									}
									?>
									<input type='radio' value='1' name='currency_sign_location' id='csl1' <?php echo $csl1; ?> /> <span for='csl1'>100<span id=cslchar1><?php echo $currency_sign; ?></span></label> &nbsp;
									<input type='radio' value='2' name='currency_sign_location' id='csl2' <?php echo $csl2; ?> /> <label for='csl2'>100 <span id=cslchar2><?php echo $currency_sign; ?></span></label> &nbsp;
									<input type='radio' value='3' name='currency_sign_location' id='csl3' <?php echo $csl3; ?> /> <label for='csl3'><span id=cslchar3><?php echo $currency_sign; ?></span>100</label> &nbsp;
									<input type='radio' value='4' name='currency_sign_location' id='csl4' <?php echo $csl4; ?> /> <label for='csl4'><span id=cslchar4><?php echo $currency_sign; ?></span> 100</label>
									</td>
								</tr>
							</table> 
							<?php
						  /* here end the general options */						  
						  ?>
							<div class="submit">
								<input type="submit" value="Update »" name="updateoption"/>
							</div>
						</div>
						
						
						
						
						<div id="options_presentation">
						  <h2><?php echo TXT_WPSC_OPTIONS_PRESENTATION_HEADER; ?></h2>
						  
							<?php
						  /* here start the presentation options */						  
						  ?>
						  
							<table class='wpsc_options form-table'>		
							<?php
							// if(function_exists('product_display_list') || function_exists('product_display_grid')) {
								?>    
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_PRODUCT_DISPLAY;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('product_view');
									$product_view1 = null;
									$product_view2 = null;
									$product_view3 = null;
									switch($display_pnp) {
										case "grid":
										if(function_exists('product_display_grid')) {
											$product_view3 = "selected ='true'";
											break;
										}
										
										case "list":
										if(function_exists('product_display_list')) {
											$product_view2 = "selected ='true'";
											break;
										}
										
										default:
										$product_view1 = "selected ='true'";
										break;
									}
									
									if(get_option('list_view_quantity') == 1) {
										$list_view_quantity_value = "checked='true'";
									} else {
										$list_view_quantity_value = '';
									}
						
									if(get_option('show_images_only') == 1) {
										$show_images_only_value = "checked='true'";
									} else {
										$show_images_only_value = '';
									}
									if(get_option('display_variations') == 1) {
										$display_variations = "checked='true'";
									} else {
										$display_variations = '';
									}
									?>
									<select name='product_view' onchange="toggle_display_options(this.options[this.selectedIndex].value)">
									<option value='default' <?php echo $product_view1; ?>><?php echo TXT_WPSC_DEFAULT;?></option>
									<?php
									if(function_exists('product_display_list')) {
										?>
										<option value='list' <?php echo $product_view2; ?>><?php echo TXT_WPSC_LIST;?></option>
										<?php      
									}  else {
										?>
										<option value='list' disabled='disabled' <?php echo $product_view2; ?>><?php echo TXT_WPSC_LIST;?></option>
										<?php      
									  
									}
									
									if(function_exists('product_display_grid')) {
										?>
									<option value='grid' <?php echo $product_view3; ?>><?php echo TXT_WPSC_GRID;?></option>
										<?php   
									} else {
										?>
									<option value='grid' disabled='disabled' <?php echo $product_view3; ?>><?php echo TXT_WPSC_GRID;?></option>
										<?php 
									}
									?>
									</select>
									<?php 
									if(!function_exists('product_display_grid')) {
									?><a href='http://www.instinct.co.nz/e-commerce/shop/'><?php echo TXT_WPSC_PURCHASE_UNAVAILABLE; ?></a> <?php 
									}
									?>
										<div id='list_view_options' <?php if(is_null($product_view2)) { echo "style='display:none;'";} ?> >
											<input type='checkbox' value='1' name='list_view_quantity' id='list_view_quantity' <?php echo $list_view_quantity_value;?> />
											<label for='list_view_options'><?php echo TXT_WPSC_ADJUSTABLE_QUANTITY;?></label>
										</div>
										<div id='grid_view_options' <?php echo $list_view_quantity_style;?> <?php if(is_null($product_view3)) { echo "style='display:none;'";} ?>>
											<input type='checkbox' value='1' name='show_images_only' id='show_images_only' <?php echo $show_images_only_value;?> />
											<label for='show_images_only'><?php echo TXT_SHOW_IMAGES_ONLY;?></label><br />
											<input type='checkbox' value='1' name='display_variations' id='display_variations' <?php echo $display_variations;?> />
											<label for='display_variations'><?php echo TXT_DISPLAY_VARIATIONS;?></label>
										</div>
									</td>
								</tr>
								<?php
								//  }
								?>			
													
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SELECT_THEME;?>:
									</th>
									<td>
									<?php
									echo wpsc_list_product_themes();
									?>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_CARTLOCATION;?>:
									</th>
									<td>
									<?php
									$cart_location = get_option('cart_location');
									$cart1 = "";
									$cart2 = "";
									switch($cart_location) {
										case 1:
										$cart1 = "checked ='true'";
										break;
										
										case 2:
										$cart2 = "checked ='true'";
										break;
										
										case 3:
										$cart3 = "checked ='true'";
										break;
										
										case 4:
										$cart4 = "checked ='true'";
										break;
										
										case 5:
										$cart5 = "checked ='true'";
										break;
									} 
									if(function_exists('register_sidebar_widget')) {
										?>
										<input type='radio' value='1' onclick='hideelement1("dropshop_option", this.value)' disabled='true'  name='cart_location' id='cart1' <?php echo $cart1; ?> /> <label style='color: #666666;' for='cart1'><?php echo TXT_WPSC_SIDEBAR;?></label> &nbsp;
										<?php
									} else {
										?>
										<input type='radio' value='1' name='cart_location' id='cart1' <?php echo $cart1; ?> /> <label for='cart1'><?php echo TXT_WPSC_SIDEBAR;?></label> &nbsp;
										<?php
									}
									?>
									<input type='radio' onclick='hideelement1("dropshop_option", this.value)' value='2' name='cart_location' id='cart2' <?php echo $cart2; ?> /> <label for='cart2'><?php echo TXT_WPSC_PAGE;?></label> &nbsp;
									<?php
									if(function_exists('register_sidebar_widget')) {
										?>
										<input type='radio' value='4' onclick='hideelement1("dropshop_option", this.value)' name='cart_location' id='cart4' <?php echo $cart4; ?> /> <label for='cart4'><?php echo TXT_WPSC_WIDGET;?></label> &nbsp;
										<?php
									} else {
										?>
										<input type='radio'  disabled='true' value='4' name='cart_location' id='cart4' alt='<?php echo TXT_WPSC_NEEDTOENABLEWIDGET;?>' title='<?php echo TXT_WPSC_NEEDTOENABLEWIDGET;?>' <?php echo $cart4; ?> /> <label style='color: #666666;' for='cart4' title='<?php echo TXT_WPSC_NEEDTOENABLEWIDGET;?>'><?php echo TXT_WPSC_WIDGET;?></label> &nbsp;
										<?php
									}
									
									if(function_exists('drag_and_drop_cart')) {
										?>
										<input type='radio' onclick='hideelement1("dropshop_option", this.value)' value='5' name='cart_location' id='cart5' <?php echo $cart5; ?> /> <label for='cart5'><?php echo TXT_WPSC_GOLD_DROPSHOP;?></label> &nbsp;
										<?php
									} else {
										?>
										<input type='radio' disabled='true' value='5' name='cart_location' id='cart5' alt='<?php echo TXT_WPSC_NEEDTOENABLEWIDGET;?>' title='<?php echo TXT_WPSC_NEEDTOENABLEDROPSHOP;?>' <?php echo $cart5; ?> /> <label style='color: #666666;' for='cart5' title='<?php echo TXT_WPSC_NEEDTOENABLEDROPSHOP;?>'><?php echo TXT_WPSC_GOLD_DROPSHOP;?></label> &nbsp;
										<?php
									}
										?>
									<input type='radio' onclick='hideelement1("dropshop_option", this.value)' value='3' name='cart_location' id='cart3' <?php echo $cart3; ?> /> <label for='cart3'><?php echo TXT_WPSC_MANUAL;?> <span style='font-size: 7pt;'>(PHP code: &lt;?php echo nzshpcrt_shopping_basket(); ?&gt; )</span></label>
							<div  style='display: <?php if (isset($cart5)) { echo "block"; } else { echo "none"; } ?>;'  id='dropshop_option'>
							<p>
							<input type="radio" id="drop1" value="all" <?php if (get_option('dropshop_display') == 'all') { echo "checked='checked'"; } ?> name="wpsc_dropshop_display" /><label for="drop1"><?php echo TXT_WPSC_SHOW_DROPSHOP_ALL;?></label>
							<input type="radio" id="drop2" value="product" <?php if (get_option('dropshop_display') == 'product') { echo "checked='checked'"; } ?> name="wpsc_dropshop_display"/><label for="drop2"><?php echo TXT_WPSC_SHOW_DROPSHOP_PRODUCT;?></label>
							</p>
							<p>
							<input type="radio" id="wpsc_dropshop_theme1" value="light" <?php if (get_option('wpsc_dropshop_theme') != 'dark') { echo "checked='checked'"; } ?> name="wpsc_dropshop_theme" /><label for="wpsc_dropshop_theme1"><?php echo TXT_WPSC_DROPSHOP_LIGHT;?></label>
							<input type="radio" id="wpsc_dropshop_theme2" value="dark" <?php if (get_option('wpsc_dropshop_theme') == 'dark') { echo "checked='checked'"; } ?> name="wpsc_dropshop_theme"/><label for="wpsc_dropshop_theme2"><?php echo TXT_WPSC_DROPSHOP_DARK;?></label>
							
							</p>
							</div>
									</td>
								</tr>
 
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_DEFAULTCATEGORY; ?>:
									</th>
									<td>
									<?php echo options_categorylist(); ?>
									</td>
								</tr>
								
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOW_CATEGORY_DESCRIPTION;?>:
									</th>
									<td>
									<?php
									$wpsc_category_description = get_option('wpsc_category_description');
									$wpsc_category_description1 = "";
									$wpsc_category_description2 = "";
									switch($wpsc_category_description) {    
										case 'true':
										$wpsc_category_description1 = "checked ='true'";
										break;
										
										case 'false':
										default:
										$wpsc_category_description2 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='wpsc_category_description' id='wpsc_category_description1' <?php echo $wpsc_category_description1; ?> /> <label for='wpsc_category_description1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='wpsc_category_description' id='wpsc_category_description2' <?php echo $wpsc_category_description2; ?> /> <label for='wpsc_category_description2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								
								
							<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOWPOSTAGEANDPACKAGING;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('display_pnp');
									$display_pnp1 = "";
									$display_pnp2 = "";
									switch($display_pnp) {
										case 0:
										$display_pnp2 = "checked ='true'";
										break;
										
										case 1:
										$display_pnp1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='display_pnp' id='display_pnp1' <?php echo $display_pnp1; ?> /> <label for='display_pnp1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='display_pnp' id='display_pnp2' <?php echo $display_pnp2; ?> /> <label for='display_pnp2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOW_BREADCRUMBS;?>:
									</th>
									<td>
									<?php
									$show_breadcrumbs = get_option('show_breadcrumbs');
									$show_breadcrumbs1 = "";
									$show_breadcrumbs2 = "";
									switch($show_breadcrumbs) {
										case 0:
										$show_breadcrumbs2 = "checked ='true'";
										break;
										
										case 1:
										$show_breadcrumbs1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='show_breadcrumbs' id='show_breadcrumbs1' <?php echo $show_breadcrumbs1; ?> /> <label for='show_breadcrumbs1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='show_breadcrumbs' id='show_breadcrumbs2' <?php echo $show_breadcrumbs2; ?> /> <label for='show_breadcrumbs2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>							
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOWPRODUCTRATINGS;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('product_ratings');
									$product_ratings1 = "";
									$product_ratings2 = "";
									switch($display_pnp) {
										case 0:
										$product_ratings2 = "checked ='true'";
										break;
										
										case 1:
										$product_ratings1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='product_ratings' id='product_ratings1' <?php echo $product_ratings1; ?> /> <label for='product_ratings1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='product_ratings' id='product_ratings2' <?php echo $product_ratings2; ?> /> <label for='product_ratings2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOW_SLIDING_CART;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('show_sliding_cart');
									$show_sliding_cart1 = "";
									$show_sliding_cart2 = "";
									switch($display_pnp) {
										case 0:
										$show_sliding_cart2 = "checked ='true'";
										break;
										
										case 1:
										$show_sliding_cart1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='show_sliding_cart' id='show_sliding_cart1' <?php echo $show_sliding_cart1; ?> /> <label for='show_sliding_cart1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='show_sliding_cart' id='show_sliding_cart2' <?php echo $show_sliding_cart2; ?> /> <label for='show_sliding_cart2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
					<!-- // Adrian - options for displaying number of products per category -->      
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOW_CATEGORY_COUNT;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('show_category_count');
									$show_category_count1 = "";
									$show_category_count2 = "";
									switch($display_pnp) {
										case 0:
										$show_category_count2 = "checked ='true'";
										break;
										
										case 1:
										$show_category_count1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='show_category_count' id='show_category_count1' <?php echo $show_category_count1; ?> /> <label for='show_category_count1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='show_category_count' id='show_category_count2' <?php echo $show_category_count2; ?> /> <label for='show_category_count2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								
					<!-- // Adrian - options for displaying category display type -->      
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_CATSPRODS_DISPLAY_TYPE;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('catsprods_display_type');
									$catsprods_display_type1 = "";
									$catsprods_display_type2 = "";
									switch($display_pnp) {
										case 0:
										$catsprods_display_type1 = "checked ='true'";
										break;
										
										case 1:
										$catsprods_display_type2 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='0' name='catsprods_display_type' id='catsprods_display_type1' <?php echo $catsprods_display_type1; ?> /> <label for='catsprods_display_type1'><?php echo TXT_WPSC_CATSPRODS_TYPE_CATONLY;?></label> &nbsp;
									<input type='radio' value='1' name='catsprods_display_type' id='catsprods_display_type2' <?php echo $catsprods_display_type2; ?> /> <label for='catsprods_display_type2'><?php echo TXT_WPSC_CATSPRODS_TYPE_SLIDEPRODS;?></label>
									</td>
								</tr>
								
					<?php
					if(function_exists('gold_shpcrt_search_form')) {
						?>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOW_SEARCH;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('show_search');
									$show_search1 = "";
									$show_search2 = "";
									switch($display_pnp) {
										case 0:
										$show_search2 = "checked ='true'";
										break;
										
										case 1:
										$show_search1 = "checked ='true'";
										break;
									}
								
									$display_ad_pnp = get_option('show_advanced_search');
									$show_advanced_search = "";
									if($display_ad_pnp == 1) {
										$show_advanced_search = "checked ='true'";
									}
								
									$display_live_pnp = get_option('show_live_search');
									if($display_ad_pnp == 1) {
										$show_live_search = "checked ='true'";
									}
								
									if ($show_search1 != "checked ='true'") {
										$dis = "style='display:none;'";
									}
									?>
									<input type='radio' onclick='jQuery("#wpsc_advanced_search").show()' value='1' name='show_search' id='show_search1' <?php echo $show_search1; ?> /> <label for='show_search1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' onclick='jQuery("#wpsc_advanced_search").hide()' value='0' name='show_search' id='show_search2' <?php echo $show_search2; ?> /> <label for='show_search2'><?php echo TXT_WPSC_NO;?></label>
									
								<div <?=$dis?> id='wpsc_advanced_search'>
									<input  type='checkbox' name='show_advanced_search' id='show_advanced_search' <?php echo $show_advanced_search; ?> />
									<?php echo TXT_WPSC_SHOWADVANCEDSEARCH;?><br>
									<input type='checkbox' name='show_live_search' id='show_live_search' <?php echo $show_live_search; ?> />
									<?php echo TXT_WPSC_SHOWLIVESEARCH;?>
								</div>
								
									</td>
								</tr>
						<?php
						}
					if(function_exists('gold_shpcrt_display_gallery')) {
						?>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOW_GALLERY;?>:
									</th>
									<td>
									<?php
									$display_pnp = get_option('show_gallery');
									$show_gallery1 = "";
									$show_gallery2 = "";
									switch($display_pnp) {
										case 0:
										$show_gallery2 = "checked ='true'";
										break;
										
										case 1:
										$show_gallery1 = "checked ='true'";
										break;
									}
									?>
									<input type='radio' value='1' name='show_gallery' id='show_gallery1' <?php echo $show_gallery1; ?> /> <label for='show_gallery1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='show_gallery' id='show_gallery2' <?php echo $show_gallery2; ?> /> <label for='show_gallery2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
						<?php
						}
					?>
					
							<tr>
								<th scope="row">
								<?php echo TXT_WPSC_DISPLAY_FANCY_NOTIFICATIONS;?>:
								</th>
								<td>
								<?php
					$fancy_notifications = get_option('fancy_notifications');
					$fancy_notifications1 = "";
					$fancy_notifications2 = "";
					switch($fancy_notifications)
						{
						case 0:
						$fancy_notifications2 = "checked ='true'";
						break;
						
						case 1:
						$fancy_notifications1 = "checked ='true'";
						break;
						}
								?>
								<input type='radio' value='1' name='fancy_notifications' id='fancy_notifications1' <?php echo $fancy_notifications1; ?> /> <label for='fancy_notifications1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
								<input type='radio' value='0' name='fancy_notifications' id='fancy_notifications2' <?php echo $fancy_notifications2; ?> /> <label for='fancy_notifications2'><?php echo TXT_WPSC_NO;?></label>
								</td>
							</tr>  
					
							<tr>
								<th scope="row">
								<?php echo TXT_WPSC_DISPLAY_PLUSTAX;?>:
								</th>
								<td>
								<?php
								$add_plustax = get_option('add_plustax');
								$add_plustax1 = "";
								$add_plustax2 = "";
								switch($add_plustax) {
									case 0:
									$add_plustax2 = "checked ='true'";
									break;
									
									case 1:
									$add_plustax1 = "checked ='true'";
									break;
								}
								?>
								<input type='radio' value='1' name='add_plustax' id='add_plustax1' <?php echo $add_plustax1; ?> /> <label for='add_plustax1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
								<input type='radio' value='0' name='add_plustax' id='add_plustax2' <?php echo $add_plustax2; ?> /> <label for='add_plustax2'><?php echo TXT_WPSC_NO;?></label>
								</td>
							</tr>
					
							</table> 
							
							
							<h3 class="form_group"><?php echo TXT_WPSC_THUMBNAILSETTINGS;?></h3>
							<table class='wpsc_options form-table'>
							<?php
								if(function_exists("getimagesize")) {
								?>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_PRODUCTTHUMBNAILSIZE;?>:
									</th>
									<td>
									<?php echo TXT_WPSC_HEIGHT;?>:<input type='text' size='6' name='product_image_height' value='<?php echo get_option('product_image_height'); ?>' /> <?php echo TXT_WPSC_WIDTH;?>:<input type='text' size='6' name='product_image_width' value='<?php echo get_option('product_image_width'); ?>' /> <br /><span class='small'></span>
									Changing this will only set the default size for images uploaded in future, to resize your current images, click <a href='#'>here</a> (this needs to be made to work, and this text needs adding to the language file)</span>
									</td>
								</tr>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_CATEGORYTHUMBNAILSIZE;?>:
									</th>
									<td>
									<?php echo TXT_WPSC_HEIGHT;?>:<input type='text' size='6' name='category_image_height' value='<?php echo get_option('category_image_height'); ?>' /> <?php echo TXT_WPSC_WIDTH;?>:<input type='text' size='6' name='category_image_width' value='<?php echo get_option('category_image_width'); ?>' /> <span class='small'></span>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SINGLE_PRODUCTTHUMBNAILSIZE;?>:
									</th>
									<td>
									<?php echo TXT_WPSC_HEIGHT;?>:<input type='text' size='6' name='single_view_image_height' value='<?php echo get_option('single_view_image_height'); ?>' /> <?php echo TXT_WPSC_WIDTH;?>:<input type='text' size='6' name='single_view_image_width' value='<?php echo get_option('single_view_image_width'); ?>' /> <span class='small'></span>
									</td>
								</tr>
						
							<?php
								}
							?>            
						
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOWTHUMBNAILS;?>:
									</th>
									<td>
									<?php
									$show_thumbnails = get_option('show_thumbnails');
									$show_thumbnails1 = "";
									$show_thumbnails2 = "";
									switch($show_thumbnails) {
										case 0:
										$show_thumbnails2 = "checked ='true'";
										break;
										
										case 1:
										$show_thumbnails1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='show_thumbnails' id='show_thumbnails1' <?php echo $show_thumbnails1; ?> /> <label for='show_thumbnails1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='show_thumbnails' id='show_thumbnails2' <?php echo $show_thumbnails2; ?> /> <label for='show_thumbnails2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_SHOWCATEGORYTHUMBNAILS;?>:
									</th>
									<td>
									<?php
									$show_category_thumbnails = get_option('show_category_thumbnails');
									$show_category_thumbnails1 = "";
									$show_category_thumbnails2 = "";
									switch($show_category_thumbnails) {
										case 0:
										$show_category_thumbnails2 = "checked ='true'";
										break;
										
										case 1:
										$show_category_thumbnails1 = "checked ='true'";
										break;
									}
						
									?>
									<input type='radio' value='1' name='show_category_thumbnails' id='show_category_thumbnails1' <?php echo $show_category_thumbnails1; ?> /> <label for='show_category_thumbnails1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input type='radio' value='0' name='show_category_thumbnails' id='show_category_thumbnails2' <?php echo $show_category_thumbnails2; ?> /> <label for='show_category_thumbnails2'><?php echo TXT_WPSC_NO;?></label>
									</td>
								</tr>
							</table>
							
									
							<h3 class="form_group"><?php echo TXT_WPSC_PAGESETTINGS;?></h3>
							<table class='wpsc_options form-table'>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_USE_PAGINATION;?>:
									</th>
									<td>
									<?php
									$use_pagination = get_option('use_pagination');
									$use_pagination1 = "";
									$use_pagination2 = "";
									switch($use_pagination) {
										case 0:
										$use_pagination2 = "checked ='true'";
										$page_count_display_state = 'style=\'display: none;\'';
										break;
										
										case 1:
										$use_pagination1 = "checked ='true'";
										$page_count_display_state = '';
										break;
									}
									?>
									<input onclick='jQuery("#wpsc_products_per_page").show()'  type='radio' value='1' name='use_pagination' id='use_pagination1' <?php echo $use_pagination1; ?> /> <label for='use_pagination1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
									<input onclick='jQuery("#wpsc_products_per_page").hide()' type='radio' value='0' name='use_pagination' id='use_pagination2' <?php echo $use_pagination2; ?> /> <label for='use_pagination2'><?php echo TXT_WPSC_NO;?></label><br />
									<div id='wpsc_products_per_page' <?php echo $page_count_display_state; ?> >
									<input type='text' size='6' name='wpsc_products_per_page' value='<?php echo get_option('wpsc_products_per_page'); ?>' /> <?php echo TXT_WPSC_OPTION_PRODUCTS_PER_PAGE; ?>
									</div>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
										<?php echo TXT_WPSC_PAGE_NUMBER_POSITION;?>:
									</th>
									<td>
										<input type='radio' value='1' name='wpsc_page_number_position' id='wpsc_page_number_position1' <?php if (get_option('wpsc_page_number_position') == 1) { echo "checked='true'"; } ?> /><label for='wpsc_page_number_position1'>Top</label>&nbsp;
										<input type='radio' value='2' name='wpsc_page_number_position' id='wpsc_page_number_position2' <?php if (get_option('wpsc_page_number_position') == 2) { echo "checked='true'"; } ?> /><label for='wpsc_page_number_position2'>Bottom</label>&nbsp;
										<input type='radio' value='3' name='wpsc_page_number_position' id='wpsc_page_number_position3' <?php if (get_option('wpsc_page_number_position') == 3) { echo "checked='true'"; } ?> /><label for='wpsc_page_number_position3'>Both</label>
										<br />
									</td>
								</tr>   
							</table> 
							
							<?php
						  /* here end the presentation options */						  
						  ?>
							<div class="submit">
								<input type="submit" value="Update »" name="updateoption"/>
							</div>
						</div>
						
						
						
						
						<div id="options_shipping">
						  <h2><?php echo TXT_WPSC_OPTIONS_SHIPPING_HEADER; ?></h2>
							<?php
							/* here start the shipping options */						  
						  ?>
							<table class='wpsc_options form-table'>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_USE_SHIPPING;?>:
									</th>
									<td>
									<?php
									$do_not_use_shipping = get_option('do_not_use_shipping');
									$do_not_use_shipping1 = "";
									$do_not_use_shipping2 = "";
									switch($do_not_use_shipping) {    
										case 1:
										$do_not_use_shipping1 = "checked ='true'";
										break;
												
										case 0:
										default:
										$do_not_use_shipping2 = "checked ='true'";
										break;
									}
						
									?>
										<input type='radio' value='0' name='do_not_use_shipping' id='do_not_use_shipping2' <?php echo $do_not_use_shipping2; ?> /> <label for='do_not_use_shipping2'><?php echo TXT_WPSC_YES;?></label>&nbsp;
									<input type='radio' value='1' name='do_not_use_shipping' id='do_not_use_shipping1' <?php echo $do_not_use_shipping1; ?> /> <label for='do_not_use_shipping1'><?php echo TXT_WPSC_NO;?></label><br />
									<?php echo TXT_WPSC_USE_SHIPPING_DESCRIPTION;?>
									</td>
								</tr>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_BASE_LOCAL;?>:
									</th>
									<td>
									<input type='text' size='10' value='<?php echo number_format(get_option('base_local_shipping'), 2); ?>' name='base_local_shipping' />
									</td>
								</tr>
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_BASE_INTERNATIONAL;?>:
									</th>
									<td>
									<input type='text' size='10' value='<?php echo number_format(get_option('base_international_shipping'), 2); ?>' name='base_international_shipping' /><br />
									<?php echo TXT_WPSC_SHIPPING_NOTE;?>
									</td>
								</tr>
								
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_USPS_USERID;?>:
									</th>
									<td>
									<input type='text' size='20' value='<?php echo get_option('usps_user_id'); ?>' name='usps_user_id' />
									</td>
								</tr>
					
								<tr>
									<th scope="row">
									<?php echo TXT_WPSC_USPS_PASSWORD;?>:
									</th>
									<td>
									<input type='text' size='20' value='<?php echo get_option('usps_user_password'); ?>' name='usps_user_password' />
									</td>
								</tr>
								
								
									<?php
									$shipwire1 = "";
									$shipwire2 = "";
									switch(get_option('shipwire')) {    
										case 1:
										$shipwire1 = "checked ='true'";
										$shipwire_settings = 'style=\'display: block;\'';
										break;
												
										case 0:
										default:
										$shipwire2 = "checked ='true'";
										$shipwire_settings = '';
										break;
									}
						
									?>
								
								<tr>
									<th scope="row">
										<?php echo TXT_WPSC_SHIPWIRESETTINGS;?><span style='color: red;'></span> :
									</th>
									<td>
										<input type='radio' onclick='jQuery("#wpsc_shipwire_setting").show()' value='1' name='shipwire' id='shipwire1' <?php echo $shipwire1; ?> /> <label for='shipwire1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
										<input type='radio' onclick='jQuery("#wpsc_shipwire_setting").hide()' value='0' name='shipwire' id='shipwire2' <?php echo $shipwire2; ?> /> <label for='shipwire2'><?php echo TXT_WPSC_NO;?></label>
											<?php
											$shipwrieemail = get_option("shipwireemail");
											$shipwriepassword = get_option("shipwirepassword");
											?>
											<div id='wpsc_shipwire_setting' <?php echo $shipwire_settings; ?>>
											<table>
												<tr><td><?=TXT_WPSC_SHIPWIREEMAIL;?> :</td><td> <input type="text" name="shipwireemail" value="<?=$shipwrieemail;?>"></td></tr>
												<tr><td><?=TXT_WPSC_SHIPWIREPASSWORD;?> :</td><td><input type="text" name="shipwirepassword" value="<?=$shipwriepassword;?>"></td></tr>
												<tr><td><a onclick='shipwire_sync()' style="cursor:pointer;">Sync product</a></td></tr>
											</table>
											</div>
									</td>
								</tr>
							</table> 						  
							<?php
							/* here end the shipping options */						  
						  ?>						  
							<div class="submit">
								<input type="submit" value="Update »" name="updateoption"/>
							</div>
						</div>
						
						
						
						<!--
						<div id="options_payment">
						  <h2><?php echo TXT_WPSC_OPTIONS_PAYMENT_HEADER; ?></h2>
						</div>-->
						
						
						
						
						<div id="options_admin">
						  <h2><?php echo TXT_WPSC_OPTIONS_ADMIN_HEADER; ?></h2>
							<?php
							/* here start the admin options */						  
						  ?>
								<table class='wpsc_options form-table'>            
									
									<tr>
										<th scope="row">
										<?php echo TXT_WPSC_MAXDOWNLOADSPERFILE;?>:
										</th>
										<td>
										<input type='text' size='10' value='<?php echo get_option('max_downloads'); ?>' name='max_downloads' />
										</td>
									</tr>
									
										<?php
										$wpsc_ip_lock_downloads1 = "";
										$wpsc_ip_lock_downloads2 = "";
										switch(get_option('wpsc_ip_lock_downloads')) {    
											case 1:
											$wpsc_ip_lock_downloads1 = "checked ='true'";
											break;
													
											case 0:
											default:
											$wpsc_ip_lock_downloads2 = "checked ='true'";
											break;
										}
							
										?>
									<tr>
										<th scope="row">
										<?php echo TXT_WPSC_LOCK_DOWNLOADS_TO_IP;?>:
										</th>
										<td>
											<input type='radio' value='1' name='wpsc_ip_lock_downloads' id='wpsc_ip_lock_downloads2' <?php echo $wpsc_ip_lock_downloads1; ?> /> <label for='wpsc_ip_lock_downloads2'><?php echo TXT_WPSC_YES;?></label>&nbsp;
											<input type='radio' value='0' name='wpsc_ip_lock_downloads' id='wpsc_ip_lock_downloads1' <?php echo $wpsc_ip_lock_downloads2; ?> /> <label for='wpsc_ip_lock_downloads1'><?php echo TXT_WPSC_NO;?></label><br />
										</td>
									</tr>     
									
									
									<tr>
										<th scope="row">
										<?php echo TXT_WPSC_PURCHASELOGEMAIL;?>:
										</th>
										<td>
										<input class='text' type='text' size='40' value='<?php echo get_option('purch_log_email'); ?>' name='purch_log_email' />
										</td>
									</tr>
									<tr>
										<th scope="row">
										<?php echo TXT_WPSC_REPLYEMAIL;?>:
										</td>
										<td>
										<input class='text' type='text' size='40' value='<?php echo get_option('return_email'); ?>' name='return_email' />
										</td>
									</tr>
									<tr>
										<th scope="row">
										<?php echo TXT_WPSC_TERMS2;?>:
										</th>
										<td>
										<textarea name='terms_and_conditions' size='40'><?php echo stripslashes(get_option('terms_and_conditions')); ?></textarea>
										</td>
									</tr>
						
								</table> 
										
								<h3 class="form_group"><?php echo TXT_WPSC_URLSETTINGS;?>:</h3>
								<table class='wpsc_options form-table'>
									</tr>
									<tr class='merged'>
										<th scope="row">
										<?php echo TXT_WPSC_PRODUCTLISTURL;?>:
										</th>
										<td>
										<input class='text' type='text' size='50' value='<?php echo get_option('product_list_url'); ?>' name='product_list_url' />
										</td>
									</tr>
									<tr class='merged'>
										<th scope="row">
										<?php echo TXT_WPSC_SHOPPINGCARTURL;?>:
										</th>
										<td>
										<input class='text' type='text' size='50' value='<?php echo get_option('shopping_cart_url'); ?>' name='shopping_cart_url' />
										</td>
									</tr>
									<?php /*
									<tr class='merged'>
										<th scope="row">
										<?php echo TXT_WPSC_CHECKOUTURL;?>:
										</th>
										<td>
										<input class='text' type='text' size='50' value='<?php echo get_option('checkout_url'); ?>' name='checkout_url' />
										</td>
									</tr>*/
									?>
									<tr class='merged'>
										<th scope="row">
										<?php echo TXT_WPSC_TRANSACTIONDETAILSURL;?>:
										</th>
										<td>
										<input class='text' type='text' size='50' value='<?php echo get_option('transact_url'); ?>' name='transact_url' />
										</td>
									</tr>
								<?php
								if(function_exists("nzshpcrt_user_log")) {
								?>
									<tr class='merged'>
										<th scope="row">
										<?php echo TXT_WPSC_USERACCOUNTURL;?>:
										</th>
										<td>
										<input class='text' type='text' size='50' value='<?php echo get_option('user_account_url'); ?>' name='user_account_url' />
										</td>
									</tr>
								<?php
								}
								?>
									<tr class='merged'>
										<td>
										</td>
										<td>
										<a href='admin.php?page=<?php echo WPSC_DIR_NAME; ?>/options.php&amp;update_page_urls=true'><?php echo TXT_WPSC_UPDATE_PAGE_URLS; ?></a> | 
										<a href='admin.php?page=<?php echo WPSC_DIR_NAME; ?>/options.php&amp;clean_categories=true'><?php echo TXT_WPSC_FIX_CATEGORY_PERMALINKS; ?></a>
										</td>
									</tr>
								</table>					  
							<?php
							/* here end the admin options */						  
						  ?>
							<div class="submit">
								<input type="submit" value="Update »" name="updateoption"/>
							</div>
						</div>
						
						
									
									
<!--						<div id="options_marketing">
						  <h2><?php echo TXT_WPSC_OPTIONS_MARKETING_HEADER; ?></h2>
						</div>-->
				
        </div>
			</form>
  <?php
  }
?>