Send Bulk OTP SMS in PHP using SMS Gateway Center HTTP API

You need to be registered member with https://www.smsgatewaycenter.com/ and then you can use this script to send OTP SMS to your clients and peers.

This script is just an example to send sms and validate OTP using session. But you can use it with your database to get users and send out OTP SMS to registered members and have them validated.

About SMSGatewayCenter.com
SMS Gateway Center is one of the leading Bulk SMS Gateway Provider in India.We are offering our white label Bulk SMS Reseller Program across India. You can Send SMS from our SMS Web Panel, SMS Gateway Center Excel SMS, Toll-Free SMS modules. We also provide Short Code SMS and Long Code SMS services to all our bulk SMS clients with Two Way SMS options. To register please visit, https://www.smsgatewaycenter.com/registration.php

Things to Edit:
You need to edit and add your login and sender name credentials which is approved from SMSGatewayCenter.com

	$smsgatewaycenter_com_user = "YourUsername"; //Your SMS Gateway Center Account Username
	$smsgatewaycenter_com_password = "********";  //Your SMS Gateway Center Account Password
	$smsgatewaycenter_com_mask = "SGCSMS"; //Your Approved Sender Name / Mask
