<?php
global $wpdb, $user_ID;
$curgateway = get_option('payment_gateway');
$sessionid = $_GET['sessionid'];
$errorcode = '';
$transactid = '';
 if ($_REQUEST['payflow']=='1') {
	echo $_SESSION['payflow_message'];
	$_SESSION['payflow_message']='';
}
if($_REQUEST['act']=='error'){
	session_start();
	$resArray=$_SESSION['reshash']; 
	?>
	
	<html>
	<head>
	<title>PayPal PHP API Response</title>
	</head>
	<center>
	
	<table width="700" align="left">
	<tr>
			<td colspan="2" class="header">The PayPal API has returned an error!</td>
		</tr>
	
	<?php  //it will print if any URL errors 
		if(isset($_SESSION['curl_error_no'])) { 
				$errorCode= $_SESSION['curl_error_no'] ;
				$errorMessage=$_SESSION['curl_error_msg'] ;	
				$response = $_SESSION['response'];
				session_unset();	
	?>
	
	<tr>
			<td>response:</td>
			<td><?php echo $response; ?></td>
		</tr>
	   
	<tr>
			<td>Error Number:</td>
			<td><?= $errorCode ?></td>
		</tr>
		<tr>
			<td>Error Message:</td>
			<td><?= $errorMessage ?></td>
		</tr>
		
		</center>
		</table>
	<?php } else {
	
	/* If there is no URL Errors, Construct the HTML page with 
	   Response Error parameters.   
	   */
	?>
	
			<td>Ack:</td>
			<td><?= $resArray['ACK'] ?></td>
		</tr>
		<tr>
			<td>Correlation ID:</td>
			<td><?= $resArray['CORRELATIONID'] ?></td>
		</tr>
		<tr>
			<td>Version:</td>
			<td><?= $resArray['VERSION']?></td>
		</tr>
	<?php
		$count=0;
		while (isset($resArray["L_SHORTMESSAGE".$count])) {		
			  $errorCode    = $resArray["L_ERRORCODE".$count];
			  $shortMessage = $resArray["L_SHORTMESSAGE".$count];
			  $longMessage  = $resArray["L_LONGMESSAGE".$count]; 
			  $count=$count+1; 
	?>
		<tr>
			<td>Error Number:</td>
			<td><?= $errorCode ?></td>
		</tr>
		<tr>
			<td>Short Message:</td>
			<td><?= $shortMessage ?></td>
		</tr>
		<tr>
			<td>Long Message:</td>
			<td><?= $longMessage ?></td>
		</tr>
		
	<?php }//end while
	}// end else
	?>
	</center>
		</table>
<?php 
} else if($_REQUEST['act']=='do') {
	session_start();
	
	/* Gather the information to make the final call to
	   finalize the PayPal payment.  The variable nvpstr
	   holds the name value pairs
	   */
	$token =urlencode( $_SESSION['token']);
	$paymentAmount =urlencode ($_SESSION['paymentAmount']);
	$paymentType = urlencode($_SESSION['paymentType']);
	$currCodeType = urlencode($_SESSION['currCodeType']);
	$payerID = urlencode($_SESSION['payer_id']);
	$serverName = urlencode($_SERVER['SERVER_NAME']);
	$BN='Instinct_e-commerce_wp-shopping-cart_NZ';
	
	$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName."&BUTTONSOURCE=".$BN ;
	
	 /* Make the call to PayPal to finalize payment
		If an error occured, show the resulting errors
		*/
	$resArray=hash_call("DoExpressCheckoutPayment",$nvpstr);
	
	/* Display the API response back to the browser.
	   If the response from PayPal was a success, display the response parameters'
	   If the response was an error, display the errors received using APIError.php.
	   */
	$ack = strtoupper($resArray["ACK"]);
	
	
	if($ack!="SUCCESS"){
		$_SESSION['reshash']=$resArray;
		$location = get_option('transact_url')."&act=error";
			 header("Location: $location");
				   }
	
	?>
		<table width ='400'>
			
			<tr>
				<td >
					Transaction ID:</td>
				<td><?=$resArray['TRANSACTIONID'] ?></td>
			</tr>
			<tr>
				<td >
					Amount:</td>
				<td><?=$currCodeType?> <?=$resArray['AMT'] ?></td>
			</tr>
		</table>

<?php 
} else if(isset($_REQUEST['paymentType'])){
	$token = $_REQUEST['token'];
	if(! isset($token)) {
		/* The servername and serverport tells PayPal where the buyer
		   should be directed back to after authorizing payment.
		   In this case, its the local webserver that is running this script
		   Using the servername and serverport, the return URL is the first
		   portion of the URL that buyers will return to after authorizing payment
		   */
	
		   $paymentAmount=$_REQUEST['paymentAmount'];
		   $currencyCodeType=$_REQUEST['currencyCodeType'];
		   $paymentType=$_REQUEST['paymentType'];
	
		 /* The returnURL is the location where buyers return when a
			payment has been succesfully authorized.
			The cancelURL is the location buyers are sent to when they hit the
			cancel button during authorization of payment during the PayPal flow
			*/
		   if(get_option('permalink_structure') != '')
			{
			$seperator ="?";
			}
			else
			  {
			  $seperator ="&";
			  }
		   $returnURL =urlencode(get_option('transact_url').$seperator.'currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&paymentAmount='.$paymentAmount);
		   $cancelURL =urlencode(get_option('transact_url').$seperator.'paymentType=$paymentType' );
	
		 /* Construct the parameter string that describes the PayPal payment
			the varialbes were set in the web form, and the resulting string
			is stored in $nvpstr
			*/
		  
		   $nvpstr="&Amt=".$paymentAmount."&PAYMENTACTION=".$paymentType."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType;
		 
		 /* Make the call to PayPal to set the Express Checkout token
			If the API call succeded, then redirect the buyer to PayPal
			to begin to authorize payment.  If an error occured, show the
			resulting errors
			*/
		   $resArray=hash_call("SetExpressCheckout",$nvpstr);
		   $_SESSION['reshash']=$resArray;
	
		   $ack = strtoupper($resArray["ACK"]);
	
		   if($ack=="SUCCESS"){
			// Redirect to paypal.com here
			$token = urldecode($resArray["TOKEN"]);
			$payPalURL = PAYPAL_URL.$token;
			header("Location: ".$payPalURL);
		  	} else  {
			 //Redirecting to APIError.php to display errors. 
				$location = get_option('transact_url')."&act=error";
				header("Location: $location");
			}
			exit();
	} else {
	 /* At this point, the buyer has completed in authorizing payment
		at PayPal.  The script will now call PayPal with the details
		of the authorization, incuding any shipping information of the
		buyer.  Remember, the authorization is not a completed transaction
		at this state - the buyer still needs an additional step to finalize
		the transaction
		*/
	
	   $token =urlencode( $_REQUEST['token']);
	
	 /* Build a second API request to PayPal, using the token as the
		ID to get the details on the payment authorization
		*/
	   $nvpstr="&TOKEN=".$token;
	
	 /* Make the API call and store the results in an array.  If the
		call was a success, show the authorization details, and provide
		an action to complete the payment.  If failed, show the error
		*/
	   $resArray=hash_call("GetExpressCheckoutDetails",$nvpstr);
	   $_SESSION['reshash']=$resArray;
	   $ack = strtoupper($resArray["ACK"]);
	
	   if($ack=="SUCCESS"){			
			?>
			
			<?php
/********************************************************
GetExpressCheckoutDetails.php

This functionality is called after the buyer returns from
PayPal and has authorized the payment.

Displays the payer details returned by the
GetExpressCheckoutDetails response and calls
DoExpressCheckoutPayment.php to complete the payment
authorization.

Called by ReviewOrder.php.

Calls DoExpressCheckoutPayment.php and APIError.php.

********************************************************/


session_start();

/* Collect the necessary information to complete the
   authorization for the PayPal payment
   */

$_SESSION['token']=$_REQUEST['token'];
$_SESSION['payer_id'] = $_REQUEST['PayerID'];

$_SESSION['paymentAmount']=$_REQUEST['paymentAmount'];
$_SESSION['currCodeType']=$_REQUEST['currencyCodeType'];
$_SESSION['paymentType']=$_REQUEST['paymentType'];

$resArray=$_SESSION['reshash'];

if(get_option('permalink_structure') != '')
{
$seperator ="?";
}
else
  {
  $seperator ="&";
  }

/* Display the  API response back to the browser .
   If the response from PayPal was a success, display the response parameters
   */

?>
	<form action=<?php echo get_option('transact_url')?> method="post">
           <table width='400'>
            <tr>
                <td align="left"><b>Order Total:</b></td>
                <td align="left">
                  <?=$_REQUEST['currencyCodeType'] ?> <?=$_REQUEST['paymentAmount']?></td>
            </tr>
			<tr>
			    <td align="left"><b>Shipping Address: </b></td>
			</tr>
            <tr>
                <td align="left">
                    Street 1:</td>
                <td align="left">
                   <?=$resArray['SHIPTOSTREET'] ?></td>

            </tr>
            <tr>
                <td align="left">
                    Street 2:</td>
                <td align="left"> <?=$resArray['SHIPTOSTREET2'] ?>
                </td>
            </tr>
            <tr>
                <td align="left">
                    City:</td>

                <td align="left">
                    <?=$resArray['SHIPTOCITY'] ?></td>
            </tr>
            <tr>
                <td align="left">
                    State:</td>
                <td align="left">
                    <?=$resArray['SHIPTOSTATE'] ?></td>
            </tr>
            <tr>
                <td align="left">
                    Postal code:</td>

                <td align="left">
                    <?=$resArray['SHIPTOZIP'] ?></td>
            </tr>
            <tr>
                <td align="left">
                    Country:</td>
                <td align="left">
                     <?=$resArray['SHIPTOCOUNTRYNAME'] ?></td>
            </tr>
            <tr>
                <td>
					<input type="hidden" name="act" value="do" />
                     <input type="submit" value="Pay" />
                </td>
            </tr>
        </table>
    </center>
    </form>

</body>
</html>

			
			
			<?php		 
		  } else  {
			//Redirecting to APIError.php to display errors. 
			$location = "APIError.php";
			header("Location: $location");
		  }
	}
} else {

	if(function_exists('decrypt_dps_response') && !is_numeric($sessionid)) {
	  $sessionid = decrypt_dps_response();
	}
	
	echo transaction_results($sessionid, true);
}
?>