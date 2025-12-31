<?php
require ("../bootstrap.html");
require ("includes/header.php");
require ("includes/cookieCheck.php");
require ("includes/menu.html");
$VehUIDUrl = $_GET["uid"];
$Cookie_user_id = $_COOKIE["user_id"];

$VehSecureSQL = "SELECT * FROM user_vehicles WHERE veh_uid=('$VehUIDUrl')";
$VehSecureResult = $conn->query($VehSecureSQL);
while($VehSecureInfo = $VehSecureResult->fetch_assoc()){

}

$Vehsql = "SELECT * FROM vehicles WHERE veh_uid=('$VehUIDUrl')";
$VehResult = $conn->query($Vehsql);
while($VehInfo = $VehResult->fetch_assoc()){
    $user_id = $VehInfo["user_uid"];
}
?>
<html>
<head>
    <title>VehTrac | Edit your vehicle info</title>
        </head>
        <body data-bs-theme="<?php echo $theme;?>">
<h3 class="text-muted">Editing Vehicle <?php echo $VehMake;?></h3>
<div class="main_site_content">


</div>
</body>
<?php require("includes/footer.html");?>
</html>
