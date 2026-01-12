<?php
$DocRoot = $_SERVER["DOCUMENT_ROOT"];
require("$DocRoot/../bootstrap.html");
require("$DocRoot/includes/header.php");
require("$DocRoot/includes/cookieCheck.php");
require("$DocRoot/includes/menu.html");
?>
<html>
    <body data-bs-theme="<?php echo $theme;?>">
    <div class="main_site_content">
    <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="themeMemorySync" id="themeMemorySync">
                              <label class="form-check-label" for="themeMemorySync">Make theme your account theme?</label>
                              <p class="text-muted">Syncs across devices and you can change theme in settings</p>
                     </div>

        <a name="sessions"></a>
        <div class="sessions">
           <p class="fs-4 text-muted">
                <p>
                    Display Session
            </p>
            </p>
        </div>

</body>
<?php require("$DocRoot/includes/footer.html");?>ß
</html>