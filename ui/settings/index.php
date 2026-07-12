<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/includes/header.php");
require "$DocRoot/includes/cookieCheck.php";
require("$DocRoot/includes/menu.html");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$CookieTheme = $_COOKIE['SiteTheme'];
$ErrorMessage = [];
$UserTheme =  "";
$UserID = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT * FROM user_info WHERE user_uid=?");
$stmt->bind_param("s", $UserID);
$stmt->execute();
$UserInfoResult = $stmt->get_result();
while ($UserInfo = $UserInfoResult->fetch_assoc()) {
    $UserInfoUsername = $UserInfo["username"];
    $UserInfoRank = $UserInfo["site_rank"];
    $UserInfoEmail = $UserInfo["email"];
}

if (isset($_POST["theme_submit"])) {
    $theme = $_POST['theme_choice'] ?? 'light';
    $stmt = $conn->prepare('UPDATE user_info SET theme = ? WHERE user_uid = ?');
    $stmt->bind_param('ss', $theme, $UserID);
    $result = $stmt->execute();
    if ($result) {
        $ErrorMessage[] = 'Saved applied theme! This will take effect on your next login.';
    } else {
        $ErrorMessage[] = "We were unable to save your theme, please try again!";
    }
}

if ($UserInfoRank != '3'){
    $PassDisable = "";
}else{
    $PassDisable = "disabled";
}


if (isset($_POST["submit_new_email"])){
    $NewEmail = $_POST["new_email"];
    $reset_email_input = $_POST["reset_email_confirm"];
    $NewEmailLow = strtolower($NewEmail);
    $ResetEmailLow = strtolower($reset_email_input);

    if ($NewEmailLow == $ResetEmailLow){
        $CurrentTime = new DateTime();

        $interval = new DateInterval('PT15M');

        $expirationTime = $CurrentTime->add($interval);

        $expireTime = $expirationTime->format('Y-m-d H:i:s');

        $reset_email_code = random_int(100000, 999999);

        $stmt = $conn->prepare(
            "UPDATE user_info SET reset_email_expire = ?, reset_email_code = ? WHERE user_uid = ?"
        );
        $stmt->bind_param("sss", $expireTime, $reset_email_code, $UserID);
        $result = $stmt->execute();

        if ($result){
             $human_readable = $expirationTime->format('F j, Y \\a\\t g:i a'); 
            $Body = "
                       <!DOCTYPE html>
       <body>
       <h3>Email Reset Request:</h3>
       <hr>
       <br>
       <p>
       Here is your reset code: $reset_email_code
       You will have 15 mins, until: $human_readable, to reset it, <br> afterward you will need to request a new code.
       </p>
       <p>Please go <a href='https://vehtrac.logandag.dev/ui/settings/res_email_step2.php?email=$UserInfoEmail'>Here</a> to finish the reset process.
       If the link doesn't work, copy and paste this in your browser: <br> https://vehtrac.logandag.dev/ui/settings/res_email_step2.php?email=$UserInfoEmail</p>
        </body>
       </html>
                ";
            $mail->setFrom(
                'no-reply@logandag.dev',
                'VehTrac No reply'
            );
            $mail->addAddress($UserInfoEmail);
            $mail->Subject = "VehTrac Email reset Verification";
            $mail->Body = $Body;
            
            if (!$mail->send()){
                $ErrorMessage[] = "There was an error submitting the request. Please contact support if issue persists. Error code: SETEMAILSEND1";
            }else{
                $ErrorMessage[] = "Request submitted! Please finish the steps in the email, if you don't have it, please check your spam or resubmit the reset request!";
            }
        }else{
            $ErrorMessage[] = "There was an error submitting the request. Please contact support if issue persists. Error code: SETEMAILRES2";
        }

    }else{
        $ErrorMessage[] = "There was an error submitting the request. Please contact support if issue persists. Error code: SETEMAILRES1";
    }
}

if (isset($_POST["submit_password"])){
    
}
?>
<html>

<head>
    <title>
        VehTrac | Profile Settings
    </title>
</head>

