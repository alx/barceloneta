<?php

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------

/**
 * There are several functions that help you integrate. You can call them several times on a single page.
 * 
 * function post_notification_feheader()
 * Returns the header as a string
 * Can be used by a template -> That's why it int the post_notification.php
 * 
 * function post_notification_febody()
 * Returns the Body as a string
 * Can be used by a template -> That's why it int the post_notification.php
 * 
 * function post_notification_fe($class = 'entry')
 * Outputs:
 * <h2>Header</h2>
 * <div class = "$class">
 * output
 * </div>
 * 	
 * function post_notification_page_content()
 * Returns a array with 'header' and 'body' entries.
 * 
*/


function post_notification_fe($class = 'entry'){
	global $wpdb;
	$content = post_notification_page_content();
	
	echo '<h2>' . $content['header']  . '</h2><div class="' . $class . '">' . $content['body']  . '</div>';
}


function post_notification_check_captcha(){
	if(get_option('post_notification_captcha') == 0) return true;
	if($_POST['captchacode'] == '') return false;
	if($_POST['captcha'] == '') return false;
	require_once( POST_NOTIFICATION_PATH . 'class.captcha.php' );
	$my_captcha = new captcha($_POST['captchacode'], POST_NOTIFICATION_PATH . '_temp');
	return $my_captcha->verify( $_POST['captcha']);
}





/**
 * This creates the content
 */
