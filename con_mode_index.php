<!doctype HTML>
<html lang='en-US'>
    <?php
    //require('includes.php');
    include("../bootstrap.html");
    /*
$ip = $_SERVER["REMOTE_ADDR"];
$date = date('Y-m-d H:i:s');
$json = file_get_contents("https://ipinfo.io/$ip?token=e99f8b9a79352f");
$json1 = json_decode($json, true);
$country = $json1['country'];
$region = $json1['region'];
$city = $json1['city'];
$coordinates = $json1['loc'];
$postal = $json1['postal'];
$org = $json1['org'];

$sql = "INSERT INTO ipThings (date, ip, org, coordinates) VALUES('" . $date . "', '" . $ip . "', '" . $org . "', '" . $coordinates . "')";
if ($conn->query($sql) == false) {
}
*/
    require 'phpmailer/phpmailer/src/Exception.php';
    require 'phpmailer/phpmailer/src/PHPMailer.php';
    require 'phpmailer/phpmailer/src/SMTP.php';
    require 'phpmailer/phpmailer/src/settings.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;

    if (isset($_POST["submit_form"])) {
        $name = $_POST['name'];
        $email = $_POST['email'];

        // Initialize checks
        $NameCheck = '';
        $EmailCheck = '';

        // Check if name is not set
        if (empty($name)) {
            $NameCheck = '1';
        }

        // Check if email is not set
        if (empty($email)) {
            $EmailCheck = '1';
        } else {
            // Check for @ symbol in email
            $EmailSymbol = strpos($email, '@');

            // Check if @ symbol is not found
            if ($EmailSymbol === false) {
                $EmailCheck = '3';
            }
        }

        // Check for errors
        if ($EmailCheck == '1' || $NameCheck == '1' || $EmailCheck == '3') {
            $Error = '1';
        } else {
            // All checks passed, process the submission
            $Body = "<!DOCTYPE html>";
            $Body .= "<h4>New newsletter submission form!</h4>";
            $Body .= "<h5>Someone subscribed for newsletters.</h5>";
            $Body .= "<h5>Name is: $name <br><br></h5>";
            $Body .= "<h5>Email is: $email</h5>";
            $Body .= "</html>";

            // Assuming you have initialized the $mail object earlier in your code
            $mail->setFrom('no-replyemail', 'contact name');
            $mail->addAddress('email to adress');
            $mail->Subject = 'Newsletter submission';
            $mail->Body = $Body;

            if (!$mail->send()) {
                $EmailSendError = '2';
                $Error = '1';
            } else {
                $EmailSendError = '1';
            }
            }
    }
    ?>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>VehTrac | NexGen IT Digital, LLC</title>
    </head>

    <body>
        <div class="container">
            <div class="info_container">
                <?php
                if ($Error == '1') {
                    echo "<div class='d-flex align-items-center justify-content-center'>";
                    echo "<div class='alert alert-danger alert-dismissible text-center' role='alert'> <h3 class='alert-header'>Sign up Issue</h3>";
                    echo "Something went wrong. Make sure you have an @ symbol in your email.</div></div>";
                }
                if ($EmailSendError == '1') {
                    echo "<div class='d-flex align-items-center justify-content-center'>";
                    echo "<div class='alert alert-success alert-dismissible text-center' role='alert'> <h3 class='alert-header'>Sign up completed!</h3>";
                    echo "You are now signed up for newsletters! Expect them weekly.</div></div>";
                } else if ($EmailSendError == '2') {
                    echo "Email not sent.<br>" . $mail->ErrorInfo . "";
                }
                ?>

                <!-- Original gray color for the alert -->
                <div class="alert alert-info change_alert_info">
                    <h2 class="alert-heading jumbotron">Website Announcement!</h2>
                    <hr>
                    <p class="text-muted fs-3 alert-body">As of right now, December 23rd, 2024, this website is being taken offline due to some recent security vulnerabilities.</p>
                    <p class="text-muted fs-6 alert-body">Thanks and Honor to God, a friend of mine was able to find some issues with this website that I have the God-given thought to take down right now and fix.</p>
                    <p class="text-muted fs-6 alert-body">I do not have an estimated time of when this website will be functional, I thank God that it seems like no one else has found these leaks.</p>
                    <p class="text-muted fs-6 alert-body">From what I can tell, I also thank God that no information is leaked, all sessions will be logged out eventually with the security implementations we will have that I thank God for the wisdom to implement it and what to do.</p>
                    <p class="text-muted fs-6 alert-body">Subscribe to the email lists in order to receive information about what is happening and keep track of the <a href="/softcenter">Software Center</a> page as well for updates. Glory and Honor to be God Almighty, Amen!</p>
                </div>

                <!-- Original gray color for the form alert -->
                <div class="alert alert-info news-letter change_alert_info">
                    <h2 class="alert-heading jumbotron">Sign up for the newsletter</h2>
                    <hr>
                    <p class="text-muted fs-6 alert-body">Fill out form below all * are required</p>
                    <form action="" method="post" class="alert-body">
                        <div class="form-floating">
                            <input type="text" name="name" id="name" class="form-control" placeholder="Your name">
                            <label for="name">Your name *</label>
                        </div>
                        <br>
                        <div class="form-floating">
                            <input type="text" name="email" class="form-control" id="email" placeholder="Your email">
                            <label for="email">Your email *</label>
                        </div>
                        <!-- Using the default button style -->
                        <button type="submit" class="btn btn-primary submit_button" name="submit_form">Sign up!</button>
                    </form>
                </div>
            </div>
        </div>
    </body>

    <style>
        body {
            display: flex !important;
            justify-content: center !important; /* Centers content horizontally */
            align-items: center !important; /* Centers content vertically */
            min-height: 100vh !important; /* Take full height of viewport */
            margin: 0 !important;
            background-color: lightslategrey !important;
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        .info_container {
            display: flex !important;
            flex-direction: column !important;
            background-color: lightgray !important;
            font-family: 'Times New Roman', Times, serif !important;
            box-shadow: 0 4px 8px 2px rgba(0, 0, 0, 0.3) !important;
            padding: 20px !important;
            width: 100%; /* Ensure full width of container */
            max-width: 800px; /* Restrict the width to prevent it from being too wide on large screens */
            margin: 0 auto; /* Center it horizontally */
            border-radius: 15px !important;
        }

        @media (max-width: 768px) {
            .info_container {
                width: 100%; /* Full width on mobile */
                max-width: none; /* Remove width restrictions on mobile */
            }
        }

        .form-floating {
            margin-bottom: 15px !important;
        }

        .submit_button:hover {
            box-shadow: 0 0px 8px 2px rgba(0, 0, 0, 0.3) !important;
        }

        /* Ensures proper scrolling on mobile */
        @media (max-height: 700px) {
            body {
                align-items: flex-start !important;
            }
        }
        .change_alert_info {
            background-color: lightgray !important;
            border: 0px !important;
        }
    </style>
</html>
