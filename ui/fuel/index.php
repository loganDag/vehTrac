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

<div class="container-fluid d-flex mx-auto">
                 <table class="table table-responsive table-hover caption-top table-striped-columns fuel_table">
                    <caption>List of all your fuel logs in the system:</caption>
                        <thead>
                                <tr>
                            <th scope="col">Logged Miles</th>
                            <th scope="col">Date Logged</th>
                            <th scope="col">Vehicle UID </th>
                            <th scope="col">Vehicle Nickname</th>
                            <th scope="col">Reason</th>
                            <th scope="col">Edit Log</th>
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
                        $dis_fuel_miles = $Fuel_results["miles"];
                        $dis_db_time = $Fuel_results["date"];
                        $dis_reason = $Fuel_results["reason"];
                        $dis_veh_uid = $Fuel_results["veh_uid"];
                        $dis_db_id = $Fuel_results["db_id"];

                        $dateTimeObj = new DateTime($dis_db_time);

                        $human_readable_time = $dateTimeObj->format('F j, Y \\a\\t g:i a');

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
                        echo "<td>$dis_fuel_miles</td>";
                        echo " <td>$human_readable_time</td>";
                        echo "<td> $veh_id </td>";
                        echo "<td>$veh_nickname_clean</td>";
                        echo " <td>$dis_reason</td>";
                        echo "<td><a href='https://vehtrac.logandag.dev/ui/fuel/editFuel.php?dbid=$dis_db_id'>Edit $dis_db_id</a></td>";
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