<?php
/*
Plugin Name:WP Shopping Cart
Plugin URI: http://www.instinct.co.nz
Description: A plugin that provides a WordPress Shopping Cart. Contact <a href='http://www.instinct.co.nz/?p=16#support'>Instinct Entertainment</a> for support. <br />Click here to to <a href='?wpsc_uninstall=ask'>Uninstall</a>.
Version: 3.6.8 RC1
Author: Thomas Howard of Instinct Entertainment
Author URI: http://www.instinct.co.nz/e-commerce/
/* Major version for "major" releases */
define('WPSC_VERSION', '3.6');
define('WPSC_MINOR_VERSION', '80');

define('WPSC_PRESENTABLE_VERSION', '3.6.8 RC1');

define('WPSC_DEBUG', false);
/*
 * {Notes} Language Files
 * {Required} Yes
 * {WP-Set} Yes (Admin Panel)
 */
define('IS_WP25', version_compare($wp_version, '2.4', '>=') );

// // we need to know where we are, rather than assuming where we are
define('WPSC_FILE_PATH', dirname(__FILE__));
define('WPSC_DIR_NAME', basename(WPSC_FILE_PATH));

$siteurl = get_option('siteurl');

// thanks to ikool for this fix
define('WPSC_FOLDER', dirname(plugin_basename(__FILE__)));
define('WPSC_URL', get_option('siteurl').'/wp-content/plugins/' . WPSC_FOLDER);

//exit("");

if(WPSC_DEBUG === true) {
	function microtime_float() {
		list($usec, $sec) = explode(" ", microtime()); 
		return ((float)$usec + (float)$sec);
	}
	
	function wpsc_debug_start_subtimer($name, $action, $loop = false) {	
		global $wpsc_debug_sections,$loop_debug_increment;
		
		if($loop === true) {
			if ($action == 'start') {
				$loop_debug_increment[$name]++;
				$wpsc_debug_sections[$name.$loop_debug_increment[$name]][$action] = microtime_float();
			} else if($action == 'stop') {
				$wpsc_debug_sections[$name.$loop_debug_increment[$name]][$action] = microtime_float();
			}
		} else {
			$wpsc_debug_sections[$name][$action] = microtime_float();		
		}
	}
	
  $wpsc_start_time = microtime_float();
} else {
	function wpsc_debug_start_subtimer($name) {	
		return null;
	}
}

 

if(get_option('language_setting') != '') {
  require(WPSC_FILE_PATH.'/languages/'.get_option('language_setting'));
} else {
  require(WPSC_FILE_PATH.'/languages/EN_en.php');
}
require(WPSC_FILE_PATH.'/classes/variations.class.php');
require(WPSC_FILE_PATH.'/classes/extra.class.php');
// require(WPSC_FILE_PATH.'/classes/http_client.php');
require(WPSC_FILE_PATH.'/classes/mimetype.php');
require(WPSC_FILE_PATH.'/classes/cart.class.php');
require(WPSC_FILE_PATH.'/classes/xmlparser.php');
if (!IS_WP25) {
	require(WPSC_FILE_PATH.'/editor.php');
} else { 
	require(WPSC_FILE_PATH.'/js/tinymce3/tinymce.php');
}

if(IS_WPMU == 1) {
		$upload_url = get_option('siteurl').'/files';
		$upload_path = ABSPATH.get_option('upload_path');
} else {
	if ( !defined('WP_CONTENT_URL') ) {
			define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
		}
	if ( !defined('WP_CONTENT_DIR') ) {
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	}
	
	$upload_path = WP_CONTENT_DIR."/uploads";
	$upload_url = WP_CONTENT_URL."/uploads";
}

$wpsc_file_dir = "{$upload_path}/wpsc/downloadables/";
$wpsc_preview_dir = "{$upload_path}/wpsc/previews/";
$wpsc_image_dir = "{$upload_path}/wpsc/product_images/";
$wpsc_thumbnail_dir = "{$upload_path}/wpsc/product_images/thumbnails/";
$wpsc_category_dir = "{$upload_path}/wpsc/category_images/";
$wpsc_user_uploads_dir = "{$upload_path}/wpsc/user_uploads/";


// $wpsc_file_dir = ABSPATH."{$upload_path}/files/";
// $wpsc_preview_dir = ABSPATH."{$upload_path}/preview_clips/";
// $wpsc_image_dir = ABSPATH."{$upload_path}/product_images/";
// $wpsc_thumbnail_dir = ABSPATH."{$upload_path}/product_images/thumbnails/";
// $wpsc_category_dir = ABSPATH."{$upload_path}/category_images/";


define('WPSC_FILE_DIR', $wpsc_file_dir);
define('WPSC_PREVIEW_DIR', $wpsc_preview_dir);
define('WPSC_IMAGE_DIR', $wpsc_image_dir);
define('WPSC_THUMBNAIL_DIR', $wpsc_thumbnail_dir);
define('WPSC_CATEGORY_DIR', $wpsc_category_dir);
define('WPSC_USER_UPLOADS_DIR', $wpsc_user_uploads_dir);


/**
* files that are uploaded as part of digital products are not directly downloaded, therefore there is no need for a URL constant for them
*/

$wpsc_preview_url = "{$upload_url}/wpsc/previews/";
$wpsc_image_url = "{$upload_url}/wpsc/product_images/";
$wpsc_thumbnail_url = "{$upload_url}/wpsc/product_images/thumbnails/";
$wpsc_category_url = "{$upload_url}/wpsc/category_images/";
$wpsc_user_uploads_url = "{$upload_url}/wpsc/user_uploads/";


// $wpsc_preview_url = "{$siteurl}/{$upload_path}/preview_clips/";
// $wpsc_image_url = "{$siteurl}/{$upload_path}/product_images/";
// $wpsc_thumbnail_url = "{$siteurl}/{$upload_path}/product_images/thumbnails/";
// $wpsc_category_url = "{$siteurl}/{$upload_path}/category_images/";

define('WPSC_PREVIEW_URL', $wpsc_preview_url);
define('WPSC_IMAGE_URL', $wpsc_image_url);
define('WPSC_THUMBNAIL_URL', $wpsc_thumbnail_url);
define('WPSC_CATEGORY_URL', $wpsc_category_url);
define('WPSC_USER_UPLOADS_URL', $wpsc_user_uploads_url);


/*
 * {Notes} Session will sometimes always exist dependent on server
 * {Notes} Controls user Session
 */
if((!is_array($_SESSION)) xor (!isset($_SESSION['nzshpcrt_cart'])) xor (!$_SESSION)) {
  session_start();
}


if(isset($_SESSION['nzshpcrt_cart'])) {
  foreach((array)$_SESSION['nzshpcrt_cart'] as $key => $item) {
      if(get_class($item) == "__PHP_Incomplete_Class") {
          $_SESSION['nzshpcrt_cart'] = unserialize($_SESSION['nzshpcrt_serialized_cart']);
    }
  }
} else {
  if(isset($_SESSION['nzshpcrt_cart'])) {
    $_SESSION['nzshpcrt_cart'] = unserialize($_SESSION['nzshpcrt_serialized_cart']);
  }
}


