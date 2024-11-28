<?php
session_start();

// Verificar que el usuario haya iniciado sesión como oficial
if (!isset($_SESSION['oficial']) || !is_array($_SESSION['oficial'])) {
    header("Location: index.php");
    exit();
}

// Obtener los datos del oficial desde la sesión
$oficial = $_SESSION['oficial'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Oficial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Estación de Policía</a>
            <div class="navbar-text text-white ms-auto d-flex align-items-center">
                <span class="me-3">
                    <i class="fas fa-user me-2"></i>
                    <?php echo htmlspecialchars($oficial['nombre_oficial'] ?? 'Oficial'); ?>
                    <span class="badge bg-light text-dark ms-2">
                        ID: <?php echo htmlspecialchars($oficial['id_oficial'] ?? 'Desconocido'); ?>
                    </span>
                </span>
                <form method="POST" action="logout.php">
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Bienvenido, <?php echo htmlspecialchars($oficial['nombre_oficial'] ?? 'Oficial'); ?></h1>
        <p>Tu ID de oficial es: <?php echo htmlspecialchars($oficial['id_oficial'] ?? 'Desconocido'); ?></p>
    </div>
</body>
</html>
