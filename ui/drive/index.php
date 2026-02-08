<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
require("$DocRoot/includes/cookieCheck.php");
require("$DocRoot/includes/menu.html");

$SetVehID = $_GET["uid"];
$SQLError = $_GET['e'];
$DeleteError = $_GET['de'];

$TotalSql = $conn->prepare("SELECT SUM(total_miles) as miles FROM drives WHERE user_uid=?");
$TotalSql->bind_param("s", $UserID_Cookie);
$TotalSql->execute();
$TotalMilesFetch = $TotalSql->get_result()->fetch_assoc();
$TotalMiles =  $TotalMilesFetch['miles'];

if (isset($_POST["submit_drive"])) {
  $milesDrive = $_POST['miles_enter'];
  $DriveReason = $_POST['drive_reason'];


  $ran_id = rand(1, 100000);
  $sql = "SELECT * FROM drives WHERE ran_id=('$ran_id')";
  $result = $conn->query($sql);
  while ($ResultsQuery = mysqli_fetch_array($result)) {
    $ran_id_result = $ResultsQuery["ran_id"];
  }
  while ($ran_id_result != 0) {
    $ran_id_uid = rand(1000, 10000);
    $ResultsQuery = mysqli_fetch_array($result);
  }

  $DriveVehUID = $_POST["veh_uid_enter"];
  $DriveTime = date("M-d-Y H:i:s", strtotime($_POST["drive_time"]));

  $sql = "INSERT INTO drives (ran_id, total_miles, reason, date_time, veh_uid, user_uid) VALUES ('".$ran_id."', '" . $milesDrive . "', '" . $DriveReason . "', '" . $DriveTime . "', '" . $DriveVehUID . "', '" . $UserID_Cookie . "')";
  if ($conn->query($sql) == false) {
    echo "<div class='align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Logging issue</h3>";
    echo "Unable to insert your information." . $sql . "<br></b>" . $conn->error;
    echo "Please <a href='mailto:support@logandag.dev?subject=SQL Drive Logging Issue.'>Email Support Here</a></div>";
  } else {
    header('refresh:0; url=/ui/drive');
  }
}
?>
<html>

<head>
  <title>VehTrac | Recorded drives</title>
</head>

