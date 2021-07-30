<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET["d"])) {
  $_SESSION["display"] = $_GET['d'];
}
?>
