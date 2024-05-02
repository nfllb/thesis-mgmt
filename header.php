<?php
if (!isset($_SESSION))
{
    session_start();
}

include ($_SERVER['DOCUMENT_ROOT'] . "/thesis-mgmt/dbconnect.php");
?>

<header class="p-3 d-flex justify-content-end">
    <div class="d-flex align-items-center">
        <!-- User Icon and Details -->
        <div class="d-flex align-items-center me-3">
            <i class="fas fa-user me-2" style="font-size: 30px;"></i>
            <div>
                <div><?php echo $_SESSION['name']; ?></div>
                <div class="text-muted"><?php echo $_SESSION['role']; ?></div>
            </div>
        </div>
        <!-- Logout button -->
        <a href="/thesis-mgmt/logout.php" id="logout" class="btn btn-sm btn-primary text-decoration-none">Logout</a>
    </div>
</header>