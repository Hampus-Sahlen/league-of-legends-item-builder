<?php
// documentation: https://docs.google.com/document/d/1M4vFmgnGQlnSS8qmiZ4SVyzixE4QK-1tPX_lmDFmVyA/edit?tab=t.s4ncqfvorcxr
function debugPrint ($var) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function redirect(string $page) {
    header("Location: " . $page);
    exit;
}

function requireLogin(string $loginPage="./") {
    $loginPage = rtrim($loginPage, "/") . "/";
    if ($loginPage === "/") {
        $loginPage = "./";
    }
    if (!isset($_SESSION["UUID"])) {
        redirect($loginPage . "");
    }
}

function checkPermission($accessLevel, $loginPage) { // redirects to loginpage if users accesslevel is not the same as $accessLevel
    if (empty($_SESSION["accessLevel"])){
        redirect($loginPage . "");
    }
    if ($_SESSION["accessLevel"] != $accessLevel) {
        redirect($loginPage . "");
    }
}

function es(string $string) { // escapeString
    return htmlspecialchars($string);
}

function truncateStr(string $str, int $length) {
    $newStr = substr($str, 0, $length);
    if (strlen($newStr) != strlen($str)) {
        $newStr .= "...";
    }
    return $newStr;
}