if(is_numeric($_GET['sessionid'])) {
  $sessionid = $_GET['sessionid'];
  $cart_log_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid`= ".$sessionid." LIMIT 1");
  if(is_numeric($cart_log_id)) {
    $_SESSION['nzshpcrt_cart'] = null;
    $_SESSION['nzshpcrt_serialized_cart'] = null;
    }
  }



$GLOBALS['nzshpcrt_imagesize_info'] = TXT_WPSC_IMAGESIZEINFO;
$nzshpcrt_log_states[0]['name'] = TXT_WPSC_RECEIVED;
$nzshpcrt_log_states[1]['name'] = TXT_WPSC_PROCESSING;
$nzshpcrt_log_states[2]['name'] = TXT_WPSC_PROCESSED;




class wp_shopping_cart {
  function wp_shopping_cart() {
    return;
  }
  function displaypages()
    {
    /*
     * Fairly standard wordpress plugin API stuff for adding the admin pages, rearrange the order to rearrange the pages
     * The bits to display the options page first on first use may be buggy, but tend not to stick around long enough to be identified and fixed
     * if you find bugs, feel free to fix them.
     *
     * If the permissions are changed here, they will likewise need to be changed for the other secions of the admin that either use ajax
     * or bypass the normal download system.
     * its in an object because nobody has moved it out of the object yet.
     */
    if(function_exists('add_options_page')) {
				//       if(get_option('nzshpcrt_first_load') == 0) {
				//         $base_page = WPSC_DIR_NAME.'/options.php';
				//         add_menu_page(TXT_WPSC_ECOMMERCE, TXT_WPSC_ECOMMERCE, 7, $base_page);
				//         add_submenu_page($base_page,TXT_WPSC_OPTIONS, TXT_WPSC_OPTIONS, 7, WPSC_DIR_NAME.'/options.php');
				//         } else {
			$base_page = WPSC_DIR_NAME.'/display-log.php';
			add_menu_page(TXT_WPSC_ECOMMERCE, TXT_WPSC_ECOMMERCE, 7, $base_page);
			add_submenu_page(WPSC_DIR_NAME.'/display-log.php',TXT_WPSC_PURCHASELOG, TXT_WPSC_PURCHASELOG, 7, WPSC_DIR_NAME.'/display-log.php');
				//         }
      //written by allen
	  add_submenu_page('users.php',TXT_WPSC_ECOMMERCE_SUBSCRIBERS, TXT_WPSC_ECOMMERCE_SUBSCRIBERS, 7, WPSC_DIR_NAME.'/display-ecommerce-subs.php');
	  //exit(ABSPATH.'wp-admin/users.php');
	  //end of written by allen
      
      add_submenu_page($base_page,TXT_WPSC_PRODUCTS, TXT_WPSC_PRODUCTS, 7, WPSC_DIR_NAME.'/display-items.php');
      add_submenu_page($base_page,TXT_WPSC_CATEGORISATION, TXT_WPSC_CATEGORISATION, 7, WPSC_DIR_NAME.'/display-category.php');
      
      add_submenu_page($base_page,TXT_WPSC_VARIATIONS, TXT_WPSC_VARIATIONS, 7, WPSC_DIR_NAME.'/display_variations.php');
      add_submenu_page($base_page,TXT_WPSC_MARKETING, TXT_WPSC_MARKETING, 7, WPSC_DIR_NAME.'/display-coupons.php');
      
      add_submenu_page($base_page,TXT_WPSC_PAYMENTGATEWAYOPTIONS, TXT_WPSC_PAYMENTGATEWAYOPTIONS, 7, WPSC_DIR_NAME.'/gatewayoptions.php');
      add_submenu_page($base_page,TXT_WPSC_FORM_FIELDS, TXT_WPSC_FORM_FIELDS, 7, WPSC_DIR_NAME.'/form_fields.php');
			add_submenu_page($base_page,TXT_WPSC_OPTIONS, TXT_WPSC_OPTIONS, 7, WPSC_DIR_NAME.'/options.php');
      if(function_exists('gold_shpcrt_options')) {
        gold_shpcrt_options($base_page);
        }
//       add_submenu_page($base_page,TXT_WPSC_HELPINSTALLATION, TXT_WPSC_HELPINSTALLATION, 7, WPSC_DIR_NAME.'/instructions.php');
      }
    return;
    }
  }

function nzshpcrt_style() {
  ?>
  <style type="text/css" media="screen">
  
	<?php
	if((get_option('product_view') == 'default') ||  (get_option('product_view') == '')) {
		$thumbnail_width = get_option('product_image_width');
		if($thumbnail_width <= 0) {
			$thumbnail_width = 96;
		}
	?>
		div.default_product_display div.textcol{
			margin-left: <?php echo $thumbnail_width + 10; ?>px !important;
			_margin-left: <?php echo ($thumbnail_width/2) + 5; ?>px !important;
		}
			
			
		div.default_product_display  div.textcol div.imagecol{
			position:absolute;
			top:0px;
			left: 0px;
			margin-left: -<?php echo $thumbnail_width + 10; ?>px !important;
		}
	<?php
	}
	
	
		
	$single_thumbnail_width = get_option('single_view_image_width');
	$single_thumbnail_height = get_option('single_view_image_height');
	if($single_thumbnail_width <= 0) {
		$single_thumbnail_width = 128;
	}
	?>
	
	div.single_product_display div.textcol{
		margin-left: <?php echo $single_thumbnail_width  + 10; ?>px !important;
		_margin-left: <?php echo ($single_thumbnail_width/2) + 5; ?>px !important;
		min-height: <?php echo $single_thumbnail_height + 10;?>px;
		_height: <?php echo $single_thumbnail_height + 10;?>px;
	}
		
		
	div.single_product_display  div.textcol div.imagecol{
		position:absolute;
		top:0px;
		left: 0px;
		margin-left: -<?php echo $single_thumbnail_width + 10; ?>px !important;
	}
	
  
  
    <?php
  if(is_numeric($_GET['brand']) || (get_option('show_categorybrands') == 3)) {
    $brandstate = 'block';
    $categorystate = 'none';
    } else {
    $brandstate = 'none';
    $categorystate = 'block';
    }
      
    ?>
    div#categorydisplay{
    display: <?php echo $categorystate; ?>;
    }
    
    div#branddisplay{
    display: <?php echo $brandstate; ?>;
    }
  </style>
  <?php
  }
  
function nzshpcrt_javascript()
  {
  $siteurl = get_option('siteurl'); 
	echo "";
  if(($_SESSION['nzshpcrt_cart'] == null) && (get_option('show_sliding_cart') == 1)) {
		?>
			<style type="text/css" media="screen">
		div#sliding_cart{
			display: none;
			}
		</style>
		<?php
	} else {
		?>
			<style type="text/css" media="screen">
		div#sliding_cart{
			display: block;
			}
		</style>
	<?php
	}
  ?>
<?php if (get_option('product_ratings') == 1){ ?>
<link href='<?php echo WPSC_URL; ?>/product_rater.css' rel="stylesheet" type="text/css" />
<?php } ?>
<link href='<?php echo WPSC_URL; ?>/thickbox.css' rel="stylesheet" type="text/css" />
<?php if (get_option('catsprods_display_type') == 1){ ?>
  <script language="JavaScript" type="text/javascript" src="<?php echo WPSC_URL; ?>/js/slideMenu.js"></script>
<?php } ?>
<script language='JavaScript' type='text/javascript'>
jQuery.noConflict();
/* base url */
var base_url = "<?php echo $siteurl; ?>";
var WPSC_URL = "<?php echo WPSC_URL; ?>";

/* LightBox Configuration start*/
var fileLoadingImage = "<?php echo WPSC_URL; ?>/images/loading.gif";    
var fileBottomNavCloseImage = "<?php echo WPSC_URL; ?>/images/closelabel.gif";
var fileThickboxLoadingImage = "<?php echo WPSC_URL; ?>/images/loadingAnimation.gif";    
var resizeSpeed = 9;  // controls the speed of the image resizing (1=slowest and 10=fastest)
var borderSize = 10;  //if you adjust the padding in the CSS, you will need to update this variable
jQuery(document).ready( function() {
  <?php
  if(get_option('show_sliding_cart') == 1) {
    if(is_numeric($_SESSION['slider_state'])) {
      if($_SESSION['slider_state'] == 0) {
        ?>
        jQuery("#sliding_cart").css({ display: "none"});  
        <?php
			} else {
        ?>
        jQuery("#sliding_cart").css({ display: "block"});  
        <?php
			}
    } else {
			if($_SESSION['nzshpcrt_cart'] == null) {
				?>
				jQuery("#sliding_cart").css({ display: "none"});  
				<?php
			} else {
				?>
				jQuery("#sliding_cart").css({ display: "block"});  
				<?php
			}
		}
	}
  ?>
});
</script>
<script src="<?php echo WPSC_URL; ?>/ajax.js" language='JavaScript' type="text/javascript"></script>
<script src="<?php echo WPSC_URL; ?>/user.js" language='JavaScript' type="text/javascript">
</script>



<?php
  $theme_path = WPSC_FILE_PATH. '/themes/';
  if((get_option('wpsc_selected_theme') != '') && (file_exists($theme_path.get_option('wpsc_selected_theme')."/".get_option('wpsc_selected_theme').".css") )) {    
    ?>    
<link href='<?php echo WPSC_URL; ?>/themes/<?php echo get_option('wpsc_selected_theme')."/".get_option('wpsc_selected_theme').".css"; ?>' rel="stylesheet" type="text/css" />
    <?php
    } else {
    ?>    
<link href='<?php echo WPSC_URL; ?>/themes/default/default.css' rel="stylesheet" type="text/css" />
    <?php
    }
    ?>    
<link href='<?php echo WPSC_URL; ?>/themes/compatibility.css' rel="stylesheet" type="text/css" />
    <?php
  }

function wpsc_admin_css() {
  $siteurl = get_option('siteurl'); 
  if(strpos($_SERVER['REQUEST_URI'], WPSC_DIR_NAME.'') !== false) {
?>
<link href='<?php echo WPSC_URL; ?>/admin.css' rel="stylesheet" type="text/css" />
<link href='<?php echo WPSC_URL; ?>/js/jquery.ui.tabs.css' rel="stylesheet" type="text/css" />
<?php

if($_GET['page'] == 'wp-shopping-cart/display-log.php') {
	?>
		<link href='<?php echo $siteurl; ?>/wp-admin/css/dashboard.css?ver=2.6' rel="stylesheet" type="text/css" />
	<?php
}
?>
<link href='<?php echo WPSC_URL; ?>/thickbox.css' rel="stylesheet" type="text/css" />
<script src="<?php echo WPSC_URL; ?>/ajax.js" language='JavaScript' type="text/javascript"></script>

<script language="JavaScript" type="text/javascript" src="<?php echo WPSC_URL; ?>/js/jquery.tooltip.js"></script>
<script language='JavaScript' type='text/javascript'>

/* base url */
var base_url = "<?php echo $siteurl; ?>";
var WPSC_URL = "<?php echo WPSC_URL; ?>";

/* LightBox Configuration start*/
var fileLoadingImage = "<?php echo WPSC_URL; ?>/images/loading.gif";    
var fileBottomNavCloseImage = "<?php echo WPSC_URL; ?>/images/closelabel.gif";
var fileThickboxLoadingImage = "<?php echo WPSC_URL; ?>/images/loadingAnimation.gif";    

var resizeSpeed = 9;  

var borderSize = 10;
/* LightBox Configuration end*/
/* custom admin functions start*/
<?php
    echo "var TXT_WPSC_DELETE = '".TXT_WPSC_DELETE."';\n\r";
    echo "var TXT_WPSC_TEXT = '".TXT_WPSC_TEXT."';\n\r";
    echo "var TXT_WPSC_EMAIL = '".TXT_WPSC_EMAIL."';\n\r";
    echo "var TXT_WPSC_COUNTRY = '".TXT_WPSC_COUNTRY."';\n\r";
    echo "var TXT_WPSC_TEXTAREA = '".TXT_WPSC_TEXTAREA."';\n\r";
    echo "var TXT_WPSC_HEADING = '".TXT_WPSC_HEADING."';\n\r";
    echo "var TXT_WPSC_COUPON = '".TXT_WPSC_COUPON."';\n\r";
    echo "var HTML_FORM_FIELD_TYPES =\"<option value='text' >".TXT_WPSC_TEXT."</option>";
    echo "<option value='email' >".TXT_WPSC_EMAIL."</option>";
    echo "<option value='address' >".TXT_WPSC_ADDRESS."</option>";
    echo "<option value='city' >".TXT_WPSC_CITY."</option>";
    echo "<option value='country'>".TXT_WPSC_COUNTRY."</option>";
    echo "<option value='delivery_address' >".TXT_WPSC_DELIVERY_ADDRESS."</option>";
    echo "<option value='delivery_city' >".TXT_WPSC_DELIVERY_CITY."</option>";
    echo "<option value='delivery_country'>".TXT_WPSC_DELIVERY_COUNTRY."</option>";
    echo "<option value='textarea' >".TXT_WPSC_TEXTAREA."</option>";    
    echo "<option value='heading' >".TXT_WPSC_HEADING."</option>";
    echo "<option value='coupon' >".TXT_WPSC_COUPON."</option>\";\n\r";
?>
/* custom admin functions end*/
</script>
<script language="JavaScript" type="text/javascript" src="<?php echo WPSC_URL; ?>/js/thickbox.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WPSC_URL; ?>/js/jquery.tooltip.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WPSC_URL; ?>/js/dimensions.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo WPSC_URL; ?>/admin.js"></script>
<?php
	}
}

function nzshpcrt_displaypages()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->displaypages();
  }

function nzshpcrt_adminpage()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->adminpage();
  }
  
function nzshpcrt_additem()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->additem();
  }

function nzshpcrt_displayitems()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->displayitems();
  }
  
function nzshpcrt_instructions()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->instructions();
  }

function nzshpcrt_options()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->options();
  }

function nzshpcrt_gatewayoptions()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->gatewayoptions();
  }

function nzshpcrt_addcategory()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->addcategory();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  
function nzshpcrt_editcategory()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->editcategory();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  
function nzshpcrt_editvariations()
  {
  $nzshpcrt = new wp_shopping_cart;
  $nzshpcrt->editvariations();
  //$GLOBALS['nzshpcrt_activateshpcrt'] = true;
  }
  
function nzshpcrt_submit_ajax()
  {
  global $wpdb,$user_level,$wp_rewrite;
  get_currentuserinfo();  
  if(get_option('permalink_structure') != '') {
    $seperator ="?";
	} else {
		$seperator ="&amp;";
	}
   
   $cartt = $_SESSION['nzshpcrt_cart'];
   $cartt1=$cartt[0]->product_id;
   
  // if is an AJAX request, cruddy code, could be done better but getting approval would be impossible
  if(($_POST['ajax'] == "true") || ($_GET['ajax'] == "true"))
    {
	if ($_POST['changetax'] == "true") {
		
		if (isset($_POST['billing_region'])){
			$billing_region=$_POST['billing_region'];
		} else {
			$billing_region=$_SESSION['selected_region'];
		}
		$billing_country=$_POST['billing_country'];
		foreach($cartt as $cart_item) {
			$product_id = $cart_item->product_id;
			$quantity = $cart_item->quantity;
			//echo("<pre>".print_r($cart_item->product_variations,true)."</pre>");
			$product = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id` = '$product_id' LIMIT 1",ARRAY_A);
		
			if($product['donation'] == 1) {
				$price = $quantity * $cart_item->donation_price;
			} else {
				$price = $quantity * calculate_product_price($product_id, $cart_item->product_variations);
				if($product['notax'] != 1) {
					$tax += nzshpcrt_calculate_tax($price, $billing_country, $billing_region) - $price;
				}
			$all_donations = false;
			}

			if($_SESSION['delivery_country'] != null) {
				$total_shipping += nzshpcrt_determine_item_shipping($product['id'], $quantity, $_SESSION['delivery_country']);
			}
		}
		echo $tax.":".$price.":".$total_shipping;
		exit();
	}
	
	
	if ($_POST['submittogoogle']) {
		$newvalue=$_POST['value'];
		$amount=$_POST['amount'];
		$reason=$_POST['reason'];
		$comment=$_POST['comment'];
		$message=$_POST['message'];
		$amount=number_format($amount, 2, '.', '');
		$log_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `id` = '".$_POST['id']."' LIMIT 1",ARRAY_A);  
		if (($newvalue==2) && function_exists('wpsc_member_activate_subscriptions')){
			wpsc_member_activate_subscriptions($_POST['id']);
		}
		$google_status = unserialize($log_data['google_status']);
		
		switch($newvalue) {
			case "Charge":
				if ($google_status[0]!='CANCELLED_BY_GOOGLE') {
					if ($amount=='') {
						$google_status['0']='Partially Charged';
					} else {
						$google_status['0']='CHARGED';
						$google_status['partial_charge_amount']=$amount;
					}
				}
				break;
				
			case "Cancel":
				if ($google_status[0]!='CANCELLED_BY_GOOGLE')
				$google_status[0]='CANCELLED';
				if ($google_status[1]!='DELIVERED')
					$google_status[1]='WILL_NOT_DELIVER';
				break;
				
			case "Refund":
				if ($amount=='') {
					$google_status['0']='Partially Refund';
				} else {
					$google_status['0']='REFUND';
					$google_status['partial_refund_amount']=$amount;
				}
				break;
				
			case "Ship":
				if ($google_status[1]!='WILL_NOT_DELIVER')
					$google_status[1]='DELIVERED';
				break;
				
			case "Archive":
				$google_status[1]='ARCHIVED';
				break;
		}
		$google_status_sql="UPDATE `".$wpdb->prefix."purchase_logs` SET google_status='".serialize($google_status)."' WHERE `id` = '".$_POST['id']."' LIMIT 1";
		$wpdb->query($google_status_sql);
		$merchant_id = get_option('google_id');
		$merchant_key = get_option('google_key');
		$server_type = get_option('google_server_type');
		$currency = get_option('google_cur');
		$Grequest = new GoogleRequest($merchant_id, $merchant_key, $server_type,$currency);
		$google_order_number=$wpdb->get_var("SELECT google_order_number FROM `".$wpdb->prefix."purchase_logs` WHERE `id` = '".$_POST['id']."' LIMIT 1");
		switch ($newvalue) {
			case 'Charge':
				$Grequest->SendChargeOrder($google_order_number,$amount);
				break;
				
			case 'Ship':
				$Grequest->SendDeliverOrder($google_order_number);
				break;
				
			case 'Archive':
				$Grequest->SendArchiveOrder($google_order_number);
				break;
			
			case 'Refund':
				$Grequest->SendRefundOrder($google_order_number,$amount,$reason);
				break;
				
			case 'Cancel':
				$Grequest->SendCancelOrder($google_order_number,$reason,$comment);
				break;
			
			case 'Send Message':
				$Grequest->SendBuyerMessage($google_order_number,$message);
				break;
		}
		$newvalue++;
		$update_sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '".$newvalue."' WHERE `id` = '".$_POST['id']."' LIMIT 1";  
		//$wpdb->query($update_sql);
		
		exit();
	}

		////changes for usps
	if ($_POST['uspsswitch']) {
		foreach ($_SESSION['uspsQuote'] as $quotes) {
			$total=$_POST['total'];
			if ($quotes[$_POST['key']]!='') {
				echo nzshpcrt_currency_display($total+$quotes[$_POST['key']],1);
					echo "<input type='hidden' value='".$total."' id='shopping_cart_total_price'>";
				$_SESSION['usps_shipping']= $quotes[$_POST['key']];
			}
		}
		
		exit();
	}
	//changes for usps ends
	
    if(($_GET['user'] == "true") && is_numeric($_POST['prodid']))
      {
	  $memberstatus = get_product_meta($_POST['prodid'],'is_membership',true);
	  if(($memberstatus[0]=='1') && ($_SESSION['nzshopcrt_cart']!=NULL)){
	  } else{
		  $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$_POST['prodid']."' LIMIT 1";
		  $item_data = $wpdb->get_results($sql,ARRAY_A);
		  
		  $item_quantity = 0;
		  if($_SESSION['nzshpcrt_cart'] != null)
			{
			foreach($_SESSION['nzshpcrt_cart'] as $cart_key => $cart_item)
			  {
				if (($memberstatus[0]!='1')&&($_SESSION['nzshpcrt_cart']!=NULL)){
					if($cart_item->product_id == $_POST['prodid']) {
						if(($_SESSION['nzshpcrt_cart'][$cart_key]->product_variations === $_POST['variation'])&&($_SESSION['nzshpcrt_cart'][$cart_key]->extras === $_POST['extras'])) {
							$item_quantity += $_SESSION['nzshpcrt_cart'][$cart_key]->quantity;
							$item_variations = $_SESSION['nzshpcrt_cart'][$cart_key]->product_variations;
						}
					}
				}
			  }
			}
		  
		  $item_stock = null;
		  $variation_count = count($_POST['variation']);
		  if(($variation_count >= 1) && ($variation_count <= 2)) {
				foreach($_POST['variation'] as $variation_id) {
					if(is_numeric($variation_id)) {
						$variation_ids[] = (int)$variation_id;
					}
				}
				if(count($variation_ids) == 2)	{
					$variation_stock_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '".$_POST['prodid']."' AND (`variation_id_1` = '".$variation_ids[0]."' AND `variation_id_2` = '".$variation_ids[1]."') OR (`variation_id_1` = '".$variation_ids[1]."' AND `variation_id_2` = '".$variation_ids[0]."') LIMIT 1",ARRAY_A);
					$item_stock = $variation_stock_data['stock'];
				} else if(count($variation_ids) == 1) {
					$variation_stock_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '".$_POST['prodid']."' AND (`variation_id_1` = '".$variation_ids[0]."' AND `variation_id_2` = '0') LIMIT 1",ARRAY_A);
					$item_stock = $variation_stock_data['stock'];
				}
			}
			
		  if($item_stock === null) {
				$item_stock = $item_data[0]['quantity'];
			}
		  
		  if((($item_data[0]['quantity_limited'] == 1) && ($item_stock > 0) && ($item_stock > $item_quantity)) || ($item_data[0]['quantity_limited'] == 0)) {
				$cartcount = count($_SESSION['nzshpcrt_cart']);
				if(is_array($_POST['variation'])) {  $variations = $_POST['variation'];  }  else  { $variations = null; }
				if(is_array($_POST['extras'])) {  $extras = $_POST['extras'];  }  else  { $extras = null; }
				$updated_quantity = false;
				if($_SESSION['nzshpcrt_cart'] != null) {
					foreach($_SESSION['nzshpcrt_cart'] as $cart_key => $cart_item) {
						if ((!($memberstatus[0]=='1')&&(count($_SESSION['nzshpcrt_cart'])>0))) {
							if((int)$cart_item->product_id === (int)$_POST['prodid']) {  // force both to integer before testing for identicality
								if(($_SESSION['nzshpcrt_cart'][$cart_key]->extras === $extras)&&($_SESSION['nzshpcrt_cart'][$cart_key]->product_variations === $variations) && ((int)$_SESSION['nzshpcrt_cart'][$cart_key]->donation_price == (int)$_POST['donation_price'])) {
									if(is_numeric($_POST['quantity'])) {
										$_SESSION['nzshpcrt_cart'][$cart_key]->quantity += (int)$_POST['quantity'];
									} else {
										$_SESSION['nzshpcrt_cart'][$cart_key]->quantity++;
									}
									$updated_quantity = true;
								}
							}
					}
				}
			}
			if($item_data[0]['donation'] == 1) {
			  $donation = $_POST['donation_price'];
			} else 	{
				$donation = false;
			}
			if(!(($memberstatus[0]=='1')&&(count($_SESSION['nzshpcrt_cart'])>0))){
				$status = get_product_meta($cartt1, 'is_membership', true);
				if ($status[0]=='1'){
				  exit();
				}	
				if($updated_quantity === false) {
				  if(is_numeric($_POST['quantity'])) {
						if($_POST['quantity'] > 0) {
							$new_cart_item = new cart_item($_POST['prodid'],$variations,$_POST['quantity'], $donation,$extras);
					  }
					} else {
						//echo "correct";
					  $new_cart_item = new cart_item($_POST['prodid'],$variations, 1, $donation,$extras);
					}
				  $_SESSION['nzshpcrt_cart'][] = $new_cart_item;
				  }
			  }
			} else {
			  $quantity_limit = true;
			}
		  
		  $cart = $_SESSION['nzshpcrt_cart'];
		  
		  if (($memberstatus[0]=='1')&&(count($cart)>1)) {
			} else {
				$status = get_product_meta($cartt1, 'is_membership', true);
				if ($status[0]=='1'){
					exit('st');
				}
			  echo  "if(document.getElementById('shoppingcartcontents') != null)
					  {
					  document.getElementById('shoppingcartcontents').innerHTML = \"".str_replace(Array("\n","\r") , "",addslashes(nzshpcrt_shopping_basket_internals($cart,$quantity_limit))). "\";
					  }
					";
		
			  if(($_POST['prodid'] != null) &&(get_option('fancy_notifications') == 1)) {
				echo "if(document.getElementById('fancy_notification_content') != null)
					  {
					  document.getElementById('fancy_notification_content').innerHTML = \"".str_replace(Array("\n","\r") , "",addslashes(fancy_notification_content($_POST['prodid'], $quantity_limit))). "\";
					  jQuery('#loading_animation').css('display', 'none');
					  jQuery('#fancy_notification_content').css('display', 'block');  
					  }
					";
				}
			  
			  if($_SESSION['slider_state'] == 0) {
				echo  'jQuery("#sliding_cart").css({ display: "none"});'."\n\r";
				} else {
				echo  'jQuery("#sliding_cart").css({ display: "block"});'."\n\r";
				}
			}
		}
      exit();
		} else if(($_POST['user'] == "true") && ($_POST['emptycart'] == "true")) {
			//exit("/* \n\r ".get_option('shopping_cart_url')." \n\r ".print_r($_POST,true)." \n\r */");
			$_SESSION['nzshpcrt_cart'] = '';			
			$_SESSION['nzshpcrt_cart'] = Array();      
			echo  "if(document.getElementById('shoppingcartcontents') != null) {   
			document.getElementById('shoppingcartcontents').innerHTML = \"".str_replace(Array("\n","\r") , "", addslashes(nzshpcrt_shopping_basket_internals($cart))). "\";
			}\n\r";
			
			if($_POST['current_page'] == get_option('shopping_cart_url')) {
			  echo "window.location = '".get_option('shopping_cart_url')."';\n\r"; // if we are on the checkout page, redirect back to it to clear the non-ajax cart too
			}
			exit();
		}

	if ($_POST['store_list']=="true") {
		$map_data['address'] = $_POST['addr'];
		$map_data['city'] = $_POST['city'];
		$map_data['country'] = 'US';
		$map_data['zipcode']='';
		$map_data['radius'] = '50000';
		$map_data['state'] = '';
		$map_data['submit'] = 'Find Store';
		$stores = getdistance($map_data);
		$i=0;
		while($rows = mysql_fetch_array($stores)) {
			//echo "<pre>".print_r($rows,1)."</pre>";
			if ($i==0) {
				$closest_store = $rows[5];
			}
			$i++;
			$store_list[$i] = $rows[5];
		}
	foreach ($store_list as $store){
		$output.="<option value='$store'>$store</option>";
	}
	echo $output;
	exit();
	}
    
    if($_POST['admin'] == "true") {
    
			if(is_numeric($_POST['prodid'])) {
				/* fill product form */    
				echo nzshpcrt_getproductform($_POST['prodid']);
				exit();
			} else if(is_numeric($_POST['catid'])) {
				/* fill category form */   
				echo nzshpcrt_getcategoryform($_POST['catid']);
				exit();
			} else if(is_numeric($_POST['brandid'])) {
				/* fill brand form */   
				echo nzshpcrt_getbrandsform($_POST['brandid']);
				exit();
			} else if(is_numeric($_POST['variation_id'])) {  
				echo nzshpcrt_getvariationform($_POST['variation_id']);
				exit();
			}
			
			
			if($_POST['hide_ecom_dashboard'] == 'true') {
				require_once (ABSPATH . WPINC . '/rss.php');
				$rss = fetch_rss('http://www.instinct.co.nz/feed/');				
				$rss->items = array_slice($rss->items, 0, 5);
				$rss_hash = sha1(serialize($rss->items));				
        update_option('wpsc_ecom_news_hash', $rss_hash);
				exit(1);
			}			
			
			if(($_POST['remove_meta'] == 'true') && is_numeric($_POST['meta_id'])) {
			  $meta_id = (int)$_POST['meta_id'];
			  $selected_meta = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}wpsc_productmeta` WHERE `id` IN('{$meta_id}') ",ARRAY_A);
			  if($selected_meta != null) {
			    if($wpdb->query("DELETE FROM `{$wpdb->prefix}wpsc_productmeta` WHERE `id` IN('{$meta_id}')  LIMIT 1")) {
			      echo $meta_id;
			      exit();
			    }
			  }
			  echo 0;
				exit();
			}	
      exit();
		}
            
    
    if(is_numeric($_POST['currencyid'])){
      $currency_data = $wpdb->get_results("SELECT `symbol`,`symbol_html`,`code` FROM `".$wpdb->prefix."currency_list` WHERE `id`='".$_POST['currencyid']."' LIMIT 1",ARRAY_A) ;
      $price_out = null;
      if($currency_data[0]['symbol'] != '') {
        $currency_sign = $currency_data[0]['symbol_html'];
			} else {
				$currency_sign = $currency_data[0]['code'];
			}
      echo $currency_sign;
      exit();
		}
      //echo "--==->";
	if($_POST['buynow'] == "true") {
		$id = $_REQUEST['product_id'];
		$price = $_REQUEST['price'];
		$downloads = get_option('max_downloads');
		$product_sql = "SELECT * FROM ".$wpdb->prefix."product_list WHERE id = ".$id." LIMIT 1";
		$product_info = $wpdb->get_results($product_sql, ARRAY_A);
		$product_info = $product_info[0];
		$sessionid = (mt_rand(100,999).time());
		$sql = "INSERT INTO `".$wpdb->prefix."purchase_logs` ( `totalprice` , `sessionid` , `date`, `billing_country`, `shipping_country`,`shipping_region`, `user_ID`, `discount_value` ) VALUES ( '".$price."', '".$sessionid."', '".time()."', 'BuyNow', 'BuyNow', 'BuyNow' , NULL , 0)";
		$wpdb->query($sql) ;
		$log_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE `sessionid` IN('".$sessionid."') LIMIT 1") ;
		$cartsql = "INSERT INTO `".$wpdb->prefix."cart_contents` ( `prodid` , `purchaseid`, `price`, `pnp`, `gst`, `quantity`, `donation`, `no_shipping` ) VALUES ('".$id."', '".$log_id."','".$price."','0', '0','1', '".$donation."', '1')";
		$wpdb->query($cartsql);
		$wpdb->query("INSERT INTO `".$wpdb->prefix."download_status` ( `fileid` , `purchid` , `downloads` , `active` , `datetime` ) VALUES ( '".$product_info['file']."', '".$log_id."', '$downloads', '0', NOW( ));");
	exit();
	}
	
	if(($_POST['changeorder'] == "true") && is_numeric($_POST['category_id'])) {
		$category_id = (int)$_POST['category_id'];
		$hash=$_POST['sort1'];
		$order=1;
		foreach($hash as $id) {
			$wpdb->query("UPDATE `".$wpdb->prefix."product_order` SET `order`=$order WHERE `product_id`=".(int)$id." AND `category_id`=".(int)$category_id." LIMIT 1");
			$order++;
		}  
	exit(" ");
	}
	
    
    /* rate item */    
    if(($_POST['rate_item'] == "true") && is_numeric($_POST['product_id']) && is_numeric($_POST['rating']))
      {
      $nowtime = time();
      $prodid = $_POST['product_id'];
      $ip_number = $_SERVER['REMOTE_ADDR'];
      $rating = $_POST['rating'];
      
      $cookie_data = explode(",",$_COOKIE['voting_cookie'][$prodid]);
      
      if(is_numeric($cookie_data[0]) && ($cookie_data[0] > 0))
        {
        $vote_id = $cookie_data[0];
        $wpdb->query("UPDATE `".$wpdb->prefix."product_rating` SET `rated` = '".$rating."' WHERE `id` ='".$vote_id."' LIMIT 1 ;");
        }
        else
          {
          $insert_sql = "INSERT INTO `".$wpdb->prefix."product_rating` ( `ipnum`  , `productid` , `rated`, `time`) VALUES ( '".$ip_number."', '".$prodid."', '".$rating."', '".$nowtime."');";
          $wpdb->query($insert_sql);
          
          $data = $wpdb->get_results("SELECT `id`,`rated` FROM `".$wpdb->prefix."product_rating` WHERE `ipnum`='".$ip_number."' AND `productid` = '".$prodid."'  AND `rated` = '".$rating."' AND `time` = '".$nowtime."' ORDER BY `id` DESC LIMIT 1",ARRAY_A) ;
          
          $vote_id = $data[0]['id'];
          setcookie("voting_cookie[$prodid]", ($vote_id.",".$rating),time()+(60*60*24*360));
          }   
      
      
      
      $output[1]= $prodid;
      $output[2]= $rating;
      echo $output[1].",".$output[2];
      exit();
      }
//written by allen
	if ($_REQUEST['save_tracking_id'] == "true"){
		$id = $_POST['id'];
		$value = $_POST['value'];
		$update_sql = "UPDATE ".$wpdb->prefix."purchase_logs SET track_id = '".$value."' WHERE id=$id";
		$wpdb->query($update_sql);
		exit();
	}
      
    if(($_POST['get_rating_count'] == "true") && is_numeric($_POST['product_id']))
      {
      $prodid = $_POST['product_id'];
      $data = $wpdb->get_results("SELECT COUNT(*) AS `count` FROM `".$wpdb->prefix."product_rating` WHERE `productid` = '".$prodid."'",ARRAY_A) ;
      echo $data[0]['count'].",".$prodid;
      exit();
      }
      
      /// Pointless AJAX call is pointless
			// 	if(isset($_POST['changeperpage'])) {
			// 		$item_per_page = $_POST['changeperpage'];
			// 		echo $item_per_page;
			// 		exit();
			// 	}
      
    if(($_POST['remove_variation_value'] == "true") && is_numeric($_POST['variation_value_id']))
      {
      $wpdb->query("DELETE FROM `".$wpdb->prefix."variation_values_associations` WHERE `value_id` = '".$_POST['variation_value_id']."'");
      $wpdb->query("DELETE FROM `".$wpdb->prefix."variation_values` WHERE `id` = '".$_POST['variation_value_id']."' LIMIT 1");
      exit();
      }
      
    if(($_POST['get_updated_price'] == "true") && is_numeric($_POST['product_id']))
      {
      $notax = $wpdb->get_var("SELECT `notax` FROM `".$wpdb->prefix."product_list` WHERE `id` IN('".$_POST['product_id']."') LIMIT 1");
      foreach((array)$_POST['variation'] as $variation)
        {
        if(is_numeric($variation))
          {
          $variations[] = $variation;
          }
        }
	foreach((array)$_POST['extra'] as $extra)
        {
        if(is_numeric($extra))
          {
          $extras[] = $extra;
          }
        }
	$pm=$_POST['pm'];
	echo "product_id=".$_POST['product_id'].";\n";
	
	echo "price=\"".nzshpcrt_currency_display(calculate_product_price($_POST['product_id'], $variations,'stay',$extras), $notax)."\";\n";
      //exit(print_r($extras,1));
	exit();
      }
      
    if(($_REQUEST['log_state'] == "true") && is_numeric($_POST['id']) && is_numeric($_POST['value'])) {
			$newvalue = $_POST['value'];
			if ($_REQUEST['suspend']=='true'){
				if ($_REQUEST['value']==1){
					wpsc_member_dedeactivate_subscriptions($_POST['id']);
				} else {
					wpsc_member_deactivate_subscriptions($_POST['id']);
				}
				exit();
			} else {
      
				$log_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `id` = '".$_POST['id']."' LIMIT 1",ARRAY_A);  
				if (($newvalue==2) && function_exists('wpsc_member_activate_subscriptions')){
					wpsc_member_activate_subscriptions($_POST['id']);
				}
				
				$update_sql = "UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '".$newvalue."' WHERE `id` = '".$_POST['id']."' LIMIT 1";  
				$wpdb->query($update_sql);
				//echo("/*");
				if(($newvalue > $log_data['processed']) && ($log_data['processed'] < 2)) {
					transaction_results($log_data['sessionid'],false);
				}      
				//echo("*/");
				$stage_sql = "SELECT * FROM `".$wpdb->prefix."purchase_statuses` WHERE `id`='".$newvalue."' AND `active`='1' LIMIT 1";
				$stage_data = $wpdb->get_row($stage_sql,ARRAY_A);
						
				echo "document.getElementById(\"form_group_".$_POST['id']."_text\").innerHTML = '".$stage_data['name']."';\n";
				echo "document.getElementById(\"form_group_".$_POST['id']."_text\").style.color = '#".$stage_data['colour']."';\n";
				
				
				$year = date("Y");
				$month = date("m");
				$start_timestamp = mktime(0, 0, 0, $month, 1, $year);
				$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
				
				echo "document.getElementById(\"log_total_month\").innerHTML = '".addslashes(nzshpcrt_currency_display(admin_display_total_price($start_timestamp, $end_timestamp),1))."';\n";
				echo "document.getElementById(\"log_total_absolute\").innerHTML = '".addslashes(nzshpcrt_currency_display(admin_display_total_price(),1))."';\n";
				exit();
		  }
    }
      
	if(($_POST['list_variation_values'] == "true") && is_numeric($_POST['new_variation_id'])) {
		$variation_processor = new nzshpcrt_variations();
		echo "variation_value_id = \"".$_POST['new_variation_id']."\";\n";
		echo "variation_value_html = \"".$variation_processor->display_variation_values($_POST['prefix'],$_POST['new_variation_id'])."\";\n";
		$variations_selected = array_values(array_unique(array_merge((array)$_POST['new_variation_id'], (array)$_POST['variation_id'])));		
		echo "variation_subvalue_html = \"".str_replace("\n\r", '\n\r', $variation_processor->variations_add_grid_view((array)$variations_selected))."\";\n";
		//echo "/*\n\r".print_r(array_values(array_unique(array_merge((array)$_POST['new_variation_id'], $_POST['variation_id']))),true)."\n\r*/";
		exit();
	}
      
	if(($_POST['redisplay_variation_values'] == "true")) {
		$variation_processor = new nzshpcrt_variations();
		$variations_selected = array_values(array_unique(array_merge((array)$_POST['new_variation_id'], (array)$_POST['variation_id'])));		
		foreach($variations_selected as $variation_id) {
		  // cast everything to integer to make sure nothing nasty gets in.
		  $variation_list[] = (int)$variation_id;
		}
		echo $variation_processor->variations_add_grid_view((array)$variation_list);
		//echo "/*\n\r".print_r(array_values(array_unique($_POST['variation_id'])),true)."\n\r*/";
		exit();
	}
	

	if(($_POST['edit_variation_value_list'] == 'true') && is_numeric($_POST['variation_id']) && is_numeric($_POST['product_id'])) {
		$variation_id = (int)$_POST['variation_id'];
		$product_id = (int)$_POST['product_id'];
		$variations_processor = new nzshpcrt_variations();
		$variation_values = $variations_processor->falsepost_variation_values($variation_id);
		if(is_array($variation_values)) {
			//echo(print_r($variation_values,true));
			$check_variation_added = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."variation_associations` WHERE `type` IN ('product') AND `associated_id` IN ('{$product_id}') AND `variation_id` IN ('{$variation_id}') LIMIT 1");
			if($check_variation_added == null) {
				$variations_processor->add_to_existing_product($product_id,$variation_values);			
			}
			echo $variations_processor->display_attached_variations($product_id);
			echo $variations_processor->variations_grid_view($product_id); 
		} else {
			echo "false";
		}
		exit();
	}
      

            
	if(($_POST['remove_form_field'] == "true") && is_numeric($_POST['form_id'])) {
		//exit(print_r($user,true));
		if(current_user_can('level_7')) {
			$wpdb->query("UPDATE `".$wpdb->prefix."collect_data_forms` SET `active` = '0' WHERE `id` ='".$_POST['form_id']."' LIMIT 1 ;");
			exit(' ');
		}
	}
      
      
      /*
       * function for handling the checkout billing address
       */      
    if(preg_match("/[a-zA-Z]{2,4}/", $_POST['billing_country']))
      {
      if($_SESSION['selected_country'] == $_POST['billing_country'])
        {
        $do_not_refresh_regions = true;
        }
        else
        {
        $do_not_refresh_regions = false;
        $_SESSION['selected_country'] = $_POST['billing_country'];
        }
	
	
	
      if(is_numeric($_POST['form_id']))
        {
        $form_id = $_POST['form_id'];
        $html_form_id = "region_country_form_$form_id";
        }
        else
          {
          $html_form_id = 'region_country_form';
          }
        
        if(is_numeric($_POST['billing_region']))
          {
          $_SESSION['selected_region'] = $_POST['billing_region'];
          }
      $cart =& $_SESSION['nzshpcrt_cart'];
	if (($memberstatus[0]=='1')&&(count($cart)>0)){
			echo "
			";
			}else{
			if ($status[0]=='1'){
			  exit();
			}
			  echo  "if(document.getElementById('shoppingcartcontents') != null)
					  {
					  document.getElementById('shoppingcartcontents').innerHTML = \"".str_replace(Array("\n","\r") , "",addslashes(nzshpcrt_shopping_basket_internals($cart,$quantity_limit))). "\";
					  }
					";
		
			  if($do_not_refresh_regions == false)
				{
				$region_list = $wpdb->get_results("SELECT `".$wpdb->prefix."region_tax`.* FROM `".$wpdb->prefix."region_tax`, `".$wpdb->prefix."currency_list`  WHERE `".$wpdb->prefix."currency_list`.`isocode` IN('".$_POST['billing_country']."') AND `".$wpdb->prefix."currency_list`.`id` = `".$wpdb->prefix."region_tax`.`country_id`",ARRAY_A) ;
				  if($region_list != null)
					{
					$output .= "<select name='collected_data[".$form_id."][1]' class='current_region' onchange='set_billing_country(\\\"$html_form_id\\\", \\\"$form_id\\\");'>";
					//$output .= "<option value=''>None</option>";
					foreach($region_list as $region)
					  {
					  if($_SESSION['selected_region'] == $region['id'])
						{
						$selected = "selected='true'";
						}
						else
						  {
						  $selected = "";
						  }
					  $output .= "<option value='".$region['id']."' $selected>".$region['name']."</option>";
					  }
					$output .= "</select>";
			  echo  "if(document.getElementById('region_select_$form_id') != null)
		  {
		  document.getElementById('region_select_$form_id').innerHTML = \"".$output."\";
		  }
		";
				  }
				  else
				  {
				  echo  "if(document.getElementById('region_select_$form_id') != null)
		  {
		  document.getElementById('region_select_$form_id').innerHTML = \"\";
		  }
		";
				  }
				}
		}
      exit();
      }
    
    if(($_POST['get_country_tax'] == "true") && preg_match("/[a-zA-Z]{2,4}/",$_POST['country_id']))  
      {
      $country_id = $_POST['country_id'];
      $region_list = $wpdb->get_results("SELECT `".$wpdb->prefix."region_tax`.* FROM `".$wpdb->prefix."region_tax`, `".$wpdb->prefix."currency_list`  WHERE `".$wpdb->prefix."currency_list`.`isocode` IN('".$country_id."') AND `".$wpdb->prefix."currency_list`.`id` = `".$wpdb->prefix."region_tax`.`country_id`",ARRAY_A) ;
      if($region_list != null)
        {
        echo "<select name='base_region'>\n\r";
        foreach($region_list as $region)
          {
          if(get_option('base_region')  == $region['id'])
            {
            $selected = "selected='true'";
            }
            else
              {
              $selected = "";
              }
          echo "<option value='".$region['id']."' $selected>".$region['name']."</option>\n\r";
          }
        echo "</select>\n\r";    
        }
        else { echo "&nbsp;"; }
      exit();
      }
      
    
    /* fill product form */    
    if(($_POST['set_slider'] == "true") && is_numeric($_POST['state']))
      {
      $_SESSION['slider_state'] = $_POST['state'];
      exit();
      }  /* fill category form */
      
      
     
      
    if($_GET['action'] == "register")
      {
      $siteurl = get_option('siteurl');       
      require_once( ABSPATH . WPINC . '/registration-functions.php');
      if(($_POST['action']=='register') && get_settings('users_can_register'))
        {        
        //exit("fail for testing purposes");
        $user_login = sanitize_user( $_POST['user_login'] );
        $user_email = $_POST['user_email'];
        
        $errors = array();
          
        if ( $user_login == '' )
          exit($errors['user_login'] = __('<strong>ERROR</strong>: Please enter a username.'));
      
        /* checking e-mail address */
        if ($user_email == '') {
          exit(__('<strong>ERROR</strong>: Please type your e-mail address.'));
        } else if (!is_email($user_email)) {
          exit( __('<strong>ERROR</strong>: The email address isn&#8217;t correct.'));
          $user_email = '';
        }
      
        if ( ! validate_username($user_login) ) {
          $errors['user_login'] = __('<strong>ERROR</strong>: This username is invalid.  Please enter a valid username.');
          $user_login = '';
        }
      
        if ( username_exists( $user_login ) )
          exit( __('<strong>ERROR</strong>: This username is already registered, please choose another one.'));
      
        /* checking the email isn't already used by another user */
        $email_exists = $wpdb->get_row("SELECT user_email FROM $wpdb->users WHERE user_email = '$user_email'");
        if ( $email_exists)
          die (__('<strong>ERROR</strong>: This email address is already registered, please supply another.'));
      
      
      
        
        if ( 0 == count($errors) ) {
          $password = substr( md5( uniqid( microtime() ) ), 0, 7);
          //xit('there?');      
          $user_id = wp_create_user( $user_login, $password, $user_email );
          if ( !$user_id )
            {
            exit(sprintf(__('<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !'), get_settings('admin_email')));
            }
            else
            {
            wp_new_user_notification($user_id, $password);
            ?>
<div id="login"> 
  <h2><?php _e('Registration Complete') ?></h2>
  <p><?php printf(__('Username: %s'), "<strong>" . wp_specialchars($user_login) . "</strong>") ?><br />
  <?php printf(__('Password: %s'), '<strong>' . __('emailed to you') . '</strong>') ?> <br />
  <?php printf(__('E-mail: %s'), "<strong>" . wp_specialchars($user_email) . "</strong>") ?></p>
</div>
<?php
            }
          }
        }
        else
          {
          // onsubmit='submit_register_form(this);return false;'
          echo "<div id='login'>
    <h2>Register for this blog</h2>
    <form id='registerform' action='index.php?ajax=true&amp;action=register'  onsubmit='submit_register_form(this);return false;' method='post'>
      <p><input type='hidden' value='register' name='action'/>
      <label for='user_login'>Username:</label><br/> <input type='text' value='' maxlength='20' size='20' id='user_login' name='user_login'/><br/></p>
      <p><label for='user_email'>E-mail:</label><br/> <input type='text' value='' maxlength='100' size='25' id='user_email' name='user_email'/></p>
      <p>A password will be emailed to you.</p>
      <p class='submit'><input type='submit' name='submit_form' id='submit' value='Register »'/><img id='register_loading_img' src='".WPSC_URL."/images/loading.gif' alt='' title=''></p>
      
    </form>
    </div>";
         }
      
      exit();
      } 
      
    }
    /*
    * AJAX stuff stops here, I would put an exit here, but it may screw up other plugins
    //exit();
    */
    }
    
   if(isset($_POST['language_setting']) && ($_GET['page'] = WPSC_DIR_NAME.'/options.php'))
    {
    if($user_level >= 7)
      {
      update_option('language_setting', $_POST['language_setting']);
      }
    }
  
  if(isset($_POST['language_setting']) && ($_GET['page'] = WPSC_DIR_NAME.'/options.php'))
    {
    if($user_level >= 7)
      {
      update_option('language_setting', $_POST['language_setting']);
      }
    }
    
  if(($_GET['rss'] == "true") && ($_GET['rss_key'] == 'key') && ($_GET['action'] == "purchase_log"))
    {
    $sql = "SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date`!='' ORDER BY `date` DESC";
    $purchase_log = $wpdb->get_results($sql,ARRAY_A);
    header("Content-Type: application/xml; charset=UTF-8"); 
    header('Content-Disposition: inline; filename="WP_E-Commerce_Purchase_Log.rss"');
    $output = '';
    $output .= "<?xml version='1.0'?>\n\r";
    $output .= "<rss version='2.0'>\n\r";
    $output .= "  <channel>\n\r";
    $output .= "    <title>WP E-Commerce Product Log</title>\n\r";
    $output .= "    <link>".get_option('siteurl')."/wp-admin/admin.php?page=".WPSC_DIR_NAME."/display-log.php</link>\n\r";
    $output .= "    <description>This is the WP E-Commerce Product Log RSS feed</description>\n\r";
    $output .= "    <generator>WP E-Commerce Plugin</generator>\n\r";
    
    foreach((array)$purchase_log as $purchase)
      {
      $purchase_link = get_option('siteurl')."/wp-admin/admin.php?page=".WPSC_DIR_NAME."/display-log.php&amp;purchaseid=".$purchase['id'];
      $output .= "    <item>\n\r";
      $output .= "      <title>Purchase No. ".$purchase['id']."</title>\n\r";
      $output .= "      <link>$purchase_link</link>\n\r";
      $output .= "      <description>This is an entry in the purchase log.</description>\n\r";
      $output .= "      <pubDate>".date("r",$purchase['date'])."</pubDate>\n\r";
      $output .= "      <guid>$purchase_link</guid>\n\r";
      $output .= "    </item>\n\r";
      }
    $output .= "  </channel>\n\r";
    $output .= "</rss>";
    echo $output;
    exit();
    }
  
    
    
  if(($_GET['rss'] == "true") && ($_GET['action'] == "product_list")) {
    $siteurl = get_option('siteurl');    
    if(is_numeric($_GET['limit'])) {
      $limit = "LIMIT ".$_GET['limit']."";
		} else {
      $limit = '';
		}
    
    // LIMIT $startnum
    if(is_numeric($_GET['product_id'])) {
      $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `active` IN('1') AND `id` IN('".$_GET['product_id']."') LIMIT 1";
      } else if($_GET['random'] == 'true') {
      $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `active` IN('1') ORDER BY RAND() $limit";
      } else if(is_numeric($_GET['category_id'])) {
      /* man, this is a hard to read SQL statement */
      $sql = "SELECT DISTINCT `".$wpdb->prefix."product_list`.*, `".$wpdb->prefix."item_category_associations`.`category_id`,`".$wpdb->prefix."product_order`.`order`, IF(ISNULL(`".$wpdb->prefix."product_order`.`order`), 0, 1) AS `order_state` FROM `".$wpdb->prefix."product_list` LEFT JOIN `".$wpdb->prefix."item_category_associations` ON `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."item_category_associations`.`product_id` LEFT JOIN `".$wpdb->prefix."product_order` ON ( ( `".$wpdb->prefix."product_list`.`id` = `".$wpdb->prefix."product_order`.`product_id` ) AND ( `".$wpdb->prefix."item_category_associations`.`category_id` = `".$wpdb->prefix."product_order`.`category_id` ) ) WHERE `".$wpdb->prefix."product_list`.`active` = '1' AND `".$wpdb->prefix."item_category_associations`.`category_id` IN ('".$_GET['category_id']."') ORDER BY `order_state` DESC,`".$wpdb->prefix."product_order`.`order` ASC $limit";      
    } else {
      $sql = "SELECT DISTINCT * FROM `".$wpdb->prefix."product_list` WHERE `active` IN('1') ORDER BY `id` DESC $limit";
    }
    
    include_once(WPSC_FILE_PATH."/product_display_functions.php");
    include_once(WPSC_FILE_PATH."/show_cats_brands.php");
    
    
		if(isset($_GET['category_id']) and is_numeric($_GET['category_id'])){
			$selected_category = "&amp;category_id=".$_GET['category']."";
		}
		$self = get_option('siteurl')."/index.php?rss=true&amp;action=product_list$selected_category";
    
    $product_list = $wpdb->get_results($sql,ARRAY_A);
    header("Content-Type: application/xml; charset=UTF-8"); 
    header('Content-Disposition: inline; filename="E-Commerce_Product_List.rss"');
    $output = "<?xml version='1.0'?>\n\r";
    $output .= "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:product='http://www.buy.com/rss/module/productV2/'>\n\r";    
    $output .= "  <channel>\n\r";
    $output .= "    <title>".get_option('blogname')." Products</title>\n\r";
    $output .= "    <link>".get_option('siteurl')."/wp-admin/admin.php?page=".WPSC_DIR_NAME."/display-log.php</link>\n\r";
    $output .= "    <description>This is the WP E-Commerce Product List RSS feed</description>\n\r";
    $output .= "    <generator>WP E-Commerce Plugin</generator>\n\r";
    $output .= "    <atom:link href='$self' rel='self' type='application/rss+xml' />";
    foreach($product_list as $product) {
      $purchase_link = wpsc_product_url($product['id']);
      $output .= "    <item>\n\r";
      $output .= "      <title>".htmlentities(stripslashes($product['name']), ENT_NOQUOTES, 'UTF-8')."</title>\n\r";
      $output .= "      <link>$purchase_link</link>\n\r";
      $output .= "      <description>".htmlentities(stripslashes($product['description']), ENT_NOQUOTES, 'UTF-8')."</description>\n\r";
      $output .= "      <pubDate>".date("r")."</pubDate>\n\r";
      $output .= "      <guid>$purchase_link</guid>\n\r"; 
      if($product['thumbnail_image'] != null) {
        $image_file_name = $product['thumbnail_image'];
        } else {
        $image_file_name = $product['image'];
        }      
      $image_path = WPSC_THUMBNAIL_DIR.$image_file_name;
      if(is_file($image_path) && (filesize($image_path) > 0)) {
        $image_data = @getimagesize($image_path); 
        $image_link = WPSC_THUMBNAIL_URL.$product['image'];
        $output .= "      <enclosure url='$image_link' length='".filesize($image_path)."' type='".$image_data['mime']."' width='".$image_data[0]."' height='".$image_data[1]."' />\n\r"; 
        }
      $output .= "      <product:price>".$product['price']."</product:price>\n\r";
      $output .= "    </item>\n\r";
      }
    $output .= "  </channel>\n\r";
    $output .= "</rss>";
    echo $output;
    exit();
    }
    
  
  if($_GET['termsandconds'] === 'true')
    {
    echo stripslashes(get_option('terms_and_conditions'));
    exit();
    }
    
    require_once(WPSC_FILE_PATH . '/processing_functions.php');


/* 
 * This plugin gets the merchants from the merchants directory and
 * needs to search the merchants directory for merchants, the code to do this starts here
 */
$gateway_directory = WPSC_FILE_PATH.'/merchants';
$nzshpcrt_merchant_list = nzshpcrt_listdir($gateway_directory);
 //exit("<pre>".print_r($nzshpcrt_merchant_list,true)."</pre>");
$num=0;
foreach($nzshpcrt_merchant_list as $nzshpcrt_merchant) {
  if(stristr( $nzshpcrt_merchant , '.php' )) {
    //echo $nzshpcrt_merchant;
    require(WPSC_FILE_PATH."/merchants/".$nzshpcrt_merchant);
	}
  $num++;
}
/* 
 * and ends here
 */
  
  if(($_GET['purchase_log_csv'] == "true") && ($_GET['rss_key'] == 'key') && is_numeric($_GET['start_timestamp']) && is_numeric($_GET['end_timestamp']))
    {
    $form_sql = "SELECT * FROM `".$wpdb->prefix."collect_data_forms` WHERE `active` = '1' AND `display_log` = '1';";
    $form_data = $wpdb->get_results($form_sql,ARRAY_A);
    
    $start_timestamp = $_GET['start_timestamp'];
    $end_timestamp = $_GET['end_timestamp'];
    $data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."purchase_logs` WHERE `date` BETWEEN '$start_timestamp' AND '$end_timestamp' ORDER BY `date` DESC",ARRAY_A);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: inline; filename="Purchase Log '.date("M-d-Y", $start_timestamp).' to '.date("M-d-Y", $end_timestamp).'.csv"');      
    
    foreach($data as $purchase)
      {
      $country_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".get_option('country_form_field')."' LIMIT 1";
      $country_data = $wpdb->get_results($country_sql,ARRAY_A);
      $country = $country_data[0]['value'];
           
      $output .= "\"".nzshpcrt_find_total_price($purchase['id'],$country) ."\",";
                
      foreach($form_data as $form_field)
        {
        $collected_data_sql = "SELECT * FROM `".$wpdb->prefix."submited_form_data` WHERE `log_id` = '".$purchase['id']."' AND `form_id` = '".$form_field['id']."' LIMIT 1";
        $collected_data = $wpdb->get_results($collected_data_sql,ARRAY_A);
        $collected_data = $collected_data[0];
        $output .= "\"".$collected_data['value']."\",";
        }
        
      if(get_option('payment_method') == 2)
        {
        $gateway_name = '';
        foreach($GLOBALS['nzshpcrt_gateways'] as $gateway)
          {
          if($purchase['gateway'] != 'testmode')
            {
            if($gateway['internalname'] == $purchase['gateway'] )
              {
              $gateway_name = $gateway['name'];
              }
            }
            else
              {
              $gateway_name = "Manual Payment";
              }
          }
        $output .= "\"". $gateway_name ."\",";
        }
              
      if($purchase['processed'] < 1)
        {
        $purchase['processed'] = 1;
        }
      $stage_sql = "SELECT * FROM `".$wpdb->prefix."purchase_statuses` WHERE `id`='".$purchase['processed']."' AND `active`='1' LIMIT 1";
      $stage_data = $wpdb->get_results($stage_sql,ARRAY_A);
              
      $output .= "\"". $stage_data[0]['name'] ."\",";
      
      $output .= "\"". date("jS M Y",$purchase['date']) ."\"";
      
      $cartsql = "SELECT * FROM `".$wpdb->prefix."cart_contents` WHERE `purchaseid`=".$purchase['id']."";
      $cart = $wpdb->get_results($cartsql,ARRAY_A) ; 
      //exit(nl2br(print_r($cart,true)));
      
      foreach($cart as $item)
        {
        $output .= ",";
        $product = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`=".$item['prodid']." LIMIT 1",ARRAY_A);        
        $variation_sql = "SELECT * FROM `".$wpdb->prefix."cart_item_variations` WHERE `cart_id`='".$item['id']."'";
        $variation_data = $wpdb->get_results($variation_sql,ARRAY_A);
         $variation_count = count($variation_data);
          if($variation_count >= 1)
            {
            $variation_list = " (";
            $i = 0;
            foreach($variation_data as $variation)
              {
              if($i > 0)
                {
                $variation_list .= ", ";
                }
              $value_id = $variation['value_id'];
              $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id`='".$value_id."' LIMIT 1",ARRAY_A);
              $variation_list .= $value_data[0]['name'];              
              $i++;
              }
            $variation_list .= ")";
            }
        
        
        $output .= "\"".$item['quantity']." ".$product['name'].$variation_list."\"";
        }
      $output .= "\n"; // terminates the row/line in the CSV file
      }
    echo $output;
    exit();
    }    
    
    
    if(is_numeric($_GET['remove']) && ($_SESSION['nzshpcrt_cart'] != null)) {
      $key = $_GET['remove'];
      if(is_object($_SESSION['nzshpcrt_cart'][$key])){
        $_SESSION['nzshpcrt_cart'][$key]->empty_item();
			}
      unset($_SESSION['nzshpcrt_cart'][$key]);
		}
    
    if($_GET['cart']== 'empty') {
      $_SESSION['nzshpcrt_cart'] = '';
      $_SESSION['nzshpcrt_cart'] = Array();
		}
      
    if(is_numeric($_POST['quantity']) && is_numeric($_POST['key'])) {
      $quantity = (int)$_POST['quantity'];
      $key = (int)$_POST['key'];
      $product_id = $_SESSION['nzshpcrt_cart'][$key]->product_id;
		  $item_data = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}product_list` WHERE `id`='$product_id' LIMIT 1",ARRAY_A);      
    	$check_stock = false;
      if((bool)(int)$item_data['quantity_limited'] == true) {				
				$item_variations = array_values((array)$_SESSION['nzshpcrt_cart'][$key]->product_variations); // reset the keys to start from 0			
				if(count($item_variations) == 2)	{
					$variation_stock_data = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}variation_priceandstock` WHERE `product_id` = '{$product_id}' AND (`variation_id_1` = '{$item_variations[0]}' AND `variation_id_2` = '{$item_variations[1]}') OR (`variation_id_1` = '{$item_variations[1]}' AND `variation_id_2` = '{$item_variations[0]}') LIMIT 1",ARRAY_A);
					$item_stock = $variation_stock_data['stock'];
				} else if(count($item_variations) == 1) {
					$variation_stock_data = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}variation_priceandstock` WHERE `product_id` = '{$product_id}' AND (`variation_id_1` = '{$item_variations[0]}' AND `variation_id_2` = '0') LIMIT 1",ARRAY_A);
					$item_stock = $variation_stock_data['stock'];
				} else {
					$item_stock = $item_data['quantity'];
				}
				$check_stock = true;
			}
			
			
      $_SESSION['out_of_stock'] = false;
      if(is_object($_SESSION['nzshpcrt_cart'][$key])) {
        if($quantity > 0) {
          // if stock is not limited or stock is limited and requested quantity is equal to or less than current stock.
          if(($check_stock == false) || (($check_stock == true) && ($quantity <= $item_stock))) {
						$_SESSION['nzshpcrt_cart'][$key]->quantity = $quantity;
          } else {
						$_SESSION['out_of_stock'] = true;
          }
				} else {
					$_SESSION['nzshpcrt_cart'][$key]->empty_item();
					unset($_SESSION['nzshpcrt_cart'][$key]);
				}
			}
		}

function nzshpcrt_download_file() {
  global $wpdb,$user_level,$wp_rewrite; 
  get_currentuserinfo();  
  function readfile_chunked($filename, $retbytes = true) {
    $chunksize = 1 * (1024 * 1024); // how many bytes per chunk
    $buffer = '';
    $cnt = 0;
    $handle = fopen($filename, 'rb');
    if($handle === false) {
      return false;
		}
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			ob_flush();
			flush();
			if($retbytes)	{
				$cnt += strlen($buffer);
			}
		}
    $status = fclose($handle);
    if($retbytes && $status) {
      return $cnt; // return num. bytes delivered like readfile() does.
		}
    return $status;
	}  
  
  if(isset($_GET['downloadid'])) {
    // strip out anything that isnt 'a' to 'z' or '0' to '9'
    $downloadid = preg_replace("/[^a-z0-9]+/i",'',strtolower($_GET['downloadid']));
    
		$download_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."download_status` WHERE `uniqueid` = '".$downloadid."' AND `downloads` > '0' AND `active`='1' LIMIT 1",ARRAY_A);
		
		if(($download_data == null) && is_numeric($downloadid)) {
		  $download_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."download_status` WHERE `id` = '".$downloadid."' AND `downloads` > '0' AND `active`='1' AND `uniqueid` IS NULL LIMIT 1",ARRAY_A);
		}
		
		if((get_option('wpsc_ip_lock_downloads') == 1) && ($_SERVER['REMOTE_ADDR'] != null)) {
		  $ip_number = $_SERVER['REMOTE_ADDR'];
		  if($download_data['ip_number'] == '') {
		    // if the IP number is not set, set it
		    $wpdb->query("UPDATE `".$wpdb->prefix."download_status` SET `ip_number` = '{$ip_number}' WHERE `id` = '{$download_data['id']}' LIMIT 1");
		  } else if($ip_number != $download_data['ip_number']) {
		    // if the IP number is set but does not match, fail here.
// 				return false;
				exit(WPSC_DOWNLOAD_INVALID);
		  }
		}
		
    //exit("<pre>".print_r($download_data,true)."</pre>");
   
    if($download_data != null) {
      $file_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_files` WHERE `id`='".$download_data['fileid']."' LIMIT 1",ARRAY_A) ;
      $file_data = $file_data[0];      
      
      if((int)$download_data['downloads'] >= 1) {
        $download_count = (int)$download_data['downloads'] - 1;
      } else {
        $download_count = 0;
      }
      
      
      $wpdb->query("UPDATE `".$wpdb->prefix."download_status` SET `downloads` = '{$download_count}' WHERE `id` = '{$download_data['id']}' LIMIT 1");

      $wpdb->query("UPDATE `".$wpdb->prefix."purchase_logs` SET `processed` = '4' WHERE `id` = '".$download_data['purchid']."' LIMIT 1");
      if(is_file(WPSC_FILE_DIR.$file_data['idhash'])) {
        header('Content-Type: '.$file_data['mimetype']);      
        header('Content-Length: '.filesize(WPSC_FILE_DIR.$file_data['idhash']));
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="'.stripslashes($file_data['filename']).'"');
        if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] != '')) {
          /*
          There is a bug in how IE handles downloads from servers using HTTPS, this is part of the fix, you may also need:
            session_cache_limiter('public');
            session_cache_expire(30);
          At the start of your index.php file or before the session is started
          */
          header("Pragma: public");
          header("Expires: 0");      
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: public"); 
				} else {
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');       
				}        
        $filename = WPSC_FILE_DIR.$file_data['idhash'];
        readfile_chunked($filename);   
        exit();
			}
		}
	} else {
		if(($_GET['admin_preview'] == "true") && is_numeric($_GET['product_id']) && current_user_can('edit_plugins')) {
			$product_id = $_GET['product_id'];
			$product_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id` = '$product_id' LIMIT 1",ARRAY_A);
			if(is_numeric($product_data[0]['file']) && ($product_data[0]['file'] > 0)) {
				$file_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_files` WHERE `id`='".$product_data[0]['file']."' LIMIT 1",ARRAY_A) ;
				$file_data = $file_data[0];
				if(is_file(WPSC_FILE_DIR.$file_data['idhash'])) {
					header('Content-Type: '.$file_data['mimetype']);
					header('Content-Length: '.filesize(WPSC_FILE_DIR.$file_data['idhash']));
					header('Content-Transfer-Encoding: binary');
					if($_GET['preview_track'] != 'true') {
						header('Content-Disposition: attachment; filename="'.$file_data['filename'].'"');
					} else {
						header('Content-Disposition: inline; filename="'.$file_data['filename'].'"');
					}
					if(isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] != '')) {
						header("Pragma: public");
						header("Expires: 0");      
						header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						header("Cache-Control: public"); 
					} else {
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');       
					}             
					$filename = WPSC_FILE_DIR.$file_data['idhash'];  
					readfile_chunked($filename);   
					exit();
				}            
			}
    }
  }
}

function nzshpcrt_display_preview_image()
  {
  global $wpdb;
  if(is_numeric($_GET['productid']) || is_numeric($_GET['image_id']))
    {
     if(function_exists("getimagesize"))
      {
      if(is_numeric($_GET['productid']))
        {
        $imagesql = "SELECT `image`,`thumbnail_image` FROM `".$wpdb->prefix."product_list` WHERE `id`='".$_GET['productid']."' LIMIT 1";
        $imagedata = $wpdb->get_row($imagesql,ARRAY_A);
        if($_GET['thumbnail'] == 'true')
          {
          if($imagedata['thumbnail_image'] != '')
            {
            $image_name = $imagedata['thumbnail_image'];
            }
            else
              {
              $image_name = $imagedata['image'];
              }
          $imagepath = WPSC_THUMBNAIL_DIR . $image_name;
          }
          else
          {
          $imagepath = WPSC_IMAGE_DIR . $imagedata['image'];
          }
        }
        else if($_GET['image_id'])
        {
        $image_id = $_GET['image_id'];
        $image = $wpdb->get_var("SELECT `image` FROM `".$wpdb->prefix."product_images` WHERE `id` = '$image_id' LIMIT 1");
        $imagepath = WPSC_IMAGE_DIR . $image;
        }
      
      
      $image_size = @getimagesize($imagepath);
      if(is_numeric($_GET['height']) && is_numeric($_GET['width']))
        {
        $height = $_GET['height'];
        $width = $_GET['width'];
        }
        else
          {
          $width = $image_size[0];
          $height = $image_size[1];
          }
      if(($height > 0) && ($height <= 1024) && ($width > 0) && ($width <= 1024))
       {
       include("image_preview.php");
       }
       else
         {
         $width = $image_size[0];
         $height = $image_size[1];
         include("image_preview.php");
         }
      }
    }
  }
  
  
function nzshpcrt_listdir($dirname)
    {
    /*
    lists the merchant directory
    */
     $dir = @opendir($dirname);
     $num = 0;
     while(($file = @readdir($dir)) !== false)
       {
       //filter out the dots and any backup files, dont be tempted to correct the "spelling mistake", its to filter out a previous spelling mistake.
       if(($file != "..") && ($file != ".") && !stristr($file, "~") && !stristr($file, "Chekcout") && !( strpos($file, ".") === 0 ))
         {
         $dirlist[$num] = $file;
         $num++;
         }
       }
    if($dirlist == null)
      {
      $dirlist[0] = "paypal.php";
      $dirlist[1] = "testmode.php";
      }
    return $dirlist; 
    }
    
    

function nzshpcrt_product_rating($prodid)
      {
      global $wpdb;
      $get_average = $wpdb->get_results("SELECT AVG(`rated`) AS `average`, COUNT(*) AS `count` FROM `".$wpdb->prefix."product_rating` WHERE `productid`='".$prodid."'",ARRAY_A);
      $average = floor($get_average[0]['average']);
      $count = $get_average[0]['count'];
      $output .= "  <span class='votetext'>";
      for($l=1; $l<=$average; ++$l)
        {
        $output .= "<img class='goldstar' src='". WPSC_URL."/images/gold-star.gif' alt='$l' title='$l' />";
        }
      $remainder = 5 - $average;
      for($l=1; $l<=$remainder; ++$l)
        {
        $output .= "<img class='goldstar' src='". WPSC_URL."/images/grey-star.gif' alt='$l' title='$l' />";
        }
      $output .=  "<span class='vote_total'>&nbsp;(<span id='vote_total_$prodid'>".$count."</span>)</span> \r\n";
      $output .=  "</span> \r\n";
      return $output;
      }

// this appears to have some star rating code in it
function nzshpcrt_product_vote($prodid, $starcontainer_attributes = '')
      {
      global $wpdb;
      $output = null;
      $useragent = $_SERVER['HTTP_USER_AGENT'];
      $visibility = "style='display: none;'";
      
      preg_match("/(?<=Mozilla\/)[\d]*\.[\d]*/", $useragent,$rawmozversion );
      $mozversion = $rawmozversion[0];
      if(stristr($useragent,"opera"))
        {
        $firstregexp = "Opera[\s\/]{1}\d\.[\d]+";
        }
        else
          {
          $firstregexp = "MSIE\s\d\.\d";
          }
      preg_match("/$firstregexp|Firefox\/\d\.\d\.\d|Netscape\/\d\.\d\.\d|Safari\/[\d\.]+/", $useragent,$rawbrowserinfo);
      $browserinfo = preg_split("/[\/\s]{1}/",$rawbrowserinfo[0]);
      $browsername = $browserinfo[0];
      $browserversion = $browserinfo[1];  
      
      //exit($browsername . " " . $browserversion);
       
      if(($browsername == 'MSIE') && ($browserversion < 7.0))
        {
        $starimg = ''. get_option('siteurl').'/wp-content/plugins/wp-shopping-cart/images/star.gif';
        $ie_javascript_hack = "onmouseover='ie_rating_rollover(this.id,1)' onmouseout='ie_rating_rollover(this.id,0)'";
        }
        else 
          {
          $starimg = ''. get_option('siteurl').'/wp-content/plugins/wp-shopping-cart/images/24bit-star.png';
          $ie_javascript_hack = '';
          }
       
      $cookie_data = explode(",",$_COOKIE['voting_cookie'][$prodid]);
       
      if(is_numeric($cookie_data[0]))
        {
        $vote_id = $cookie_data[0];
        }
      
      $chkrate = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_rating` WHERE `id`='".$vote_id."' LIMIT 1",ARRAY_A);
      //$output .= "<pre>".print_r($chkrate,true)."</pre>";
      if($chkrate[0]['rated'] > 0)
        {
        $rating = $chkrate[0]['rated'];
        $type = 'voted';
        }
        else
          {
          $rating = 0;
          $type = 'voting';
          }
      //$output .= "<pre>".print_r($rating,true)."</pre>";
      $output .=  "<div class='starcontainer' $starcontainer_attributes >\r\n";
      for($k=1; $k<=5; ++$k)
        {
        $style = '';
        if($k <= $rating)
          {
          $style = "style='background: url(". WPSC_URL."/images/gold-star.gif)'";
          }
        $output .= "      <a id='star".$prodid."and".$k."_link' onclick='rate_item(".$prodid.",".$k.")' class='star$k' $style $ie_javascript_hack ><img id='star".$prodid."and".$k."' class='starimage' src='$starimg' alt='$k' title='$k' /></a>\r\n";
        }
      $output .=  "   </div>\r\n";
      $output .= "";
      $voted = TXT_WPSC_CLICKSTARSTORATE;
      
      switch($ratecount[0]['count'])
        {
        case 0:
        $votestr = TXT_WPSC_NOVOTES;
        break;
        
        case 1:
        $votestr = TXT_WPSC_1VOTE;
        break;
        
        default:
        $votestr = $ratecount[0]['count']." ".TXT_WPSC_VOTES2;
        break;
        }
        
      for($i= 5; $i>= 1; --$i)
         {
        //$tmpcount = $this->db->GetAll("SELECT COUNT(*) AS 'count' FROM `pxtrated` WHERE `pxtid`=".$dbdat['rID']." AND `rated`=$i");
            
         switch($tmpcount[0]['count'])
           {
           case 0:
           $othervotes .= "";
           break;
           
           case 1:
           $othervotes .= "<br />". $tmpcount[0]['count'] . " ".TXT_WPSC_PERSONGIVEN." $i ".TXT_WPSC_PERSONGIVEN2;
           break;
           
           default:
           $othervotes .= "<br />". $tmpcount[0]['count'] . " ".TXT_WPSC_PEOPLEGIVEN." $i ".TXT_WPSC_PEOPLEGIVEN2;
           break;
           }  
         } /*
      $output .=  "</td><td class='centerer2'>&nbsp;</td></tr>\r\n";
      $output .= "<tr><td colspan='3' class='votes' >\r\n";//id='startxtmove'
      $output .= "   <p class='votes'> ".$votestr."<br />$voted <br />
      $othervotes</p>";*/
      
      return Array($output,$type);
      } //*/
  

 function get_country($country_code)  
  {
  global $wpdb;
  $country = $wpdb->get_var("SELECT `country` FROM `".$wpdb->prefix."currency_list` WHERE `isocode` IN ('".$country_code."') LIMIT 1");
  return $country; 
  }

 function get_region($region_code)  
  {
  global $wpdb;
  $region = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."region_tax` WHERE `id` IN('$region_code')");
  return $region; 
  }
  
