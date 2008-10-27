<?php

require_once (ABSPATH . WPINC . '/rss.php');
global $wpdb;
?>
<div class="wrap">
  <?php
  if($_GET['debug'] == 'true') {  
    //echo "<pre>".print_r($_SESSION['nzshpcrt_cart'],true)."</pre>";
		phpinfo();
	} else if($_GET['zipup'] == 'true') {  
	  // Code to zip the plugin up for ease of downloading from slow or otherwise cruddy FTP servers, we sometimes develop on servers like that
		$ecommerce_path = escapeshellarg(ABSPATH."wp-content/plugins/wp-shopping-cart");
		$destination_path = escapeshellarg(ABSPATH."wp-content/plugins/wp-shopping-cart.tar.gz");
		/// disabled for excess paranoia
		//echo `tar -czf $destination_path $ecommerce_path`;
		//echo "<a href='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart.tar.gz' />Downloaded the zipped up plugin here</a>";
		exit();
	} else {
	?>
	<h2><?php echo TXT_WPSC_HELPINSTALLATION;?></h2>
	<p>
		<?php echo TXT_WPSC_INSTRUCTIONS;?>
	</p>
	<?php
	}
?>
</div>