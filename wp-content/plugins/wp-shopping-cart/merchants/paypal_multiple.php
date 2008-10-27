<?php
$nzshpcrt_gateways[$num]['name'] = 'Paypal';
$nzshpcrt_gateways[$num]['internalname'] = 'paypal_multiple';
$nzshpcrt_gateways[$num]['function'] = 'gateway_paypal_multiple';
$nzshpcrt_gateways[$num]['form'] = "form_paypal_multiple";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_paypal_multiple";

function gateway_paypal_multiple($seperator, $sessionid) {
  global $wpdb;
  $purchase_log_sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
  $purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;
//exit(print_r($purchase_log,1));
  $cart_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
  $cart = $wpdb->get_results($cart_sql,ARRAY_A) ;
  //written by allen
  //exit("<pre>".print_r($cart,true)."</pre>");
  $member_subtype = get_product_meta($cart[0]['prodid'],'is_permenant',true);
  $status = get_product_meta($cart[0]['prodid'],'is_membership',true);
  $is_member = $status;
  $is_perm = $member_subtype;
  //end of written by allen
  $transact_url = get_option('transact_url');
  // paypal connection variables
  $data['business'] = get_option('paypal_multiple_business');
  $data['return'] = urlencode($transact_url.$seperator."sessionid=".$sessionid."&gateway=paypal");
  $data['cancel_return'] = urlencode($transact_url);
  $data['notify_url'] =urlencode(get_option('siteurl')."/?ipn_request=true");
  $data['rm'] = '2';
  
  // look up the currency codes and local price

  $currency_code = $wpdb->get_results("SELECT `code` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".get_option('currency_type')."' LIMIT 1",ARRAY_A);
  $local_currency_code = $currency_code[0]['code'];
  $paypal_currency_code = get_option('paypal_curcode');
//exit(get_option('currency_type'). " ".$paypal_currency_code);

  // Stupid paypal only accepts payments in one of 5 currencies. Convert from the currency of the users shopping cart to the curency which the user has specified in their paypal preferences.
  $curr=new CURRENCYCONVERTER();
  
  $data['currency_code'] = $paypal_currency_code;
//   $data['lc'] = 'US';
  $data['lc'] = 'NZ';
  $data['bn'] = 'wp_e-commerce';
  $data['no_shipping'] = '2';
  if(get_option('address_override') == 1) {
		$data['address_override'] = '1';
	}
  $data['no_note'] = '1';
  
  switch($paypal_currency_code) {
    case "JPY":
    $decimal_places = 0;
    break;
    
    case "HUF":
    $decimal_places = 0;
    
    default:
    $decimal_places = 2;
    break;
	}
  
  $i = 1;
  
  $all_donations = true;
  $all_no_shipping = true;
  
  
$total = nzshpcrt_overall_total_price($_SESSION['selected_country'],false,true);

$discount = nzshpcrt_apply_coupon($total,$_SESSION['coupon_num']);
	if(($discount > 0) && ($_SESSION['coupon_num'] != null)) {
		$data['item_name_'.$i] = "Your Shopping Cart";
		$data['amount_'.$i] = number_format(sprintf("%01.2f", $discount),$decimal_places,'.','');
		$data['quantity_'.$i] = 1;
		// $data['item_number_'.$i] = 0;
		$data['shipping_'.$i] = 0;
		$data['shipping2_'.$i] = 0;
		$data['handling_'.$i] = 0;
		$i++;
	} else {
		foreach($cart as $item) {
			$product_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$item['prodid']."' LIMIT 1",ARRAY_A);
			$product_data = $product_data[0];
			$variation_count = count($product_variations);
			
			$variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$item['id']."'";
			$variation_data = $wpdb->get_results($variation_sql,ARRAY_A); 
			$variation_count = count($variation_data);
			if($variation_count >= 1) {
				$variation_list = " (";
				$j = 0;
				foreach($variation_data as $variation) {
					if($j > 0) {
						$variation_list .= ", ";
					}
					$value_id = $variation['value_id'];
					$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
					$variation_list .= $value_data[0]['name'];
					$j++;
				}
				$variation_list .= ")";
			} else {
				$variation_list = '';
			}
			
			
			$local_currency_productprice = $item['price'];
			$local_currency_shipping = $item['pnp'];
			
			//exit($local_currency_productprice . " " . $local_currency_code);
			if($paypal_currency_code != $local_currency_code) {
				$paypal_currency_productprice = $curr->convert($local_currency_productprice,$paypal_currency_code,$local_currency_code);
				$paypal_currency_shipping = $curr->convert($local_currency_shipping,$paypal_currency_code,$local_currency_code);
			} else {
				$paypal_currency_productprice = $local_currency_productprice;
				$paypal_currency_shipping = $local_currency_shipping;
			}
			//exit("---->".$paypal_currency_shipping);
			$data['item_name_'.$i] = urlencode(stripslashes($product_data['name']).$variation_list);
			$data['amount_'.$i] = number_format(sprintf("%01.2f", $paypal_currency_productprice),$decimal_places,'.','');
			$data['quantity_'.$i] = $item['quantity'];
			$data['item_number_'.$i] = $product_data['id'];
			if($item['donation'] !=1) {
				$all_donations = false;
				$data['shipping_'.$i] = number_format($paypal_currency_shipping,$decimal_places,'.','');
				$data['shipping2_'.$i] = number_format($paypal_currency_shipping,$decimal_places,'.','');      
			} else {
				$data['shipping_'.$i] = number_format(0,$decimal_places,'.','');
				$data['shipping2_'.$i] = number_format(0,$decimal_places,'.','');
			}
					
			if($product_data['no_shipping'] != 1) {
				$all_no_shipping = false;
			}
			
			$data['handling_'.$i] = '';
			$i++;
		}
	}
  $data['tax'] = '';

  $base_shipping = $purchase_log[0]['base_shipping'];
  //exit($base_shipping);
  if(($base_shipping > 0) && ($all_donations == false) && ($all_no_shipping == false)) {
    $data['handling_cart'] = number_format($base_shipping,$decimal_places,'.','');
	}
  
  $data['custom'] = '';
  $data['invoice'] = $sessionid;
  
  // User details   
  if($_POST['collected_data'][get_option('paypal_form_first_name')] != '') {
    $data['first_name'] = urlencode($_POST['collected_data'][get_option('paypal_form_first_name')]);
	}
    
  if($_POST['collected_data'][get_option('paypal_form_last_name')] != '') {   
    $data['last_name'] = urlencode($_POST['collected_data'][get_option('paypal_form_last_name')]);
	}
    
  if($_POST['collected_data'][get_option('paypal_form_address')] != '') {   
    $address_rows = explode("\n\r",$_POST['collected_data'][get_option('paypal_form_address')]);
    $data['address1'] = urlencode(str_replace(array("\n", "\r"), '', $address_rows[0]));
    unset($address_rows[0]);    
    if($address_rows != null) {
			$data['address2'] = implode(", ",$address_rows);
    } else {
			$data['address2'] = '';
    }
	}
  
	if($_POST['collected_data'][get_option('paypal_form_city')] != '') {
		$data['city'] = urlencode($_POST['collected_data'][get_option('paypal_form_city')]); 
	}
    
  if(preg_match("/^[a-zA-Z]{2}$/",$_SESSION['selected_country'])) {   
    $data['country'] = $_SESSION['selected_country'];
	}    
    
  if(is_numeric($_POST['collected_data'][get_option('paypal_form_post_code')])) {   
    $data['zip'] =  urlencode($_POST['collected_data'][get_option('paypal_form_post_code')]); 
	}    
    
  // Change suggested by waxfeet@gmail.com, if email to be sent is not there, dont send an email address        
  $email_data = $wpdb->get_results("SELECT `id`,`type` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type` IN ('email') AND `active` = '1'",ARRAY_A);
  foreach((array)$email_data as $email) {
    $data['email'] = $_POST['collected_data'][$email['id']];
	}
    
  if(($_POST['collected_data'][get_option('email_form_field')] != null) && ($data['email'] == null)) {
    $data['email'] = $_POST['collected_data'][get_option('email_form_field')];
	}
	
  $data['upload'] = '1';
  $data['cmd'] = "_ext-enter";
  $data['redirect_cmd'] = "_cart";
  $datacount = count($data);
  $num = 0;
  foreach($data as $key=>$value) {
    $amp = '&';
    $num++;
    if($num == $datacount) {
      $amp = '';
      }
    //$output .= $key.'='.urlencode($value).$amp;
    $output .= $key.'='.$value.$amp;
	}
  if(get_option('paypal_ipn') == 0) { //ensures that digital downloads still work for people without IPN, less secure, though
    //$wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '2' WHERE `sessionid` = ".$sessionid." LIMIT 1");
    }
	//written by allen
  if ($is_member == '1') {
		$membership_length = get_product_meta($cart[0]['prodid'],'membership_length',true);
		if ($is_perm == '1'){
			$permsub = '&src=1';
		} else {
			$permsub = '';
		}
		$output = 'cmd=_xclick-subscriptions&business='.urlencode($data['business']).'&no_note=1&item_name='.urlencode($data['item_name_1']).'&return='.urlencode($data['return']).'&cancel_return='.urlencode($data['cancel_return']).$permsub.'&a3='.urlencode($data['amount_1']).'&p3='.urlencode($membership_length['length']).'&t3='.urlencode(strtoupper($membership_length['unit']));
	}

	//   echo "<a href='".get_option('paypal_multiple_url')."?".$output."'>Test the URL here</a>";
	//   exit("<pre>".print_r($data,true)."</pre>");
  header("Location: ".get_option('paypal_multiple_url')."?".$output);
  exit();
}
  
