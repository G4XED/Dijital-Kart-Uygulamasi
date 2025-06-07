<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "dijitalkart";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Veritabanı bağlantı hatası: " . mysqli_connect_error());
}
?>