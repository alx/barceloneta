<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

require_once("ldif2array.class.php");

function ldif2addresses($input)
{
  $ld = new ldif2array($input);
  $retval='';
  if($ld->makeArray()) {
    foreach ($ld->getArray() as $a) {
      $retval.= $a['mail'].',';
    }
  }
  return $retval;
}

function post_notification_admin_sub(){
	echo '<h3>' . __('Manage addresses', 'post_notification') . '</h3>';
	if (!$_POST['manage']){
		?>
		<p> <?php _e('The Emails may be seprated by newline, space, comma, semi colon, tabs, [, ], &lt; or &gt;.' , 'post_notification'); ?> <br />
		<b><?php _e('Watch out! There is only simple checking whether the email address is valid.', 'post_notification'); ?> </b></p>
		
		<!-- The data encoding type, enctype, MUST be specified as below -->
		<form enctype="multipart/form-data" action="admin.php?page=post_notification/admin.php&amp;action=manage" method="POST">
		    <?php _e('Load LDIF-File:', 'post_notification'); ?>
			<input name="ldif_file" type="file" />
		    <input type="submit" value="<?php _e('Load', 'post_notification'); ?>" name="ldif_import" />
		</form>

		<form name="import" action="admin.php?page=post_notification/admin.php&amp;action=manage" method="post">
		  	<b><?php _e('Emails' , 'post_notification'); ?>:</b>
		  	<br />
			<textarea name="imp_emails" cols="60" rows="10" class="commentBox"><?php
				 if ($_POST['ldif_import']) echo ldif2addresses($_FILES['ldif_file']['tmp_name']) 
			?></textarea>
		  	<br /><br />
		
		  	
		  	<?php _e('What should be done?', 'post_notification'); ?><br/>
			<input type="radio" name="logic" value="add" checked="checked" ><?php _e('Add selected categories', 'post_notification'); ?></input><br />
			<input type="radio" name="logic" value="rem"><?php _e('Remove selected categories', 'post_notification'); ?></input><br />
			<input type="radio" name="logic" value="repl"><?php _e('Replace with selected categories', 'post_notification'); ?></input><br />
			<input type="radio" name="logic" value="del"><?php _e('Delete the listed emails', 'post_notification'); ?></input><br />
			<?php 
				$selected_cats = explode(',', get_option('post_notification_selected_cats'));    
				echo post_notification_get_catselect('', $selected_cats); 

			?>
			<input type="submit" name="manage" value="<?php _e('Manage', 'post_notification'); ?>" class="commentButton" />
		  	<input type="reset" name="Reset" value="<?php _e('Reset', 'post_notification'); ?>" class="commentButton" /><br/><br/><br/>
		</form>
		<?php
	} else {	
		global $wpdb;
		$t_emails = $wpdb->prefix . 'post_notification_emails';
		$t_cats = $wpdb->prefix . 'post_notification_cats';
		
		
		$import_array = preg_split('/[\s\n\[\]<>\t,;]+/',$_POST['imp_emails'],-1, PREG_SPLIT_NO_EMPTY);

		foreach($import_array as $addr){
			// Set Variables //
			$gets_mail = 1;
			$now = post_notification_date2mysql();;
			
			// Basic checking
			if(!is_email($addr)){
				if(!$addr == ""){
					echo '<div class="error">' .  __('Email is not valid:', 'post_notification') . " $addr</div>";			
				}
				continue;
			}
			//*************************************/
			//*    Check database for duplicates  */
			//*************************************/
			
			$mid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'"); 
			
			if($_POST['logic'] == 'del'){
				if($mid != ''){
					$wpdb->query("DELETE FROM $t_emails WHERE id = $mid");
					$wpdb->query("DELETE FROM $t_cats WHERE id = $mid");
					echo "<div>" . __('Removed email:', 'post_notification') . " $email_addr</div>";
				} else {
					echo '<div class="error">' .  __('Email is not in DB:', 'post_notification') . " $addr</div>";
				}	
				continue;
			}
			
			
			//Let's create an entry
				
			if (!$mid) {
				$wpdb->query(
						"INSERT " . $t_emails .
						" (email_addr, gets_mail, last_modified, date_subscribed) " .
						" VALUES ('$addr', '$gets_mail', '$now', '$now')");
				echo "<div>" . __('Added Email:', 'post_notification') . " $addr</div>";
				$mid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'"); 		
			} 
			
			
			if($mid == ''){
				echo '<div>' . __('Something went wrong with the Email:', 'post_notification') . $addr . '</div>';
				continue;
			}
			

			
			if($_POST['logic'] == 'repl'){
				$wpdb->query("DELETE FROM $t_cats WHERE id = $mid");
			}
			
			
			$pn_cats = $_POST['pn_cats'];
			
			if(!is_array($pn_cats)) $pn_cats = array(); //Just to make sure it doesn't crash
			
			//Let's see what cats we have
			foreach($pn_cats as $cat){
					if(is_numeric($cat)){ //Security 
						if($_POST['logic'] == 'rem'){
							$wpdb->query("DELETE FROM $t_cats WHERE id = $mid AND cat_id = $cat");
						} else { 
							if(!$wpdb->get_var("SELECT id FROM $t_cats WHERE id = $mid AND cat_id = $cat")){
								$wpdb->query("INSERT INTO $t_cats (id, cat_id) VALUES($mid, $cat)");
							}
						}
					}
			}
			echo '<div>' . __('Updated Email:', 'post_notification') . " $addr</div>";	
			
		} //end foreach
	}
}
