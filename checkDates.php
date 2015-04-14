<?php
/* for testing
  use test credit card number 280 (uncomment)
  use test hmackey and keyid 286, 287
  use ExactID and Password 312, 314
  use demo in URL 350
  make sure there are no live records within the date range
*/
$d = strtotime("+1 Weeks");
$future = date("Y-m-d", $d);
$today = date("Y-m-d");
$year = date("Y");
$targets = array();
$messageLog = array();
$messageLog[] = "Date range from today: $today to +1 week: $future <br><br>";
echo "Date range from today: $today to +1 week: $future <br><br>";

$conn = new mysqli('localhost', 'database_user', 'xxxxxxxx', 'ddatabase_name');
if(!$conn){
	die('Database Connection Error' . $conn->connect_error);
}

// Get all table records

$sql = "SELECT * FROM xxxxxx_hosting_info";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        //echo "id: " . $row["id"]. " - Username: " . $row["hiUsername"] . " - joindate: " . $row["joindate"] . "<br>";
        $renewal = substr_replace($row["joindate"], $year, 0, 4);
        $d=mktime(0, 0, 0, $row["expiremm"], 1, "20".$row["expireyy"]);
				$expireDate = date("Y-m-d", $d);
				$data = array();
				$data[] = $renewal;       								// 0 is renewal date
				$data[] = $expireDate;										// 1 is expire date
				$data[] = $row["plantype"];								// 2 is plan type
				$data[] = $row["planAmount"];							// 3 is plan amount
				$data[] = $row["address1"];								// 4 is address1
				$data[] = $row["address2"];								// 5 is address2
				$data[] = $row["city"];										// 6 is city
				$data[] = $row["state"];									// 7 is state
				$data[] = $row["zipcode"];								// 8 is zipcode
				$data[] = $row["cardnumber"];							// 9 is cardnumber
				$data[] = $row["domain"];									// 10 is domain
				$data[] = $row["cvdcode"];								// 11 is cvdcode
				echo "renewal: " . $renewal . " future: " . $future . " expireDate: " . $expireDate . "<br>";
				// builds and array of dates that fall within range
        if ($renewal >= $today and $renewal <= $future) { $targets[$row["hiUsername"]] = $data; }
    }
} else {
		$messageLog[] = "NO results found" . $conn->error;
    echo "NO results found" . $conn->error;
}

$conn->close();

//echo count($targets);
foreach ($targets as $key => &$value) {
 	 //echo "User found in date range: " . $key . " Renewal: " . $value[0] . " Expire: "  . $value[1] . "<br>"; 
 	 // The expire date cannot be less than the renewal date or the card is expired
 	 if ($value[0] >= $value[1]) {
 	 		$messageLog[] = "***** User: " . $key . " - Expire date: " . $value[1] . " email sent<br>";
 	 	  echo "***** User: " . $key . " - Expire date: " . $value[1] . " email sent<br>";
 	 	  sendExpireEmail($key, $value[0], $value[1], $value[2], $value[3]);
 	 } else {
 	 		if ($value[0] == $future) {
 	 			$messageLog[] = "User: " . $key . " - Renewal date: " . $value[0] . " announcement of renewal email sent<br>";
 	  		echo "User: " . $key . " - Renewal date: " . $value[0] . " announcement of renewal email sent<br>";
 	  		sendNoticeEmail($key, $value[0], $value[1], $value[2], $value[3]);
 	  	}	elseif ($value[0] == $today) {
 	  		$messageLog[] = "User: " . $key . " - Renewal date: " . $value[0] . " billing happens today and sent invoice<br>";
 	  		echo "User: " . $key . " - Renewal date: " . $value[0] . " billing happens today and sent invoice<br>";
 	  		sendInvoiceBilling($key, $value[0], $value[1], $value[2], $value[3], $value[4], $value[5], $value[6], $value[7], $value[8], $value[9], $value[10], $value[11]);
 	  	}
 	 }
}

sendLogEmail($messageLog);

