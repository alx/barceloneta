<?php
function post_notification_admin_sub(){ 
	global $wpdb;
	require_once (POST_NOTIFICATION_PATH  . 'sendmail.php');
	
	echo '<h3>' . __('Test', 'post_notification') . '</h3>';

?>

<form id="test" method="post" action="admin.php?page=post_notification/admin.php&amp;action=test">
<table width="100%">
	
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Post id:', 'post_notification') ?></th>
		<td>
			<input name="pid" type="text" size="35" value="<?php  echo $_POST['pid'] ?>" />
		</td>
	</tr>
	<tr class="alternate">
		<td />
		<td>
			<?php _e('This must be the ID of the post you want to send. You can find the ID under Manage->Posts.', 'post_notification') ?>
		</td>
		
	</tr>
<?php
	if(($email = $_POST['email']) == '') $email = get_option('post_notification_from_email');
?>
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Email:', 'post_notification') ?></th>
		<td>
			<input name="email" type="text" size="35" value="<?php  echo $email ?>" />
		</td>
	</tr>
<?php
	///Find templates
	if(($template = $_POST['template']) == '') $template = get_option('post_notification_template');
	$dir_handle=opendir(post_notification_get_profile_dir());
	while (false !== ($file = readdir ($dir_handle))) {
		if(substr($file, -5) == '.html' or substr($file, -4) == '.txt') {
			$en_templates .= "<option value=\"$file\" ";
			if($file == $template) $en_templates .= ' selected="selected"';
			$en_templates .= ">$file</option>";
		}
	}
	closedir($dir_handle); 
?>
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Template:', 'post_notification'); ?></th>
		<td>
	        <select name="template" >
				<?php  echo $en_templates; ?>
	        </select>	
		</td>
	</tr>
	<tr class="alternate">
		<th style="text-align:right;padding-right:10px;"><?php _e('Do not send mail:', 'post_notification'); ?></th>
		<td>
	        <input type="checkbox" name="nosend" value="true"
	        	<?php 
	        		if($_POST['nosend'] == 'true') echo ' checked="checked" '; 
	        	?> 
	        />	
		</td>
	</tr>
	
	
	<tr class="alternate">
		<td>&nbsp;</td>
		<td><input type="submit" name="updateSettings" value="<?php _e('Send test mail.', 'post_notification'); ?>" /></td>
	</tr>

</table>
</form>
<?php
	
	//send of email
	if(isset($_POST['email']) && isset($_POST['pid'])){
		echo '<h3>' . __('Email', 'post_notification') . '</h3>';
		$t_emails = $wpdb->prefix . 'post_notification_emails';
		
		$emails = $wpdb->get_results(
			" SELECT e.email_addr, e.id, e.act_code" .
			" FROM $t_emails e".
			" WHERE e.email_addr = '" . $_POST['email'] . "'"); //We need this. Otherwise we can't be sure whether we already have sent mail.
		if(!$emails){
			echo '<div class="error"> '. __('Error:', 'post_notification') .  '  ' .__('Email has to be in the database.', 'post_notification') . '</div>';
		} else {
			$email = $emails[0];
			if(file_exists(POST_NOTIFICATION_PATH . 'userfunctions.php'))
				include_once(POST_NOTIFICATION_PATH . 'userfunctions.php'); 
			
			$GLOBALS['wp_query']->init_query_flags();
			
			$maildata = post_notification_create_email($_POST['pid'] , $_POST['template']);
			$send = ($_POST['nosend'] == 'true') ? false :  true; 
			
			$maildata = post_notification_sendmail($maildata, $email->email_addr,'', $send); //returns the modified body.
			if($maildata['sent']==false){
				global $phpmailer;
				echo '<br/><b>The mail has not been sent!</b><br/>';	
				echo 'PHP-Mailer - Dump: <br/><pre>';
				var_dump($phpmailer);
				echo '</pre>';
			}
			echo '<b>Header:</b><BR><pre>';
			echo $maildata['header'];
			echo '</pre><br /> <b>Subject:</b><pre>' . $maildata['subject'] . '</pre> </br>';
			echo '<b>Body:</b><BR><pre>';
			echo htmlspecialchars($maildata['body']);
			echo '</pre><br/>';
		}
	}

}
?>