function get_brand($brand_id)  
  {
  global $wpdb;
  $brand_data = $wpdb->get_results("SELECT `name` FROM `".$wpdb->prefix."product_brands` WHERE `id` IN ('".$brand_id."') LIMIT 1",ARRAY_A);
  return $brand_data[0]['name']; 
  }


function filter_input_wp($input) {
  // if the input is numeric, then its probably safe
  if(is_numeric($input)) {
    $output = $input;
	} else {
		// if its not numeric, then make it safe
		if(!get_magic_quotes_gpc()) {
			$output = mysql_real_escape_string($input);
		} else {
			$output = mysql_real_escape_string(stripslashes($input));
		}
	}
	return $output;
}
    
function make_csv($array)
  {
  $count = count($array);
  $num = 1;
  foreach($array as $value)
    {
    $output .= "'$value'";
    if($num < $count)
      {
      $output .= ",";
      }
    $num++;
    }
  return $output;
  }   
  
function nzshpcrt_product_log_rss_feed() {
  echo "<link type='application/rss+xml' href='".get_option('siteurl')."/index.php?rss=true&amp;rss_key=key&amp;action=purchase_log&amp;type=rss' title='WP E-Commerce Purchase Log RSS' rel='alternate'/>";
}
  
function nzshpcrt_product_list_rss_feed() {
  if(isset($_GET['category']) and is_numeric($_GET['category'])){
    $selected_category = "&amp;category_id=".$_GET['category']."";
	}
  echo "<link rel='alternate' type='application/rss+xml' title='".get_option('blogname')." Product List RSS' href='".get_option('siteurl')."/index.php?rss=true&amp;action=product_list$selected_category'/>";
}


