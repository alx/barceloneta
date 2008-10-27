<?php

$wpconfig = realpath("../../../../../wp-config.php");

if (!file_exists($wpconfig))  {
	echo "Could not found wp-config.php. Error in path :\n\n".$wpconfig ;	
	die;	
}// stop when wp-config is not there

require_once($wpconfig);
require_once(ABSPATH.'/wp-admin/admin.php');
// 
// // check for rights
// if(!current_user_can('edit_posts')) die;

global $wpdb;

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>WP Shopping Cart</title>
<!-- 	<meta http-equiv="Content-Type" content="<?php// bloginfo('html_type'); ?>; charset=<?php //echo get_option('blog_charset'); ?>" /> -->
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo WPSC_URL; ?>/js/tinymce3/tinymce.js"></script>
	<base target="_self" />
</head>
		<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('category').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="WPSC" action="#">
	<div class="tabs">
		<ul>
		<li id="category" class="current"><span><a href="javascript:mcTabs.displayTab('category','wpsc_category_panel');" onmousedown="return false;"><?php _e("Category", 'wpsc_category'); ?></a></span></li>
		<li id="prodcut_slider"><span><a href="javascript:mcTabs.displayTab('prodcut_slider','product_slider_panel');" onmousedown="return false;"><?php _e("Product Slider", 'wpsc_category'); ?></a></span></li>
		</ul>
	</div>
	
	<div class="panel_wrapper">
		<!-- gallery panel -->
		<div id="wpsc_category_panel" class="panel current">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
			<td nowrap="nowrap"><label for="wpsc_category"><?php _e("Select Category", 'wpsc_category'); ?></label></td>
		<td><select id="wpsc_category" name="wpsc_category" style="width: 200px">
			<option value="0"><?php _e("No Category", 'wpsc_category'); ?></option>
				<?php
				$categorylist = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."product_categories ORDER BY id ASC",ARRAY_A);
				if(is_array($categorylist)) {
					foreach($categorylist as $category) {
						echo "<option value=".$category['id']." >".$category['name']."</option>"."\n";
					}
				}
				?>
            </select></td>
          </tr>
          <tr>
						<td nowrap="nowrap" valign="top"><label for="showtype"><?php _e("Full Display", 'wpsc_category'); ?></label></td>
            <td><label><input name="showtype" id='wpsc_fulldisplay' type="checkbox" checked="checked" /></label></td>
          </tr>
        </table>
		</div>
		<!-- gallery panel -->
		<div id="product_slider_panel" class="panel">
		<br />
		<table border="0" cellpadding="4" cellspacing="0">
         <tr>
			<td nowrap="nowrap"><label for="wpsc_category"><?php _e("Select Category", 'wpsc_category'); ?></label></td>
		<td><select id="wpsc_slider_category" name="wpsc_category" style="width: 200px">
			<option value="0"><?php _e("No Category", 'wpsc_category'); ?></option>
				<?php
				$categorylist = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."product_categories ORDER BY id ASC",ARRAY_A);
				if(is_array($categorylist)) {
					foreach($categorylist as $category) {
						echo "<option value=".$category['id']." >".$category['name']."</option>"."\n";
					}
				}
				?>
            </select></td>
          </tr>
	  <tr>
		<td>
			<?php _e("Number of visible items", 'wpsc_category'); ?>:
		</td>
		<td>
			<input type='text' id='wpsc_slider_visibles' name='wpsc_slider_visibles'>
		</td>
	</tr>
        </table>
		</div>
		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			    <input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'wpsc_category'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
				<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'wpsc_category'); ?>" onclick="insertWPSCLink();" />
		</div>
	</div>
</form>
</body>
</html>
