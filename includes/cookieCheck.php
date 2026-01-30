<?php
require ("dbconnect.php");
$UserID_Cookie = $_SESSION["user_id"];
$CookieID = $_SESSION["cookie_id"];

if (isset($_SESSION["user_id"])){
    $UserID_Cookie = $_SESSION["user_id"];
}else{
    header('refresh:0; url=/index.php?error=2');
}

if (isset($_SESSION["cookie_id"])){
    $CookieID = $_SESSION["cookie_id"];

}else{
    header('refresh:0; url=/index.php?error=3');
}
$CookieValidQuery = "SELECT * FROM user_logins WHERE cookie_id=('$CookieID')";
$CValidRQuery = $conn->query($CookieValidQuery);
while($CValidResult = mysqli_fetch_array($CValidRQuery)){
   $cookie_valid_uid = $CValidResult["cookie_id"];
   $cookie_valid_num = $CValidResult["is_valid"];
}
if (!$cookie_valid_uid){
    header('refresh:0; url=/index.php?error=1');
}
if ($cookie_valid_num == "0"){
    header('refresh:0; url=/index.php?error=4');
}
?>