//handles replacing the tags in the pages
  
function nzshpcrt_products_page($content = '') {
  //if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('nzshpcrt_products_page','start');}
  //exit(htmlentities($content));
  if(preg_match("/\[productspage\]/",$content)) {
    $GLOBALS['nzshpcrt_activateshpcrt'] = true;
    ob_start();
    include_once(WPSC_FILE_PATH . "/products_page.php");
    $output = ob_get_contents();
    ob_end_clean();
    //if(WPSC_DEBUG === true) {wpsc_debug_start_subtimer('nzshpcrt_products_page','stop');}
    //return preg_replace("/\[productspage\]/",$output, $content);
    return preg_replace("/(<p>)*\[productspage\](<\/p>)*/",$output, $content);
	} else {
    return $content;
	}
}

function nzshpcrt_shopping_cart($content = '')
  {
  if(preg_match("/\[shoppingcart\]/",$content))
    {
    ob_start();
    include_once(WPSC_FILE_PATH . "/shopping_cart.php");
    $output = ob_get_contents();
    ob_end_clean();
    return preg_replace("/(<p>)*\[shoppingcart\](<\/p>)*/",$output, $content);
    }
    else
    {
    return $content;
    }
  }
  

function nzshpcrt_checkout($content = '')
  {
  if(preg_match("/\[checkout\]/",$content))
    {
    ob_start();
    include_once(WPSC_FILE_PATH . "/checkout.php");
    $output = ob_get_contents();
    ob_end_clean();
    return preg_replace("/(<p>)*\[checkout\](<\/p>)*/",$output, $content);
    }
    else
    {
    return $content;
    }
  }

