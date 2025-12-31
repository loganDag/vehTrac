<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use function Safe\file_get_contents;

require 'phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/phpmailer/src/SMTP.php';
require 'phpmailer/phpmailer/src/settings.php';

if (isset($_POST['email'])) {
    echo "Attempting to send";
    /* $sub_email = $_POST['sub_email'];
  $TestName = $_POST["name"];
  //$Body = include('EmailTemps/registrationEmail.php');
  $dbServer = "localhost";
  $dbUser = "OutsideNonAdmin";
  $dbPassword = "OutsideNonAdminLPD01!";
  $dbName = "vehtrac";
  $conn = new mysqli("$dbServer", "$dbUser", "$dbPassword", "$dbName");
  if ($conn->connect_error) {
  }
  $emID = '1';
  $sql = "SELECT * FROM email_templates WHERE email_id =('$emID')";
  $result = $conn->query("$sql");
  if ($result == TRUE) {
    while ($Info = $result->fetch_assoc()) {
      $BeginEmailText = $Info["begin_reg_email_temp"];
      $EndEmailText = $Info["end_reg_email_temp"];
    }
  } else {
    $EmailText = 'DB TEXT PULL FAILED!';
  }*/
    //$Body = "$BeginEmailText";
    //$Body .="$TestName";
    // $Body .="$EndEmailText";
    $TestNonDBText = $_POST["text"];
    $sub_email = $_POST['sub_email'];

    $Body = "$TestNonDBText";
    $mail->setFrom('no-reply@nexgenit.digital', 'VehTrac Administration');
    $mail->addAddress($sub_email);
    $mail->Subject = 'Thank you for using VehTrac!';
    $mail->msgHTML($Body);
    $mail->isHTML(true);
    if (!$mail->send()) {
        echo "<div class='d-flex align-items-center justify-content-center'>";
        echo "<div class='alert alert-danger text-center' role='alert'> <h3 class='alert-header'>Sign up issue</h3>";
        echo "Email not sent.<br>" . $mail->ErrorInfo . "<br></b>";
        echo "Please <a href='mailto:support@nexgenit.digital?subject=VehTrac signup email not sending.'>Email Support Here</a>";
        echo "Your account is still made but the welcome email couldn't be sent, please send support and email to resolve this</div>";
    } else {
        echo "<div class='alert alert-success text-center' role='alert'> <p>Welcome Letter sent!</p></div>";
    }
}
if (isset($_POST['date'])) {
    $Date = $_POST['drive_time'];
    $DateConvert = date("M-d-Y H:i:s", strtotime($_POST["drive_time"]));
    echo $DateConvert;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Theme Toggle Example</title>
  <!-- Bootstrap CSS (for demonstration purposes) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Custom styles for dark theme */
    body[data-bs-theme="dark"] {
      background-color: #1a1a1a;
      color: #fff;
    }
  </style>
</head>

<body data-bs-theme="dark">

  <div class="container mt-3">
    <form class='form-control' action='' method='post'>
      <input type='email' class="form form-control" name='sub_email' placeholder="Email">
      <input type='text' class="form form-control" name='text' placeholder="Text">
      <button type="submit" class="btn btn-primary" name="email">Send Email</button>
    </form>

    <!-- Your existing content goes here -->

  </div>
  <label for="exampleDataList" class="form-label">Datalist example</label>
  <input class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
  <datalist id="datalistOptions">
    <option value="San Francisco">
    <option value="New York">
    <option value="Seattle">
    <option value="Los Angeles">
    <option value="Chicago">
  </datalist>
  <script>
    // Function to toggle between dark and light themes
    function toggleTheme() {
      const body = document.body;
      const currentTheme = body.getAttribute('data-bs-theme');

      // Toggle the theme
      const newTheme = currentTheme === 'light' ? 'dark' : 'light';
      body.setAttribute('data-bs-theme', newTheme);
    }

    // Get the theme toggle switch element
    const themeToggleSwitch = document.getElementById('themeToggle');

    // Add an event listener to the theme toggle switch
    themeToggleSwitch.addEventListener('change', toggleTheme);
  </script>

</body>

</html>