<?php
// Inicializujeme session, abychom k ní měli přístup
session_start();

// Smažeme všechny proměnné v session (včetně přihlášení uživatele i obsahu košíku, pokud chceš)
// Pokud chceš košík zachovat i po odhlášení, odstraň pouze $_SESSION['uzivatel_id'] a $_SESSION['uzivatel_jmeno']
session_unset();

// Zničíme celou session
session_destroy();

// Přesměrujeme uživatele zpět na hlavní stránku
header("Location: index.php");
exit();
?>