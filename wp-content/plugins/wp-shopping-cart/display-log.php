<?php
/*
 * this updates the processing status of an item
 */
if(is_numeric($_GET['id']) && is_numeric($_GET['value'])) {
  $max_stage = $wpdb->get_var("SELECT MAX(*) AS `max` FROM `".$wpdb->prefix."purchase_statuses` WHERE `active`='1'");
  if(is_numeric($_GET['value']) && ($_GET['value'] <= $max_stage)) {
    $newvalue = $_GET['value'];
	} else {
		$newvalue = 1;
	}
  $log_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `id` = '".$_GET['id']."' LIMIT 1");  
  $update_sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '".$newvalue."' WHERE `id` = '".$_GET['id']."' LIMIT 1";  
  $wpdb->query($update_sql);
  if(($newvalue > $log_data['processed']) && ($log_data['processed'] <=1)) {
    transaction_results($log_data['sessionid'], false);
	}
}


if(is_numeric($_GET['deleteid'])) {
  $delete_id = $_GET['deleteid'];
  $delete_log_form_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='$delete_id'";
  $cart_content = $wpdb->get_results($delete_log_form_sql,ARRAY_A);
  foreach((array)$cart_content as $cart_item) {
    $cart_item_variations = $wpdb->query("DELETE FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id` = '".$cart_item['id']."'", ARRAY_A);
	}
  $wpdb->query("DELETE FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='$delete_id'");
  $wpdb->query("DELETE FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` IN ('$delete_id')");
  $wpdb->query("DELETE FROM `".$wpdb->prefix."purchase_logs` WHERE `id`='$delete_id' LIMIT 1");
  echo '<div id="message" class="updated fade"><p>'.TXT_WPSC_THANKS_DELETED.'</p></div>';
}



if(isset($_GET['clear_locks']) && ($_GET['clear_locks'] == 'true') && is_numeric($_GET['purchaseid'])) {
  $purchase_id = (int)$_GET['purchaseid'];
  $downloadable_items = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."download_status` WHERE `purchid` IN ('$purchase_id')", ARRAY_A);
  
  $clear_locks_sql = "UPDATE`".$wpdb->prefix."download_status` SET `ip_number` = '' WHERE `purchid` IN ('$purchase_id')";
  $wpdb->query($clear_locks_sql);
  
  
	$email_form_field = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type` IN ('email') AND `active` = '1' ORDER BY `order` ASC LIMIT 1");
	$email_address = $wpdb->get_var("SELECT `value` FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id`='{$purchase_id}' AND `form_id` = '{$email_form_field}' LIMIT 1");
	
	foreach($downloadable_items as $downloadable_item) {
	  $download_links .= $siteurl."?downloadid=".$downloadable_item['uniqueid']. "\n";
	}
	
	
	mail($email_address, TXT_WPSC_USER_UNLOCKED_EMAIL, str_replace("[download_links]", $download_links, TXT_WPSC_USER_UNLOCKED_EMAIL_MESSAGE), "From: ".get_option('return_email')."");
  
  echo '<div id="message" class="updated fade"><p>'.TXT_WPSC_THANKS_UNLOCKED.'</p></div>';
}


//echo("<pre>".print_r($cart_item,true)."</pre>");
/*
 * this finds the earliest time in the shopping cart and sorts out the timestamp system for the month by month display
 */  

$earliest_record_sql = "SELECT MIN(`date`) AS `date` FROM `".$wpdb->prefix."purchase_logs` WHERE `date`!=''";
$earliest_record = $wpdb->get_results($earliest_record_sql,ARRAY_A) ;

$current_timestamp = time();
$earliest_timestamp = $earliest_record[0]['date'];

$current_year = date("Y");
$earliest_year = date("Y",$earliest_timestamp);

$j = 0;
for($year = $current_year; $year >= $earliest_year; $year--) {
  for($month = 12; $month >= 1; $month--) {          
    $start_timestamp = mktime(0, 0, 0, $month, 1, $year);
    $end_timestamp = mktime(0, 0, 0, ($month+1), 1, $year);
    if(($end_timestamp >= $earliest_timestamp) && ($start_timestamp <= $current_timestamp)) {
      $date_list[$j]['start'] = $start_timestamp;
      $date_list[$j]['end'] = $end_timestamp;
      $j++;
		}
	}
}

