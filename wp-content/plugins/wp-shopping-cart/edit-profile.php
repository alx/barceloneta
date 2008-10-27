<?php
$any_bad_inputs = false;
$changes_saved = false;
if($_POST['collected_data'] != null)
  {
  foreach((array)$_POST['collected_data'] as $value_id => $value)
    {
    $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `id` = '$value_id' LIMIT 1";
    $form_data = $wpdb->get_results($form_sql,ARRAY_A);
    $form_data = $form_data[0];
    $bad_input = false;
    if($form_data['mandatory'] == 1)
      {
      switch($form_data['type'])
        {
        case "email":
        if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+\.[a-zA-Z]{2,5}$/",$value))
          {
          $any_bad_inputs = true;
          $bad_input = true;
          }
        break;
        
        case "delivery_country":
        if(($value != null))
          {
          $_SESSION['delivery_country'] == $value;
          }
        break;
        
        default:/*
        if($value == null)
          {
          $any_bad_inputs = true;
          $bad_input = true;
          }*/
        break;
        }
      if($bad_input === true)
        {
        switch($form_data['name'])
          {
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
  
          default:
          $bad_input_message .= TXT_WPSC_PLEASEENTERAVALID . " " . strtolower($form_data['name']) . ".";
          break;
          }
        $bad_input_message .= "<br />";
        }
        else
          {
          $meta_data[$value_id] = $value;
          }
      }
    }
 
  $saved_data_sql = "SELECT * FROM `".$wpdb->prefix."usermeta` WHERE `user_id` = '".$user_ID."' AND `meta_key` = 'wpshpcrt_usr_profile';";
  $saved_data = $wpdb->get_row($saved_data_sql,ARRAY_A);
  
  $new_meta_data = serialize($meta_data);
  if($saved_data != null)
    {
    $wpdb->query("UPDATE `".$wpdb->prefix."usermeta` SET `meta_value` =  '$new_meta_data' WHERE `user_id` IN ('$user_ID') AND `meta_key` IN ('wpshpcrt_usr_profile');");
    $changes_saved = true;
    }
    else
      {
      $wpdb->query("INSERT INTO `".$wpdb->prefix."usermeta` ( `user_id` , `meta_key` , `meta_value` ) VALUES ( ".$user_ID.", 'wpshpcrt_usr_profile', '$new_meta_data');");
      $changes_saved = true;
      }  
  } 
?>
<div class="wrap" style=''>
<?php
echo " <div class='user-profile-links'><a href='".get_option('user_account_url')."'>Purchase History</a> | <a href='".get_option('user_account_url').$seperator."edit_profile=true'>Your Details</a> | <a href='".get_option('user_account_url').$seperator."downloads=true'>Your Downloads</a></div><br />";
?>
<form method='POST'>
<?php
if($changes_saved == true)
  {
  echo TXT_WPSC_THANKS_SAVED;
  }
  else
  {
  echo $bad_input_message;
  }
?>
<table>
<?php
// arr, this here be where the data will be saved
$meta_data = null;
$saved_data_sql = "SELECT * FROM `".$wpdb->prefix."usermeta` WHERE `user_id` = '".$user_ID."' AND `meta_key` = 'wpshpcrt_usr_profile';";
$saved_data = $wpdb->get_row($saved_data_sql,ARRAY_A);

$meta_data = unserialize($saved_data['meta_value']);

$form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' ORDER BY `order`;";
$form_data = $wpdb->get_results($form_sql,ARRAY_A);

foreach($form_data as $form_field)
  {
  if($form_field['type'] == 'heading')
    {
    echo "
    <tr>
      <td colspan='2'>\n\r";
    echo "<strong>".$form_field['name']."</strong>";        
    echo "
      </td>
    </tr>\n\r";
    }
    else
      {
      if($form_field['type'] == "country")
        {
        continue;
        }
      
      echo "
      <tr>
        <td align='left'>\n\r";
      echo $form_field['name'];
      if($form_field['mandatory'] == 1)
        {
        if(!(($form_field['type'] == 'country') || ($form_field['type'] == 'delivery_country')))
          {
          echo "*";
          }
        }
      echo "
        </td>\n\r
        <td  align='left'>\n\r";
      switch($form_field['type'])
        {
        case "text":
        case "city":
        case "delivery_city":
        echo "<input type='text' value='".$meta_data[$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
        break;
        
        case "address":
        case "delivery_address":
        case "textarea":
        echo "<textarea name='collected_data[".$form_field['id']."]'>".$meta_data[$form_field['id']]."</textarea>";
        break;
        
        
        case "region":
        case "delivery_region":
        echo "<select name='collected_data[".$form_field['id']."]'>".nzshpcrt_region_list($_SESSION['collected_data'][$form_field['id']])."</select>";
        break;
        
        
        case "country":   
        break;
        
        case "delivery_country":
        echo "<select name='collected_data[".$form_field['id']."]' >".nzshpcrt_country_list($meta_data[$form_field['id']])."</select>";
        break;
        
        case "email":
        echo "<input type='text' value='".$meta_data[$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
        break;
        
        default:
        echo "<input type='text' value='".$meta_data[$form_field['id']]."' name='collected_data[".$form_field['id']."]' />";
        break;
        }
      echo "
        </td>
      </tr>\n\r";
      }
  }
  ?>
    <?php
    if(isset($gateway_checkout_form_fields))
      {
      echo $gateway_checkout_form_fields;
      }
    ?>
    <tr>
      <td>
      </td>
      <td>
      <input type='hidden' value='true' name='submitwpcheckout_profile' />
      <input type='submit' value='<?php echo TXT_WPSC_SAVE_PROFILE;?>' name='submit' />
      </td>
    </tr>
</table>
</form>