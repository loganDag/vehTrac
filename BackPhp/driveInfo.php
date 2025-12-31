<?php
$DriveInfoQuery = "SELECT * FROM drives WHERE user_uid=('$UserID_Cookie')";
if ($DriveInfoQueryResult = mysqli_query($conn, $DriveInfoQuery)){
    $DriveCount = mysqli_num_rows($DriveInfoQueryResult);
}
?>