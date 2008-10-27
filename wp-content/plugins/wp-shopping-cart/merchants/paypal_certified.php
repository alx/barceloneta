<?php

$nzshpcrt_gateways[$num]['name'] = 'Paypal - Express Checkout';
$nzshpcrt_gateways[$num]['internalname'] = 'paypal_certified';
$nzshpcrt_gateways[$num]['function'] = 'gateway_paypal_certified';
$nzshpcrt_gateways[$num]['form'] = "form_paypal_certified";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_paypal_certified";

function gateway_paypal_certified($seperator, $sessionid)
  {
  global $wpdb;
  $purchase_log_sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
  $purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;

  $cart_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
  $cart = $wpdb->get_results($cart_sql,ARRAY_A) ; 
  
  $transact_url = get_option('transact_url');
  // paypal connection variables
  $data['business'] = get_option('paypal_multiple_business');
  $data['return'] = $transact_url.$seperator."sessionid=".$sessionid."&gateway=paypal";
  $data['cancel_return'] = $transact_url;
  $data['notify_url'] =get_option('siteurl')."/?ipn_request=true";
  $data['rm'] = '2';
  
  // look up the currency codes and local price
  $currency_code = $wpdb->get_results("SELECT `code` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".get_option(currency_type)."' LIMIT 1",ARRAY_A);
  $local_currency_code = $currency_code[0]['code'];
  $paypal_currency_code = get_option('paypal_curcode');

  // Stupid paypal only accepts payments in one of 5 currencies. Convert from the currency of the users shopping cart to the curency which the user has specified in their paypal preferences.
  $curr=new CURRENCYCONVERTER();
  
  $data['currency_code'] = $paypal_currency_code;
  $data['Ic'] = 'US';
  $data['bn'] = 'toolkit-php';
  $data['no_shipping'] = '2';
  $data['address_override'] = '1';
  $data['no_note'] = '1';
  
  switch($paypal_currency_code)
    {
    case "JPY":
    $decimal_places = 0;
    break;
    
    case "HUF":
    $decimal_places = 0;
    
    default:
    $decimal_places = 2;
    break;
    }
 	
	
	
  header("Location: ".get_option('paypal_multiple_url')."?".$output);
  exit();
  }
  

function submit_paypal_certified()
  {  
  if($_POST['paypal_certified_apiuser'] != null)
    {
    update_option('paypal_certified_apiuser', $_POST['paypal_certified_apiuser']);
    }
    
  if($_POST['paypal_certified_apipass'] != null)
    {
    update_option('paypal_certified_apipass', $_POST['paypal_certified_apipass']);
    }
    
  if($_POST['paypal_curcode'] != null)
    {
    update_option('paypal_curcode', $_POST['paypal_curcode']);
    }
    
  if($_POST['paypal_certified_apisign'] != null)
    {
    update_option('paypal_certified_apisign', $_POST['paypal_certified_apisign']);
    }
  return true;
  }

function form_paypal_certified()
  {
  $select_currency[get_option('paypal_curcode')] = "selected='true'";
  $output = "
  <tr>
      <td>
      PayPal API Username
      </td>
      <td>
      <input type='text' size='40' value='".get_option('paypal_certified_apiuser')."' name='paypal_certified_apiuser' />
      </td>
  </tr>
  <tr>
      <td>
      PayPal API Password
      </td>
      <td>
      <input type='text' size='40' value='".get_option('paypal_certified_apipass')."' name='paypal_certified_apipass' />
      </td>
  </tr>
  <tr>
     <td>
	 PayPal API Signature
     </td>
     <td>
     <input type='text' size='70' value='".get_option('paypal_certified_apisign')."' name='paypal_certified_apisign' />
     </td>
  </tr>
  ";
  
$output .= "
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
   </tr>
   
   
	<tr class='update_gateway' >
		<td colspan='2'>
			<div class='submit'>
			<input type='submit' value='Update &raquo;' name='updateoption'/>
		</div>
		</td>
	</tr>
	
    <tr style='background: none;'>
      <td colspan='2'>
				<h4>Forms Sent to Gateway</h2>
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
  </tr>
";
  return $output;
  }
?>