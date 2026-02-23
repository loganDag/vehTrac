<?php
session_start();
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require "$DocRoot/includes/dbconnect.php"; 
$UserCookie = $_SESSION["user_id"];
$CookieID = $_SESSION["cookie_id"];
$DocRoot= $_SERVER["DOCUMENT_ROOT"];

$NewValStat= "0";

$stmt = $conn->prepare("UPDATE user_logins SET is_valid=? WHERE cookie_id=?");
$stmt->bind_param("ss", $NewValStat, $CookieID);
$stmt->execute();
$stmt->get_result();

session_unset();
session_destroy();

     header('Location:/index.php');

?>