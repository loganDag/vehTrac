<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
/// This is the "Settings" page username reset feture.
/// This will use the email from the session_start in order to change with the database
/// The "Forgot Username" page will require the email in order to obtain, NOT reset, the username.

// 1. Get the path to the 'vehtrac' folder (two levels up from /ui/settings/)
$vehtracRoot = dirname(__DIR__, 2);

// 2. Include header and database using the calculated root
require_once $vehtracRoot . "/includes/header.php";
require_once $vehtracRoot . "/includes/cookieCheck.php";

$CLIP = $_SERVER["REMOTE_ADDR"];
$IP2 = $_SERVER['HTTP_CF_CONNECTING_IP'];
$ErrorMessage = [];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$user_uidGet = $_GET["user_uid"] ?? null;
if (!$user_uidGet) {
    $ErrorMessage[] = "User UID is missing from the URL. Please check the link again.";
}else{
    $user_uid = $user_uidGet;
}

// Set the default timezone to ensure correct time calculations
date_default_timezone_set('America/New_York');
session_start();


?>

<!DOCTYPE html>
<html>
<head>
    <title>VehTrac registration completion</title>
    </head>
<body>
        <?php if (!empty($ErrorMessage)): ?>
            <div class="alert alert-info error-container w-100 align-items-center text-center">
                <?php
                $errors = implode("<br>", $ErrorMessage);
                echo $errors;
                ?>
            </div>
        <?php endif; ?>
<div class="h-75 d-flex align-items-center justify-content-center">
    <div class="container col-md-4 form_items">
        <div class="jumbotron text-center">
            <h1>VehTrac registration completion</h1>
        </div>
        <form action="" method="post">
                                <div class="form-floating mb-3">
                        <input type="email" class="form-control" 
                               id="reset_email" name="reset_email" placeholder="name@example.com" required 
                               value="">
                        <label for="reset_email">Email address</label>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="reset_password">Reset Password</button>
                     <a href="https://vehtrac.logandag.dev" class="btn btn-link btn-md text-decoration-none">Login</a>
        </form>

    </div>
</div>
    <?php require "$DocRoot/includes/footer.html"; ?>

</body>
</html>