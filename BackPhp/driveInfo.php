<?php
$DriveInfoQuery = $conn->prepare("SELECT COUNT(*) FROM drives WHERE user_uid=?");
$DriveInfoQuery->bind_param("s", $UserID_Cookie);
$DriveInfoQuery->execute();
$DriveInfoQuery->bind_result($DriveCount);
$DriveInfoQuery->fetch();
$DriveInfoQuery->close();
?>