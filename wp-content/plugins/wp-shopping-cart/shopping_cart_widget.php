<?php
function widget_wp_shopping_cart($args) {
    extract($args);
    $options = get_option('widget_wp_shopping_cart');
      
		if(get_option('show_sliding_cart') == 1)	{
			if(is_numeric($_SESSION['slider_state']))	{
				if($_SESSION['slider_state'] == 0) { $collapser_image = 'plus.png'; } else { $collapser_image = 'minus.png'; }
				$fancy_collapser = "<a href='#' onclick='return shopping_cart_collapser()' id='fancy_collapser_link'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/$collapser_image' title='' alt='' id='fancy_collapser' /></a>";
			} else {
				if($_SESSION['nzshpcrt_cart'] == null) { $collapser_image = 'plus.png'; } else { $collapser_image = 'minus.png'; }
				$fancy_collapser = "<a href='#' onclick='return shopping_cart_collapser()' id='fancy_collapser_link'><img src='".get_option('siteurl')."/wp-content/plugins/wp-shopping-cart/images/$collapser_image' title='' alt='' id='fancy_collapser' /></a>";
			}
		} else {
			$fancy_collapser = "";
		}
      
      
    
    $title = empty($options['title']) ? __('Shopping Cart') : $options['title'];
    //$title .= $fancy_collapser;
    echo $before_widget;
    $full_title = $before_title . "Shopping Cart ". $fancy_collapser . $after_title;    
    echo $full_title;
    echo "<ul>\n\r";
    echo "  <li>\n\r";
    nzshpcrt_shopping_basket("", 4);
    echo "  </li>\n\r";
    echo "</ul>\n\r";
    echo $after_widget;
    }

function widget_wp_shopping_cart_control() {
        $options = $newoptions = get_option('widget_wp_shopping_cart');
        if ( $_POST["wp_shopping_cart-submit"] ) {
                $newoptions['title'] = strip_tags(stripslashes($_POST["wp_shopping_cart-title"]));
        }
        if ( $options != $newoptions ) {
                $options = $newoptions;
                update_option('widget_wp_shopping_cart', $options);
        }
        $title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
                        <p><label for="wp_shopping_cart-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="wp_shopping_cart-title" name="wp_shopping_cart-title" type="text" value="<?php echo $title; ?>" /></label></p>
                        <input type="hidden" id="wp_shopping_cart-submit" name="wp_shopping_cart-submit" value="1" />
<?php
}

 function widget_wp_shopping_cart_init()
   {
   if(function_exists('register_sidebar_widget'))
    {
    register_sidebar_widget('Shopping Cart', 'widget_wp_shopping_cart');
    $GLOBALS['wpsc_cart_widget'] = true;
    if(get_option('cart_location') == 1)
      {
      update_option('cart_location', 4);
      remove_action('wp_list_pages','nzshpcrt_shopping_basket');
      }
    #register_widget_control('Shopping Cart', 'widget_wp_shopping_cart_control', 300, 90);
    }
   return;
   }
?>