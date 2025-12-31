<!DOCTYPE html>
<html lang="en">
<?php
require("../../bootstrap.html");
$theme = $_COOKIE["SiteTheme"];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>VehTrac | Roadmap and Software Information.</title>

</head>

<body data-bs-theme="<?php echo $theme; ?>">
    <div class="container mt-5">
        <div class='alert alert-danger alert-dismissible text-center' role='alert'>
            <h3 class='alert-header fs-4'>Secuirty Alert</h3>
        </div>
        <div class='alert alert-danger alert-dismissible text-center' role='alert'>
            <h3 class='alert-body fs-6'>Please check <a href="#backend_info">Here</a> for information about the issue.</h3>
        </div>
        <h1 class='text-center'>VehTrac Roadmap and Software Information.</h3>
            <p class="text-centered text-muted fs-6">Welcome to the SoftCenter!</p>
            <p class="text-centered text-muted fs-6">This is where you can find news, new software updates, when they will be relased, the meaning of software codes, and much more! </p>
            <p class="text-centered text-muted fs-6">This is also where the projects that we are working on will have a home for a roadmap and any further news in their relation along with current software versions.</p>
            <p class="text-centered text-muted fs-6">Below you can also find a place to specifcally sign up for project updates for specific projects as well.</p>
            <p class="text-centered text-muted fs-6">Look around and make yourself familiar as we add new things when we are ready to and put updates here along with any issues that we are facing.</p>

            <br><br>
            <h3 class='jumbotron text-center'>Dashboard Software</h3>
            <p class="text-centered text-muted fs-6">Below is the progress for the dashboard of the website.</p>
            <p class="text-centered text-muted fs-6">The dashboard provides quick access information along with adding a new vehicle and containing the quick info side bar.</p>
            <p class="text-centered text-muted fs-6">We are working on adding security measures along with more quick info access to add in the background. Thank you for your patience.</p>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" id="DashboardProgressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <br><br>

            <h3 class='jumbotron text-center'>Settings Software</h3>
            <p class="text-centered text-muted fs-6">Below is the progress for the Settings page for the website</p>
            <p class="text-centered text-muted fs-6">The settings page is where you can view your email, reset your password, view current sessions, and so forth.</p>
            <p class="text-centered text-muted fs-6">We are currently adding such features and will keep updates posted here and when they are expected to be completed.</p>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" id="SettingsProgressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>
            <p class="text-centered text-muted fs-6"><i>Latest update: adding a new feature in order to view currently logged in sessions and logout unauthroized sessions.</i></p>
            <br><br>

            <a name="backend_info"></a>
            <div class='alert alert-danger alert-dismissible text-center' role='alert'>
                <h3 class='alert-header'>Secuirty Alert</h3>
            </div>
            <div class='alert alert-danger alert-dismissible text-center fs-4' role='alert'>
                <h3 class='alert-body fs-6'>Currently investigating an issue with security vulnerabilities. Please Check back later and may God bless you, Amen!</h3>
            </div>
            <h3 class='jumbotron text-center'>Back end Software</h3>
            <p class="text-centered text-muted fs-6">This section is used to display the progress of the backend of the website.</p>
            <p class="text-centered text-muted fs-6">This includes, but not limited to, the user expereince system, vehicle information system, drive information system, fuel tracking, etc..</p>
            <p class="text-centered text-muted fs-6">Please see the sub-progress bars for any issues that are being resolved right now and check back for updates when needed.</p>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" style="width: 0%;" id="BackEndProgressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>

            <br><br>
            <h3 class='jumbotron text-center'>Front end Software</h3>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;" id="FrontEndProgressBar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
            </div>

    </div>

    <script>
        window.onload = function() {
            const progressBars = [{
                    id: 'DashboardProgressBar',
                    targetPercentage: 5
                },
                {
                    id: 'SettingsProgressBar',
                    targetPercentage: 1
                },
                {
                    id: 'BackEndProgressBar',
                    targetPercentage: 40
                },
                {
                    id: 'FrontEndProgressBar',
                    targetPercentage: 1
                }
            ];

            progressBars.forEach(bar => {
                const progressBar = document.getElementById(bar.id);
                let width = 0;

                const interval = setInterval(() => {
                    if (width >= bar.targetPercentage) {
                        clearInterval(interval);
                    } else {
                        width++;
                        progressBar.style.width = width + '%';
                        progressBar.setAttribute('aria-valuenow', width);
                        progressBar.innerHTML = width + '%';
                    }
                }, 50);
            });
        };
    </script>
</body>

</html>

</body>
<style>
    .progress {
        height: 30px;
        transition: width 0.5s ease;
    }

    .progress-bar {
        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
    }
</style>
<div class="container h-25"></div>
<?php require("../includes/footer.html"); ?>

</html>