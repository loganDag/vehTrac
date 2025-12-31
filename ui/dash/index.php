<?php
$DocRoot= $_SERVER["DOCUMENT_ROOT"];
require ("$DocRoot/includes/header.php");
require ("$DocRoot/../bootstrap.html");
require ("$DocRoot/includes/cookieCheck.php");
require ("$DocRoot/includes/menu.html");
$SQLError = $_GET['e'];
$DeleteError = $_GET['de'];

if ($DriveCount > 20){
    $DriveCount = "20+";
}

$VehTableCarQuery_del_stat = '0';
$VehTableCarQuery = "SELECT * FROM vehicles WHERE del_stat='$VehTableCarQuery_del_stat'";

// Execute the query
$VehTableCarQueryResult = mysqli_query($conn, $VehTableCarQuery);

// Check if the query was successful
if ($VehTableCarQueryResult) {
    // Check if there are any rows returned
    if (mysqli_num_rows($VehTableCarQueryResult) > 0) {
        // Fetch the first row of the result
        $vehTableData = mysqli_fetch_assoc($VehTableCarQueryResult);
        $vehTable_uid = $vehTableData["veh_uid"];
        // Now you can use $vehTable_uid as needed
    }
}
$UserCarNumQuery = "SELECT * FROM user_vehicles WHERE veh_uid=('$vehTable_uid') AND user_uid = ('$UserID_Cookie')";

if ($UserCarNumResult = mysqli_query($conn, $UserCarNumQuery)){
    $CarNumber = mysqli_num_rows($UserCarNumResult);
}
if ($CarNumber <= 0){
$CarNumber = "0";
}
if (isset($_POST['add_vehicle'])){
$new_vin = $_POST['vin'];
$new_veh_year = $_POST['model_year'];
$new_veh_make = $_POST['make'];
$new_veh_model = $_POST['model'];
$new_veh_color = $_POST['color'];
$del_stat = '0';

$sql = "SELECT * FROM vehicles WHERE vin=('$new_vin')";
$result = $conn->query($sql);
if ($result == TRUE){
if (mysqli_num_rows($result)>=1){
    echo "<div class='d-flex align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Error</h3>";
    echo "This car already exists in our system, if you need ownership transferred. Please contact <a href='https://support.nexgenit.digital'>Support</a></div></div>";
}
else if (mysqli_num_rows($result)<1){
    $new_veh_uid = rand(1000,10000);
    $sql= "SELECT * FROM vehicles WHERE veh_uid=('$new_veh_uid')";
    $result = $conn->query($sql);
   while($ResultsQuery = mysqli_fetch_array($result)){
      $veh_uid_result = $ResultsQuery["veh_uid"];
   }
    while ($veh_uid_result !=0){
      $new_veh_uid = rand(1000,10000);
      $ResultsQuery = mysqli_fetch_array($result);
    }
    $date = date("M-d-Y  H:i:s");
        $sql = "INSERT INTO vehicles (veh_uid, make, model, year, color, vin, del_stat, date_added) VALUES ('".$new_veh_uid."', '".$new_veh_make."', '".$new_veh_model."', '".$new_veh_year."', '".$new_veh_color."', '".$new_vin."', '".$del_stat."', '".$date."')";
        $InsertResult = $conn->query($sql);
        if ($InsertResult == TRUE){
           $VehID = $new_veh_uid;
            if ($VehID){
                    $UVSql = "INSERT INTO user_vehicles (veh_uid, user_uid) VALUES ('".$new_veh_uid."', '".$UserID_Cookie."')";
                    $UVResult = $conn->query($UVSql);
                    if ($UVResult == TRUE){
                        echo "<div class='alert alert-success text-center' role='alert'> <p>Vehicle created!</p>";
                        echo "You can now start using this vehicle!</div>";
                        header('refresh:3; url=dash.php');
                    }   
                    else if ($UVResult == FALSE){
                        echo "<div class='align-items-center justify-content-center'>";
                        echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Logging issue</h3>";
                        echo "Unable to make you the owner" .$sql."<br></b>" . $conn->error;
                        echo "Please <a href='mailto:support@nexgenit.digital?subject=SQL ownership insert'>Email Support Here</a></div>";
                    }   
            }else if (!$VehID){
                echo "<div class='align-items-center justify-content-center'>";
                echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Logging issue</h3>";
                echo "Vehicle ID variable not set. The vehicle has been created, we were unable to make you as the owner.";
                echo "Please <a href='mailto:support@nexgenit.digital?subject=SQL Vehicle ID variable not set for user vehicles table'>Email Support Here</a></div>";
            }
        }else if ($InsertResult== FALSE){
            echo "<div class='align-items-center justify-content-center'>";
            echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Logging issue</h3>";
            echo "Unable to insert your vehicle." .$sql."<br></b>" . $conn->error;
            echo "Please <a href='mailto:support@nexgenit.digital?subject=SQL Vehicle insert.'>Email Support Here</a></div>";
        }
    }
}
}

?>
<!doctype html>
<html lang="en">
<head>
        
    <title>VehTrac | <?php echo $user_display_name;?>'s Dashboard</title>

</head>
<body data-bs-theme="<?php echo $theme;?>">
<div class="main_site_content">
<div class="container align-items-center w-100 justify-content-center d-flex">
        <h3 class="jumbotron text-centered">Please choose which car to log.</h3>
    </div>
    <div class="container align-items-center w-100 justify-content-center d-flex">
    <p class="text-muted">or click the profile icon for your quick info.</p>
