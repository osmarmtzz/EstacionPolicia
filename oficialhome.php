<?php
session_start();

// Check if user is logged in as an oficial
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'oficial') {
    header("Location: index.php");
    exit();
}

$oficial = $_SESSION['oficial'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Oficial Home</title>
</head>
<body>
    <h1>Bienvenido Oficial <?php echo htmlspecialchars($oficial['nombre_oficial']); ?></h1>
    
    <div>
        <h2>Menú Principal</h2>
        <ul>
            <li><a href="mis_casos.php">Mis Casos</a></li>
            <li><a href="evidencias.php">Gestionar Evidencias</a></li>
            <li><a href="logout.php">Cerrar Sesión</a></li>
        </ul>
    </div>
</body>
</html>