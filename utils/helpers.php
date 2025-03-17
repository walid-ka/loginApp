<?php 


function activeLink($pageName) {

    $currentPage = basename($_SERVER["SCRIPT_FILENAME"]);

    return $currentPage === $pageName ? "active" : ""; 
   
}

function pageClass() {

    return basename($_SERVER["SCRIPT_FILENAME"], ".php");
}


function isLoggedIn() {
    return isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] === true;
}

function redirect($location) {
    header("Location: {$location}");
    exit;
}


?>