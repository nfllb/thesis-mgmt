<?php
session_start();

session_unset();
session_destroy();

header("Location: /thesis-mgmt/login.php");