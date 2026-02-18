<?php
session_start();
include 'db.php';

// Ochrana: Objednat může jen přihlášený uživatel s neprázdným košíkem
if (!isset($_SESSION['uzivatel_id']) || empty($_SESSION['kosik'])) {
    header("Location: kosik.php");
    exit();
}

$uzivatel_id = $_SESSION['uzivatel_id'];
$kosik = $_SESSION['kosik'];

// 1. Nejprve spočítáme celkovou částku (včetně slevy a dopravy)
$celkem = 0;
$counts = array_count_values($kosik);
$ids = implode(',', array_keys($counts));
$sql_produkty = "SELECT * FROM produkty WHERE id IN ($ids)";
$result_produkty = $conn->query($sql_produkty);

while($row = $result_produkty->fetch_assoc()) {
    $celkem += $row['cena'] * $counts[$row['id']];
}

// Aplikace slevy 15% a dopravy (stejná logika jako v kosik.php)
$celkem_po_sleve = $celkem * 0.85; 
$doprava = ($celkem_po_sleve >= 800) ? 0 : 69;
$finalni_cena = $celkem_po_sleve + $doprava;

// 2. Vložíme hlavní záznam o objednávce
$sql_objednavka = "INSERT INTO objednavky (uzivatel_id, celkova_castka) VALUES ('$uzivatel_id', '$finalni_cena')";

if ($conn->query($sql_objednavka) === TRUE) {
    $objednavka_id = $conn->insert_id; // Získáme ID právě vytvořené objednávky

    // 3. Vložíme jednotlivé položky košíku
    $result_produkty->data_seek(0); // Resetujeme výsledek dotazu pro další použití
    while($row = $result_produkty->fetch_assoc()) {
        $p_id = $row['id'];
        $p_pocet = $counts[$p_id];
        $p_cena = $row['cena'];
        
        $sql_polozka = "INSERT INTO polozky_objednavky (objednavka_id, produkt_id, pocet, cena_za_kus) 
                        VALUES ('$objednavka_id', '$p_id', '$p_pocet', '$p_cena')";
        $conn->query($sql_polozka);
    }

    // 4. Vyčistíme košík a přesměrujeme na potvrzení
    unset($_SESSION['kosik']);
    header("Location: index.php?objednavka_ok=1");
} else {
    echo "Chyba při odesílání: " . $conn->error;
}
?>