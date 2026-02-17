<?php
$host = "localhost";
$user = "root";
$pass = ""; // V XAMPPu na Macu/Win bývá heslo prázdné
$db   = "tropicke_ovoce";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Chyba připojení: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>