<?php
if(isset($_POST) && is_array($_POST)) {
  if(isset($_POST['add_coupon']) && ($_POST['add_coupon'] == 'true')&& (!($_POST['is_edit_coupon'] == 'true'))) {
    $coupon_code = $_POST['add_coupon_code'];
    $discount = (double)$_POST['add_discount'];
    // cast to boolean, then integer, prevents the value from being anything but 1 or 0
    $discount_type = (int)(bool)$_POST['add_discount_type'];
    $use_once = (int)(bool)$_POST['add_use-once'];
		$every_product = (int)(bool)$_POST['add_every_product'];
    $start_date = date("Y-m-d H:i:s", mktime(0, 0, 0, (int)$_POST['add_start']['month'], (int)$_POST['add_start']['day'], (int)$_POST['add_start']['year']));
    $end_date = date("Y-m-d H:i:s", mktime(0, 0, 0, (int)$_POST['add_end']['month'], (int)$_POST['add_end']['day'], (int)$_POST['add_end']['year']));
    
    if($wpdb->query("INSERT INTO `".$wpdb->prefix."wpsc_coupon_codes` ( `coupon_code` , `value` , `is-percentage` , `use-once` , `is-used` , `active` , `every_product` , `start` , `expiry` ) VALUES ( '$coupon_code', '$discount', '$discount_type', '$use_once', '0', '1', '$every_product', '$start_date' , '$end_date' );")) {  
      echo "<div class='updated'><p align='center'>".TXT_WPSC_COUPONHASBEENADDED."</p></div>";
      }
    }
  if(isset($_POST['is_edit_coupon']) && ($_POST['is_edit_coupon'] == 'true')) {
    foreach((array)$_POST['edit_coupon'] as $coupon_id => $coupon_data) {
			//echo('<pre>'.print_r($coupon_data,true)."</pre>");
      $coupon_id = (int)$coupon_id;
			// convert dates to a form that compares well and can be inserted into the database
			$coupon_data['start'] = date("Y-m-d H:i:s", mktime(0, 0, 0, (int)$coupon_data['start']['month'], (int)$coupon_data['start']['day'], (int)$coupon_data['start']['year']));
			$coupon_data['expiry'] = date("Y-m-d H:i:s", mktime(0, 0, 0, (int)$coupon_data['expiry']['month'], (int)$coupon_data['expiry']['day'], (int)$coupon_data['expiry']['year']));
			$check_values = $wpdb->get_row("SELECT `id`, `coupon_code`, `value`, `is-percentage`, `use-once`, `active`, `start`, `expiry` FROM `".$wpdb->prefix."wpsc_coupon_codes` WHERE `id` = '$coupon_id'", ARRAY_A);
			//sort both arrays to make sure that if they contain the same stuff, that they will compare to be the same, may not need to do this, but what the heck
			ksort($check_values); ksort($coupon_data);
						
			if($check_values != $coupon_data) {
				$insert_array = array();
				foreach($coupon_data as $coupon_key => $coupon_value) {
				  if(($coupon_key == "submit_coupon") || ($coupon_key == "delete_coupon")) {
				    continue;
				  }
					if($coupon_value != $check_values[$coupon_key]) {
						$insert_array[] = "`$coupon_key` = '$coupon_value'";
					}
				}
					
				//echo("<pre>".print_r($insert_array,true)."</pre>");
				if(count($insert_array) > 0) {
					$wpdb->query("UPDATE `".$wpdb->prefix."wpsc_coupon_codes` SET ".implode(", ", $insert_array)." WHERE `id` = '$coupon_id' LIMIT 1;");
				}
				unset($insert_array);
				//echo("<pre>".print_r($check_values,true)."</pre>");
			}
				//echo("<pre>".print_r($coupon_data,true)."</pre>");
				
			if($coupon_data['delete_coupon'] != '') {
				$wpdb->query("DELETE FROM `".$wpdb->prefix."wpsc_coupon_codes` WHERE `id` = '$coupon_id' LIMIT 1;");
			}
		}
	}
  
  if($_POST['change-settings'] == 'true') {
    if($_POST['wpsc_also_bought'] == 1) {
      update_option('wpsc_also_bought', 1);
      } else {
      update_option('wpsc_also_bought', 0);
      }
	
    if($_POST['display_find_us'] == 'on') {
      update_option('display_find_us', 1);
      } else {
      update_option('display_find_us', 0);
      }
      
    if($_POST['wpsc_share_this'] == 1) {
      update_option('wpsc_share_this', 1);
      } else {
      update_option('wpsc_share_this', 0);
      }
    }
  }

