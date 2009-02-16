<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------


function post_notification_is_file($path, $file){
	if(!is_file($path . '/' . $file)){
		echo '<div class="error">'. __('File missing in profile folder.', 'post_notification') . '<br />';
		echo __('Folder', 'post_notification') . ': <b>' . $path . '</b><br />';
		echo __('File', 'post_notification') . ': <b>' . $file. '</b></div>';
		return false;
	}
	return true;
}

function post_notification_check_string($path, $string){
	require($path . '/strings.php');
	if(!array_key_exists($string, $post_notification_strings)){
		echo '<div class="error">'. __('Missing string in string file.', 'post_notification') .'<br />';
		echo __('File', 'post_notification') . ': <b>' . $path . '/strings.php </b><br />';
		echo __('String', 'post_notification') . ': <b>' . $string . '</b></div>';
		return false;
	}
	return true;
}

function post_notification_is_profile($path){
	if(!(
		post_notification_is_file($path , 'confirm.tmpl') &&
		post_notification_is_file($path , 'reg_success.tmpl') &&
		post_notification_is_file($path , 'select.tmpl') &&
		post_notification_is_file($path , 'subscribe.tmpl') &&
		post_notification_is_file($path , 'unsubscribe.tmpl') &&
		post_notification_is_file($path , 'strings.php'))) return false;
	
	if(!(
		post_notification_check_string($path, 'error') &&
		post_notification_check_string($path, 'already_subscribed') &&
		post_notification_check_string($path, 'activation_faild') &&
		post_notification_check_string($path, 'address_not_in_database') &&
		post_notification_check_string($path, 'sign_up_again') &&
		post_notification_check_string($path, 'deaktivated') &&
		post_notification_check_string($path, 'no_longer_activated') &&
		post_notification_check_string($path, 'check_email') &&
		post_notification_check_string($path, 'wrong_captcha') &&
		post_notification_check_string($path, 'all') &&
		post_notification_check_string($path, 'saved')
		)) return false;
	
	return true;

}

function post_notification_select($var, $comp){
	if(get_option('post_notification_' . $var) == $comp) return ' selected="selected" ';
	else return '';
}

function post_notification_select_yesno($var){
		echo '<select name="' . $var . '" >';
		echo '<option value="no" ' . post_notification_select( $var,'no') . ' >' .  __('No', 'post_notification') . '</option>';
		echo '<option value="yes" ' . post_notification_select($var,'yes') . ' >' .  __('Yes', 'post_notification') . '</option>';
		echo '</select>';
}