// end of program
//-------------------------------------------------------------------------

function sendExpireEmail($username, $renewal, $expire, $plan, $amount) {
	//echo "userid: " . $username . "<br>"; 
 	$auser = getName($username);
	$name = $auser['name'];
	$email = $auser['email'];
	//echo "name: " . $name . " email: " . $email . "<br>";
	$d=strtotime($renewal);
	$renewMDY = date("m/d/Y", $d);
	$message = "<html>";
  $message .= "<head>";
  $message .= "<title>Delco Website Design - Card Expired Notice</title>";
  $message .= '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
  $message .= "</head>";
  $message .= "<body>";
  $message .=	  '<table cellSpacing="0" cellPadding="0" width="800" align="left" border="0">';
  $message .= 		'<tr><td style="padding: 15px 0 30px 15px; font-size: 16px;">Dear <b>' . $name . '</b>,</td></tr>';
  $message .=   	'<tr><td style="padding-left: 30px; font-size: 16px;"> Your &nbsp;<b>' . $plan . '</b>&nbsp; website management and hosting plan for the amount of &nbsp;<b>$' . $amount . '</b> </td></tr>'; 
  $message .=     '<tr><td style="padding: 5px 0 0 30px; font-size: 16px;">is scheduled for renewal on <b>' . $renewMDY . '</b>.&nbsp; However <span style="background-color: #ffffbb;">your credit card has a past expiration date</span>.<br/><br/> </td></tr>';
  $message .=   	'<tr><td style="padding: 5px 0 0 30px; font-size: 16px;">To avoid disruption of service please login to your account at <a href="https://delcowebsitedesign.com/client-area/login-out">Client Login</a> to update your</td></tr>'; 
  $message .=     '<tr><td style="padding: 5px 0 0 30px; font-size: 16px;">cards expiration date. &nbsp;Then please proceed to our <a href="https://delcowebsitedesign.com/client-area/billing-info">Billing Info</a> page, update these values and</td></tr>';
  $message .=     '<tr><td style="padding: 5px 0 0 30px; font-size: 16px;">press "Update Details".</td></tr>';  
  $message .=     '<tr><td style="padding: 30px 0 0 15px; font-size: 16px;"><span style="background-color: #ffffbb;">Thank you for your prompt attention to this matter</span>,<br><b>DelcoWebsiteDesign.com</b> &nbsp;610-833-8900</td></tr>';
  $message .=   "</table>";
  $message .= "</body>";
  $message .= "</html>";
  $subject = "Delco Website Design - Card Expired Notice";
  $to = $email;
  $headers = "From: Delco Web Hosting <admin@delcowebhosting.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);

}

function sendNoticeEmail($username, $renewal, $expire, $plan, $amount) {
 	$auser = getName($username);
	$name = $auser['name'];
	$email = $auser['email'];
	//echo "name: " . $name . " email: " . $email . "<br>";
	$d=strtotime($renewal);
	$renewMDY = date("m/d/Y", $d);
	$message = "<html>";
  $message .= "<head>";
  $message .= "<title>Delco Website Design - Billing Notice</title>";
  $message .= '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
  $message .= "</head>";
  $message .= "<body>";
  $message .=	  '<table cellSpacing="0" cellPadding="0" width="800" align="left" border="0">';
  $message .= 		'<tr><td style="padding: 15px 0 30px 15px; font-size: 16px;">Hello <b> ' . $name . ' </b>,</td></tr>';
  $message .=     '<tr><td style="padding-left: 30px; font-size: 16px;">Your &nbsp;<b>' . $plan . '</b>&nbsp; website management and hosting plan for the amount of &nbsp;<b>$' . $amount . '</b> </td></tr>';
  $message .=   	'<tr><td style="padding: 5px 0 0 30px; font-size: 16px;">is scheduled for renewal on <b>' . $renewMDY . '</b>. &nbsp;On that date your account will be </td></tr>'; 
  $message .=     '<tr><td style="padding: 5px 0 0 30px; font-size: 16px;">billed and you will receive and email invoice.</td></tr>';
  $message .=     '<tr><td style="padding: 30px 0 0 15px; font-size: 16px;">Thank you for your business,<br>DelcoWebsiteDesign.com &nbsp;610-833-8900</td></tr>';
  $message .=   "</table>";
  $message .= "</body>";
  $message .= "</html>";
  $subject = "Delco Website Design - Billing Notice";
  $to = $email;
  $headers = "From: Delco Web Hosting <admin@delcowebhosting.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);
}

