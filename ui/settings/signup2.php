<?php
date_default_timezone_set('America/New_York');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DocRoot = $_SERVER["DOCUMENT_ROOT"];
$vehtracRoot = dirname(__DIR__, 2);

require_once $vehtracRoot . "/includes/header.php";

$CLIP = $_SERVER["REMOTE_ADDR"] ?? null;
$IP2 = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? null;
$ErrorMessage = [];

$user_uid = filter_input(INPUT_GET, "user_uid", FILTER_VALIDATE_INT);
$showForm = true;

if ($user_uid === false || $user_uid === null) {
    $showForm = false;
    $ErrorMessage[] = "This registration link is missing a valid user ID, so the form has been hidden to keep the page layout intact.";
}

if (isset($_POST["complete_registration"])) {
    $registration_code = $_POST["registration_code"] ?? null;

    if (!$registration_code) {
        $ErrorMessage[] = "Registration code is required.";
    } elseif (!$showForm) {
        $ErrorMessage[] = "Unable to complete registration because the user ID is invalid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM user_reg_codes WHERE user_uid = ? AND reg_code = ?");
        $stmt->bind_param("is", $user_uid, $registration_code);

        if (!$stmt->execute()) {
            $ErrorMessage[] = "Unable to validate the registration code.";
        } else {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $FName = "User";
                $LName = "User";
                $lock_status = "0";
                $stmt = $conn->prepare("UPDATE user_info SET lock_status = ?, FName =?, LName =? WHERE user_uid = ?");
                $stmt->bind_param("sssi", $lock_status, $FName, $LName, $user_uid);

                if (!$stmt->execute()) {
                    $ErrorMessage[] = "Unable to update your registration status.<br>" . $conn->error;
                } else {
                    $showForm = false;
                    $registrationComplete = true;
                }
            } else {
                $ErrorMessage[] = "Invalid registration code. Please check the code and try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>VehTrac registration completion</title>
</head>
<body>
    <?php if (!empty($ErrorMessage)): ?>
        <div class="alert alert-info error-container w-100 align-items-center text-center">
            <?php echo implode("<br>", $ErrorMessage); ?>
        </div>
    <?php endif; ?>

    <div class="h-75 d-flex align-items-center justify-content-center">
        <div class="container col-md-4 form_items">
            <?php if (isset($registrationComplete) && $registrationComplete): ?>
                <div class="alert alert-success text-center" role="alert">
                    <h3 class="alert-header">Registration completed successfully</h3>
                    Your registration has been completed successfully. You can now log in to your account
                    <a href="https://vehtrac.logandag.dev">here.</a>
                </div>
            <?php elseif ($showForm): ?>
                <div class="jumbotron text-center">
                    <h1>VehTrac registration completion</h1>
                </div>

                <form action="" method="post">
                    <div class="form-floating mb-3">
                        <input
                            type="text"
                            class="form-control"
                            id="registration_code"
                            name="registration_code"
                            placeholder="Registration code"
                            required
                            value=""
                        >
                        <label for="registration_code">Registration code</label>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary mb-3" type="submit" name="complete_registration">
                        Complete Registration
                    </button>

                    <a href="https://vehtrac.logandag.dev" class="btn btn-link btn-md text-decoration-none">Login</a>
                </form>
            <?php else: ?>
                <div class="alert alert-warning text-center" role="alert">
                    <h3 class="alert-header">Registration link invalid</h3>
                    This registration link is incomplete or missing a valid user ID. Please check the link and try again. If you continue to have issues, please contact support.
                    <a href="https://support.vehtrac.logandag.dev" target="_blank">Support</a> <br> <a href="https://vehtrac.logandag.dev" target="_blank">Homepage</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php require "$DocRoot/includes/footer.html"; ?>
</body>
</html>