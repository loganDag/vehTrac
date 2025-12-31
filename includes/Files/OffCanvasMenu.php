<!--Start off canvas-->

<div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="DashboardOffCanvas">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="DashboardOffCanvasTitle">Quick Info</h5>
             <img src="/includes/images/default_avatar.jpg" alt="" width="50" height="50" class="rounded-circle">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="close"></button>
        </div>
    <div class="offcanvas-body">
        <p class="text-muted">Welcome, <?php echo $user_display_name;?></p>
        <p class="text-muted">You have <?php echo $DriveCount;?> Drive(s) entered</p>
        <p class="text-muted">You have <?php echo $CarNumber;?> Car(s) entered</p>
                <ul class="nav flex-column">
                     <li class="nav-item"><a class="nav-link nav-item text-muted Off_menu_link" href='/ui/settings'><i class="bi bi-arrow-right-circle"></i> Account Settings</a></li>
                     <li class="nav-item"><a class="nav-link nav-item text-muted Off_menu_link" href='/ui/settings/index.php#sessions'><i class="bi bi-arrow-right-circle"></i> Current sessions</a></li>
                    <li class="nav-item"><a class="nav-link nav-item text-muted Off_menu_link" href='/ui/drive'><i class="bi bi-arrow-right-circle"></i> Drives</a></li> 
                     <li class= "nav-item"><a class="nav-link nav-item text-muted Off_menu_link" href='/ui/logout'><i class="bi bi-arrow-right-circle"></i> Logout</a></li>
             </ul>
             <p class="text-muted">VehTrac, NexGenit Services LLC 2024 &copy;</p>
             <p class="text-muted">A <a href='https://nexgenit.digital' target="__blank" class='Off_menu_link'>NexGenit Services (NGIS), LLC</a> product
   <!--End offcanvas body div below-->
     </div>
</div>
<!--End off canvas div above-->
<style>
.Off_menu_link:hover{
    text-decoration: underline !important;
    text-decoration-color: blue !important;
    border: 3px dashed blue !important;
}
    </style>