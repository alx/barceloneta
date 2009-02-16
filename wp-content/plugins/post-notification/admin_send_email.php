<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

function post_notification_admin_sub(){?>
<H3> Email versand deaktiviert </H3>
<p> Der Emailversand ist momentan deaktiviert. In der n&auml;chsten version wird er wahrscheinlich wieder vorhanden sein.</p>
<?php
}

function post_notification_for_future_use(){

if (!isset($emailList)) {
?>
	<form method="post" action="admin.php?page=wp-email-notification/index.php&amp;action=email_subscribers">

	<h3>eMail senden</h3>
	<b>Betreff</b><br />
	<input name="subject" type="text" size="50" class="commentBox" /><br /><br />
	<b>Mitteilung</b><br />
	<textarea name="message" cols="80" rows="10" class="commentBox"></textarea>

	<p><small>Info: reine Text-eMail</small></p>
	
	<input type="submit" name="emailList" value="Senden" class="commentButton" />
	<input type="reset" name="Reset" value="Zur&uuml;cksetzen" class="commentButton" />
	
	</form>
	
<?php
} else {    

	
		$site_name = $row_config['site_name'];
		$site_url = $row_config['site_url'];
		$from_email = $row_config['from_email'];
	}

	/*************************************************/
	/* Get Subscribed Email Addresses                */
	/*************************************************/
		
	$sql_email_list = "SELECT * FROM " . $dbtable_praefix . "email_list WHERE gets_mail = 1";	
	$result_email_list = mysql_query($sql_email_list);
	
	if (!$result_email_list) {
		echo '<p class="error">Konnte die Anfrage ($sql_email_list) an die DB nicht erfolgreich laufen lassen: ' . mysql_error() . '</p>';
		echo '</div>';
		exit;
	}
	
	if (mysql_num_rows($result_email_list) == 0) {
		echo '<p class="error">Keine Adressen gefunden !</p>';
		echo '</div>';
		exit;
	}
	$number = 0;
	while ($row = mysql_fetch_assoc($result_email_list)) {
		$email_addr = $row['email_addr'];
   
   		$site_name = str_replace(array(utf8_encode('Ä'), utf8_encode('ä'), utf8_encode('Ö'), utf8_encode('ö'),utf8_encode('Ü'), utf8_encode('ü'), utf8_encode('ß')), array('Ä', 'ä', 'Ö', 'ö', 'Ü', 'ü', 'ß'), $site_name);
		$header = "From: \"" . $site_name . "\" <$from_email>\n";

		$subject = stripslashes($subject);
		$subject = str_replace(array("â€œ","â€?","â€™","â€“","â€”","â€¦","&nbsp;"), array('"','"','´','–','—','...',' '), $subject);
		$subject = str_replace(array(chr(196), chr(228), chr(214), chr(246), chr(220), chr(252), chr(223)), array(utf8_encode('Ä'), utf8_encode('ä'), utf8_encode('Ö'), utf8_encode('ö'), utf8_encode('Ü'), utf8_encode('ü'), utf8_encode('ß')), $subject);
		$subject = utf8_decode($subject);	

		$subject = stripslashes($subject);
 
		$message = stripslashes($message);
		$message = str_replace(array("â€œ","â€?","â€™","â€“","â€”","â€¦","&nbsp;"), array('"','"','´','–','—','...',' '), $message);
		$message = str_replace(array(chr(196), chr(228), chr(214), chr(246), chr(220), chr(252), chr(223)), array(utf8_encode('Ä'), utf8_encode('ä'), utf8_encode('Ö'), utf8_encode('ö'), utf8_encode('Ü'), utf8_encode('ü'), utf8_encode('ß')), $message);
		$message = utf8_decode($message);	
 
		$msg = $message;
		$msg .= "\n\n______________________________________________________\n";
		$msg .= "\n";
		$msg .= "Du hast das Empfangen diese Mitteilungen unterzeichnet. \n\n";
		$msg .= "Wenn Du das ändern möchtest, besuche die folgende URL:\n";
		$msg .= $site_url;
		$msg .= "maillist/index.php?action=unsub&addr=$email_addr\n";
		
		$msg = stripslashes($msg);
 
		Mail($email_addr, $subject, $msg, $header);
		$number++;
	}
	
	mysql_free_result($result_config);  // Free the memory
	mysql_free_result($result_email_list);  // Free the memory
	
	if ($number == "1") {
		echo '<p class="updated"><br />Die eMail wurde an einen User versandt.<br /></p>';
		}
	
	else {	
		echo '<p class="updated"><br />Die eMail wurde an ' . $number . ' User versandt.<br /></p>';
		}
		
}

?>		