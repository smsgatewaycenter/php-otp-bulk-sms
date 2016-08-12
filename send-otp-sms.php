<?php
/**************************************************************************************************
| SmsGatewayCenter.com
| httpw://www.SmsGatewayCenter.com
| contact@SmsGatewayCenter.com.com
|**************************************************************************************************
| By using this software you agree that you have read and acknowledged our End-User License 
| Agreement and to be bound by it.
|
| Copyright (c) 2010 - 2016 SmsGatewayCenter.com. All rights reserved.
|**************************************************************************************************/
	/////////////////////////////////////////////////////////////////////////////
	//      SmsGatewayCenter.com One Time Password (OTP) Example               //
	//      This script executes OTP and sends an SMS to authenticate user     //
	/////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////
	//             Lets start the session                                      //
	//             and end it in 6 minutes                                     //
	/////////////////////////////////////////////////////////////////////////////
	session_start();
	session_set_cookie_params(360);
	
	/////////////////////////////////////////////////////////////////////////////
	//   Since we are not using database call, lets create users array         //
	//   with their respective phone numbers to send out OTP to their mobile   //
	/////////////////////////////////////////////////////////////////////////////
	$getuser = array('user1' => '123456','user2' => '234567','user3' => '345678',);
	$getmobile = array('user1' => '9930447726','user2' => '9769818858','user3' => '9619141191',);
	
	/////////////////////////////////////////////////////////////////////////////
	//            Your Login Credentials with SMSGatewayCenter.com             //
	/////////////////////////////////////////////////////////////////////////////
	$smsgatewaycenter_com_user = "YourUsername"; //Your SMS Gateway Center Account Username
	$smsgatewaycenter_com_password = "********";  //Your SMS Gateway Center Account Password
	$smsgatewaycenter_com_url = "https://www.smsgatewaycenter.com/library/send_sms_2.php?"; //SMS Gateway Center API URL
	$smsgatewaycenter_com_mask = "SGCSMS"; //Your Approved Sender Name / Mask
	
	/////////////////////////////////////////////////////////////////////////////
	// Function to Initiate SMS Message with SMS Gatewaycenter.com using CURL  //
	/////////////////////////////////////////////////////////////////////////////
	function smsgatewaycenter_com_Send($mobile, $sendmessage, $debug=false){
		global $smsgatewaycenter_com_user,$smsgatewaycenter_com_password,$smsgatewaycenter_com_url,$smsgatewaycenter_com_mask;
		$parameters = 'UserName='.$smsgatewaycenter_com_user;
		$parameters.= '&Password='.$smsgatewaycenter_com_password;
		$parameters.= '&Type=Individual';
		$parameters.= '&Language=English';
		$parameters.= '&Mask='.$smsgatewaycenter_com_mask;
		$parameters.= '&To='.urlencode($mobile);
		$parameters.= '&Message='.urlencode($sendmessage);
		$apiurl =  $smsgatewaycenter_com_url.$parameters;
		$ch = curl_init($apiurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$curl_scraped_page = curl_exec($ch);
		curl_close($ch);
		
		if ($debug) {
			echo "Response: <br><pre>" . $curl_scraped_page . "</pre><br>";
		}
		return($curl_scraped_page);
	}

	/////////////////////////////////////////////////////////////////////////////
	//      Function to generate and append OTP code within the message        //
	/////////////////////////////////////////////////////////////////////////////
	function smsgatewaycenter_com_OTP($length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789'){
		$chars_length = (strlen($chars) - 1);
		$string = $chars{rand(0, $chars_length)};
		for ($i = 1; $i < $length; $i = strlen($string)){
			$r = $chars{rand(0, $chars_length)};
			if ($r != $string{$i - 1}) $string .=  $r;
		}
		return $string;
	}

	/////////////////////////////////////////////////////////////////////////////
	//           If Debug is set to true below, then response from             //
	//         SMSGatewayCenter.com API will be printed on the screen          //
	/////////////////////////////////////////////////////////////////////////////
	$debug = false; //Set to true if you want to see the response
	
	/////////////////////////////////////////////////////////////////////////////
	//     If user has not posted anything, lets load the user login page      //
	/////////////////////////////////////////////////////////////////////////////
	
	if (empty($_POST)){
		$i = 0;
		echo '   
		<html>
			<body>
				<h1>One Time Password Form</h1>
				<form method="POST">
				<table border=1>
					<tr>
						<td>Username:</td>
						<td><input type="text" id="username" name="username"></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" id="password" name="password"></td>
					</tr>
					<tr>
						<td> </td>
						<td><input type=submit name="sendsms" value="Get OTP" OnClick="smsgatewaycenter_com_Send(this.form);"></td>
					</tr>
				</table>
				</form>
			</body>
		</html>';
	}

	/////////////////////////////////////////////////////////////////////////////
	//   If form has been submitted by the user, lets create OTP or validate   //
	/////////////////////////////////////////////////////////////////////////////
	
	if (isset($_POST['sendsms'])){
		$_SESSION['smsgatewaycenterotp'] = smsgatewaycenter_com_OTP(); //Generate OTP
		/////////////////////////////////////////////////////////////////////////////
		//             Lets validate user credentials with getuserarray            //
		/////////////////////////////////////////////////////////////////////////////
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if ($password != $getuser[$_POST['username']] || ((empty($_POST['username']) && (!empty($_POST['password'])))) || (empty($_POST['password']) && (!empty($_POST['username'])))){
			echo 'Please enter a valid username or password!'; //username and password does not match
		}
		elseif ((!empty($_POST['submit'])) && (empty($_POST['password'])) && (empty($_POST['username']))){
			echo 'No username or password entered'; //Empty form submitted
		}
		elseif ($password == $getuser[$_POST['username']]){
			/////////////////////////////////////////////////////////////////////////////            
			// User has successfully validated credentials, lets append OTP and send   //
			// and send the SMS to recipient's mobile number to authenticate OTP       //
			/////////////////////////////////////////////////////////////////////////////       
			smsgatewaycenter_com_Send($getmobile[$_POST['username']],'Dear '.$username.'! Please authenticate your OTP. Your One Time password is: '.$_SESSION['smsgatewaycenterotp'],$debug);
			echo '
			<html>
				<body>
					<h1>Authenticate OTP (One Time Password)</h1>
					<p>We have sent an SMS to your registered phone number, please authenticate your one time password entering below.</p>
					<form method="POST">
					<table border=1>
						<tr>
							<td>Your one time password (OTP):</td>
							<td><input type="text" name="getsmsgatewaycenterotp"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type=submit name="submitotp" value="Authenticate OTP"></td>
						</tr>
					</table>
					</form>
				</body>
			</html>';
		}
	}
	elseif (isset($_POST['submitotp'])){
		///////////////////////////////////////////////////////////////////////////// 
		//      Lets validate user's OTP and validate with the stored session      //
		/////////////////////////////////////////////////////////////////////////////
		$sgc_otp = $_POST['getsmsgatewaycenterotp'];
		if($_SESSION['smsgatewaycenterotp'] == $sgc_otp){
			echo '
			<html>
				<body>
					<h2>You\'ve been successfully verified your One-Time Password</h2>
				</body>
			</html>';
		} else { 
			echo'
			<html>
				<body>
					<h2>Wrong Password!</h2>
				</body>
			</html>';
		}
	}
