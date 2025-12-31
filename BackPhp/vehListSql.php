<!--3 row limit centered table for user vehicles-->
<div class="container-fluid d-flex">
                 <table class="table table-responsive table-hover caption-top table-striped-columns veh_table">
                        <caption>The 3 newest vehicles in our system</caption>
                        <thead>
                                <tr>
                            <th scope="col">Make</th>
                            <th scope="col">Model</th>
                            <th scope="col">Year</th>
                            <th scope="col">Vehicle ID</th>
                            <th scope="col">Enter miles</th>
                            <th scope="col">Edit Vehicle</th>
                                </tr>
                        </thead>
                            <tbody>
                     <?php                
$del_stat = '0';

// Prepare the SQL statement to avoid SQL injection and optimize execution
$CarInfoGSql = "
    SELECT v.make, v.model, v.year, v.veh_uid 
    FROM user_vehicles uv
    INNER JOIN vehicles v ON uv.veh_uid = v.veh_uid
    WHERE uv.user_uid = ? 
    AND v.del_stat = ?
    ORDER BY v.date_added DESC";

// Prepare the statement
if ($stmt = $conn->prepare($CarInfoGSql)) {

    // Bind the parameters to the prepared statement
    $stmt->bind_param('ss', $UserID_Cookie, $del_stat);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if we have results
    if ($result->num_rows > 0) {
        while ($CarIDGrabInfoDis = $result->fetch_assoc()) {
            $dis_veh_make = $CarIDGrabInfoDis["make"];
            $dis_veh_model = $CarIDGrabInfoDis["model"];
            $dis_veh_year = $CarIDGrabInfoDis["year"];
            $dis_veh_uid = $CarIDGrabInfoDis["veh_uid"];
                    
                        echo "<tr>";
                        echo "<td>$dis_veh_make</td>";
                        echo " <td>$dis_veh_model</td>";
                        echo " <td>$dis_veh_year</td>";
                        echo "<td>$dis_veh_uid</td>";
                        echo "<td><a href='drive.php?uid=".htmlspecialchars($dis_veh_uid)."'>Add Drive</a></td>";
                        echo "<td><a href='vehicles.php?uid=".htmlspecialchars($dis_veh_uid)."'>Edit Vehicle</a></td>";
                        echo "</tr>";
                    } 
                      
                }
              }
            
            
                    ?>
                    
                </tbody>
            </table>
     </div>