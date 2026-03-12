<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
require "$DocRoot/includes/cookieCheck.php";
require("$DocRoot/includes/menu.html");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$UserID = $_SESSION["user_id"];

?>
<html>
    <head>
        <title>
            VehTrac | Profile Settings
        </title>
    </head>
<body data-bs-theme="<?php echo $theme;?>">
    <div class="main_site_content">
        <div class="text-center">
        <h2 class="jumbotron">Account Settings:</h2>
        </div>
<div class="theme-switch d-flex flex-column align-items-center text-center">
    <p class="fs-5">Display Theme:</p>
    <span class="fs-6 text-muted">Choose a theme to sync across your account for devices.</span>

    <form class="d-flex flex-column align-items-center mt-3" action="" method="post">

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="themeMemorySync_Light" name="themeMemorySync_Light">
            <label class="form-check-label" for="themeMemorySync_Light">Light theme.</label>
        </div>

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" id="themeMemorySync_Dark" name="themeMemorySync_Dark">
            <label class="form-check-label" for="themeMemorySync_Dark">Dark theme.</label>
        </div>

        <button type="submit" class="btn btn-primary" name="theme_submit">Save theme choice</button>
    </form>
</div>

        <a name="sessions"></a>
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
                                <th scope="col">Deauthroize Sessions:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM user_logins WHERE user_uid = ? ORDER BY login_date DESC");
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
        <div class="reset_password">

        </div>
    </div> <!--END MAIN SITE CONTENT DIV-->
</body>
<?php require("$DocRoot/includes/footer.html"); ?>
<script>
    // Theme Toggle Script
    document.getElementById('themeMemorySync_Dark').addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        document.body.setAttribute('data-bs-theme', theme);
    });
</script>
</html>