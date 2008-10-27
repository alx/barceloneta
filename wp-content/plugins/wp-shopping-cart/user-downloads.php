<?php
	global $wpdb, $user_ID;
	$sql = "SELECT `id` FROM `".$wpdb->prefix."purchase_logs` WHERE user_ID = ".$user_ID."";
	$purchases= $wpdb->get_col($sql) ;
	$rowcount = count($purchases);
	//echo "<pre>".print_r($purchases,true)."</pre>";
	
	if($rowcount >= 1) {
		$perchidstr = "(";
		$perchidstr .= implode(',',$purchases);
	  $perchidstr .= ")";		
		$sql = "SELECT * FROM `".$wpdb->prefix."download_status` WHERE `purchid` IN ".$perchidstr." AND `active` IN ('1')";
		$products = $wpdb->get_results($sql,ARRAY_A) ;
	}
	//exit($products);
	foreach ((array)$products as $key => $product){
	  if($product['uniqueid'] == null) {  // if the uniqueid is not equal to null, its "valid", regardless of what it is
	  	$links[] = get_option('siteurl')."?downloadid=".$product['id'];
	  } else {
	  	$links[] = get_option('siteurl')."?downloadid=".$product['uniqueid'];
	  }	
		$sql = "SELECT * FROM `".$wpdb->prefix."product_files` WHERE id = ".$product['fileid']."";
		$file = $wpdb->get_results($sql,ARRAY_A) ;
		$files[] = $file[0];
	}
	
	//exit("---------------<pre>".print_r($files,1)."</pre>");
	?>
<div class="wrap" style=''>
	<?php
		echo " <div class='user-profile-links'><a href='".get_option('user_account_url')."'>Purchase History</a> | <a href='".get_option('user_account_url').$seperator."edit_profile=true'>Your Details</a> | <a href='".get_option('user_account_url').$seperator."downloads=true'>Your Downloads</a></div><br />";
	?>
	<?php
	if(count($files) > 0) {
	?>
		<table class='logdisplay'>
			<tr>
				<th>
					File Names
				</th>
				<th>
					Downloads Left
				</th>
			</tr>
			<tr>
				<td>
					<?php
						$i=0;
						
						foreach((array)$files as $file){
							$alternate1 = "";
							if(($i % 2) != 0)
							{
							$alternate1 = "class='alt'";
							}
							echo "<tr $alternate1>\n\r";
							echo "<td>";
							if ($products[$i]['downloads'] > 0) {
								echo "<a href = ".$links[$i].">".$file['filename']."</a>";
							} else {
								echo $file['filename']."";
							}
							echo "</td>";
							echo "<td>";
							echo $products[$i]['downloads'];
							echo "</td>";
							echo "</tr>";
							$i++;
						}
					?>
				</td>
			</tr>
		</table>
	<?php
} else {	
	echo TXT_WPSC_NO_DOWNLOADABLES; 
}
?>