<?php
session_start();

// Si ya hay una sesión activa de supervisor u oficial, redirigir
if (isset($_SESSION['supervisor']) || isset($_SESSION['oficial'])) {
    if (isset($_SESSION['supervisor'])) {
        header("Location: supervisorhome.php");
    } else {
        header("Location: oficialhome.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="shortcut icon" href="img/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <img src="img/logo.png" alt="Logo" class="logo">
        </div>
        <h2>Iniciar Sesión</h2>
        <form action="db.php" method="POST">
            <label for="officer_id">ID del Oficial o Supervisor</label>
            <input type="text" name="officer_id" id="officer_id" placeholder="Ingresa tu ID de oficial" required>
            <button type="submit" class="btn-login">Iniciar Sesión</button>
        </form>
        <?php if (isset($_GET['error'])): ?>
            <div id="error-message" class="error-message">
                ID de oficial incorrecto.
            </div>
        <?php endif; ?>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            document.getElementById('error-message').classList.add('active');
        }
    </script>
</body>
</html>
