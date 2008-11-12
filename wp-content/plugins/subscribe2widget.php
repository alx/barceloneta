<?php
/*
Plugin Name: Subscribe2 widget
Description: Adds a sidebar widget for Subscribe2
Author: Matthew Robinson.
Version: 1.3
Author URI: http://www.prescriber.org.uk

Version History
	1.0 - Display form without any user feeback
	1.1 - Implemented user feedback and Subscribe2 check
	1.2 - Fixed Subscribe2 check ->required PHP4 and above
	1.3 - Fixed conflict with exec-PHP plugin
*/

function widget_subscribe2widget_init() {

//Check Sidebar Widget and Subscribe2 plugins are activated
if ( !function_exists('register_sidebar_widget') || !class_exists('s2class'))
	return;
		
function widget_subscribe2widget($args) {
	extract($args);
	$options = get_option('widget_subscribe2widget');
	$title = empty($options['title']) ? __('Subscribe2') : $options['title'];
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<div class="search">
			<?php
			$content = apply_filters('the_content', '<p><!--subscribe2--></p>');
			echo $content;
			?>
			</div>
		<?php echo $after_widget; ?>
<?php
}
function widget_subscribe2widget_control() {
	$options = $newoptions = get_option('widget_subscribe2widget');
	if ( $_POST["s2w-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["s2w-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_subscribe2widget', $options);
	}
	$title = htmlspecialchars($options['title'], ENT_QUOTES);
?>
			<p><label for="s2w-title"><?php _e('Title:'); ?> <input style="width: 250px;" id="s2w-title" name="s2w-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="s2w-submit" name="s2w-submit" value="1" />
<?php
}

	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget('Subscribe2Widget', 'widget_subscribe2widget');

	// This registers our optional widget control form.
	register_widget_control('Subscribe2Widget', 'widget_subscribe2widget_control');
}

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_subscribe2widget_init');
?>