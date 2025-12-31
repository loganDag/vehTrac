<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/../bootstrap.html");
require("$DocRoot/includes/header.php");
require("$DocRoot/includes/cookieCheck.php");
require("$DocRoot/includes/menu.html");
$VehUIDUrl = $_GET["uid"];
if (isset($VehUIDUrl)){
        header('location: editVeh.php?VehId='.$VehUIDUrl);
}
if (isset($_POST["delte_"]))
?>
<html>
        <head>
                <title>VehTrac | <?php echo $user_display_name;?>'s Vehicles</title>
        </head>
        <body data-bs-theme="<?php echo $theme;?>">
        <div class="main_site_content">
        <h3 class="container-fluid text-center">Your Vehicles.</h3>
        <div class="container-fluid d-flex veh_main_con">
                 <table class="table table-responsive table-hover caption-top table-striped-columns veh_table">
                        <caption>List of all of your vehicles in our system</caption>
                        <thead>
                                <tr>
                            <th scope="col">Make</th>
                            <th scope="col">Model</th>
                            <th scope="col">Year</th>
                            <th scope="col">Database ID</th>
                            <th scope="col">Edit Vehicle</th>
                            <!--<th scope="col">Additional</th>-->
                                </tr>
                        </thead>
                            <tbody>
                            <?php                     
                        $CarIDGrabInfoSql = "SELECT * FROM user_vehicles WHERE user_uid=('$UserID_Cookie')";
                        $CarIDGrabResult = $conn->query($CarIDGrabInfoSql);
                        if ($CarIDGrabResult->num_rows > 0){
                        while($CarIDGrabInfo = $CarIDGrabResult->fetch_assoc()){
                           $veh_id = $CarIDGrabInfo["veh_uid"];
                        
                        $CarInfoGSql = "SELECT * FROM vehicles WHERE veh_uid=('$veh_id')";
                        $CarGrabInfoGResult= $conn->query($CarInfoGSql);
                       
                            
                                while($CarIDGrabInfoDis = $CarGrabInfoGResult->fetch_assoc()){
                        $dis_veh_make = $CarIDGrabInfoDis["make"];
                        $dis_veh_model = $CarIDGrabInfoDis["model"];
                        $dis_veh_year = $CarIDGrabInfoDis["year"];
                        $dis_db_id = $CarIDGrabInfoDis["veh_uid"];
                        
                        echo "<tr><td>$dis_veh_make</td>";
                        echo " <td>$dis_veh_model</td>";
                        echo " <td>$dis_veh_year</td>";
                        echo "<td>$dis_db_id</td>";
                        echo "<td><a href='editVeh.php?uid=".htmlspecialchars($veh_id)."'>Edit Vehicle</a></td></tr>";
                        
                    } 
              }
            }
?>                      </tbody>
                </table>
        </div>
</div>
</body>
<?php require("includes/footer.html");?>
</html>
