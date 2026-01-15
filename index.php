<?php
$htmlRoot = dirname(__DIR__, 1);

// 2. Include header and database using the calculated root
//require_once $htmlRoot . "/cdn-files/bootstrap.html";
require $htmlRoot."/cdn-files/bootstrap.html";
//echo "$htmlRoot/cdn-files/bootstrap.html'";
include "includes/header.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

session_start();

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
            $_SESSION["user_id"] = $db_idnum;
            $_SESSION["email"] = $db_email;
            $_SESSION["cookie_id"] = $cookie_set_id;
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VehTrac | Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            color: #6c757d;
        }
        .separator::before, .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        .separator:not(:empty)::before { margin-right: .5em; }
        .separator:not(:empty)::after { margin-left: .5em; }
    </style>
</head>

<body data-bs-theme="<?php echo $theme; ?>">

<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center">
    
    <?php if ($Cookie_security): ?>
        <div class='alert alert-danger text-center mb-4' role='alert'>
            <h4 class='alert-heading'>Security Issue</h4>
            <p class="mb-0">Error code: <?php echo htmlspecialchars($Cookie_security); ?>. Please try again.</p>
        </div>
    <?php endif; ?>

    <?php if (isset($login_success)): ?>
        <div class="text-center py-5">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
            <p class="mt-3">Signing you in...</p>
        </div>
    <?php else: ?>

    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
        <div class="text-center mb-4">
            <h1 class="display-5 fw-bold">VehTrac</h1>
            <p class="text-muted">Vehicle tracking software for gig workers.</p>
        </div>

        <div class="card shadow-sm border">
            <div class="card-body p-4 p-sm-5">
                <form action="" method="post" id="login_form">
                    
                    <div class="d-flex justify-content-between mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name='themeToggle' id="themeToggle" <?php echo ($theme == "dark") ? "checked" : ""; ?>>
                            <label class="form-check-label small" for="themeToggle">Dark Mode</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="themeMemoryCookie" id="themeMemoryCookie">
                            <label class="form-check-label small" for="themeMemoryCookie">Save Choice</label>
                        </div>
                    </div>

                    <h4 class="mb-3 fw-normal text-secondary">Sign in</h4>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control <?php echo ($LoginError == '1') ? 'is-invalid' : ''; ?>" 
                               id="Log_email" name="Log_email" placeholder="name@example.com" required 
                               value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <label for="Log_email">Email address</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control <?php echo ($LoginError == '1') ? 'is-invalid' : ''; ?>" 
                               id="Log_password" name="Log_pass" placeholder="Password" required>
                        <label for="Log_password">Password</label>
                        <?php if ($LoginError == "1"): ?>
                            <div class="invalid-feedback">Invalid email or password.</div>
                        <?php endif; ?>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="signin_button">Sign In</button>
                    
                    <div class="separator my-3">Or</div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#sign_up">Create Account</button>
                        <a href="/resetpassword" class="btn btn-link btn-sm text-decoration-none">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="sign_up" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create your account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="signup_email" placeholder="..." required>
                        <label>Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="signup_pass" placeholder="..." required>
                        <label>Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" name="signup_confpass" placeholder="..." required>
                        <label>Confirm Password</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="submit_register" class="btn btn-primary">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme Toggle Script
    document.getElementById('themeToggle').addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        document.body.setAttribute('data-bs-theme', theme);
    });
</script>

<?php require "includes/footer.html"; ?>
</body>
</html>