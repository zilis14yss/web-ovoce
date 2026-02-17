<?php
session_start();
if (!isset($_SESSION['prihlasen']) || $_SESSION['prihlasen'] !== true) {
    header("Location: login.php"); // Pokud není přihlášen, pošli ho na login
    exit();
}
?>
<?php
include 'db.php'; 

// Jednoduché zpracování formuláře po odeslání
if (isset($_POST['pridat'])) {
    $nazev = $_POST['nazev'];
    $zeme = $_POST['zeme'];
    $cena = $_POST['cena'];
    $url = $_POST['url'];

    $sql = "INSERT INTO produkty (nazev, zeme_puvodu, cena, obrazek_url) VALUES ('$nazev', '$zeme', '$cena', '$url')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Produkt byl úspěšně přidán!</div>";
    } else {
        echo "Chyba: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Administrace | TropiOvoce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a href="logout.php" class="btn btn-outline-light btn-sm">Odhlásit se</a>
        <a class="navbar-brand" href="index.php">← Zpět na web</a>
        <span class="navbar-text text-white">Administrace systému</span>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Přidat nové ovoce</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="admin.php">
                        <div class="mb-3">
                            <label class="form-label">Název ovoce</label>
                            <input type="text" name="nazev" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Země původu</label>
                            <input type="text" name="zeme" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cena (Kč/ks)</label>
                            <input type="number" name="cena" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">URL obrázku</label>
                            <input type="text" name="url" class="form-control" placeholder="https://..." required>
                        </div>
                        <button type="submit" name="pridat" class="btn btn-success w-100">Uložit do databáze</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>