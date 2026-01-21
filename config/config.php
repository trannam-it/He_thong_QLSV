<?php
// $servername ="localhost";
// $username ="root";
// $password ="";
// $db_name ="student_management";

// $conn = new mysqli($servername, $username, $password, $db_name);

// if (!$conn){
// 	die("connection error");
// }

$servername ="localhost";   // hoặc 127.0.0.1
$username   = "root";
$password   = "";
$db_name    = "database_qlsv";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $db_name, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>



