<?php
session_start();

if (!isset($_SESSION['supervisor'])) {
    header("Location: index.php");
    exit();
}

$supervisor = $_SESSION['supervisor'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estacion_policia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_sospechoso = $_POST['id_sospechoso'];

    $sql = "DELETE FROM sospechoso WHERE id_sospechoso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_sospechoso);

    if ($stmt->execute()) {
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al eliminar el sospechoso: " . $conn->error;
    }

    $stmt->close();
}

$id_sospechoso = $_GET['id'];
$sql = "SELECT * FROM sospechoso WHERE id_sospechoso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_sospechoso);
$stmt->execute();
$result = $stmt->get_result();
$sospechoso_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Eliminar Sospechoso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Sospechoso</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="id_sospechoso" value="<?php echo htmlspecialchars($sospechoso_data['id_sospechoso']); ?>">
            <div class="mb-3">
                <label for="nombre_sospechoso" class="form-label">Nombre del Sospechoso</label>
                <input type="text" class="form-control" id="nombre_sospechoso" name="nombre_sospechoso" value="<?php echo htmlspecialchars($sospechoso_data['nombre_sospechoso']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="direccion_sospechoso" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion_sospechoso" name="direccion_sospechoso" value="<?php echo htmlspecialchars($sospechoso_data['direccion_sospechoso']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="estado_arresto_sospechoso" class="form-label">Estado de Arresto</label>
                <input type="text" class="form-control" id="estado_arresto_sospechoso" name="estado_arresto_sospechoso" value="<?php echo htmlspecialchars($sospechoso_data['estado_arresto_sospechoso']); ?>" disabled>
            </div>
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
