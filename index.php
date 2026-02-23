<?php
// 1. Initialize session and dependencies immediately
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$DocRoot = $_SERVER["DOCUMENT_ROOT"];
include "includes/header.php";

$LoginError = ""; // Initialize variable

if (isset($_POST["signin_button"])) {
    $email = $_POST["Log_email"];
    $password = $_POST["Log_pass"];

    // Use prepared statement to find user
    $stmt = $conn->prepare("SELECT * FROM user_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $LoginDBResult = $stmt->get_result();
    $LoginDBInfo = $LoginDBResult->fetch_assoc();

    if ($LoginDBInfo && password_verify($password, $LoginDBInfo["password"])) {
        $db_email = $LoginDBInfo["email"];
        $db_idnum = $LoginDBInfo["user_uid"];

        // Handle Theme Selection
        if (isset($_POST["themeMemoryCookie"])) {
            $themeVal = isset($_POST["themeToggle"]) ? "dark" : "light";
            setcookie("SiteTheme", $themeVal, time() + (86400 * 10), "/");
        }

        // --- FIX FOR "OUT OF SYNC" ERROR ---
        $is_unique = false;
        $cookie_set_id = 0;
        
        // Prepare the check statement ONCE outside the loop
        $check_query = $conn->prepare("SELECT cookie_id FROM user_logins WHERE cookie_id = ?");

        while (!$is_unique) {
            $cookie_set_id = rand(1, 10000); // Consider bin2hex(random_bytes(8)) for better security
            $check_query->bind_param("i", $cookie_set_id);
            $check_query->execute();
            $check_res = $check_query->get_result();
            
            if ($check_res->num_rows == 0) {
                $is_unique = true;
            }
            $check_res->free(); 
        }
        $check_query->close(); // Close it after the loop finishes

        // Insert login record
        $login_ip = $_SERVER["REMOTE_ADDR"];
        $date = date('Y-m-d H:i:s');
        $valid = "1";

        $stmt_insert = $conn->prepare("INSERT INTO user_logins (cookie_id, user_uid, login_ip, login_date, is_valid) VALUES (?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("issss", $cookie_set_id, $db_idnum, $login_ip, $date, $valid);

        if ($stmt_insert->execute()) {
            $_SESSION["user_id"] = $db_idnum;
            $_SESSION["email"] = $db_email;
            $_SESSION["cookie_id"] = $cookie_set_id;
            
            // Set success flag to trigger the UI spinner
            $login_success = true;
            header('refresh:2; url=ui/dash');
        } else {
            die("Database Error: " . $conn->error);
        }
        $stmt_insert->close();
    } else {
        $LoginError = "1"; // Invalid credentials
    }
    $stmt->close();
}

require ("signup.php");
$conn->close();

// Capture security errors from GET
$Cookie_security = $_GET["error"] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VehTrac | Home</title>
    <style>
        body { font-family: 'Roboto', sans-serif; transition: background 0.3s ease; }
        .separator { display: flex; align-items: center; text-align: center; color: #6c757d; }
        .separator::before, .separator::after { content: ''; flex: 1; border-bottom: 1px solid #dee2e6; }
        .separator:not(:empty)::before { margin-right: .5em; }
        .separator:not(:empty)::after { margin-left: .5em; }
    </style>
</head>

<body data-bs-theme="<?php echo $_COOKIE['SiteTheme'] ?? 'light'; ?>">

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
            <h4 class="mt-3">Signing you in...</h4>
        </div>
    <?php else: ?>

    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
        <div class="text-center mb-4">
            <h1 class="display-5 fw-bold text-primary">VehTrac</h1>
            <p class="text-muted">Vehicle tracking for gig workers.</p>
        </div>

        <div class="card shadow-lg border-0">
            <div class="card-body p-4 p-sm-5">
                <form action="" method="post">
                    
                    <div class="d-flex justify-content-between mb-4 bg-light p-2 rounded">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name='themeToggle' id="themeToggle" <?php echo (($_COOKIE['SiteTheme'] ?? '') == "dark") ? "checked" : ""; ?>>
                            <label class="form-check-label small" for="themeToggle">Dark Mode</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="themeMemoryCookie" id="themeMemoryCookie" checked>
                            <label class="form-check-label small" for="themeMemoryCookie">Save Choice</label>
                        </div>
                    </div>

                    <h4 class="mb-3 fw-bold">Sign in</h4>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('themeToggle').addEventListener('change', function() {
        const theme = this.checked ? 'dark' : 'light';
        document.body.setAttribute('data-bs-theme', theme);
    });
</script>

<?php require "includes/footer.html"; ?>
</body>
</html>