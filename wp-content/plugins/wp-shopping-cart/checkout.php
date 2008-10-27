<?php
global $wpdb,$gateway_checkout_form_fields, $user_ID;
$_SESSION['cart_paid'] = false;


if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."usermeta'")) {
	if(is_numeric($user_ID) && ($user_ID > 0)) {
		$saved_data_sql = "SELECT * FROM `".$wpdb->prefix."usermeta` WHERE `user_id` = '".$user_ID."' AND `meta_key` = 'wpshpcrt_usr_profile';";
		$saved_data = $wpdb->get_row($saved_data_sql,ARRAY_A);
		$meta_data = unserialize($saved_data['meta_value']);
	}
}

if(!isset($_SESSION['collected_data']) || ($_SESSION['collected_data'] == null)) {
  $_SESSION['collected_data'] = $meta_data;
} else {
	foreach($_SESSION['collected_data'] as $form_key => $session_form_data) {
		if($session_form_data == null) {
			$_SESSION['collected_data'][$form_key] = $meta_data[$form_key];
		}
	}
}

$checkout = $_SESSION['checkoutdata'];
if(get_option('permalink_structure') != '') {
	$seperator ="?";
} else {
	$seperator ="&amp;";
}
$currenturl = get_option('checkout_url') . $seperator .'total='.$_GET['total'];
if(get_option('permalink_structure') == '') {
  $currenturl = str_replace(trailingslashit(get_option('siteurl')).'?',trailingslashit(get_option('siteurl')) . 'index.php?', $currenturl);
}
if(!isset($_GET['result'])){
  if(!(get_option('payment_gateway')=='google')) {
?>
<div class="wrap wpsc_container">
<strong><?php echo TXT_WPSC_CONTACTDETAILS;?></strong><br />
<?php
 echo TXT_WPSC_CREDITCARDHANDY;
 if(!is_numeric($user_ID) && ($user_ID < 1) && get_settings('users_can_register')) {
   echo " ".TXT_WPSC_IF_USER_CHECKOUT."<a href='#' onclick='jQuery(\"#checkout_login_box\").slideToggle(\"fast\"); return false;'>".TXT_WPSC_LOG_IN."</a>";
   echo "<div id='checkout_login_box'>";
   ?>
<form name="loginform" id="loginform" action="wp-login.php" method="post">
  <label>Username:<br /><input type="text" name="log" id="log" value="" size="20" tabindex="1" /></label><br />
  <label>Password:<br /> <input type="password" name="pwd" id="pwd" value="" size="20" tabindex="2" /></label>
  <input type="submit" name="submit" id="submit" value="Login &raquo;" tabindex="4" />
  <input type="hidden" name="redirect_to" value="<?php echo get_option('checkout_url'); ?>" />
</form>
   <?php 
   echo "<a class='thickbox' rel='".TXT_WPSC_REGISTER."' href='".$siteurl."?ajax=true&amp;action=register&amp;width=360&amp;height=300' >".TXT_WPSC_REGISTER."</a>";
   echo "</div>";
}
echo "<br /><br />";
echo TXT_WPSC_ASTERISK;
if($_SESSION['nzshpcrt_checkouterr'] != null) {
  echo "<br /><span style='color: red;'>".$_SESSION['nzshpcrt_checkouterr']."</span>";
  $_SESSION['nzshpcrt_checkouterr'] = '';
}
?>

<form action='' method='POST' enctype="multipart/form-data">
<table class='wpsc_checkout_table'>

 
 

<?php
  $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' ORDER BY `order`;";
  $form_data = $wpdb->get_results($form_sql,ARRAY_A);
  //exit("<pre>".print_r($form_data,true)."</pre>");
  foreach($form_data as $form_field) {
    if($form_field['type'] == 'heading') {
      
      echo "<tr>\n\r";
      echo "  <td colspan='2'>\n\r";
      echo "    <strong>".$form_field['name']."</strong>\n\r";
      echo "  </td>\n\r";
      echo "</tr>\n\r";
		} else {
			
			echo "<tr>\n\r";
			echo "	<td>\n\r";
			echo $form_field['name'];
			if($form_field['mandatory'] == 1) {
				if(!(($form_field['type'] == 'country') || ($form_field['type'] == 'delivery_country'))) {
					echo "*";
				}
			}
			
			echo "	</td>\n\r";
			echo "	<td>\n\r";
			switch($form_field['type']) {
				case "city":
				if (function_exists('getdistance')) {
					echo "<input onblur='store_list()' id='user_city' type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
				} else  {
					echo "<input type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
				}
				break;

				case "text":
				case "city":
				case "delivery_city":
				case "coupon":
				echo "<input type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
				break;

				case "address":
				if (function_exists('getdistance')) {
					echo "<input type='text' id='user_address' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]'>";
				} else  {
					echo "<textarea name='collected_data[".$form_field['id']."]'>".$_SESSION['collected_data'][$form_field['id']]."</textarea>";
				}
				break;

				case "address":
				case "delivery_address":
				case "textarea":
				echo "<textarea name='collected_data[".$form_field['id']."]'>".$_SESSION['collected_data'][$form_field['id']]."</textarea>";
				break;
				

				/*
				case "region":
				case "delivery_region":
				echo "<select name='collected_data[".$form_field['id']."]'>".nzshpcrt_region_list($_SESSION['collected_data'][$form_field['id']])."</select>";
				break;
				*/

				case "country":
				echo wpsc_country_region_list($form_field['id'] , false, $_SESSION['selected_country'], $_SESSION['selected_region']);
				break;

				case "delivery_country":
				$country_name = $wpdb->get_var("SELECT `country` FROM `".$wpdb->prefix."currency_list` WHERE `isocode`='".$_SESSION['delivery_country']."' LIMIT 1");
				echo "<input type='hidden' name='collected_data[".$form_field['id']."]' value='".$_SESSION['delivery_country']."'>".$country_name." ";
				break;

				case "email":
				echo "<input type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
				break;

				default:
				echo "<input type='text' value='".$_SESSION['collected_data'][$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
				break;
			}
			echo "	</td>\n\r";
			echo "</tr>\n\r";
		}
	}
    
	$cart = $_SESSION['nzshpcrt_cart'];
  foreach($cart as $key => $product) {
		$product_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id` = '{$product->product_id}' LIMIT 1",ARRAY_A);
		$can_have_uploaded_image = get_product_meta($product->product_id,'can_have_uploaded_image',true);
		if ($can_have_uploaded_image[0]=='on'){
			echo "<tr>\n\r";
			echo "  <td colspan='2'>\n\r";
			echo "<h2 style='margin-bottom: 6px;'>".TXT_WPSC_UPLOAD_IMAGE_FOR." ".$product_data['name']."</h2>\n\r";
			echo "<input type='file' name='uploaded_image[$key]' value=''> \n\r";//
			
			echo "  </td>\n\r";
			echo "</tr>\n\r";
		}
  }    

	if (count(get_option('custom_gateway_options')) > 1) {
		echo "<tr>\n\r";
		echo "  <td colspan='2'>\n\r";
		echo "    <strong>".TXT_WPSC_SELECTGATEWAY."</strong>\n\r";
		echo "  </td>\n\r";
		echo "</tr>\n\r";
		echo "<tr>\n\r";
		echo "  <td colspan='2'>\n\r";
		foreach (get_option('custom_gateway_options') as $option) {
			foreach ($GLOBALS['nzshpcrt_gateways'] as $gateway){
				if ($gateway['internalname'] == $option) {
					echo "<input name='custom_gateway' value='$option' type='radio'>{$gateway['name']}<br>";
				}
			}
		}
		echo "  </td>\n\r";
		echo "</tr>";
	} else {
		foreach ((array)get_option('custom_gateway_options') as $option) {
			foreach ($GLOBALS['nzshpcrt_gateways'] as $gateway){
				if ($gateway['internalname'] == $option) {
					echo "<input name='custom_gateway' value='$option' type='hidden' />";
				}
			}
		}
	}
	if(isset($gateway_checkout_form_fields)) {
		echo $gateway_checkout_form_fields;
	}
	$product=$_SESSION['nzshpcrt_cart'][0];
	$engrave = get_product_meta($product->product_id,'engraved',true);
	if ($engrave[e0] == true) {
		echo "	<tr>\n\r";
		echo "		<td>\n\r";
		echo "			Engrave text:\n\r";
		echo "		</td>\n\r";
		echo "		<td>\n\r";
		echo "			<input type='text' name='engrave1'>\n\r";
		echo "		</td>\n\r";
		echo "	</tr>\n\r";
		echo "	<tr>\n\r";
		echo "		<td>\n\r";
		echo "		</td>\n\r";
		echo "		<td>\n\r";
		echo "			<input type='text' name='engrave2'>\n\r";
		echo "		</td>\n\r";
		echo "	</tr>\n\r";
	}
	
	if (get_option('display_find_us') == '1') {
		echo "<tr><td>&nbsp;</td></tr><tr>
		<td>How did you find us:</td>
		<td><select name='how_find_us'>
			<option value='Word of Mouth'>Word of mouth</option>
			<option value='Advertisement'>Advertising</option>
			<option value='Internet'>Internet</option>
			<option value='Customer'>Existing Customer</option>
		</select></td></tr>";
	}
	
    $termsandconds = get_option('terms_and_conditions');
    if($termsandconds != '') {
      ?>
	<tr>
      <td>
      </td>
      <td>
      <input type='checkbox' value='yes' name='agree' /> <?php echo TXT_WPSC_TERMS1;?><a class='thickbox' target='_blank' href='<?php
      echo get_option('siteurl')."?termsandconds=true&amp;width=360&amp;height=400'"; ?>' class='termsandconds'><?php echo TXT_WPSC_TERMS2;?></a>
      </td>
    </tr>
      <?php
		} else {
			echo "<input type='hidden' value='yes' name='agree' />";
			echo "";
		}
	
    if(get_option('payment_method') == 2)
      {
      $curgateway = get_option('payment_gateway');
      foreach($GLOBALS['nzshpcrt_gateways'] as $gateway)
        {
        if($gateway['internalname'] == $curgateway )
          {
          $gateway_name = $gateway['name'];
          }
        }
      ?>
      <tr>
        <td colspan="2">
        <strong>Payment Method</strong>
        </td>
      </tr>

      <tr>
        <td colspan='2'>
        <input type='radio' name='payment_method' value='1' id='payment_method_1' checked='true'>
        <label for='payment_method_1'><?php echo TXT_WPSC_PAY_USING;?> <?php echo $gateway_name; ?>/<?php echo TXT_WPSC_CREDIT_CARD;?></label>
        </td>
      </tr>
      
      <tr>
        <td colspan='2'>
        <input type='radio' name='payment_method' value='2' id='payment_method_2'>
        <label for='payment_method_2'><?php echo TXT_WPSC_PAY_MANUALLY;?></label>
        </td>
      </tr>
      <?php
      }
// 	if ((function_exists('getdistance')) && (get_option('googleStoreLocator')==1)) {
// 		echo "<tr><td colspan='2'><strong>2. Select Store</strong</td></tr>";
// 		echo "<tr><td>Stores</td><td width='80'><select name='chosen_store' style='float:left;' id='gloc_storelist'></select><div style='float:left;display:none;' id='gloc_loading'>Loading Stores...</div></td></tr>";
// 	}
// 	if (get_option('googleStoreLocator')==1) {
// 		echo "<tr>
// 			<td>
// 				<input type='radio' name='pickupordelivery' id='pickupordelivery1' value='1'><label for='pickupordelivery1'>".TXT_WPSC_PICKUP."</label>
// 			</td>
// 			<td>
// 				<input type='radio' name='pickupordelivery' id='pickupordelivery2' value='2'><label for='pickupordelivery2'>".TXT_WPSC_DELIVERY."</label>
// 			</td>
// 		</tr>";
// 	}
	?>
    <tr>
      <?php if((is_user_logged_in() && (get_option('require_register') == 1)) xor (get_option('require_register') == 0)) { ?>
      <td colspan='2'><br />
      <input type='hidden' value='true' name='submitwpcheckout' />
	<?php 
	if (get_option('payment_gateway') == 'google') { 
		echo "<br>";
		if (get_option('google_button_size') == '0'){
			$google_button_w=180;
			$google_button_h=46;
		} elseif(get_option('google_button_size') == '1') {
			$google_button_w=168;
			$google_button_h=44;
		} elseif(get_option('google_button_size') == '2') {
			$google_button_w=160;
			$google_button_h=43;
		}
		

		if ($_SESSION['google_prohibited']!='1') {
	?>
		<input  type='image' class='googlebutton' src="https://checkout.google.com/buttons/checkout.gif?merchant_id=<?php echo get_option('google_id')?>&w=<?php echo $google_button_w?>&h=<?php echo $google_button_h?>&style=<?php echo get_option('google_button_bg')?>&variant=text&loc=en_US">
		<?php } else { ?>
		<img src="https://checkout.google.com/buttons/checkout.gif?merchant_id=<?php echo get_option('google_id')?>&w=<?php echo $google_button_w?>&h=<?php echo $google_button_h?>&style=<?php echo get_option('google_button_bg')?>&variant=disabled&loc=en_US"/>
		<?php }?>
	<?php } else {
		
		?>
	<input type='submit' value='<?php echo TXT_WPSC_MAKEPURCHASE;?>' name='submit' />
	<?php  } ?>
	<?php } else { ?>
      <td colspan='2'>
      <br /><strong><?php echo TXT_WPSC_PLEASE_LOGIN;?></strong><br />
      <?php echo TXT_WPSC_IF_JUST_REGISTERED;?>
      </td>
      <?php } ?>
    </tr>
</table>
</form>
</div>
<?php
    }
  }
  else
    {
    echo TXT_WPSC_BUYPRODUCTS;
    }
  ?> 