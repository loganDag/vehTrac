<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require "$DocRoot/includes/header.php";
require "$DocRoot/includes/cookieCheck.php";
$FuelDBID = $_GET["dbid"];
$intent = $_GET["action"];

if ($intent == "delete"){
    echo "<style>
    .edit_site_content{
    display: none;
}
.delete_site_content{
display: block;
}
;
</style>";
}

if ($intent == "edit"){
    echo "<style>
    .delete_site_content{
    display: none;
}
.edit_site_content{
display: block;
}
;
</style>";
}

?>
<!DOCTYPE html>
<html>
<head></head>
<body>
<div class="edit_site_content min-vh-100">
<h1>Editing</h1>
</div>

<div class="delete_site_content min-vh-100">
<h1>Deleting</h1>
</div>
</body>
</html>