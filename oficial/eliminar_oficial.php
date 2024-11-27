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
    $id_oficial = $_POST['id_oficial'];

    $sql = "DELETE FROM oficial WHERE id_oficial = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_oficial);

    if ($stmt->execute()) {
        // After deletion, redirect to supervisorhome.php
        header("Location: ../supervisorhome.php");
        exit();
    } else {
        echo "Error al eliminar el oficial: " . $conn->error;
    }

    $stmt->close();
}

$id_oficial = $_GET['id'];
$sql = "SELECT * FROM oficial WHERE id_oficial = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_oficial);
$stmt->execute();
$result = $stmt->get_result();
$oficial_data = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Eliminar Oficial</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Oficial</h1>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="hidden" name="id_oficial" value="<?php echo $oficial_data['id_oficial']; ?>">
            <div class="mb-3">
                <label for="nombre_oficial" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre_oficial" name="nombre_oficial" value="<?php echo $oficial_data['nombre_oficial']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="rango_oficial" class="form-label">Rango</label>
                <input type="text" class="form-control" id="rango_oficial" name="rango_oficial" value="<?php echo $oficial_data['rango_oficial']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="años_servicio_oficial" class="form-label">Años de Servicio</label>
                <input type="number" class="form-control" id="años_servicio_oficial" name="años_servicio_oficial" value="<?php echo $oficial_data['años_servicio_oficial']; ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="id_estacion" class="form-label">Estación</label>
                <input type="text" class="form-control" id="id_estacion" name="id_estacion" value="<?php echo $oficial_data['id_estacion']; ?>" disabled>
            </div>
            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="../supervisorhome.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
