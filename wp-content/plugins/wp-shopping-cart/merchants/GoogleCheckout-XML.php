<?php

require_once('library/googlecart.php');
require_once('library/googleitem.php');
require_once('library/googleshipping.php');
require_once('library/googletax.php');
require_once('library/googleresponse.php');
require_once('library/googlemerchantcalculations.php');
require_once('library/googleresult.php');
require_once('library/googlerequest.php');

$nzshpcrt_gateways[$num]['name'] = 'Google Checkout';
$nzshpcrt_gateways[$num]['internalname'] = 'google';
$nzshpcrt_gateways[$num]['function'] = 'gateway_google';
$nzshpcrt_gateways[$num]['form'] = "form_google";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_google";
$nzshpcrt_gateways[$num]['is_exclusive'] = true;

function gateway_google($seperator, $sessionid)
{
	Usecase($seperator, $sessionid);
	exit();
}

 function Usecase($seperator, $sessionid) {
	global $wpdb;
	$purchase_log_sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
	$purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;
	
	$cart_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
	$wp_cart = $wpdb->get_results($cart_sql,ARRAY_A) ; 
	$merchant_id = get_option('google_id');
	$merchant_key = get_option('google_key');
	$server_type = get_option('google_server_type');
	$currency = get_option('google_cur');
	$cart = new GoogleCart($merchant_id, $merchant_key, $server_type, $currency);
	$cart->SetContinueShoppingUrl(get_option('product_list_url'));
	$cart->SetEditCartUrl(get_option('shopping_cart_url'));
	$no=1;
	//exit("<pre>".print_r($wp_cart,true)."</pre>");
	foreach($wp_cart as $item){
		$product_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$item['prodid']."' LIMIT 1",ARRAY_A);
		$product_data = $product_data[0];
		$prohibited = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `product_id`='".$item['prodid']."' AND meta_key='google_prohibited' LIMIT 1",ARRAY_A);
		$prohibited_data = $prohibited_data[0];
		if (count($prohibited)>0){
			$_SESSION['google_prohibited']='1';
		} else {
			$_SESSION['google_prohibited']='0';
		}
		$variation_count = count($product_variations);
		
		$variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$item['id']."'";
		$variation_data = $wpdb->get_results($variation_sql,ARRAY_A); 
		$variation_count = count($variation_data);
		
		$extras_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_extras` WHERE `cart_id`='".$item['id']."'";
		$extras_data = $wpdb->get_results($extras_sql,ARRAY_A);
		$extras_count = count($extras_data);
		$price = nzshpcrt_calculate_tax($item['price'], $_SESSION['selected_country'], $_SESSION['selected_region']);
		if ($extras_count>0) {
			foreach ($extras_data as $extras_datum) {
				$price+=$wpdb->get_var("SELECT `price` FROM `".$wpdb->prefix."extras_values_associations` WHERE `product_id` = '".$item['prodid']."' AND `extras_id` = '".$extras_datum['extra_id']."' LIMIT 1");
			}
		}
		//exit("------->".$price);
		$local_currency_shipping = $item['pnp'];
		$base_shipping = $purchase_log[0]['base_shipping'];
		$total_shipping = $local_currency_shipping+$base_shipping;
		$cartitem["$no"] = new GoogleItem($product_data['name'],      // Item name
		$product_data['description'], // Item      description
		$item['quantity'], // Quantity
		$price); // Unit price
		$cart->AddItem($cartitem["$no"]);
		$no++;
	}
	// Add shipping options
	$Gfilter = new GoogleShippingFilters();
	$Gfilter->SetAllowedCountryArea('ALL');
	$google_checkout_shipping=get_option("google_shipping_country");
	$google_shipping_country_ids = implode(",",$google_checkout_shipping);
	$google_shipping_country = $wpdb->get_var("SELECT isocode FROM ".$wpdb->prefix."currency_list WHERE id IN (".$google_shipping_country_ids.")");
	$Gfilter->AddAllowedPostalArea($google_shipping_country);
	$ship_1 = new GoogleFlatRateShipping('Flat Rate Shipping', $total_shipping);
	$ship_1->AddShippingRestrictions($Gfilter);
	$cart->AddShipping($ship_1);

      // Add tax rules
	if ($_SESSION['selected_country']=='US'){
		$tax_rule = new GoogleDefaultTaxRule(0.05);
		$state_name = $wpdb->get_var("SELECT name FROM ".$wpdb->prefix."region_tax WHERE id='".$_SESSION['selected_region']."'");
		$tax_rule->SetStateAreas(array($state_name));
		$cart->AddDefaultTaxRules($tax_rule);
	}
	$_SESSION['nzshpcrt_cart'] = null;
	// Specify <edit-cart-url>
	// $cart->SetEditCartUrl("https://www.example.com/cart/");
	
	// Specify "Return to xyz" link
	//$cart->SetContinueShoppingUrl("https://www.example.com/goods/");
	
	// Request buyer's phone number
	//$cart->SetRequestBuyerPhone(true);
	
	// Display Google Checkout button
	
	echo $cart->CheckoutButtonCode("BIG");
}

function submit_google() {
	if($_POST['google_id'] != null) {
		update_option('google_id', $_POST['google_id']);
	}

	if($_POST['google_key'] != null) {
		update_option('google_key', $_POST['google_key']);
	}
	if($_POST['google_cur'] != null) {
		update_option('google_cur', $_POST['google_cur']);
	}
	if($_POST['google_button_size'] != null) {
		update_option('google_button_size', $_POST['google_button_size']);
	}
	if($_POST['google_button_bg'] != null) {
		update_option('google_button_bg', $_POST['google_button_bg']);
	}
	if($_POST['google_server_type'] != null) {
		update_option('google_server_type', $_POST['google_server_type']);
	}
	if($_POST['google_auto_charge'] != null) {
		update_option('google_auto_charge', $_POST['google_auto_charge']);
	}
  return true;
  }
  
function form_google()
  {
	if (get_option('google_button_size') == '0'){
		$button_size1="checked='checked'";
	} elseif(get_option('google_button_size') == '1') {
		$button_size2="checked='checked'";
	} elseif(get_option('google_button_size') == '2') {
		$button_size3="checked='checked'";
	}

	if (get_option('google_server_type') == 'sandbox'){
		$google_server_type1="checked='checked'";
	} elseif(get_option('google_server_type') == 'production') {
		$google_server_type2="checked='checked'";
	}
	
	if (get_option('google_auto_charge') == '1'){
		$google_auto_charge1="checked='checked'";
	} elseif(get_option('google_auto_charge') == '0') {
		$google_auto_charge2="checked='checked'";
	}

	if (get_option('google_button_bg') == 'trans'){
		$button_bg1="selected='true'";
	} else {
		$button_bg2="selected='true'";
	}
	$output = "
	<tr>
		<td>
		Google Checkout Merchant ID		</td>
		<td>
		<input type='text' size='40' value='".get_option('google_id')."' name='google_id' />
		</td>
	</tr>
	<tr>
		<td>
		Google Checkout Merchant Key
		</td>
		<td>
		<input type='text' size='40' value='".get_option('google_key')."' name='google_key' />
		</td>
	</tr>
	<tr>
		<td>
		Turn on auto charging 
		</td>
		<td>
			<input $google_auto_charge1 type='radio' name='google_auto_charge' value='1' /> Yes
			<input $google_auto_charge2 type='radio' name='google_auto_charge' value='0' /> No
		</td>
	</tr>
	<tr>
		<td>
		Google Checkout Server Type
		</td>
		<td>
			<input $google_server_type1 type='radio' name='google_server_type' value='sandbox' /> Sandbox (For testing)
			<input $google_server_type2 type='radio' name='google_server_type' value='production' /> Production
		</td>
	</tr>
	  <tr>
		  <td>
		  Select your currency
		  </td>
		  <td>
		  <select name='google_cur'>";
		  	if (get_option('google_cur') == 'USD') {
			$output.=
			"<option selected value='USD'>USD</option>
		  	<option value='GBP'>GBP</option>";
			} else {
			$output.=
			"<option value='USD'>USD</option>
		  	<option value='GBP' selected>GBP</option>";
			}
		  $output.="</select>
	</tr>

	<tr>
		<td>
		Select Shipping Countries for Google Checkout
		</td>
		<td>
		<a href='?page=wp-shopping-cart/gatewayoptions.php&googlecheckoutshipping=1'>Set Shipping countries
		</td>
	</tr>

	<tr>
		  <td>
		  Google Checkout Button Styles
		  </td>
			<td><div>Size:
				<input $button_size1 type='radio' name='google_button_size' value='0' /> 180&times;46
				<input $button_size2 type='radio' name='google_button_size' value='1' /> 168&times;44
				<input $button_size3 type='radio' name='google_button_size' value='2' /> 160&times;43
				</div>
				<div>
				Background:
		  <select name='google_button_bg'>
		  <option $button_bg1 value='trans'>Transparent</option>
		  <option $button_bg2 value='white'>White</option>
		  </div>				
			</td>
	</tr>

	<tr>
		<td colspan='2'>
				Note: Please put this link to your Google API callback url field on your Google checkout account: <strong>".get_option('siteurl')."/index.php</strong>
		</td>
	</tr>";
  return $output;
  }

function nzsc_googleResponse() {
	global $wpdb, $user_ID;
	$merchant_id = get_option('google_id');
	$merchant_key = get_option('google_key');
	$server_type = get_option('google_server_type');
	$currency = get_option('google_cur');
	
	define('RESPONSE_HANDLER_ERROR_LOG_FILE', 'library/googleerror.log');
	define('RESPONSE_HANDLER_LOG_FILE', 'library/googlemessage.log');
	if (stristr($_SERVER['HTTP_USER_AGENT'],"Google Checkout Notification Agent")) {
		$Gresponse = new GoogleResponse($merchant_id, $merchant_key);
		$xml_response = isset($HTTP_RAW_POST_DATA)?$HTTP_RAW_POST_DATA:file_get_contents("php://input");
		if (get_magic_quotes_gpc()) {
			$xml_response = stripslashes($xml_response);
		}
		list($root, $data) = $Gresponse->GetParsedXML($xml_response);

		$message = "<pre>".print_r($user_marketing_preference,1)."</pre>";
		
		$sessionid = (mt_rand(100,999).time());
		if ($root == "new-order-notification") {
			$_SESSION['nzshpcrt_cart'] = '';
			$cart_items = $data['new-order-notification']['shopping-cart']['items'];
			$user_marketing_preference=$data['new-order-notification']['buyer-marketing-preferences']['email-allowed']['VALUE'];
			$shipping_name = $data['new-order-notification']['buyer-shipping-address']['contact-name']['VALUE'];
			$shipping_name = explode(" ",$shipping_name);
			$shipping_firstname = $shipping_name[0];
			$shipping_lastname = $shipping_name[count($shipping_name)-1];
			$shipping_country = $data['new-order-notification']['buyer-shipping-address']['country-code']['VALUE'];
			$shipping_address1 = $data['new-order-notification']['buyer-shipping-address']['address1']['VALUE'];
			$shipping_address2 = $data['new-order-notification']['buyer-shipping-address']['address2']['VALUE'];
			$shipping_city = $data['new-order-notification']['buyer-shipping-address']['city']['VALUE'];
			$shipping_region = $data['new-order-notification']['buyer-shipping-address']['region']['VALUE'];
			$billing_name = $data['new-order-notification']['buyer-billing-address']['contact-name']['VALUE'];
			$billing_name = explode(" ",$shipping_name);
			$billing_firstname = $shipping_name[0];
			$billing_lastname = $shipping_name[count($shipping_name)-1];
			$billing_region = $data['new-order-notification']['buyer-billing-address']['region']['VALUE'];
			$billing_country = $data['new-order-notification']['buyer-billing-address']['country-code']['VALUE'];
			$total_price = $data['new-order-notification']['order-total']['VALUE'];
			$billing_email = $data['new-order-notification']['buyer-billing-address']['email']['VALUE'];
			$billing_phone = $data['new-order-notification']['buyer-billing-address']['phone']['VALUE'];
			$billing_address = $data['new-order-notification']['buyer-billing-address']['address1']['VALUE'];
			$billing_address .= " ".$data['new-order-notification']['buyer-billing-address']['address2']['VALUE'];
			$billing_address .= " ". $data['new-order-notification']['buyer-billing-address']['city']['VALUE'];
			$billing_city = $data['new-order-notification']['buyer-billing-address']['city']['VALUE'];
			$google_order_number = $data['new-order-notification']['google-order-number']['VALUE'];
			$pnp = $data['new-order-notification']['order-adjustment']['shipping']['flat-rate-shipping-adjustment']['shipping-cost']['VALUE'];
			//$tax = $data['new-order-notification']['order-adjustment'][];
			$Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type,$currency);
			$result = $Grequest->SendProcessOrder($google_order_number);
			$region_number = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."region_tax` WHERE code ='".$billing_region."'");
			$sql = "INSERT INTO `".$wpdb->prefix."purchase_logs` ( `totalprice` , `sessionid` , `date`, `billing_country`, `shipping_country`,`base_shipping`,`shipping_region`, `user_ID`, `discount_value`,`gateway`, `google_order_number`, `google_user_marketing_preference`) VALUES ( '".$total_price."', '".$sessionid."', '".time()."', '".$billing_country."', '".$shipping_country."', '".$pnp."','".$region_number."' , '".$user_ID."' , '".$_SESSION['wpsc_discount']."','".get_option('payment_gateway')."','".$google_order_number."','".$user_marketing_preference."')";
// 			mail('hanzhimeng@gmail.com',"",$sql);
			
			$wpdb->query($sql) ;
			$log_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid` IN('".$sessionid."') LIMIT 1") ;
			$sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET firstname='".$shipping_firstname."', lastname='".$shipping_lastname."', email='".$billing_email."', phone='".$billing_phone."' WHERE id='".$log_id."'";
			$wpdb->query($sql) ;
			if (array_key_exists(0,$cart_items['item'])) {
				$cart_items = $cart_items['item'];
			}
			//logging to submited_form_data
			$billing_fname_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='first_name' LIMIT 1") ;
			$sql = "INSERT INTO `".$wpdb->prefix."submited_form_data` (log_id, form_id, value) VALUES ('".$log_id."','".$billing_fname_id."','".$billing_firstname."')";
			//$wpdb->query($sql) ;
			$billing_lname_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='last_name' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$billing_lname_id."','".$billing_lastname."')";
			$billing_address_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='address' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$billing_address_id."','".$billing_address."')";
			$billing_city_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='city' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$billing_city_id."','".$billing_city."')";
			$billing_country_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='country' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$billing_country_id."','".$billing_country."')";
			$billing_state_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='state' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$billing_state_id."','".$billing_region."')";
			$shipping_fname_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='delivery_first_name' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$shipping_fname_id."','".$shipping_firstname."')";
			$shipping_lname_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='delivery_last_name' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$shipping_lname_id."','".$shipping_lastname."')";
			$shipping_address_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='delivery_address' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$shipping_address_id."','".$shipping_address1." ".$shipping_address2."')";
			$shipping_city_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='delivery_city' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$shipping_city_id."','".$shipping_city."')";
			$shipping_state_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='delivery_state' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$shipping_state_id."','".$shipping_region."')";
			$shipping_country_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type`='delivery_country' LIMIT 1") ;
			$sql .= ", ('".$log_id."','".$shipping_country_id."','".$shipping_country."')";
			$wpdb->query($sql) ;
			//$variations = $cart_item->product_variations;
			foreach($cart_items as $cart_item) {
				$product_id = $cart_item['merchant-item-id']['VALUE'];
				$item_name = $cart_item['item-name']['VALUE'];
				$item_desc = $cart_item['item-description']['VALUE'];
				$item_unit_price = $cart_item['unit-price']['VALUE'];
				$item_quantity = $cart_item['quantity']['VALUE'];
				$product_info = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE id='".$product_id."' LIMIT 1", ARRAY_A) ;
				$product_info = $product_info[0];
				//mail("hanzhimeng@gmail.com","",print_r($product_info,1));
				if($product_info['notax'] != 1) {
					//$price = nzshpcrt_calculate_tax($item_unit_price, $billing_country, $region_number);
					if(get_option('base_country') == $billing_country) {
						$country_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."currency_list` WHERE `isocode` IN('".get_option('base_country')."') LIMIT 1",ARRAY_A);
						if(($country_data['has_regions'] == 1)) {
							if(get_option('base_region') == $region_number) {
								$region_data = $wpdb->get_row("SELECT `".$wpdb->prefix."region_tax`.* FROM `".$wpdb->prefix."region_tax` WHERE `".$wpdb->prefix."region_tax`.`country_id` IN('".$country_data['id']."') AND `".$wpdb->prefix."region_tax`.`id` IN('".get_option('base_region')."') ",ARRAY_A) ;
							}
							$gst =  $region_data['tax'];
						} else {
							$gst =  $country_data['tax'];
						}
					} else {
						$gst = 0;
					}
				} else {
					$gst = 0;
				}
				
				if ($product_info['no_shipping'] == '0') {
					if ($shipping_country == get_option('base_country')) {
						$pnp = $product_info['pnp'];
					} else {
						$pnp = $product_info['international_pnp'];
					}
				} else {
					$pnp=0;
				}
				
				$cartsql = "INSERT INTO `".$wpdb->prefix."cart_contents` ( `prodid` , `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `donation`, `no_shipping` ) VALUES ('".$product_id."', '".$log_id."','".$item_unit_price."','".$pnp."', '".$gst."','".$item_quantity."', '".$product_info['donation']."', '".$product_info['no_shipping']."')";
				
				$wpdb->query($cartsql) ;
			}
		}
		
		if ($root == "order-state-change-notification") {
			$google_order_number = $data['order-state-change-notification']['google-order-number']['VALUE'];
			$google_status=$wpdb->get_var("SELECT google_status FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
			$google_status = unserialize($google_status);
			if (($google_status[0]!='Partially Charged') && ($google_status[0]!='Partially Refunded')) {
				$google_status[0]=$data['order-state-change-notification']['new-financial-order-state']['VALUE'];
				$google_status[1]=$data['order-state-change-notification']['new-fulfillment-order-state']['VALUE'];
			}
			$google_status = serialize($google_status);
			$sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET google_status='".$google_status."' WHERE google_order_number='".$google_order_number."'";
			$wpdb->query($sql) ;
			if (($data['order-state-change-notification']['new-financial-order-state']['VALUE'] == 'CHARGEABLE') && (get_option('google_auto_charge') == '1')) {
				$Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type,$currency);
				$result = $Grequest->SendChargeOrder($google_order_number);
				
				$_SESSION['nzshpcrt_cart'] = '';
				unset($_SESSION['coupon_num'], $_SESSION['google_session']);
				$sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET processed='2' WHERE google_order_number='".$google_order_number."'";
				$wpdb->query($sql) ;
			}
		}
		
		if ($root == "charge-amount-notification") {
			$google_order_number = $data['charge-amount-notification']['google-order-number']['VALUE'];
			$google_status=$wpdb->get_var("SELECT google_status FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
			$google_status = unserialize($google_status);
			$total_charged = $data['charge-amount-notification']['total-charge-amount']['VALUE'];
			$google_status['partial_charge_amount'] = $total_charged;
			$totalprice=$wpdb->get_var("SELECT totalprice FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
			if ($totalprice>$total_charged) {
				$google_status[0] = 'Partially Charged';
			} else if ($totalprice=$total_charged) {
				$google_status[0] = 'CHARGED';
			}
			$google_status = serialize($google_status);
			$sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET google_status='".$google_status."' WHERE google_order_number='".$google_order_number."'";
			$wpdb->query($sql) ;
		}
		
		if ($root == "refund-amount-notification") {
			$google_order_number = $data['refund-amount-notification']['google-order-number']['VALUE'];
			$google_status=$wpdb->get_var("SELECT google_status FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
			$google_status = unserialize($google_status);
			$total_charged = $data['refund-amount-notification']['total-refund-amount']['VALUE'];
			$google_status['partial_refund_amount'] = $total_charged;
			$totalprice=$wpdb->get_var("SELECT totalprice FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
			if ($totalprice>$total_charged) {
				$google_status[0] = 'Partially refunded';
			} else if ($totalprice=$total_charged) {
				$google_status[0] = 'REFUNDED';
			}
			$google_status = serialize($google_status);
			$sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET google_status='".$google_status."' WHERE google_order_number='".$google_order_number."'";
			$wpdb->query($sql) ;
		}
// 		<avs-response>Y</avs-response>
// 		<cvn-response>M</cvn-response>
		
		if ($root == "risk-information-notification") {
			$google_order_number = $data['risk-information-notification']['google-order-number']['VALUE'];
			$google_status=$wpdb->get_var("SELECT google_status FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
			$google_status = unserialize($google_status);
			$google_status['cvn']=$data['risk-information-notification']['risk-information']['cvn-response']['VALUE'];
			$google_status['avs']=$data['risk-information-notification']['risk-information']['avs-response']['VALUE'];
			$google_status['protection']=$data['risk-information-notification']['risk-information']['eligible-for-protection']['VALUE'];
			$google_status = serialize($google_status);
			$google_status=$wpdb->query("UPDATE ".$wpdb->prefix."purchase_logs SET google_status='".$google_status."' WHERE google_order_number='".$google_order_number."'");
			if ($data['risk-information-notification']['risk-information']['cvn-response']['VALUE'] == 'E') {
				$google_risk='cvn';
			}
			if (in_array($data['risk-information-notification']['risk-information']['avs-response']['VALUE'],array('N','U'))) {
				if (isset($google_risk)) {
					$google_risk = 'cvn+avs';
				} else {
					$google_risk='avs';
				}
			}
			if (isset($google_risk)) {
				$sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET google_risk='".$google_risk."' WHERE google_order_number='".$google_order_number."'";
				$wpdb->query($sql);
			}
		}
		
		if ($root == "order-state-change-notification") {
			$google_order_number = $data['order-state-change-notification']['google-order-number']['VALUE'];
			if ($data['order-state-change-notification']['new-financial-order-state']['VALUE'] == "CANCELLED_BY_GOOGLE") {
				$google_status = $wpdb->get_var("SELECT google_status FROM ".$wpdb->prefix."purchase_logs WHERE google_order_number='".$google_order_number."'");
				$google_status = unserialize($google_status);
				$google_status[0] = "CANCELLED_BY_GOOGLE";
				$wpdb->get_var("UPDATE ".$wpdb->prefix."purchase_logs SET google_status='".serialize($google_status)."' WHERE google_order_number='".$google_order_number."'");
			}
		}
// 		mail('hanzhimeng@gmail.com',"",$root . " <pre>". print_r($data,1)."</pre>");
		exit();
	}
}
add_action('init', 'nzsc_googleResponse');
?>