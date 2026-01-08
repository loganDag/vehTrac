<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
include "$DocRoot/../bootstrap.html";
include "../includes/header.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
// Set the default timezone to ensure correct time calculations
date_default_timezone_set('America/New_York');

//Initialize variable
if (!isset($text_input)) {
    $text_input = '';
}
if (isset($_POST["reset_password"])) {
    $text_input = $_POST["reset_email"];
    $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = :email");
    $stmt->bindParam(':email', $text_input);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $db_email = $result["email"];
    $new_db_email = strtolower($db_email);
    $new_text_input = strtolower($text_input);
    if ($new_db_email == $new_text_input) {
        //successful email match, now give reset auth code and set an expiration of 15 mins.

        // Get the current date and time
        $currentTime = new DateTime();
        // $CurrentTime = new DateTime();

        // Create a DateInterval object for 15 minutes
        $interval = new DateInterval('PT15M'); // PT represents "period time", 15M for 15 minutes

        // Add the interval to the current time
        $expirationTime = $currentTime->add($interval);

        // Format the expiration time for storage or display
        $expireTime = $expirationTime->format('Y-m-d H:i:s');

        // echo "Current time: " . $CurrentTime->format('Y-m-d H:i:s') . "\n";
        // echo "Password reset expiration time: " . $formattedExpirationTime . "\n";
        $reset_pass_code = random_int(100000, 999999);

        $stmt = $conn->prepare(
            "UPDATE user_info SET reset_pass_time_expire = :expireTime, reset_pass_code = :PassCode WHERE email = :email"
        );
        $result = $stmt->execute([
            'expireTime' => $expireTime,
            'PassCode' => $reset_pass_code,
            'email' => $new_text_input,
        ]);

        if ($result) {
            $Body = "
                       <!DOCTYPE html>
       <body>
       <h2>
       Here is your reset code: $reset_pass_code
       You will have 15 mins, until $expireTime, to reset it, afterward you will need to request a new code.
       </h4>
       <h3>Please go <a href='https://link?code=$reset_pass_code'>Here</a> to finish the reset process.</h3>
       if the link doesn't work, copy and paste this in your browser: https://link?code=$reset_pass_code
       </body>
       </html>
                ";
            $mail->setFrom(
                'Email',
                'From name'
            );
            $mail->addAddress($text_input);
            $mail->Subject = "Subject";
            $mail->Body = $Body;
            if (!$mail->send()) {
                echo "<div class='d-flex align-items-center justify-content-center'>";
                echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Reset issue</h3>";
                echo "There was an issue with emailing the verification, please email the webmaster.</div></div>";
            } else {
                echo "<div class='d-flex align-items-center justify-content-center'>";
                echo "<div class='alert alert-success text-center' role='alert'> <h3 class='alert-header'>Reset request successful!</h3>";
                echo "We were able to submit the request, please look out for an email for the code, it is only valid for 15 mins.</div></div>";
            }
        } else {
            echo "<div class='d-flex align-items-center justify-content-center'>";
            echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Reset issue</h3>";
            echo "There was an issue with submitting the request, please try again or contact the webmaster.</div></div>";
        }
    } else {
        echo "<div class='d-flex align-items-center justify-content-center'>";
        echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Reset issue</h3>";
        echo "There was an issue with resetting the password, that email does not exist. Please try again.</div></div>";
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>VehTrac | Password Reset</title>
    </head>
<body>

<div class="h-75 d-flex align-items-center justify-content-center">
    <div class="container col-md-4 form_items">
        <div class="jumbotron text-center">
            <h1>VehTrac Reset Password</h1>
        </div>
        <div id="loading" style="display: none;">
            <img src='includes/images/loading.gif' alt="Loading...">
        </div>
        <form action="" method="post" id="login_form">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name='themeToggle' id="themeToggle" <?php if (
                    $theme == "dark"
                ) {
                    echo "checked";
                } ?>>
                <label class="form-check-label" for="themeToggle">Dark/light Mode</label>
            </div>
            <h3 class="text-muted">Sign in here:</h3>
            <div class='form-floating'>

                <input type='email' class='form-control <?php if (
                    $LoginError == "1"
                ) {
                    echo " is-invalid";
                } ?>' <?php if ($LoginError == "1") {
    echo "value='$email'";
} ?> id='Log_email' name='Log_email' placeholder='' required>
                <label for='Log_email'>Email:</label>
                <?php if ($LoginError == "1") {
                    echo " <div class='invalid-feedback'>Invalid email or password.</div>";
                } ?>
            </div>  
            <br>
            <div class='form-floating'>
                <input type='password' class='form-control <?php if (
                    $LoginError == "1"
                ) {
                    echo " is-invalid";
                } ?>' id='Log_password' name='Log_pass' placeholder='Enter password' required>
                <label for='Log_password'>Password:</label>
                <?php if ($LoginError == "1") {
                    echo " <div class='invalid-feedback'>Invalid email or password.</div>";
                } ?>
            </div>
            <br>
            <div class="text-center form-row">
                <button type="submit" class="btn btn-primary" name="signin_button">Sign In</button>
            </div>
        </form>
        <div class="separator">Or</div>
        <br>

        <!-- Button trigger modal -->
        <div class="form-row text-center">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sign_up">
                Sign up </button>
            <a href="/resetpassword" class="btn btn-primary" role="button">Reset Password</a>

        </div>
    </div>
</div>
    <?php require "../includes/footer.html"; ?>
    </body>
    <script>
        // Function to toggle between dark and light themes
        function toggleTheme() {
            const body = document.body;
            const newTheme = themeToggleSwitch.checked ? 'dark' : 'light';

            // Toggle the theme
            body.setAttribute('data-bs-theme', newTheme);
        }

        // Get the theme toggle switch element
        const themeToggleSwitch = document.getElementById('themeToggle');

        // Add an event listener to the theme toggle switch
        themeToggleSwitch.addEventListener('change', toggleTheme);

    </script>
</html>