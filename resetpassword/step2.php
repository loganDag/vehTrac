<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require "$DocRoot/includes/header.php";
date_default_timezone_set('America/New_York');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$IP2 = $_SERVER["REMOTE_ADDR"];
$ErrorMessage = [];
if (isset($_GET["email"])){
$email = $_GET["email"];
}else{
    $ErrorMessage[] = "Seems like the email is not in the URL. Please check the link again.";
}
if (isset($_GET["code"])){
$reset_code = $_GET["code"];
}else{
    $ErrorMessage[] = "Seems like the Reset code is not in the URL. Please check the link again.";
}

if (isset($reset_code) && isset($email)){
}
if (isset($_POST["reset_password"])){
    $IP = $_SERVER["REMOTE_ADDR"];
    $new_password = $_POST["password_one"];
    $conf_password = $_POST["conf_password"];
    $entered_email = $_POST["reset_email"];


 if ($entered_email == $email){
$stmt = $conn->prepare("SELECT email, reset_pass_code, reset_pass_time_expire FROM user_info WHERE email = ?");
$stmt->bind_param("s", $entered_email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$db_reset_code = $result["reset_pass_code"];
$db_expire_time = $result["reset_pass_time_expire"];
        if ($reset_code == $db_reset_code){

        if ($conf_password == $new_password){
        $encrypted_pass = password_hash($conf_password, PASSWORD_BCRYPT);
        $GetcurrentTime = new DateTime();
        $currentTime = $GetcurrentTime->format('Y-m-d H:i:s');

        if ($currentTime <= $db_expire_time){
            $reset_success = "Y";
            $reset_code = 0;
            $stmt = $conn->prepare("UPDATE user_info SET password = ?, reset_pass_code = ?, reset_success = ? WHERE email = ?");
            $stmt->bind_param("ssss", $encrypted_pass, $reset_code, $reset_success, $email);
            $result = $stmt->execute();
            if($result){
                $ErrorMessage[] = "Password reset was successful!";
                $human_readable = $GetcurrentTime->format('F j, Y \\a\\t g:i a'); 

                                $Body = "
                       <!DOCTYPE html>
       <body>
       <h4>
       Security Alert.
       </h4>
       <p>
       Your account recently had its password reset at: $human_readable. <br>
       IP: $IP <br>
       If this was you, there is no further action that needs to be taken and you can disregard this email. <br>
       If you did not make these changes, please either click <a href='https://vehtrac.logandag.dev/ui/security'>Here.</a> <br>
       Or, reset your passsword again and change your email and/or username information.
       <br>
       Best regards, <br>
       VehTrac Administration <br>
       May God Bless and Keep you!
       </p>
       </body>
       </html>
                ";
            $mail->setFrom(
                'no-reply@logandag.dev',
                'VehTrac No reply'
            );
            $mail->addAddress($email);
            $mail->Subject = "Password reset";
            $mail->Body = $Body;

            if (!$mail->send()){}else{}
            }else{$ErrorMessage[] = "Password could not reset!";}
        }else{
            $ErrorMessage[] = "This code has expired, please request a new code.'";
        }
        }else{
            $ErrorMessage[] = "Passwords do not match, please try again.";
        }
    }else{
        $ErrorMessage[] = "The code in the URL and the one assigned do not match. Please check the link in the email or request a new code.";
    }
      }else{
        $ErrorMessage[] = "The email you entered and the one for the code do not match. Please check the link in your email again or the email field.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>VehTrac | Password Reset step 2</title>
    </head>
<body>

<div class="h-75 d-flex align-items-center justify-content-center">

    <div class="container col-md-4 form_items">
        <div class="jumbotron text-center">
                <?php
    if (!empty($ErrorMessage)):

?>
<div class="alert alert-info error-container">
        <?php
            $errors = implode("<br>", $ErrorMessage);
            echo $errors;
        ?>
</div>
<?php endif;?>
            <h1>VehTrac Reset Password step 2</h1>
        </div>
        <form action="" method="post">
                                <div class="form-floating mb-3">
                        <input type="email" class="form-control" 
                               id="reset_email" name="reset_email" placeholder="name@example.com" required 
                               value="">
                        <label for="reset_email">Confirm email address:</label>
                    </div>
                    <br>
                         <div class="form-floating mb-3">
                        <input type="password" class="form-control" 
                               id="password_one" name="password_one" placeholder="New Password:" required 
                               value="">
                        <label for="password_one">New Password:</label>
                    </div>
                    <br>
                         <div class="form-floating mb-3">
                        <input type="password" class="form-control" 
                               id="conf_password" name="conf_password" placeholder="Confirm Password:" required 
                               value="">
                        <label for="conf_password">Confirm Password:</label>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="reset_password">Reset Password</button>
                     <a href="/resetpassword" class="btn btn-link btn-md text-decoration-none">Click for new code.</a>
        </form>

    </div>
</div>
    <?php require "$DocRoot/includes/footer.html"; ?>

</body>
</html>