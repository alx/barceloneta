<?php
function variationlist($curent_variation) {
  global $wpdb;
  $options = "";
  //$options .= "<option value=''>".TXT_WPSC_SELECTAVARIATION."</option>\r\n";
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_categories` ORDER BY `id` ASC",ARRAY_A);
  foreach($values as $option) {
    if($curent_variation == $option['id']) {
      $selected = "selected='selected'";
		}
    $options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
    $selected = "";
	}
  $concat .= "<select name='variation'>".$options."</select>\r\n";
  return $concat;
}

function display_variation_row($variation) {
  // displays the row the variation is on
  echo "          <tr>\n\r";
  
  echo "            <td>\n\r";
  echo "".htmlentities(stripslashes($variation['name']), ENT_QUOTES, 'UTF-8')."";
  echo "            </td>\n\r";
    
  echo "            <td>\n\r";
  echo "<a href='#' onclick='fillvariationform(".$variation['id'].");return false;'>".TXT_WPSC_EDIT."</a>";
  echo "            </td>\n\r";
  echo "          </tr>\n\r";
}


  $imagedir = WPSC_FILE_PATH."/variation_images/";
  
 /*  delete variation_value */  
  if($_GET['delete_value'] == 'true') {
   if(is_numeric($_GET['variation_id']) && is_numeric($_GET['value_id'])) {
			//exit("DELETE FROM `".$wpdb->prefix."variation_values_associations` WHERE `value_id` = '".$_GET['value_id']."'");
			$wpdb->query("DELETE FROM `".$wpdb->prefix."variation_values_associations` WHERE `value_id` = '".$_GET['value_id']."'");
			$wpdb->query("DELETE FROM `".$wpdb->prefix."variation_values` WHERE `id` = '".$_GET['value_id']."' AND `variation_id` = '".$_GET['variation_id']."' LIMIT 1");
		}
	}  
  
 /* add variation */
  if($_POST['submit_action'] == "add") {
    //exit("<pre>".print_r($_POST,true)."</pre>");
    $variation_sql = "INSERT INTO `".$wpdb->prefix."product_variations` (`name`, `variation_association`) VALUES ( '".$_POST['name']."', 0);";
    if($wpdb->query($variation_sql)) {
      $variation_id = $wpdb->get_results("SELECT LAST_INSERT_ID() AS `id` FROM `".$wpdb->prefix."product_variations` LIMIT 1",ARRAY_A);
      $variation_id = $variation_id[0]['id'];
      $variation_values = $_POST['variation_values'];
      $variation_value_sql ="INSERT INTO `".$wpdb->prefix."variation_values` ( `name` , `variation_id` )
  VALUES ";
      $num = 0;
      foreach($variation_values as $variation_value) {
        switch($num) {
          case 0:
          $comma = '';
          break;
          
          default:
          $comma = ', ';
          break;
				}
        $variation_value_sql .= "$comma( '".$wpdb->escape(trim($variation_value))."', '".$variation_id."')";
        $num++;
			}
      $variation_value_sql .= ";";
      $wpdb->query($variation_value_sql);
      echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASBEENADDED."</p></div>";
		} else {
			echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASNOTBEENADDED."</p></div>";
		}
	}
    
    
    
  /* edit variation */
  if(($_POST['submit_action'] == "edit") && is_numeric($_POST['prodid'])) {
    //exit("<pre>".print_r($_POST,true)."</pre>");
    $variation_id = $_POST['prodid'];
    foreach($_POST['variation_values'] as $variation_value_id => $variation_value) {
      if(is_numeric($variation_value_id)) {
        $variation_value_state = $wpdb->get_results("SELECT `name` FROM `".$wpdb->prefix."variation_values` WHERE `id` = '$variation_value_id' AND `variation_id` = '$variation_id' LIMIT 1",ARRAY_A);
        $variation_value_state = $variation_value_state[0]['name'];
			}
        
      if($variation_value_state != $variation_value) {
        $wpdb->query("UPDATE `".$wpdb->prefix."variation_values` SET `name` = '".$wpdb->escape($variation_value)."' WHERE `id` = '$variation_value_id' AND `variation_id` = '".$variation_id."' LIMIT 1;");
			}
		}
    
    $variation_value_sql ="INSERT INTO `".$wpdb->prefix."variation_values` ( `name` , `variation_id` )
VALUES ";
    $num = 0; 
    if($_POST['new_variation_values'] != null) {
      $num = 0;
      foreach($_POST['new_variation_values'] as $variation_value) {
        switch($num) {
          case 0:
          $comma = '';
          break;
          
          default:
          $comma = ', ';
          break;
				}
        $variation_value_sql .= "$comma('".$wpdb->escape(trim($variation_value))."', '".$variation_id."')";
        $num++;
			}
      $variation_value_sql .= ";";
      $wpdb->query($variation_value_sql);  
		}
    
    $updatesql = "UPDATE `".$wpdb->prefix."product_variations` SET `name` = '".$wpdb->escape($_POST['title'])."' WHERE `id`='".$variation_id."' LIMIT 1";
    $wpdb->query($updatesql);
  
    echo "<div class='updated'><p align='center'>".TXT_WPSC_VARIATIONHASBEENEDITED."</p></div>";
	}
  

if(is_numeric($_GET['deleteid']))
  {
  $delete_value_assoc_sql = "DELETE FROM `".$wpdb->prefix."variation_values_associations` WHERE `variation_id` = '".$_GET['deleteid']."'";
  $delete_variation_assoc_sql = "DELETE FROM `".$wpdb->prefix."variation_associations` WHERE `variation_id` = '".$_GET['deleteid']."'";
  $delete_values_sql = "DELETE FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` = '".$_GET['deleteid']."';";
  $delete_variation_sql = "DELETE FROM `".$wpdb->prefix."product_variations` WHERE `id`='".$_GET['deleteid']."' LIMIT 1";
  $wpdb->query($delete_value_assoc_sql);
  $wpdb->query($delete_variation_assoc_sql);
  $wpdb->query($delete_values_sql);
  $wpdb->query($delete_variation_sql);
  }

