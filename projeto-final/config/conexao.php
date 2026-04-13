<?php
$host = "sqlXXX.infinityfree.com";
$user = "if0_XXXXXXX";
$pass = "SUA_SENHA";
$db   = "if0_XXXXXXX_portal_esportivo";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>