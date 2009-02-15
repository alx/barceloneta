<?php
/*
Plugin Name: Json Output
Plugin URI: http://alexgirard.com
Description: Output the requested pge in JSON format. Permalink must be activated
Author: Alexandre Girard
Version: 1.0
Author URI: http://alexgirard.com
*/


function json_flush_rewrite_rules() 
{
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
}


function json_add_rewrite_rules( $wp_rewrite ) 
{
  $new_rules = array( 
     'json/(.+)' => 'index.php?json=' .
       $wp_rewrite->preg_index(1) );

  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

function json_queryvars( $qvars )
{
  $qvars[] = 'json';
  return $qvars;
}

add_action('init', 'json_flush_rewrite_rules');
add_action('generate_rewrite_rules', 'json_add_rewrite_rules');

add_filter('query_vars', 'json_queryvars' );
?>