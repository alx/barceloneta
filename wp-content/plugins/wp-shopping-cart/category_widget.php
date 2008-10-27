<?php
function widget_checkout($args)
  {
  global $wpdb, $table_prefix;
  extract($args);
  //$options = get_option('widget_wp_shopping_cart');
  $title = empty($options['title']) ? __(TXT_WPSC_CATSANDBRAND) : $options['title'];
  echo $before_widget; 
  $full_title = $before_title . $title . $after_title;
  echo $full_title;
  show_cats_brands('sidebar');
  echo $after_widget;
  }

function widget_checkout_control() { return null; }

 function widget_checkout_init()
   {
   if(function_exists('register_sidebar_widget'))
    {
    register_sidebar_widget('Brands and Categories', 'widget_checkout');
    #register_widget_control('Brands and Categories', 'widget_checkout_control', 300, 90);
    }
   return;
   }
add_action('plugins_loaded', 'widget_checkout_init');
?>