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
    $nombre_victima = $_POST['nombre_victima'];
    $direccion_victima = $_POST['direccion_victima'];
    $estado_seguridad_victima = $_POST['estado_seguridad_victima'];

    $sql = "UPDATE victima SET nombre_victima = ?, direccion_victima = ?, estado_seguridad_victima = ? WHERE id_victima = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre_victima, $direccion_victima, $estado_seguridad_victima, $id_victima);

    if ($stmt->execute()) {
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al actualizar la víctima: " . $conn->error;
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
    <title>Estación de Policía - Editar Víctima</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Víctima</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="hidden" name="id_victima" value="<?php echo htmlspecialchars($victima_data['id_victima']); ?>">
            <div class="mb-3">
                <label for="nombre_victima" class="form-label">Nombre de la Víctima</label>
                <input type="text" class="form-control" id="nombre_victima" name="nombre_victima" value="<?php echo htmlspecialchars($victima_data['nombre_victima']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="direccion_victima" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion_victima" name="direccion_victima" value="<?php echo htmlspecialchars($victima_data['direccion_victima']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="estado_seguridad_victima" class="form-label">Estado de Seguridad</label>
                <select class="form-control" id="estado_seguridad_victima" name="estado_seguridad_victima" required>
                    <option value="Protegida" <?php echo ($victima_data['estado_seguridad_victima'] == 'Protegida') ? 'selected' : ''; ?>>Protegida</option>
                    <option value="En riesgo" <?php echo ($victima_data['estado_seguridad_victima'] == 'En riesgo') ? 'selected' : ''; ?>>En riesgo</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
