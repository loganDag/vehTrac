<?php
require('dbconnect.php');
    //$connError = $conn->connect_error;
    //header('refresh:0; url=/dash.php?e='.$connError);

$UserID = $_COOKIE['user_id']; // Get user ID from cookie

if (isset($_POST['delete_vehicle'])) {
    $RemoveID = $_POST['delete_veh_id'];

    // Check if the vehicle exists in user_vehicles
    $sql = "SELECT * FROM user_vehicles WHERE veh_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $RemoveID); // "s" for string type
    $stmt->execute();
    $result = $stmt->get_result();

    // If no vehicle found, redirect with error
    if ($result->num_rows <= 0) {
        header('Location: /dash.php?de=1&db_id=' . urlencode($RemoveID));
        exit();
    }

    // Loop through vehicle info and check if the user is authorized
    while ($VehInfo = $result->fetch_assoc()) {
        $user_uid = $VehInfo["user_uid"];
        $veh_uid = $VehInfo["veh_uid"];

        // Check if the current user owns the vehicle
        if ($UserID == $user_uid) {

            // Update the vehicle's del_stat field in the vehicles table (set to 0)
            $sql = "UPDATE vehicles SET del_stat = 1, del_stat_reason = ?, del_date = ? WHERE veh_uid = ?";
            $stmt = $conn->prepare($sql);
            $reason = "User removed vehicle";
            $date_removed = date('Y-m-d H:i:s');
            $stmt->bind_param("sss", $reason, $date_removed, $RemoveID); // "s" for string type
            if ($stmt->execute()) {
                // Success - redirect to dashboard
                header('Location: /dash.php?s=1&uid=' . urlencode($UserID));
                exit();
            } else {
                // Handle errors if updating del_stat fails
                $connError = $sql . $conn->error;
                header('Location: /dash.php?e=' . urlencode($connError));
                exit();
            }

        } else {
            // If the user doesn't own the vehicle, redirect with an error
            header('Location: /dash.php?de=2');
            exit();
        }
    }
}

$conn->close(); // Close the database connection

//header('refresh:0; url=/dash.php?e='.$connError); //result error
//header('refresh:0; url=/dash.php?s=1&uid='.$UserID); //Success
//header('refresh:0; url=/dash.php?de=2'); //User id no match
?>