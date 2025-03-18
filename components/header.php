<?php
include "./db/database.php";
include ("utils/helpers.php");


session_start(); // the session is started to check if the user is logged in or not. its uses the session id. it should be at the top of the page before any output is sent to the browser to prevent any errors. 

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
</head>

<body>

<?php include "./components/navigation.php"; ?>