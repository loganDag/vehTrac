<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"79e61bce852e487ea0a4d350438d4a78","r":1,"server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>

    <title>VehTrac | Legal homepage</title>
</head>

<body data-bs-theme="<?php echo $_COOKIE['SiteTheme'] ?? 'light'; ?>">
    <?php require("$DocRoot/includes/menu.html"); ?>

    <div class="site_content" style="height: 100vh;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1>Legal</h1>
                    <p>Welcome to the legal section of our website. Here you can find important information regarding our terms of service, privacy policy, and other legal matters.</p>

                    <div class="card text-center">
                        <div class="card-header">
                            Terms of Service
                        </div>
                        <div class="card-body">
                            <p class="card-text">View our current terms of service and how they apply to you.</p>
                            <a href="/ui/legal/tos/" class="btn btn-primary">View Terms of Service</a>
                        </div>
                        <div class="card-footer text-body-secondary">
                            Updated: Work in progress
                        </div>
                    </div>
                    <br />
                    <br />
                    
                        <div class="card text-center">
                        <div class="card-header">
                            Privacy Policy
                        </div>
                        <div class="card-body">
                            <p class="card-text">View our current privacy policy and how it applies to you.</p>
                            <a href="/ui/legal/privacy/" class="btn btn-primary">View Privacy Policy</a>
                        </div>
                        <div class="card-footer text-body-secondary">
                            Updated: Work in progress
                        </div>
                    </div>
                    <br />
                    <br />
                     <div class="card text-center">
                        <div class="card-header">
                            Email us!
                        </div>
                        <div class="card-body">
                            <p class="card-text">Have questions? Contact us at <a href="mailto:legal@logandag.dev">legal@logandag.dev</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require("$DocRoot/includes/footer.html"); ?>
</body>

</html>
