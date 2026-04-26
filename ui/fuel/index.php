<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require "$DocRoot/includes/header.php";
require "$DocRoot/includes/cookieCheck.php";
require "$DocRoot/includes/menu.html";
$CleanIP = '';
$fuel_vehuid = '';
$fuel_enter = '';
$price_enter = '';
$mileage_enter = '';
$fuel_reason = '';
$fuel_time = '';
$VehUID = $_GET["uid"];

if (isset($VehUID)){
$SetUID = $VehUID;
}
else{
  $SetUID = NULL;
}

if (isset($_POST['submit_fuel'])){
          $GetcurrentTime = new DateTime();

    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
        $CleanIP = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }else{
    $CleanIP = $_SERVER["REMOTE_ADDR"];
    }
$fuel_vehuid = $_POST["fuel_vehuid"];
$fuel_enter = $_POST["fuel_enter"];
$price_enter = $_POST["price_enter"];
$mileage_enter = $_POST["mileage_enter"];
$fuel_reason = $_POST["fuel_reason"];
$fuel_time = $GetcurrentTime->format('Y-m-d H:i:s');

 $ran_id = rand(1, 100000);
  $stmt = $conn->prepare("SELECT * FROM fuel_logs WHERE ran_id= ?");
  $stmt->bind_param("i", $ran_id);
$stmt->execute();
  $result = $stmt->get_result();
  while ($ResultsQuery = mysqli_fetch_array($result)) {
    $ran_id_result = $ResultsQuery["ran_id"];
  }
  while ($ran_id_result != 0) {
    $ran_id_uid = rand(1000, 10000);
    $ResultsQuery = mysqli_fetch_array($result);
  }


$sql = $conn->prepare("INSERT INTO fuel_logs (ran_id, veh_uid, user_uid, log_ip, reason, veh_mileage, total_price, date_logged) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("iissssss", $ran_id, $fuel_vehuid, $UserID_Cookie, $CleanIP, $fuel_reason, $mileage_enter, $price_enter, $fuel_time);

if ($sql->execute()==false){
      echo "<div class='align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Logging issue</h3>";
    echo "Unable to insert your information." . $sql . "<br></b>" . $conn->error;
    echo "Please <a href='mailto:support@logandag.dev?subject=SQL Fuel Logging Issue.'>Email Support Here</a></div>";
}else{
    header('refresh:0; url=/ui/fuel');
}

}
?>
<html>
    <head>
        <title>VehTrac | Fuel Log</title>
    </head>
    <body data-bs-theme="<?php echo $theme; ?>">
    <div class="main_site_content min-vh-100">
