<?php

require("class.phpmailer.php");

define("MAIL_HOST", 'smtp.exmail.qq.com');
define("MAIL_USERNAME", 'dale.wu@joyplus.tv');
define("MAIL_PWD", 'osca@WSXzaq1');

function sendMail($toArray,$subject,$body){
	$mail = new PHPMailer();
	$mail->IsSMTP(); 
	                                    // set mailer to use SMTP
	$mail->Host = MAIL_HOST;  // specify main and backup server
	$mail->SMTPAuth = true;     // turn on SMTP authentication
	$mail->Username = MAIL_USERNAME;  // SMTP username
	$mail->Password = MAIL_PWD; // SMTP password
	
	$mail->From = "dale.wu@joyplus.tv";
	$mail->FromName = "CMS";
	foreach ($toArray as $to){
	  $mail->AddAddress($to); 
	} 
	$mail->IsHTML(true);                                  // set email format to HTML
	
	$mail->Subject = $subject;
	$mail->Body    = $body;
	if(!$mail->Send())
	{
	   echo "Message could not be sent. <p>";
	   echo "Mailer Error: " . $mail->ErrorInfo;
	   exit;
	}
	echo "Message has been sent";

}


?>