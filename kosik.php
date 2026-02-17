<?php
session_start();
include 'db.php';
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Váš košík | TropiOvoce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Nákupní košík</h2>
    <a href="index.php" class="btn btn-secondary mb-3">← Zpět k nákupu</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produkt</th>
                <th>Cena</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $celkem = 0;
            if (isset($_SESSION['kosik']) && !empty($_SESSION['kosik'])) {
                // Převedeme pole ID na řetězec pro SQL dotaz (např. "1, 3, 5")
                $ids = implode(',', $_SESSION['kosik']);
                $sql = "SELECT * FROM produkty WHERE id IN ($ids)";
                $result = $conn->query($sql);

                while($row = $result->fetch_assoc()) {
                    // Počítáme, kolikrát je dané ID v košíku
                    $pocet = array_count_values($_SESSION['kosik'])[$row['id']];
                    $mezisoucet = $row['cena'] * $pocet;
                    $celkem += $mezisoucet;

                    echo "<tr>
                            <td>{$row['nazev']} ({$pocet} ks)</td>
                            <td>{$mezisoucet} Kč</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>Košík je prázdný</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Celkem k úhradě:</th>
                <th><?php echo $celkem; ?> Kč</th>
            </tr>
        </tfoot>
    </table>
    
    <?php if ($celkem > 0): ?>
        <a href="smazat_kosik.php" class="btn btn-danger">Vysypat košík</a>
        <button class="btn btn-primary float-end">Odeslat objednávku</button>
    <?php endif; ?>
</div>
</body>
</html>