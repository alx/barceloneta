<?php

function widget_price_range($args)
{
	global $wpdb, $table_prefix;
	extract($args);
	$title = empty($options['title']) ? __(TXT_WPSC_PRICE_RANGE) : $options['title'];
	echo $before_widget."";
	$full_title = $before_title . $title . $after_title;
	echo $full_title."<br>";
	nzshpcrt_price_range();
	echo $after_widget;
}

function widget_price_range_control() { return null; }

 function widget_price_range_init()
 {
	 if(function_exists('register_sidebar_widget'))
	 {
		 register_sidebar_widget(TXT_WPSC_PRICE_RANGE, 'widget_price_range');
#register_widget_control('Product', 'widget_product_tag', 300, 90);
	 }
	 return;
 }
 
function nzshpcrt_price_range($input = null) {
	global $wpdb;
	$siteurl = get_option('siteurl');
	$product_page=get_option("product_list_url");
// 	if (get_option('permalink_structure')!=''){
// 		$seperater='?';
// 	} else {
// 		$seperater='&';
// 	}
	if (stristr($product_page,"?")) {
		$seperater='&';
	} else {
		$seperater='?';
	}
	$result = $wpdb->get_results("SELECT DISTINCT price FROM ".$wpdb->prefix."product_list ORDER BY price ASC",ARRAY_A);
	sort($result);
	$count = count($result);
	$price_seperater = ceil($count/6);
	for($i=0;$i<$count;$i+=$price_seperater) {
		$ranges[]=round($result[$i]['price'],-1);
	}
	$ranges = array_unique($ranges);
	
	$final_count = count($ranges);
	$ranges = array_merge(array(), $ranges);
	$_SESSION['price_range']=$ranges;
	for($i=0;$i<$final_count;$i++) {
		$j=$i+1;
		if ($i==$final_count-1) {
			echo "<a href='".$product_page.$seperater."range=".$j."'>Over ".$ranges[$i]."</a><br>";
		} else if($ranges[$i]==0){ 
			echo "<a href='".$product_page.$seperater."range=".$j."'>Under ".$ranges[$i+1]."</a><br>";
		}else {
			echo "<a href='".$product_page.$seperater."range=".$j."'>".$ranges[$i]." - ".$ranges[$i+1]."</a><br>";
		}
	}
	echo "<a href='".get_option("product_list_url")."'>".TXT_WPSC_SHOWALL."</a><br>";
	//exit("<pre>".print_r($ranges,1)."</pre>");
}
 add_action('plugins_loaded', 'widget_price_range_init');
 ?>