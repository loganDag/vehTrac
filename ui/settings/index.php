<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
require "$DocRoot/includes/cookieCheck.php";
require("$DocRoot/includes/menu.html");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$UserID = $_SESSION["user_id"];
     $stmt = $conn->prepare("SELECT * FROM user_info WHERE user_uid=?");
     $stmt->bind_param("s", $UserID);
     $stmt->execute();
    $UserInfoResult = $stmt->get_result();
    while ($UserInfo = $UserInfoResult->fetch_assoc()){
            $UserInfoUsername = $UserInfo["username"];
   }
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

        <a name="sessions"></a>
        <?php
        require ("$DocRoot/BackPhp/DisplaySessions.php");
          if (!isset($UserInfoUsername)){
            $UserInfoUsernameShow = "None set";
            $Disabled = "";
          }else{
            $UserInfoUsernameShow = $UserInfoUsername;
            $Disabled = "disabled";
          }
        ?>
         <div class="username_form ">
            <form action="" method="post" class="d-flex flex-column align-items-center mt-3">
                <div class="form-floating mb-3 w-auto">
                <input type = "text" value = "<?php echo $UserInfoUsernameShow; ?>" id='username' name='username' <?php echo $Disabled; ?> class="form-control">
                <label for="username">Username:</label>
                <br>
                <button class="w-auto btn btn-lg btn-primary mb-3" type="submit" name="submit_username" <?php echo $Disabled; ?>>Submit Username</button>
                </div>
            </form>
            
        </div>

        <div class="reset_password">

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