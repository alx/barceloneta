<?php
function nzshpcrt_submit_checkout() {
 /*
  * This is the function used for handling the submitted checkout page
  */
  global $wpdb, $nzshpcrt_gateways, $user_ID;
  session_start();
  if(get_option('permalink_structure') != '') {
    $seperator ="?";
	} else {
    $seperator ="&";
	}
	
  if(($_POST['submitwpcheckout'] == 'true')) {
    $check_checkout_page = $wpdb->get_var("SELECT `id` FROM `".$wpdb->posts."` WHERE `post_content` LIKE '%[checkout]%' LIMIT 1");
    if(is_numeric($check_checkout_page)) {
      $returnurl = "Location: ".get_option('shopping_cart_url').$seperator."total=".$_GET['total'];
		} else {
      $returnurl = "Location: ".get_option('shopping_cart_url');
		}

    $_SESSION['collected_data'] = $_POST['collected_data'];
    $find_us = $_POST['how_find_us'];
    if(!(($_POST['engrave1'] == '') && ($_POST['engrave2'] == ''))) {
			$engrave = $wpdb->escape($_POST['engrave1'].",".$_POST['engrave2']);
    }
    $any_bad_inputs = false;
    foreach($_POST['collected_data'] as $value_id => $value) {
      $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `id` = '$value_id' LIMIT 1";
      $form_data = $wpdb->get_results($form_sql,ARRAY_A);
      $form_data = $form_data[0];
      /*
			if($_POST['collected_data'][get_option('paypal_form_address')] != '')
			{
			$map_data['address'] = addslashes($_POST['collected_data'][get_option('paypal_form_address')]);
			}
			if($_POST['collected_data'][get_option('paypal_form_city')] != '')
			{
			$map_data['city'] = addslashes($_POST['collected_data'][get_option('paypal_form_city')]); 
			}
			if(preg_match("/^[a-zA-Z]{2}$/",$_SESSION['selected_country']))
			{
			$map_data['country'] = $_SESSION['selected_country'];
			}
			$map_data['zipcode']='';
			$map_data['radius'] = '50000';
			$map_data['state'] = '';
			$map_data['submit'] = 'Find Store';
		
			$i=0;
			if (function_exists('getdistance')) {
				$maps = getdistance($map_data);
				while($rows = mysql_fetch_array($maps)) {
					if ($i==0) {
						$closest_store = $rows[5];
					}
					$i++;
				}
			}
			//$wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `closest_store` = '".$closest_store."' WHERE `id` = '".$log_id."' LIMIT 1 ;");
			*/
			$bad_input = false;
      if(($form_data['mandatory'] == 1) || ($form_data['type'] == "coupon")) {
        switch($form_data['type']) {
          case "email":
          if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+\.[a-zA-Z]{2,5}$/",$value)) {
            $any_bad_inputs = true;
            $bad_input = true;
            }
          break;

          case "delivery_country":
          break;

          case "country":
          break;
          
          default:
          if($value == null) {
            $any_bad_inputs = true;
            $bad_input = true;
            }
          break;
          }
          /*
					if($form_data['type'] == "coupon") {
						if($value != '') { // only act if data has been entered
							$coupon_sql = "SELECT * FROM `".$wpdb->prefix."wpsc_coupon_codes` WHERE `coupon_code` = '".$value."' AND `active` = '1' LIMIT 1";
							$coupon_data = $wpdb->get_results($coupon_sql,ARRAY_A);
							if($coupon_data == null) {
								$any_bad_inputs = true;
								$bad_input = true;
								}
							}
						}
					*/
        if($bad_input === true) {
          switch($form_data['name']) {
            case TXT_WPSC_FIRSTNAME:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDNAME . "";
            break;
    
            case TXT_WPSC_LASTNAME:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDSURNAME . "";
            break;
    
            case TXT_WPSC_EMAIL:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDEMAILADDRESS . "";
            break;
    
            case TXT_WPSC_ADDRESS1:
            case TXT_WPSC_ADDRESS2:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDADDRESS . "";
            break;
    
            case TXT_WPSC_CITY:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDCITY . "";
            break;
    
            case TXT_WPSC_PHONE:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALIDPHONENUMBER . "";
            break;
    
            case TXT_WPSC_COUNTRY:
            $bad_input_message .= TXT_WPSC_PLEASESELECTCOUNTRY . "";
            break;
    
//             case TXT_WPSC_COUPON:
//             $bad_input_message .= TXT_WPSC_COUPON_DOESNT_EXIST . "";
//             break;
            
            default:
            $bad_input_message .= TXT_WPSC_PLEASEENTERAVALID . " " . strtolower($form_data['name']) . ".";
            break;
            }
          $bad_input_message .= "\n\r";
          }
        }
      }
      
      
      
  // this here section handles uploading files specified by the user for products 
  $accepted_file_types['mime'][] = 'image/jpeg';
  $accepted_file_types['mime'][] = 'image/gif';
  $accepted_file_types['mime'][] = 'image/png';
  
  
  $accepted_file_types['ext'][] = 'jpeg';
  $accepted_file_types['ext'][] = 'jpg';
  $accepted_file_types['ext'][] = 'gif';
  $accepted_file_types['ext'][] = 'png';
  
  
  
  foreach($_SESSION['nzshpcrt_cart'] as $key => $item) {    
		$can_have_uploaded_image = get_product_meta($item->product_id,'can_have_uploaded_image',true);
		if ($can_have_uploaded_image[0]=='on') {
		  $file_data['name'] = basename($_FILES['uploaded_image']['name'][$key]);
		  $file_data['type'] = $_FILES['uploaded_image']['type'][$key];
		  $file_data['tmp_name'] = $_FILES['uploaded_image']['tmp_name'][$key];
		  $file_data['error'] = $_FILES['uploaded_image']['error'][$key];
		  $file_data['size'] = $_FILES['uploaded_image']['size'][$key];
		  $mime_type_data = wpsc_get_mimetype($file_data['tmp_name'], true);
			
			$name_parts = explode('.',basename($file_data['name']));
			$extension = array_pop($name_parts);
			echo $extension ."<br />";
		  if($mime_type_data['is_reliable'] == true) {
		    $mime_type = $mime_type_data['mime_type'];
		  } else {
		    // if we can't use what PHP provides us with, we have to trust the user as there aren't really any other choices.
		    $mime_type = $file_data['type'];
		  }
			if((array_search($mime_type, $accepted_file_types['mime']) !== false) && (array_search($extension, $accepted_file_types['ext']) !== false) ) {
			  if(is_file(WPSC_USER_UPLOADS_DIR.$file_data['name'])) {
					$name_parts = explode('.',basename($file_data['name']));
					$extension = array_pop($name_parts);
					$name_base = implode('.',$name_parts);
					$file_data['name'] = null;
					$num = 2;
					//  loop till we find a free file name, first time I get to do a do loop in yonks
					do {
						$test_name = "{$name_base}-{$num}.{$extension}";
						if(!file_exists(WPSC_USER_UPLOADS_DIR.$test_name)) {
							$file_data['name'] = $test_name;
						} 						
						$num++;
					} while ($file_data['name'] == null);
			  }
			  //exit($file_data['name']);
				if(move_uploaded_file($file_data['tmp_name'], WPSC_USER_UPLOADS_DIR.$file_data['name']) ) {
					$_SESSION['nzshpcrt_cart'][$key]->file_data = array('file_name' => $file_data['name'], 'mime_type' => $mime_type );			
				}
			}
		}
  }
  //echo("<pre>".print_r($_FILES,true)."</pre>");
  //exit("<pre>".print_r($_SESSION['nzshpcrt_cart'],true)."</pre>");
    

    foreach((array)$_SESSION['nzshpcrt_cart'] as $item) {
			//exit("------><pre>".print_r((array)$_SESSION['nzshpcrt_cart'],1)."</pre>");
			$in_stock = check_in_stock($item->product_id, $item->product_variations, $item->quantity);
			if (get_option('checkbox_variation')=='1') {
				$in_stock = true;
			}
      if($in_stock == false) {
        $bad_input_message .= TXT_WPSC_ITEM_GONE_OUT_OF_STOCK . "";
        $bad_input_message .= "\n\r";
        $any_bad_inputs = true;
        break;
			}
		}
		
 		if(get_option('custom_gateway_options') == null) {
			$bad_input_message .= TXT_WPSC_PROCESSING_PROBLEM . "";
			$bad_input_message .= "\n\r";
			$any_bad_inputs = true;
 		}

   list($bad_input_message, $any_bad_inputs) = apply_filters('wpsc_additional_checkout_checks', array($bad_input_message, $any_bad_inputs));
   //exit("<pre>".print_r($bad_input_message, true)."</pre>");

    if($any_bad_inputs === true) {
      $_SESSION['nzshpcrt_checkouterr'] = nl2br($bad_input_message);
      header($returnurl);
      exit();
		}
    $cart = $_SESSION['nzshpcrt_cart'];
    $_SESSION['checkoutdata'] = $_POST;
    if($_POST['agree'] != 'yes') {
      $_SESSION['nzshpcrt_checkouterr'] = TXT_WPSC_PLEASEAGREETERMSANDCONDITIONS;
      header($returnurl);
      exit();
		}
    if($cart == null) {
      $_SESSION['nzshpcrt_checkouterr'] = TXT_WPSC_NOTHINGINYOURSHOPPINGCART;
      header($returnurl);
      exit();
		}
    $sessionid = (mt_rand(100,999).time());

   if( !(is_numeric($user_ID) && ($user_ID > 0))) {
     $user_ID = 'null';
     }
	  if(isset($_SESSION['usps_shipping']) && is_numeric($_SESSION['usps_shipping'])) { 
	    $base_shipping = $_SESSION['usps_shipping'];
    } else {
			$base_shipping = nzshpcrt_determine_base_shipping(0, $_SESSION['delivery_country']);
    }
    //clear the coupon
    //$_SESSION['coupon_num'] = '';
    
    
    //insert the record into the purchase log table
	//exit("----->". $_SESSION['delivery_country']);
	$price = nzshpcrt_overall_total_price($_SESSION['selected_country'],false);
	$sql = "INSERT INTO `".$wpdb->prefix."purchase_logs` ( `totalprice` , `sessionid` , `date`, `billing_country`, `shipping_country`,`base_shipping`,`shipping_region`, `user_ID`, `discount_value`, `discount_data`, `find_us`, `engravetext`, `google_status`) VALUES ( '".$wpdb->escape($price)."', '".$sessionid."', '".time()."', '".$_SESSION['selected_country']."', '".$_SESSION['delivery_country']."', '".$base_shipping."','".$_SESSION['selected_region']."' , '".(int)$user_ID."' , '".(float)$_SESSION['wpsc_discount']."', '".$wpdb->escape($_SESSION['coupon_num'])."', '', '{$engrave}', ' ')";
	//exit($sql);
	$wpdb->query($sql) ;
	$email_user_detail = '';

   $log_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid` IN('".$sessionid."') LIMIT 1") ;
   foreach($_POST['collected_data'] as $value_id => $value) {
     $wpdb->query("INSERT INTO `".$wpdb->prefix."submited_form_data` ( `log_id` , `form_id` , `value` ) VALUES ( '".$log_id."', '".$value_id."', '".$value."');") ;
     }
   
		if(function_exists("nzshpcrt_user_log")) {
			if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."usermeta'")) {
				$saved_data_sql = "SELECT * FROM `".$wpdb->prefix."usermeta` WHERE `user_id` = '".$user_ID."' AND `meta_key` = 'wpshpcrt_usr_profile';";
				$saved_data = $wpdb->get_row($saved_data_sql,ARRAY_A);
			
				$new_meta_data = serialize($_POST['collected_data']);
				if(($saved_data != null)) {
					$wpdb->query("UPDATE `".$wpdb->prefix."usermeta` SET `meta_value` =  '$new_meta_data' WHERE `user_id` IN ('$user_ID') AND `meta_key` IN ('wpshpcrt_usr_profile');");
				} else if(is_numeric($user_ID)) {
					$wpdb->query("INSERT INTO `".$wpdb->prefix."usermeta` ( `user_id` , `meta_key` , `meta_value` ) VALUES ( ".$user_ID.", 'wpshpcrt_usr_profile', '$new_meta_data');");
				}
			}
		}

   $downloads = get_option('max_downloads');
   $also_bought = array();
   $all_donations = true;
   $all_no_shipping = true;
   foreach($cart as $cart_item) {
     $row = $cart_item->product_id;
     $quantity = $cart_item->quantity;
     $variations = $cart_item->product_variations;
     $extras = $cart_item->extras;
     // serialize file data
     if(is_array($cart_item->file_data)) {
       $file_data = $wpdb->escape(serialize($cart_item->file_data));
     } else {
       $file_data = '';
     }
     /* creates an array of purchased items for logging further on */
     if(isset($also_bought[$cart_item->product_id])) {
       $also_bought[$cart_item->product_id]++;
       } else {
       $also_bought[$cart_item->product_id] = 1;
       }
     
     $product_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id` = '$row' LIMIT 1",ARRAY_A) ;
		 if($product_data['file'] > 0) {
			 $unique_id = sha1(uniqid(mt_rand(), true));
			 $wpdb->query("INSERT INTO `".$wpdb->prefix."download_status` ( `fileid` , `purchid` , `uniqueid`, `downloads` , `active` , `datetime` ) VALUES ( '".$product_data['file']."', '".$log_id."', '".$unique_id."', '$downloads', '0', NOW( ));");
			 }
     
     if($product_data['donation'] == 1) {
       $price = $cart_item->donation_price;
       $gst = 0;
       $donation = 1;
       } else {
       $price = calculate_product_price($row, $variations);
       if($product_data['notax'] != 1) {
         $price = nzshpcrt_calculate_tax($price, $_SESSION['selected_country'], $_SESSION['selected_region']);
         if(get_option('base_country') == $_SESSION['selected_country']) {
           $country_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."currency_list` WHERE `isocode` IN('".get_option('base_country')."') LIMIT 1",ARRAY_A);
           if(($country_data['has_regions'] == 1)) {
             if(get_option('base_region') == $_SESSION['selected_region']) {
               $region_data = $wpdb->get_row("SELECT `".$wpdb->prefix."region_tax`.* FROM `".$wpdb->prefix."region_tax` WHERE `".$wpdb->prefix."region_tax`.`country_id` IN('".$country_data['id']."') AND `".$wpdb->prefix."region_tax`.`id` IN('".get_option('base_region')."') ",ARRAY_A) ;
               }
             $gst =  $region_data['tax'];
             } else {
             $gst =  $country_data['tax'];
             }
           }
         } else { $gst = 0; }
        $donation = 0;
        $all_donations = false;
        }
      
        
      if($product_data['no_shipping'] != 1) {
        $all_no_shipping = false;
        }
            
      $country = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id`='".$log_id."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1",ARRAY_A);
      $country = $country[0]['value'];
     
     $country_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."currency_list` WHERE `isocode` IN('".get_option('base_country')."') LIMIT 1",ARRAY_A);
     
     $shipping = nzshpcrt_determine_item_shipping($row, 1, $_SESSION['delivery_country']);
     $cartsql = "INSERT INTO `".$wpdb->prefix."cart_contents` ( `prodid` , `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `donation`, `no_shipping`, `files` ) VALUES ('".$row."', '".$log_id."','".$price."','".$shipping."', '".$gst."','".$quantity."', '".$donation."', '".$product_data['no_shipping']."', '$file_data')";
    //exit($cartsql);
  
     
     $wpdb->query($cartsql);
     $cart_id = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `".$wpdb->prefix."product_variations` LIMIT 1",ARRAY_A);
     $cart_id = $cart_id[0]['id'];
	$extra_var='';
     if($variations != null) {
	$extra_var.='[';
	$i=0;
       foreach($variations as $variation => $value) {
         $wpdb->query("INSERT INTO `".$wpdb->prefix."cart_item_variations` ( `cart_id` , `variation_id` , `value_id` ) VALUES ( '".$cart_id."', '".$variation."', '".$value."' );");
        $i++;
		if ($i==1) {
			$extra_var.=$value;
		} else {
			$extra_var.=",".$value;
		}	
	}
	}
	$j=0;
	$extra_var.='],[';
	if($extras != null) {
       foreach($extras as $extra) {
		$wpdb->query("INSERT INTO `".$wpdb->prefix."cart_item_extras` ( `cart_id` , `extra_id`) VALUES ( '".$cart_id."', '".$extra."');");
		$name = $wpdb->get_var("SELECT name FROM ".$wpdb->prefix."extras_values WHERE id=$extra");
		$j++;
		if ($j==1) {
			$extra_var.=$name;
		} else {
			$extra_var.=",".$name;
		}
	}
	$extra_var.=']';
       }
	/*
	if (function_exists('sendemailstostores')) {
		if ($_POST['pickupordelivery']==1){
			$delivery = "Pick Up";
		} else {
			$delivery = "Delivery";
		}
		$chosen_store = $_POST['chosen_store'];
		$email_sql = "SELECT * FROM locations WHERE storename='".$chosen_store."'";
		$email_data = $wpdb->get_results($email_sql,ARRAY_A);
		
		$email_message = "Order: ".$product_data['name']." with additional variations : ".$extra_var."<br>";
		$email_message .= "<br>";
		$email_message .= "Delivery/Pick Up:".$delivery;
		$email_message .= "<br>";
		$email_message .= "Customer detail: <br>";
		$email_message .= "Name:". $_POST['collected_data'][get_option('paypal_form_first_name')]." ".$_POST['collected_data'][get_option('paypal_form_last_name')]."<br>";
		$email_message .= "Address: ".$map_data['address']."<br>";
		$email_message .= "City:".$map_data['city'];
		sendemailstostores($email_data[0]['url'],'New Order',$email_message);
	}*/
     /*
      * This code decrements the stock quantitycart_item_variations`
     */
     if(is_array($variations)) {
       $variation_values = array_values($variations);
       }
     //$debug .= "<pre>".print_r($variations,true)."</pre>";
     if($product_data['quantity_limited'] == 1) {
       switch(count($variation_values)) {
         case 2:
         $variation_stock_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '".$product_data['id']."' AND (`variation_id_1` = '".$variation_values[0]."' AND `variation_id_2` = '".$variation_data[1]."') OR (`variation_id_1` = '".$variation_values[1]."' AND `variation_id_2` = '".$variation_values[0]."') LIMIT 1",ARRAY_A);
         //$debug .= "<pre>".print_r($variation_stock_data,true)."</pre>";
         $wpdb->query("UPDATE `".$wpdb->prefix."variation_priceandstock` SET `stock` = '".($variation_stock_data['stock']-$quantity)."'  WHERE `id` = '".$variation_stock_data['id']."' LIMIT 1",ARRAY_A);
         break;
         
         case 1:
         $variation_stock_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '".$product_data['id']."' AND (`variation_id_1` = '".$variation_values[0]."' AND `variation_id_2` = '0') LIMIT 1",ARRAY_A);
         //$debug .= "<pre>".print_r($variation_stock_data,true)."</pre>";
         $wpdb->query("UPDATE `".$wpdb->prefix."variation_priceandstock` SET `stock` = '".($variation_stock_data['stock']-$quantity)."'  WHERE `id` = '".$variation_stock_data['id']."' LIMIT 1",ARRAY_A);
         break;
        
         default:
         /* normal form of decrementing stock */
         $wpdb->query("UPDATE `".$wpdb->prefix."product_list` SET `quantity`='".($product_data['quantity']-$quantity)."' WHERE `id`='".$product_data['id']."' LIMIT 1");
         break;
         }
       }     
     }
   
   
   $unneeded_value = null; //this is only used to store the quantity for the item we are working on, so that we can get the array key
   $assoc_quantity = null;
   foreach($also_bought as $selected_product => $unneeded_value) {
     foreach($also_bought as $associated_product => $assoc_quantity) {
       if(($selected_product == $associated_product)) {
         continue; //don't want to associate products with themselves
         }
       $check_assoc = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."also_bought_product` WHERE `selected_product` IN('$selected_product') AND `associated_product` IN('$associated_product') LIMIT 1");
       if(isset($check_assoc) && ($check_assoc > 0)) {
         $wpdb->query("UPDATE `".$wpdb->prefix."also_bought_product` SET `quantity` = (`quantity` + $assoc_quantity) WHERE `id` = '$check_assoc' LIMIT 1;");
         } else {
         $wpdb->query("INSERT INTO `".$wpdb->prefix."also_bought_product` ( `selected_product` , `associated_product` , `quantity` ) VALUES ( '$selected_product', '".$associated_product."', '".$assoc_quantity."' );");
         }
       }
     }
   
   do_action('wpsc_submit_checkout', $log_id);
   //mail( get_option('purch_log_email'),('debug from '.date("d/m/Y H:i:s")), $debug);
   $curgateway = get_option('payment_gateway');
	 //	if (get_option('custom_gateway')) {
	 
	 
		$selected_gateways = get_option('custom_gateway_options');
		
		if(count($selected_gateways) > 1) {
			if (in_array($_POST['custom_gateway'], (array)$selected_gateways)) {
				$curgateway = $_POST['custom_gateway'];
			} else {
				$curgateway = get_option('payment_gateway');
			}
		} else if(count($selected_gateways) == 1) {
			$curgateway = array_pop($selected_gateways);
		}
				
				
		
		//} else {
		//	$curgateway = get_option('payment_gateway');
		//}


    if(get_option('permalink_structure') != '') {
      $seperator ="?";
		} else {
      $seperator ="&";
		}
		
    if((($_POST['payment_method'] == 2) && (get_option('payment_method') == 2)) || (get_option('payment_method') == 3)) {
      foreach($nzshpcrt_gateways as $gateway) {
        if($gateway['internalname'] == 'testmode')  {
          $gateway_used = $gateway['internalname'];
          $wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `gateway` = '".$gateway_used."' WHERE `id` = '".$log_id."' LIMIT 1 ;");
          $gateway['function']($seperator, $sessionid);
          }
        }
      } else {
      foreach($nzshpcrt_gateways as $gateway) {
        if($gateway['internalname'] == $curgateway ) {
          $gateway_used = $gateway['internalname'];
          $wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `gateway` = '".$gateway_used."' WHERE `id` = '".$log_id."' LIMIT 1 ;");
	$gateway['function']($seperator, $sessionid);
          }
        }
      }
    $_SESSION['coupon_num'] = '';
  //exit("<pre>".print_r($nzshpcrt_gateways,true)."</pre>");
    }
  }
?>