function nzshpcrt_paypal_ipn()
  {
  global $wpdb;
  // needs to execute on page start
  // look at page 36
  if(($_GET['ipn_request'] == 'true') && (get_option('paypal_ipn') == 1)) {
    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-validate';
    $message = "";
    foreach ($_POST as $key => $value) {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
		}
    //$req .= "&ipn_request=true";
    $replace_strings[0] = 'http://';
    $replace_strings[1] = 'https://';
    $replace_strings[2] = '/cgi-bin/webscr';
    
    $paypal_url = str_replace($replace_strings, "",get_option('paypal_multiple_url'));
    
    // post back to PayPal system to validate
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    $fp = fsockopen ($paypal_url, 80, $errno, $errstr, 30);
    
    // assign posted variables to local variables
    $sessionid = $_POST['invoice'];
    $transaction_id = $_POST['txn_id'];
    $verification_data['item_name'] = $_POST['item_name'];
    $verification_data['item_number'] = $_POST['item_number'];
    $verification_data['payment_status'] = $_POST['payment_status'];
    $verification_data['payment_amount'] = $_POST['mc_gross'];
    $verification_data['payment_currency'] = $_POST['mc_currency'];
    $verification_data['txn_id'] = $_POST['txn_id'];
    $verification_data['receiver_email'] = $_POST['receiver_email'];
    $verification_data['payer_email'] = $_POST['payer_email'];
    
    if(!$fp) {
       //mail(get_option('purch_log_email'),'IPN CONNECTION FAILS IT',("Fix the paypal URL, it is currently:\n\r". $paypal_url));
      // HTTP ERROR
		} else {
      fputs ($fp, $header . $req);
      while (!feof($fp)) {
        $res = fgets ($fp, 1024);
        if(strcmp ($res, "VERIFIED") == 0){
          switch($verification_data['payment_status']) {
            case 'Processed': // I think this is mostly equivalent to Completed
            case 'Completed':
            $wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '2' WHERE `sessionid` = ".$sessionid." LIMIT 1");
            transaction_results($sessionid, false, $transaction_id);
            break;

            case 'Failed': // if it fails, delete it
            $log_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`='$sessionid' LIMIT 1");
            $delete_log_form_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='$log_id'";
            $cart_content = $wpdb->get_results($delete_log_form_sql,ARRAY_A);
            foreach((array)$cart_content as $cart_item) {
              $cart_item_variations = $wpdb->query("DELETE FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id` = '".$cart_item['id']."'", ARRAY_A);
						}
            $wpdb->query("DELETE FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='$log_id'");
            $wpdb->query("DELETE FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` IN ('$log_id')");
            $wpdb->query("DELETE FROM `".$wpdb->prefix."purchase_logs` WHERE `id`='$log_id' LIMIT 1");
            break;
            
            case 'Pending':      // need to wait for "Completed" before processing
            $sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET `transactid` = '".$transaction_id."', `date` = '".time()."'  WHERE `sessionid` = ".$sessionid." LIMIT 1";
            $wpdb->query($sql) ;
            break;
            
            default: // if nothing, do nothing, safest course of action here.
            break;            
					}
				} else if (strcmp ($res, "INVALID") == 0) {
					// Its already logged, not much need to do more
				}
			}
      fclose ($fp);
		}
    /*
     * Detect use of sandbox mode, if sandbox mode is present, send debugging email.
     */
     if(stristr(get_option('paypal_multiple_url'), "sandbox")) {
				$message = "This is a debugging message sent because it appears that you are using sandbox mode.\n\rIt is only sent if the paypal URL contains the word \"sandbox\"\n\r\n\r";
				$message .= "OUR_POST:\n\r".print_r($header . $req,true)."\n\r\n\r";
				$message .= "THEIR_POST:\n\r".print_r($_POST,true)."\n\r\n\r";
				$message .= "GET:\n\r".print_r($_GET,true)."\n\r\n\r";
				$message .= "SERVER:\n\r".print_r($_SERVER,true)."\n\r\n\r";
				$wpdb->query("INSERT INTO `paypal_log` ( `id` , `text` , `date` ) VALUES ( '', '$message', NOW( ) );");
				mail(get_option('purch_log_email'), "IPN Data", $message);
			}    
    }
  }
  

