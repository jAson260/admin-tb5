<?php
$conn = mysqli_connect("localhost", "root", "", "big_five_db");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
?>
<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "big_five_db";

// Using mysqli (Procedural)
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>