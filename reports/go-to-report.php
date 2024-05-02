<?php

if (isset($_POST["phpFile"]))
{
    header("Location: /thesis-mgmt/reports/" . $_POST["phpFile"]);
} else
{
    echo "no phpHile";
}