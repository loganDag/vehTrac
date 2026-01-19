<?php
session_start();
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require ("$DocRoot/includes/header.php");
require ("$DocRoot/../bootstrap.html");
require ("$DocRoot/includes/menu.html");

// 1. Securely fetch session data
$UserID_Cookie = $_SESSION["user_id"] ?? null;
$CookieID = $_SESSION["cookie_id"] ?? null;

if (!$UserID_Cookie) {
    header('Location: /index.php?error=2');
    exit;
}

if (!$CookieID) {
    header('Location: /index.php?error=3');
    exit;
}

// 2. Initial Vehicle Query (Using Prepared Statement)
$vehTable_uid = null;
$del_stat_val = '0';
$stmt = $conn->prepare("SELECT veh_uid FROM vehicles WHERE del_stat = ? LIMIT 1");
$stmt->bind_param("s", $del_stat_val);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $vehTable_uid = $row["veh_uid"];
}

// 3. Get Car Count for this user
$CarNumber = 0;
if ($vehTable_uid) {
    $stmt = $conn->prepare("SELECT count(*) as total FROM user_vehicles WHERE veh_uid = ? AND user_uid = ?");
    $stmt->bind_param("ss", $vehTable_uid, $UserID_Cookie);
    $stmt->execute();
    $countRes = $stmt->get_result()->fetch_assoc();
    $CarNumber = $countRes['total'];
}

// 4. Handle Form Submission
if (isset($_POST['add_vehicle'])) {
    $new_vin = $_POST['vin'];
    $new_veh_year = $_POST['model_year'];
    $new_veh_make = $_POST['make'];
    $new_veh_model = $_POST['model'];
    $new_veh_color = $_POST['color'];
    $del_stat = '0';

    // Check if VIN exists
    $stmt = $conn->prepare("SELECT vin FROM vehicles WHERE vin = ?");
    $stmt->bind_param("s", $new_vin);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows >= 1) {
        echo "<div class='alert alert-danger text-center'>This car already exists. Contact Support.</div>";
    } else {
        // Generate UNIQUE random UID
        $new_veh_uid = null;
        $is_unique = false;
        while (!$is_unique) {
            $temp_uid = rand(1000, 10000);
            $stmt = $conn->prepare("SELECT veh_uid FROM vehicles WHERE veh_uid = ?");
            $stmt->bind_param("i", $temp_uid);
            $stmt->execute();
            if ($stmt->get_result()->num_rows == 0) {
                $new_veh_uid = $temp_uid;
                $is_unique = true;
            }
        }

        $date = date("M-d-Y H:i:s");

        // Insert new vehicle
        $stmt = $conn->prepare("INSERT INTO vehicles (veh_uid, make, model, year, color, vin, del_stat, date_added) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $new_veh_uid, $new_veh_make, $new_veh_model, $new_veh_year, $new_veh_color, $new_vin, $del_stat, $date);
        
        if ($stmt->execute()) {
            // Link vehicle to user
            $stmt = $conn->prepare("INSERT INTO user_vehicles (veh_uid, user_uid) VALUES (?, ?)");
            $stmt->bind_param("is", $new_veh_uid, $UserID_Cookie);
            
            if ($stmt->execute()) {
                echo "<div class='alert alert-success text-center'>Vehicle created and linked to your account!</div>";
                header('refresh:3; url=dash.php');
            } else {
                echo "<div class='alert alert-danger'>Linked failed: " . $conn->error . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Insert failed: " . $conn->error . "</div>";
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
