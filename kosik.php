<?php
session_start();
include 'db.php';

// --- LOGIKA ODEBRÁNÍ POLOŽKY ---
if (isset($_GET['odebrat'])) {
    $id_k_odebrani = $_GET['odebrat'];
    if (($key = array_search($id_k_odebrani, $_SESSION['kosik'])) !== false) {
        unset($_SESSION['kosik'][$key]);
        $_SESSION['kosik'] = array_values($_SESSION['kosik']); // reindexace
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
        body { background-color: #fdfae6; background-image: url("https://www.transparenttextures.com/patterns/cubes.png"); color: #4e342e; }
        .card-basket { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .navbar { background-color: rgba(33, 37, 41, 0.9) !important; backdrop-filter: blur(10px); }
        .price-old { text-decoration: line-through; color: #a0a0a0; font-size: 0.9rem; }
        .discount-badge { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 50px; font-size: 0.8rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="index.php">🍍 TropiOvoce</a>
    </div>
</nav>

<div class="container">
    <div class="card card-basket p-4 mb-5">
        <h2 class="mb-4 fw-bold">🛒 Váš nákupní košík</h2>

        <?php if (isset($_SESSION['kosik']) && !empty($_SESSION['kosik'])): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Produkt</th>
                            <th>Cena za ks</th>
                            <th>Počet</th>
                            <th class="text-end">Mezisoučet</th>
                            <th class="text-center">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $celkem = 0;
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
                                    <span class="fw-bold d-block"><?php echo $row['nazev']; ?></span>
                                    <small class="text-muted">Dovoz: <?php echo $row['zeme_puvodu']; ?></small>
                                </td>
                                <td><?php echo $row['cena']; ?> Kč</td>
                                <td>
                                    <span class="badge bg-dark rounded-pill px-3"><?php echo $pocet; ?> ks</span>
                                </td>
                                <td class="text-end fw-bold"><?php echo $mezisoucet; ?> Kč</td>
                                <td class="text-center">
                                    <a href="kosik.php?odebrat=<?php echo $id; ?>" class="btn btn-sm btn-outline-danger">Odebrat 1 ks</a>
                                </td>
                            </tr>
                            <?php
                        }

                        // --- VÝPOČET SLEVY ---
                        $sleva_procenta = 0;
                        $zprava_pro_cleny = "";

                        if (isset($_SESSION['uzivatel_id'])) {
                            $sleva_procenta = 15; // Registrovaný člen má 15%
                            $zprava_pro_cleny = '<span class="discount-badge">Aktivována věrnostní sleva 15 %</span>';
                        } else {
                            $zprava_pro_cleny = '<a href="registrace.php" class="text-decoration-none text-muted small">Přihlaste se pro získání slevy 15 %</a>';
                        }

                        $castka_slevy = ($celkem * $sleva_procenta) / 100;
                        $k_uhrade = $celkem - $castka_slevy;
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-6">
                    <?php echo $zprava_pro_cleny; ?>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-1 <?php echo ($sleva_procenta > 0) ? 'price-old' : 'fw-bold fs-4'; ?>">
                        Součet položek: <?php echo $celkem; ?> Kč
                    </p>
                    
                    <?php if ($sleva_procenta > 0): ?>
                        <p class="text-success mb-1">Věrnostní sleva: -<?php echo round($castka_slevy); ?> Kč</p>
                        <h2 class="fw-bold text-dark">K úhradě: <?php echo round($k_uhrade); ?> Kč</h2>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-5">
                <a href="index.php" class="btn btn-outline-secondary px-4">← Zpět k nákupu</a>
                <div>
                    <a href="smazat_kosik.php" class="btn btn-danger me-2">Vysypat košík</a>
                    <button class="btn btn-success btn-lg px-5 shadow">Dokončit objednávku</button>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <h1 class="display-1">🛒</h1>
                <h3 class="mt-3">Váš košík je zatím prázdný</h3>
                <p class="text-muted">Ale naše ananasy a manga už se na vás těší!</p>
                <a href="index.php" class="btn btn-warning btn-lg mt-3 px-5">Přejít k výběru</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-5 text-muted">
    <p>&copy; 2026 TropiOvoce | Registrace = Sleva</p>
</footer>

</body>
</html>