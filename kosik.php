<?php
session_start();
include 'db.php';

// --- LOGIKA ODEBRÁNÍ POLOŽKY ---
if (isset($_GET['odebrat'])) {
    $id_k_odebrani = $_GET['odebrat'];
    if (isset($_SESSION['kosik'])) {
        if (($key = array_search($id_k_odebrani, $_SESSION['kosik'])) !== false) {
            unset($_SESSION['kosik'][$key]);
            $_SESSION['kosik'] = array_values($_SESSION['kosik']); // reindexace
        }
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
        /* POZADÍ STRÁNKY S TVÝM OBRÁZKEM */
        body { 
            background-color: #fdfae6; 
            background-image: url("https://media.istockphoto.com/id/913533942/cs/fotografie/ko%C5%A1%C3%ADk-s-tropick%C3%BDm-ovocem.jpg?s=612x612&w=0&k=20&c=k-STCo103FLjLKgUkkCOrxE948IjDWeXH-SgMkzwJPY="); 
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            color: #4e342e; 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        
        /* KONTRASTNÍ KARTA KOŠÍKU */
        .card-basket { 
            border-radius: 30px; 
            border: none; 
            background-color: rgba(255, 255, 255, 0.98); 
            box-shadow: 0 20px 50px rgba(0,0,0,0.3); 
            margin-top: 50px;
        }

        .navbar { 
            background-color: rgba(33, 37, 41, 0.95) !important; 
            backdrop-filter: blur(10px); 
            border-bottom: 3px solid #2ecc71;
        }

        .price-old { text-decoration: line-through; color: #a0a0a0; font-size: 0.9rem; }
        .discount-badge { background-color: #28a745; color: white; padding: 5px 15px; border-radius: 50px; font-size: 0.9rem; font-weight: bold; }
        .shipping-info { font-size: 0.9rem; color: #1e3a2b; background: #e8f5e9; padding: 10px; border-radius: 15px; display: inline-block; }
        
        .table thead { background-color: #f8f9fa; }
        h2 { color: #1e3a2b; }
        .shadow-text { text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="index.php">🍍 TropiOvoce</a>
    </div>
</nav>

<div class="container">
    <div class="card card-basket p-4 p-md-5 mb-5">
        <h2 class="mb-4 fw-bold">🛒 Váš nákupní košík</h2>

        <?php if (isset($_SESSION['kosik']) && !empty($_SESSION['kosik'])): ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="py-3">Produkt</th>
                            <th class="py-3">Cena za ks</th>
                            <th class="py-3">Počet</th>
                            <th class="text-end py-3">Mezisoučet</th>
                            <th class="text-center py-3">Akce</th>
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
                                <td class="py-3">
                                    <span class="fw-bold d-block fs-5"><?php echo $row['nazev']; ?></span>
                                    <small class="text-muted text-uppercase">Původ: <?php echo $row['zeme_puvodu']; ?></small>
                                </td>
                                <td><?php echo $row['cena']; ?> Kč</td>
                                <td>
                                    <span class="badge bg-dark rounded-pill px-3 py-2"><?php echo $pocet; ?> ks</span>
                                </td>
                                <td class="text-end fw-bold fs-5"><?php echo $mezisoucet; ?> Kč</td>
                                <td class="text-center">
                                    <a href="kosik.php?odebrat=<?php echo $id; ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3">Odebrat 1 ks</a>
                                </td>
                            </tr>
                            <?php
                        }

                        // --- VÝPOČET SLEVY ---
                        $sleva_procenta = 0;
                        $zprava_pro_cleny = "";

                        if (isset($_SESSION['uzivatel_id'])) {
                            $sleva_procenta = 15;
                            $zprava_pro_cleny = '<span class="discount-badge shadow-sm">🌟 Aktivována věrnostní sleva 15 %</span>';
                        } else {
                            $zprava_pro_cleny = '<div class="alert alert-warning py-2 border-0 shadow-sm" style="font-size: 0.9rem; border-radius: 15px;">
                                <a href="registrace.php" class="alert-link">Zaregistrujte se</a> a získejte okamžitou slevu 15 %!
                            </div>';
                        }

                        $castka_slevy = ($celkem * $sleva_procenta) / 100;
                        $cena_po_sleve = $celkem - $castka_slevy;

                        // --- VÝPOČET DOPRAVY ---
                        $limit_zdarma = 800;
                        $cena_dopravy = 69;
                        $zbyva_do_zdarma = $limit_zdarma - $cena_po_sleve;

                        if ($cena_po_sleve >= $limit_zdarma) {
                            $cena_dopravy = 0;
                        }

                        $k_uhrade = $cena_po_sleve + $cena_dopravy;
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-md-6 mb-4 mb-md-0">
                    <?php echo $zprava_pro_cleny; ?>
                    <?php if ($cena_dopravy > 0): ?>
                        <div class="mt-3 shipping-info shadow-sm">
                            🚚 Nakupte ještě za <strong><?php echo round($zbyva_do_zdarma); ?> Kč</strong> a máte dopravu <strong>ZDARMA</strong>!
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-1 <?php echo ($sleva_procenta > 0) ? 'price-old' : 'fw-bold'; ?>">
                        Mezisoučet: <?php echo $celkem; ?> Kč
                    </p>
                    
                    <?php if ($sleva_procenta > 0): ?>
                        <p class="text-success mb-1 fw-bold">Věrnostní sleva (15%): -<?php echo round($castka_slevy); ?> Kč</p>
                    <?php endif; ?>

                    <p class="mb-1 fs-5">
                        Doprava: 
                        <?php if ($cena_dopravy == 0): ?>
                            <span class="text-success fw-bold text-uppercase">Zdarma</span>
                        <?php else: ?>
                            <span class="fw-bold"><?php echo $cena_dopravy; ?> Kč</span>
                        <?php endif; ?>
                    </p>

                    <h1 class="fw-bold text-dark mt-3 display-6">Celkem: <?php echo round($k_uhrade); ?> Kč</h1>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between mt-5 gap-3">
                <a href="index.php" class="btn btn-outline-secondary btn-lg rounded-pill px-4 shadow-sm">← Zpět k nákupu</a>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="smazat_kosik.php" class="btn btn-light btn-lg rounded-pill px-4 border shadow-sm">Vysypat košík</a>
                    
                    <?php if(isset($_SESSION['uzivatel_id'])): ?>
                        <a href="odeslat_objednavku.php" class="btn btn-success btn-lg rounded-pill px-5 shadow fw-bold">Dokončit a zaplatit</a>
                    <?php else: ?>
                        <a href="login_uzivatel.php" class="btn btn-warning btn-lg rounded-pill px-5 shadow fw-bold">Přihlaste se pro objednání</a>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <div class="text-center py-5">
                <div style="font-size: 6rem;" class="mb-4">🛒</div>
                <h2 class="fw-bold">Váš košík zeje prázdnotou</h2>
                <p class="text-muted fs-5">Ale naše šťavnaté plody jsou připraveny k odeslání!</p>
                <a href="index.php" class="btn btn-warning btn-lg mt-4 px-5 shadow rounded-pill fw-bold">Doplnit vitamíny</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="text-center py-5 text-white">
    <div class="container">
        <p class="mb-0 fw-bold shadow-text">&copy; 2026 TropiOvoce | Exotická čerstvost až k vám domů</p>
    </div>
</footer>

</body>
</html>