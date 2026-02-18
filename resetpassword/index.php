<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
//include "$DocRoot/../bootstrap.html";
require "$DocRoot/includes/header.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
// Set the default timezone to ensure correct time calculations
date_default_timezone_set('America/New_York');

//Initialize variable
if (!isset($email_input)) {
    $email_input = '';
}
if (isset($_POST["reset_password"])) {
    $email_input = $_POST["reset_email"];
    $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
    $stmt->bind_param('s', $email_input);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    $db_email = $result["email"];
    $new_db_email = strtolower($db_email);
    $new_email_input = strtolower($email_input);
    if ($new_db_email == $new_email_input) {
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
            "UPDATE user_info SET reset_pass_time_expire = ?, reset_pass_code = ? WHERE email = ?"
        );
        $stmt->bind_param("sss", $expireTime, $reset_pass_code, $new_db_email);
        $result = $stmt->execute();

        if ($result) {
            $Body = "
                       <!DOCTYPE html>
       <body>
       <h2>
       Here is your reset code: $reset_pass_code
       You will have 15 mins, until $expireTime, to reset it, afterward you will need to request a new code.
       </h4>
       <h3>Please go <a href='https://vehtrac.logandag.dev/resetpassword/step2.php?code=$reset_pass_code'>Here</a> to finish the reset process.</h3>
       if the link doesn't work, copy and paste this in your browser: https://vehtrac.logandag.dev/step2.php?code=$reset_pass_code
       </body>
       </html>
                ";
            $mail->setFrom(
                'no-reply@logandag.dev',
                'VehTrac No reply'
            );
            $mail->addAddress($email_input);
            $mail->Subject = "VehTrac Password reset Verification";
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
        <form action="" method="post">
                                <div class="form-floating mb-3">
                        <input type="email" class="form-control" 
                               id="reset_email" name="reset_email" placeholder="name@example.com" required 
                               value="">
                        <label for="reset_email">Email address</label>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="reset_password">Reset Password</button>
        </form>

    </div>
</div>
    <?php require "$DocRoot/includes/footer.html"; ?>

</body>
</html>