function nzshpcrt_transaction_results($content = '')
  {
  if(preg_match("/\[transactionresults\]/",$content))
    {
    ob_start();
    include_once(WPSC_FILE_PATH . "/transaction_results.php");
    $output = ob_get_contents();
    ob_end_clean();
    return preg_replace("/(<p>)*\[transactionresults\](<\/p>)*/",$output, $content);
    }
    else
    { 
    return $content;
    }
  }

function nzshpcrt_user_log($content = '') {
  if(preg_match("/\[userlog\]/",$content)) {
    ob_start();
    include_once(WPSC_FILE_PATH . '/user-log.php');
    $output = ob_get_contents();
    ob_end_clean();
    return preg_replace("/(<p>)*\[userlog\](<\/p>)*/",$output, $content);
	} else {
    return $content;
	}
}
  
  
//displays a list of categories when the code [showcategories] is present in a post or page.
function nzshpcrt_show_categories($content = '') {
  if(preg_match("/\[showcategories\]/",$content)) {
    $GLOBALS['nzshpcrt_activateshpcrt'] = true;
    $output = nzshpcrt_display_categories_groups();
    return preg_replace("/(<p>)*\[showcategories\](<\/p>)*/",$output, $content);
	} else {
    return $content;
	}
}

// substitutes in the buy now buttons where the shortcode is in a post.
function nzshpcrt_substitute_buy_now_button($content = '') {
  if(preg_match_all("/\[buy_now_button=([\d]+)\]/", $content, $matches)) {
    foreach($matches[1] as $key => $product_id) {
      $original_string = $matches[0][$key];
      //print_r($matches);
      $output = wpsc_buy_now_button($product_id, true);  
			$content = str_replace($original_string, $output, $content);
    }
	}	
	return $content;
}



