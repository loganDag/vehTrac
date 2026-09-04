<?php
$UserID_Cookie = $_SESSION["user_id"];
$UserInfoQuery = $conn->prepare("SELECT * FROM user_info WHERE user_uid = ?");
$UserInfoQuery->bind_param("s", $UserID_Cookie);
$UserInfoQuery->execute();

$GetInfoQuery = $UserInfoQuery->get_result();

// If you only expect one user, use 'if' instead of 'while'
if ($UserInfo = $GetInfoQuery->fetch_assoc()) {
    $user_first_name = $UserInfo["FName"];
    $user_app_id     = $UserInfo["user_uid"];
}

// --- THIS IS THE FIX ---
$GetInfoQuery->free();    // Clear the result buffer
$UserInfoQuery->close();  // Close the statement
// -----------------------
?>