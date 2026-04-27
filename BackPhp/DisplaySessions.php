        <div class="sessions">
           <p class="fs-4 text-muted">
                <p class="w-75 mx-auto">
                    Current Sessions:
                    <table class="table  table-responsive table-hover caption-top table-striped-columns sessions_table">
                        <thead>
                            <tr>
                                <th scope="col">Login Date:</th>
                                <th scope="col">Login IP:</th>
                                <th scope="col">Login Valid:</th>
                                <th scope="col">Deauthorize Sessions:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM user_logins WHERE user_uid = ? ORDER BY login_date DESC LIMIT 4");
        $stmt->bind_param("s", $UserID);
        $stmt->execute();
        $CookieInfoFetch = $stmt->get_result();
        while ($CookieInfo = $CookieInfoFetch->fetch_assoc()){

        $LoggedInIp = $CookieInfo["login_ip"];
        $LoggedInDate = $CookieInfo["login_date"];
        $RawCookieStatus = $CookieInfo["is_valid"];
        $db_cookie_id = $CookieInfo["cookie_id"];
        $FixedCookieStatus = "";

        if ($RawCookieStatus == "0"){
        $FixedCookieStatus = "Not authorized.";
        }elseif($RawCookieStatus == "1"){
        $FixedCookieStatus = "Authorized session.";
        }/*elseif($CookieID == $db_cookie_id){
           // $FixedCookieStatus = "This current session";
        }*/else{
        $FixedCookieStatus = "Error getting status.";
        }
                           echo "<tr>";
                            echo "<td>";
                        echo $LoggedInDate;
                            echo "</td>";
                            echo" <td>";
                              echo $LoggedInIp;
                            echo "</td>";
                              echo "<td>";
                                echo $FixedCookieStatus;
                               echo "</td>";
                               echo "<td>";
                               echo "<a href='deauth.php?id=$db_cookie_id'>Deauthorize this session</a>";
                               echo "</td>";
                        echo "</tr>";
    }
                            ?>
                        </tbody>
                    </table>
            </p>
            </p>
        </div>