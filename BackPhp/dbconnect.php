<?php
$dbServer = "localhost";
$dbUser = "OutsideNonAdmin";
$dbPassword = "OutsideNonAdminLPD01!";
$dbName = "vehtrac";
$conn = new mysqli("$dbServer", "$dbUser", "$dbPassword", "$dbName");
if ($conn->connect_error)
{
 }
 ?>