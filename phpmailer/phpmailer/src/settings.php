<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
//Create a new PHPMailer instance
$mail = new PHPMailer();
//Tell PHPMailer to use SMTP
$mail->isSMTP();
$mail->isHTML(TRUE);
//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
//Set the hostname of the mail server
$mail->Host = '';
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 587;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = '';
//Password to use for SMTP authentication
$mail->Password = '';
//Set who the message is to be sent from
//Set the subject line
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
//Replace the plain text body with one created manually
//$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//SMTP XCLIENT attributes can be passed with setSMTPXclientAttribute method
//$mail->setSMTPXclientAttribute('LOGIN', 'yourname@example.com');
//$mail->setSMTPXclientAttribute('ADDR', '10.10.10.10');
//$mail->setSMTPXclientAttribute('HELO', 'test.example.com');

//send the message, check for errors

?>