// This function displays the category gropus, it is used by the above function
function nzshpcrt_display_categories_groups() {
    global $wpdb;

    if(get_option('permalink_structure') != '') {
      $seperator ="?";
    } else {
      $seperator ="&amp;";
    }

    if(function_exists('gold_shpcrt_search_form') && get_option('show_search') == 1) {
      echo gold_shpcrt_search_form();
    }

    //include("show_cats_brands.php");
    if (get_option('cat_brand_loc') == 0) {
      show_cats_brands();
    }
  }


function add_product_meta($product_id, $key, $value, $unique = false, $custom = false) {
  global $wpdb, $post_meta_cache, $blog_id;
  $product_id = (int)$product_id;
  if($product_id > 0) {
    if(($unique == true) && $wpdb->get_var("SELECT meta_key FROM `".$wpdb->prefix."wpsc_productmeta` WHERE meta_key = '$key' AND product_id = '$product_id'")) {
      return false;
		}
    
    $value = $wpdb->escape(maybe_serialize($value));
    
    if(!$wpdb->get_var("SELECT meta_key FROM `".$wpdb->prefix."wpsc_productmeta` WHERE meta_key = '$key' AND product_id = '$product_id'")) {
      $custom = (int)$custom;
      $wpdb->query("INSERT INTO `".$wpdb->prefix."wpsc_productmeta` (product_id,meta_key,meta_value, custom) VALUES ('$product_id','$key','$value', '$custom')");
		} else {
      $wpdb->query("UPDATE `".$wpdb->prefix."wpsc_productmeta` SET meta_value = '$value' WHERE meta_key = '$key' AND product_id = '$product_id'");
		}
    return true;
	}
  return false; 
}
  
