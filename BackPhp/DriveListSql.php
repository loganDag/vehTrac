<!--10 row limit centered table for user vehicles-->
<div class="container-fluid d-flex miles_main_con py-3 mb-3">
                 <table class="table table-responsive table-hover caption-top table-striped-columns veh_table">
                        <caption>The 5 newest drives in our system</caption>
                        <thead>
                                <tr>
                            <th scope="col">Reason</th>
                            <th scope="col">Miles</th>
                            <th scope="col">Date and Time</th>
                                </tr>
                        </thead>
                            <tbody>
                     <?php                
$del_stat = '0';

// Prepare the SQL statement to avoid SQL injection and optimize execution
$DriveQuickInfoSql = "
    SELECT d.reason, d.total_miles, d.date_time
    FROM drives d
    WHERE d.user_uid = ? 
    AND d.del_stat = ?
    ORDER BY d.date_time DESC
    LIMIT 5";

// Prepare the statement
if ($stmt = $conn->prepare($DriveQuickInfoSql)) {

    // Bind the parameters to the prepared statement
    $stmt->bind_param('ss', $UserID_Cookie, $del_stat);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if we have results
    if ($result->num_rows > 0) {
        while ($DriveQuickInfoDis = $result->fetch_assoc()) {
            $dis_drive_reason = $DriveQuickInfoDis["reason"];
            $dis_drive_miles = $DriveQuickInfoDis["total_miles"];
            $dis_drive_datetime = $DriveQuickInfoDis["date_time"];
                    
                        echo "<tr>";
                        echo "<td>$dis_drive_reason</td>";
                        echo " <td>$dis_drive_miles</td>";
                        echo " <td>$dis_drive_datetime</td>";
                        echo "</tr>";
                    } 
                      
                }
              }
            
            
                    ?>
                    
                </tbody>
            </table>
     </div>