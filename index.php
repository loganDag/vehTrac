<?php
require "../bootstrap.html";
include "includes/header.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
echo 'test';
$Cookie_security = $_GET["error"];
if ($Cookie_security == "1") {
    echo "<div class='d-flex align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Security Issue</h3>";
    echo "There was an issue with your login check, please make sure cookies are enabled properly.</div></div>";
}
if ($Cookie_security == "2") {
    echo "<div class='d-flex align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Security Issue</h3>";
    echo "The user id cookie was not set, please try again.</div></div>";
}
if ($Cookie_security == "3") {
    echo "<div class='d-flex align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Security Issue</h3>";
    echo "The Security was not set, please try again.</div></div>";
}
if ($Cookie_security == "4") {
    echo "<div class='d-flex align-items-center justify-content-center'>";
    echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Security Issue</h3>";
    echo "Unauthorized session</div></div>";
}
if (isset($_POST["signin_button"])) {
    $email = $_POST["Log_email"];
    $password = $_POST["Log_pass"];

    $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $LoginDBResult = $stmt->get_result();
    $LoginDBInfo = $LoginDBResult->fetch_assoc();

    if ($LoginDBInfo) {
        $db_email = $LoginDBInfo["email"];
        $db_password = $LoginDBInfo["password"];
        $db_idnum = $LoginDBInfo["user_uid"];
    }

    if (isset($_POST["themeMemoryCookie"])) {
        if (isset($_POST["themeToggle"])) {
            setcookie("SiteTheme", "dark", time() + 86400 * 10, "/");
            $toggle = "Dark Set";
        }
        if (!isset($_POST["themeToggle"])) {
            setcookie("SiteTheme", "light", time() + 86400 * 10, "/");
            $toggle = "Light Set";
        }
    }

    if (isset($_POST["themeMemorySync"])) {
        if (isset($_POST["themeToggle"])) {
            setcookie("SiteTheme", "dark", time() + 86400 * 10, "/");
            $toggle = "Dark Set";
        }
        if (!isset($_POST["themeToggle"])) {
            setcookie("SiteTheme", "light", time() + 86400 * 10, "/");
            $toggle = "Light Set";
        }
    }

    if (!isset($db_email) || $password != $db_password) {
        $LoginError = "1";
    } elseif ($email == $db_email && $password == $db_password) {
        $LoginError = "3";
        /*
       A cookie is set with a unique ID, that is tied to the user's id. 
       A DB query is ran to see if this cookie is a valid cookie login.
        We do this by running the cookie ID aganist the User UID in the "user_logins" DB. 
      If not, then we logout the user and then make them login again, if it is. Nothing happens.
      */
        //Start Cookie generation and db insert for cookie
        $cookie_set_id = rand(1, 10000);
        $ran_num_query = "SELECT * FROM user_logins WHERE cookie_id=('$cookie_set_id')";
        $ran_num_result_query = $conn->query($ran_num_query);
        while ($ran_num_result = mysqli_fetch_array($ran_num_result_query)) {
            $cookie_uid_result = $ran_num_result["cookie_id"];
        }
        while ($cookie_uid_result != 0) {
            $cookie_set_id = rand(1, 10000);
            $ran_num_result = mysqli_fetch_array($ran_num_result_query);
        }
        $login_ip = $_SERVER["REMOTE_ADDR"];
        $date = date('Y-m-d H:i:s');
        $valid = "1";
        $CookieUIDInsert =
            "INSERT INTO user_logins (cookie_id, user_uid, login_ip, login_date, is_valid) VALUES ('" .
            $cookie_set_id .
            "', '" .
            $db_idnum .
            "', '" .
            $login_ip .
            "', '" .
            $date .
            "', '" .
            $valid .
            "')";
        if ($conn->query($CookieUIDInsert) == false) {
            echo "<div class='alert alert-danger text-center' role='alert'><b>MAJOR ERROR: ALERT WEBMASTER TO IT. ERROR:" .
                $CookieUIDInsert .
                "<br></b></div>" .
                $conn->error;
        } else {
            echo "<div class='d-flex align-items-center justify-content-center'>";
            //echo "<div class='alert alert-success text-center' role='alert'> <p>Signing you in, please allow 3 seconds to be redirected.</p></div></div>";
            //ALL COOKIES GOOD ONLY FOR 10 DAYS
            echo "      
                <div class='spinner-border' style='width: 3rem; height: 3rem;' role='status'>
  <span class='visually-hidden''>Loading...</span>
</div>";
            echo "<style>.form_items{display:none !important;}</style>";
            echo "</div>";
            setcookie("user_id", "$db_idnum", time() + 86400 * 10, "/");
            setcookie("cookie_id", "$cookie_set_id", time() + 86400 * 10, "/");
            header('refresh:3; url=ui/dash');
        }
    }
    //End Cookie Generation
} //End Login GIANT if statement

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
                echo "Please <a href='mailto:support@nexgenit.digital?subject=VehTrac signup issue with db.'>Email Support Here</a></div>";
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
       <p>Please go to the <a href='https://vehtrac.nexgenit.digital/settings.php'>Settings</a> page to finish setting up your account.</p>
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
                    echo "Please <a href='mailto:support@nexgenit.digital?subject=VehTrac signup email not sending.'>Email Support Here</a>";
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

$conn->close();
?>

<html>
    <head>

        <title>VehTrac | Home</title>
</head>
<body data-bs-theme="<?php echo $theme; ?>">
  <div class="container h-25 index_pos_container"></div>
  <!--DIV BELOW WRAPS EVERYTHING UNTIL FOOTER TO ENSURE BEING CENTER OF SCREEN-->
<div class="h-75 d-flex align-items-center justify-content-center">
  <div class="container col-md-4 form_items">
        <div class="jumbotron text-center">
            <h1>VehTrac Login </h1>
            <small class="form-text text-muted">Vehicle tracking software for gig workers.</small><br>
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
                      <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="themeMemoryCookie" id="themeMemoryCookie">
                              <label class="form-check-label" for="themeMemoryCookie">Remember Theme Choice for current session?</label>
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
  <!--Modal-->

  <!-- Modal -->
  <div class="modal fade" id="sign_up" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="SignUpLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">VehTrac | Sign up</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form action="" method="post">
            <div class='form-floating'>
                <input type='email' class='form-control' id='signup_email' name='signup_email' placeholder='Enter email...' required>
                <label for='signup_email'>Enter email:</label>
            </div>
                      <br>
            <div class='form-floating'>
                <input type='password' class='form-control' id='signup_pass' name='signup_pass' placeholder='Enter password...' required>
                <label for='signup_pass'>Enter your password:</label>
            </div>
            <br>
            <div class='form-floating'>
                <input type='password' class='form-control' id='signup_confpass' name='signup_confpass' placeholder='Please confirm password..' required>
                <label for='signup_confpass'>Confirm your password:</label>
            </div>
            <!--end modal body-->
          </div>    
        <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="submit_register">Sign Up</button>
      </form>

      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
  </div>
    <!--end modal-->
</div>
<!--END ENTIRE BODY DIV WITH DIV TAG ABOVE-->
<div class="container h-25"></div>
<?php require "includes/footer.html"; ?>
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