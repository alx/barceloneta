<?php


function post_notification_admin_sub(){
	echo '<h3>' . __('Edit templates', 'post_notification') . '</h3>';
	
	
	$dir_handle=opendir(POST_NOTIFICATION_PATH);
	while (false !== ($dir = readdir($dir_handle))) {
		if(is_dir(POST_NOTIFICATION_PATH . $dir) && $dir[0] != '.' && $dir[0] != '_') {
			
				echo "<p><b>{$dir}</b><ul>";
				$file_handle=opendir(POST_NOTIFICATION_PATH  .'/' .$dir);
				while (false !== ($file = readdir($file_handle))) {
					if( $file[0] == '.' || $file[0] == '..') continue;
					echo "<li><a href=\"templates.php?file=". POST_NOTIFICATION_PATH_REL ."/$dir/$file\">$file</a></li>";	
				}
				echo "</ul></p>";			
			
		}
	}
	closedir($dir_handle); 
	
	
}	
?>