function post_notification_page_content(){
	global $post_notification_page_content_glob, $wpdb;
	if($post_notification_page_content_glob) return $post_notification_page_content_glob;
	
	//It doesn't matter where this goes:
	
	
	$content = & $post_notification_page_content_glob;
	$content = array();
	$content['header'] = '';
	$content['body'] = '';
	
	
		
	// ******************************************************** //
	//                  GET VARIABLES FROM URL
	// ******************************************************** //
	
	
	$action = $_GET['action'];
	$addr   = $wpdb->escape($_GET['addr']);
	$code   = $wpdb->escape($_GET['code']); 

	
	if ($_POST['addr'] != '') {
		$action = $_POST['action'];
		$addr = $wpdb->escape($_POST['addr']);
		$code = $wpdb->escape($_POST['code']);
		$pn_cats = $_POST['pn_cats']; //Security is handled in the function.
	}

	$msg = &$content['body'];
	
	// ******************************************************** //
	//                  DEFINE OTHER VARS NEEDED
	// ******************************************************** //
	require(post_notification_get_profile_dir(). '/strings.php');
	
	
	$t_emails = $wpdb->prefix . 'post_notification_emails';
	$t_cats = $wpdb->prefix . 'post_notification_cats';
	
	$from_email = get_option('post_notification_from_email');
	$pnurl      = post_notification_get_link();
	if(get_option('post_notification_hdr_nl') == "rn")
		$hdr_nl = "\r\n";
	else
		$hdr_nl = "\n";
	$blogname     = get_option('blogname');
	
	// ******************************************************** //
	//                      Code Check
	// ******************************************************** //	
	
	//This code is not very nice in performance, but I wanted to keep it as easy to understand as possible. It's not called that often.
	if(($code != '') && $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr' AND act_code = '" . $code . "'")){
		// ******************************************************** //
		//                   WITH AUTH
		// ******************************************************** //
			
		if(1 != $wpdb->get_var("SELECT gets_mail FROM $t_emails WHERE email_addr = '$addr'")){
			//The user just subscribed, so let's set him up
			$now = post_notification_date2mysql();
			$wpdb->query("UPDATE $t_emails SET gets_mail = 1, date_subscribed = '$now' WHERE email_addr = '$addr'");
 	        $mailid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'");
			$selected_cats = explode(',', get_option('post_notification_selected_cats'));
 			$queryCats = '';
			if (! empty($selected_cats)) {
 			    $queryCats = "";
 			    foreach ($selected_cats as $category) {
 			        if(is_numeric($category)) $queryCats .= ", ($mailid, $category)";
 			    }
 			    if(strlen($queryCats) > 0)
 			    	$wpdb->query("INSERT INTO $t_cats (id, cat_id) VALUES" . substr($queryCats, 1));
 			}
 			if(isset($post_notification_strings['welcome'])){
 				$msg =  '<h3>' . str_replace('@@blogname' , get_option(blogname), $post_notification_strings['welcome']).  '</h3>';
 			}  else {
 				$msg =  '<h3>' . $post_notification_strings['saved'] .  '</h3>';
 			} 
			
		}
	
		
		// ******************************************************** //
		//                      Select Cats
		// ******************************************************** //
		if ($action == "subscribe") { 
			
			$wpdb->query("UPDATE $t_emails SET gets_mail = 1 WHERE email_addr = '$addr'");
			$mid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'"); 

			if(get_option('post_notification_show_cats') == 'yes'){ 
				//Delete all entries
				$wpdb->query("DELETE FROM $t_cats WHERE id = $mid");
				
				if(!is_array($pn_cats)) $pn_cats = array(); //Just to make shure it doesn't crash
				
				//Let's see what cats we have
				$queryCats = '';
				foreach($pn_cats as $cat){
					if(is_numeric($cat)) $queryCats .= ", ($mid, $cat)";//Security		
				}
				
				if(strlen($queryCats) > 0)
					$wpdb->query("INSERT INTO $t_cats (id, cat_id) VALUES" . substr($queryCats, 1));
			}
			$msg .= '<h3>' . $post_notification_strings['saved'] .  '</h3>';

		}
		
		
		// ******************************************************** //
		//                    UNSUBSCRIBE
		// ******************************************************** //
		if ($action == "unsubscribe" AND is_email($addr)) {

			$mid = $wpdb->get_var("SELECT id FROM $t_emails WHERE email_addr = '$addr'"); 
			if($mid != ''){
				$wpdb->query("DELETE FROM $t_emails WHERE id = $mid");
				$wpdb->query("DELETE FROM $t_cats WHERE id = $mid");
			}
			
			$content['header'] = $post_notification_strings['deaktivated'];
			$msg = str_replace(array('@@addr', '@@blogname'), array($addr, $blogname),
							$post_notification_strings['no_longer_activated']);		
			return $content; 
		}
		
		
		// ********************************************************//
		//                     Subscribe-page
		// ********************************************************//
		
		
		$content['header'] = get_option('post_notification_page_name');
		
		

		
		$id = $wpdb->get_var("SELECT id FROM $t_emails  WHERE email_addr = '$addr'");
		
		
		if(get_option('post_notification_show_cats') == 'yes'){ 
			$subcats_db = $wpdb->get_results("SELECT cat_id FROM $t_cats  WHERE id = $id");
			$subcats = array();
			if(isset($subcats_db)){
				foreach($subcats_db as $subcat){
					$subcats[] =  $subcat->cat_id;
				}
			}
			
			
			// Get cats listing
			$cats_str = post_notification_get_catselect($post_notification_strings['all'], $subcats);
		} else {
			$cats_str = '';
		}
		$vars = '<input type="hidden" name="code" value="' . $code . '" /><input type="hidden" name="addr" value="' . $addr . '" />';
		
		if($action == "subscribe" && get_option('post_notification_saved_tmpl') == 'yes'){
			$msg = 	post_notification_ldfile('saved.tmpl');
		} else {
			$msg .= post_notification_ldfile('select.tmpl');
		}
		$msg = str_replace('@@action',post_notification_get_link(),$msg);
		$msg = str_replace('@@addr',$addr,$msg);
		$msg = str_replace('@@cats',$cats_str,$msg);
		$msg = str_replace('@@vars',$vars,$msg);

		
		
	
	} else {
		// ******************************************************** //
		//                   WITHOUT AUTH
		// ******************************************************** //
		$code = '';
		if(is_email($addr) && post_notification_check_captcha()){
			// ******************************************************** //
			//                      SUBSCRIBE
			// ******************************************************** //
			if ($action == "subscribe" || $action == '') {				
				$conf_url = post_notification_get_mailurl($addr);
						
				// Build  mail
				$mailmsg = post_notification_ldfile('confirm.tmpl');
				
				$mailmsg = str_replace('@@addr',$addr,$mailmsg);
				$mailmsg = str_replace('@@conf_url',$conf_url,$mailmsg);

				wp_mail($addr, "$blogname - " . get_option('post_notification_page_name'), $mailmsg, post_notification_header());
				
				//Output Page
				$content['header'] = $post_notification_strings['registration_successful'];
				$msg = post_notification_ldfile('reg_success.tmpl');
				return $content; //here it ends - We don't want to show the selection screen.
	
			}
			// ******************************************************** //
			//                    UNSUBSCRIBE
			// ******************************************************** //
			if ($action == "unsubscribe") {
				if ($wpdb->get_var("SELECT email_addr FROM $t_emails WHERE email_addr = '$addr'")){ //There is a mail in the db	
					$conf_url = post_notification_get_mailurl($addr);
					$conf_url .= "action=unsubscribe";
					
					$mailmsg = post_notification_ldfile('unsubscribe.tmpl');
					
					$mailmsg = str_replace(array('@@addr','@@conf_url'), array($addr, $conf_url), $mailmsg);
					wp_mail($addr, "$blogname - " . $post_notification_strings['deaktivated'], $mailmsg, post_notification_header());
				}
				$content['header'] = $post_notification_strings['deaktivated'];
				$msg = str_replace(array('@@addr', '@@blogname'), array($addr, $blogname),
								$post_notification_strings['unsubscribe_mail']);
				return $content; //here it ends - We don't want to show the selection screen.
			}
				
		}
		
		if($addr != ''){
			if(!is_email($addr))
				$msg .= '<p class="error">' . $post_notification_strings['check_email'] . '</p>';
			if(!post_notification_check_captcha() && action != '')
				$msg .= '<p class="error">' . $post_notification_strings['wrong_captcha'] . '</p>';
		} 
		
		//Try to get the email addr
		if($addr == ''){
			$addr = post_notification_get_addr();
		}
		
		$content['header'] = get_option('post_notification_page_name');
		
	
		$msg .= post_notification_ldfile('subscribe.tmpl');
		$msg = str_replace('@@action',post_notification_get_link($addr),$msg);
		$msg = str_replace('@@addr',$addr,$msg);
		$msg = str_replace('@@cats','',$msg);
		$msg = str_replace('@@vars',$vars,$msg);
		
		//Do Captcha-Stuff
		if(get_option('post_notification_captcha') == 0){ 
			$msg = preg_replace('/<!--capt-->(.*?)<!--cha-->/is', '', $msg); //remove captcha
		} else {
			require_once( POST_NOTIFICATION_PATH . 'class.captcha.php' );
			$captcha_code = md5(round(rand(0,40000))); 
			$my_captcha = new captcha($captcha_code, POST_NOTIFICATION_PATH . '_temp');
			$captchaimg = POST_NOTIFICATION_PATH_URL . '_temp/cap_' . $my_captcha->get_pic(get_option('post_notification_captcha')) . '.jpg';
			$msg = str_replace('@@captchaimg',$captchaimg,$msg);
			$msg = str_replace('@@captchacode',$captcha_code,$msg);
			
		}
	}
		
	return $content;
	
}


function post_notification_filter_content($content){
	if(strpos($content, '@@post_notification_')!== false){ //Just looking for the start
		$fe = post_notification_page_content();
		$content = str_replace('@@post_notification_header', $fe['header'], $content);
		$content = str_replace('@@post_notification_body', $fe['body'], $content);
	}
	
	return $content;
}


?>
