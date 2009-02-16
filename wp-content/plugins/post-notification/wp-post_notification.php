<?php
#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the post_notification.php for
# details.
#------------------------------------------------------



//********************************************************//
//                      START UP
//********************************************************//

define('WP_USE_THEMES', false);
require_once('./wp-blog-header.php');

get_header();
echo '<div id="content" class="narrowcolumn"><div class="post">';

require_once(POST_NOTIFICATION_PATH  . 'frontend.php'); //load FE
post_notification_fe(); //run FE

//********************************************************//
//                     SHUT DOWN
//********************************************************//

echo '</div></div>';
//include Sidebar
get_sidebar(); 
//Include the WP footer
get_footer();


?>