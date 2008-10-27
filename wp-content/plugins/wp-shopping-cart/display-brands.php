<?php
function brandlist($curent_brand)
  {
  global $wpdb;
  $options = "";
  //$options .= "<option value=''>".TXT_WPSC_SELECTABRAND."</option>\r\n";
  $values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_brands` ORDER BY `id` ASC",ARRAY_A);
  foreach($values as $option)
    {
    if($curent_brand == $option['id'])
      {
      $selected = "selected='selected'";
       }
    $options .= "<option  $selected value='".$option['id']."'>".$option['name']."</option>\r\n";
    $selected = "";
    }
  $concat .= "<select name='brand'>".$options."</select>\r\n";
  return $concat;
  }

  $imagedir = ABSPATH."/wp-content/plugins/wp-shopping-cart/brand_images/";

  if($_POST['submit_action'] == "add")
    { 
    if($_FILES['image'] != null)
      {
      if(function_exists("getimagesize"))
        {
        // pe.{
        $imagefield = 'image';
        // }.pe
        include("image_processing.php");
        }
        else
          {
          move_uploaded_file($_FILES['image']['tmp_name'], ($imagedir.$_FILES['image']['name']));
          $image = $wpdb->escape($_FILES['image']['name']);
          }
      }
      else
        {
        $image = '';
        }
    $insertsql = "INSERT INTO `".$wpdb->prefix."product_brands` ( `name` , `description`, `active`, `order` )VALUES ('".$_POST['name']."', '".$_POST['description']."', '1', '0')";
  
    if($wpdb->query($insertsql))
      {
      echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASBEENADDED."</p></div>";
      }
      else
        {
        echo "<div class='updated'><p align='center'>".TXT_WPSC_ITEMHASNOTBEENADDED."</p></div>";
        }
    }

  if(($_POST['submit_action'] == "edit") && is_numeric($_POST['prodid']))
    {
   if($_POST['special'] == 'yes')
     {
     $special = 1;
     }
     else
       {
       $special = 0;
       }

   if($_POST['notax'] == 'yes')
     {
     $notax = 1;
     }
     else
       {
       $notax = 0;
       }
    $updatesql = "UPDATE `".$wpdb->prefix."product_brands` SET `name` = '".$wpdb->escape($_POST['title'])."', `description` = '".$wpdb->escape($_POST['description'])."'  WHERE `id`='".(int)$_POST['prodid']."' LIMIT 1";
    $wpdb->query($updatesql);
   echo "<div class='updated'><p align='center'>".TXT_WPSC_BRANDHASBEENEDITED."</p></div>";
     }
  

if(is_numeric($_GET['deleteid']))
  {
  $deletesql = "UPDATE `".$wpdb->prefix."product_brands` SET  `active` = '0' WHERE `id`='".(int)$_GET['deleteid']."' LIMIT 1";
  $wpdb->query($deletesql);
  }

$sql = "SELECT * FROM `".$wpdb->prefix."product_brands` WHERE `active`=1";
$product_list = $wpdb->get_results($sql,ARRAY_A) ;
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
    echo "fillbrandform(".$_POST['prodid'].");";
    }
?>
</script>
<noscript>
</noscript>
<div class="wrap">
  <h2><?php echo TXT_WPSC_DISPLAYBRANDS;?></h2>
  <a href='' onclick='return showaddform()' class='add_item_link'><img src='../wp-content/plugins/wp-shopping-cart/images/package_add.png' alt='<?php echo TXT_WPSC_ADD; ?>' title='<?php echo TXT_WPSC_ADD; ?>' />&nbsp;<span><?php echo TXT_WPSC_ADDBRAND;?></span></a>
  <span id='loadingindicator_span'><img id='loadingimage' src='../wp-content/plugins/wp-shopping-cart/images/indicator.gif' alt='Loading' title='Loading' /></span><br />
  <?php
  $num = 0;
echo "    <table id='productpage'>\n\r";
echo "      <tr><td>\n\r";
echo "        <table id='itemlist'>\n\r";
echo "          <tr class='firstrow'>\n\r";
/*
echo "            <td>\n\r";
echo TXT_WPSC_IMAGE;
echo "            </td>\n\r";
*/

echo "            <td>\n\r";
echo TXT_WPSC_NAME;
echo "            </td>\n\r";

echo "            <td>\n\r";
echo TXT_WPSC_DESCRIPTION;
echo "            </td>\n\r";

echo "            <td>\n\r";
echo TXT_WPSC_EDIT;
echo "            </td>\n\r";

echo "          </tr>\n\r";

if($product_list != null)
  {
  foreach($product_list as $product)
    {
/*
echo "          <tr>\n\r";
echo "            <td>\n\r";
if($product['image'] !=null)
      {
      echo "<img src='../wp-content/plugins/wp-shopping-cart/brand_images/".$product['image']."' title='".$product['name']."' alt='".$product['name']."' width='50' height='50' />";
      }
      else
        {
        echo "<img src='../wp-content/plugins/wp-shopping-cart/no-image-uploaded.gif' title='".$product['name']."' alt='".$product['name']."' width='50' height='50'  />";
        }
echo "            </td>\n\r";
*/
echo "            <td>\n\r";
echo "".stripslashes($product['name'])."";
echo "            </td>\n\r";

$displaydescription = substr(stripslashes($product['description']),0,44);
if($displaydescription != $product['description'])
  {
  $displaydescription_arr = explode(" ",$displaydescription);
  $lastword = count($displaydescription_arr);
  if($lastword > 1)
    {
    unset($displaydescription_arr[$lastword-1]);
    $displaydescription = '';
    $j = 0;
    foreach($displaydescription_arr as $displaydescription_row)
      {
      $j++;
      $displaydescription .= $displaydescription_row;
      if($j < $lastword -1)
        {
        $displaydescription .= " ";
        }
      }
    }
  $displaydescription .= "...";
  }

echo "            <td>\n\r";
echo "".stripslashes($displaydescription)."";
echo "            </td>\n\r";

echo "            <td>\n\r";
echo "<a href='#' onclick='fillbrandform(".$product['id'].");return false;'>".TXT_WPSC_EDIT."</a>";
echo "            </td>\n\r";
echo "          </tr>\n\r";
    }
  }
  
echo "        </table>\n\r";
echo "      </td><td class='secondcol'>\n\r";
echo "        <div id='productform'>";
echo "<form method='POST'  enctype='multipart/form-data' name='editproduct$num'>";
echo "        <table class='producttext'>\n\r";;    

echo "          <tr>\n\r";
echo "            <td colspan='2'>\n\r";
echo "<strong>".TXT_WPSC_EDITBRAND."</strong>";
echo "            </td>\n\r";
echo "          </tr>\n\r";

echo "        </table>\n\r";
echo "        <div id='formcontent'>\n\r";
echo "        </div>\n\r";
echo "</form>";
echo "        </div>";
?>
<div id='additem'>
  <form method='POST' enctype='multipart/form-data'>
  <table>
    <tr>
      <td colspan='2'>
        <strong><?php echo TXT_WPSC_ADDBRAND;?></strong>
      </td>
    </tr>
    <tr>
      <td>
        <?php echo TXT_WPSC_NAME;?>:
      </td>
      <td>
        <input type='text' name='name' value=''  />
      </td>
    </tr>
    <tr>
      <td>
        <?php echo TXT_WPSC_DESCRIPTION;?>:
      </td>
      <td>
        <textarea name='description' cols='40' rows='8'></textarea>
      </td>
    </tr>
    <tr>
      <td>
      </td>
      <td>
        <input type='hidden' name='submit_action' value='add' />
        <input class='button' type='submit' name='submit' value='<?php echo TXT_WPSC_ADD;?>' />
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