<?php include 'db.php'; session_start(); ?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TropiOvoce | Exotický e-shop</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        /* TROPICKÉ POZADÍ A FONTY */
        body {
            background-color: #fdfae6; /* Krémová barva papíru */
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png"); 
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #4e342e;
        }

        /* STICKY NAVIGACE */
        .navbar {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background-color: rgba(33, 37, 41, 0.85) !important;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 2px solid #ffc107;
        }

        /* NOVÁ HERO SEKCE */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1528825831135-339165c07d24?q=80&w=2000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            text-align: center;
            border-bottom: 8px solid #ffc107;
            margin-bottom: 40px;
        }

        .hero-section h1 {
            font-size: 4rem;
            text-shadow: 3px 3px 15px rgba(0,0,0,0.7);
            font-weight: 800;
        }

        .shadow-text {
            text-shadow: 1px 1px 5px rgba(0,0,0,0.8);
        }
        
        #mapa { height: 400px; border-radius: 20px; margin-bottom: 50px; border: 3px solid #e9e4bc; }
        .card-img-top { height: 250px; object-fit: cover; }

        /* TROPICKÉ KARTY */
        .card {
            background-color: #ffffff;
            border: 2px solid #e9e4bc;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
            border-color: #28a745;
        }

        /* NADPISY SE SYMBOLY */
        h2 {
            color: #5d4037;
            font-weight: 800;
            margin-bottom: 30px;
        }

        h2::after {
            content: '🍍';
            display: block;
            font-size: 1.5rem;
            margin-top: 10px;
        }

        /* TLAČÍTKA */
        .btn-success {
            background-color: #28a745;
            border: none;
            font-weight: bold;
            padding: 10px;
            transition: all 0.2s ease;
        }

        .btn-success:active { transform: scale(0.95); }

        .btn-warning {
            background-color: #ffc107;
            border: none;
            font-weight: 800;
            color: #333;
        }

        /* ANIMACE PRO SEKCE */
        section, main {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.8s forwards;
        }

        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .nav-link:hover { color: #ffc107 !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="#">🍍 TropiOvoce</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto text-uppercase">
                <li class="nav-item"><a class="nav-link active" href="index.php">Domů</a></li>
                <li class="nav-item"><a class="nav-link" href="kosik.php">🛒 Košík (<?php echo isset($_SESSION['kosik']) ? count($_SESSION['kosik']) : 0; ?>)</a></li>
                <li class="nav-item"><a class="nav-link" href="admin.php">Administrace</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <h1 class="display-3 fw-bold">Šťavnaté Tropické Plody</h1>
        <p class="lead fs-3 shadow-text">Dovážíme slunce přímo na váš stůl.</p>
        <a href="#nabidka" class="btn btn-warning btn-lg px-5 py-3 shadow">Chci ochutnat!</a>
    </div>
</header>

<main class="container my-5" id="nabidka">
    <h2 class="text-center mb-4">Dnešní čerstvá nabídka</h2>
    <div class="row">
    <?php
    $sql = "SELECT * FROM produkty";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo $row['obrazek_url']; ?>" class="card-img-top" alt="<?php echo $row['nazev']; ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-bold"><?php echo $row['nazev']; ?></h5>
                        <p class="card-text text-muted">Země původu: <?php echo $row['zeme_puvodu']; ?></p>
                        <p class="fw-bold fs-4 text-success"><?php echo $row['cena']; ?> Kč / ks</p>
                        
                        <button type="button" class="btn btn-success w-100 shadow-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalKosik" 
                                data-id="<?php echo $row['id']; ?>" 
                                data-nazev="<?php echo $row['nazev']; ?>">
                            Přidat do košíku
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<p class="text-center fs-4">Momentálně čekáme na novou zásilku ovoce...</p>';
    }
    ?>
    </div>

    <hr class="my-5 border-warning opacity-50">
    <h2 class="text-center mb-4">Kde naše ovoce roste?</h2>
    <div id="mapa" class="shadow"></div>
</main>

<footer class="bg-dark text-white text-center py-5 mt-5 border-top border-warning">
    <div class="container">
        <p class="fs-5">🍍 TropiOvoce s.r.o. | Semestrální práce 2026</p>
        <small class="text-muted">Vyrobeno s láskou k exotice a PHP</small>
    </div>
</footer>

<div class="modal fade" id="modalKosik" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header bg-warning text-dark border-0">
        <h5 class="modal-title fw-bold" id="modalNazevProduktu">Přidat do košíku</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="pridat_do_kosiku.php" method="GET">
        <div class="modal-body p-4">
          <input type="hidden" name="id" id="modalInputId">
          <div class="mb-3">
            <label class="form-label fw-bold">Zadejte počet kusů:</label>
            <input type="number" name="pocet" class="form-control form-control-lg border-2" value="1" min="1" max="50">
          </div>
          <div class="alert alert-warning border-0 shadow-sm mt-4">
            <strong>💡 TIP:</strong> Zkuste také naše <strong>Avokádo</strong>! Je ideální pro zdravou snídani.
          </div>
        </div>
        <div class="modal-footer border-0 p-4 pt-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Zavřít</button>
          <button type="submit" class="btn btn-success px-4 py-2">Potvrdit výběr</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Modal Logic
    const modalKosik = document.getElementById('modalKosik');
    modalKosik.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nazev = button.getAttribute('data-nazev');

        modalKosik.querySelector('.modal-title').textContent = 'Koupit ' + nazev;
        modalKosik.querySelector('#modalInputId').value = id;
    });

    // 2. Leaflet Map
    const map = L.map('mapa').setView([10, -40], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const lokality = [
        { nazev: "Brazílie (Mango)", coords: [-14.235, -51.925] },
        { nazev: "Kostarika (Ananas)", coords: [9.748, -83.753] },
        { nazev: "Mexiko (Avokádo)", coords: [23.634, -102.552] }
    ];

    lokality.forEach(misto => {
        L.marker(misto.coords).addTo(map).bindPopup(`<b>${misto.nazev}</b><br>Děkujeme, že volíte čerstvost.`);
    });

    // 3. SweetAlert Success
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('vlozeno')) {
        Swal.fire({
            title: 'Skvělá volba!',
            text: 'Ovoce už na vás čeká v košíku.',
            icon: 'success',
            confirmButtonColor: '#28a745'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>
</body>
</html>