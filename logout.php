<?php

include "./components/header.php";

// Destroying a Session

session_start();

session_unset();

session_destroy();

redirect("index.php");

?>
