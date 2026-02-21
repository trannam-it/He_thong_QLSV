<?php
$servername ="localhost";
$username ="root";
$password ="";
$db_name ="test_qlsv";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $db_name,$port);

if (!$conn){
	die("connection error");
}

// Define base URL for assets
if (!defined('BASE_URL')) {
    define('BASE_URL', '/abc/web_QLSV/public/');
}




// $servername ="localhost";   // hoặc 127.0.0.1
// $username   = "root";
// $password   = "";
// $db_name    = "database_qlsv";
// $port       = 3307;
// define('BASE_URL', '/web_QLSV');

// $conn = new mysqli($servername, $username, $password, $db_name, $port);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// ?>



