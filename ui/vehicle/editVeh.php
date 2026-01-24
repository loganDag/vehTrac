<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
require("$DocRoot/includes/cookieCheck.php");
require("$DocRoot/includes/menu.html");
$VehUIDUrl = $_GET["uid"];
$Cookie_user_id = $_SESSION["cookie_id"];

$VehSecureSQL = "SELECT * FROM user_vehicles WHERE veh_uid=('$VehUIDUrl')";
$VehSecureResult = $conn->query($VehSecureSQL);
while($VehSecureInfo = $VehSecureResult->fetch_assoc()){

}

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE veh_uid= ?");
$stmt->bind_param("s", $VehUIDUrl);
$stmt->execute();
$VehResult = $stmt->get_result();
if ($VehResult->num_rows > 0){
while($VehInfo = $VehResult->fetch_assoc()){
    $user_id = $VehInfo["user_uid"];
    $VehLongInfo = $VehInfo["year"]. " "  .$VehInfo["make"]. " "  . $VehInfo["model"];
}
}
?>
<html>
<head>
    <title>VehTrac | Edit your vehicle info</title>
        </head>
        <body data-bs-theme="<?php echo $theme;?>">
<h3 class="text-muted">Editing Vehicle <?php echo $VehLongInfo;?></h3>
<div class="main_site_content">


</div>
</body>
<?php require("includes/footer.html");?>
</html>
