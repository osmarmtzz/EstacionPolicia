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
    $id_evidencia = $_POST['id_evidencia'];
    $tipo_evidencia = $_POST['tipo_evidencia'];
    $descripcion_evidencia = $_POST['descripcion_evidencia'];
    $fecha_registro_evidencia = $_POST['fecha_registro_evidencia'];
    $lugar_recolectada_evidencia = $_POST['lugar_recolectada_evidencia'];
    $id_caso = $_POST['id_caso'];
    $id_oficial = $_SESSION['oficial_id'];  // Suponiendo que el ID del oficial está guardado en la sesión

    $sql = "UPDATE evidencia SET tipo_evidencia = ?, descripcion_evidencia = ?, fecha_registro_evidencia = ?, lugar_recolectada_evidencia = ?, id_caso = ?, id_oficial = ? WHERE id_evidencia = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $tipo_evidencia, $descripcion_evidencia, $fecha_registro_evidencia, $lugar_recolectada_evidencia, $id_caso, $id_oficial, $id_evidencia);

    if ($stmt->execute()) {
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al actualizar la evidencia: " . $conn->error;
    }

    $stmt->close();
}

$id_evidencia = $_GET['id'];
$sql = "SELECT * FROM evidencia WHERE id_evidencia = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_evidencia);
$stmt->execute();
$result = $stmt->get_result();
$evidencia_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Editar Evidencia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Evidencia</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="hidden" name="id_evidencia" value="<?php echo htmlspecialchars($evidencia_data['id_evidencia']); ?>">
            <div class="mb-3">
                <label for="tipo_evidencia" class="form-label">Tipo de Evidencia</label>
                <input type="text" class="form-control" id="tipo_evidencia" name="tipo_evidencia" value="<?php echo htmlspecialchars($evidencia_data['tipo_evidencia']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion_evidencia" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_evidencia" name="descripcion_evidencia" rows="3" required><?php echo htmlspecialchars($evidencia_data['descripcion_evidencia']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="fecha_registro_evidencia" class="form-label">Fecha de Registro</label>
                <input type="date" class="form-control" id="fecha_registro_evidencia" name="fecha_registro_evidencia" value="<?php echo htmlspecialchars($evidencia_data['fecha_registro_evidencia']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="lugar_recolectada_evidencia" class="form-label">Lugar de Recolectada</label>
                <input type="text" class="form-control" id="lugar_recolectada_evidencia" name="lugar_recolectada_evidencia" value="<?php echo htmlspecialchars($evidencia_data['lugar_recolectada_evidencia']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_caso" class="form-label">ID del Caso</label>
                <input type="number" class="form-control" id="id_caso" name="id_caso" value="<?php echo htmlspecialchars($evidencia_data['id_caso']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