/*<strong><?php echo TXT_WPSC_ADD_COUPON; ?></strong>*/
?>
<div class="wrap">
  <h2><?php echo TXT_WPSC_DISPLAYCOUPONS;?></h2>
  <a target="_blank" href="http://www.instinct.co.nz/e-commerce/marketing/" class="about_this_page"><span>About This Page</span> </a>

  <a href='' onclick='return show_status_box("add_coupon_box","add_coupon_box_link");' class='add_item_link' id='add_coupon_box_link'><img src='<?php echo WPSC_URL; ?>/images/package_add.png' alt='<?php echo TXT_WPSC_ADD; ?>' title='<?php echo TXT_WPSC_ADD; ?>' />&nbsp;<span><?php echo TXT_WPSC_ADD_COUPON;?></span></a>
  <span id='loadingindicator_span'><img id='loadingimage' src='<?php echo WPSC_URL; ?>/images/indicator.gif' alt='Loading' title='Loading' /></span><br />  
  <p style='margin: 0px 0px 5px 0px;'>		  <?php echo TXT_WPSC_PAYPALNOTE;?>
  </p>
<!-- <form name='edit_coupon' method='post' action=''>   -->
<table style="width: 100%;">
  <tr>
    <td id="coupon_data">
    

<div id='add_coupon_box' class='modify_coupon' >
<form name='add_coupon' method='post' action=''>
<table class='add-coupon'>
 <tr>
   <th>
   <?php echo TXT_WPSC_COUPON_CODE; ?>
   </th>
   <th>
   <?php echo TXT_WPSC_DISCOUNT; ?>
   </th>
   <th>
   <?php echo TXT_WPSC_START; ?>
   </th>
   <th>
   <?php echo TXT_WPSC_EXPIRY; ?>
   </th>
   <th>
   <?php echo TXT_WPSC_USE_ONCE; ?>
   </th>
   <th>
   <?php echo TXT_WPSC_ACTIVE; ?>
   </th>
   <th>
   <?php echo TXT_WPSC_PERTICKED; ?>
   </th>
 </tr>
 <tr>
   <td>
   <input type='text' value='' name='add_coupon_code' />
   </td>
   <td>
   <input type='text' value='' size='3' name='add_discount' />
   <select name='add_discount_type'>
     <option value='0' >$</option>
     <option value='1' >%</option>
   </select>
   </td>
   <td>
   <select name='add_start[day]'>
   <?php
   for($i = 1; $i <=31; ++$i) {
     $selected = '';
     if($i == date("d")) { $selected = "selected='true'"; }
     echo "<option $selected value='$i'>$i</option>";
     }
   ?>
   </select>
   <select name='add_start[month]'>
   <?php
   for($i = 1; $i <=12; ++$i) {
     $selected = '';
     if($i == (int)date("m")) { $selected = "selected='true'"; }
     echo "<option $selected value='$i'>".date("M",mktime(0, 0, 0, $i, 1, date("Y")))."</option>";
     }
   ?>
   </select>
   <select name='add_start[year]'>
   <?php
   for($i = date("Y"); $i <= (date("Y") +12); ++$i) {
     $selected = '';
     if($i == date("Y")) { $selected = "selected='true'"; }
     echo "<option $selected value='$i'>".$i."</option>";
     }
   ?>
   </select>
   </td>
   <td>
   <select name='add_end[day]'>
   <?php
   for($i = 1; $i <=31; ++$i) {
     $selected = '';
     if($i == date("d")) { $selected = "selected='true'"; }
     echo "<option $selected value='$i'>$i</option>";
     }
   ?>
   </select>
   <select name='add_end[month]'>
   <?php
   for($i = 1; $i <=12; ++$i) {
     $selected = '';
     if($i == (int)date("m")) { $selected = "selected='true'"; }
     echo "<option $selected value='$i'>".date("M",mktime(0, 0, 0, $i, 1, date("Y")))."</option>";
     }
   ?>
   </select>
   <select name='add_end[year]'>
   <?php
   for($i = date("Y"); $i <= (date("Y") +12); ++$i) {
     $selected = '';
     if($i == (date("Y")+1)) { $selected = "selected='true'"; }
     echo "<option $selected value='$i'>".$i."</option>";
     }
   ?>
   </select>
   </td>
   <td>
   <input type='hidden' value='0' name='add_use-once' />
   <input type='checkbox' value='1' name='add_use-once' />
   </td>
   <td>
   <input type='hidden' value='0' name='add_active' />
   <input type='checkbox' value='1' checked='true' name='add_active' />
   </td>
   	<td>
		   <input type='hidden' value='0' name='add_every_product' />
			<input type="checkbox" value="1" name='add_every_product'/>
		</td>

   <td>
   
   <input type='hidden' value='true' name='add_coupon' />
   <input type='submit' value='Submit' name='submit_coupon' />
   </td>
 </tr>
