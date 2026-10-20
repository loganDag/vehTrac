<?php
$dbServer = "";
$dbUser = "";
$dbPassword = "";
$dbName = "";

$conn = new mysqli("$dbServer", "$dbUser", "$dbPassword", "$dbName");
if ($conn->connect_error)
{
 }
?>