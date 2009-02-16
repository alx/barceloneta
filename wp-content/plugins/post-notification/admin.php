<?php  

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

function post_notification_admin_page_load(){
	//for future use;
	//global $editing;
	//$editing = true;
}

function post_notification_admin_page(){  
	//Ok, now we'll need I18N
	load_plugin_textdomain('post_notification', POST_NOTIFICATION_PATH_REL);

?>

<div class="wrap"> 

	<h2>Post Notification</h2>

	<?php
	//********************************************************//
	// Secure the admin area
	//********************************************************//
	
	global $user_level;
	get_currentuserinfo();
	if(get_option('db_version')< 4772){
		if ($user_level < 8) {
			echo __("You need at least Userlevel 8 to modify the settings.", 'post_notification');
			return;
		}
	} else {
		global $current_user;
		if(!$current_user->has_cap('administrator')){
			echo __("You need to be administrator to modify the settings.", 'post_notification');
			return;
		}
	}

	?>
	
	<div id="menu">
		<a href="admin.php?page=post_notification/admin.php"> <?php _e('Info', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=manage"> <?php _e('Manage addresses', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=export"><?php _e('Export addresses', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=list_email"><?php _e('List addresses', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=remove_email"><?php _e('Delete addresses', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=settings"><?php _e('Settings', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=instructions"><?php _e('Instructions', 'post_notification') ?></a> &middot;
		<a href="admin.php?page=post_notification/admin.php&amp;action=test"><?php _e('Test', 'post_notification') ?></a> &middot; 
		<a href="admin.php?page=post_notification/admin.php&amp;action=changelog"><?php _e('Change log', 'post_notification') ?></a>
	</div>
	<br />
	
	<?php
	
	//********************************************************//
	//                  GET VARIABLES FROM URL
	//********************************************************//
		if(isset($_GET['action']))
			$action=$_GET['action'];
		else
			$action = '';
		
		
		//***************************************************//
		//             MANAGE EMAIL ADDRESSES - FORM
		//***************************************************//
		if ($action == 'manage'){ require_once (POST_NOTIFICATION_PATH  . 'admin_manage.php'); }
		
		
		//***************************************************//
		//             EXPORT EMAIL ADDRESSES
		//***************************************************//
		elseif ($action == 'export'){ require_once (POST_NOTIFICATION_PATH  . 'admin_export.php'); }
		
		//***************************************************//
		//             LIST OR REMOVE EMAIL ADDRESSES
		//***************************************************//
		elseif ($action == 'list_email' or $action == 'remove_email'){ require_once (POST_NOTIFICATION_PATH  . 'admin_list_email.php'); }
		
		//***************************************************//
		//             EMAIL LIST SUBSCRIBERS
		//***************************************************//
		elseif ($action == 'send_email'){ require_once (POST_NOTIFICATION_PATH  . 'admin_send_email.php'); }
		
		//***************************************************//
		//             UPDATE SETTINGS
		//***************************************************//
		elseif ($action == 'settings'){ require_once (POST_NOTIFICATION_PATH  . 'admin_settings.php'); }
		
		//***************************************************//
		//             Edit Templates
		//***************************************************//
		elseif ($action == 'templates'){ require_once (POST_NOTIFICATION_PATH  . 'admin_templates.php'); }
		
		//***************************************************//
		//             INSTRUCTIONS
		//***************************************************//
		elseif ($action == 'instructions'){ require_once (POST_NOTIFICATION_PATH  . 'admin_instructions.php'); }
		  
		//***************************************************//
		//             TEST
		//***************************************************//
		elseif ($action == 'test'){ require_once (POST_NOTIFICATION_PATH  . 'admin_test.php'); }
	
		//***************************************************//
		//             Change log
		//***************************************************//
		elseif ($action == 'changelog'){ require_once (POST_NOTIFICATION_PATH  . 'admin_changelog.php'); }
		
		
		else {
			require_once (POST_NOTIFICATION_PATH  . "admin_info.php");
		}
		
		if(function_exists('post_notification_admin_sub')) post_notification_admin_sub();
		
		echo  '</div>';
}


?>

