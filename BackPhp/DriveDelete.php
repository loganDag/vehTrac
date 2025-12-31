<?php
require ('dbconnect.php');
if (isset($_POST['delete_drive'])){
$RemoveID = $_POST['delete_drive_id'];
$sql = "SELECT * FROM drives WHERE ran_id=('$RemoveID')";
$result = $conn->query($sql);
if ($result->num_rows <= 0){
    header('refresh:0; url=/drive.php?de=1&db_id='.$RemoveID);
}else{
    $sql = "DELETE FROM drives WHERE ran_id=('$RemoveID')";
    $result = $conn->query($sql);
    if ($result == TRUE){
        header('refresh:0; url=/drive.php?de=2');
    }else if ($result == FALSE){
        $connError = $sql . $conn->error;
        header('refresh:0; url=/drive.php?e='.$connError);
    }
}
}
$conn->close();
?>