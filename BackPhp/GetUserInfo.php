<?php
$UserID_Cookie = $_SESSION["user_id"];
$UserInfoQuery = "SELECT * FROM user_info WHERE user_uid=('$UserID_Cookie')";
$GetInfoQuery= $conn->query($UserInfoQuery);
while($UserInfo = mysqli_fetch_array($GetInfoQuery)){
   $user_display_name = $UserInfo["name"];
   $user_app_id = $UserInfo["user_uid"];
}
?>