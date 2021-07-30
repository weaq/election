<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if($_SESSION["user"]) {
} else {
  echo '<h1>Please click here to <a href="login.php" tite="Login"> login first .</h1>';
  header("Location:login.php");
  exit();
}
?>
