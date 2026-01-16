<?php
$UserCookie = $_COOKIE["user_id"];
$CookieID = $_COOKIE["cookie_id"];
$DocRoot= $_SERVER["DOCUMENT_ROOT"];



   //             setcookie("user_id", "", time()-(86400 * 10), "/");
     //           setcookie("cookie_id", "", time()-(86400 * 10), "/");
 
     session_destroy();
     header('Location:/index.php');

?>