function delete_product_meta($product_id, $key, $value = '') {
  global $wpdb, $post_meta_cache, $blog_id;
  $product_id = (int)$product_id;
  if($product_id > 0) {
    if ( empty($value) ) {
      $meta_id = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."wpsc_productmeta` WHERE product_id = '$product_id' AND meta_key = '$key'");      
      if(is_numeric($meta_id) && ($meta_id > 0)) {
        $wpdb->query("DELETE FROM `".$wpdb->prefix."wpsc_productmeta` WHERE product_id = '$product_id' AND meta_key = '$key'");
        }
      } else {
      $meta_id = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."wpsc_productmeta` WHERE product_id = '$product_id' AND meta_key = '$key' AND meta_value = '$value'");
      if(is_numeric($meta_id) && ($meta_id > 0)) {
        $wpdb->query("DELETE FROM `".$wpdb->prefix."wpsc_productmeta` WHERE product_id = '$product_id' AND meta_key = '$key' AND meta_value = '$value'");
        }        
      }
  }
  return true;
}


function get_product_meta($product_id, $key, $single = false) {
  global $wpdb, $post_meta_cache, $blog_id;  
  $product_id = (int)$product_id;
  if($product_id > 0) {
    $meta_id = $wpdb->get_var("SELECT `id` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN('$key') AND `product_id` = '$product_id' LIMIT 1");
    if(is_numeric($meta_id) && ($meta_id > 0)) {      
      if($single != false) {
        $meta_values[0] = maybe_unserialize($wpdb->get_var("SELECT `meta_value` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN('$key') AND `product_id` = '$product_id' LIMIT 1"));
        } else {
        $temp_meta_values = $wpdb->get_results("SELECT `meta_value` FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN('$key') AND `product_id` = '$product_id'", ARRAY_A);
        foreach($temp_meta_values as $value) {
          $meta_values[] = maybe_unserialize($value['meta_value']);
          }
        }
      }
    } else {
    $meta_values = false;
    }    
  return $meta_values;
  }

function update_product_meta($product_id, $key, $value, $prev_value = '') {
  global $wpdb, $blog_id;
  $product_id = (int)$product_id;
  if($product_id > 0) {
  $value = $wpdb->escape(maybe_serialize($value));
  
  if(!empty($prev_value)) {
    $prev_value = $wpdb->escape(maybe_serialize($prev_value));
    }

  if($wpdb->get_var("SELECT meta_key FROM `".$wpdb->prefix."wpsc_productmeta` WHERE `meta_key` IN('$key') AND product_id = '$product_id'")) {
    if (empty($prev_value)) {
      $wpdb->query("UPDATE `".$wpdb->prefix."wpsc_productmeta` SET `meta_value` = '$value' WHERE `meta_key` IN('$key') AND product_id = '$product_id'");
      } else {
      $wpdb->query("UPDATE `".$wpdb->prefix."wpsc_productmeta` SET `meta_value` = '$value' WHERE `meta_key` IN('$key') AND product_id = '$product_id' AND meta_value = '$prev_value'");
      }
    } else {
    $wpdb->query("INSERT INTO `".$wpdb->prefix."wpsc_productmeta` (product_id,meta_key,meta_value) VALUES ('$product_id','$key','$value')");
    }
  return true;
  }
}
    
    
    
    
function wpsc_refresh_page_urls($content) {
  global $wpdb;
  $wpsc_pageurl_option['product_list_url'] = '[productspage]';
  $wpsc_pageurl_option['shopping_cart_url'] = '[shoppingcart]';
  $check_chekout = $wpdb->get_var("SELECT `guid` FROM `".$wpdb->prefix."posts` WHERE `post_content` LIKE '%[checkout]%' LIMIT 1");
  if($check_chekout != null) {
    $wpsc_pageurl_option['checkout_url'] = '[checkout]';
    } else {
    $wpsc_pageurl_option['checkout_url'] = '[checkout]';
    }
  $wpsc_pageurl_option['transact_url'] = '[transactionresults]';
  $wpsc_pageurl_option['user_account_url'] = '[userlog]';
  $changes_made = false;
  foreach($wpsc_pageurl_option as $option_key => $page_string) {
    $post_id = $wpdb->get_var("SELECT `ID` FROM `".$wpdb->prefix."posts` WHERE `post_content` LIKE '%$page_string%' LIMIT 1");
    $the_new_link = get_permalink($post_id);
    if(stristr(get_option($option_key), "https://")) {
      $the_new_link = str_replace('http://', "https://",$the_new_link);
    }    
    update_option($option_key, $the_new_link);
	}
  return $content;
}
  

	function wpsc_product_permalinks($rewrite_rules) {
		global $wpdb, $wp_rewrite;  
		
		$page_details = $wpdb->get_row("SELECT * FROM `".$wpdb->posts."` WHERE `post_content` LIKE '%[productspage]%' LIMIT 1", ARRAY_A);
		$is_index = false;
		if((get_option('page_on_front') == $page_details['ID']) && (get_option('show_on_front') == 'page')) {		
		  $is_index = true;
		}
		$page_name = $page_details['post_name'];
		
		if(!function_exists('wpsc_rewrite_categories')) {	 // to stop this function from being declared multiple times, which causes wordpress to fail.
			function wpsc_rewrite_categories($page_name, $id = null, $level = 0, $parent_categories = array(), $is_index = false) {
				global $wpdb,$category_data;
				if($is_index == true) {
				  $rewrite_page_name = '';				  
				} else {
				  $rewrite_page_name = $page_name.'/';
				}
				
				if(is_numeric($id)) {
					$category_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '".$id."' ORDER BY `id`";
					$category_list = $wpdb->get_results($category_sql,ARRAY_A);
				}	else {
					$category_sql = "SELECT * FROM `".$wpdb->prefix."product_categories` WHERE `active`='1' AND `category_parent` = '0' ORDER BY `id`";
					$category_list = $wpdb->get_results($category_sql,ARRAY_A);
				}
				if($category_list != null)	{
					foreach($category_list as $category) {
						if($level === 0) {
							$parent_categories = array();
						}
						$parent_categories[] = $category['nice-name'];
						$new_rules[($rewrite_page_name.implode($parent_categories,"/").'/?$')] = 'index.php?pagename='.$page_name.'&product_category='.$category['id'];
						$new_rules[($rewrite_page_name.implode($parent_categories,"/").'/([A-Za-z0-9\-]+)/?$')] = 'index.php?pagename='.$page_name.'&product_category='.$category['id'].'&product_name=$matches[1]';
						$sub_rules = wpsc_rewrite_categories($page_name, $category['id'], ($level+1), $parent_categories, $is_index);
						array_pop($parent_categories);
						$new_rules = array_merge((array)$new_rules, (array)$sub_rules);
					}
				}
			return $new_rules;
			}
		}
		$new_rules = wpsc_rewrite_categories($page_name, null, 0, null, $is_index);
		$new_rules = array_reverse((array)$new_rules);
	  //$new_rules[$page_name.'/product-tag/(.+?)/page/?([0-9]{1,})/?$'] = 'index.php?pagename='.$page_name.'&ptag=$matches[1]&paged=$matches[2]';
	  $new_rules[$page_name.'/tag/([A-Za-z0-9\-]+)?$'] = 'index.php?pagename='.$page_name.'&ptag=$matches[1]';
		$new_rewrite_rules = array_merge((array)$new_rules,(array)$rewrite_rules);
		return $new_rewrite_rules;
	}

