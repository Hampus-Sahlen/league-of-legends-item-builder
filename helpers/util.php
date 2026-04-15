<?php
function debugPrint ($var) {
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

function redirect(string $page) {
    header("Location: " . $page);
    exit;
}

function requireLogin(string $rootPath="./") {
    $rootPath = rtrim($rootPath, "/") . "/";
    if ($rootPath === "/") {
        $rootPath = "./";
    }
    if (!isset($_SESSION["UUID"])) {
        redirect($rootPath . "");
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