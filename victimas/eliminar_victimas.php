<?php
session_start();

if (!isset($_SESSION['oficial'])) {
    header("Location: index.php");
    exit();
}

$oficial = $_SESSION['oficial'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estacion_policia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_victima = $_POST['id_victima'];

    $sql = "DELETE FROM victima WHERE id_victima = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_victima);

    if ($stmt->execute()) {
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al eliminar la víctima: " . $conn->error;
    }

    $stmt->close();
}

$id_victima = $_GET['id'];
$sql = "SELECT * FROM victima WHERE id_victima = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_victima);
$stmt->execute();
$result = $stmt->get_result();
$victima_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Eliminar Víctima</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Víctima</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="id_victima" value="<?php echo htmlspecialchars($victima_data['id_victima']); ?>">
            <div class="mb-3">
                <label for="nombre_victima" class="form-label">Nombre de la Víctima</label>
                <input type="text" class="form-control" id="nombre_victima" name="nombre_victima" value="<?php echo htmlspecialchars($victima_data['nombre_victima']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="direccion_victima" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion_victima" name="direccion_victima" value="<?php echo htmlspecialchars($victima_data['direccion_victima']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="estado_seguridad_victima" class="form-label">Estado de Seguridad</label>
                <input type="text" class="form-control" id="estado_seguridad_victima" name="estado_seguridad_victima" value="<?php echo htmlspecialchars($victima_data['estado_seguridad_victima']); ?>" disabled>
            </div>
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
