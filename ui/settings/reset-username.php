<?php
// reset-username.php

// Start session (required for user-based actions later)
session_start();

// Placeholder message variable
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newUsername = trim($_POST["username"]);

    if (empty($newUsername)) {
        $message = "Username cannot be empty.";
    } else {
        // Backend logic placeholder
        // Here you will later update username in database
        $message = "Username reset request submitted successfully!";
    }
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
                            <input type="text" name="username" class="form-control" placeholder="Enter new username">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Reset Username
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
