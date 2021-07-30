<?php
$servername = "localhost";
$username = "weaq";
$password = "Db@12341234";
/*
$username = "root";
$password = "11223344";
*/

$dbname = "elections";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Change character set to utf8
mysqli_set_charset($conn,"utf8");
?>
