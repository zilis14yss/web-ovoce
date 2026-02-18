<?php
session_start();
include 'db.php';
$error = "";

if (isset($_POST['prihlasit'])) {
    $email = $_POST['email'];
    $heslo = $_POST['heslo'];

    // Najdeme uživatele podle emailu
    $sql = "SELECT * FROM uzivatele WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Ověření zahashovaného hesla
        if (password_verify($heslo, $user['heslo'])) {
            $_SESSION['uzivatel_id'] = $user['id'];
            $_SESSION['uzivatel_jmeno'] = $user['jmeno'];
            
            // Přesměrování do košíku, kde se hned aktivuje sleva
            header("Location: kosik.php");
            exit();
        } else {
            $error = "Nesprávné heslo!";
        }
    } else {
        $error = "Uživatel s tímto emailem neexistuje!";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení | TropiOvoce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fdfae6; background-image: url("https://www.transparenttextures.com/patterns/cubes.png"); }
        .card-login { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-login p-4 mt-5">
                <h2 class="text-center fw-bold">Vítejte zpět! 🍍</h2>
                <p class="text-center text-muted">Přihlaste se ke svému účtu</p>
                
                <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Emailová adresa</label>
                        <input type="email" name="email" class="form-control" placeholder="vase@adresa.cz" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Heslo</label>
                        <input type="password" name="heslo" class="form-control" placeholder="******" required>
                    </div>
                    <button type="submit" name="prihlasit" class="btn btn-success btn-lg w-100 shadow">Přihlásit se</button>
                </form>
                
                <div class="mt-4 text-center">
                    <p>Ještě nemáte účet? <a href="registrace.php" class="text-success fw-bold">Zaregistrujte se</a></p>
                    <a href="index.php" class="text-muted">Zpět do obchodu</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>