<body data-bs-theme="<?php echo $theme; ?>">
  <div class="main_site_content">
    <div class='row'>
      <div class='col-lg-3'>
        <h3 class="container-fluid text-center">Quick Information</h3>
        <div class="quick_box">
          <p class="text-muted">You have <?php echo $DriveCount; ?> Drive(s) entered</p>
          <p class="text-muted">You have <?php echo $TotalMiles; ?> Miles driven</p>
          <p class='text-primary'>Your vehicles in our system</p>
          <?php require ("$DocRoot/BackPhp/DriveVehList.php"); ?>
        </div>
      </div>
      <div class='col-lg-6'>
        <h3 class="container-fluid text-center">Your current drives entered</h3>
        <div class="container-fluid d-flex drive_main_con">
          <table class="table table-responsive table-hover caption-top table-striped-columns veh_table">
            <caption>List of all of your drives</caption>
            <thead>
              <tr>
                <th scope="col">Distance</th>
                <th scope="col">Reason</th>
                <th scope="col">Vehicle UID</th>
                <th scope="col">Date/Time logged</th>
                <th scope="col">Options</th>
                <!--<th scope="col">Additional</th>-->
              </tr>
            </thead>
            <tbody>
              <?php
              $DriveSql = "SELECT * FROM drives WHERE user_uid=('$UserID_Cookie')";
              $DriveResult = $conn->query($DriveSql);
              if ($DriveResult->num_rows > 0) {
                while ($DriveInfo = $DriveResult->fetch_assoc()) {

                  $dis_drive_dis = $DriveInfo["total_miles"];
                  $dis_drive_reason = $DriveInfo["reason"];
                  $dis_drive_uid = $DriveInfo["veh_uid"];
                  $dis_drive_date = $DriveInfo["date_time"];
                  $drive_db_id = $DriveInfo["ran_id"];


                  echo "<tr><td>$dis_drive_dis Miles</td>";
                  echo " <td>$dis_drive_reason</td>";
                  echo "<td><a href='vehicles.php?uid=" . htmlspecialchars($dis_drive_uid) . "'>$dis_drive_uid</a></td>";
                  echo "<td>$dis_drive_date</td>";
                 // echo "<td><button type='button' class='btn btn-secondary' onclick='myFunction($drive_db_id)'>Edit Drive ID: $drive_db_id</button> </td>";
                 echo "<td><a href='https://vehtrac.logandag.dev/ui/drive/editDrive.php?uid=$drive_db_id'>Edit $drive_db_id</a></td>";
                  echo "</tr>";
                }
              }

              ?> </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-3">
        <h3 class="container-fluid text-center">Drive options</h3>
        <div class='d-flex align-items-center justify-content-center'>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDrive">Add a drive </button>
        </div>

        <br>

        <div class="dropdown d-flex align-items-center justify-content-center">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            Delete a Drive
          </button>
          <div class="form-control dropdown-menu" aria-labelledby="dropdownMenuButton1">
            <div class="form-floating">
              <form action="/BackPhp/DriveDelete.php" method="post" class="form-control-sm form-floating">
                <input type="text" class="form-control" id="delete_drive_id" name="delete_drive_id" placeholder='Enter drive ID to delete:'>
                <label for='delete_drive_id'>Enter drive ID to delete:</label>
                <br>
                <p class="fs-6 text-muted">This CANNOT be undone</p>
                <button type="submit" class="btn btn-primary" name="delete_drive">Delete Drive</button>
              </form>
            </div>
          </div>
        </div>
      </div><!--Ends column tag-->
    </div><!--END Row tag-->
  </div><!--End Main Site Content div-->

  <div class="modal fade" id="addDrive" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="AddDrive" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Add a drive</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post">
            <div class='form-floating'>
              <input type='text' class='form-control' id='miles_enter' name='miles_enter' placeholder='Enter Miles Driven: *' required>
              <label for='miles_enter'>Enter Miles Driven: *</label>
            </div>
            <br>
            <div class='form-floating'>
              <input type='text' class='form-control' id='veh_uid_enter' name='veh_uid_enter' required placeholder='Enter the Vehicle UID: *' <?php if (isset($SetVehID)) {
                echo "value='$SetVehID'";
                 } ?>>
              <label for='veh_uid_enter'>Enter the Vehicle UID: *</label>
            </div>
            <br>
            <div class='form-floating'>
              <input type='text' class='form-control' id='drive_reason' name='drive_reason' placeholder='Reason for drive:'>
              <label for='drive_reason'>Reason for drive (i.e. Doordash):</label>
            </div>
            <br>
            <div class='form-floating'>
              <p class='text-muted'>Date and time of drive</p>
              <p class='text-muted'>This is in <a href='https://www.timecalculator.net/12-hour-to-24-hour-converter' target='__blank'>24 hour</a> time and in EST</p>
              <p class='text-muted fs-6'>Conversion clock by <a href='https://www.timecalculator.net/' target='__blank'>timecalculator.net</a></p>
              <input type='datetime-local' class='form-control' id='drive_time' name='drive_time' required>

            </div>
            <!--end modal body-->
        </div>
        <div class="modal-footer">
          <p class='text-muted'>Fields with * are required</p>
          <button type="submit" class="btn btn-primary" name="submit_drive">Submit Drive</button>
          </form>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div><!--END Modal-->

  <!--Start update Success Modal-->
  <div class="modal" tabindex="-1" id="UpdateDriveSuccess">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Updated</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Successfuly updated Drive</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> <!--End update Success Modal-->


  <!--Start Delete Success Modal-->
  <div class="modal" tabindex="-1" id="DeleteDriveSuccess">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Deleted</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Successfuly deleted drive</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> <!--End Delete Success Modal-->

  <!--Start Delete Error Modal-->
  <div class="modal" tabindex="-1" id="DeleteDriveError">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger">Delete Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Error Deleting Drive:</p>
          <p>Drive by the ID of <?php $db_id = $_GET['db_id'];
                                echo $db_id; ?> does not exist.</p>
          <p>Please try a different ID</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> <!--End Delete Error Modal-->

  <!--Start Error Modal-->
  <div class="modal" tabindex="-1" id="SQLError">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h3 class='.fs-3 text-danger'>SQL Error</h3>
          <p class="text-muted fs-4">Error is:</p>
          <p class="text-muted fs-5"><?php echo $SQLError; ?></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div> <!--End Error Modal-->

  <?php
  if (isset($SetVehID)) {
    echo "<script type='text/javascript'>
    var DriveModal = new bootstrap.Modal(document.getElementById('addDrive'), {
  keyboard: false
});
DriveModal.show();
    </script>";
  }
  if (isset($_GET['s'])) {
    echo "<script type='text/javascript'>
  var UpdateDriveSuccess = new bootstrap.Modal(document.getElementById('UpdateDriveSuccess'), {
keyboard: false
});
UpdateDriveSuccess.show();
  </script>";
  }
  if (isset($SQLError)) {
    echo "<script type='text/javascript'>
  var SQLError = new bootstrap.Modal(document.getElementById('SQLError'), {
keyboard: false
});
SQLError.show();
  </script>";
  }
  if ($DeleteError == '2') {
    echo "<script type='text/javascript'>
  var DeleteDriveSuccess = new bootstrap.Modal(document.getElementById('DeleteDriveSuccess'), {
keyboard: false
});
DeleteDriveSuccess.show();
event.preventDefault();
  </script>";
  }
  if ($DeleteError == '1') {
    echo "<script type='text/javascript'>
  var DeleteDriveError = new bootstrap.Modal(document.getElementById('DeleteDriveError'), {
keyboard: false
});
DeleteDriveError.show();
  </script>";
  }
  ?>
</body>
<?php require("$DocRoot/includes/footer.html"); ?>

</html>