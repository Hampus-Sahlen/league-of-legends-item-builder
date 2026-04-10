<?php 
if (PHP_VERSION_ID < 80100) { // php 8.1+ is required for page to run properly
    exit("PHP 8.1+ required.");
}

// disable error reporting in prod
// error_reporting(0); 

session_start(); // use session for login status

require_once "util.php"; // contains utility functions

require_once "database.php"; // contains database wrapper class

require_once "database_credentials.php"; // contains db credentials, included in gitignore
$dbObject = new DatabaseConnection($host, $user, $password, $database);