function wpsc_query_vars($vars) {
  $vars[] = "product_category";
  $vars[] = "product_name";
  return $vars;
  }

add_filter('query_vars', 'wpsc_query_vars');

// using page_rewrite_rules makes it so that odd permalink structures like /%category%/%postname%.htm do not override the plugin permalinks.
add_filter('page_rewrite_rules', 'wpsc_product_permalinks');
 
 
function wpsc_replace_the_title($input) {
  global $wpdb, $wp_query;
	if(is_numeric($wp_query->query_vars['product_category'])) {
		// using debug_backtrace here is not a good way of doing this, but wordpress provides no way to differentiate between the various uses of this plugin hook.
		$backtrace = debug_backtrace();
		if($backtrace[3]['function'] == 'get_the_title') {
			return $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_categories` WHERE `id`='{$wp_query->query_vars['product_category']}' LIMIT 1");
		}	
	}
	return $input;
}
 
 
 
add_filter('the_title', 'wpsc_replace_the_title', 10, 2);

require_once(WPSC_FILE_PATH . '/product_display_functions.php');

if(is_file(WPSC_FILE_PATH.'/gold_shopping_cart.php')) {
  require_once(WPSC_FILE_PATH.'/gold_shopping_cart.php');
}

require_once(WPSC_FILE_PATH."/currency_converter.inc.php"); 
require_once(WPSC_FILE_PATH."/form_display_functions.php"); 
require_once(WPSC_FILE_PATH."/shopping_cart_functions.php"); 
require_once(WPSC_FILE_PATH."/homepage_products_functions.php"); 
require_once(WPSC_FILE_PATH."/transaction_result_functions.php"); 
include_once(WPSC_FILE_PATH.'/submit_checkout_function.php');
require_once(WPSC_FILE_PATH."/admin-form-functions.php"); 
require_once(WPSC_FILE_PATH."/shipwire_functions.php"); 

/* widget_section */
include_once(WPSC_FILE_PATH.'/widgets/product_tag_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/shopping_cart_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/category_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/donations_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/specials_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/latest_product_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/price_range_widget.php');
include_once(WPSC_FILE_PATH.'/widgets/admin_menu_widget.php');


include_once(WPSC_FILE_PATH.'/image_processing.php');
include_once(WPSC_FILE_PATH."/show_cats_brands.php");


$theme_path = WPSC_FILE_PATH . '/themes/';
if((get_option('wpsc_selected_theme') != '') && (file_exists($theme_path.get_option('wpsc_selected_theme')."/".get_option('wpsc_selected_theme').".php") )) {    
  include_once(WPSC_FILE_PATH.'/themes/'.get_option('wpsc_selected_theme').'/'.get_option('wpsc_selected_theme').'.php');
}

$current_version_number = get_option('wpsc_version');
if(count(explode(".",$current_version_number)) > 2) {
	// in a previous version, I accidentally had the major version number have two dots, and three numbers
	// this code rectifies that mistake
	$current_version_number_array = explode(".",$current_version_number);
	array_pop($current_version_number_array);
	$current_version_number = (float)implode(".", $current_version_number_array );
} else if(!is_numeric(get_option('wpsc_version'))) {
  $current_version_number = 0;
}

if(isset($_GET['activate']) && ($_GET['activate'] == 'true')) {
	include_once("install_and_update.php");
  add_action('init', 'nzshpcrt_install');
} else if(($current_version_number < WPSC_VERSION ) || (($current_version_number == WPSC_VERSION ) && (get_option('wpsc_minor_version') <= WPSC_MINOR_VERSION))) {
	include_once("install_and_update.php");
  add_action('init', 'wpsc_auto_update');
}

add_filter('single_post_title','wpsc_post_title_seo');
   
function nzshpcrt_enable_page_filters($excerpt = ''){
  global $wp_query;
  add_filter('the_content', 'nzshpcrt_products_page', 12);
  add_filter('the_content', 'nzshpcrt_shopping_cart', 12);
  add_filter('the_content', 'nzshpcrt_transaction_results', 12);
  add_filter('the_content', 'nzshpcrt_checkout', 12);
  add_filter('the_content', 'nszhpcrt_homepage_products', 12);
  add_filter('the_content', 'nzshpcrt_user_log', 12);
  add_filter('the_content', 'nszhpcrt_category_tag', 12);
  add_filter('the_content', 'nzshpcrt_show_categories', 12);
  add_filter('the_content', 'nzshpcrt_substitute_buy_now_button', 12);
  return $excerpt;
  }

function nzshpcrt_disable_page_filters($excerpt = '') {
  remove_filter('the_content', 'nzshpcrt_products_page');
  remove_filter('the_content', 'nzshpcrt_shopping_cart');
  remove_filter('the_content', 'nzshpcrt_transaction_results');
  remove_filter('the_content', 'nzshpcrt_checkout');
  remove_filter('the_content', 'nszhpcrt_homepage_products');
  remove_filter('the_content', 'nzshpcrt_user_log');
  remove_filter('the_content', 'nszhpcrt_category_tag');
  remove_filter('the_content', 'nzshpcrt_show_categories');
  remove_filter('the_content', 'nzshpcrt_substitute_buy_now_button');
  return $excerpt;
  }

nzshpcrt_enable_page_filters();

add_filter('get_the_excerpt', 'nzshpcrt_disable_page_filters', -1000000);
add_filter('get_the_excerpt', 'nzshpcrt_enable_page_filters', 1000000);
 
 
add_action('wp_head', 'nzshpcrt_style');

add_action('admin_head', 'wpsc_admin_css');
if($_GET['page'] == WPSC_DIR_NAME."/display-log.php") {
  add_action('admin_head', 'nzshpcrt_product_log_rss_feed');
}
add_action('wp_head', 'nzshpcrt_javascript');
add_action('wp_head', 'nzshpcrt_product_list_rss_feed');

if(($_POST['submitwpcheckout'] == 'true')) {
  add_action('init', 'nzshpcrt_submit_checkout');
}
add_action('init', 'nzshpcrt_submit_ajax');
add_action('init', 'nzshpcrt_download_file');
add_action('init', 'nzshpcrt_display_preview_image');

if(stristr($_GET['page'], WPSC_DIR_NAME)) {
  add_action('admin_notices', 'wpsc_admin_notices');
}


function wpsc_admin_notices() {
  global $wpdb;
  if(get_option('wpsc_default_category') != 'all') {
		if((get_option('wpsc_default_category') < 1) || $wpdb->get_var("SELECT `id` FROM `{$wpdb->prefix}product_categories` WHERE `id` IN ('".get_option('wpsc_default_category')."') AND `active` NOT IN ('1');")) {  // if there is no default category or it is deleted
			if(!$_POST['wpsc_default_category']) { // if we are not changing the default category
				echo "<div id='message' class='updated fade' style='background-color: rgb(255, 251, 204);'>";
				echo "<p>".TXT_WPSC_NO_DEFAULT_PRODUCTS."</p>";
				echo "</div>\n\r";
			}
		}
  }
}

function wpsc_admin_latest_activity() {
  $user = wp_get_current_user();
	if($user->user_level>9){
		echo "<div>";
		echo "<h3>".TXT_WPSC_E_COMMERCE."</h3>";
		echo "<p>";
		
		echo "<strong>".TXT_WPSC_TOTAL_THIS_MONTH."</strong><br />";
		$year = date("Y");
		$month = date("m");
		$start_timestamp = mktime(0, 0, 0, $month, 1, $year);
		$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
		echo nzshpcrt_currency_display(admin_display_total_price($start_timestamp, $end_timestamp),1);
		echo "</p>";
		echo "<p>";
		echo "<strong>".TXT_WPSC_TOTAL_INCOME."</strong><br />";
		echo nzshpcrt_currency_display(admin_display_total_price(),1);
		echo "</p>";
		echo "</div>";
		}
  }

add_action('activity_box_end', 'wpsc_admin_latest_activity');

//this adds all the admin pages, before the code was a mess, now it is slightly less so.
add_action('admin_menu', 'nzshpcrt_displaypages');

// pe.{
if(get_option('wpsc_share_this') == 1) {
  if(stristr(("http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']), get_option('product_list_url'))){
    include_once(WPSC_FILE_PATH."/share-this.php");
  }
}
 
add_filter('option_update_plugins', 'wpsc_plugin_no_upgrade');
function wpsc_plugin_no_upgrade($option) {
	$this_plugin = plugin_basename(__FILE__);
  //echo "<pre>".print_r($option->response[ $this_plugin ],true)."</pre>";
	if( isset($option->response[ $this_plugin ]) ) {
		$option->response[ $this_plugin ]->package = '';
	}
	return $option;
}

// if(get_option('cat_brand_loc') != 0) {
//   add_action('wp_list_pages', 'show_cats_brands');
//   }
// }.pe
add_action('plugins_loaded', 'widget_wp_shopping_cart_init');


// refresh page urls when permalinks are turned on or altered
add_filter('mod_rewrite_rules', 'wpsc_refresh_page_urls');

// refresh the page URL's when permalinks are turned off
// the plugin hook used just above doesnt run when they are turned off
// if(stristr($_POST['_wp_http_referer'], 'options-permalink.php')) {
// 	add_filter('admin_head', 'wpsc_refresh_page_urls');
// }


if(strpos($_SERVER['SCRIPT_NAME'], "wp-admin") === false) {
  wp_enqueue_script( 'jQuery', WPSC_URL.'/js/jquery.js', false, '1.2.3');
// 	wp_enqueue_script('instinct_thickbox',WPSC_URL.'/js/thickbox.js', 'jQuery', 'Instinct_e-commerce');
	wp_enqueue_script('ngg-thickbox',WPSC_URL.'/js/thickbox.js', 'jQuery', 'Instinct_e-commerce');
} else {
	wp_enqueue_script('thickbox');
	wp_enqueue_script('ui-tabs',WPSC_URL.'/js/jquery.tabs.pack.js?ver=2.7.4', array('jquery'), '2.7.4');
}
if(strpos($_SERVER['REQUEST_URI'], WPSC_DIR_NAME.'') !== false) {
	wp_enqueue_script('interface',WPSC_URL.'/js/interface.js', 'Interface');
}

switch(get_option('cart_location')) {
  case 1:
  add_action('wp_list_pages','nzshpcrt_shopping_basket');
  break;
  
  case 2:
  add_action('the_content', 'nzshpcrt_shopping_basket' , 14);
  break;
  
  case 4:
  break;
  
  case 5:
  //exit("<pre>".print_r($_SERVER,true)."</pre>");
  if(function_exists('drag_and_drop_cart')) {
    $shop_pages_only = 1;
		add_action('init', 'drag_and_drop_cart_ajax');  
		if (get_option('dropshop_display')=='product'){
		  $url_prefix_array = explode("://", get_option('product_list_url'));
		  $url_prefix = $url_prefix_array[0]."://";
			if(stristr(($url_prefix.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']), get_option('product_list_url'))){
			  
				wp_enqueue_script('interface',WPSC_URL.'/js/interface.js', 'Interface');
				add_action('wp_head', 'drag_and_drop_js');  
				add_action('wp_footer', 'drag_and_drop_cart');  
			}
		} else {		  
			wp_enqueue_script('interface',WPSC_URL.'/js/interface.js', 'Interface');
			add_action('wp_head', 'drag_and_drop_js');  
			add_action('wp_footer', 'drag_and_drop_cart');  
		}
	}
  break;
  
  case 3:
  //add_action('the_content', 'nzshpcrt_shopping_basket');
  //<?php nzshpcrt_shopping_basket(); ?/>   
  break;
  
  default:
  add_action('the_content', 'nzshpcrt_shopping_basket', 14);
  break;
}


  
/*
 * This serializes the shopping cart variable as a backup in case the unserialized one gets butchered by various things
 */  
function serialize_shopping_cart() {
  global $wpsc_start_time, $wpsc_debug_sections;
  @$_SESSION['nzshpcrt_serialized_cart'] = serialize($_SESSION['nzshpcrt_cart']);
  if(WPSC_DEBUG === true) {
    $wpsc_end_time = microtime_float() - $wpsc_start_time;    
    $memory_usage = (@memory_get_usage() / 1000);
    $debug_message = "/*\n\r<div style='position: absolute; top: 4px; left: 4px; background: #ffffff; padding: 3px; outline: 1px solid black; text-align: left;'>\n\r";
    $debug_message .= "<div>Total Seconds: $wpsc_end_time</div>\n\r";
    $debug_message .= "<div>Total Memory: $memory_usage kb</div>\n\r";
    //$sections 
    
    foreach((array)$wpsc_debug_sections as $debug_section_name => $debug_section_values) {
      $execution_time = ($debug_section_values['stop'] - $debug_section_values['start']);
      $debug_message .= "<div>{$debug_section_name} Seconds: {$execution_time}</div>\n\r";
    }
    
    $debug_message .= "</div>\n\r*/";
    //mail(get_option('purch_log_email'), "debug_report", $debug_email);
    exit($debug_message);  
	}
  return true;
}  
register_shutdown_function("serialize_shopping_cart");

?>
