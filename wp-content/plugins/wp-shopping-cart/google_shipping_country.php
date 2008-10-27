<?php
	if (isset($_POST['submit'])) {
		foreach($_POST['google_shipping'] as $key=>$country) {
			if ($country=='on') {
				$google_shipping_country[]=$key;
			}
		}
		update_option('google_shipping_country',$google_shipping_country);
		//header("Location: ?page=".$_GET['page']);
	}
?>

<div class="wrap">
<h2><?php echo TXT_WPSC_GOOGLESHIPPING;?></h2>
<form action='?page=<?php echo $_GET['page']; ?>&amp;googlecheckoutshipping=1' method='POST'>
<?php
	$google_shipping_country = get_option("google_shipping_country");
	$country_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."currency_list` ORDER BY `country` ASC",ARRAY_A);
	$country_data = array_chunk($country_data, 50);
	echo "<table>\n\r";
	echo "<tr>\n\r";
	foreach($country_data as $country_col)
	{
		echo "<td style='vertical-align: top; padding-right: 3em;'>\n\r";
		echo "<table>\n\r";
		foreach($country_col as $country) {
			if (in_array($country['id'], (array)$google_shipping_country)) {
				$checked="checked='true'";
			} else {
				$checked="";
			}
			echo "  <tr>\n\r";
			echo "    <td><input $checked type='checkbox' id='google_shipping_".$country['id']."' name='google_shipping[".$country['id']."]'/></td>\n\r";
			if($country['id'] == $base_country){
				echo "    <td><label for='google_shipping_".$country['id']."' style='text-decoration: underline;'>".$country['country'].":</label></td>\n\r";
			} else {
				echo "    <td><label for='google_shipping_".$country['id']."'>".$country['country']."</label></td>\n\r";
			}
			
			echo "  </tr>\n\r";
		}
		echo "</table>\n\r";
		echo "    </td>\n\r";
	}
	echo "  </tr>\n\r";
	echo "</table>\n\r";
?>
	<a style="cursor:pointer;" onclick="jQuery('input[@type=\'checkbox\']').each(function() {this.checked = true; });">Select All</a>&emsp; <a style="cursor:pointer;" onclick="jQuery('input[@type=\'checkbox\']').each(function() {this.checked = false; });">Unselect All</a><br><br>
		<input type='submit' name='submit' value='<?php echo TXT_WPSC_SAVE_CHANGES;?>' /> <a href='?page=<?=$_GET['page']?>'>Go Back</a>
	</form>
</div>