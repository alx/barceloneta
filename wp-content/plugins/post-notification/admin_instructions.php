<?php 

#------------------------------------------------------
# INFO
#------------------------------------------------------
# This is part of the Post Notification Plugin for 
# Wordpress. Please see the readme.txt for details.
#------------------------------------------------------


function post_notification_admin_sub(){
?>
<h3><?php _e('Instructions', 'post_notification'); ?></h3>
<ol>
	<li>
		<strong><?php _e('Important', 'post_notification'); ?></strong>
		<p><?php echo __('I\'m very bad in writing documentation. Please check the PN Forum for support! And look around whether someone else already asked that question.', 'post_notification'); ?><br />
 		<a href="http://pn.xn--strbe-mva.de/"><?php _e('To the PN Forum.', 'post_notification'); ?></a>
	</li>
	<li>
		<strong><?php echo __('What does this plugin do?', 'post_notification'); ?></strong>
		<p><?php echo __('With each new post an email is sent to every registered user in the database. The email can be text or HTML.', 'post_notification'); ?> <br />
		<?php echo __('After subscribing the user gets a opt-in email with a link he has to visit before getting any mails.', 'post_notification'); ?>
	</li>
	
	<li>
		<strong><?php _e('Integration', 'post_notification'); ?></strong>
		<p><?php _e('Post Notification always needs a special Post Notification Page. This may be a page, post enty or a special .php page..', 'post_notification'); ?></p>
		<p><?php _e('There are several ways of integration:', 'post_notification'); ?>
		<ul>
			<li><?php 	echo str_replace('@@repl', __('Replacement in Posts', 'post_notification'), __('The strings @@post_notification_header and @@post_notification_body will be replaced in your post in case the "@@repl" option ist turned on in the settings.', 'post_notification')) . ' ';
						echo __('Therefore a new page with @@post_notification_header in the title and @@post_notification_body in the Post itself must be created.', 'post_notification') . ' '; 
						echo __('This is done automaticly by the "Add Post Notification page" option. If you do not do that you have to add the post-id/page-id in the "Link to the Post Notification page" setting by hand.', 'post_notification'); ?>
			</li>
			<li><?php	echo __('Use the Post Notification template. The Template is automaticly copied to your theme. This method might have some trouble with badly written themes. But has a little better performance.', 'post_notification'). ' '; 
						echo __('If you do not use the "Add Post Notification page" option you have to add the post-id in the "Link to the Post Notification page" setting by hand.', 'post_notification'); ?>
			</li>

			<li><?php 	echo __('Copy the wp-post_notification.php to your WP-root. Copy&Paste the url to the "Link to the Post Notification page" setting', 'post_notification'); ?>
			</li>
			
			<li><?php 	echo __('You can copy the content of the subscribe_snippet.php into your theme.', 'post_notification') . ' ';
						echo __('You still need a special Post Notification Page. ', 'post_notification'); ?> 
			</li>
			<li><?php 	echo __('Coders might want to look at the frontend.php.', 'post_notification'); ?> 
			</li>
		</ul>
	</li>
	
	<li>
		<strong><?php _e('Templates', 'post_notification'); ?></strong>
		<p><em> <?php _e('If you want to modify the templates please copy your favorit template in to the folder &lt;wp-dir&gt;/wp-content/post_notification. Afterwards rename it to whatever you like.', 'post_notification'); ?></em>
			
		</p>
		<p><?php _e('Vars used in the templates', 'post_notification'); ?>
			<ul>
				<li>@@title - <?php _e('The title of the post', 'post_notification'); ?></li>
				<li>@@author - <?php _e('The author of the post', 'post_notification'); ?></li>
				<li>@@content - <?php _e('The content of the post', 'post_notification'); ?></li>
				<li>@@permalink - <?php _e('The link to the post.', 'post_notification'); ?></li>
				<li>@@blogname - <?php _e('The Name of the blog', 'post_notification'); ?></li>
				<li>@@date - <?php _e('Date of the post being published', 'post_notification'); ?></li>
				<li>@@time - <?php _e('Time of the post being published', 'post_notification'); ?></li>
				<li>@@to_addr - <?php _e('The email the mail is sent to.', 'post_notification'); ?></li>
				<li>@@conf_url - <?php _e('The URL that has to be visited to verify the subscription.', 'post_notification'); ?></li>
			</ul>
		</p>
		<p><?php _e('The different templates', 'post_notification'); ?> <br />
			<?php _e('I know the names are not the best, but I\'m to lazy too change them.', 'post_notification'); ?> 
			<ul>
				<li>subscribe.tmpl - <?php _e('This is the subscription page.', 'post_notification'); ?></li>
				<li>reg_success.tmpl - <?php _e('This is the page you see after subscribing.', 'post_notification'); ?></li>
				<li>confirm.tmpl - <?php _e('This email is sent with the activation-link in it.', 'post_notification'); ?></li>
				<li>select.tmpl - <?php _e('The page you see to select your categories.', 'post_notification'); ?></li>
				<li>unsubscribe.tmpl - <?php _e('This email is sent in case someone unsubscribes.', 'post_notification'); ?></li>
				<li>strings.php - <?php _e('In this file all the other strings needed are saved.', 'post_notification'); ?></li>
	</ul>
		</p>
	</li>

	<li>
		<strong><?php _e('Bugs', 'post_notification'); ?></strong>
		<p><?php _e('Please report bugs. Finding bugs is much more difficult then fixing them.', 'post_notification'); ?> <br />
			<b><a
		href="http://pn.xn--strbe-mva.de/forum.php?req=main&id=5"><?php _e('Report a bug in English or German (Forum).', 'post_notification'); ?></a>
		</b></p>
		
	</li>
	
	
	<li><strong><?php _e('History', 'post_notification'); ?></strong>
	<p><?php _e('The Plugin on which this plugin originally is based was written by <a href="http://watershedstudio.com/portfolio/software/wp-email-notification.html">Brian Groce</a>. <a href="http://bueltge.de">Frank B&uuml;ltge</a> translated it to german and maintained it for a while. Major parts were rewritten and it is now maintained by <a href="http://xn--strbe-mva.de">Moritz Str&uuml;be</a>.', 'post_notification'); ?></p>
	</li>
	
	<li><strong><?php _e('Donations', 'post_notification'); ?></strong>
	<p><?php _e('There are several ways to donate:', 'post_notification'); ?></p>
	<ul>
		<li><?php _e('Donate time: Find and report bugs. If you know php: fix them and mail the fix to me. If you are interested you can also get svn-access.', 'post_notification'); ?></li>
		<li><?php _e('Donate motivation: Send me a postcard (with snailmail), make a comment on my page how cool the plugin is, or anything else that might motivate me.', 'post_notification'); ?></li>
		<li><?php _e('Donate money:', 'post_notification'); ?> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=morty%40gmx%2ede&item_name=Post%20Notification&no_shipping=1&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8">Paypal </a></li>
	</ul>
	</li>
	<li><strong><?php _e('Supporters', 'post_notification'); ?></strong>
	<ul>
		<li>Michel Scriban: <?php _e('French translation', 'post_notification'); ?></li>
		<li><a href="http://caspi.org.il/">Eitan Caspi</a>: <?php _e('Hebrew translation', 'post_notification'); ?></li>
		<li><a href="http://www.werkgroepen.net/wordpress/vertaald-plugin-post-notification/">Anja</a>: <?php _e('Dutch translation', 'post_notification'); ?></li>
		<li><a href="http://www.christian.sasse.com/">Christian Sasse</a>: <?php _e('Testing and bugs', 'post_notification'); ?></li>
		
	</ul>
	</li>
</ol>
<?php
} ?>
