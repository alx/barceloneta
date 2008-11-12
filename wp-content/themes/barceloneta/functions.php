<?php
/* Register the dynamic sidebars
==================================================== */
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name'=>'Sidebar',
		'before_widget' => '<div id="%1$s" class="sidebar %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>',
	));
}

/* Replace the search widget with custom search form
==================================================== */
function widget_mytheme_search() {
	$filename = TEMPLATEPATH . '/searchform.php'; if (file_exists($filename)) { include($filename); }
}
if ( function_exists('register_sidebar_widget') ) {
	register_sidebar_widget(__('Search'), 'widget_mytheme_search');
}

/* Fix page navigation when posts are offset
==================================================== */
function my_post_limit($limit) {
	global $paged, $myOffset;
	if (empty($paged)) {
			$paged = 1;
	}
	$postperpage = intval(get_option('posts_per_page'));
	$pgstrt = ((intval($paged) -1) * $postperpage) + $myOffset . ', ';
	$limit = 'LIMIT '.$pgstrt.$postperpage;
	return $limit;
}

/* Separate Trackbacks/Pingbacks From Comments
==================================================== */
add_filter('comments_array', 'filterComments', 0); 
add_filter('get_comments_number', 'filterCommentsNumber');
//Updates the comment number for posts with trackbacks
function filterCommentsNumber($count) {
	global $id;
	if (empty($id)) { return $count; }
	$comments = get_approved_comments((int)$id);
	$comments = array_filter($comments, "stripTrackback");
	return sizeof($comments);
}
//Updates the count for comments and trackbacks
function filterComments($comms) {
	global $comments, $trackbacks;
	$comments = array_filter($comms,"stripTrackback");
	$trackbacks = array_filter($comms, "stripComment");
	return $comments;
}
//Strips out trackbacks/pingbacks
function stripTrackback($var) {
	if ($var->comment_type == 'trackback' || $var->comment_type == 'pingback') { return false; }
	return true;
}
//Strips out comments
function stripComment($var) {
	if ($var->comment_type != 'trackback' && $var->comment_type != 'pingback') { return false; }
	return true;
}
?>