function sendInvoiceBilling($username, $renewal, $expire, $plan, $amount, $address1, $address2, $city, $state, $zipcode, $numb_encoded, $domain, $cvdcode) {
 	$auser = getName($username);
	$name = $auser['name'];
	$email = $auser['email'];
	$d=strtotime($renewal);
	$renewMDY = date("m/d/Y", $d);
	$d=strtotime("+1 Years");
	$renewUntil = date("m/d/Y", $d);
	$numb = getNumb($numb_encoded);
	$numbXXXX = substr($numb, 12, 4);
	//echo "strlen of $numbXXXX: " . strlen($numbXXXX) . "<br/>";
	$expiremonth = substr($renewMDY, 0, 2);
	$expireyear = substr($renewMDY, 8, 2);
	$errMsg = "invalid error";
	$errMsg = billToE4($username, $expiremonth,  $expireyear, $plan, $amount, $address1, $address2, $city, $state, $zipcode, $numb, $domain, $email, $name, $cvdcode);
	$msgOut = "<br/>&nbsp;" . $errMsg;
	//<span style="padding: 0 10px; background-color: #FF8888"> . "</span>"
	$message = "<html>";
  $message .= "<head>";
  $message .= "<title>&nbsp;</title>";
  $message .= '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
  $message .= "</head>";
  $message .= "<body>";
  $message .=	  '<table cellSpacing="0" cellPadding="0" width="800" align="left" style="border: 1px solid #000000; font-size: 16px; padding-bottom: 35px; margin: 15px 0 35px 0;">';
  $message .= 		'<tr><td style="padding: 15px 0 15px 5px;">Delco Website Design - Managed Plan Invoice<br/>Service Provided for: </td></tr>';
  $message .=     '<tr><td style="padding: 0 0 0 20px;"><table style="padding: 15px; border: 1px solid #000000;">';
  $message .=   		'<tr><td style="">' . $name . '</td></tr>';
  $message .=   		'<tr><td style="">' . $address1 . '</td></tr>';
  $message .=   		'<tr><td style="">' . $city . ', ' . $state . ' ' . $zipcode . '</td></tr>';
  $message .=   	"</table></td></tr>";
  $message .=   	'<tr><td style="padding: 15px 0 0 15px;">Service dates - from: ' . $renewMDY . ' to: ' . $renewUntil . '</td></tr>';
  $message .=   	'<tr><td style="padding: 15px 0 5px 15px;">Your Service Agreement: </td></tr>';
  $message .=   	'<tr><td style="padding: 0 0 0 20px;"><table width="700" style="font-size: 16px; margin-left: 15px; border: 1px solid #000000;">';
  $message .=   		'<tr ><td style="width: 80%; height: 50px; padding-left: 25px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;">1 Year of <b>' . $plan . '</b> Website Management Plan</td><td style="width: 20%; text-align: center; border-bottom: 1px solid #000000;">$' . $amount . '</td></tr>'; 
  $message .=   		'<tr ><td style="width: 80%; height: 100px; padding-left: 25px; border-right: 1px solid #000000;">' . ($errMsg ? "Error processing ***** Error in attempting" : "Services are according to explanation on our website") . '<br/>charge to card on file xxxxxxxxxxxx' . $numbXXXX . '</td><td style="width: 20%; text-align: center;"> &nbsp;</td></tr>';
	$message .= 			'<tr ><td style="width: 80%; height: 20px;  padding-left: 25px; border-right: 1px solid #000000; border-bottom: 1px solid #000000;' . ($errMsg ? "background-color: #ff7777;" : "") . '">' . $errMsg . '</td><td style="width: 20%; text-align: center; border-bottom: 1px solid #000000;">&nbsp;</td></tr>';
  $message .=   		'<tr ><td style="width: 80%; height: 50px; padding-left: 25px; border-right: 1px solid #000000;">TOTAL OF CHARGE </td><td style="width: 20%; text-align: center;">$' . ($errMsg ? "0" : $amount) . '</td></tr>';
 	$message .=   	"</table></td></tr>"; 
  $message .=   "</table>";
 	$message .= "</body>";
  $message .= "</html>";
  global $messageLog;
  $messageLog[] = "strlen of message: " . strlen($message) . " - the limit is 2000<br/>";
  echo "strlen of message: " . strlen($message) . " - the limit is 2000<br/>";
  $subject = "Delco Web Hosting - Website Managed Plan Invoice";
  $to = $email;
  $headers = "From: Delco Web Hosting <admin@delcowebhosting.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);
  //echo "name: " . $name . " email: " . $email . " message: " . $message . "<br>";
}

