<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
require("$DocRoot/includes/cookieCheck.php");
require("$DocRoot/includes/menu.html");
$UserCookie = $_SESSION["user_id"];


?>
<html>
    <head>
        <title>VehTrac | Fuel Log</title>
    </head>
    <body data-bs-theme="<?php echo $theme;?>">
    <div class="main_site_content container min-vh-100 d-flex flex-column">

<div class="container-fluid d-flex veh_main_con">
                 <table class="table table-responsive table-hover caption-top table-striped-columns veh_table">
                        <thead>
                                <tr>
                            <th scope="col">Logged Miles</th>
                            <th scope="col">Date Logged</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Vehicle UID </th>
                            <th scope="col">Vehicle Nickname</th>
                                </tr>
                        </thead>
                            <tbody>
                     <?php               
                        
                        $stmt = $conn->prepare("SELECT * FROM fuel_logs WHERE user_uid=?");
                        $stmt->bind_param("s", $UserID_Cookie);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0){
                                while($Fuel_results = $result->fetch_assoc()){
                        $dis_veh_make = $Fuel_results["miles"];
                        $dis_veh_model = $Fuel_results["date"];
                        $dis_veh_year = $Fuel_results["reason"];
                        $dis_veh_uid = $Fuel_results["veh_uid"];

                          $CarIDGrabInfoSql = $conn->prepare("SELECT veh_uid, nickname FROM vehicles WHERE veh_uid=?");
                       $CarIDGrabInfoSql->bind_param("i", $dis_veh_uid);
                        $CarIDGrabInfoSql->execute();
                        $CarIDGrabResult = $CarIDGrabInfoSql->get_result();
                        if ($CarIDGrabResult->num_rows > 0){
                        while($CarIDGrabInfo = $CarIDGrabResult->fetch_assoc()){
                           $veh_id = $CarIDGrabInfo["veh_uid"];
                           $veh_nickname = $CarIDGrabInfo["nickname"];
                           if ($veh_nickname == NULL){
                            $veh_nickname_clean = "No nickname set";
                           }else{
                            $veh_nickname_clean = $veh_nickname;
                           }
                    
                        echo "<tr>";
                        echo "<td>$dis_veh_make</td>";
                        echo " <td>$dis_veh_model</td>";
                        echo " <td>$dis_veh_year</td>";
                        echo "<td> $veh_id </td>";
                        echo "<td>$veh_nickname_clean</td>";
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
</body>
<?php require("$DocRoot/includes/footer.html");?>
</html>