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

<!---HTML FILES FOR BOOTSTRAP BELOW TO SIMPLIFY LOADING-->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"79e61bce852e487ea0a4d350438d4a78","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>
