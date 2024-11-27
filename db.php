<?php
session_start();

$servername = "localhost"; 
$username = "root";        
$password = "";            
$dbname = "estacion_policia"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar el inicio de sesión si se recibe el ID del oficial
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $officer_id = $_POST['officer_id'];
    
    // Primero, verificar si es supervisor
    $supervisor_sql = "SELECT * FROM supervisor WHERE id_supervisor = ?";
    $supervisor_stmt = $conn->prepare($supervisor_sql);
    $supervisor_stmt->bind_param("i", $officer_id);
    $supervisor_stmt->execute();
    $supervisor_result = $supervisor_stmt->get_result();

    if ($supervisor_result->num_rows > 0) {
        // Es un supervisor
        $supervisor = $supervisor_result->fetch_assoc();
        $_SESSION['supervisor'] = $supervisor['id_supervisor'];  // Almacenar solo el id
        $_SESSION['user_type'] = 'supervisor';  // Establecer el tipo de usuario
        header("Location: supervisorhome.php");
        exit();
    }

    // Si no es supervisor, verificar si es oficial
    $oficial_sql = "SELECT * FROM oficial WHERE id_oficial = ?";
    $oficial_stmt = $conn->prepare($oficial_sql);
    $oficial_stmt->bind_param("i", $officer_id);
    $oficial_stmt->execute();
    $oficial_result = $oficial_stmt->get_result();

    if ($oficial_result->num_rows > 0) {
        // El oficial existe, guardar datos en sesión
        $oficial = $oficial_result->fetch_assoc();
        $_SESSION['oficial'] = $oficial['id_oficial'];  // Almacenar solo el id
        $_SESSION['user_type'] = 'oficial';  // Establecer el tipo de usuario
        header("Location: oficialhome.php");
        exit();
    } else {
        // El oficial no existe, redirigir de vuelta al index.php con mensaje de error
        header("Location: index.php?error=true");
        exit();
    }
}

$conn->close();
?>
