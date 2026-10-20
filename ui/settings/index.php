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
$Full_Name = "";
$UserID = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT * FROM user_info WHERE user_uid=?");
$stmt->bind_param("s", $UserID);
$stmt->execute();
$UserInfoResult = $stmt->get_result();
while ($UserInfo = $UserInfoResult->fetch_assoc()) {
    $UserInfoUsername = $UserInfo["username"];
    $UserInfoRank = $UserInfo["site_rank"];
    $UserInfoEmail = $UserInfo["email"];
    $UserIDNum = $UserInfo["user_uid"];
    $UserInfoFName = $UserInfo["FName"];
    $UserInfoLName = $UserInfo["LName"];
}

if ($UserInfoFName == null || $UserInfoLName == null){
$Full_Name == "Name properly set, please edit";
}else{
$Full_Name = $UserInfoFName . " " . $UserInfoLName;
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
if (isset($_POST["submit_reset_username"])){
    $rawUsername = $_POST["username"];
    $cleanUsername = preg_replace('/[^a-zA-Z0-9]/', '', $rawUsername);
    $_SESSION["preferred_username"] = $cleanUsername;

    if (isset($_SESSION["preferred_username"])){
        header('Location: https://vehtrac.logandag.dev/ui/settings/reset-username.php');
        exit();
    }else{
        $ErrorMessage[] = "We are unable to redirect to the username reset page, for some reason the session won't set.";
        $ErrorMessage[] = "Please tell support, error code: SETUNRESET1";
    }
}
if (isset($_POST["submit_delete"])){
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VehTrac | Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body data-bs-theme="<?php echo $_COOKIE['SiteTheme'] ?? 'light'; ?>">
        <?php
        if (!isset($UserInfoUsername)) {
            $UserInfoUsernameShow = "None set";
            $Disabled = "";
        } else {
            $UserInfoUsernameShow = $UserInfoUsername;
            $Disabled = "disabled";
        }
        ?>
        <?php if (!empty($ErrorMessage)): ?>
            <div class="alert alert-info error-container w-100 align-items-center text-center">
                <?php
                $errors = implode("<br>", $ErrorMessage);
                echo $errors;
                ?>
            </div>
        <?php endif; ?>

<div class="container mt-5">
    
    <div class="row">
        <!-- 1. THE SIDEBAR / TOP BAR (Full width on mobile, 3/12 columns on desktop) -->
        <div class="col-12 col-md-3 mb-3 mb-md-0">
            
            <!-- 
              nav-pills setup:
              - flex-row flex-nowrap overflow-x-auto: scrollable horizontal row on mobile
              - flex-md-column: snaps to vertical sidebar on desktop (md screens and up)
              - gap-1: clean spacing between buttons in both directions
            -->
            <div class="nav nav-pills flex-row flex-nowrap overflow-x-auto flex-md-column border rounded p-2 bg-body-tertiary gap-1" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                
                <button class="nav-link active text-nowrap text-md-start" id="tab-profile" data-bs-toggle="pill" data-bs-target="#content-profile" type="button" role="tab">Profile Info</button>
                
                <button class="nav-link text-nowrap text-md-start" id="tab-account" data-bs-toggle="pill" data-bs-target="#content-account" type="button" role="tab">Account Mgmt</button>
                
                <button class="nav-link text-nowrap text-md-start" id="tab-security" data-bs-toggle="pill" data-bs-target="#content-security" type="button" role="tab">Security</button>
                
                <button class="nav-link text-nowrap text-md-start" id="tab-preferences" data-bs-toggle="pill" data-bs-target="#content-preferences" type="button" role="tab">Preferences</button>

            </div>
        </div>

        <!-- 2. THE MAIN CONTENT (Full width on mobile, 9/12 columns on desktop) -->
        <div class="col-12 col-md-9">
            
            <!-- Tab content container -->
            <div class="tab-content border rounded p-4 shadow-sm" id="v-pills-tabContent">
                
                <!-- Profile Content -->
                <div class="tab-pane fade show active" id="content-profile" role="tabpanel">
                    <h3 class="border-bottom pb-2 mb-3">Profile Information</h3>

                            <div class="container">
            <div class="row rows-cols-1 rows-col-sm-2 g-4">
             <div class="col">
                <p class="fs-5 text-center">Current email: <?php echo $UserInfoEmail;?></p>
                </div>
                <div class="col">
                    <p class="fs-5 text-center">User ID Number: <?php echo $UserIDNum;?></p>
                    </div>
                </div>
                <div class="row rows-cols-1 rows-col-sm-2 g-4">
                    <div class="col">
                        <p class="fs-5 text-center">Name: <?php echo $Full_Name;?></p>
                    </div>
                    <div class="col">
                        <p class="fs-5 text-center">Username: <?php echo $UserInfoUsernameShow;?></p>
                    </div>
                </div>
            </div>
                </div>

                <!-- Account Content -->
                <div class="tab-pane fade" id="content-account" role="tabpanel">
                    <h3 class="border-bottom pb-2 mb-3">Account Management</h3>
                    <!--change email-->
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
<!--change username-->
            <div class="username_form">
                <p class="fs-5 text-center">Reset Username:</p>
                <form action="" method="post" class="d-flex flex-column align-items-center mt-3">
                    <div class="form-floating mb-3 w-100">
                        <input type="text" value="<?php echo $UserInfoUsernameShow; ?>" id='username' name='username' <?php echo $Disabled; ?> class="form-control">
                        <label for="username">Username:</label>
                        <br>
                        <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="submit_reset_username" <?php echo $Disabled; ?>>Submit Username</button>
                    </div>
                </form>
            </div>

                </div>

                <!-- Security Content -->
                <div class="tab-pane fade" id="content-security" role="tabpanel">
                    <h3 class="border-bottom pb-2 mb-3">Security</h3>
                                        <!--List active sessions-->
                     <?php  require("$DocRoot/BackPhp/DisplaySessions.php"); ?>
                   <!--Reset Password-->
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
            <!--Delete account-->
                                <div class="col d-flex justify-content-center">
                        <form action="" method="post"  class="w-100 d-flex justify-content-center">
                        <button class="w-50 btn btn-lg btn-danger mb-3" type="submit" name="submit_delete">Delete Account</button>
                        </form>
                    </div>
                </div>

                <!-- Preferences Content -->
                <div class="tab-pane fade" id="content-preferences" role="tabpanel">
                    <h3 class="border-bottom pb-2 mb-3">Preferences</h3>
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
        
    </div>
</div>

<!-- Bootstrap JS is REQUIRED for data-bs-toggle to work -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    // 1. Get the hash from the URL (e.g., "#settings")
    const hash = window.location.hash;
    
    if (hash) {
      // 2. Find the pill button that targets this specific content ID
      const targetTrigger = document.querySelector(`[data-bs-target="${hash}"]`);
      
      if (targetTrigger) {
        // 3. Initialize and trigger the Bootstrap Tab/Pill instance
        const tab = new bootstrap.Tab(targetTrigger);
        tab.show();
      }
    }
  });
</script>
</body>
</html>