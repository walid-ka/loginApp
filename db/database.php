<?php

$host = "localhost"; 
$user = "root";
$password = "root";
$dbname = "registration_test";

$conn = new mysqli($host, $user, $password, $dbname);

 
if ($conn->connect_error) { 
    die("Connection failed :" . $conn->connect_error); 
} 
// else if ($conn) {
//     echo "Connection successful";
// }



?>