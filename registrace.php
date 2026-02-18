<?php
include 'db.php';
$msg = "";

if (isset($_POST['registrovat'])) {
    $jmeno = $_POST['jmeno'];
    $email = $_POST['email'];
    $heslo = password_hash($_POST['heslo'], PASSWORD_DEFAULT); // Zabezpečení hesla

    $sql = "INSERT INTO uzivatele (jmeno, email, heslo) VALUES ('$jmeno', '$email', '$heslo')";
    
    if ($conn->query($sql) === TRUE) {
        $msg = "<div class='alert alert-success'>Registrace úspěšná! Nyní se můžete přihlásit.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Chyba: Email již existuje.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Registrace | TropiOvoce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background-color: #fdfae6; }</style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5 card shadow p-4">
            <h2 class="text-center">Vstupte do klubu 🍍</h2>
            <p class="text-center text-muted">Získejte okamžitou slevu 15% na každý nákup.</p>
            <?php echo $msg; ?>
            <form method="POST">
                <input type="text" name="jmeno" class="form-control mb-3" placeholder="Celé jméno" required>
                <input type="email" name="email" class="form-control mb-3" placeholder="Váš Email" required>
                <input type="password" name="heslo" class="form-control mb-3" placeholder="Heslo" required>
                <button type="submit" name="registrovat" class="btn btn-success w-100">Registrovat se a získat slevu</button>
            </form>
            <div class="mt-3 text-center"><a href="index.php">Zpět na hlavní stránku</a></div>
        </div>
    </div>
</div>
</body>
</html>