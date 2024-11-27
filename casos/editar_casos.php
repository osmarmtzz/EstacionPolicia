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
    $id_caso = $_POST['id_caso'];
    $descripcion_caso = $_POST['descripcion_caso'];
    $fecha_creacion_caso = $_POST['fecha_creacion_caso'];
    $estado_caso = $_POST['estado_caso'];
    $id_estacion = $_POST['id_estacion'];

    $sql = "UPDATE caso SET descripcion_caso = ?, fecha_creacion_caso = ?, estado_caso = ?, id_estacion = ? WHERE id_caso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $descripcion_caso, $fecha_creacion_caso, $estado_caso, $id_estacion, $id_caso);

    if ($stmt->execute()) {
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al actualizar el caso: " . $conn->error;
    }

    $stmt->close();
}

$id_caso = $_GET['id'];
$sql = "SELECT * FROM caso WHERE id_caso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_caso);
$stmt->execute();
$result = $stmt->get_result();
$caso_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Editar Caso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Editar Caso</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="hidden" name="id_caso" value="<?php echo $caso_data['id_caso']; ?>">
            <div class="mb-3">
                <label for="descripcion_caso" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_caso" name="descripcion_caso" rows="3" required readonly><?php echo $caso_data['descripcion_caso']; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="fecha_creacion_caso" class="form-label">Fecha de Creación</label>
                <input type="date" class="form-control" id="fecha_creacion_caso" name="fecha_creacion_caso" value="<?php echo $caso_data['fecha_creacion_caso']; ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="id_estacion" class="form-label">Estación</label>
                <input type="text" class="form-control" id="id_estacion" name="id_estacion" value="<?php echo $caso_data['id_estacion']; ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="estado_caso" class="form-label">Estado</label>
                <select class="form-control" id="estado_caso" name="estado_caso" required>
                    <option value="Abierto" <?php if ($caso_data['estado_caso'] == 'Abierto') echo 'selected'; ?>>Abierto</option>
                    <option value="En investigación" <?php if ($caso_data['estado_caso'] == 'En investigación') echo 'selected'; ?>>En investigación</option>
                    <option value="Cerrado" <?php if ($caso_data['estado_caso'] == 'Cerrado') echo 'selected'; ?>>Cerrado</option>
                    <option value="Archivado" <?php if ($caso_data['estado_caso'] == 'Archivado') echo 'selected'; ?>>Archivado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>