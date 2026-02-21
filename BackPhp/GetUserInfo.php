<?php
$UserID_Cookie = $_SESSION["user_id"];
$UserInfoQuery = $conn->prepare("SELECT * FROM user_info WHERE user_uid= ?");
$UserInfoQuery->bind_param("s", $UserID_Cookie);
$UserInfoQuery->execute();
$GetInfoQuery= $UserInfoQuery->get_result();
while($UserInfo = $GetInfoQuery->fetch_assoc()){
   $user_first_name = $UserInfo["FName"];
   $user_app_id = $UserInfo["user_uid"];
}
?>