?>

<script language='javascript' type='text/javascript'>
function conf()
  {
  var check = confirm("<?php echo TXT_WPSC_SURETODELETEPRODUCT;?>");
  if(check)
    {
    return true;
	}
	else
	  {
	  return false;
	  }
  }

<?php
  if(is_numeric($_POST['prodid']))
    {
    echo "fillvariationform(".$_POST['prodid'].");";
    }
    
  if(is_numeric($_GET['variation_id']))
    {
    echo "fillvariationform(".$_GET['variation_id'].");";
    }
?>
</script>
<noscript>
</noscript>
<div class="wrap">
  <h2><?php echo TXT_WPSC_DISPLAYVARIATIONS;?></h2>
  <p>	
  	  <?php echo TXT_WPSC_DISPLAYVARIATIONSDESCRIPTION;?>

</p>

  
  
  
  
  <div class="tablenav wpsc_admin_nav" >
	<div class="alignleft" style='width: 500px;'>
		<a href='' onclick='return showaddform()' class='add_item_link'><img src='<?php echo WPSC_URL; ?>/images/package_add.png' alt='<?php echo TXT_WPSC_ADD; ?>' title='<?php echo TXT_WPSC_ADD; ?>' />&nbsp;<span><?php echo TXT_WPSC_ADDVARIATION;?></span></a>
		<span id='loadingindicator_span'><img id='loadingimage' src='<?php echo WPSC_URL; ?>/images/indicator.gif' alt='Loading' title='Loading' /></span><br />
	</div>
	
	  
	<div class="alignright">
		<a target="_blank" href='http://www.instinct.co.nz/e-commerce/variations/' class='about_this_page'><span><?php echo TXT_WPSC_ABOUT_THIS_PAGE;?></span>&nbsp;</a>
	</div>
	<br class="clear"/>
</div>
  <?php
  $num = 0;
echo "  <table id='productpage'>\n\r";
echo "    <tr><td class='firstcol'>\n\r";
echo "  <div class='categorisation_title'>\n\r";
echo "		<strong class='form_group'>".TXT_WPSC_VARIATION_LIST."</strong>\n\r";
echo "	</div>\n\r";
echo "      <table id='itemlist'>\n\r";
echo "        <tr class='firstrow'>\n\r";

echo "          <td>\n\r";
echo TXT_WPSC_NAME;
echo "          </td>\n\r";

echo "          <td>\n\r";
echo TXT_WPSC_EDIT;
echo "          </td>\n\r";

echo "        </tr>\n\r";
$variation_sql = "SELECT * FROM `".$wpdb->prefix."product_variations` ORDER BY `id`";
$variation_list = $wpdb->get_results($variation_sql,ARRAY_A);
if($variation_list != null) {
  foreach($variation_list as $variation) {
    display_variation_row($variation);
	}
}
  
echo "      </table>\n\r";
echo "      </td><td class='secondcol'>\n\r";
echo "        <div id='productform'>";
echo "  <div class='categorisation_title'>\n\r";
echo "		<strong class='form_group'>".TXT_WPSC_EDITVARIATION."</strong>\n\r";
echo "	</div>\n\r";


echo "<form method='POST'  enctype='multipart/form-data' name='editproduct$num'>";
echo "        <div id='formcontent'>\n\r";
echo "        </div>\n\r";
echo "</form>";
echo "        </div>";
?>
<div id='additem'>
  <div class="categorisation_title">
		<strong class="form_group"><?php echo TXT_WPSC_ADDVARIATION;?></strong>
	</div>
  <form method='POST' enctype='multipart/form-data'>
  <table class='category_forms'>
    <tr>
      <td>
        <?php echo TXT_WPSC_NAME;?>:
      </td>
      <td>
        <input type='text'  class="text" name='name' value=''  />
      </td>
    </tr>
    <tr>
      <td>
        <?php echo TXT_WPSC_VARIATION_VALUES;?>:
      </td>
      <td>
        <div id='add_variation_values'><span id='variation_value_1'>
        <input type='text' class="text" name='variation_values[]' value='' />
        <a class='image_link' href='#' onclick='remove_variation_value_field("variation_value_1")'><img src='<?php echo WPSC_URL; ?>/images/trash.gif' alt='<?php echo TXT_WPSC_DELETE; ?>' title='<?php echo TXT_WPSC_DELETE; ?>' /></a><br />
        </span><span id='variation_value_2'>
        <input type='text' class="text" name='variation_values[]' value='' />
        <a class='image_link' href='#' onclick='remove_variation_value_field("variation_value_2")'><img src='<?php echo WPSC_URL; ?>/images/trash.gif' alt='<?php echo TXT_WPSC_DELETE; ?>' title='<?php echo TXT_WPSC_DELETE; ?>' /></a><br />
        </span></div>
       <a href='#' onclick='return add_variation_value("add")'><?php echo TXT_WPSC_ADD;?></a>
      </td>
    </tr>
    <tr>
      <td>
      </td>
      <td>
        <input type='hidden' name='submit_action' value='add' />
        <input class='button'  type='submit' name='submit' value='<?php echo TXT_WPSC_ADD;?>' />
      </td>
    </tr>
  </table>
  </form>
</div>
<?php
echo "      </td></tr>\n\r";
echo "     </table>\n\r";
  ?>
</div>