<?php
$changes_made = false;
if(is_array($_POST['region_tax']))
  {
  foreach($_POST['region_tax'] as $region_id => $tax)
    {
    if(is_numeric($region_id) && is_numeric($tax))
      {
      $previous_tax = $wpdb->get_var("SELECT `tax` FROM `".$wpdb->prefix."region_tax` WHERE `id` = '$region_id' LIMIT 1");
      if($tax != $previous_tax)
        {
        $wpdb->query("UPDATE `".$wpdb->prefix."region_tax` SET `tax` = '$tax' WHERE `id` = '$region_id' LIMIT 1");
        $changes_made = true;
        }
      }
    }
  }
  
function country_list($selected_country = null)
  {
  global $wpdb;
  $output = "";
  $country_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."currency_list` ORDER BY `country` ASC",ARRAY_A);
  foreach ($country_data as $country)
    {
    $selected ='';
    if($selected_country == $country['isocode'])
      {
      $selected = "selected='true'";
      }
    $output .= "<option value='".$country['isocode']."' $selected>".$country['country']."</option>";
    }
  return $output;
  }
  
if(preg_match("/[a-zA-Z]{2,4}/",$_GET['isocode']))
  {
  $country_isocode = $_GET['isocode'];
  }
  else
    {
    $country_isocode = get_option('base_country');
    }
$base_region = get_option('base_region');
?>
<div class="wrap">
  <h2><?php echo TXT_WPSC_GSTTAXRATE;?></h2>
  <?php
  if($changes_made === true)
    {
    echo "Thanks, your changes have been made<br />";
    }
  ?>
  <form action='?page=<?php echo $_GET['page']; ?>&amp;isocode=<?php echo $_GET['isocode']; ?>' method='POST' name='regional_tax'>
  <?php
  $country_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."currency_list` WHERE `isocode` IN('".$country_isocode."') LIMIT 1",ARRAY_A);
  if(($country_data['has_regions'] == 1))
    {
    $region_data = $wpdb->get_results("SELECT `".$wpdb->prefix."region_tax`.* FROM `".$wpdb->prefix."region_tax` WHERE `".$wpdb->prefix."region_tax`.`country_id` IN('".$country_data['id']."') ",ARRAY_A) ;
    $region_data = array_chunk($region_data, 14);
    
    echo "<table>\n\r";
    echo "  <tr>\n\r";
    foreach($region_data as $region_col)
      {
      echo "    <td style='vertical-align: top; padding-right: 3em;'>\n\r";
      echo "<table>\n\r";
      foreach($region_col as $region)
        {
        $tax_percentage =  $region['tax'];
        echo "  <tr>\n\r";
        if($region['id'] == $base_region)
          {
          echo "    <td><label for='region_tax_".$region['id']."' style='text-decoration: underline;'>".$region['name'].":</label></td>\n\r";
          }
          else
            {
            echo "    <td><label for='region_tax_".$region['id']."'>".$region['name'].":</label></td>\n\r";
            }
        echo "    <td><input type='text' id='region_tax_".$region['id']."' name='region_tax[".$region['id']."]' value='".$tax_percentage."' class='tax_forms'  maxlength='5' size='5'/>%</td>\n\r";
        echo "  </tr>\n\r";
        }      
      echo "</table>\n\r";
      echo "    </td>\n\r";
      }
    echo "  </tr>\n\r";
    echo "</table>\n\r";
    }
    else
      {
      $tax_percentage =  $country_data['tax'];
      echo "<label for='country_tax'>Tax Rate:</label> ";
      echo "<input type='text' id='country_tax' name='country_tax' value='".$tax_percentage."' class='tax_forms' maxlength='5' size='5'/>%";
      }
  ?>
  <input type='submit' name='submit' value='<?php echo TXT_WPSC_SAVE_CHANGES;?>' />
  </form>
</div>