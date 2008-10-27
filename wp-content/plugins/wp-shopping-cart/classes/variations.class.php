<?php
class nzshpcrt_variations {
  function nzshpcrt_variations() {
    global $wpdb;
	}
    
  function display_variation_values($prefix,$variation_id) {
    global $wpdb;
    if(is_numeric($variation_id)) {
      $variation_values = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}variation_values` WHERE `variation_id` = '{$variation_id}' ORDER BY `id` ASC",ARRAY_A);
      if($variation_values != null) {
        $output .= "<input type='hidden' name='variation_id[]' class='variation_ids' value='{$variation_id}'>";
        $output .= "<table>";
        $output .= "<tr><th>".TXT_WPSC_VISIBLE."</th><th>".TXT_WPSC_NAME."</th></tr>";
        foreach($variation_values as $variation_value) {
          $output .= "<tr>";
          $output .= "<td style='text-align: center;'><input type='checkbox' name='variation_values[{$variation_id}][{$variation_value['id']}][active]' value='1' checked='true' id='variation_active_{$variation_id}_{$variation_value['id']}' />";
          $output .= "<input type='hidden' name='variation_values[{$variation_id}][{$variation_value['id']}][blank]' value='null' />  </td>";
          $output .= "<td>".stripslashes($variation_value['name'])."</td>";
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
    
  
  function falsepost_variation_values($variation_id) {
    global $wpdb;
    if(is_numeric($variation_id)) {
      $variation_values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` = '$variation_id' ORDER BY `id` ASC",ARRAY_A);
      if($variation_values != null) {
        foreach($variation_values as $variation_value) {
          $output_variation_values[$variation_id][$variation_value['id']]['active'] = 1;
          $output_variation_values[$variation_id][$variation_value['id']]['blank'] = 'null';
				}
			}
		}
    return $output_variation_values;
	}
    
    
  function add_to_existing_product($product_id,$variation_list) {
     global $wpdb;
    if(is_numeric($product_id)) {
      foreach($variation_list as $variation_id => $variation_values) {
        if(is_numeric($variation_id)) {
          $num = 0;
          $variation_assoc_sql = "INSERT INTO `".$wpdb->prefix."variation_associations` ( `type` , `name` , `associated_id` , `variation_id` ) VALUES ( 'product', '', '$product_id', '$variation_id');";

          $product_assoc_sql = "INSERT INTO `".$wpdb->prefix."variation_values_associations` ( `product_id` , `value_id` , `quantity` , `price` , `visible` , `variation_id` ) VALUES";
          foreach($variation_values as $variation_value_id => $variation_value_properties) {
            if(is_numeric($variation_value_id)) {
              switch($num) {
                case 0:
                $comma = '';
                break;
                
                default:
                $comma = ', ';
                break;
							}
                
              if(is_numeric($variation_value_properties['price']) && ($variation_value_properties['price'] > 0))
                {
                $price = $variation_value_properties['price'];
							} else {
								$price = '';
							}
                
              if($variation_value_properties['active'] == 1) {
                $active = 1;
							} else {
								$active = 0;
							}
              
              if(is_numeric($variation_value_properties['stock']) && ($variation_value_properties['stock'] > 0)) {
                $quantity = $variation_value_properties['stock'];
							} else {
								$quantity = 0;
							}
              
              $product_assoc_sql .= "$comma ( '$product_id', '$variation_value_id', '$quantity', '".$price."', '$active', '$variation_id')";
              $num++;
						}
					}
          $product_assoc_sql .= ";";
          $wpdb->query($product_assoc_sql);
          $wpdb->query($variation_assoc_sql);
				}
			}
		}
    return $output;
	}
  
  function variations_grid_view($product_id) {
    global $wpdb;
    
		$product_price = $wpdb->get_var("SELECT `price` FROM `".$wpdb->prefix."product_list` WHERE `id` IN('$product_id') LIMIT 1");
    
    $associated_variations = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_associations` WHERE `type` IN ('product') AND `associated_id` = '$product_id' ORDER BY `id` ASC",ARRAY_A);
    $variation_count = count($associated_variations);
    if(!(($variation_count >= 1) && ($variation_count <= 2))) {
      return "";
      exit();
		}

    switch($variation_count) {
      case 1:
      $variation_name = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$associated_variations[0]['variation_id']."' ORDER BY `id` ASC LIMIT 1");

      $grid_header = str_replace(":variation1:",$variation_name ,TXT_WPSC_VARIATION_GRID_CONTROL_SINGLE);
      break;
      
      case 2:
      $variation_name1 = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$associated_variations[0]['variation_id']."' ORDER BY `id` ASC LIMIT 1");
      $variation_name2 = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$associated_variations[1]['variation_id']."' ORDER BY `id` ASC LIMIT 1");
      
      $grid_header = str_replace(Array(":variation1:", ":variation2:"),Array($variation_name1, $variation_name2) ,TXT_WPSC_VARIATION_GRID_CONTROL_PAIR);
      
      break;
		}
    
    $output .= "<table class='product_variation_grid'>\n\r";   
    $output .= "  <tr>\n\r";
    $output .= "    <th colspan='3' class='variation_name'>".$grid_header."</th>\n\r";
    $output .= "  </tr>\n\r";
    $output .= "  <tr>\n\r";
    $output .= "    <td><strong>".TXT_WPSC_VARIATION."</strong></td>\n\r";
    $output .= "    <td><strong>".TXT_WPSC_STOCK."</strong></td>\n\r";
    $output .= "    <td><strong>".TXT_WPSC_PRICE."</strong></td>\n\r";
	 if(count($associated_variations) == 1) {
			$output .= "    <td><strong>".TXT_WPSC_ASSOCIATEWITHFILE."</strong></td>\n\r";
		}
		$output .= "  </tr>\n\r";
    if(count($associated_variations) == 2) {
      foreach($associated_variations as $key => $associated_variation) {
        $associated_variation_values[$key] = $wpdb->get_results("SELECT `".$wpdb->prefix."variation_values`.* FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` = '".$associated_variation['variation_id']."'",ARRAY_A);
        }
      
      foreach((array)$associated_variation_values[0] as $associated_variation_value[0]) {
        $variation_active[0] = $wpdb->get_var("SELECT `visible` FROM `".$wpdb->prefix."variation_values_associations` WHERE `product_id` = '$product_id' AND `value_id` = '".$associated_variation_value[0]['id']."' AND `variation_id` = '".$associated_variation_value[0]['variation_id']."'");
        if(($variation_active[0] === null) || ($variation_active[0] == 1)) {
          foreach((array)$associated_variation_values[1] as $associated_variation_value[1]) {
            $variation_active[1] = $wpdb->get_var("SELECT `visible` FROM `".$wpdb->prefix."variation_values_associations` WHERE `product_id` = '$product_id' AND `value_id` = '".$associated_variation_value[1]['id']."' AND `variation_id` = '".$associated_variation_value[1]['variation_id']."'");
            if(($variation_active[1] === null) || ($variation_active[1] == 1)) {
              $output .= "  <tr>\n\r";
              $output .= "    <td>".str_replace(" ", "&nbsp;", (stripslashes($associated_variation_value[0]['name']).", ".stripslashes($associated_variation_value[1]['name'])))."</td>\n\r";
              $variation_info = $wpdb->get_row("SELECT id, file, `stock`,`price` FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '$product_id' AND (`variation_id_1` = '".$associated_variation_value[0]['id']."' AND `variation_id_2` = '".$associated_variation_value[1]['id']."') OR (`variation_id_1` = '".$associated_variation_value[1]['id']."' AND `variation_id_2` = '".$associated_variation_value[0]['id']."') LIMIT 1",ARRAY_A);
              $output .= "    <td><input type='text' name='variation_priceandstock[".$associated_variation_value[0]['id']."][".$associated_variation_value[1]['id']."][stock]' value='".$variation_info['stock']."' size='3' /></td>\n\r";
              $output .= "    <td><input type='text' name='variation_priceandstock[".$associated_variation_value[0]['id']."][".$associated_variation_value[1]['id']."][price]' value='";
              if(is_numeric($variation_info['price'])) {
                $output .= number_format($variation_info['price'],2,'.', '');
							} else {
								$output .= $product_price;
							}
              $output .= "' size='6' /></td>\n\r";
								// 		if ($variation_info['file']=='1'){
								// 			$checked = "checked=true";
								// 		} else {
								// 			$checked = "";
								// 		} 
								// 		$output .= "<td><input $checked type='checkbox' name='file".$variation_info['id']."'/></td>";
              $output .= "  </tr>\n\r";
						}
					}
				}
			}
		} else {
			$associated_variation = $associated_variations[0];
			$associated_variation_values = $wpdb->get_results("SELECT `".$wpdb->prefix."variation_values`.* FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` = '".$associated_variation['variation_id']."'",ARRAY_A);

			foreach((array)$associated_variation_values as $associated_variation_value) {
				$variation_active = $wpdb->get_var("SELECT `visible` FROM `".$wpdb->prefix."variation_values_associations` WHERE `product_id` = '$product_id' AND `value_id` = '".$associated_variation_value['id']."' AND `variation_id` = '".$associated_variation_value['variation_id']."'");
				if(($variation_active === null) || ($variation_active == 1)) {
					$output .= "  <tr>\n\r";
					$output .= "    <td>".str_replace(" ", "&nbsp;", stripslashes($associated_variation_value['name']))."</td>\n\r";
					$variation_info = $wpdb->get_row("SELECT id, `stock`,`price`, file FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '$product_id' AND (`variation_id_1` = '".$associated_variation_value['id']."' AND `variation_id_2` = '0') LIMIT 1",ARRAY_A);
					$output .= "    <td><input type='text' name='variation_priceandstock[".$associated_variation_value['id']."][0][stock]' value='".$variation_info['stock']."' size='3' /></td>\n\r";
					$output .= "    <td><input type='text' name='variation_priceandstock[".$associated_variation_value['id']."][0][price]' value='";
					if(is_numeric($variation_info['price'])) {
						$output .= number_format($variation_info['price'],2,'.', '');
					} else {
						$output .= $product_price;
					}
					$output .= "' size='6' /></td>\n\r";
					if ($variation_info['file']=='1'){
						$checked = "checked=true";
					} else {
						$checked = "";
					}
					$output .= "<td><input $checked type='checkbox' name='variation_priceandstock[".$associated_variation_value['id']."][0][file]'/></td>";
					$output .= "  </tr>\n\r";
				}
			}
		}
    
    $output .= "</table>\n\r";    
    return $output;
	}
  
  function display_attached_variations($product_id)
    {
     global $wpdb;
    $associated_variations = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_associations` WHERE `type` IN ('product') AND `associated_id` = '$product_id' ORDER BY `id` ASC",ARRAY_A);
    foreach((array)$associated_variations as $associated_variation)
      {
      $associated_variation_values = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values_associations` WHERE `variation_id` = '".$associated_variation['variation_id']."' AND `product_id` = '$product_id' ORDER BY `id` ASC",ARRAY_A);
      
      $variation_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$associated_variation['variation_id']."' ORDER BY `id` ASC LIMIT 1",ARRAY_A);
      //exit("SELECT * FROM `".$wpdb->prefix."variation_values_associations` WHERE `variation_id` = '".$associated_variation['variation_id']."' AND `product_id` = '$product_id' ORDER BY `id` ASC");
      $variation_data = $variation_data[0];
      
      $output .= "<table class='product_variation_listing'>";
      $output .= "<tr><th colspan='4' class='variation_name'>".$variation_data['name']."</th></tr>";
       $output .= "<tr><th>".TXT_WPSC_VISIBLE."</th><th>".TXT_WPSC_NAME."</th></tr>";
      $num = 0;
      $not_included_in_statement = '';
      foreach((array)$associated_variation_values as $associated_variation_value) {
        $product_value_id = $associated_variation_value['id'];
        $value_id = $associated_variation_value['value_id'];
        $value_stock = $associated_variation_value['quantity'];
        $value_price = $associated_variation_value['price'];
        $value_active = "";
        if($associated_variation_value['visible'] == 1) {
          $value_active = "checked='true'";
          }
        $value_data = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id` = '$value_id' ORDER BY `id` ASC",ARRAY_A);
        $value_data = $value_data[0];
        $output .= "<tr>";
        
        $output .= "<td style='text-align: center;'><input type='checkbox' name='edit_variation_values[".$product_value_id."][active]' value='1' id='variation_active_".$value_id."' $value_active>
        <input type='hidden' name='edit_variation_values[".$product_value_id."][blank]' value='null'>
        </td>"; 
        $output .= "<td>".stripslashes($value_data['name'])."</td>";
        $output .= "</tr>";
        switch($num) {
          case 0:
          $comma = '';
          break;
          
          default:
          $comma = ', ';
          break;
				}
        $not_included_in_statement .= "$comma'$value_id'";
        $num++;
			}
      if($not_included_in_statement != '')
        {
        $not_included_sql = "SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `variation_id` IN ('".$associated_variation['variation_id']."') AND `id` NOT IN ($not_included_in_statement)";
        $values_not_included = $wpdb->get_results($not_included_sql,ARRAY_A);
        }

      
      //$output .= "<pre>".print_r($not_included_sql,true)."</pre>";  
      $variation_id = $associated_variation['variation_id'];
      if($values_not_included != null)
        {
        foreach($values_not_included as $variation_value)
          {
          $output .= "<tr>";
          $output .= "<td style='text-align: center;'><input type='checkbox' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][active]' value='1' id='variation_active_".$variation_value['id']."'>
          <input type='hidden' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][blank]' value='null' />
          </td>"; 
          $output .= "<td>".$variation_value['name']."</td>";
//           $output .= "<td><input type='text' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][stock]' size='3' value='' /></td>";
//           $output .= "<td><input type='text' name='edit_add_variation_values[".$variation_id."][".$variation_value['id']."][price]' size='3' value='' /></td>";
          $output .= "</tr>";
          }
        }
      
      $output .= "<tr>";
      $output .= "<td colspan='4'>";
      $output .= "<a href='admin.php?page=".WPSC_DIR_NAME."/display-items.php&amp;submit_action=remove_set&amp;product_id=".$product_id."&amp;variation_assoc_id=".$associated_variation['id']."'>".TXT_WPSC_REMOVE_SET."</a>";
      $output .= "</td>";
      $output .= "</tr>";
      $output .= "</table>";
      $num++;
      }
    //$output .= "<pre>".print_r($values_not_included,true)."</pre>";  
    return $output;
    }
    
  function edit_product_values($product_id,$variation_value_list) {
     global $wpdb;
    foreach($variation_value_list as $variation_value_id => $variation_values) {
      $quantity = $variation_values['stock'];
      $variation_values['price'] = str_replace(",","",$variation_values['price']);
      //exit(print_r($variation_values,true));
      if(is_numeric($variation_values['price']) && ($variation_values['price'] > 0)) {
        $price = $variation_values['price'];
			} else {
				$price = '';
			}
        
      if($variation_values['active'] == 1) {
        $visible_state = 1;
			} else {
				$visible_state = 0;
			}
      $update_sql = "UPDATE `".$wpdb->prefix."variation_values_associations` SET `visible` = '".$visible_state."' WHERE `id` = '".$variation_value_id."' LIMIT 1 ;";
      $wpdb->query($update_sql);
      //echo "<pre>".print_r($update_sql,true)."</pre>";
      //echo "<pre>".print_r($variation_values,true)."</pre>"; 
		}
    return $output;
	}
    
   function edit_add_product_values($product_id,$variation_value_list) {
    global $wpdb;
    foreach($variation_value_list as $variation_id => $variation_values) {
      if(is_numeric($variation_id)) { 
        foreach($variation_values as $variation_value_id => $variation_value_properties) {
          $quantity = $variation_value_properties['stock'];
          $variation_value_properties['price'] = str_replace(",","",$variation_value_properties['price']);
          if(is_numeric($variation_value_properties['price']) && ($variation_value_properties['price'] > 0)) {
            $price = $variation_value_properties['price'];
					} else {
						$price = '';
					}
            
          if($variation_value_properties['active'] == 1) {
            $visible_state = 1;
					} else {
						$visible_state = 0;
					}
          $product_assoc_sql = "INSERT INTO `".$wpdb->prefix."variation_values_associations` ( `product_id` , `value_id` , `quantity` , `price` , `visible` , `variation_id` ) VALUES ( '$product_id', '$variation_value_id', '".$quantity."', '".$price."', '$visible_state', '$variation_id')";
          $wpdb->query($product_assoc_sql);
         // echo "<pre>".print_r($product_assoc_sql,true)."</pre>";
          //echo "<pre>".print_r($variation_values,true)."</pre>"; 
				}
			}
		}
    return $output;
	}
  
  function display_product_variations($product_id,$no_label = false, $no_div = false, $update_price = false ) {
    global $wpdb;
    $sql = "SELECT * FROM `".$wpdb->prefix."product_list` WHERE `id`='".$product_id."' LIMIT 1";
    $product_data = $wpdb->get_row($sql,ARRAY_A);
    $variation_assoc_sql = "SELECT * FROM `".$wpdb->prefix."variation_associations` WHERE `type` IN ('product') AND `associated_id` IN ('$product_id')";
    $variation_assoc_data = $wpdb->get_results($variation_assoc_sql,ARRAY_A);
    if($variation_assoc_data != null) {
      $first_entries = Array();
			foreach($variation_assoc_data as $variation_association) {
				$variation_ids[] = $variation_association['variation_id'];
			}
			$j=0;
      foreach((array)$variation_assoc_data as $variation_association) {
        $i = 0;
        if($no_div !== true) {
			
				}
				if($j==0) {
					$default_topping='checked="checked"';
				} else {
					$default_topping='';
				}
				$j++;
        $variation_id = $variation_association['variation_id'];
        $value_assoc_sql = "SELECT * FROM `".$wpdb->prefix."variation_values_associations` WHERE `product_id` IN ('$product_id') AND `variation_id` IN ('$variation_id') AND `visible` IN ('1')";
        $value_assoc_data = $wpdb->get_results($value_assoc_sql,ARRAY_A);
        $variation_data_sql = "SELECT * FROM `".$wpdb->prefix."product_variations`  WHERE `id` IN ('$variation_id') LIMIT 1";
        $variation_data = $wpdb->get_results($variation_data_sql,ARRAY_A);
        $variation_data = $variation_data[0];
        if($no_label !== true) {
          $output .= "<label for='variation_select_".$product_id."_".$variation_data['id']."'>". $variation_data['name'] . ":</label> ";
				}
        if(($update_price === true) && (count($variation_ids) >= 1) && (count($variation_ids) <= 2)) {
          $special = 'false';
          if($no_label == true) {
            $special = 'true';
					}
          $on_change = "onchange='change_variation($product_id, Array(\"".implode("\",\"", $variation_ids)."\"), $special)'";
	      } else { $on_change = ''; }
				$special_prefix = '';
				if($no_label == true) {
					$special_prefix = 'special_';
				}
				if (get_option("checkbox_variations")=='1') {
					$output .= "<br>";
				} else {
					$output .= "<select id='".$special_prefix."variation_select_".$product_id."_".$variation_data['id']."' name='variation[".$variation_data['id']."]' $on_change >";
				}
				foreach((array)$value_assoc_data as $value_association) {
          if($i == 0) { 
						$first_entries[] = $value_association['value_id']; 
					}
          $value_id = $value_association['value_id'];
          $value_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."variation_values` WHERE `id` = '$value_id' ORDER BY `id` ASC",ARRAY_A);
          $check_stock = false;
          if(($product_data['quantity_limited'] == 1) && (count($variation_assoc_data) == 1)) {
            $variation_stock_data = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."variation_priceandstock` WHERE `product_id` = '".$product_id."' AND (`variation_id_1` = '".$value_data['id']."' AND `variation_id_2` = '0') LIMIT 1",ARRAY_A);
            $check_stock = true;
            $stock = $variation_stock_data['stock'];
					}
		
					if (get_option('checkbox_variations')==1) {
					$output .= "<input type='checkbox' id='variation[".$value_data['id']."]' name='variation[".$variation_data['name']."][]'".$default_topping." value='".$value_data['id']."' onclick='manage_topping(".$product_id.",".$value_data['id'].",".$special.")'>".$value_data['name']."<br>";
					//exit("'onclick='add_toping(".$product_id.", ".$value_data['id'].")'>");
					} else {
						if(($check_stock == true) && ($stock < 1)) {
							$output .= "<option value='".$value_data['id']."' disabled='true'>".$value_data['name']." - ".TXT_WPSC_NO_STOCK."</option>";
						} else {
							$output .= "<option value='".$value_data['id']."'>".$value_data['name']."</option>";
						}
					}
					$i++;
				}
				if (get_option("checkbox_variations")=='1') {
					$output .= "";
				} else {
					$output .= "</select>";
				}
				if($no_div !== true) {
		
				}
			}
		}
    
	if($update_price === true) {
		$first_entry_count = count($first_entries);
		if(($first_entry_count >= 1) && ($first_entry_count <= 2)) {
			$price = calculate_product_price($product_id, $first_entries);
			} else { $price = null; }
			return Array($output, $price);
		} else {
			return $output;
		}
	}
    
	function variations_add_grid_view($variations) {
		global $wpdb;
		$variation_count = count($variations);
		if(!(($variation_count >= 1) && ($variation_count <= 2))) {
			return "";
			exit();
		}
	
		switch($variation_count) {
			case 1:
			$variation_name = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$variations[0]."' ORDER BY `id` ASC LIMIT 1");	
			$grid_header = str_replace(":variation1:",$variation_name ,TXT_WPSC_VARIATION_GRID_CONTROL_SINGLE);
			break;
			
			case 2:
			$variation_name1 = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$variations[0]."' ORDER BY `id` ASC LIMIT 1");
			$variation_name2 = $wpdb->get_var("SELECT `name` FROM `".$wpdb->prefix."product_variations` WHERE `id` = '".$variations[1]."' ORDER BY `id` ASC LIMIT 1");			
			$grid_header = str_replace(Array(":variation1:", ":variation2:"),Array($variation_name1, $variation_name2) ,TXT_WPSC_VARIATION_GRID_CONTROL_PAIR);
			break;
		}
		
		$output .= "<table class='product_variation_grid'>\n\r";   
		$output .= "  <tr>\n\r";
		$output .= "    <th colspan='3' class='variation_name'>".$grid_header."</th>\n\r";
		$output .= "  </tr>\n\r";
		$output .= "  <tr>\n\r";
		$output .= "    <td><strong>".TXT_WPSC_VARIATION."</strong></td>\n\r";
		$output .= "    <td><strong>".TXT_WPSC_STOCK."</strong></td>\n\r";
		$output .= "    <td><strong>".TXT_WPSC_PRICE."</strong></td>\n\r";
		/*
		if(count($associated_variations) == 1) {
			$output .= "    <td><strong>".TXT_WPSC_ASSOCIATEWITHFILE."</strong></td>\n\r";
		}
		*/
		$output .= "  </tr>\n\r";
		if(count($variations) == 2) {
			foreach($variations as $key => $variation) {
				$associated_variation_values[$key] = $wpdb->get_results("SELECT `{$wpdb->prefix}variation_values`.* FROM `{$wpdb->prefix}variation_values` WHERE `variation_id` = '{$variation}'",ARRAY_A);
			}
			
			foreach((array)$associated_variation_values[0] as $associated_variation_value[0]) {
				foreach((array)$associated_variation_values[1] as $associated_variation_value[1]) {
					$output .= "  <tr>\n\r";
					$output .= "    <td>".str_replace(" ", "&nbsp;", (stripslashes($associated_variation_value[0]['name']).", ".stripslashes($associated_variation_value[1]['name'])))."</td>\n\r";
					$output .= "    <td><input type='text' name='variation_priceandstock[{$associated_variation_value[0]['id']}][{$associated_variation_value[1]['id']}][stock]' value='' size='3' /></td>\n\r";
					$output .= "    <td><input type='text' name='variation_priceandstock[{$associated_variation_value[0]['id']}][{$associated_variation_value[1]['id']}][price]' value='' size='6' /></td>\n\r";
					$output .= "  </tr>\n\r";
				}
			}
		} else {
			$variation = $variations[0];
			$associated_variation_values = $wpdb->get_results("SELECT `{$wpdb->prefix}variation_values`.* FROM `{$wpdb->prefix}variation_values` WHERE `variation_id` = '{$variation}'",ARRAY_A);
			
			foreach((array)$associated_variation_values as $associated_variation_value) {
				$output .= "  <tr>\n\r";
				$output .= "    <td>".str_replace(" ", "&nbsp;", stripslashes($associated_variation_value['name']))."</td>\n\r";
				$variation_info = $wpdb->get_row("SELECT id, `stock`,`price`, file FROM `{$wpdb->prefix}variation_priceandstock` WHERE `product_id` = '{$product_id}' AND (`variation_id_1` = '{$associated_variation_value['id']}' AND `variation_id_2` = '0') LIMIT 1",ARRAY_A);
				$output .= "    <td><input type='text' name='variation_priceandstock[{$associated_variation_value['id']}][0][stock]' value='{$variation_info['stock']}' size='3' /></td>\n\r";
				$output .= "    <td><input type='text' name='variation_priceandstock[{$associated_variation_value['id']}][0][price]' value='' size='6' /></td>\n\r";
				$output .= "  </tr>\n\r";
			}
		}
		
		$output .= "</table>\n\r";    
		return $output;
	}
  function update_variation_values($product_id, $variation_values) {
    global $wpdb;
    $product_id = (int)$product_id;
		foreach($variation_values as $key[0] => $variation_row) {
			$key[0] = (int)$key[0];
			foreach($variation_row as $key[1] => $variation_data) {
			  // sanitise the input
			  $key[1] = (int)$key[1];
				$variation_price = (float)str_replace(",","",$variation_data['price']);
				$variation_stock =(int)$variation_data['stock']; // having 1.2 stock makes no sense unless dealing with different units 
				$variation_file =(int)(bool)$variation_data['file'];		
				
				if(is_numeric($key[0]) && is_numeric($key[1]) && (is_numeric($variation_data['stock']) || is_numeric($variation_data['price']))) {
					$variation_stock_data = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}variation_priceandstock` WHERE `product_id` = '{$product_id}' AND ((`variation_id_1` = '{$key[0]}' AND `variation_id_2` = '{$key[1]}') OR (`variation_id_1` = '{$key[1]}' AND `variation_id_2` = '{$key[0]}')) LIMIT 1",ARRAY_A);
					if(is_numeric($variation_stock_data['id'])) {
						$variation_sql = null; // return the sql array to null for each trip round the loop
						if(($variation_stock_data['stock'] != $variation_stock)) {
							$variation_sql[] = "`stock` = '{$variation_stock}'";
						}
						
						if(($variation_stock_data['price'] != $variation_price)) {
							$variation_sql[] = "`price` = '{$variation_price}'";
						}
						
						if(($variation_stock_data['file'] != $variation_file)) {
							$variation_sql[] = "`file` = '{$variation_file}'";
						}
						
						if($variation_sql != null){
							$wpdb->query("UPDATE `".$wpdb->prefix."variation_priceandstock` SET ".implode(",",$variation_sql)."WHERE `id` = '".$variation_stock_data['id']."' LIMIT 1 ;");
						}
					}	else	{
						$wpdb->query("INSERT INTO `{$wpdb->prefix}variation_priceandstock` ( `product_id` , `variation_id_1` , `variation_id_2` , `stock`, `price` ) VALUES ('{$product_id}', '{$key[0]}', '{$key[1]}', '{$variation_stock}', '{$variation_price}');");
					}
				}
			}
		}
  }   
}
?>