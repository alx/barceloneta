<?php
$Debug = 0;
function transaction_results($sessionid, $echo_to_screen = true, $transaction_id = null) {
	global $wpdb,$_SESSION,$Debug,$_GET; //why are autoglobal arrays here?
	$curgateway = get_option('payment_gateway');
	$errorcode = '';
	$order_status= 2;
	$siteurl = get_option('siteurl');
	
	/*
	 * {Notes} Double check that $Echo_To_Screen is a boolean value
	 */
	$echo_to_screen=(((!is_bool($echo_to_screen)))?((true)):(($echo_to_screen)));
	
	
	if(is_numeric($sessionid)) {
		$report = TXT_WPSC_EMAILMSG2;
		$selectsql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
		$purchase_log = $wpdb->get_row($selectsql,ARRAY_A) ;
		
	  if(($purchase_log['gateway'] == "testmode") && ($purchase_log['processed'] < 2))  {
			$message = "".TXT_WPSC_YOUR_ORDER.":\n";
			$message_html = "<h2  style='padding-top: 0px;' >".TXT_WPSC_YOUR_ORDER."</h2>";
		} else {
			$message = TXT_WPSC_EMAILMSG1;
			$message_html = $message;
		}
    $order_url = $siteurl."/wp-admin/admin.php?page=".WPSC_DIR_NAME."/display-log.php&amp;purchcaseid=".$purchase_log['id'];

    if(($_GET['ipn_request'] != 'true') and (get_option('paypal_ipn') == 1)) {
      if($purchase_log == null) {
        echo TXT_WPSC_ORDER_FAILED;
        if((get_option('purch_log_email') != null) && ($purchase_log['email_sent'] != 1)) {        
          mail(get_option('purch_log_email'), TXT_WPSC_NEW_ORDER_PENDING_SUBJECT, TXT_WPSC_NEW_ORDER_PENDING_BODY.$order_url, "From: ".get_option('return_email')."");
				}
        return false;
      } else if(($purchase_log['email_sent'] != 1) && ($purchase_log['processed'] < 2)) {  //added by Thomas on 20/6/2007 
        echo TXT_WPSC_ORDER_PENDING . "<p style='margin: 1em 0px 0px 0px;' >".nl2br(get_option('payment_instructions'))."</p>";
        if($purchase_log['gateway'] != 'testmode') {
					if((get_option('purch_log_email') != null) && ($purchase_log['email_sent'] != 1)) {
						mail(get_option('purch_log_email'), TXT_WPSC_NEW_ORDER_PENDING_SUBJECT, TXT_WPSC_NEW_ORDER_PENDING_BODY.$order_url, "From: ".get_option('return_email')."");
					} 
					return false;      
        }
      }
    } else if($purchase_log['processed'] < 2) {  //added by Thomas on 20/6/2007       
        echo TXT_WPSC_ORDER_PENDING . "<p style='margin: 1em 0px 0px 0px;' >".nl2br(get_option('payment_instructions'))."</p>";
        if($purchase_log['gateway'] != 'testmode') {
					if((get_option('purch_log_email') != null) && ($purchase_log['email_sent'] != 1)) {
						mail(get_option('purch_log_email'), TXT_WPSC_NEW_ORDER_PENDING_SUBJECT, TXT_WPSC_NEW_ORDER_PENDING_BODY.$order_url, "From: ".get_option('return_email')."");
					}
					return false;
        }
    }
	$cartsql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`=".$purchase_log['id']."";
	$cart = $wpdb->get_results($cartsql,ARRAY_A);
	
	if($purchase_log['shipping_country'] != '') {
		$billing_country = $purchase_log['billing_country'];
		$shipping_country = $purchase_log['shipping_country'];
	} else {
		$country = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id`=".$purchase_log['id']." AND `form_id` = '".get_option('country_form_field')."' LIMIT 1",ARRAY_A);
		$billing_country = $country[0]['value'];
		$shipping_country = $country[0]['value'];
	}

	$email_form_field = $wpdb->get_results("SELECT `id`,`type` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type` IN ('email') AND `active` = '1' ORDER BY `order` ASC LIMIT 1",ARRAY_A);
	$email_address = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id`=".$purchase_log['id']." AND `form_id` = '".$email_form_field[0]['id']."' LIMIT 1",ARRAY_A);
	$email = $email_address[0]['value'];
		

	$previous_download_ids = array(0); 
	if(($cart != null) && ($errorcode == 0)) {
		foreach($cart as $row) {
		$link = "";
		$productsql= "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`=".$row['prodid']."";
		$product_data = $wpdb->get_results($productsql,ARRAY_A) ;
		if($product_data[0]['file'] > 0) {
			if($purchase_log['email_sent'] != 1) {
				$wpdb->query("UPDATE `".$wpdb->prefix."download_status` SET `active`='1' WHERE `fileid`='".$product_data[0]['file']."' AND `purchid` = '".$purchase_log['id']."' LIMIT 1");
			}
			/*
			$digitalsql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE purchaseid=".$purchase_log['id']."";
			$digital = $wpdb->get_results($digitalsql,ARRAY_A);
			$digitalsql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`=".$digital[0]['id']."";
			$digital = $wpdb->get_results($digitalsql,ARRAY_A);
			$digitalsql = "SELECT * FROM `".$wpdb->prefix."variation_priceandstock` WHERE `variation_id_1`=".$digital[0]['value_id']." AND product_id=".$product_data[0]['id']."";
			$digital = $wpdb->get_results($digitalsql,ARRAY_A);*/
			
			
			
			
			$downloadable='1';
			// 		if ($digital[0]['file'] == '1'){
			// 			$downloadable='1';
			// 		}
			if ($downloadable){
				$download_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."download_status` WHERE `fileid`='".$product_data[0]['file']."' AND `purchid`='".$purchase_log['id']."' AND `id` NOT IN (".make_csv($previous_download_ids).") LIMIT 1",ARRAY_A);
				$download_data = $download_data[0];
				
				if($download_data['uniqueid'] == null) {  // if the uniqueid is not equal to null, its "valid", regardless of what it is
					$link = $siteurl."?downloadid=".$download_data['id'];
				} else {
					$link = $siteurl."?downloadid=".$download_data['uniqueid'];
				}	
				$previous_download_ids[] = $download_data['id'];
				$order_status= 4;
			}
		}
	
	
		do_action('wpsc_confirm_checkout', $purchase_log['id']);
	
		$shipping = nzshpcrt_determine_item_shipping($row['prodid'], $row['quantity'], $shipping_country);
		$total_shipping += $shipping;
		
		if($product_data[0]['special']==1) {
			$price_modifier = $product_data[0]['special_price'];
		} else {
			$price_modifier = 0;
		}
		
		$total+=($row['price']*$row['quantity']);
		$message_price = nzshpcrt_currency_display(($row['price']*$row['quantity']), $product_data[0]['notax'], true);
		$shipping_price  = nzshpcrt_currency_display($shipping, 1, true);
		$variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$row['id']."'";
		$variation_data = $wpdb->get_results($variation_sql,ARRAY_A); 
		$variation_count = count($variation_data);
			
		if($variation_count > 1) {
			$variation_list = " (";
			
			if($purchase['gateway'] != 'testmode') {
				if($gateway['internalname'] == $purch_data[0]['gateway'] ) {
					$gateway_name = $gateway['name'];
				}
			} else {
				$gateway_name = "Manual Payment";
			}
								
			$i = 0;
			
			foreach($variation_data as $variation) {	
				if($i > 0) {
					$variation_list.= ", ";
				}
				
				$value_id = $variation['value_id'];
				$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
				$variation_list.= $value_data[0]['name'];              
				$i++;	
			}
			$variation_list .= ")";
		} else {
			if($variation_count == 1) {
				$value_id = $variation_data[0]['value_id'];
				$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
				$variation_list = " (".$value_data[0]['name'].")";
			} else {
				$variation_list = '';
			}
		}
        
		if($link != '') {
			$message.= " - ". $product_data[0]['name'] . $variation_list ."  ".$message_price ."  ".TXT_WPSC_CLICKTODOWNLOAD.": $link\n";
			$message_html.= " - ". $product_data[0]['name'] . $variation_list ."  ".$message_price ."&nbsp;&nbsp;<a href='$link'>".TXT_WPSC_DOWNLOAD."</a>\n";
		} else {
			$plural = '';
			
			if($row['quantity'] > 1) {
				$plural = "s";
			}
			$message.= " - ".$row['quantity']." ". $product_data[0]['name'].$variation_list ."  ". $message_price ."\n - ". TXT_WPSC_SHIPPING.":".$shipping_price ."\n\r";
			$message_html.= " - ".$row['quantity']." ". $product_data[0]['name'].$variation_list ."  ". $message_price ."\n - ". TXT_WPSC_SHIPPING.":".$shipping_price ."\n\r";
		}
					
		$report.= " - ". $product_data[0]['name'] .$variation_list."  ".$message_price ."\n";
	}
			
			if($purchase_log['discount_data'] != '') {
				$coupon_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."wpsc_coupon_codes` WHERE coupon_code='".$wpdb->escape($purchase_log['discount_data'])."' LIMIT 1",ARRAY_A);
				if($coupon_data['use-once'] == 1) {
					$wpdb->query("UPDATE `".$wpdb->prefix."wpsc_coupon_codes` SET `active`='0', `is-used`='1' WHERE `id`='".$coupon_data['id']."' LIMIT 1");
				}
			}
			//$wpdb->query("UPDATE `".$wpdb->prefix."download_status` SET `active`='1' WHERE `fileid`='".$product_data[0]['file']."' AND `purchid` = '".$purchase_log['id']."' LIMIT 1");
        
        
			$total_shipping = nzshpcrt_determine_base_shipping($total_shipping, $shipping_country);
			$total = (($total+$total_shipping) - $purchase_log['discount_value']);
			// $message.= "\n\r";
			$message.= "Your Purchase No.: ".$purchase_log['id']."\n\r";			
			if($purchase_log['discount_value'] > 0) {
				$message.= TXT_WPSC_DISCOUNT.": ".nzshpcrt_currency_display($purchase_log['discount_value'], 1, true)."\n\r";
			}
			$message.= TXT_WPSC_TOTALSHIPPING.": ".nzshpcrt_currency_display($total_shipping,1,true)."\n\r";
			$message.= TXT_WPSC_TOTAL.": ".nzshpcrt_currency_display($total,1,true)."\n\r";
      
      
			$message_html.= "Your Purchase No.: ".$purchase_log['id']."\n\n\r";
			
			if($purchase_log['discount_value'] > 0) {
				$message_html.= TXT_WPSC_DISCOUNT.": ".nzshpcrt_currency_display($purchase_log['discount_value'], 1, true)."\n\r";
			}
			$message_html.= TXT_WPSC_TOTALSHIPPING.": ".nzshpcrt_currency_display($total_shipping,1,true)."\n\r";
			$message_html.= TXT_WPSC_TOTAL.": ".nzshpcrt_currency_display($total, 1,true)."\n\r";
        
			if(isset($_GET['ti'])) {
				$message.= "\n\r".TXT_WPSC_YOURTRANSACTIONID.": " . $_GET['ti'];
				$message_html.= "\n\r".TXT_WPSC_YOURTRANSACTIONID.": " . $_GET['ti'];
				$report.= "\n\r".TXT_WPSC_TRANSACTIONID.": " . $_GET['ti'];
			} else {
				$report_id = "Purchase No.: ".$purchase_log['id']."\n\r";
			}
			
			if(($email != '') && ($purchase_log['email_sent'] != 1)) {
				if($purchase_log['processed'] < 2) {
					$payment_instructions = strip_tags(get_option('payment_instructions'));
					$message = TXT_WPSC_ORDER_PENDING . "\n\r" . $payment_instructions ."\n\r". $message;
					mail($email, TXT_WPSC_ORDER_PENDING_PAYMENT_REQUIRED, $message, "From: ".get_option('return_email')."");
				} else {
					mail($email, TXT_WPSC_PURCHASERECEIPT, $message, "From: ".get_option('return_email')."");
				}
			}

			$report_user = TXT_WPSC_CUSTOMERDETAILS."\n\r";
    
			$form_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase_log['id']."'";
			$form_data = $wpdb->get_results($form_sql,ARRAY_A);
			
			if($form_data != null) {
        foreach($form_data as $form_field) {
					$form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `id` = '".$form_field['form_id']."' LIMIT 1";
					$form_data = $wpdb->get_results($form_sql,ARRAY_A);
					$form_data = $form_data[0];
					
					if($form_data['type'] == 'country' ) {
						$report_user .= $form_data['name'].": ".get_country($form_field['value'])."\n";
					} else {
						$report_user .= $form_data['name'].": ".$form_field['value']."\n";
					}
				}
			}

			$report_user .= "\n\r";
			$report = $report_user. $report_id . $report;

			if((get_option('purch_log_email') != null) && ($purchase_log['email_sent'] != 1)) {
				mail(get_option('purch_log_email'), TXT_WPSC_PURCHASEREPORT, $report, "From: ".get_option('return_email')."");
			}			
			
			if(($purchase_log['gateway'] == 'testmode') && ($purchase_log['processed'] < 2)) {
				echo "<br />" . nl2br(str_replace("$",'\$',$message_html));
				return;
			}
			
			$_SESSION['nzshpcrt_cart'] = '';
			$_SESSION['nzshpcrt_cart'] = Array();
      
			if(true === $echo_to_screen) { 
				echo '<div class="wrap">';
				if($sessionid != null) {
					echo TXT_WPSC_THETRANSACTIONWASSUCCESSFUL."<br />";
					echo "<br />" . nl2br(str_replace("$",'\$',$message_html));
				}
				echo '</div>';
			}
		} else {
			if(true === $echo_to_screen) {    
				echo '<div class="wrap">';
				echo TXT_WPSC_BUYPRODUCTS;
				echo '</div>';
			}
		}
    
		if(($purchase_log['email_sent'] != 1) and ($sessionid != '')) {
		
			if(preg_match("/^[\w\s._,-]+$/",$transaction_id)) {
				$transact_id_sql = "`transactid` = '".$transaction_id."',";
			}
			
			$update_sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET $transact_id_sql `date` = '".time()."',`email_sent` = '1', `processed` = '$order_status' WHERE `sessionid` = ".$sessionid." LIMIT 1";
			$wpdb->query($update_sql) ;
		} 
	}
}
?>