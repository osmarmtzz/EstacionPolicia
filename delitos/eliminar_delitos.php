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
    $id_delito = $_POST['id_delito'];

    $sql = "DELETE FROM delito WHERE id_delito = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_delito);

    if ($stmt->execute()) {
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al eliminar el delito: " . $conn->error;
    }

    $stmt->close();
}

$id_delito = $_GET['id'];
$sql = "SELECT * FROM delito WHERE id_delito = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_delito);
$stmt->execute();
$result = $stmt->get_result();
$delito_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Eliminar Delito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Delito</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="id_delito" value="<?php echo htmlspecialchars($delito_data['id_delito']); ?>">
            <div class="mb-3">
                <label for="nombre_delito" class="form-label">Nombre del Delito</label>
                <input type="text" class="form-control" id="nombre_delito" name="nombre_delito" value="<?php echo htmlspecialchars($delito_data['nombre_delito']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="descripcion_delito" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion_delito" name="descripcion_delito" rows="3" disabled><?php echo htmlspecialchars($delito_data['descripcion_delito']); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="categoria_delito" class="form-label">Categoría</label>
                <input type="text" class="form-control" id="categoria_delito" name="categoria_delito" value="<?php echo htmlspecialchars($delito_data['categoria_delito']); ?>" disabled>
            </div>
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
