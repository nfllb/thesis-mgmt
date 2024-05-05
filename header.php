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
        <div class="d-flex align-items-center me-2">
            <i class="fas fa-user me-1" style="font-size: 30px; color: #333;"></i>
            <div>
                <div style="color: #333;">
                    <?php echo $_SESSION['name']; ?>
                </div>
                <span class="text-muted"><?php echo $_SESSION['role']; ?></span>
            </div>
        </div>
        <!-- Logout button -->
        <a href="/thesis-mgmt/logout.php" id="logout" class="btn btn-sm btn-danger text-decoration-none logout-btn"
            style="padding: 5px 10px; font-size: 14px;">Logout</a>
    </div>
</header>