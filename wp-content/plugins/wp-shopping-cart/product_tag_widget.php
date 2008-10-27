<?php
include_once('tagging_functions.php');

function widget_product_tag($args)
  {
  global $wpdb, $table_prefix;
  extract($args);
  //$options = get_option('widget_wp_shopping_cart');
  $title = empty($options['title']) ? __(TXT_WPSC_PRODUCT_TAGS) : $options['title'];
  echo $before_widget; 
  $full_title = $before_title . $title . $after_title;
  echo $full_title;
  product_tag_cloud();
  echo $after_widget;
  }

function widget_product_tag_control() { return null; }

 function widget_product_tag_init()
   {
   if(function_exists('register_sidebar_widget'))
    {
    register_sidebar_widget('Product tags', 'widget_product_tag');
    #register_widget_control('Product', 'widget_product_tag', 300, 90);
    }
   return;
   }
add_action('plugins_loaded', 'widget_product_tag_init');
?>