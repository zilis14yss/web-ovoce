<?php
session_start();

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Pokud košík v session ještě neexistuje, vytvoříme ho jako prázdné pole
    if (!isset($_SESSION['kosik'])) {
        $_SESSION['kosik'] = array();
    }

    // Přidáme ID produktu do pole košíku
    $_SESSION['kosik'][] = $id;
}

// Vrátíme uživatele zpět na hlavní stránku
header("Location: index.php?vlozeno=1");
exit();
?>