<?php
session_start();
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require "$DocRoot/includes/dbconnect.php";
require "$DocRoot/BackPhp/GetUserInfo.php";
require "$DocRoot/BackPhp/driveInfo.php";
require "$DocRoot/phpmailer/phpmailer/src/Exception.php";
require "$DocRoot/phpmailer/phpmailer/src/PHPMailer.php";
require "$DocRoot/phpmailer/phpmailer/src/SMTP.php";
require "$DocRoot/phpmailer/phpmailer/src/settings.php";

$theme = $_COOKIE["SiteTheme"];
?>
<link rel='stylesheet' href='/includes/CssFiles/main.css'>
<meta name="viewport" content="width=device-width, initial-scale=1" />