</table>
</form>  
</div>    
    
  <?php
  $num = 0;
  
echo "<table class='coupon-list'>\n\r";
echo "  <tr class='toprow'>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_COUPON_CODE;
echo "    </td>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_DISCOUNT;
echo "    </td>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_START;
echo "    </td>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_EXPIRY;
echo "    </td>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_ACTIVE;
echo "    </td>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_PERTICKED;
echo "    </td>\n\r";

echo "    <td>\n\r";
echo TXT_WPSC_EDIT;
echo "    </td>\n\r";

$i=0;
$coupon_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."wpsc_coupon_codes` ",ARRAY_A);
foreach((array)$coupon_data as $coupon) {
  $alternate = "";
  $i++;
  if(($i % 2) != 0) {
    $alternate = "class='alt'";
    }
  echo "<tr $alternate>\n\r";
  
  echo "    <td>\n\r";
  echo $coupon['coupon_code'];
  echo "    </td>\n\r";
  
  echo "    <td>\n\r";
  if($coupon['is-percentage'] == 1){
    echo $coupon['value']."%";
    } else {    
    echo nzshpcrt_currency_display($coupon['value'], 1);
    }
  echo "    </td>\n\r";
  
  echo "    <td>\n\r";
  echo date("d/m/Y",strtotime($coupon['start']));
  echo "    </td>\n\r";
  
  echo "    <td>\n\r";
  echo date("d/m/Y",strtotime($coupon['expiry']));
  echo "    </td>\n\r";
  
  echo "    <td>\n\r";  
  switch($coupon['active']) {
    case 1:
    echo "<img src='".WPSC_URL."/images/yes_stock.gif' alt='' title='' />";
    break;
    
    case 0: default:
    echo "<img src='".WPSC_URL."/images/no_stock.gif' alt='' title='' />";
    break;
    }
  echo "    </td>\n\r";
  
   echo "    <td>\n\r";
  switch($coupon['every_product']) {
	  case 1:
		  echo "<img src='".WPSC_URL."/images/yes_stock.gif' alt='' title='' />";
		  break;
    
	  case 0: default:
		  echo "<img src='".WPSC_URL."/images/no_stock.gif' alt='' title='' />";
		  break;
  }
  echo "    </td>\n\r";

  
  
  echo "    <td>\n\r";
  echo "<a href='#' onclick='return show_status_box(\"coupon_box_".$coupon['id']."\",\"coupon_box_".$coupon['id']."\");' >".TXT_WPSC_EDIT."</a>";
  echo "    </td>\n\r";
  
  echo "  </tr>\n\r";
  echo "  <tr>\n\r";
  echo "    <td colspan='7'>\n\r";
  //$status_style = "style='display: block;'";
  echo "      <div id='coupon_box_".$coupon['id']."' class='modify_coupon' $status_style>\n\r";  
  echo coupon_edit_form($coupon);
  echo "      </div>\n\r";
  echo "    </td>\n\r";
  echo "  </tr>\n\r";
  }
