<?php
	global $wpdb;
	$results = $wpdb->get_results('SELECT DISTINCT user_id FROM '.$wpdb->prefix.'wpsc_logged_subscriptions', ARRAY_A);
	//echo "<pre>".print_r($results)."</pre>";
	echo "<div class='wrap'>";
	echo "<h2>e-Commerce Subscribers</h2>";
	echo "<table class='widefat'>";
	echo "<tr>";
	echo "<th>".TXT_WPSC_USERID."</th><th>".TXT_WPSC_NAME."</th><th>".TXT_WPSC_EMAIL."</th><th>".TXT_WPSC_REGISTEREDDATE."</th><th>".TXT_WPSC_SUSPEND."/".TXT_WPSC_ACTIVATE."</th>";
	echo "</tr>";
	if ($results != NULL){
	$now = time();
		foreach ($results as $user){
			$user_info = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'users WHERE id='.$user['user_id'], ARRAY_A);
			$user_subscription = $wpdb->get_results('SELECT active FROM '.$wpdb->prefix.'wpsc_logged_subscriptions WHERE user_id='.$user['user_id'].' AND start_time < '.$now.' AND (start_time+length) > '.$now.'',ARRAY_A);
			
			//echo "<pre>".print_r($user_info,true)."</pre>";
			echo "<tr>";
			echo "<td>";
			echo $user_info[0]['ID'];
			echo "</td>";
			echo "<td>";
			echo $user_info[0]['display_name'];
			echo "</td>";
			echo "<td>";
			echo $user_info[0]['user_email'];
			echo "</td>";
			echo "<td>";
			echo $user_info[0]['user_registered'];
			echo "</td>";
			echo "<td>";
			if ($user_subscription[0]['active']==1) {
				echo "<input type='checkbox' checked='true' id='suspend_subs".$user_info[0]['ID']."' value='1' onclick='suspendsubs(".$user_info[0]['ID'].")' />";
			} else {
				echo "<input type='checkbox' id='suspend_subs".$user_info[0]['ID']."' value='1' onclick='suspendsubs(".$user_info[0]['ID'].")' />";
			}
			echo "</td>";
			echo "</tr>";
		}
	}
	echo "</table>";
	echo "</div>";
?>