if($_GET['filter'] !== 'true') {
  if(is_numeric($_GET['filter'])) {
    $max_number = $_GET['filter'];
	} else {
		if ($_GET['filter']=='paid') {
			$paidlog=true;
		}
		$max_number = 3;
	}
  
  $date_list = array_slice($date_list, 0, $max_number);
}

?>
<div class="wrap" style=''>
  <h2><?php echo TXT_WPSC_PURCHASELOG;?></h2><br />
   <?php
    echo wpsc_right_now();
    ?>

  <table style='width: 100%;'>
   <tr>  
    <td id='product_log_data'>
   <?php
  if(($purchase_log == null) && !is_numeric($_GET['purchaseid'])) {
    if($earliest_record[0]['date'] != null) {
      $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' AND `display_log` = '1';";
      $form_data = $wpdb->get_results($form_sql,ARRAY_A);
      
      $col_count = 5 + count($form_data);
      
      $i = 0;
      echo "<table class='logdisplay'>";

      //exit("<pre>".print_r($date_list,true)."</pre>");
      foreach($date_list as $date_pair){
        if(($date_pair['end'] >= $earliest_timestamp) && ($date_pair['start'] <= $current_timestamp)) {   
          $sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date` BETWEEN '".$date_pair['start']."' AND '".$date_pair['end']."' ORDER BY `date` DESC";
					if ($paidlog) {
						$sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date` BETWEEN '".$date_pair['start']."' AND '".$date_pair['end']."' AND `processed` >= '2' ORDER BY `date` DESC";
					} else if($_GET['filteremail']) {
						$sql = "SELECT DISTINCT `{$wpdb->prefix}purchase_logs` . * FROM `{$wpdb->prefix}submited_form_data` LEFT JOIN `{$wpdb->prefix}purchase_logs` ON `{$wpdb->prefix}submited_form_data`.`log_id` = `{$wpdb->prefix}purchase_logs`.`id` WHERE `{$wpdb->prefix}submited_form_data`.`value` IN ( '".$wpdb->escape($_GET['filteremail'])."' ) AND `{$wpdb->prefix}purchase_logs`.`date` BETWEEN '".$date_pair['start']."' AND '".$date_pair['end']."' ORDER BY `{$wpdb->prefix}purchase_logs`.`date` DESC;";
					}

          
          
          $purchase_log = $wpdb->get_results($sql,ARRAY_A) ;
          $i = 0;
          $subtotal = 0;
          echo "<tr>";
          echo " <td colspan='$col_count'>";
          echo "<h3 class='log_headers'>".date("M Y", $date_pair['start']) ."</h3>";
          echo " </td>";
          echo "</tr>";
          if($purchase_log != null)
            {
            echo "<tr class='toprow'>";

            //  echo " <td>";
            //  echo TXT_WPSC_PURCHASE_NUMBER;
            //  echo " </td>";

            echo " <td style='text-align: left;'>";
            echo TXT_WPSC_STATUS;
            echo " </td>";

            echo " <td>";
            echo TXT_WPSC_DATE;
            echo " </td>";

            foreach($form_data as $form_field)
              {
              echo " <td>";
              echo $form_field['name'];
              echo " </td>";
              }

            echo " <td>";
            echo TXT_WPSC_PRICE;
            echo " </td>";  

            if(get_option('payment_method') == 2)
              {
              echo " <td>";
              echo TXT_WPSC_PAYMENT_METHOD;
              echo " </td>";  
              }

            echo " <td>";
            echo TXT_WPSC_VIEWDETAILS;
            echo " </td>";

						//echo "<td width='1%'>";
						//echo TXT_WPSC_STORENAME;
						//echo "<td>";

            echo "</tr>";

            foreach($purchase_log as $purchase)
              {
              $status_state = "expand";
              $status_style = "";
              $alternate = "";
                $i++;
                if(($i % 2) != 0)
                  {
                  $alternate = "class='alt'";
                  }
              echo "<tr $alternate>\n\r";
              //  echo " <td>";
              //  echo $purchase['id'];
              //  echo " </td>";

              echo " <td class='processed'>";
              if($purchase['processed'] < 1)
                {
                $purchase['processed'] = 1;
                }
              $stage_sql = "SELECT * FROM `".$wpdb->prefix."purchase_statuses` WHERE `id`='".$purchase['processed']."' AND `active`='1' LIMIT 1";
              $stage_data = $wpdb->get_row($stage_sql,ARRAY_A);

              echo "<a href='#' onclick='return show_status_box(\"status_box_".$purchase['id']."\",\"log_expander_icon_".$purchase['id']."\");'>";
              if($_GET['id'] == $purchase['id'])
                {
                $status_state = "collapse";
                $status_style = "style='display: block;'";
                }
              echo "<img class='log_expander_icon' id='log_expander_icon_".$purchase['id']."' src='".WPSC_URL."/images/icon_window_$status_state.gif' alt='' title='' />";
              if($stage_data['colour'] != '')
                {
                $colour = "style='color: #".$stage_data['colour'].";'";
                }
              echo "<span $colour  id='form_group_".$purchase['id']."_text'>".$stage_data['name']."</span>";
              echo "</a>";
              echo " </td>\n\r";
        
              echo " <td>";
              echo date("jS M Y",$purchase['date']);
              echo " </td>\n\r";
            
              foreach($form_data as $form_field) {
                $collected_data_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".$form_field['id']."' LIMIT 1";
                $collected_data = $wpdb->get_results($collected_data_sql,ARRAY_A);
                $collected_data = $collected_data[0];
                switch($form_field['type']) {
                  case 'country': 
                  echo " <td>";
                  echo get_country($purchase['billing_country']);
                  echo " </td>\n\r";
                  break;

                  case 'delivery_country': 
                  echo " <td>";
                  echo get_country($purchase['shipping_country']);
                  echo " </td>\n\r";   
                  break;

                  default:
                  echo " <td>";
                  echo $collected_data['value'];
                  echo " </td>\n\r";
                  break;
								}
							}
        
              echo " <td>";

              if($purchase['shipping_country'] != '') {
                $billing_country = $purchase['billing_country'];
                $shipping_country = $purchase['shipping_country'];
							} else {
								$country_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
								$country_data = $wpdb->get_results($country_sql,ARRAY_A);
								$billing_country = $country_data[0]['value'];
								$shipping_country = $country_data[0]['value'];
							}
              //echo $country;
              echo nzshpcrt_currency_display(nzshpcrt_find_total_price($purchase['id'],$shipping_country),1);
              $subtotal += nzshpcrt_find_total_price($purchase['id'],$shipping_country);
              echo " </td>\n\r";

              if(get_option('payment_method') == 2) {
                echo " <td>";
                $gateway_name = '';
                foreach($GLOBALS['nzshpcrt_gateways'] as $gateway) {
                  if($purchase['gateway'] != 'testmode') {
                    if($gateway['internalname'] == $purchase['gateway'] ) {
                      $gateway_name = $gateway['name'];
										}
									} else {
										$gateway_name = "Manual Payment";
									}
								}
                echo $gateway_name;
                echo " </td>\n\r";
							}
              echo " <td>";
              echo "<a href='admin.php?page=".WPSC_DIR_NAME."/display-log.php&amp;purchaseid=".$purchase['id']."'>".TXT_WPSC_VIEWDETAILS."</a>";
              echo " </td>\n\r";
	
							//echo " <td>";
							//echo $purchase['closest_store'];
							//echo " </td>\n\r";
          
              echo "</tr>\n\r";
              
              $stage_list_sql = "SELECT * FROM `".$wpdb->prefix."purchase_statuses` ORDER BY `id` ASC";
              $stage_list_data = $wpdb->get_results($stage_list_sql,ARRAY_A);
              
              echo "<tr>\n\r";
              echo " <td colspan='$col_count'>\n\r";
              echo "  <div id='status_box_".$purchase['id']."' class='order_status' $status_style>\n\r";
              echo "  <div>\n\r";
              echo "  <strong class='form_group'>".TXT_WPSC_ORDER_STATUS."</strong>\n\r";
              echo "  <form onsubmit='log_submitform(this);return false;' id='form_group_".$purchase['id']."' method='GET' action='admin.php?page=".WPSC_DIR_NAME."/display-log.php'>\n\r";
              echo "  <input type='hidden' name='page' value='".$_GET['page']."' />\n\r";
              if(isset($_GET['filter']))
                {
                echo "  <input type='hidden' name='filter' value='".$_GET['filter']."' />\n\r";
                }
              echo "  <input type='hidden' name='id' value='".$purchase['id']."' />\n\r";
              //echo "  <input type='hidden' name='id' value='".$purchase['id']."' />\n\r";
              echo "  <ul>\n\r";
              foreach($stage_list_data as $stage)
                {
                $selected = '';
                if($stage['id'] == $purchase['processed'])
                  {
                  $selected = "checked='true'";
                  }
                $button_id = "button_".$purchase['id']."_".$stage['id'];
                echo "    <li><input type='radio' name='value' $selected value='".$stage['id']."' onclick='log_submitform(\"form_group_".$purchase['id']."\");' id='".$button_id."'/><label for='$button_id'>".$stage['name']."</label>\n\r";
		}

              echo "  </ul>\n\r";
              //echo "  <input type='submit' name='log_state_submit' value='Save... &raquo;' class='button' /> \n\r";
              echo "  </form>\n\r";
							echo "<li style='display:none;' id='track_id'>Tracking ID: <form method='GET'><input type='text' siez='20' id='tracking_id_".$purchase['id']."' name='track_id' value=".$purchase['track_id']."><input type = 'button' value='Submit' onclick='save_tracking_id(".$purchase['id'].");'></form></li>";
              if($purchase['transactid'] != '')
                {
                echo "  <span style='float:right; margin-right: 15px; '>".TXT_WPSC_TXN_ID.": ".$purchase['transactid']."</span>";
                }
              echo "<a href='admin.php?page=".WPSC_DIR_NAME."/display-log.php&amp;deleteid=".$purchase['id']."'>".TXT_WPSC_REMOVE_LOG."</a>";
              echo "  </div>\n\r";
              echo "  </div>\n\r";
              echo " </td>\n\r";
              echo "</tr>\n\r";
              }
              
            echo "<tr>";
            echo " <td colspan='$col_count'>";
            echo "<strong>Total:</strong> ".nzshpcrt_currency_display($subtotal ,1);
            echo "<br /><a class='admin_download' href='index.php?purchase_log_csv=true&rss_key=key&start_timestamp=".$date_pair['start']."&end_timestamp=".$date_pair['end']."' ><img align='absmiddle' src='".WPSC_URL."/images/download.gif' alt='' title='' /><span>".TXT_WPSC_DOWNLOAD_CSV."</span></a>";
            echo " </td>";      
            echo "</tr>";
            }
            else
              {
              echo "<tr>";
              echo " <td colspan='$col_count'>";
              echo "No transactions for this month.";
              echo " </td>";      
              echo "</tr>";
              }
          }
        }
      echo " </table>";
		} else {
			echo " <table>"; 
			echo "<tr>";
			echo " <td>";     
			echo TXT_WPSC_NO_PURCHASES;
			echo " </td>";      
			echo "</tr>";
			echo " </table>";
		}
	} else if(is_numeric($_GET['purchaseid'])) {

		$purch_sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `id`='".$_GET['purchaseid']."'";
		$purch_data = $wpdb->get_results($purch_sql,ARRAY_A) ;

		$cartsql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`=".$_GET['purchaseid']."";
		$cart_log = $wpdb->get_results($cartsql,ARRAY_A) ; 
		$j = 0;
		if($cart_log != null) {
			echo "<table class='logdisplay'>";
			echo "<tr class='toprow2'>";

			echo " <td>";
			echo TXT_WPSC_NAME;
			echo " </td>";

			echo " <td>";
			echo TXT_WPSC_QUANTITY;
			echo " </td>";
			
			echo " <td>";
			echo TXT_WPSC_PRICE;
			echo " </td>";

			echo " <td>";
			echo TXT_WPSC_TAX;
			echo " </td>";

			echo " <td>";
			echo TXT_WPSC_SHIPPING;
			echo " </td>";       

			echo " <td>";
			echo TXT_WPSC_TOTAL;
			echo " </td>";

			echo "</tr>";
			$endtotal = 0;
			$all_donations = true;
			$all_no_shipping = true;
			$file_link_list = array();
			foreach($cart_log as $cart_row)
				{
				$alternate = "";
				$j++;
				if(($j % 2) != 0)
					{
					$alternate = "class='alt'";
					}
				$productsql= "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`=".$cart_row['prodid']."";
				$product_data = $wpdb->get_results($productsql,ARRAY_A); 
			
				$variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$cart_row['id']."'";
				$variation_data = $wpdb->get_results($variation_sql,ARRAY_A); 
				$variation_count = count($variation_data);
				if($variation_count > 1)
					{
					$variation_list = " (";
					$i = 0;
					foreach($variation_data as $variation)
						{
						if($i > 0)
							{
							$variation_list .= ", ";
							}
						$value_id = $variation['value_id'];
						$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
						$variation_list .= $value_data[0]['name'];
						$i++;
						}
					$variation_list .= ")";
					}
					else if($variation_count == 1)
						{
						$value_id = $variation_data[0]['value_id'];
						$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
						$variation_list = " (".$value_data[0]['name'].")";
						}
						else
							{
							$variation_list = '';
							}

				if($purch_data[0]['shipping_country'] != '') {
					$billing_country = $purch_data[0]['billing_country'];
					$shipping_country = $purch_data[0]['shipping_country'];
				} else {
					$country_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$_GET['purchaseid']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
					$country_data = $wpdb->get_results($country_sql,ARRAY_A);
					$billing_country = $country_data[0]['value'];
					$shipping_country = $country_data[0]['value'];
				}

				if($cart_row['donation'] != 1) {
					$all_donations = false;
				}
				if($cart_row['no_shipping'] != 1) {
					$shipping = $cart_row['pnp'] * $cart_row['quantity'];
					$total_shipping += $shipping;            
					$all_no_shipping = false;
				} else {
					$shipping = 0;
				}
				
				if($cart_row['files'] != null) {
				  $file_data = unserialize($cart_row['files']);
				  if(is_array($file_data)) {
				    $file_link_list[] = "<a href='".WPSC_USER_UPLOADS_URL.$file_data['file_name']."'>{$product_data[0]['name']} </a><br />";
				  }
				}
				echo "<tr $alternate>";
		
				echo " <td>";
				echo $product_data[0]['name'];
				echo $variation_list;
				echo " </td>";
		
				echo " <td>";
				echo $cart_row['quantity'];
				echo " </td>";

				echo " <td>";
	
				$price = $cart_row['price'] * $cart_row['quantity'];
				$gst = $price - ($price  / (1+($cart_row['gst'] / 100)));
				echo nzshpcrt_currency_display($price-$gst, 1);
				echo " </td>";

				echo " <td>";
				
				echo nzshpcrt_currency_display($gst, 1);
				echo " </td>";

				echo " <td>";
				echo nzshpcrt_currency_display($cart_row['pnp'], 1);
				echo " </td>";

				echo " <td>";
				$endtotal += $price;
				echo nzshpcrt_currency_display(($shipping + $price), 1);
				echo " </td>";
							
				echo '</tr>';
				}
				echo "<tr >";
		
				echo " <td colspan='4'>";
				echo " </td>";

				echo " <td>";
				
				if($purch_data[0]['discount_value'] > 0) {
					echo "<strong>".TXT_WPSC_DISCOUNT.":</strong><br />"; 
				}
				
				
				if(($all_donations == false) && ($all_no_shipping == false)) {

				
					echo "<strong>".TXT_WPSC_BASESHIPPING.":</strong><br />";    
					echo "<strong>".TXT_WPSC_TOTALSHIPPING.":</strong><br />";    
				}
				echo "<strong>".TXT_WPSC_FINALTOTAL.":</strong>";
				echo " </td>";
		
				echo " <td>";
				if($purch_data[0]['discount_value'] > 0) {
					echo nzshpcrt_currency_display($purch_data[0]['discount_value'], 1)."<br />";
				}
									
				if(($all_donations == false) && ($all_no_shipping == false)) {
					echo nzshpcrt_currency_display($purch_data[0]['base_shipping'],1)."<br />";
					$total_shipping += $purch_data[0]['base_shipping'];
					$endtotal += $total_shipping;    
					echo nzshpcrt_currency_display($total_shipping, 1) . "<br />";
				}
				$endtotal -= $purch_data[0]['discount_value'];
				echo nzshpcrt_currency_display($endtotal,1);
				echo " </td>";
							
				echo '</tr>';
				
			echo "</table>";
			echo "<br />";
			
			
			if(count($file_link_list) > 0) {
			  echo "<p>\n\r";
				echo "  <strong>".TXT_WPSC_DOWNLOAD_ATTACHED_FILES."</strong><br />\n\r";
			  foreach($file_link_list as $file_link) {
			    echo "{$file_link}\n\r";
			  }
			  echo "</p>\n\r";
			}
			

			
			echo "<strong>".TXT_WPSC_CUSTOMERDETAILS."</strong>";
			echo "<table>";
			$form_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE  `log_id` = '".$_GET['purchaseid']."'";
			$input_data = $wpdb->get_results($form_sql,ARRAY_A);
			//exit("<pre>".print_r($input_data,true)."</pre>");
			if($input_data != null) {
				foreach($input_data as $form_field) {
					$form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' AND `id` = '".$form_field['form_id']."' LIMIT 1";
					$form_data = $wpdb->get_results($form_sql,ARRAY_A);
					if($form_data != null) {
						$form_data = $form_data[0];
						switch($form_data['type']) {
							case 'country': 
							if(is_numeric($purch_data[0]['shipping_region'])) {
								echo "  <tr><td>".TXT_WPSC_STATE.":</td><td>".get_region($purch_data[0]['shipping_region'])."</td></tr>";   
							}
							echo "  <tr><td>".$form_data['name'].":</td><td>".get_country($purch_data[0]['billing_country'])."</td></tr>";   
							break;           
									
							case 'delivery_country': 
							echo "  <tr><td>".$form_data['name'].":</td><td>".get_country($purch_data[0]['shipping_country'])."</td></tr>";   
							break;                
							
							default:
							echo "  <tr><td>".$form_data['name'].":</td><td>".$form_field['value']."</td></tr>";
							break;
						}
					}
				}
			} else {
					echo "  <tr><td>".TXT_WPSC_NAME.":</td><td>".$purch_data[0]['firstname']." ".$purch_data[0]['lastname']."</td></tr>";
					echo "  <tr><td>".TXT_WPSC_ADDRESS.":</td><td>".$purch_data[0]['address']."</td></tr>";
					echo "  <tr><td>".TXT_WPSC_PHONE.":</td><td>".$purch_data[0]['phone']."</td></tr>";
					echo "  <tr><td>".TXT_WPSC_EMAIL.":</td><td>".$purch_data[0]['email']."</td></tr>";
			}
			
			if(get_option('payment_method') == 2) {
				$gateway_name = '';
				foreach($GLOBALS['nzshpcrt_gateways'] as $gateway) {
					if($purch_data[0]['gateway'] != 'testmode') {
						if($gateway['internalname'] == $purch_data[0]['gateway'] ) {
							$gateway_name = $gateway['name'];
						}
					} else {
						$gateway_name = "Manual Payment";
					}
				}
			}
			echo "  <tr><td>".TXT_WPSC_PAYMENT_METHOD.":</td><td>".$gateway_name."</td></tr>";
			echo "  <tr><td>".TXT_WPSC_PURCHASE_NUMBER.":</td><td>".$purch_data[0]['id']."</td></tr>";
			echo "  <tr><td>".TXT_WPSC_HOWCUSTOMERFINDUS.":</td><td>".$purch_data[0]['find_us']."</td></tr>";
			$engrave_line = explode(",",$purch_data[0]['engravetext']);
			echo "  <tr><td>".TXT_WPSC_ENGRAVE."</td><td></td></tr>";
			echo "  <tr><td>Line1:</td><td>".$engrave_line[0]."</td></tr>";
			echo "  <tr><td>Line2:</td><td>".$engrave_line[1]."</td></tr>";
			if($purch_data[0]['transactid'] != '') {
				echo "  <tr><td>".TXT_WPSC_TXN_ID.":</td><td>".$purch_data[0]['transactid']."</td></tr>";
			}
			echo "</table>";
		} else {
			echo "<br />".TXT_WPSC_USERSCARTWASEMPTY;
		}
		echo "<br /><a href='admin.php?page=".WPSC_DIR_NAME."/display-log.php&amp;purchaseid=".$_GET['purchaseid']."&amp;clear_locks=true'>".TXT_WPSC_CLEAR_IP_LOCKS."</a><br />";
		echo "<br /><a href='admin.php?page=".WPSC_DIR_NAME."/display-log.php&amp;deleteid=".$_GET['purchaseid']."'>".TXT_WPSC_REMOVE_LOG."</a>";
		echo "<br /><a href='admin.php?page=".WPSC_DIR_NAME."/display-log.php'>".TXT_WPSC_GOBACK."</a>";
	}
      
$sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date`!=''";
$purchase_log = $wpdb->get_results($sql,ARRAY_A) ;
  ?>
   </td>
   
    <td id='order_summary_container'>
    <?php
		require_once (ABSPATH . WPINC . '/rss.php');
		$rss = fetch_rss('http://www.instinct.co.nz/feed/');	
		if($rss != null) {
			$rss->items = array_slice((array)$rss->items, 0, 5);
			$current_hash = sha1(serialize($rss->items));
			if((string)get_option('wpsc_ecom_news_hash') !== (string)$current_hash ) {
				?>
				<div class='wpsc_news'> 
					<strong><?php echo TXT_WPSC_ECOM_NEWS; ?></strong>
					<ul class='ecom_dashboard'>
					<?php
					foreach($rss->items as $items) {
						echo "<li><a href='".$items['link']."'>".$items['title']."</a></li>";
					}
					?>
					</ul>
					<a href='admin.php?page=<?php echo WPSC_DIR_NAME;?>/display-log.php&#038;hide_news=true' id='close_news_box'>X</a>
				</div>
				<?php
			}
    }
    ?>
	<div class='menu'> 
		<div class='order_summary_subsection'>
			<strong><?php echo TXT_WPSC_MENU; ?></strong>
			<p>
			<a href="?page=<?php echo WPSC_DIR_NAME;?>/options.php">Shop Settings</a><br>
			<a href="?page=<?php echo WPSC_DIR_NAME;?>/gatewayoptions.php">Gateway Settings</a><br>
			<a href="?page=<?php echo WPSC_DIR_NAME;?>/form_fields.php">Checkout Settings</a><br>
			<a href="?page=<?php echo WPSC_DIR_NAME;?>/instructions.php">Help</a>
			</p>
		</div>
	</div>
	<br>
    <strong class='order_summary'><?php echo TXT_WPSC_ORDER_SUMMARY; ?></strong>
    <div class='order_summary'> 
      <div class='order_summary_subsection'>
      
      <strong><?php echo TXT_WPSC_FILTER_ORDER; ?></strong>
      <p class='order_filters'>
      <form class='order_filters' method='GET' action='' name='order_filters'>
      <input type='hidden' name='page' value='<?php echo $_GET['page']?>' />
      <?php
      
      switch($_GET['filter'])
        {        
        case "true":
        $filter[1] = "checked='true'";
        break;
        
        case 3:
        default:
        $filter[0] = "checked='true'";
        break;
        
        case 1:
        default:
        $filter[2] = "checked='true'";
        break;
        }
      
      ?>
      <input class='order_filters' onclick='document.order_filters.submit();' type='radio' <?php echo $filter[0];?> name='filter' value='1' id='order_filter_1' /> <label class='order_filters' for='order_filter_1'><?php echo TXT_WPSC_LOG_CURRENT_MONTH; ?></label>
      <br />
      <input class='order_filters' onclick='document.order_filters.submit();' type='radio' <?php echo $filter[0];?> name='filter' value='3' id='order_filter_3' /> <label class='order_filters' for='order_filter_3'><?php echo TXT_WPSC_LOG_PAST_THREE_MONTHS; ?></label>
      <br />
			<input class='order_filters' onclick='document.order_filters.submit();'  type='radio' <?php echo $filter[1];?> name='filter' value='paid' id='order_filter_paid' /> <label class='order_filters' for='order_filter_paid'><?php echo TXT_WPSC_LOG_TRANSACTIONACCEPTEDLOGS; ?></label>
      <br />
      <input class='order_filters' onclick='document.order_filters.submit();'  type='radio' <?php echo $filter[1];?> name='filter' value='true' id='order_filter_none' /> <label class='order_filters' for='order_filter_none'><?php echo TXT_WPSC_LOG_ALL; ?></label>
      <br>
      <?=TXT_WPSC_SEARCHEMAIL?>: <input type='text' name='filteremail' >
      </form>
      <br />
      </p>
            
      <strong><?php echo TXT_WPSC_TOTAL_THIS_MONTH; ?></strong>
      <p id='log_total_month'>
      <?php 
      $year = date("Y");
      $month = date("m");
      $start_timestamp = mktime(0, 0, 0, $month, 1, $year);
      $end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
      echo nzshpcrt_currency_display(admin_display_total_price($start_timestamp, $end_timestamp),1);
      echo " ".TXT_WPSC_ACCEPTED_PAYMENTS;
      ?>
      </p>
      </div>
     
      
      <div class='order_summary_subsection'>
      <strong><?php echo TXT_WPSC_TOTAL_INCOME; ?></strong>
      <p id='log_total_absolute'>
      <?php
       //$total_income = $wpdb->get_results($sql,ARRAY_A);
       echo nzshpcrt_currency_display(admin_display_total_price(),1);
       ?>
      </p>
      </div>
      
      
     
      <div class='order_summary_subsection'>
      <strong><?php echo TXT_WPSC_RSS_FEED_HEADER; ?></strong>
      <p>
        <a class='product_log_rss' href='index.php?rss=true&amp;rss_key=key&amp;action=purchase_log'><img align='absmiddle' src='<?php echo WPSC_URL; ?>/images/rss-icon.jpg' alt='' title='' />&nbsp;<span><?php echo TXT_WPSC_RSS_FEED_LINK; ?></span></a> <?php echo TXT_WPSC_RSS_FEED_TEXT; ?>      </p>
      </div>
         <div class='order_summary_subsection'>
      <strong><?php echo TXT_WPSC_PLUGIN_NEWS_HEADER; ?></strong>
      <p>
      <?php echo TXT_WPSC_PLUGIN_NEWS; ?>        
        <br /><br /><?php echo TXT_WPSC_POWERED_BY; ?><a href='http://www.instinct.co.nz'>Instinct</a>
      </p>
      </div>
    </div>
    <?php
    if(get_option('activation_state') != "true")
      {
      ?>
      <div class='gold-cart_pesterer'> 
        <div>
        <img src='<?php echo WPSC_URL; ?>/images/gold-cart.png' alt='' title='' /><a href='http://www.instinct.co.nz/blogshop/'>Upgrade to Gold</a> and unleash more functionality into your shop.
        </div>
      </div>
      <?php
      }
    ?>
    </td>  
  </tr>
 </table>

</div>