<div class="d-flex flex-column container">
<div class="container-fluid d-flex mx-auto">
                 <table class="table table-responsive table-hover caption-top table-striped-columns fuel_table">
                    <caption>List of all your fuel logs in the system:</caption>
                        <thead>
                                <tr>
                            <th scope = "col">Odometer Reading</th>
                            <th scope="col">Date Logged</th>
                            <th scope="col">Vehicle UID </th>
                            <th scope="col">Vehicle Nickname</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Additional Actions</th>
                                </tr>
                        </thead>
                            <tbody>
                     <?php
                     $stmt = $conn->prepare(
                         "SELECT * FROM fuel_logs WHERE user_uid=?"
                     );
                     $stmt->bind_param("s", $UserID_Cookie);
                     $stmt->execute();
                     $result = $stmt->get_result();
                     if ($result->num_rows > 0) {
                         while ($Fuel_results = $result->fetch_assoc()) {
                          $dis_db_mileage = $Fuel_results["veh_mileage"];
                             $dis_db_time = $Fuel_results["date_logged"];
                             $dis_reason = $Fuel_results["reason"];
                             $dis_veh_uid = $Fuel_results["veh_uid"];
                             $dis_db_id = $Fuel_results["db_id"];

                             $dateTimeObj = new DateTime($dis_db_time);

                             $human_readable_time = $dateTimeObj->format(
                                 'F j, Y \\a\\t g:i a'
                             );

                             $CarIDGrabInfoSql = $conn->prepare(
                                 "SELECT veh_uid, nickname FROM vehicles WHERE veh_uid=?"
                             );
                             $CarIDGrabInfoSql->bind_param("i", $dis_veh_uid);
                             $CarIDGrabInfoSql->execute();
                             $CarIDGrabResult = $CarIDGrabInfoSql->get_result();
                             if ($CarIDGrabResult->num_rows > 0) {
                                 while (
                                     $CarIDGrabInfo = $CarIDGrabResult->fetch_assoc()
                                 ) {
                                     $veh_id = $CarIDGrabInfo["veh_uid"];
                                     $veh_nickname = $CarIDGrabInfo["nickname"];
                                     if ($veh_nickname == null) {
                                         $veh_nickname_clean =
                                             "No nickname set";
                                     } else {
                                         $veh_nickname_clean = $veh_nickname;
                                     }

                                     echo "<tr>";
                                     echo "<td>$dis_db_mileage</td>";
                                     echo " <td>$human_readable_time</td>";
                                     echo "<td> $veh_id </td>";
                                     echo "<td>$veh_nickname_clean</td>";
                                     echo " <td>$dis_reason</td>";
                                        echo "<td> 
                        <div class='dropdown'>
                                  <button class='btn btn-secondary dropdown-toggle' type='button' id='dropdownMenuButton1' data-bs-toggle='dropdown' aria-expanded='false'>
                                Actions
                                 </button>
                                <ul class='dropdown-menu' aria-lebelledby='dropdownMenuButton1'>
                                <li><a href='/ui/fuel/editFuel.php?dbid=".htmlspecialchars($dis_db_id)."&action=edit'>Edit Fuel Log</a></li>
                                <li><a href='/ui/fuel/editFuel.php?dbid=".htmlspecialchars($dis_db_id)."&action=delete'>Delete Fuel Log</a></li>
                                </ul>
                        </div>
                        </td>";
                                     echo "</tr>";
                                 }
                             }
                         }
                     }
                     ?>
                    
                </tbody>
            </table>
     </div>
</div>

<hr>
<a name="addFuel">
<div class="d-flex flex-column container">
  <p class="fs-3">Log your fuel below:</p>
<div class="container-fluid d-flex mx-auto">
                     
          <form action="" method="post" class='w-100'>
            <p class='text-muted alert alert-warning'>Fields with * are required</p>
                        <div class='form-floating'>
              <input type='text' class='form-control' id='fuel_vehuid' name='fuel_vehuid' placeholder = '' value = '<?php echo "$SetUID";?>' required>
              <label for='fuel_vehuid'>Vehicle ID: *</label>
            </div>
            <br>
            <div class='form-floating'>
              <input type='text' class='form-control' id='fuel_enter' name='fuel_enter' placeholder='Enter Fuel: *' required>
              <label for='fuel_enter'>Enter pumped amount: *</label>
            </div>
            <br>
                        <div class='form-floating'>
              <input type='text' class='form-control' id='price_enter' name='price_enter' placeholder='Enter Price: *' required>
              <label for='price_enter'>Total Price: *</label>
            </div>
            <br>
                        <div class='form-floating'>
              <input type='text' class='form-control' id='mileage_enter' name='mileage_enter' placeholder='Enter Mileage: *' required>
              <label for='mileage_enter'>Enter odometer reading: *</label>
            </div>
            <br>
            <div class='form-floating'>
              <input type='text' class='form-control' id='fuel_reason' name='fuel_reason' placeholder='Reason:'>
              <label for='fuel_reason'>Reason for fuel (i.e. Doordash):</label>
            </div>
            <br>
            <div class='form-floating'>
              <p class='text-muted'>Date and time of fill up</p>
              <p class='text-muted'>This is in <a href='https://www.timecalculator.net/12-hour-to-24-hour-converter' target='__blank'>24 hour</a> time and in EST</p>
              <p class='text-muted fs-6'>Conversion clock by <a href='https://www.timecalculator.net/' target='__blank'>timecalculator.net</a></p>
              <input type='datetime-local' class='form-control' id='fuel_time' name='fuel_time' required>

            </div>
            <br>
          <button type="submit" class="btn btn-primary" name="submit_fuel">Submit Fuel Log</button>
          </form>

</div>
                    </a>
</div>
</div>
</body>
<?php require "$DocRoot/includes/footer.html"; ?>
</html>