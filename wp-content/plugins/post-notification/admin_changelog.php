<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

function post_notification_admin_sub(){

	echo '<h3>' .  __('Change log', 'post_notification') . '</h3>';
	echo '<p><a href = "http://dev.wp-plugins.org/browser/post-notification/branches/1.2/changelog.txt">';
		_e('Latest change log from the server.', 'post_notification');
	echo '</a></p><pre>';
	echo htmlspecialchars(file_get_contents(POST_NOTIFICATION_PATH . 'changelog.txt'));
	echo '</pre>';
}
?>
