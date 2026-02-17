<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TropiOvoce | Exotický e-shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1519996529931-28324d5a630e?q=80&w=1974&auto=format&fit=crop');
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">🍍 TropiOvoce</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-link"><a class="nav-link active" href="index.php">Domů</a></li>
                <li class="nav-link"><a class="nav-link" href="#">Katalog</a></li>
                <li class="nav-link"><a class="nav-link" href="admin.php">Administrace</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <h1 class="display-3 fw-bold">Nejčerstvější tropické plody</h1>
        <p class="lead">Dovážíme přímo od farmářů z celého světa.</p>
        <a href="#nabidka" class="btn btn-warning btn-lg">Prohlédnout nabídku</a>
    </div>
</header>

<main class="container my-5" id="nabidka">
    <h2 class="text-center mb-4">Dnešní nabídka</h2>
    <div class="row">
    <?php
    $sql = "SELECT * FROM produkty";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="' . $row["obrazek_url"] . '" class="card-img-top" alt="' . $row["nazev"] . '">
                    <div class="card-body text-center">
                        <h5 class="card-title">' . $row["nazev"] . '</h5>
                        <p class="card-text text-muted">Země původu: ' . $row["zeme_puvodu"] . '</p>
                        <p class="fw-bold fs-4">' . $row["cena"] . ' Kč / ks</p>
                        <button class="btn btn-success">Do košíku</button>
                    </div>
                </div>
            </div>';
        }
    } else {
        echo "<p>Momentálně nemáme žádné ovoce skladem.</p>";
    }
    ?>
</div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <img src="https://images.unsplash.com/photo-1550258114-68bd2950ec91?q=80&w=1974&auto=format&fit=crop" class="card-img-top" alt="Ananas">
                <div class="card-body text-center">
                    <h5 class="card-title">Ananas Extra Sweet</h5>
                    <p class="card-text text-muted">Země původu: Kostarika</p>
                    <p class="fw-bold fs-4">65 Kč / ks</p>
                    <button class="btn btn-success">Do košíku</button>
                </div>
            </div>
        </div>
    </div>
</main>

<footer class="bg-dark text-white text-center py-4">
    <p>&copy; 2026 TropiOvoce s.r.o. | Semestrální práce</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>