<?php
$htmlRoot = dirname(__DIR__, 1);

// 2. Include header and database using the calculated root
require $htmlRoot."/cdn-files/bootstrap.html";
include "includes/header.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

session_start();
if (isset($_POST["signin_button"])) {
    $email = $_POST["Log_email"];
    $password = $_POST["Log_pass"];

    $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $LoginDBResult = $stmt->get_result();
    $LoginDBInfo = $LoginDBResult->fetch_assoc();

    // 1. Check if the user exists in the database
    if ($LoginDBInfo) {
        $db_email = $LoginDBInfo["email"];
        $db_password = $LoginDBInfo["password"];
        $db_idnum = $LoginDBInfo["user_uid"];

        // Handle Theme Selection (Simplified logic)
        if (isset($_POST["themeMemoryCookie"]) || isset($_POST["themeMemorySync"])) {
            if (isset($_POST["themeToggle"])) {
                setcookie("SiteTheme", "dark", time() + 86400 * 10, "/");
                $toggle = "Dark Set";
            } else {
                setcookie("SiteTheme", "light", time() + 86400 * 10, "/");
                $toggle = "Light Set";
            }
        }

        // 2. Verify the Password
        if (password_verify($password, $db_password)) {
            // Password is correct
            $LoginError = "3";

            // 3. Generate a Unique Cookie ID 
            // We use a loop to ensure the ID doesn't already exist in the DB
            $is_unique = false;
            while (!$is_unique) {
                $cookie_set_id = rand(1, 10000);
                $check_query = $conn->prepare("SELECT cookie_id FROM user_logins WHERE cookie_id = ?");
                $check_query->bind_param("i", $cookie_set_id);
                $check_query->execute();
                $check_result = $check_query->get_result();
                
                if ($check_result->num_rows == 0) {
                    $is_unique = true;
                }
            }

            $login_ip = $_SERVER["REMOTE_ADDR"];
            $date = date('Y-m-d H:i:s');
            $valid = "1";

            // 4. Insert login record
            $stmt_insert = $conn->prepare("INSERT INTO user_logins (cookie_id, user_uid, login_ip, login_date, is_valid) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("issss", $cookie_set_id, $db_idnum, $login_ip, $date, $valid);

            if ($stmt_insert->execute() === false) {
                echo "<div class='alert alert-danger text-center'><b>MAJOR ERROR:</b> " . $conn->error . "</div>";
            } else {
                // Success! Set sessions and redirect
                $_SESSION["user_id"] = $db_idnum;
                $_SESSION["email"] = $db_email;
                $_SESSION["cookie_id"] = $cookie_set_id;

                echo "<div class='d-flex align-items-center justify-content-center'>";
                echo "<div class='spinner-border' style='width: 3rem; height: 3rem;' role='status'><span class='visually-hidden'>Loading...</span></div>";
                echo "<style>.form_items{display:none !important;}</style>";
                echo "</div>";

                header('refresh:3; url=ui/dash');
            }
        } else {
            // Password verify failed
            $LoginError = "1";
        }
    } else {
        // User (email) not found in database
        $LoginError = "1";
    }
} // End of signin_button check



  /*  if (!isset($db_email) || $password != $db_password) {
        $LoginError = "1";
    } elseif ($email == $db_email && $password == $db_password) {
        
} //End Login GIANT if statement
*/
require ("signup.php");
$conn->close();

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



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VehTrac | Home</title>

    
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