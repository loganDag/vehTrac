
<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require ("$DocRoot/includes/header.php");
require ("$DocRoot/includes/menu.html");
$SetDriveID = $_GET["uid"];
$UserID_Cookie = $_SESSION["user_id"];

if (!isset($SetDriveID)){
  header('refresh:0; url=/ui/drive/');
}
else{
$sql = $conn->prepare("SELECT * FROM drives WHERE ran_id = ?");
$sql->bind_param("i", $SetDriveID);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows > 0){
while($Info = $result->fetch_assoc()){
$db_veh_uid = $Info["veh_uid"];
$db_miles = $Info["total_miles"];
$db_reason = $Info["reason"];
$db_date_time = $Info["date_time"];
$drive_db_id = $Info["ran_id"];
$drive_owner_id = $Info["user_uid"];
}
$dateTime = new DateTime($db_date_time);
$formatted_datetime = $dateTime->format('Y-m-d\TH:i');
}
else{
  echo "<div class='align-items-center justify-content-center'>";
  echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Editing Issue</h3>";
  echo "Unable to grab your information." .$sql."<br></b>" . $conn->error;
  echo "Please <a href='mailto:contact@logandag.dev?subject=SQL Drive Editting Issue.'>Email Support Here</a></div>";
}//END DB SELECTION IF STATEMENT

if ($UserID_Cookie != $drive_owner_id){
 echo "<div class='align-items-center justify-content-center'>";
   echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Editing Issue</h3>";
   echo "You seem to not be the owner of this drive, please try again, will redirect now. </div>";
   header('refresh:4; url=/ui/drive');
die();
  }

if (isset($_POST['save_drive'])){
$miles_update = $_POST['miles_update'];
$veh_uid_update = $_POST['veh_uid_update'];
$reason_update = $_POST['reason_update'];
$time_update = date("M-d-Y H:i:s", strtotime ($_POST["time_update"]));

$sql =$conn->prepare("UPDATE drives SET total_miles= ?, reason=?, date_time=?, veh_uid = ? WHERE ran_id=?");
$sql->bind_param(
"ssssi", $miles_update, $reason_update, $time_update, $veh_uid_update, $drive_db_id
);
$Update = $sql->execute();
if ($Update == TRUE){
  header('refresh:0; url=/ui/drive/index.php?s=1');
}elseif ($Update == FALSE){
  $connError = $sql->error . $sql->errno;
  header('refresh:0; url=/ui/drive/index.php?e='.$connError);
}

  $conn->close();
}//END UPDATE STATEMENT
}
if (isset($_POST['drive_back'])){
  header('refresh:0; url=/ui/drive/index.php');
}
?>
<!doctype html>
<html lang="en">
<head>
        
    <title>VehTrac | Edit Drive <?php echo $SetDriveID;?> </title>
</head>
<body data-bs-theme="<?php echo $theme;?>">
<div class="modal fade" id="editDrive" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="AddDrive" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-fullscreen">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Editing drive <?php echo $SetDriveID;?></h5>
        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
      </div>
      <div class="modal-body">
      <form action="" method="post">
            <div class="row">
              <div class="col-md-6">
            <div class='form-floating'>
                <input type='text' class='form-control' id='miles_update' name='miles_update' placeholder='Enter Miles Driven: *' value='<?php echo $db_miles;?>'>
                <label for='miles_update'>Edit Miles Driven:</label>
            </div>
                      <br>
            <div class='form-floating'>
                <input type='text' class='form-control' id='veh_uid_update' name='veh_uid_update' placeholder='Enter the Vehicle UID: *' value='<?php echo $db_veh_uid;?>'>
                <label for='veh_uid_update'>Edit Vehicle Assigned (must get UID):</label>
            </div>
            <br>
            <div class='form-floating'>
                <input type='text' class='form-control' id='reason_update' name='reason_update' placeholder='Reason for drive:' value='<?php echo $db_reason;?>'>
                <label for='reason_update'>Edit Reason:</label>
            </div>
            <br>
            <div class='form-floating'>
              <p class='text-muted'>Date and time of drive</p>
              <p class='text-muted'>This is in <a href='https://www.timecalculator.net/12-hour-to-24-hour-converter' target='__blank'>24 hour</a> time and in EST</p>
              <p class='text-muted fs-6'>Conversion clock by <a href='https://www.timecalculator.net/'>timecalculator.net</a></p>
                <input type='datetime-local' class='form-control' id='time_update' name='time_update' value='<?php echo $formatted_datetime;?>'>
                </div>
                  </div>
                  <div class="col-5">
                      <div class="container-fluid">
                        <p class="fs-5 text-secondary">Miles entered in system: <?php echo $db_miles;?></p>
                      </div>
                      <br>
                      <div class="container-fluid">
                        <p class="fs-5 text-secondary">Vehicle assigned (UID): <?php echo $db_veh_uid;?></p>
                      </div>
                      <br>
                      <div class="container-fluid">
                        <p class="fs-5 text-secondary">Reason entered in system: <?php echo $db_reason;?></p>
                      </div>
                      <br>
                      <div class="container-fluid">
                        <p class="fs-5 text-secondary">Date and time in system: <?php echo $db_date_time;?></p>
                      </div>
                  </div>
                </div>
          </div> <!--End modal body-->
        <div class="modal-footer">
          <p class=" fs-5 alert alert-danger">Only update what you want to change, leave the other values as they are or they will be blank in the database!</p>
          <button type="submit" class="btn btn-primary" name="save_drive">Save Drive</button>
          <button type="submit" class="btn btn-secondary" name="drive_back">Go back to drives</button>
      </form>
      <!--<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>-->
      </div>
    </div>
  </div>
  </div><!--END Modal-->

</body>
  <?php
  if (isset($SetDriveID)){
    echo "<script type='text/javascript'>
    var DriveModal = new bootstrap.Modal(document.getElementById('editDrive'), {
  keyboard: false
  });
  DriveModal.show();
    </script>";
  }
  ?>
  </html>