</div>
    <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#DashboardOffCanvas">
    <img src="/includes/images/default_avatar.jpg" alt="" width="50" height="50" class="rounded-circle">
    </button>
    <?php require ("$DocRoot/includes/Files/OffCanvasMenu.php");?>

<style>
    .veh_add_form {
        margin-bottom: 20px !important;
    }
    @media (max-width: 768px) {
        .veh_add_form {
            margin-bottom: 50px !important;
        }
        .main_site_content {
            padding-bottom: 100px !important; /* Ensure space for modal */
        }
    }
    @media (min-width: 769px) {
        .main_site_content {
            padding-bottom: 20px !important; /* Reduce gap on desktop */
        }
    }
    .form-floating, .form-check, .form-row {
        width: 100%;
    }
    .veh_add_form form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
</style>

<div class="row">
    <div class="col-md-6">
        <?php require("$DocRoot/BackPhp/vehListSql.php");?>
        <?php require("$DocRoot/BackPhp/DriveListSql.php");?>
    </div>
    <div class="col-md-6 veh_add_form">
        <h3 class='fs-5 text-muted'>Add another vehicle here</h3>
        <form action="" method="post">
            <div class="form-floating">
                <input type='text' class='form-control' id="vin" name="vin" placeholder='' required>
                <label for='vin'>Please Enter your vin (must be 17 characters)</label>
            </div>
            <div class="form-check form-switch">
                <input type='checkbox' class='form-check-input' name='vin_bypass' id='vin_bypass'>
                <label for='vin_bypass' class='form-check-label'>Check this if Vehicle year is 1981 or earlier for vin bypass</label>
            </div>
            <div class="form-floating">
                <input type='text' class='form-control' id="model_year" name="model_year" placeholder='' required>
                <label for='model_year'>Please Enter your vehicle year</label>
            </div>
            <div class="form-floating">
                <input type='text' class='form-control' id="make" name="make" placeholder='' required>
                <label for='make'>Enter vehicle make</label>
            </div>
            <div class="form-floating">
                <input type='text' class='form-control' id="model" name="model" placeholder='' required>
                <label for='model'>Enter vehicle model</label>
            </div>
            <div class="form-floating">
                <input type='text' class='form-control' id="model" name="color" placeholder='' required>
                <label for='model'>Enter vehicle color</label>
            </div>
            <div class="form-row">
                <button type="submit" class="btn btn-secondary" name="add_vehicle">Add this vehicle</button>
            </div>
        </form>
    </div>
</div>

                      <?php
                            if ($CarNumber > 3){
                                echo "<style> .veh_table{display:none}</style>";
                                echo "<div class='d-flex align-items-center justify-content-center'>";
                                echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Count Issue</h3>";
                                echo "You have $CarNumber cars in our system, this is only to display 3 or less, please <a href='vehicles.php'>Click here</a> for a full list</div></div>";
                            }
                    ?>

         </div><!--End Main Site content div-->


  <!--Start Delete Success Modal-->
  <div class="modal" tabindex="-1" id="DeleteVehicleSuccess">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-success">Deleted</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Successfuly deleted your vehicle.</p>
        <p>Contact Support if you need to reverse this.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!--End Delete Success Modal-->

  <!--Start Delete Error Modal-->
  <div class="modal" tabindex="-1" id="VehicleDeleteError">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Error Deleting Vehicle:</p>
        <p>Vehicle by the ID of <?php $db_id = $_GET['db_id']; echo $db_id;?> does not exist.</p>
        <p>Please try a different ID</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!--End Delete Error Modal-->

  <!--Start User not owner Error Modal-->
  <div class="modal" tabindex="-1" id="UserNotOwner">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Delete Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
                <p>It seems like this isn't your vehicle.</p> 
                <p>Please make sure this is supposed to be your vehicle and doesn't belong to someone else.</p>
                <p class='text-muted fs-5'>If you believe this is in error, please <a href='https://support.nexgenit.digital'>Contact Support</a>.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!--Start User not owner Error Modal-->

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
        <p class="text-muted fs-5"><?php echo $SQLError;?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div> <!--End Error Modal-->




<?php
  if (isset($_GET['s'])){
  echo "<script type='text/javascript'>
  var DeleteVehicleSuccess = new bootstrap.Modal(document.getElementById('DeleteVehicleSuccess'), {
keyboard: false
});
DeleteVehicleSuccess.show();
  </script>";
}
if (isset($SQLError)){
  echo "<script type='text/javascript'>
  var SQLError = new bootstrap.Modal(document.getElementById('SQLError'), {
keyboard: false
});
SQLError.show();
  </script>";
}
if ($DeleteError == '2'){
  echo "<script type='text/javascript'>
  var UserNotOwner = new bootstrap.Modal(document.getElementById('UserNotOwner'), {
keyboard: false
});
UserNotOwner.show();
  </script>";
}
if ($DeleteError == '1'){
  echo "<script type='text/javascript'>
  var VehicleDeleteError= new bootstrap.Modal(document.getElementById('VehicleDeleteError'), {
keyboard: false
});
VehicleDeleteError.show();
  </script>";
}
?>
    <div class="h-25"> </div>
  
</style>
<style>
    .veh_add_form {
        margin-bottom: 20px !important;
    }
    @media (max-width: 768px) {
        .veh_add_form {
            margin-bottom: 50px !important;
        }
        .main_site_content {
            padding-bottom: 100px !important; /* Ensure space for modal */
        }
    }
    @media (min-width: 769px) {
        .main_site_content {
            padding-bottom: 20px !important; /* Reduce gap on desktop */
        }
    }
</style>
        </body>
<?php require("$DocRoot/includes/footer.html");
    $conn->close();?>
</html>
