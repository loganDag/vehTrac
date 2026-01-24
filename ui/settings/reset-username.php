<?php
/// This is the "Settings" page username reset feture.
/// This will use the email from the session_start in order to change with the database
/// The "Forgot Username" page will require the email in order to obtain, NOT reset, the username.

// 1. Get the path to the 'vehtrac' folder (two levels up from /ui/settings/)
$vehtracRoot = dirname(__DIR__, 2);

// 2. Include header and database using the calculated root
require_once $vehtracRoot . "/includes/header.php";
// 3. Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_email = $_SESSION["email"];


// Placeholder message variable
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Initial Trimming
    $rawUsername = trim($_POST["new-username"]);
    $accPassword = trim($_POST["acc-password"]);
    $confPassword = trim($_POST["conf-password"]);

    // 2. Basic Empty Checks
    if (empty($rawUsername)) {
        $message = "Username cannot be empty.";
    } elseif (empty($accPassword)) {
        $message = "Password field cannot be blank.";
    } elseif ($confPassword !== $accPassword) {
        $message = "Passwords DO NOT match, please try again.";
    } else {
        // 3. Apply Alphanumeric Filter
        // This removes everything except a-z, A-Z, and 0-9
        $cleanUsername = preg_replace('/[^a-zA-Z0-9]/', '', $rawUsername);

        // 4. Enforce Character Limit
        // We check the CLEANED version to ensure the final result isn't too long
        $limit = 12;
        if (mb_strlen($cleanUsername) > $limit) {
            // Option A: Show an error (Better UX)
            $message = "Username must be $limit characters or less.";
        } else {
            // 5. Database Check
            $stmt = $conn->prepare("SELECT username FROM user_info WHERE username = ?");
            $stmt->bind_param("s", $cleanUsername);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                $stmt = $conn->prepare("UPDATE user_info SET username = ? WHERE email = ?");
                $stmt -> bind_param("ss", $cleanUsername, $user_email);
                $ConnResult = $stmt->execute();
            if ($ConnResult){
                // Logic to update username would go here
                $message = "Username reset successfully to: " . htmlspecialchars($cleanUsername);
            }else{
                $message = "Username was not able to be changed.";
            }
            } else {
                $message = "Username is taken, please choose another one.";
            }
        }
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Username</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Reset Username</h5>
                </div>

                <div class="card-body">
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-info">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">New Username</label>
                            <input type="text" name="new-username" class="form-control" placeholder="Enter new username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Your Password</label>
                            <input type="password" name="acc-password" class="form-control" placeholder="Enter your current password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"> Confirm Password</label>
                            <input type="password" name="conf-password" class="form-control" placeholder="Confirm your password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            Reset Username
                        </button>
                    </form>
                    <br>
                    <h5 class="text-muted">Requirements:</h5>
                    <br>
                    <p class="text-muted">Must be text and numbers only and a max of 12 characters. All other characters, including spaces, will be removed after submission. If you do uses spaces or special characters, your username will be the characters not including the space or special characters, leaving you with a shorter than intended username.</p>
                    <br>
                    <p class="text-muted">Example: This is a sample text. The new username is: <i>Thisisasa</i>. The A in sample is the 12th character however, the new username is only 9 digits long. </p>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>