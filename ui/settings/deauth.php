<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
$Session_ID = "";

$Session_ID = $_GET["id"];
$new_stat = '0';

$stmt = $conn->prepare("UPDATE user_logins SET is_valid = ? WHERE cookie_id = ?");
$stmt->bind_param("ss", $new_stat, $Session_ID);

if ($stmt->execute() == false){
echo "Unable to update the session, redirecting to settings page now.";
header ('refresh: 3 /ui/settings');
}else{
    header('refresh: 1 /ui/settings');
}

?>