echo "</table>\n\r";
  ?>
    </td>
  </tr>
</table>
<input type='hidden' value='true' name='is_edit_coupon' />
</form>

<br />


      
<h2><?php echo TXT_WPSC_MARKETING_SETTINGS;?></h2>

<form name='cart_options' method='POST' action=''>
<input type='hidden' value='true' name='change-settings' />
  <table>
    <tr>
      <td>
        <?php echo TXT_WPSC_OPTION_ALSO_BOUGHT;?>:
      </td>
      <td>
        <?php
        $wpsc_also_bought = get_option('wpsc_also_bought');
        $wpsc_also_bought1 = "";
        $wpsc_also_bought2 = "";
        switch($wpsc_also_bought) {
        case 0:
        $wpsc_also_bought2 = "checked ='true'";
        break;
        
        case 1:
        $wpsc_also_bought1 = "checked ='true'";
        break;
        }
        ?>
        <input type='radio' value='1' name='wpsc_also_bought' id='wpsc_also_bought1' <?php echo $wpsc_also_bought1; ?> /> <label for='wpsc_also_bought1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
        <input type='radio' value='0' name='wpsc_also_bought' id='wpsc_also_bought2' <?php echo $wpsc_also_bought2; ?> /> <label for='wpsc_also_bought2'><?php echo TXT_WPSC_NO;?></label>
      </td>
    </tr>
    
    <tr>
      <td>
      <?php echo TXT_WPSC_SHOW_SHARE_THIS;?>:
      </td>
      <td>
        <?php
        $wpsc_share_this = get_option('wpsc_share_this');
        $wpsc_share_this1 = "";
        $wpsc_share_this2 = "";
        switch($wpsc_share_this) {
          case 0:
          $wpsc_share_this2 = "checked ='true'";
          break;
          
          case 1:
          $wpsc_share_this1 = "checked ='true'";
          break;
          }
        ?>
        <input type='radio' value='1' name='wpsc_share_this' id='wpsc_share_this1' <?php echo $wpsc_share_this1; ?> /> <label for='wpsc_share_this1'><?php echo TXT_WPSC_YES;?></label> &nbsp;
        <input type='radio' value='0' name='wpsc_share_this' id='wpsc_share_this2' <?php echo $wpsc_share_this2; ?> /> <label for='wpsc_share_this2'><?php echo TXT_WPSC_NO;?></label>
      </td>
    </tr>
	<tr>
        <td>
		<?php echo TXT_WPSC_DISPLAYHOWCUSTOMERFINDUS?>
        </td>
	<?php
		$display_find_us = get_option('display_find_us');
		if ($display_find_us=='1') {
			$display_find_us1 = "checked ='checked'";
		}
	?>
        <td>
		<input <?php echo $display_find_us1; ?> type='checkbox' name='display_find_us'>
        </td>
      </tr>
      <tr>
        <td>
	
        </td>
        <td>
        <input  type='submit' value='<?php echo TXT_WPSC_SUBMIT;?>' name='form_submit' />
        </td>
      </tr>
  </table>
</form>

<h2><?php echo TXT_WPSC_RSS_ADDRESS;?></h2>
<table>
	<tr>
		<td colspan='2'>
			<?=TXT_WPSC_RSSNOTE;?>
		</td>
	</tr>
	<tr><TD>&nbsp;</TD></tr>
	<tr>
		<td>
			RSS Feed Address:
		</td>
		<td>
			<?php echo get_option('siteurl')."/index.php?rss=true&amp;action=product_list" ?>
		</td>
	</tr>
</table>
</div>