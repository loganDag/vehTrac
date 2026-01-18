<?php

//Start Sign up function
if (isset($_POST["submit_register"])) {
    //Start signup statement
    $sub_email = $_POST["signup_email"];
    $sub_pass = $_POST["signup_pass"];
    $sub_confpass = $_POST["signup_confpass"];
    if ($sub_pass != $sub_confpass) {
        echo "<div class='d-flex align-items-center justify-content-center'>";
        echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Password Issue</h3>";
        echo "Passwords do not match, please try again</div></div>";
    } elseif ($sub_pass == $sub_confpass) {
        $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
        $stmt->bind_param("s", $sub_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $ResultsQuery = $result->fetch_assoc();

        if (!$ResultsQuery) {
            $user_set_uid = rand(1, 10000);
            $stmt = $conn->prepare(
                "SELECT * FROM user_info WHERE user_uid = ?"
            );
            $stmt->bind_param("i", $user_set_uid);
            $stmt->execute();
            $result = $stmt->get_result();
            $ResultsQuery = $result->fetch_assoc();

            while ($ResultsQuery) {
                $user_set_uid = rand(1, 10000);
                $stmt->execute();
                $result = $stmt->get_result();
                $ResultsQuery = $result->fetch_assoc();
            }

            $reg_date = date('Y-m-d H:i:s');
            $site_rank = "0";
            $sub_tier = "1";
            $lock_status = "0";
            $stmt = $conn->prepare(
                "INSERT INTO user_info (user_uid, email, password, site_rank, sub_tier, register_date, lock_status) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "issssss",
                $user_set_uid,
                $sub_email,
                $sub_pass,
                $site_rank,
                $sub_tier,
                $reg_date,
                $lock_status
            );

            if ($stmt->execute() == false) {
                echo "<div class='d-flex align-items-center justify-content-center'>";
                echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Sign up issue</h3>";
                echo "Unable to insert your information.<br></b>" .
                    $conn->error;
                echo "Please <a href='mailto:contact@logandag.dev?subject=VehTrac signup issue with db.'>Email Support Here</a></div>";
            } else {
                echo "<div class='alert alert-success text-center' role='alert'> <p>User created, please go to settings to finish your account.</p>";
                echo "Please expect an email with your User UID and email with a welcome letter. Thank you again!</div>";
                //$Body = require ('EmailTemps/registrationEmail.php');
                $Body = "
       <!DOCTYPE html>
       <body>
       <h4>
       Thank you for choosing VehTrac! $sub_email
       We hope you enjoy your adventures with VehTrac and what it can do!
       </h4>
       <p>Your Unique ID is: $user_set_uid</p><br>
       <p>Please go to the <a href='https://vehtrac.logandag.dev/ui/settings'>Settings</a> page to finish setting up your account.</p>
       </body>
       </html>";
                $mail->setFrom(
                    'no-reply@logandag.dev',
                    'VehTrac Administration'
                );
                $mail->addAddress($sub_email);
                $mail->Subject = 'Thank you for using VehTrac!';
                $mail->Body = $Body;
                if (!$mail->send()) {
                    echo "<div class='d-flex align-items-center justify-content-center'>";
                    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Sign up issue</h3>";
                    echo "Email not sent.<br>" . $mail->ErrorInfo . "<br></b>";
                    echo "Please <a href='mailto:contact@logandag.dev?subject=VehTrac signup email not sending.'>Email Support Here</a>";
                    echo "Your account is still made but the welcome email couldn't be sent, please send support an email to resolve this</div>";
                } else {
                    echo "<div class='alert alert-success text-center' role='alert'> <p>Welcome Letter sent!</p></div>";
                }
            }
        } elseif (isset($db_email)) {
            echo "<div class='d-flex align-items-center justify-content-center'>";
            echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Sign up issue</h3>";
            echo "Email already exists</div></div>";
        }
    } //End IF when passwords match
} //End Sign up IF statement
?>