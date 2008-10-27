<?php
class extras {
	function extras(){
		return;
	}

	function display_extra_values($prefix,$extras_id) {
		global $wpdb;
		if(is_numeric($variation_id)) {
			$variation_values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."extras_values` WHERE `variation_id` = '$variation_id' ORDER BY `id` ASC",ARRAY_A);
			if($variation_values != null) {
				$output .= "<input type='hidden' name='' value='".$variation_id."'>";
				$output .= "<table>";
				$output .= "<tr><th>".TXT_WPSC_VISIBLE."</th><th>".TXT_WPSC_NAME."</th></tr>";
				foreach($variation_values as $variation_value) {
					$output .= "<tr>";
					$output .= "<td style='text-align: center;'><input type='checkbox' name='variation_values[".$variation_id."][".$variation_value['id']."][active]' value='1' checked='true' id='variation_active_".$variation_value['id']."' />";
					$output .= "<input type='hidden' name='variation_values[".$variation_id."][".$variation_value['id']."][blank]' value='null' />  </td>";
					$output .= "<td>".$variation_value['name']."</td>";
					$output .= "</tr>";
				}
			
				$output .= "<tr>";
				$output .= "<td colspan='4'>";
				$output .= "<a href='#' onclick='return remove_variation_value_list(\\\"$prefix\\\",\\\"$variation_id\\\");'>".TXT_WPSC_REMOVE_SET."</a>";
				$output .= "</td>";
				$output .= "</tr>";
				$output .= "</table>";
			}
		}
		return $output;
	}

	function display_product_extras($product_id,$no_label = false, $no_br = false, $update_price = false ) {
		global $wpdb;
		$output='';
		$sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$product_id."' LIMIT 1";
		$product_data = $wpdb->get_row($sql,ARRAY_A);
		$extras_assoc_sql = "SELECT * FROM `".$wpdb->prefix."extras_values_associations` WHERE product_id IN ('$product_id')";
		$extras_assoc_data = $wpdb->get_results($extras_assoc_sql,ARRAY_A);
		if (count($extras_assoc_data)==0){
			return '';
		}
		foreach($extras_assoc_data as $extras_association) {
			$extras_ids[] = $extras_association['extras_id'];
		}
		//echo 
		$special = 'false';
		if($no_label == true) {
			$special = 'true';
		}
		$extras_ids_str = implode(',',$extras_ids);
		$extras_name_sql = "SELECT * FROM ".$wpdb->prefix."product_extra WHERE id IN (".$extras_ids_str.") ORDER BY id";
		$extras_name_data = $wpdb->get_results($extras_name_sql,ARRAY_A);
		//exit("<pre>".print_r($extras_name_data,1)."</pre>");
		$j=0;$x=0;
		foreach ($extras_name_data as $extras_name_datum) {
			$j++;
			$extras_value_sql = "SELECT * FROM ".$wpdb->prefix."extras_values WHERE extras_id IN (".$extras_name_datum['id'].")";
			$extras_value_data = $wpdb->get_results($extras_value_sql,ARRAY_A);
			//exit("<pre>".print_r($extras_value_data,1)."</pre>");
			$output.= "<label>".$extras_name_datum['name']."</label>".$extras_name_datum['price']."<br>";
			if ($j==1) {
				$price='';
				$checked='checked="checked"';
			} else {
				$price=nzshpcrt_currency_display($extras_assoc_data[$x]['price'],0);
				$checked='';
			}
			foreach ($extras_value_data as $extras_value_datum) {
				if ($j==1) {
					$price='';
				} else {
					$price=nzshpcrt_currency_display($extras_assoc_data[$x]['price'],0);
				}
				$output.="<input style='float:left;' type='checkbox' $checked name='extras[]' value='".$extras_value_datum['id']."' class='extras_".$product_id."' id='extras_".$product_id."_".$extras_value_datum['id']."' onclick='manage_extras(".$product_id.",".$extras_value_datum['id'].",".$special.")' id='extra_value_id_".$extras_value_datum['id']."'><label style='float:left;' for='extras_".$product_id."_".$extras_value_datum['id']."'>". $extras_value_datum['name']."&ensp;".$price."</label><img style='display:none;float:left;' id='extras_indicator".$product_id.$extras_value_datum['id']."' src=' ".WPSC_DIR_NAME."/images/indicator.gif'><br>";
				$x++;
			}
		}
		return $output;
	}
}
?>