<?php
session_start();
if (isset($_GET['id']) && isset($_GET['pocet'])) {
    $id = (int)$_GET['id'];
    $pocet = (int)$_GET['pocet'];

    if (!isset($_SESSION['kosik'])) {
        $_SESSION['kosik'] = array();
    }

    // Přidáme ID tolikrát, kolik uživatel zadal
    for ($i = 0; $i < $pocet; $i++) {
        $_SESSION['kosik'][] = $id;
    }
}
header("Location: index.php");
exit();