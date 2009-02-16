
<!-- Add subscription Form  -->
<h2>Get notified of new posts:</h2>
<form id="newsletter" method="post" action="<?php echo post_notification_get_link(); ?>" style="text-align:left">
	<p>email: <input type="text" name="addr" size="25" maxlength="50" value="<?php echo post_notification_get_addr(); ?>"/> </p>
	<input type="submit" name="submit" value="Submit" /></p>

</form>


<!-- Show number of subscribers 
post_notification_get_subscribers() will return the number of subscribers as integer.
-->
<h2>Number of Mail-Subscribers<h2> 
<?php echo post_notification_get_subscribers(); ?>


