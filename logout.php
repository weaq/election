<?php
session_start();
unset($_SESSION["id"]);
unset($_SESSION["user"]);
unset($_SESSION["table"]);
header("Location:index.php");
?>
