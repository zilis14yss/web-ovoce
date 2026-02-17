<?php
session_start();
include 'db.php';

// Pomocná funkce pro smazání jedné položky (volitelné)
if (isset($_GET['odebrat'])) {
    $id_k_odebrani = $_GET['odebrat'];
    if (($key = array_search($id_k_odebrani, $_SESSION['kosik'])) !== false) {
        unset($_SESSION['kosik'][$key]);
        // Reindexujeme pole, aby v něm nebyly díry
        $_SESSION['kosik'] = array_values($_SESSION['kosik']);
    }
    header("Location: kosik.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Váš košík | TropiOvoce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
    <div class="container">
        <a class="navbar-brand" href="index.php">🍍 TropiOvoce</a>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm p-4">
        <h2 class="mb-4">🛒 Váš nákupní košík</h2>

        <?php if (isset($_SESSION['kosik']) && !empty($_SESSION['kosik'])): ?>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Produkt</th>
                        <th>Cena za ks</th>
                        <th>Počet</th>
                        <th>Mezisoučet</th>
                        <th>Akce</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $celkem = 0;
                    // Spočítáme výskyty jednotlivých ID v košíku
                    $counts = array_count_values($_SESSION['kosik']);
                    $ids = implode(',', array_keys($counts));

                    $sql = "SELECT * FROM produkty WHERE id IN ($ids)";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $pocet = $counts[$id];
                        $mezisoucet = $row['cena'] * $pocet;
                        $celkem += $mezisoucet;
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo $row['nazev']; ?></strong><br>
                                <small class="text-muted"><?php echo $row['zeme_puvodu']; ?></small>
                            </td>
                            <td><?php echo $row['cena']; ?> Kč</td>
                            <td>
                                <span class="badge bg-secondary fs-6"><?php echo $pocet; ?> ks</span>
                            </td>
                            <td class="fw-bold"><?php echo $mezisoucet; ?> Kč</td>
                            <td>
                                <a href="kosik.php?odebrat=<?php echo $id; ?>" class="btn btn-sm btn-outline-danger">Odebrat 1 ks</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot class="table-group-divider">
                    <tr>
                        <td colspan="3" class="text-end fs-4 fw-bold">Celkem k úhradě:</td>
                        <td colspan="2" class="fs-4 fw-bold text-success"><?php echo $celkem; ?> Kč</td>
                    </tr>
                </tfoot>
            </table>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php" class="btn btn-outline-secondary">← Pokračovat v nákupu</a>
                <div>
                    <a href="smazat_kosik.php" class="btn btn-danger me-2">Vysypat košík</a>
                    <button class="btn btn-primary btn-lg">Odeslat objednávku</button>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <h4>Váš košík zeje prázdnotou...</h4>
                <p class="text-muted">Vyberte si něco z naší nabídky čerstvého ovoce.</p>
                <a href="index.php" class="btn btn-primary mt-3">Přejít do obchodu</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-5 text-muted">
    &copy; 2026 TropiOvoce s.r.o.
</footer>

</body>
</html>