function post_notification_admin_sub(){
	echo  '<h3>' . __('Settings', 'post_notification') . '</h3>';
	

	if ($_POST['updateSettings']){
		
		if(!isset($_POST['the_content'])) $_POST['the_content'] = array();
		
		//simple things first
		update_option('post_notification_read_more', $_POST['read_more']);
		update_option('post_notification_show_content', $_POST['show_content']);
		update_option('post_notification_send_default', $_POST['send_default']);
		update_option('post_notification_send_private', $_POST['send_private']);
		update_option('post_notification_send_page', $_POST['send_page']);
		update_option('post_notification_subject', $_POST['subject']);
		update_option('post_notification_from_name', $_POST['from_name']);
		update_option('post_notification_from_email', $_POST['from_email']);
		update_option('post_notification_page_name', $_POST['page_name']);
		update_option('post_notification_url', $_POST['pn_url']);
		update_option('post_notification_page_meta', $_POST['page_meta']);
		update_option('post_notification_filter_include', $_POST['filter_include']);
		update_option('post_notification_uninstall',$_POST['uninstall']);
		update_option('post_notification_debug',$_POST['debug']);
		update_option('post_notification_lock', $_POST['lock']);
		update_option('post_notification_the_content_exclude', serialize($_POST['the_content']));
		update_option('post_notification_empty_cats', $_POST['empty_cats']);
		update_option('post_notification_show_cats', $_POST['show_cats']);
		update_option('post_notification_sendcheck', $_POST['sendcheck']);
		update_option('post_notification_saved_tmpl', $_POST['saved_tmpl']);
		
		$p_captcha = $_POST['captcha'];
		if(is_numeric($p_captcha)){
			if($p_captcha >= 0){
				update_option('post_notification_captcha', $p_captcha );
			} else {
				echo '<div class="error">' . __('Number of captcha-chars must be 0 or greater.', 'post_notification') . '</div>';
			}
		} else {
			echo '<div class="error">' . __('Number of captcha-chars must be a number.', 'post_notification') . '</div>';
		}
			
			
		
		$p_pause = $_POST['pause'];
		if(is_numeric($p_pause)){
			if($p_pause >= 0){
				update_option('post_notification_pause', $p_pause );
			} else {
				echo '<div class="error">' . __('Pause must be zero or greater.', 'post_notification') . '</div>';
			}
		} else {
			echo '<div class="error">' . __('Pause must be a number.', 'post_notification') . '</div>';
		}

		$p_nervous = $_POST['nervous'];
		if(is_numeric($p_nervous)){
			if($p_nervous >= 0){
				update_option('post_notification_nervous', $p_nervous);
			} else {
				echo '<div class="error">' . __('Nervous Finger must be zero or greater.', 'post_notification') . '</div>';
			}
		} else {
			echo '<div class="error">' . __('Nervous Finger must be a number.', 'post_notification') . '</div>';
		}


		
		$p_maxmail = $_POST['maxmails'];
		if(is_numeric($p_maxmail)){
			if($p_maxmail > 0){
				update_option('post_notification_maxsend', $p_maxmail );
			} else {
				echo '<div class="error">' . __('Number of mails must be greater then zero.', 'post_notification') .'</div>';
			}
		} else {
			echo '<div class="error">' . __('Number of mails must be a number', 'post_notification') . '</div>';
		}
		
		

		
		
		if($_POST['hdr_nl'] == "rn")
			update_option('post_notification_hdr_nl', "rn");
		else
			update_option('post_notification_hdr_nl', "n");
		
		
		
		// Check wheather the template exists in the Profile
		if( is_file(POST_NOTIFICATION_PATH . $_POST['en_profile'] .'/' . $_POST['en_template']) ||
			is_file(POST_NOTIFICATION_DATA . $_POST['en_profile'] .'/' . $_POST['en_template']) ){
			update_option('post_notification_profile', $_POST['en_profile']);
			update_option('post_notification_template', $_POST['en_template']);
		} else {
			// Don't save any Profile / Template-inforamtion so we don't get in to an inconsisten state;
			echo '<div class="error">' . __('Could not find the template in this profile. Please select a template and save again.', 'post_notification') . '</diV>';
			$profile = $_POST['en_profile'];
		}
		

 		// Update default categories
		$categories = $_POST['pn_cats'];
		if (empty($categories)) {
			update_option('post_notification_selected_cats', '');
		} else {
			$categoryList = '';
			foreach ($categories as $category) {
				if (is_numeric($category)) {
					$categoryList .= ',' . $category;
				}
			}
			update_option('post_notification_selected_cats', substr($categoryList, 1));
		}
		
			
		//Add the page
		
		if($_POST['add_page']=="add"){
			
			
			//Database change in 2.1
			if(get_option('db_version')< 4772){
				$post_status = "static";
			} else {
				$post_type = "page";
				$post_status = "publish";
			}

			
			//Collect the Data
			if(get_option('post_notification_filter_include') == 'no'){
				$post_title = $_POST['page_name'];
				$post_content = __('If you can read this, something went wrong. :-(', 'post_notification');
			} else {
				$post_title = '@@post_notification_header';
				$post_content = '@@post_notification_body';
			}
			$post_data = compact('post_content','post_title', 'post_status', 'post_type');
			$post_data = add_magic_quotes($post_data);
			
			//Post
			$post_ID = wp_insert_post($post_data);
			
			//Add meta if we are using the Template.
			if(get_option('post_notification_filter_include') == 'no'){
				add_post_meta($post_ID, '_wp_page_template',  'post_notification_template.php', true);
			}
			
			//Add the ID to the URL
			update_option('post_notification_url', $post_ID);
		}
		echo '<H4>' . __('Data was updated.', 'post_notification') . '</H4>';
	} 
	
	
	//Try to install the theme in case we need it. There be no warning. Warnings are only on the info-page.
	post_notification_installtheme();
	
	$selected = 'selected="selected"';
	
	/**
	 * @todo Move all this stuff down to where it is displayed,
	 * 	having all this stuff up here was a good Idea while there were few settings.
	 */
	
	
	if(get_option('post_notification_hdr_nl') == 'rn')
		$hdr_rn = $selected;
	else
		$hdr_n = $selected;

	if(get_option('post_notification_show_content') == 'no')
		$contentN = $selected;
	else
		$contentY = $selected;
		
	if(get_option('post_notification_send_default') == 'no')
		$sendN = $selected;
	else
		$sendY = $selected;
		
	if(get_option('post_notification_send_private') == 'no')
		$privateN = $selected;
	else
		$privateY = $selected;
		
	if(get_option('post_notification_send_page') == 'no')
		$pageN = $selected;
	else
		$pageY = $selected;
		
		
	if(get_option('post_notification_page_meta') == 'no')
		$metaN = $selected;
	else 
		$metaY = $selected;
	
	if(get_option('post_notification_filter_include') == 'no')
		$filter_includeN = $selected;
	else 
		$filter_includeY = $selected;	
	
	
	
	
	if(get_option('post_notification_uninstall') == 'yes') //rather have No
		$uninstallY = $selected;
	else 
		$uninstallN = $selected;	
	
	
	//Find Profiles
	if(!isset($profile)) //If the profile is already set, dont change.
		$profile = get_option('post_notification_profile');
	
	 $profile_list = array();
	
	if(file_exists(POST_NOTIFICATION_DATA)){
		$dir_handle=opendir(POST_NOTIFICATION_DATA);
		while (false !== ($file = readdir ($dir_handle))) {
			if(is_dir(POST_NOTIFICATION_DATA . $file) && $file[0] != '.' && $file[0] != '_') {
				if(post_notification_is_profile(POST_NOTIFICATION_DATA . $file)){
					$profile_list[] = $file;
				}
			}
		}
		closedir($dir_handle);
	} else {
		echo '<div class = "error">' . __('Please save own Profiles in: ', 'post_notification') .' '. POST_NOTIFICATION_DATA . '<br/>';
		echo __('Otherwise they may be deleted using autoupdate. ', 'post_notification') . '</div>';
		
	}
	
		
	$dir_handle=opendir(POST_NOTIFICATION_PATH);
	while (false !== ($file = readdir ($dir_handle))) {
		if(is_dir(POST_NOTIFICATION_PATH . $file) && $file[0] != '.' && $file[0] != '_') {
			if(post_notification_is_profile(POST_NOTIFICATION_PATH . $file)){
				if(!in_array($file, $profile_list)) $profile_list[] = $file;
			}
		}
	}
	closedir($dir_handle); 
	foreach($profile_list as $profile_list_el){
		$en_profiles .= "<option value=\"$profile_list_el\" ";
		if($profile_list_el == $profile) $en_profiles .= ' selected="selected"';
		$en_profiles .= ">$profile_list_el</option>";
	}
	
	///Find templates
	$template = get_option('post_notification_template');
	$dir_handle=opendir( post_notification_get_profile_dir($profile ));
	while (false !== ($file = readdir ($dir_handle))) {
		if(substr($file, -5) == '.html' or substr($file, -4) == '.txt') {
			$en_templates .= "<option value=\"$file\" ";
			if($file == $template) $en_templates .= ' selected="selected"';
			$en_templates .= ">$file</option>";
		}
	}
	closedir($dir_handle); 

	?>
<form id="update" method="post" action="admin.php?page=post_notification/admin.php&amp;action=settings">
<h4> <?php  _e('When to send', 'post_notification');  ?></h4>
<table width="100%">
	



	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Send normal posts by default:', 'post_notification'); ?></th>
		<td>
	        <select name="send_default" >
	          <option value="no"  <?php  echo $sendN; ?> > <?php  _e('No', 'post_notification'); ?></option>
	          <option value="yes" <?php  echo $sendY; ?> > <?php  _e('Yes', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Send private posts by default:', 'post_notification'); ?></th>
		<td><select name="send_private">
			<option value="no"  <?php  echo $privateN; ?>><?php  _e('No', 'post_notification'); ?></option>
			<option value="yes" <?php  echo $privateY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
		</select></td>
	</tr>
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Send pages by default:', 'post_notification'); ?></th>
		<td>
			<select name="send_page">
				<option value="no"  <?php  echo $pageN; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes" <?php  echo $pageY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
			</select>
		</td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Note:', 'post_notification'); ?></th> 
		<td>
			<?php echo '<b>' . __('You can always override the settings above when writing a post. There is a Post Notification box somewhere near the upload box when writing or editing a post.', 'post_notification') . '</b>'; ?>
		</td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Nervous finger wait:', 'post_notification'); ?></th>
		<td>
	        <input name="nervous" type="text" id="nervous" size="35" value="<?php  echo get_option('post_notification_nervous'); ?>" />	<?php _e('seconds.', 'post_notification'); ?>
		</td>
	</tr>
	<tr class="alternate">
		<td /> 
		<td>
			<?php _e('This option sets the time to wait before sending an Email. So if you have an nervous finger you can unpublish your post quickly and no mails are sent.', 'post_notification'); ?>
		</td>
	</tr>
	
</table>
	<h4> <?php  _e('Look', 'post_notification');  ?></h4>
<table width="100%">
	
		<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Copy complete post in to the mail:', 'post_notification') ?></th>
		<td>
	        <select name="show_content" >
				<option value="no"      <?php  if(get_option('post_notification_show_content') == 'no') echo $selected; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes"     <?php  if(get_option('post_notification_show_content') == 'yes') echo $selected; ?>><?php  _e('Yes', 'post_notification'); ?></option>
				<option value="more"    <?php  if(get_option('post_notification_show_content') == 'more') echo $selected; ?>><?php  _e('Up to the more-tag.', 'post_notification'); ?></option>
				<option value="excerpt"	<?php  if(get_option('post_notification_show_content') == 'excerpt') echo $selected; ?>><?php  _e('The excerpt', 'post_notification'); ?></option>
		</select>	
		</td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Read-more-text:', 'post_notification'); ?></th>
		<td><input name="read_more" type="text" size="35" value="<?php  echo get_option('post_notification_read_more'); ?>" /></td>
	</tr>
	<tr class="alternate">
		<td />
		<td><?php _e('This text is put behind the content in case the mail is truncated. E.g. because of the more-tag.', 'post_notification'); ?>
		</td>
	</tr>
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Profile:', 'post_notification'); ?></th>
		<td>
	        <select name="en_profile" >
				<?php  echo $en_profiles; ?>
	        </select>	
		</td>
	</tr>
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Template:', 'post_notification'); ?></th>
		<td>
	        <select name="en_template" >
				<?php  echo $en_templates; ?>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<td /> 
		<td>
			<?php _e('Templates with the extension .txt are sent as text-mails. Templates with the extension .html are sent as HTML-mails', 'post_notification'); ?>
		</td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Subject:', 'post_notification'); ?></th>
		<td><input name="subject" type="text" size="35" value="<?php  echo get_option('post_notification_subject'); ?>" /></td>
	</tr>
	<tr class="alternate">
		<td />
		<td><?php _e('Use @@blogname as placeholder for the blog name.', 'post_notification'); ?>
			<?php _e('Use @@title as placeholder for the title of the post.', 'post_notification'); ?>
		</td>
	</tr>
	
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Sender-Name:', 'post_notification'); ?></th>
		<td><input name="from_name" type="text" size="35" value="<?php  echo get_option('post_notification_from_name'); ?>" /></td>
	</tr>
	<tr class="alternate">
		<td />
		<td><?php _e('Use @@blogname as placeholder for the blog name.', 'post_notification'); ?>
		</td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Sender-Email:', 'post_notification'); ?></th>
		<td><input name="from_email" type="text"  size="35" value="<?php  echo get_option('post_notification_from_email'); ?>" /></td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Show "saved.tmpl" when saving frontend settings.', 'post_notification'); ?></th>
		<?php
		if(get_option('post_notification_saved_tmpl') == 'yes'){
			$savedTmplY = $selected;
		} else {
			$savedTmplN = $selected;
		}
		?>
		<td>
			<select name="saved_tmpl">
				<option value="no"  <?php  echo $savedTmplN; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes" <?php  echo $savedTmplY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
			</select>
		</td>
	</tr>
	
</table>







	<h4> <?php  _e('Technical', 'post_notification');  ?></h4>
<table width="100%">	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Number of mails to be sent in a burst:', 'post_notification'); ?></th>
		<td><input name="maxmails" type="text" id="maxmail" size="35" value="<?php  echo get_option('post_notification_maxsend'); ?>" /></td>
	</tr>
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Pause between transmission:', 'post_notification'); ?></th>
		<td><input name="pause" type="text" id="pause" size="35" value="<?php  echo get_option('post_notification_pause'); ?>" /> <?php _e('seconds.', 'post_notification'); ?></td>
	</tr>
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Type of header line break:', 'post_notification'); ?></th>
		<td>
	        <select name="hdr_nl" >
	          <option value="rn" <?php  echo $hdr_rn; ?>>\r\n</option>
	          <option value="n"  <?php  echo $hdr_n; ?>>\n</option>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<td /> 
		<td>
			<?php _e('According to the PHP-specification \r\n must be used. Never the less quite a few servers have trouble if they get a \r\n instead of \n. You\'ll see part of the header in your mail if you have the wrong selection.', 'post_notification') ?>
		</td>
	</tr>

		<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Locking:', 'post_notification') ?></th>
		<?php
			if(get_option('post_notification_lock') == 'db')
				$lockDB= $selected;
			else 
				$lockFILE = $selected;	
		?>
		<td>
	        <select name="lock" >
	         	<option value="file"  <?php  echo $lockFILE; ?>><?php  _e('File', 'post_notification'); ?></option>
				<option value="db" <?php  echo $lockDB; ?>><?php  _e('Database', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<td />
		<td>
			<?php 	_e('Try using database locking if you are geting duplicate messages.', 'post_notification') ;
				echo  ' ' . '<a href="http://php.net/manual/function.flock.php">' . __('More information.', 'post_notification') . '</a>';
				?>
		</td>
	</tr>
	
	
	
	
	<tr class="alternate">

		<th style="text-align:right;padding-right:10px;"><?php _e('Filters to exclude from filtering "the_content":', 'post_notification') ?></th>

		<td>
			<?php 
				global $wp_filter;
				$rem_filters = get_option('post_notification_the_content_exclude');
				if(is_string($rem_filters) && strlen($rem_filters)){
					$rem_filters = unserialize($rem_filters);
				}
				if(!is_array($rem_filters)){
					$rem_filters = array();
				}
				
				foreach($wp_filter['the_content']  as $filter_level => $filters_in_level ){
					foreach($filters_in_level as $filter){
						if(function_exists('_wp_filter_build_unique_id')){
							// If a function is passed the unique_id will return the function name. 
							// Therefore there should be no problem with backward compatibilty
							// priority may/must be false as all functions should get an Id when being registered
							// As prio = false, $tag is not needed at all!
							$fn_name = _wp_filter_build_unique_id('the_content', $filter['function'], $filter_level);
						} else {
							$fn_name = $filter['function'];
						}
						if(!($fn_name === false)){
							echo '<input type="checkbox"  name="the_content[]" value="' .  $fn_name . '" ';
							if(in_array($fn_name, $rem_filters)) echo ' checked="checked" ';
							
							echo '>' .  $fn_name . '</input><br />';
						}
					}
				}
			?>
		</td>	
	</tr>
	
		<tr class="alternate">
		<td />
		<td>
			<?php 
				_e('Some plugins use filters to modify the content of a post. You might not want some of them modifying your mails. Finding the right filters might need some playing around.', 'post_notification') ;
				
				?>
		</td>
	</tr>
	
	
	</tr>

		<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('When to send:', 'post_notification') ?></th>
		<?php
			if(get_option('post_notification_sendcheck') == 'head') {
				$sendhead= $selected;
			} else if(get_option('post_notification_sendcheck') == 'footer'){ 
				$sendfoot = $selected;	
			} else {
				$sendshutdown = $selected;
			}
		?>
		<td>
	        <select name="sendcheck" >
	         	<option value="head"  <?php  echo $sendhead; ?>><?php  _e('Header', 'post_notification'); ?></option>
				<option value="footer" <?php  echo $sendfoot; ?>><?php  _e('Footer', 'post_notification'); ?></option>
				<option value="shutdown" <?php  echo $sendshutdown; ?>><?php  _e('Shutdown', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<td />
		<td>
			<?php 	_e('By default PN sends mails after the page has been rendered and sent to the user (shutdown).' .
					' Some hosters kill all scripts after the connection has been closed. ' .
					'You can try sending mails before the page is rendered (header) or before creating the footer of the ' .
					'page (footer).', 'post_notification') ;
				
				?>
		</td>
	</tr>
	
	
	
	

</table>









	<h4> <?php  _e('Frontend', 'post_notification');  ?></h4>
<table width="100%">

	

	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Name of the Post Notification page:', 'post_notification'); ?></th>
		<td><input name="page_name" type="text" id="page_name" size="60" value="<?php echo get_option('post_notification_page_name'); ?>" /></td>
	</tr>

	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Allow selection of categories:', 'post_notification'); ?></th>
		<td>
			<?php post_notification_select_yesno('show_cats'); ?>	
		</td>
	</tr>


	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Show empty categories:', 'post_notification'); ?></th>
		<td>
			<?php post_notification_select_yesno('empty_cats'); ?>	
		</td>
	</tr>






	<?
	$selected_cats_list = get_option('post_notification_selected_cats');
 	$selected_cats = explode(',', $selected_cats_list);
 	?>
	<tr class="alternate">	
 		<th style="text-align:right;padding-right:10px;"><?php _e('Default categories:', 'post_notification'); ?></th>
 		<td><?php echo post_notification_get_catselect('', $selected_cats); ?></td>
 	</tr>
 	<tr class="alternate">
 		<td />
 		<td><?php _e('The categories which will be automatically selected when a user subscribes, and which is also default for the Manage Addresses dialog. Choosing a category includes all subcategories.', 'post_notification'); ?>
 		</td>
 	</tr>
 
	

	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;">
		<a href="<?php _e('http://en.wikipedia.org/wiki/Captcha', 'post_notification'); ?>"><?php _e('Captcha-chars:', 'post_notification'); ?></a></th>
		<td><input name="captcha" type="text" size="60" value="<?php  echo get_option('post_notification_captcha'); ?>" /></td>
	</tr>
	<tr class="alternate">
		<td />
		<td> 
			<?php _e('Number of Captcha-chars. 0 is no Captcha', 'post_notification'); ?><br />
			<b><?php _e('Warning:', 'post_notification'); ?></b>
			<?php _e('Your template must support Captchas.', 'post_notification'); ?>
		</td>
	</tr>
	

	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Post Notification link in the meta-section:', 'post_notification'); ?></th>
		<td>
	        <select name="page_meta" >
	         	<option value="no"  <?php  echo $metaN; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes" <?php  echo $metaY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>

	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Replacement in Posts:', 'post_notification'); ?></th>
		<td>
	        <select name="filter_include" >
	         	<option value="no"  <?php  echo $filter_includeN; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes" <?php  echo $filter_includeY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<td />
		<td> 
			<?php _e('The Stings @@post_notification_header and @@post_notification_body will be replaced in your post.', 'post_notification'); ?><br />
			<?php _e('Also see the Instructions for this.', 'post_notification'); ?>
		</td>
	</tr>
	
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Add Post Notification page:', 'post_notification'); ?></th>
		<td><input name="add_page" type="checkbox" id="add_page" value="add" /></td>
	</tr>
	
	
	<tr class="alternate">
		<td />
		<td>
			<?php _e('Adds a Post Notification page to your pages.', 'post_notification') . ' ';
				  _e('The file "post_notification_template.php" has been copied into the active theme. You may want to edit this file to fit your needs.  ', 'post_notification');?><br />
			<?php _e('This checkbox is cleared after execution.', 'post_notification');?><br />
			<?php _e('Also see the Instructions for this.', 'post_notification'); ?>
		</td>
	</tr>
	
	
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Link to the Post Notification page:', 'post_notification'); ?></th>
		<td><input name="pn_url" type="text" id="pn_url" size="60" value="<?php  echo get_option('post_notification_url'); ?>" /></td>
	</tr>
	<tr class="alternate">
		<td />
		<td>
			<?php 	_e('This must be the URL or the ID of the page on which you subscribe.', 'post_notification') . ' ';
					_e('If you pick "Add Post Notification page" this will be compleated automaticly.', 'post_notification') . ' ';    
					_e('Also see the Instructions for this.', 'post_notification'); ?>
		</td>
	</tr>


</table>
	<h4> <?php  _e('Miscellaneous', 'post_notification');  ?></h4>
<table width="100%">


	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Debug:', 'post_notification') ?></th>
		<?php
			if(get_option('post_notification_debug') != 'yes')
				$debugN = $selected;
			else 
				$debugY = $selected;	
		?>
		<td>
	        <select name="debug" >
	         	<option value="no"  <?php  echo $debugN; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes" <?php  echo $debugY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Uninstall:', 'post_notification') ?></th>
		<td>
	        <select name="uninstall" >
	         	<option value="no"  <?php  echo $uninstallN; ?>><?php  _e('No', 'post_notification'); ?></option>
				<option value="yes" <?php  echo $uninstallY; ?>><?php  _e('Yes', 'post_notification'); ?></option>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<td />
		<td>
			<?php _e('WARNING: If this option is set, all database entries are deleted upon deactivation. Of course all data is lost.', 'post_notification'); ?>
		</td>
	</tr>	
	
	<tr class="alternate">
	<td>&nbsp;</td>
	<td><input type="submit" name="updateSettings" value="<?php _e('save', 'post_notification'); ?>" /></td>
	</tr>
</table>		
</form>	
<?php
}
?> 
