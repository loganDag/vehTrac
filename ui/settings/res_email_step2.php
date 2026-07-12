<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require "$DocRoot/includes/header.php";
date_default_timezone_set('America/New_York');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$CLIP = $_SERVER["REMOTE_ADDR"];
$IP2 = $_SERVER['HTTP_CF_CONNECTING_IP'];
$ErrorMessage = [];
if (isset($_GET["email"])){
$email = $_GET["email"];
}else{
    $ErrorMessage[] = "Seems like the email is not in the URL. Please check the link again.";
}

if (isset($_POST["reset_email"])){
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])){
        $IP = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }else{
    $IP = $_SERVER["REMOTE_ADDR"];
    }

        $json = file_get_contents("https://ipinfo.io/$IP/geo/?token=e99f8b9a79352f");
$json1 = json_decode($json, true);
$country = $json1['country'];
$region = $json1['region'];
$city = $json1['city'];
$coordinates = $json1['loc'];
$postal = $json1['postal'];
    $old_email = $_POST["old_email"];
    $new_email = $_POST["new_email"];
    $conf_email = $_POST["conf_email"];
    $reset_code_input = $_POST["conf_passcode"];

 if ($old_email == $email){
$stmt = $conn->prepare("SELECT user_uid, reset_email_expire, reset_email_code FROM user_info WHERE email = ?");
$stmt->bind_param("s", $old_email);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if (!$result){
    $ErrorMessage[] = "That email does not seem to exist, please try the link again. If that doesn't work, please resubmit the reset request.";
}else{
$db_user_uid = $result["user_uid"];
$db_reset_code = $result["reset_email_code"];
$db_expire_time = $result["reset_email_expire"];
        if ($reset_code_input == $db_reset_code){

        if ($conf_email == $new_email){
        $GetcurrentTime = new DateTime();
        $currentTime = $GetcurrentTime->format('Y-m-d H:i:s');

        if ($currentTime <= $db_expire_time){
            $reset_success = "Y";
            $reset_code = 0;
            $stmt = $conn->prepare("UPDATE user_info SET email = ?, reset_email_code = ?, reset_email_success = ? WHERE user_uid = ?");
            $stmt->bind_param("sssi", $new_email, $reset_code, $reset_success, $db_user_uid);
            $result = $stmt->execute();
            if($result){
                $ErrorMessage[] = "Email reset was successful!";
                $human_readable = $GetcurrentTime->format('F j, Y \\a\\t g:i a'); 

                                $Body = "
                       <!DOCTYPE html>
       <body>
       <h4>
       Security Alert.
       </h4>
       <p>
       Your account recently had its email reset at: $human_readable. <br>
       From IP: $IP <br>
       If this was you, there is no further action that needs to be taken and you can disregard this email. <br>
       If you did not make these changes, please click <a href='https://vehtrac.logandag.dev/ui/support'>Here.</a> or contact support.<br>
       <br>
       <hr>
       Location details of device who reset your email: <br>
       $city, $region, $country
       <hr>
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
            $mail->addAddress($old_email);
            $mail->Subject = "Account Security Alert - Email was reset.";
            $mail->Body = $Body;

            if (!$mail->send()){}else{}

            }else{$ErrorMessage[] = "Email could not be reset! Error: EMAILRESST2UPDB";}
        }else{
            $ErrorMessage[] = "This code has expired, please request a new code.'";
        }
        }else{
            $ErrorMessage[] = "Emails do not match, please try again.";
        }
    }else{
        $ErrorMessage[] = "The code in the URL and the one assigned do not match. Please check the link in the email or request a new code.";
        }
    }      
    }else{
        $ErrorMessage[] = "The email in either the URL or the one given as the old email do not match, please check them again.";
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>VehTrac | Reset Email Step 2</title>
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
                               id="old_email" name="old_email" placeholder="name@example.com" required 
                               value="">
                        <label for="old_email">Confirm old email address:</label>
                    </div>
                    <br>
                         <div class="form-floating mb-3">
                        <input type="email" class="form-control" 
                               id="new_email" name="new_email" placeholder="New Email:" required 
                               value="">
                        <label for="new_email">New Email:</label>
                    </div>
                    <br>
                         <div class="form-floating mb-3">
                        <input type="email" class="form-control" 
                               id="conf_email" name="conf_email" placeholder="Confirm Email:" required 
                               value="">
                        <label for="conf_email">Confirm New Email:</label>
                    </div>
                    <br>
                     <div class="form-floating mb-3">
                        <input type="text" class="form-control" 
                               id="conf_passcode" name="conf_passcode" placeholder="" required 
                               value="">
                        <label for="conf_passcode">Confirm Passcode:</label>
                    </div>
                    <br>
                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="reset_email">Reset Email</button>
                    <p class="fs-5 alert alert-warning">If you need a new code, <a href="https://vehtrac.logandag.dev">signin</a> with your old info and go through the process again.</p>
        </form>

    </div>
</div>
    <?php require "$DocRoot/includes/footer.html"; ?>

</body>
</html>