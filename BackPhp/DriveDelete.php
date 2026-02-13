<?php
require ('dbconnect.php');
if (isset($_POST['delete_drive'])){
$RemoveID = $_POST['delete_drive_id'];
$stmt = $conn->prepare("SELECT * FROM drives WHERE ran_id= ?");
$stmt->bind_param("s", $RemoveID);
$stmt->execute();
$result = $stmt->get_result();
$ResultQuery = $result->fetch_assoc();

if (!$ResultQuery){
    header('refresh:0; url=/ui/drive/index.php?de=1&db_id='.$RemoveID);
}else{
    $stmt = $conn->prepare("DELETE FROM drives WHERE ran_id = ?");
    $stmt->bind_param("s", $RemoveID);
    $result = $stmt->execute();
    if ($result == TRUE){
        header('refresh:0; url=/ui/drive/index.php?de=2');
    }else if ($result == FALSE){
        $connError = $stmt . $conn->error;
        header('refresh:0; url=/ui/drive/index.php?e='.$connError);
    }
}
}
$conn->close();
?>