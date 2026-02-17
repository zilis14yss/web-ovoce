<?php
session_start();

$error = "";
// Nastav si své heslo
$heslo_v_kodu = "admin123"; 

if (isset($_POST['login'])) {
    if ($_POST['password'] == $heslo_v_kodu) {
        $_SESSION['prihlasen'] = true;
        header("Location: admin.php"); // Přesměrování do administrace
        exit();
    } else {
        $error = "Nesprávné heslo!";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Přihlášení | TropiOvoce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Vstup pro správce</h3>
                        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Heslo</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary w-100">Přihlásit se</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>