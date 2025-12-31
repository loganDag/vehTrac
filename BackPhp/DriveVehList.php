<!--10 row limit centered table for user vehicles-->
<div class="container-fluid d-flex veh_main_con">
                 <table class="table table-responsive table-hover caption-top table-striped-columns veh_table">
                        <thead>
                                <tr>
                            <th scope="col">Make</th>
                            <th scope="col">Model</th>
                            <th scope="col">Year</th>
                            <th scope="col">Vehicle UID</th>
                                </tr>
                        </thead>
                            <tbody>
                     <?php                
                        $CarIDGrabInfoSql = "SELECT veh_uid FROM user_vehicles WHERE user_uid=('$UserID_Cookie')";
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
                    
                        echo "<tr>";
                        echo "<td>$dis_veh_make</td>";
                        echo " <td>$dis_veh_model</td>";
                        echo " <td>$dis_veh_year</td>";
                        echo "<td>$veh_id</td>";
                        echo "</tr>";
                    } 
                      
                
              }
            }
            
                    ?>
                    
                </tbody>
            </table>
     </div>