function submit_paypal_multiple(){
  if($_POST['paypal_multiple_business'] != null) {
    update_option('paypal_multiple_business', $_POST['paypal_multiple_business']);
	}
    
  if($_POST['paypal_multiple_url'] != null) {
    update_option('paypal_multiple_url', $_POST['paypal_multiple_url']);
	}
    
  if($_POST['paypal_curcode'] != null) {
    update_option('paypal_curcode', $_POST['paypal_curcode']);
	}
    
  if($_POST['paypal_curcode'] != null) {
    update_option('paypal_curcode', $_POST['paypal_curcode']);
	}
    
  if($_POST['paypal_ipn'] != null) {
    update_option('paypal_ipn', (int)$_POST['paypal_ipn']);
	}
  if($_POST['address_override'] != null) {
    update_option('address_override', (int)$_POST['address_override']);
	}
    
  foreach((array)$_POST['paypal_form'] as $form => $value) {
    update_option(('paypal_form_'.$form), $value);
	}
  return true;
}

function form_paypal_multiple() {
  $select_currency[get_option('paypal_curcode')] = "selected='true'";
  $output = "
  <tr>
      <td>
      PayPal Username
      </td>
      <td>
      <input type='text' size='40' value='".get_option('paypal_multiple_business')."' name='paypal_multiple_business' />
      </td>
  </tr>
  <tr>
      <td>
      PayPal Url
      </td>
      <td>
      <input type='text' size='40' value='".get_option('paypal_multiple_url')."' name='paypal_multiple_url' /> <br />
     <strong>Note:</strong>The URL to use for the paypal gateway is: https://www.paypal.com/cgi-bin/webscr
      </td>
  </tr>
  ";
  
  
	$paypal_ipn = get_option('paypal_ipn');
	$paypal_ipn1 = "";
	$paypal_ipn2 = "";
	switch($paypal_ipn) {
		case 0:
		$paypal_ipn2 = "checked ='true'";
		break;
		
		case 1:
		$paypal_ipn1 = "checked ='true'";
		break;
	}
		
	$output .= "
   <tr>
     <td>
      PayPal IPN
     </td>
     <td>
       <input type='radio' value='1' name='paypal_ipn' id='paypal_ipn1' ".$paypal_ipn1." /> <label for='paypal_ipn1'>".TXT_WPSC_YES."</label> &nbsp;
       <input type='radio' value='0' name='paypal_ipn' id='paypal_ipn2' ".$paypal_ipn2." /> <label for='paypal_ipn2'>".TXT_WPSC_NO."</label>
     </td>
  </tr>
  <tr>
      <td>
      PayPal Accepted Currency (e.g. USD, AUD)
      </td>
      <td>
        <select name='paypal_curcode'>
          <option ".$select_currency['USD']." value='USD'>U.S. Dollar</option>
          <option ".$select_currency['CAD']." value='CAD'>Canadian Dollar</option>
          <option ".$select_currency['AUD']." value='AUD'>Australian Dollar</option>
          <option ".$select_currency['EUR']." value='EUR'>Euro</option>
          <option ".$select_currency['GBP']." value='GBP'>Pound Sterling</option>
          <option ".$select_currency['JPY']." value='JPY'>Yen</option>
          <option ".$select_currency['NZD']." value='NZD'>New Zealand Dollar</option>
          <option ".$select_currency['CHF']." value='CHF'>Swiss Franc</option>
          <option ".$select_currency['HKD']." value='HKD'>Hong Kong Dollar</option>
          <option ".$select_currency['SGD']." value='SGD'>Singapore Dollar</option>
          <option ".$select_currency['SEK']." value='SEK'>Swedish Krona</option>
          <option ".$select_currency['HUF']." value='HUF'>Hungarian Forint</option>
          <option ".$select_currency['DKK']." value='DKK'>Danish Krone</option>
          <option ".$select_currency['PLN']." value='PLN'>Polish Zloty</option>
          <option ".$select_currency['NOK']." value='NOK'>Norwegian Krone</option>
          <option ".$select_currency['CZK']." value='CZK'>Czech Koruna</option>
        </select> 
      </td>
   </tr>";
   
	$address_override = get_option('address_override');
	$address_override1 = "";
	$address_override2 = "";
	switch($address_override) {
		case 1:
		$address_override1 = "checked ='true'";
		break;
		
		case 0:
		default:
		$address_override2 = "checked ='true'";
		break;
	}
     
$output .= "
   <tr>
     <td>
      Override the users address stored on paypal:
     </td>
     <td>
       <input type='radio' value='1' name='address_override' id='address_override1' ".$address_override1." /> <label for='address_override1'>".TXT_WPSC_YES."</label> &nbsp;
       <input type='radio' value='0' name='address_override' id='address_override2' ".$address_override2." /> <label for='address_override2'>".TXT_WPSC_NO."</label>
     </td>
   </tr>
   
   
   <tr class='update_gateway' >
		<td colspan='2'>
			<div class='submit'>
			<input type='submit' value='Update &raquo;' name='updateoption'/>
		</div>
		</td>
	</tr>
	
	<tr class='firstrowth'>
		<td style='border-bottom: medium none;' colspan='2'>
			<strong class='form_group'>Forms Sent to Gateway</strong>
		</td>
	</tr>
   
    <tr>
      <td>
      First Name Field
      </td>
      <td>
      <select name='paypal_form[first_name]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_first_name'))."
      </select>
      </td>
  </tr>
    <tr>
      <td>
      Last Name Field
      </td>
      <td>
      <select name='paypal_form[last_name]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_last_name'))."
      </select>
      </td>
  </tr>
    <tr>
      <td>
      Address Field
      </td>
      <td>
      <select name='paypal_form[address]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_address'))."
      </select>
      </td>
  </tr>
  <tr>
      <td>
      City Field
      </td>
      <td>
      <select name='paypal_form[city]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_city'))."
      </select>
      </td>
  </tr>
  <tr>
      <td>
      State Field
      </td>
      <td>
      <select name='paypal_form[state]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_state'))."
      </select>
      </td>
  </tr>
  <tr>
      <td>
      Postal code/Zip code Field
      </td>
      <td>
      <select name='paypal_form[post_code]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_post_code'))."
      </select>
      </td>
  </tr>
  <tr>
      <td>
      Country Field
      </td>
      <td>
      <select name='paypal_form[country]'>
      ".nzshpcrt_form_field_list(get_option('paypal_form_country'))."
      </select>
      </td>
  </tr> ";
  
  return $output;
}
  
  
add_action('init', 'nzshpcrt_paypal_ipn');
?>