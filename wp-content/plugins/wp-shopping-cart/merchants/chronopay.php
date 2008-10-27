<?php
$nzshpcrt_gateways[$num]['name'] = 'Chronopay';
$nzshpcrt_gateways[$num]['internalname'] = 'chronopay';
$nzshpcrt_gateways[$num]['function'] = 'gateway_chronopay';
$nzshpcrt_gateways[$num]['form'] = "form_chronopay";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_chronopay";

function gateway_chronopay($seperator, $sessionid)
{
	global $wpdb;
	$purchase_log_sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1";
	$purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;

	$cart_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='".$purchase_log[0]['id']."'";
	$cart = $wpdb->get_results($cart_sql,ARRAY_A) ; 
  
	// Chronopay post variables
	$chronopay_url = get_option('chronopay_url');
	
	$data['product_id'] = get_option('chronopay_product_id');
	$data['product_name'] = get_option('chronopay_product_name');
	$data['product_price_currency'] = get_option('chronopay_curcode');
	$data['language'] = get_option('chronopay_language');
	$data['cb_url'] = get_option('siteurl')."/?chronopay_callback=true";
	$data['cb_type'] = 'P';
	$data['decline_url'] = get_option('siteurl')."/?chronopay_callback=true";
	$data['cs1'] = $sessionid;
	$data['cs2'] = 'chronopay';
	$salt = get_option('chronopay_salt');
	$data['cs3'] = md5($salt . md5($sessionid . $salt));	// placed in here for security so that the return call can be validated as 'real'	
		
	// User details   
	if($_POST['collected_data'][get_option('chronopay_form_first_name')] != '')
    {   
    	$data['f_name'] = $_POST['collected_data'][get_option('chronopay_form_first_name')];
    }
	if($_POST['collected_data'][get_option('chronopay_form_last_name')] != "")
    {   
    	$data['s_name'] = $_POST['collected_data'][get_option('chronopay_form_last_name')];
    }
  	if($_POST['collected_data'][get_option('chronopay_form_address')] != '')
    {   
    	$data['street'] = str_replace("\n",', ', $_POST['collected_data'][get_option('chronopay_form_address')]); 
    }
   	if($_POST['collected_data'][get_option('chronopay_form_city')] != '')
    {
    	$data['city'] = $_POST['collected_data'][get_option('chronopay_form_city')]; 
    }
  	if(preg_match("/^[a-zA-Z]{2}$/",$_SESSION['selected_country']))
    {   
    	$data['country'] = $_SESSION['selected_country'];
    }    

  	// Change suggested by waxfeet@gmail.com, if email to be sent is not there, dont send an email address        
  	$email_data = $wpdb->get_results("SELECT `id`,`type` FROM `".$wpdb->prefix."collect_data_forms` WHERE `type` IN ('email') AND `active` = '1'",ARRAY_A);
  	foreach((array)$email_data as $email)
    {
    	$data['email'] = $_POST['collected_data'][$email['id']];
    }
  	if(($_POST['collected_data'][get_option('email_form_field')] != null) && ($data['email'] == null))
    {
    	$data['email'] = $_POST['collected_data'][get_option('email_form_field')];
    }
	
	
	// Get Currency details abd price
	$currency_code = $wpdb->get_results("SELECT `code` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".get_option(currency_type)."' LIMIT 1",ARRAY_A);
	$local_currency_code = $currency_code[0]['code'];
	$chronopay_currency_code = get_option('chronopay_curcode');
  
	// Chronopay only processes in the set currency.  This is USD or EUR dependent on what the Chornopay account is set up with.  
	// This must match the Chronopay settings set up in wordpress.  Convert to the chronopay currency and calculate total.
	$curr=new CURRENCYCONVERTER();
	$decimal_places = 2;
	$total_price = 0;
  
	$i = 1;
  
	$all_donations = true;
	$all_no_shipping = true;
	
	foreach($cart as $item)
	{
		$product_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$item['prodid']."' LIMIT 1",ARRAY_A);
		$product_data = $product_data[0];
		$variation_count = count($product_variations);
    
		$variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$item['id']."'";
		$variation_data = $wpdb->get_results($variation_sql,ARRAY_A);
		$variation_count = count($variation_data);

		if($variation_count >= 1)
      	{
      		$variation_list = " (";
      		$j = 0;
      		foreach($variation_data as $variation)
        	{
        		if($j > 0)
          		{
          			$variation_list .= ", ";
          		}
        		$value_id = $variation['venue_id'];
        		$value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
        		$variation_list .= $value_data[0]['name'];              
        		$j++;
        	}
      		$variation_list .= ")";
      	}
      	else
        {
        	$variation_list = '';
        }
    
    	$local_currency_productprice = $item['price'];

		$local_currency_shipping = nzshpcrt_determine_item_shipping($item['prodid'], 1, $_SESSION['delivery_country']);
    	
		if($chronopay_currency_code != $local_currency_code)
      	{
      		$chronopay_currency_productprice = $curr->convert($local_currency_productprice,$chronopay_currency_code,$local_currency_code);
      		$chronopay_currency_shipping = $curr->convert($local_currency_shipping,$chronopay_currency_code,$local_currency_code);
      	}
      	else
        {
        	$chronopay_currency_productprice = $local_currency_productprice;
        	$chronopay_currency_shipping = $local_currency_shipping;
        }
    	$data['item_name_'.$i] = $product_data['name'].$variation_list;
    	$data['amount_'.$i] = number_format(sprintf("%01.2f", $chronopay_currency_productprice),$decimal_places,'.','');
    	$data['quantity_'.$i] = $item['quantity'];
    	$data['item_number_'.$i] = $product_data['id'];
    	
		if($item['donation'] !=1)
      	{
      		$all_donations = false;
      		$data['shipping_'.$i] = number_format($chronopay_currency_shipping,$decimal_places,'.','');
      		$data['shipping2_'.$i] = number_format($chronopay_currency_shipping,$decimal_places,'.','');      
      	}
      	else
      	{
      		$data['shipping_'.$i] = number_format(0,$decimal_places,'.','');
      		$data['shipping2_'.$i] = number_format(0,$decimal_places,'.','');
      	}
        
    	if($product_data['no_shipping'] != 1) {
      		$all_no_shipping = false;
      	}
    
		
		$total_price = $total_price + ($data['amount_'.$i] * $data['quantity_'.$i]);

		if( $all_no_shipping != false )
			$total_price = $total_price + $data['shipping_'.$i] + $data['shipping2_'.$i];

    	$i++;
	}
   
  	$base_shipping = nzshpcrt_determine_base_shipping(0, $_SESSION['delivery_country']);
  	if(($base_shipping > 0) && ($all_donations == false) && ($all_no_shipping == false))
    {
    	if($chronopay_currency_code != $local_currency_code)
		{
			$base_shipping = $curr->convert($base_shipping,$chronopay_currency_code,$local_currency_code);
		}
		$data['handling_cart'] = number_format($base_shipping,$decimal_places,'.','');
		$total_price += number_format($base_shipping,$decimal_places,'.','');
    }

	$data['product_price'] = $total_price;


	// Create Form to post to Chronopay
	$output = "
		<form id=\"chronopay_form\" name=\"chronopay_form\" method=\"post\" action=\"$chronopay_url\">\n";
	
	foreach($data as $n=>$v) {
			$output .= "			<input type=\"hidden\" name=\"$n\" value=\"$v\" />\n";
	}
	
	$output .= "			<input type=\"submit\" value=\"Continue to ChronoPay\" />
		</form>
	";

	// echo form.. 
	if( get_option('chronopay_debug') == 1)
	{
		echo ("DEBUG MODE ON!!<br/>");
		echo("The following form is created and would be posted to Chronopay for processing.  Press submit to continue:<br/>");
		echo("<pre>".htmlspecialchars($output)."</pre>");
	}
	
	echo($output);
	
	if(get_option('chronopay_debug') == 0)
	{
		echo "<script language=\"javascript\" type=\"text/javascript\">document.getElementById('chronopay_form').submit();</script>";
	}

  	exit();
}
  