function sendLogEmail($messageLog) {

	$message = "<html>";
  $message .= "<head>";
  $message .= "<title>Daily Check Dates Log Report</title>";
  $message .= '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
  $message .= "</head>";
  $message .= "<body>";
  if (count($messageLog) > 1) {
  	foreach ($messageLog as &$value) {
  	 	 $message .= $value; 
  	} 
  } else {
  	$message .= $messageLog[0];
  	$message .= "No date processing today";
  }
  $message .= "</body>";
  $message .= "</html>";
  $subject = "Daily Check Dates Log Report";
  $to = "webmaster@delcowebsitedesign.com";
  $headers = "From: Delco Web Hosting <admin@delcowebhosting.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);

}

function getName($usernme) {

  $conn = new mysqli('localhost', 'databasse_user', 'xxxxxxx', 'database_name');
  if(!$conn){
  	die('Database Connection Error' . $conn->connect_error);
  }
  
  // Get cardnumber from database
  $sql = "SELECT name,email FROM xxxxxx_users WHERE username = '$usernme' LIMIT 1";
  
  $result = $conn->query($sql);
  
  if ($result->num_rows > 0) {
		// output data of each row
		$row = $result->fetch_assoc();
    //echo "name: " . $row["name"] . " - email: " . $row["email"] . "<br>";
    $auser = array(
    	"name" => $row["name"],
    	"email" => $row["email"]
    );

  } else {
    echo "NO results found" . $conn->error;
  }
  
  $conn->close();
  //echo "auser name: " . $auser["name"] . " auser email: " . $auser["email"] . "<br>";
  return $auser;

}

function getNumb($numb_encoded) {
	// decode, decrypt and show number
	$ciphertext = base64_decode($numb_encoded);
	# retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
	$key = pack('H*', "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx");
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  $iv = substr($ciphertext, 0, $iv_size);
    
  # retrieves the cipher text (everything except the $iv_size in the front)
  $numb_encrypt = substr($ciphertext, $iv_size);
  
	$numb = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $numb_encrypt, MCRYPT_MODE_CBC, $iv);
  return $numb;
}

