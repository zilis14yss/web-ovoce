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
        /* CELKOVÉ POZADÍ S TVÝM OBRÁZKEM */
        body {
            background-color: #f7f3e3; 
            background-image: url('https://amazingthailand.cz/wp-content/uploads/2022/06/Thajske-ovoce-1.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #2d3436;
        }

        /* STICKY NAVIGACE */
        .navbar {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background-color: rgba(33, 37, 41, 0.95) !important;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 3px solid #2ecc71;
        }

        /* HERO SEKCE S KONTRASTNÍ BUBLINOU */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3));
            padding: 160px 0;
            text-align: center;
        }

        .hero-bubble {
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            display: inline-block;
            padding: 40px 60px;
            border-radius: 50px;
            border: 2px solid #2ecc71;
            color: white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .hero-section h1 {
            font-size: 4.5rem;
            font-weight: 900;
            text-transform: uppercase;
            color: #2ecc71;
            margin-bottom: 10px;
        }

        /* KONTRASTNÍ BUBLINY PRO NADPISY */
        .section-title-container {
            background: #ffffff;
            display: inline-block;
            padding: 15px 50px;
            border-radius: 100px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 4px solid #2ecc71;
            margin-bottom: 50px;
        }

        .section-title-container h2 {
            margin-bottom: 0;
            color: #1e3a2b;
        }

        .section-title-container h2::after {
            display: none; /* Odstraníme ikonu z CSS, vložíme ji přímo do HTML */
        }

        /* KARTY PRODUKTŮ - PLNĚ BÍLÉ POZADÍ */
        .card {
            background-color: #ffffff !important;
            border: none;
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(46, 204, 113, 0.3) !important;
        }

        .card-img-top {
            height: 260px;
            object-fit: cover;
            border-bottom: 5px solid #ffc107;
        }

        /* MAPA V BÍLÉM KONTEJNERU */
        .map-container-bubble {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 50px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 4px solid #2ecc71;
            margin: 80px 0;
        }

        .map-text-bubble {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 30px;
            margin-bottom: 30px;
            border: 1px solid #dee2e6;
        }

        #mapa { 
            height: 450px; 
            border-radius: 25px; 
            border: 1px solid #ddd;
        }

        .btn-success { background-color: #2ecc71; border: none; font-weight: bold; padding: 12px; }
        .btn-warning { background-color: #ffc107; border: none; font-weight: 800; color: #333; }

        section, main { opacity: 0; transform: translateY(20px); animation: fadeInUp 0.8s forwards; }
        @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold text-warning" href="index.php">🍍 TropiOvoce</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto text-uppercase small fw-bold">
                <li class="nav-item"><a class="nav-link active" href="index.php">Domů</a></li>
                <li class="nav-item"><a class="nav-link" href="kosik.php">🛒 Košík (<?php echo isset($_SESSION['kosik']) ? count($_SESSION['kosik']) : 0; ?>)</a></li>
                
                <?php if(isset($_SESSION['uzivatel_id'])): ?>
                    <li class="nav-item"><a class="nav-link text-warning" href="#">🌴 <?php echo $_SESSION['uzivatel_jmeno']; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="logout_uzivatel.php">Odhlásit</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login_uzivatel.php">Přihlášení</a></li>
                <?php endif; ?>
                
                <li class="nav-item"><a class="nav-link text-muted" href="admin.php">Admin</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <div class="hero-bubble shadow">
            <h1 class="display-3 fw-bold">Tropický Ráj</h1>
            <p class="lead fs-2 fw-bold text-white mb-4">Dovážíme čerstvost přímo z hlubin pralesa.</p>
            <a href="#nabidka" class="btn btn-warning btn-lg px-5 py-3 shadow-lg rounded-pill">Prozkoumat džungli chutí</a>
        </div>
    </div>
</header>

<?php if(!isset($_SESSION['uzivatel_id'])): ?>
<div class="container mt-4">
    <div class="alert alert-warning border-0 shadow-lg d-flex justify-content-between align-items-center p-4" style="border-radius: 30px; background: white; border: 2px solid #ffc107 !important;">
        <div>
            <h4 class="mb-1 fw-bold text-dark">🌟 Staň se členem TropiKlubu!</h4>
            <p class="mb-0 text-dark">Registruj se a získej <strong>doživotní slevu 15%</strong>.</p>
        </div>
        <a href="registrace.php" class="btn btn-dark btn-lg px-4 rounded-pill shadow">Chci výhody</a>
    </div>
</div>
<?php endif; ?>

<main class="container my-5" id="nabidka">
    <div class="text-center">
        <div class="section-title-container">
            <h2 class="fw-bold">🌴 Dnešní čerstvý sběr 🌴</h2>
        </div>
    </div>

    <div class="row">
    <?php
    $sql = "SELECT * FROM produkty";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-md-4 mb-5">
                <div class="card h-100 shadow">
                    <img src="<?php echo $row['obrazek_url']; ?>" class="card-img-top" alt="<?php echo $row['nazev']; ?>">
                    <div class="card-body text-center p-4">
                        <h4 class="card-title fw-bold mb-3 text-dark"><?php echo $row['nazev']; ?></h4>
                        <p class="card-text text-muted mb-2">Původ: <strong><?php echo $row['zeme_puvodu']; ?></strong></p>
                        <p class="fw-bold fs-3 text-success mb-4"><?php echo $row['cena']; ?> Kč</p>
                        
                        <button type="button" class="btn btn-success w-100 rounded-pill shadow-sm py-2 fw-bold" 
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
    }
    ?>
    </div>

    <div class="map-container-bubble">
        <div class="text-center map-text-bubble mx-auto shadow-sm" style="max-width: 800px;">
            <h2 class="fw-bold text-dark mb-2">🌴 Cesta z pralesa až k vám</h2>
            <p class="text-muted fs-5 mb-0">Sledujte původ našich plodů na interaktivní mapě světa.</p>
        </div>
        <div id="mapa" class="shadow-sm"></div>
    </div>
</main>

<footer class="bg-dark text-white text-center py-5 mt-5 border-top border-success">
    <div class="container">
        <h3 class="fw-bold text-warning mb-3">🍍 TropiOvoce s.r.o.</h3>
        <p class="text-muted">Přinášíme kousek exotiky do každé domácnosti.</p>
        <small class="text-muted">Semestrální práce 2026 | Vyrobeno s láskou k přírodě</small>
    </div>
</footer>

<div class="modal fade" id="modalKosik" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
      <div class="modal-header bg-success text-white border-0 p-4">
        <h5 class="modal-title fw-bold" id="modalNazevProduktu">🛒 Přidat do košíku</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="pridat_do_kosiku.php" method="GET">
        <div class="modal-body p-4 text-dark">
          <input type="hidden" name="id" id="modalInputId">
          <div class="mb-4">
            <label class="form-label fw-bold fs-5">Kolik kusů si přejete?</label>
            <input type="number" name="pocet" class="form-control form-control-lg border-2" value="1" min="1" max="50" style="border-radius: 15px;">
          </div>
        </div>
        <div class="modal-footer border-0 p-4 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Zrušit</button>
          <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-bold">Potvrdit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const modalKosik = document.getElementById('modalKosik');
    modalKosik.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nazev = button.getAttribute('data-nazev');
        modalKosik.querySelector('.modal-title').textContent = 'Koupit ' + nazev;
        modalKosik.querySelector('#modalInputId').value = id;
    });

    const map = L.map('mapa').setView([10, -30], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const lokality = [
        { nazev: "Brazílie (Mango)", coords: [-14.235, -51.925] },
        { nazev: "Kostarika (Ananas)", coords: [9.748, -83.753] },
        { nazev: "Mexiko (Avokádo)", coords: [23.634, -102.552] }
    ];

    lokality.forEach(misto => {
        L.marker(misto.coords).addTo(map).bindPopup(`<b style="color: #2ecc71;">${misto.nazev}</b>`);
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('vlozeno')) {
        Swal.fire({
            title: 'Paráda!',
            text: 'Ovoce je připraveno v košíku.',
            icon: 'success',
            background: '#ffffff',
            confirmButtonColor: '#2ecc71'
        });
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>
</body>
</html>