function nzshpcrt_chronopay_callback()
{
	global $wpdb;
	// needs to execute on page start
	// look at page 36
	if($_GET['chronopay_callback'] == 'true' && $_POST['cs2'] == 'chronopay')
	{
    	// This is a call from chronopay.  validate that it is from a chronopay server in the and process.
		// validate cs3 variable to see if it makes sense for security
		$salt = get_option('chronopay_salt');
		$gen_hash = md5($salt . md5($_POST['cs1'] . $salt));	
		
		if($gen_hash == $_POST['cs3'])
		{
			// Added in to fake a TX number for testing.  ChronoPay dev accounts do not return a trans_id.
			//if($_POST['transaction_id'] == '')
			//	$_POST['transaction_id'] = 'testid123123';
				
			// process response.
		    $sessionid = trim(stripslashes($_POST['cs1']));
			$transaction_id = trim(stripslashes($_POST['transaction_id']));
			$verification_data['trans_id'] = trim(stripslashes($_POST['transaction_id']));
			$verification_data['trans_type'] = trim(stripslashes($_POST['transaction_type']));

			switch($verification_data['trans_type'])
			{
				case 'onetime': // All successful processing statuses.
	            case 'initial':
				case 'rebill':
	            	$wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET 
										`processed` = '2', 
										`transactid` = '".$transaction_id."', 
										`date` = '".time()."'
									WHERE `sessionid` = ".$sessionid." LIMIT 1");
					
					transaction_results($sessionid, false, $transaction_id);
	            	break;                        
            
	            case 'decline': // if it fails, delete it
	            	$log_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`='$sessionid' LIMIT 1");
	            	$delete_log_form_sql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`='$log_id'";
	            	$cart_content = $wpdb->get_results($delete_log_form_sql,ARRAY_A);
	            	foreach((array)$cart_content as $cart_item)
	              	{
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
		}
		else
		{
			// Security Hash failed!!.. notify someone.. 
			$message = "This message has been sent because a call to your ChronoPay function was made by a server that did not have the correct security key.  This could mean someone is trying to hack your payment site.  The details of the call are below.\n\r\n\r";
			$message .= "OUR_POST:\n\r".print_r($header . $req,true)."\n\r\n\r";
			$message .= "THEIR_POST:\n\r".print_r($_POST,true)."\n\r\n\r";
			$message .= "GET:\n\r".print_r($_GET,true)."\n\r\n\r";
			$message .= "SERVER:\n\r".print_r($_SERVER,true)."\n\r\n\r";
			mail(get_option('purch_log_email'), "ChronoPay Security Key Failed!", $message);
		}
			
		// If in debug, email details
		if(get_option('chronopay_debug') == 1)
		{
			$message = "This is a debugging message sent because it appears that you are in debug mode.\n\rEnsure ChronoPay debug is turned off once you are happy with the function.\n\r\n\r";
			$message .= "OUR_POST:\n\r".print_r($header . $req,true)."\n\r\n\r";
			$message .= "THEIR_POST:\n\r".print_r($_POST,true)."\n\r\n\r";
			$message .= "GET:\n\r".print_r($_GET,true)."\n\r\n\r";
			$message .= "SERVER:\n\r".print_r($_SERVER,true)."\n\r\n\r";
			mail(get_option('purch_log_email'), "ChronoPay Data", $message);
		}
	} 
}

function nzshpcrt_chronopay_results()
{
	// Function used to translate the ChronoPay returned cs1=sessionid POST variable into the recognised GET variable for the transaction results page.
	if($_POST['cs1'] !='' && $_GET['sessionid'] == '')
	{
		$_GET['sessionid'] = $_POST['cs1'];
	}
}

function submit_chronopay()
{  
	if($_POST['chronopay_product_id'] != null)
    {
    	update_option('chronopay_product_id', $_POST['chronopay_product_id']);
    }
    
  	if($_POST['chronopay_product_name'] != null)
    {
    	update_option('chronopay_product_name', $_POST['chronopay_product_name']);
    }
    
  	if($_POST['chronopay_curcode'] != null)
    {
    	update_option('chronopay_curcode', $_POST['chronopay_curcode']);
    }
    
  	if($_POST['chronopay_language'] != null)
    {
    	update_option('chronopay_language', $_POST['chronopay_language']);
    }
    
  	if($_POST['chronopay_url'] != null)
    {
    	update_option('chronopay_url', $_POST['chronopay_url']);
    }

 	if($_POST['chronopay_salt'] != null)
    {
    	update_option('chronopay_salt', $_POST['chronopay_salt']);
    }

  	if($_POST['chronopay_debug'] != null)
    {
    	update_option('chronopay_debug', $_POST['chronopay_debug']);
    }
    
	foreach((array)$_POST['chronopay_form'] as $form => $value)
    {
    	update_option(('chronopay_form_'.$form), $value);
    }
	return true;
}

function form_chronopay()
{	
	$select_currency[get_option('chronopay_curcode')] = "selected='true'";
	$select_language[get_option('chronopay_language')] = "selected='true'";
	$chronopay_url = ( get_option('chronopay_url')=='' ? 'https://secure.chronopay.com/index_shop.cgi' : get_option('chronopay_url') );
	$chronopay_salt = ( get_option('chronopay_salt')=='' ? 'changeme' : get_option('chronopay_salt') );
	
	$chronopay_debug = get_option('chronopay_debug');
	$chronopay_debug1 = "";
	$chronopay_debug2 = "";
	switch($chronopay_debug)
	{
		case 0:
			$chronopay_debug2 = "checked ='true'";
			break;
		case 1:
			$chronopay_debug1 = "checked ='true'";
			break;
	}

	$output = "
		<tr>
			<td>ChronoPay Product ID</td>
			<td><input type='text' size='40' value='".get_option('chronopay_product_id')."' name='chronopay_product_id' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>This should be set to your product ID that has been set up in the ChronoPay client interface.
			This is the ChronoPay product that all purchases will be processed against. The cost will be changed depending on the grand total of the users cart.</small></td>
		</tr>
		<tr>
			<td>ChronoPay Product Name</td>
			<td><input type='text' size='40' value='".get_option('chronopay_product_name')."' name='chronopay_product_name' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>This is not important and is usually set to the name of the web shop. It is displayed on the ChronoPay secure processing page.</small></td>
		</tr>
		<tr>
			<td>ChronoPay Accepted Currency (USD, EUR)</td>
			<td><select name='chronopay_curcode'>
					<option ".$select_currency['USD']." value='USD'>USD - U.S. Dollar</option>
					<option ".$select_currency['EUR']." value='EUR'>EUR - Euros</option>
				</select> 
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>The currency code that ChronoPay will process the payment in. All products must be set up in this currency.</small></td>
		</tr>
		<tr>
			<td>ChronoPay Language</td>
			<td><select name='chronopay_language'>
					<option ".$select_language['EN']." value='EN'>Engish</option>
					<option ".$select_language['ES']." value='ES'>Spanish</option>
					<option ".$select_language['NL']." value='NL'>Dutch</option>
					<option ".$select_language['RU']." value='RU'>Russian</option>
				</select> 
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>The language that the ChronoPay secure processing page will be displayed in.</small></td>
		</tr>
		<tr>
			<td>ChronoPay processing URL</td>
			<td><input type='text' size='40' value='".$chronopay_url."' name='chronopay_url' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>URL of the secure payment page customers are sent to for payment processing. If unsure leave at default setting.</small></td>
		</tr>
		<tr>
			<td>ChronoPay return URL</td>
			<td><input type='text' size='40' value='".get_option('transact_url')."' name='chronopay_return_url' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Enter this URL in the ChronoPay web client against the Product ID that you have set up. This page is the transaction details page that you have configured in Shop Options.  It can not be edited on this page.</small></td>
		</tr>
		<tr>
			<td>ChronoPay Security Key</td>
			<td><input type='text' size='40' value='".$chronopay_salt."' name='chronopay_salt' /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>A bit of security... This is a keyword that is used to ensure transaction approval calls from ChronoPay to this application are real and were instigated from this server.  Enter a unique word into this field.</small></td>
		</tr>
		<tr>
			<td>ChronoPay Debug Mode</td>
			<td>
				<input type='radio' value='1' name='chronopay_debug' id='chronopay_debug1' ".$chronopay_debug1." /> <label for='chronopay_debug1'>".TXT_WPSC_YES."</label> &nbsp;
				<input type='radio' value='0' name='chronopay_debug' id='chronopay_debug2' ".$chronopay_debug2." /> <label for='chronopay_debug2'>".TXT_WPSC_NO."</label>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><small>Debug mode is used to write HTTP communications between the ChronoPay server and your host to a log file.  This should only be activated for testing!</small></td>
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
			<td>First Name Field</td>
			<td><select name='chronopay_form[first_name]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_first_name'))."
				</select>
			</td>
		</tr>
		<tr>
			<td>Last Name Field</td>
			<td><select name='chronopay_form[last_name]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_last_name'))."
				</select>
			</td>
		</tr>
		<tr>
			<td>Address Field</td>
			<td><select name='chronopay_form[address]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_address'))."
				</select>
			</td>
		</tr>
		<tr>
			<td>City Field</td>
			<td><select name='chronopay_form[city]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_city'))."
				</select>
			</td>
		</tr>
		<tr>
			<td>State Field</td>
			<td><select name='chronopay_form[state]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_state'))."
				</select>
			</td>
		</tr>
		<tr>
			<td>Postal code/Zip code Field</td>
			<td><select name='chronopay_form[post_code]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_post_code'))."
				</select>
			</td>
		</tr>
		<tr>
			<td>Country Field</td>
			<td><select name='chronopay_form[country]'>
				".nzshpcrt_form_field_list(get_option('chronopay_form_country'))."
				</select>
			</td>
		</tr>
	";
	return $output;
}
  
  
add_action('init', 'nzshpcrt_chronopay_callback');
add_action('init', 'nzshpcrt_chronopay_results');
	
?>