function billToE4($username, $expiremonth,  $expireyear, $plantype, $planamount, $address1, $address2, $city, $state, $zipcode, $cardnumber, $domain, $email, $name, $cvdcode) {
	
	$invalid = array();  // initialize here because not under RSForm Pro
	$holdErrMessage = "";
	//echo "fields: Username: $username expiremonth: $expiremonth expireyear: $expireyear plantype: $plantype planamount: $planamount address1: $address1 address2: $address2 city: $city state: $state zipcode: $zipcode cardnumber: $cardnumber domain: $domain email: $email name: $name cvdcode: $cvdcode <br/>"; 
  //$cardnumber = "5500000000000004";                   // use this as valid test card number
  $holdMessage = "";
  
  class SoapClientHMAC extends SoapClient {
    public function __doRequest($request, $location, $action, $version, $one_way = NULL) {
    	global $context;
    	$hmackey = "xxxxxxxxxxxxxxxxxxxxxxxx"; 		// <-- Insert your HMAC key here live: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx  test: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    	$keyid = "xxxxxxxxxxxxx"; 																// <-- Insert the Key ID here live: xxxxxx  test: xxxxxx
    	$hashtime = date("c");
    	$hashstr = "POST\ntext/xml; charset=utf-8\n" . sha1($request) . "\n" . $hashtime . "\n" . parse_url($location,PHP_URL_PATH);
    	$authstr = base64_encode(hash_hmac("sha1",$hashstr,$hmackey,TRUE));
    	if (version_compare(PHP_VERSION, '5.3.11') == -1) {
    		ini_set("user_agent", "PHP-SOAP/" . PHP_VERSION . "\r\nAuthorization: GGE4_API " . $keyid . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request));
    	} else {
    		stream_context_set_option($context,array("http" => array("header" => "authorization: GGE4_API " . $keyid . ":" . $authstr . "\r\nx-gge4-date: " . $hashtime . "\r\nx-gge4-content-sha1: " . sha1($request))));
    	}
        return parent::__doRequest($request, $location, $action, $version, $one_way);
    }
    
    public function SoapClientHMAC($wsdl, $options = NULL) {
    	global $context;
    	$context = stream_context_create();
    	$options['stream_context'] = $context;
    	return parent::SoapClient($wsdl, $options);
    }
  }
  
  $trxnProperties = array(
    "User_Name"=>$username,
    "Secure_AuthResult"=>"",
    "Ecommerce_Flag"=>"2",													// specifies recurring ??????????????????
    "XID"=>"",
    "ExactID"=>"xxxxxxxxxxxxxxxx",				 									//Payment Gateway live: xxxxxxxx	test: xxxxxxxx
    "CAVV"=>"",
    "Password"=>"xxxxxxxxxxxxxxx",													//Gateway Password live: xxxxxxxx	test: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    "CAVV_Algorithm"=>"",
    "Transaction_Type"=>"00",												//Transaction Code I.E. Purchase="00" Pre-Authorization="01" etc.
    "Reference_No"=>"",
    "Customer_Ref"=>"managed-" . $plantype,					// plan type
    "Reference_3"=>$domain,													// domain
    "Client_IP"=>"",					                    	//This value is only used for fraud investigation.
    "Client_Email"=>$email,													//This value is only used for fraud investigation.
    "Language"=>"en",																//English="en" French="fr"
    "Card_Number"=>$cardnumber,		 	 	  						//For Testing, Use Test#s VISA="4111111111111111" MasterCard="5500000000000004" etc.
    "Expiry_Date"=>$expiremonth . $expireyear,			//This value should be in the format MM/YY.
    "CardHoldersName"=>$name,
    "Track1"=>"",
    "Track2"=>"",
    "Authorization_Num"=>"",
    "Transaction_Tag"=>"",
    "DollarAmount"=>$planamount,
    "VerificationStr1"=>$address1 . $address2 . '|'  . $zipcode . '|' . $city . '|' . $state . '|USA',
    "VerificationStr2"=>$cvdcode,						//$cvdcode,
    "CVD_Presence_Ind"=>"0",
    "Secure_AuthRequired"=>"",
    "Currency"=>"USD",
    "PartialRedemption"=>"",
    
    // Level 2 fields 
    "ZipCode"=>"",
    "Tax1Amount"=>"",
    "Tax1Number"=>"",
    "Tax2Amount"=>"",
    "Tax2Number"=>"",
    
    "SurchargeAmount"=>"",												//Used for debit transactions only
    "PAN"=>""
  );
  
  $client = new SoapClientHMAC("https://api.globalgatewaye4.firstdata.com/transaction/v12/wsdl");  		// test: api.demo.globalgatewaye4.  live: api.globalgatewaye4.
  $trxnResult = $client->SendAndCommit($trxnProperties);
  
  if(@$client->fault){
      // there was a fault, inform
      print "<B>FAULT:  Code: {$client->faultcode} <BR />";
      print "String: {$client->faultstring} </B>";
      $trxnResult["CTR"] = "There was an error while processing. No TRANSACTION DATA IN CTR!";
  }
  $holdError = "";
  $holdErrMessage = "";
  
  if ($trxnResult->EXact_Resp_Code != '00') {
  	 //$invalid[] = RSFormProHelper::getComponentId("cardnumber");							// ******************** COMMENT OUT THIS LINE FOR NATIVE PHP TESTING *********************
  	 $holdError = "EXact_Resp_Code: " . $trxnResult->EXact_Resp_Code;
  	 $holdErrMessage = " EXact_Message: " . $trxnResult->EXact_Message;
  	 $holdMessage = $trxnResult->EXact_Message;
  }
  //echo "<br/>got here too";
  //Uncomment the following commented code to display the full results.
  
  /*echo "<H3><U>Transaction Properties BEFORE Processing</U></H3>";
  echo "<TABLE border='0'>\n";
  echo " <TR><TD><B>Property</B></TD><TD><B>Value</B></TD></TR>\n";
  foreach($trxnProperties as $key=>$value){
     echo " <TR><TD>$key</TD><TD>:$value</TD></TR>\n";
  }
  echo "</TABLE>\n";
  
  echo "<H3><U>Transaction Properties AFTER Processing</U></H3>";
  echo "<TABLE border='0'>\n";
  echo " <TR><TD><B>Property</B></TD><TD><B>Value</B></TD></TR>\n";
  foreach($trxnResult as $key=>$value){
      $value = nl2br($value);
      echo " <TR><TD valign='top'>$key</TD><TD>:$value</TD></TR>\n";
  }
  echo "</TABLE>\n";*/
  
  
  // kill object
  
  unset($client);
  
  // Send e4gateway response message in email
  $message = "<html>";
  $message .= "<head>";
  $message .= "<title>VPOS - Sale</title>";
  $message .= '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
  $message .= "</head>";
  
  $message .= "<body>";
  $message .=    "<table>";
  $message .=	   "<tr><td>";
  $message .=	   		'<table cellSpacing="0" cellPadding="0" width="660" align="left" border="0">';
  $message .= 			"<tr>";
  $message .=					'<td><font face="verdana,arial,helvetica" size="5"><b>First Data Global Gateway e4 POS</b></font></td>';
  $message .= 			"</tr>";
  $message .=     	"</table>";
  $message .=     "</td></tr>";
  $message .=	    "<tr><td>";
  $message .=	    	'<table cellSpacing="6" cellPadding="0" width="660" align="left" border="2">';
  $message .=		        "<tr>";
  $message .=			        '<td align="left" valign="top">';
  						
  							foreach($trxnResult as $key=>$value){
  								if ($key == "CTR") {
  								    $value = nl2br($value);
  									$message .= $value;
  								}
  							}
  $message .=							"</td>";
  $message .=		        "</tr>";
  $message .=						"<tr><td>" . $holdError . "   " . $holdErrMessage. "</td></tr>";
  $message .=		        "<tr>";
  $message .=			        '<td align="center" valign="top">&nbsp;</td>';
  $message .=		        "</tr>";
  $message .=	    	"</table></td></tr>";
  $message .=    "</table>";
  $message .= "</body>";
  $message .= "</html>";
  $subject = "***** MANAGED PLAN PAYMENT *****";
  $to = "webmaster@delcowebsitedesign.com";
  $headers = "From: Delco Web Hosting <admin@delcowebhosting.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/html; charset=utf-8\r\n";
  mail($to, $subject, $message, $headers);
	
	return $holdMessage;

}