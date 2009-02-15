<?php
/*
Plugin Name: Wordpress Newsletter subscription Opt-in for SendBlaster
Plugin URI: http://www.sendblaster.com/wordpress-newsletter-double-optin-widget/
Description: Create a simple form to collect subscription requests to newsletter software managed mailing lists. User input is stored in the db and sent by e-mail in a format compatible with common newsletter softwares' data structure and subscription management.
Version: 1.1.6
Author: Max
Author URI: http://www.sendblaster.com/
*/

/*

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/

$wpsb_db_version = "0.1";


function wpsb_show_form($rtn = 0) {	
	$wpsb_flds = (get_option('wpsb_form_fields'));
	$add_link_lv = get_option("wpsb_link_love");
	$out = '<form action="#wpsbw" method="post">' . "\n";
	$out .= '<p class="wpsb_form_label">' . get_option('wpsb_form_email');
	$out .= '<br /> <input type="text" name="wpsb_email" id="wpsb_email" class="wpsb_form_txt" /></p>' . "\n";
	
	$out .= '<script type="text/javascript">
	//<![CDATA[	
		function wpsb_toggle_custom_fields (state) {
			for (i=2; i<16; i++) {
				if (obj = document.getElementById(\'wpsb_fld_\'+i)) {
					obj.disabled = !state;
					obj.readOnly = !state;
				}
			}
		}
	//]]>
	</script>
	';
	$out .= '<p class="wpsb_form_label"><input type="radio" name="wpsb_radio_option" id="wpsb_radio_option1" onclick="wpsb_toggle_custom_fields(1)" class="wpsb_form_radio" value="wpsb_radio_in" checked="checked" /> '.$wpsb_flds['wpsb_radio_in'];
	$out .= '<br/>';
	$out .= '<input type="radio" name="wpsb_radio_option" id="wpsb_radio_option2" onclick="wpsb_toggle_custom_fields(0)" class="wpsb_form_radio" value="wpsb_radio_out" /> '.$wpsb_flds['wpsb_radio_out'].'</p>';
	
	if (is_array($wpsb_flds)) {
		foreach ($wpsb_flds as $wpsb_k => $wpsb_v) {
			if (is_numeric($wpsb_k) && $wpsb_v) {
				$out .= '<p class="wpsb_form_label">' . $wpsb_v;
				$out .= ' <input type="text" name="wpsb_fld['. $wpsb_k .']" id="wpsb_fld_'. $wpsb_k .'"  maxlength="64" class="wpsb_form_txt" /></p>' . "\n";
			}
		}
	}
	$out .= '<p class="wpsb_form_label"><input type="submit" value="' . get_option('wpsb_form_send');
	$out .= '" class="wpsb_form_btn" /></p>' . "\n</form>\n<!-- Made by www.Sendblaster.com Newsletter Software Opt-in -->\n";
	if ($add_link_lv) {
		$out .= "Get this <a href=\"http://wordpress.org/extend/plugins/newsletter-subscription-double-optin-7/\" title=\"Wordpress newsletter plugin\">Wordpress newsletter widget</a><br /> for <a href=\"http://www.sendblaster.com/newsletter-software-no-recurring-fees/\" title=\"newsletter softwares\">newsletter software</a>";
	}
	if ($rtn) {
		return $out;
	}
	else {
		echo $out;
	}
}

function wpsb_getip() {
	if (isset($_SERVER)) {
		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$ip_addr = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} 
		elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
			$ip_addr = $_SERVER["HTTP_CLIENT_IP"];
		} 
		else {
			$ip_addr = $_SERVER["REMOTE_ADDR"];
		}
	} 
	else {
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ip_addr = getenv( 'HTTP_X_FORWARDED_FOR' );
		} 
		elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ip_addr = getenv( 'HTTP_CLIENT_IP' );
		} 
		else {
			$ip_addr = getenv( 'REMOTE_ADDR' );
		}
	}
	return $ip_addr;
}

function wpsb_has_email_headers($text) {
   return preg_match("/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i", $text);
}

function wpsb_opt_in() {
	global $wpdb;
	$table_users = $wpdb->prefix . "wpsb_users";

	echo stripslashes(get_option('wpsb_form_header'));

	$_POST['wpsb_email'] = trim($_POST['wpsb_email']);
	if (empty($_POST['wpsb_email'])) {
		if (!empty($_GET['wpsb_d']) && !empty($_GET['wpsb_s'])) {
			wpsb_dbl_optin_confirm();
		}
		else {
			wpsb_show_form();
		}
	} 
	else {
		$email = stripslashes($_POST['wpsb_email']);
		$wpsb_custom_flds = "";
		if (!preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email)) {
				echo stripslashes(get_option('wpsb_msg_bad'));
				wpsb_show_form();
		}
		else {
			if ($_POST['wpsb_radio_option'] && $_POST['wpsb_radio_option'] == "wpsb_radio_out") {
				$manager_email = stripslashes(get_option('wpsb_email_from'));
				$wpsb_flds = (get_option('wpsb_form_fields'));
				$headers = "MIME-Version: 1.0\n";
				$headers .= "From: $email\n";
				$headers .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
				if (mail($manager_email, "Unsubscribe", "", $headers)) {
					echo stripslashes($wpsb_flds['wpsb_unsubscr_success']);
				} 
				else {
					echo stripslashes(get_option('wpsb_msg_fail'));
				}
			}
			else {
				$wpsb_double_optin = get_option('wpsb_double_optin');
				$wpsb_auto_delete = get_option('wpsb_auto_delete');
				if (!empty($_POST['wpsb_fld'])) {
					foreach ($_POST['wpsb_fld'] as $wpsb_k => $wpsb_v) {
						if (ereg("^[ ]*([^\t\r\n\\]{1,64}[^ ])[ ]*$", stripslashes($wpsb_v), $wpsb_r)) {
							$wpsb_custom_flds .= "#".$wpsb_k."#: ".$wpsb_r[1]."\n";
						}
					}
				}
				$email_from = stripslashes(get_option('wpsb_email_from'));
				$subject = stripslashes(get_option('wpsb_email_subject'));
				$message = stripslashes(get_option('wpsb_email_message'));
				
				$headers = "MIME-Version: 1.0\n";
				$headers .= "From: $email_from\n";
				$headers .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
		
				$wpsb_time = time();
				$wpsb_ip = wpsb_getip();
				if ($wpsb_double_optin == 1) {
					$wpsb_link = Array ("scheme" => "http", "host" => $_SERVER['HTTP_HOST'], "port" => "", "user" => "", "pass" => "", "path" => "", "query" => "", "fragment" => "");
					$wpsb_link += parse_url(get_bloginfo('wpurl'));
					$wpsb_optin_url = $wpsb_link['scheme']."://".$wpsb_link['host'].$_SERVER['SCRIPT_NAME']."?wpsb_d=".$wpsb_time."&wpsb_s=".md5($email.$wpsb_ip)."#wpsbw";
					$message = str_replace('#link#', $wpsb_optin_url, $message);
				}
				$selectqry = "SELECT * FROM " . $table_users . " WHERE `email` = '" . $email ."'";
				if ($wpdb->query($selectqry)) {
					echo stripslashes(get_option('wpsb_msg_dbl'));
				}
				else {
					if (mail($email,$subject,$message,$headers)) {
						if ($wpsb_double_optin || !$wpsb_auto_delete) {
							// Write new user to database
							$insert = "INSERT INTO " . $table_users . " 
								(time, ip, email, msg_sent, custom_data) 
								VALUES (
								'" . $wpsb_time . "',
								'" . $wpsb_ip . "',
								'" . $email . "',
								'" . (int) !$wpsb_double_optin ."',
								'" . $wpsb_custom_flds . "'
								)";
						 	$result = $wpdb->query($insert);
						}
						if (!$wpsb_double_optin) {
							$headers = "MIME-Version: 1.0\n";
							$headers .= "From: $email\n";
							$headers .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
							mail($email_from, "Subscribe", $wpsb_custom_flds, $headers);
						}
						echo stripslashes(get_option('wpsb_msg_sent'));
					} 
					else {
						echo stripslashes(get_option('wpsb_msg_fail'));
					}
				}
			}
		}
	}
	echo stripslashes(get_option('wpsb_form_footer'));
}

function wpsb_dbl_optin_confirm() {
	global $wpdb;
	$table_users = $wpdb->prefix . "wpsb_users";
	$email = stripslashes(get_option('wpsb_email_from'));
	$wpsb_auto_delete = get_option('wpsb_auto_delete');
	$sql = "SELECT * FROM `". $table_users . "` WHERE `time` = '" . $_GET['wpsb_d'] . "' AND MD5(CONCAT(`email`, `ip`)) = '" . $_GET['wpsb_s'] ."' AND `msg_sent` = '0'";
	$res = $wpdb->get_results($sql);
	if (sizeof($res)) {
		$record = $res[0];
		$headers = "MIME-Version: 1.0\n";
		$headers .= "From: ". $record->email."\n";
		$headers .= "Content-Type: text/plain; charset=\"" . get_settings('blog_charset') . "\"\n";
		if (mail($email, "Subscribe", $record->custom_data, $headers)) {
			if ($wpsb_auto_delete) {
				$update = "DELETE FROM `$table_users` WHERE `id` = ". $record->id;
			}
			else {
				$update = "UPDATE `$table_users` SET `msg_sent` = '1' WHERE `id` = ". $record->id;
			}
			$res = $wpdb->query($update);
			echo stripslashes(get_option('wpsb_dbl_sent'));
		}
		else {
			echo stripslashes(get_option('wpsb_msg_fail'));
		}
	}
	else {
		echo stripslashes(get_option('wpsb_dbl_fail'));
	}
}

function wpsb_install() {
	global $wpdb;
	global $wpsb_db_version;

	$table_users = $wpdb->prefix . "wpsb_users";

	if($wpdb->get_var("show tables like '$table_users'") != $table_users) {

		// Table did not exist; create new
		$sql = "CREATE TABLE `" . $table_users . "` (
  			`id` mediumint(9) NOT NULL auto_increment,
  			`time` bigint(11) NOT NULL default '0',
  			`ip` varchar(50) NOT NULL default '',
 			`email` varchar(50) NOT NULL default '',
  			`msg_sent` enum('0','1') NOT NULL default '0',
  			`custom_data` text NOT NULL,
  			UNIQUE KEY `id` (`id`)
		);";
		$result = $wpdb->query($sql);
		//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		//dbDelta($sql);

		// Insert initial data in table
		$insert = "INSERT INTO `$table_users` (`time`, `ip`, `email`, `msg_sent`) " .
			"VALUES ('" . time() . "','" . wpsb_getip() .
			"','" . get_option('admin_email') . "', '1')";
		$result = $wpdb->query($insert);

		add_option("wpsb_db_version", $wpsb_db_version);

		// Initialise options with default values
		$blogname = get_option('blogname');
		add_option('wpsb_widget_title', 'Choose a title for the widget');
		add_option('wpsb_email_from', get_option('admin_email') );
		add_option('wpsb_email_subject', "[$blogname] Mailing list subscription");
		add_option('wpsb_email_message', "This is an automatic response to a subscription request started at $blogname.\n\nThanks for subscribing.\n\n#link#");
		add_option('wpsb_double_optin', "1");
		add_option('wpsb_link_love', "1");
		add_option('wpsb_auto_delete', "0");
		
		add_option('wpsb_msg_bad', "<p>Bad e-mail address.</p>");
		add_option('wpsb_msg_dbl', "<p>E-mail address already subscribed.</p>");
		add_option('wpsb_msg_fail', "<p>Failed sending to e-mail address.</p>");
		add_option('wpsb_msg_sent', "<p>Thanks for subscribing.</p>");
		add_option('wpsb_dbl_fail', "<p>E-mail address not found or already confirmed.</p>");
		add_option('wpsb_dbl_sent', "<p>Subscription confirmed. Thank you.</p>");

		add_option('wpsb_form_header', "<a name=\"wpsbw\"></a><div class=\"widget module\">You may want to put some text here");
		add_option('wpsb_form_footer', "</div>");
		add_option('wpsb_form_email', "E-mail:");
		//add_option('wpsb_form_fields', "");
		add_option('wpsb_form_fields', array("wpsb_radio_in"=>"Subscribe","wpsb_radio_out"=>"Unsubscribe"));
		add_option('wpsb_form_send', "Submit");
	}
}

function wpsb_options() {
	global $wpdb;
	$table_users = $wpdb->prefix . "wpsb_users";

	// Handle options from get method information
	if (isset($_GET['user_id'])) {
		$user_id = $_GET['user_id'];

		// Delete user from database
		$delete = "DELETE FROM " . $table_users .
				" WHERE id = '" . $user_id . "'";
		$result = $wpdb->query($delete);

		// Notify admin of delete
		echo '<div id="message" class="updated fade"><p><strong>';
		_e('User deleted.', 'wpsb_domain');
		echo '</strong></p></div>';
	}
	
	if (isset($_GET['purge'])) {
		$goOn = false;
		switch (intval($_GET['purge'])) {
			case 1:
				// all
				$to_del = "1";
				$goOn = true;
				break;
			case 2:
				// older than 1 week
				$to_del = "`time` < " . strtotime("-1 week");
				$goOn = true;
				break;
			case 3:
				// older than 2 weeks
				$to_del = "`time` < " . strtotime("-2 weeks");
				$goOn = true;
				break;
			case 4:
				// older than 1 month
				$to_del = "`time` < " . strtotime("-1 month");
				$goOn = true;
				break;
		}
		if ($goOn) {
			// Delete user from database
			$delete = "DELETE FROM `" . $table_users .
					"` WHERE " . $to_del . " AND `msg_sent` = '0'";
			$result = $wpdb->query($delete);
	
			// Notify admin of delete
			echo '<div id="message" class="updated fade"><p><strong>';
			_e($result .' user(s) deleted.', 'wpsb_domain');
			echo '</strong></p></div>';
		}
	}

	// Get current options from database
	$email_from = stripslashes(get_option('wpsb_email_from'));
	$email_subject = stripslashes(get_option('wpsb_email_subject'));
	$email_message = stripslashes(get_option('wpsb_email_message'));
	$double_optin = get_option('wpsb_double_optin');
	$link_love = get_option('wpsb_link_love');
	$auto_delete = get_option('wpsb_auto_delete');
	$msg_bad = stripslashes(get_option('wpsb_msg_bad'));
	$msg_dbl = stripslashes(get_option('wpsb_msg_dbl'));
	$msg_fail = stripslashes(get_option('wpsb_msg_fail'));
	$msg_sent = stripslashes(get_option('wpsb_msg_sent'));
	$dbl_fail = stripslashes(get_option('wpsb_dbl_fail'));
	$dbl_sent = stripslashes(get_option('wpsb_dbl_sent'));

	$form_header = stripslashes(get_option('wpsb_form_header'));
	$form_footer = stripslashes(get_option('wpsb_form_footer'));
	$form_email = stripslashes(get_option('wpsb_form_email'));
	$form_fields = (get_option('wpsb_form_fields'));
	$form_send = stripslashes(get_option('wpsb_form_send'));

	// Update options if user posted new information
	if( $_POST['wpsb_hidden'] == 'SAb13c' ) {
		// Read from form
		$email_from = stripslashes($_POST['wpsb_email_from']);
		$email_subject = stripslashes($_POST['wpsb_email_subject']);
		$email_message = stripslashes($_POST['wpsb_email_message']);
		$double_optin = (int) isset($_POST['wpsb_double_optin']);
		$link_love = (int) isset($_POST['wpsb_link_love']);
		$auto_delete = (int) isset($_POST['wpsb_auto_delete']);
		$msg_bad = stripslashes($_POST['wpsb_msg_bad']);
		$msg_dbl = stripslashes($_POST['wpsb_msg_dbl']);
		$msg_fail = stripslashes($_POST['wpsb_msg_fail']);
		$msg_sent = stripslashes($_POST['wpsb_msg_sent']);
		$dbl_fail = stripslashes($_POST['wpsb_dbl_fail']);
		$dbl_sent = stripslashes($_POST['wpsb_dbl_sent']);

		$form_header = stripslashes($_POST['wpsb_form_header']);
		$form_footer = stripslashes($_POST['wpsb_form_footer']);
		$form_email = stripslashes($_POST['wpsb_form_email']);
		$form_fields = is_array($_POST['wpsb_form_fld']) ? $_POST['wpsb_form_fld'] : array();
		$form_send = stripslashes($_POST['wpsb_form_send']);

		// Save to database
		update_option('wpsb_email_from', $email_from );
		update_option('wpsb_email_subject', $email_subject);
		update_option('wpsb_email_message', $email_message);
		update_option('wpsb_double_optin', $double_optin);
		update_option('wpsb_link_love', $link_love);
		update_option('wpsb_auto_delete', $auto_delete);

		update_option('wpsb_msg_bad', $msg_bad);
		update_option('wpsb_msg_dbl', $msg_dbl);
		update_option('wpsb_msg_fail', $msg_fail);
		update_option('wpsb_msg_sent', $msg_sent);
		update_option('wpsb_dbl_fail', $dbl_fail);
		update_option('wpsb_dbl_sent', $dbl_sent);

		update_option('wpsb_form_header', $form_header);
		update_option('wpsb_form_footer', $form_footer);
		update_option('wpsb_form_email', $form_email);
		update_option('wpsb_form_fields', ($form_fields));
		update_option('wpsb_form_send', $form_send);

		// Notify admin of change
		echo '<div id="message" class="updated fade"><p><strong>';
		_e('Options saved.', 'wpsb_domain');
		echo '</strong></p></div>';
	}
?>
<div class="wrap">
  <h2>Newsletter subscription Double Opt-in Options</h2>
<form method="post" action="">
    <fieldset class="options"> <legend>General settings</legend> 
    <table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">
      <tr valign="top"> 
        <th scope="row">Mailbox for managing subscriptions:</th>
        <td> 
          <p>
		    <input type="hidden" name="wpsb_hidden" value="SAb13c" />
            <input type="text" name="wpsb_email_from" id="wpsb_email_from" value="<?php echo $email_from; ?>" size="40" />
          </p>
          <p><em>Note for <a href="http://www.sendblaster.com" title="Free newsletter software">SendBlaster</a> 
            users</em>: this is the main parameter you have to insert inside <a href="http://www.sendblaster.com/bulk-email-software/wp-content/manage-subscriptions.gif">SendBlaster 
            Manage Subscription</a> section</p>
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Message to subscriber, subject:</th>
        <td> 
          <input type="text" name="wpsb_email_subject" id="wpsb_email_subject" value="<?php echo $email_subject; ?>" size="40" />
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Message to subscriber, content:</th>
        <td> 
          <p> 
            <textarea name="wpsb_email_message" id="wpsb_email_message" rows="4" cols="40"><?php echo $email_message; ?></textarea>
          </p>
          <p> Use the #link# placeholder where you want the URL for confirming 
            subscription to appear (only if double opt-in is checked). </p>
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Double Opt-in:</th>
        <td> 
          <input type="checkbox" name="wpsb_double_optin" id="wpsb_double_optin" value="1"<?php echo $double_optin ? " checked=\"checked\"" : "";?> />
          If checked you will receive subscribing emails only when user clicks 
          on the appropriate link inside confirmation message.</td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Link Love:</th>
        <td> 
          <input type="checkbox" name="wpsb_link_love" id="wpsb_link_love" value="1"<?php echo $link_love ? " checked=\"checked\"" : "";?> />
          If unchecked removes Plugin credits from sidebar.</td>
      </tr>
      <tr valign="top">
        <th scope="row">Delete subscribed users:</th>
        <td>
          <input type="checkbox" name="wpsb_auto_delete" id="wpsb_auto_delete" value="1"<?php echo $auto_delete ? " checked=\"checked\"" : "";?> />
          If checked automatically removes users upon their subscription (use 
          only if you download your suscriptions daily)</td>
      </tr>
      <tr valign="top"> 
        <td colspan="2">&nbsp;</td>
      </tr>
    </table>
    </fieldset> <fieldset class="options"> <legend>Front side messages</legend> 
    <table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">
      <tr valign="top"> 
        <th scope="row">Bad e-mail address:</th>
        <td> 
          <input type="text" name="wpsb_msg_bad" id="wpsb_msg_bad" value="<?php echo $msg_bad; ?>" size="40" />
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">Duplicate e-mail address:</th>
        <td>
          <input type="text" name="wpsb_msg_dbl" id="wpsb_msg_dbl" value="<?php echo $msg_dbl; ?>" size="40" />
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Failed to send:</th>
        <td> 
          <input type="text" name="wpsb_msg_fail" id="wpsb_msg_fail" value="<?php echo $msg_fail; ?>" size="40" />
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Success:</th>
        <td> 
          <input type="text" name="wpsb_msg_sent" id="wpsb_msg_sent" value="<?php echo $msg_sent; ?>" size="40" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Unsubscribe success:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[wpsb_unsubscr_success]" id="wpsb_unsubscr_success" value="<?php echo $form_fields['wpsb_unsubscr_success']; ?>" size="40" />
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Double opt-in failure:</th>
        <td>
          <input type="text" name="wpsb_dbl_fail" id="wpsb_dbl_fail" value="<?php echo $dbl_fail; ?>" size="40" />
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Double opt-in success:</th>
        <td>
          <input type="text" name="wpsb_dbl_sent" id="wpsb_dbl_sent" value="<?php echo $dbl_sent; ?>" size="40" />
        </td>
      </tr>
    </table>
    </fieldset> <fieldset class="options"> 
    <legend>Front side form appearance and labels</legend>
    <table width="100%" cellspacing="2" cellpadding="5" class="optiontable editform">
      <tr valign="top"> 
        <th scope="row">Form header:</th>
        <td> 
          <textarea name="wpsb_form_header" id="wpsb_form_header" rows="4" cols="40"><?php echo $form_header; ?></textarea>
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Form footer:</th>
        <td> 
          <textarea name="wpsb_form_footer" id="wpsb_form_footer" rows="2" cols="40"><?php echo $form_footer; ?></textarea>
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">E-mail (mandatory: field #1)</th>
        <td> <p> 
            <input type="text" name="wpsb_form_email" id="wpsb_form_email" value="<?php echo $form_email; ?>" size="40" maxlength="64" /></p><p>First field (E-mail) is mandatory and cannot be removed <br />
            Leave blank to <strong>disable</strong> other custom fields, <br />
            Writing label names will <strong>enable</strong> the custom fields.</p></td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Custom field #2:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[2]" id="wpsb_form_fld2" value="<?php echo $form_fields[2]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #3:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[3]" id="wpsb_form_fld3" value="<?php echo $form_fields[3]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #4:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[4]" id="wpsb_form_fld4" value="<?php echo $form_fields[4]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #5:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[5]" id="wpsb_form_fld5" value="<?php echo $form_fields[5]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #6:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[6]" id="wpsb_form_fld6" value="<?php echo $form_fields[6]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #7:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[7]" id="wpsb_form_fld7" value="<?php echo $form_fields[7]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #8:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[8]" id="wpsb_form_fld8" value="<?php echo $form_fields[8]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #9:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[9]" id="wpsb_form_fld9" value="<?php echo $form_fields[9]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #10:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[10]" id="wpsb_form_fld10" value="<?php echo $form_fields[10]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #11:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[11]" id="wpsb_form_fld11" value="<?php echo $form_fields[11]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #12:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[12]" id="wpsb_form_fld12" value="<?php echo $form_fields[12]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #13:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[13]" id="wpsb_form_fld13" value="<?php echo $form_fields[13]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #14:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[14]" id="wpsb_form_fld14" value="<?php echo $form_fields[14]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Custom field #15:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[15]" id="wpsb_form_fld15" value="<?php echo $form_fields[15]; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Subscribe label:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[wpsb_radio_in]" id="wpsb_form_fld16" value="<?php echo $form_fields['wpsb_radio_in']; ?>" size="40" maxlength="64" />
        </td>
      </tr>
	  <tr valign="top"> 
        <th scope="row">Unsubscribe label:</th>
        <td> 
          <input type="text" name="wpsb_form_fld[wpsb_radio_out]" id="wpsb_form_fld17" value="<?php echo $form_fields['wpsb_radio_out']; ?>" size="40" maxlength="64" />
        </td>
      </tr>
      <tr valign="top"> 
        <th scope="row">Submit button:</th>
        <td> 
          <input type="text" name="wpsb_form_send" id="wpsb_form_send" value="<?php echo $form_send; ?>" size="40" maxlength="64" />
        </td>
      </tr>
      <tr valign="top"> 
        <td colspan="2" scope="row">&nbsp;</td>
      </tr>
    </table>
</fieldset>
<p class="submit">
<input type="submit" name="Submit" value="Update Options &raquo;" />
</p>
</form>
</div>
<div class="wrap">
<h2>Temp Opted-in users backup</h2>
  <p>Delete users from this panel once you have downloaded subscriptions with 
    your mailing list software. <br />
</p>
<?php
	if ($users = $wpdb->get_results("SELECT * FROM $table_users WHERE `msg_sent` = '1' ORDER BY `id` DESC")) {
?>
<h3>Bcc friendly format:</h3>
<p>
<?php
		$additional_user=0;
		foreach ($users as $user) {
			if ($user->msg_sent == "1") {
				if ($additional_user) {
					echo ', ';
				}
				$additional_user=1;
				echo $user->email;
			}
		}	
?>
</p>
<?php
	}
	if ($users = $wpdb->get_results("SELECT * FROM $table_users ORDER BY `id` DESC")) {
		$user_no=0;
		$url = get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=' .
			basename(__FILE__);
?>
<table class="widefat">
<thead>    
	<tr align="right"> 
      <td colspan="6">
	    <script type="text/javascript"> 
			//<![CDATA[
			function confirm_purge (frm) {
				if(frm.purge.selectedIndex != 0 && confirm('Are you sure you want to proceed?')) {
					top.location.href='<?php echo $url; ?>&purge=' + frm.purge.options[frm.purge.selectedIndex].value;
				}
			}
			//]]>
		</script>
        <form method="get" action="">
		  <fieldset class="options">Purge non opted-in users: 
          <select name="purge" id="purge">
            <option value="0">Select...</option>
            <option value="1">All</option>
            <option value="2">Older than 1 week</option>
            <option value="3">Older than 2 weeks</option>
            <option value="4">Older than 1 month</option>
          </select>
          <input type="button" name="prg_btn" id="prg_btn" value="Go" onclick="confirm_purge(this.form)" />
		  </fieldset>
		</form>
	  </td>
</tr>
<tr>
<th scope="col">ID</th>
<th scope="col">Date/Time</th>
<th scope="col">Opted-in</th>
<th scope="col">IP</th>
<th scope="col">E-mail</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<?php
		$url = $url . '&amp;user_id=';
		foreach ($users as $user) {
			if ($user_no&1) {
				echo "<tr class=\"alternate\">";
			} else {
				echo "<tr>";
			}
			$user_no=$user_no+1;
			echo "<td>$user->id</td>";
			echo "<td>" . date(get_option('date_format'), $user->time). " " . date(get_option('time_format'), $user->time) . "</td>";
			echo "<td>";
			echo $user->msg_sent ? "Yes" : "No";
			echo "</td>";
			echo "<td>$user->ip</td>";
			echo "<td>$user->email</td>";
			echo "<td><a href=\"$url$user->id\" onclick=\"if(confirm('Are you sure you want to delete user with ID $user->id?')) return; else return false;\">Delete</a></td>";
			echo "</tr>";
		}
?>
</tbody>
</table>
<p><em>ToolTip</em>: to insert the module in a page: 1) install the <a href="http://wordpress.org/extend/plugins/exec-php/">exec php</a> plugin; 2) insert this code in your pages: &lt;?php wpsb_opt_in(); ?&gt;</p></div>
<?php
	}
}

function wpsb_widget_init() {
	global $wp_version;

	if (!function_exists('register_sidebar_widget')) {
		return;
	}

	function wpsb_widget($args) {
		extract($args);
		echo $before_widget . $before_title;
		echo get_option('wpsb_widget_title');
		echo $after_title;
		wpsb_opt_in();
		echo $after_widget;
	}

	function wpsb_widget_control() {
		$title = get_option('wpsb_widget_title');
		if ($_POST['wpsb_submit']) {
			$title = stripslashes($_POST['wpsb_widget_title']);
			update_option('wpsb_widget_title', $title );
		}
		echo '<p>Title:<input  style="width: 200px;" type="text" value="';
		echo $title . '" name="wpsb_widget_title" id="wpsb_widget_title" /></p>';
		echo '<input type="hidden" id="wpsb_submit" name="wpsb_submit" value="1" />';
	}

	$width = 300;
	$height = 100;
	if ( '2.2' == $wp_version || (!function_exists( 'wp_register_sidebar_widget' ))) {
		register_sidebar_widget('WP Sendblaster Opt-in', 'wpsb_widget');
		register_widget_control('WP Sendblaster Opt-in', 'wpsb_widget_control', $width, $height);
	} else {
		// v2.2.1+
		$size = array('width' => $width, 'height' => $height);
		$class = array( 'classname' => 'wpsb_opt_in' ); // css classname
		wp_register_sidebar_widget('wpsb', 'WP Sendblaster Opt-in', 'wpsb_widget', $class);
		wp_register_widget_control('wpsb', 'WP Sendblaster Opt-in', 'wpsb_widget_control', $size);
	}
	if (function_exists('register_sidebar_module')) {
		$class = array( 'classname' => 'wpsb_opt_in' ); // css classname
		register_sidebar_module('WP Sendblaster Opt-in', 'wpsb_widget', '', $class);
		register_sidebar_module_control('WP Sendblaster Opt-in', 'wpsb_widget_control');

	}
}

function wpsb_add_to_menu() {
	add_options_page('WP Sendblaster Opt-in Options', 'WP Sendblaster Opt-in', 7, __FILE__, 'wpsb_options' );
}

function wpsb_insert ($cnt) {
	 global $wpsb_ob;
	 $cnt = str_replace("<!--wpsb-opt-in-->", $wpsb_ob, $cnt);
	 return $cnt;
}


register_activation_hook(__FILE__, 'wpsb_install');
add_action('admin_menu', 'wpsb_add_to_menu');
add_action('init', 'wpsb_widget_init');

?>