<body data-bs-theme="<?php echo $_COOKIE['SiteTheme'] ?? 'light'; ?>">
    <div class="main_site_content h-100">
        <div class="text-center">
            <h2 class="jumbotron">Account Settings:</h2>
        </div>

        <a name="sessions"></a>
        <?php
        require("$DocRoot/BackPhp/DisplaySessions.php");
        if (!isset($UserInfoUsername)) {
            $UserInfoUsernameShow = "None set";
            $Disabled = "";
        } else {
            $UserInfoUsernameShow = $UserInfoUsername;
            $Disabled = "disabled";
        }
        ?>
        <hr>
        <?php if (!empty($ErrorMessage)): ?>
            <div class="alert alert-info error-container w-100 align-items-center text-center">
                <?php
                $errors = implode("<br>", $ErrorMessage);
                echo $errors;
                ?>
            </div>
        <?php endif; ?>
<div class="container">
    <div class="row rows-cols-1 rows-col-sm-2 g-4">
        <div class="col">
            <div class="username_form">
                <p class="fs-5 text-center">Reset Username:</p>
                <form action="" method="post" class="d-flex flex-column align-items-center mt-3">
                    <div class="form-floating mb-3 w-100">
                        <input type="text" value="<?php echo $UserInfoUsernameShow; ?>" id='username' name='username' <?php echo $Disabled; ?> class="form-control">
                        <label for="username">Username:</label>
                        <br>
                        <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="submit_username" <?php echo $Disabled; ?>>Submit Username</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col">
            <div class="reset_password">
                <p class="fs-5 text-center">Reset Password:</p>
                <form action="" method="post" class="d-flex flex-column align-items-center mt-3">
                    <div class="form-floating mb-3 w-100">
                        <input type="password" id='reset_pass_new' name='reset_pass_new' class="form-control" <?php echo $PassDisable; ?>>
                        <label for="reset_pass_new">New Password:</label>
                    </div>
                    <div class="form-floating mb-3 w-100">
                        <input type="password" id='reset_pass_confirm' name='reset_pass_confirm' class="form-control" <?php echo $PassDisable; ?>>
                        <label for="reset_pass_confirm">Confirm Password:</label>
                        <br>
                        <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="submit_password" <?php echo $PassDisable; ?>>Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
            <div class="col">
                <div class="change_email">
                  
                <p class="fs-5 text-center">Change Email:</p>
                <form action="" method="post" class="d-flex flex-column align-items-center mt-3">
                    <div class="form-floating mb-3 w-100">
                        <input type="email" id='new_email' name='new_email' class="form-control">
                        <label for="new_email">New Email:</label>
                    </div>
                    <div class="form-floating mb-3 w-100">
                        <input type="email" id='reset_email_confirm' name='reset_email_confirm' class="form-control">
                        <label for="reset_email_confirm">Confirm New Email:</label>
                        <br>
                        <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="submit_new_email">Start reset process</button>
                    </div>
                </form>
            </div>
            </div>
            <div class="col">
        <div class="theme-switch d-flex flex-column align-items-center text-center">
            <p class="fs-5">Display Theme:</p>
            <span class="fs-6 text-muted">Choose a theme to sync across your account for devices.</span>
            <form class="d-flex flex-column align-items-center mt-3" action="" method="post">

                <!-- Light Theme Radio Option -->
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="radio" id="theme_light" name="theme_choice" value="light" checked>
                    <label class="form-check-label" for="theme_light">Light theme.</label>
                </div>

                <!-- Dark Theme Radio Option -->
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="radio" id="theme_dark" name="theme_choice" value="dark">
                    <label class="form-check-label" for="theme_dark">Dark theme.</label>
                </div>

                <button type="submit" class="btn btn-primary" name="theme_submit">Save theme choice</button>
             </form>
        </div>
        </div>
        </div>
        <hr>
        <br />
        <br />
        </div>
                <div class="text-center">
            <h2 class="jumbotron">Profile Information:</h2>
        </div>
        <div class="container">
            <div class="row rows-cols-1 rows-col-sm-2 g-4">
             <div class="col">
                <p class="fs-5 text-center">Current email: <?php echo $UserInfoEmail;?></p>
                </div>
            </div>
        </div>
    </div> <!--END MAIN SITE CONTENT DIV-->
</body>
<?php require("$DocRoot/includes/footer.html"); ?>

</html>