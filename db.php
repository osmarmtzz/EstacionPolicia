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
    
    // Validar si el ID del oficial existe en la base de datos
    $sql = "SELECT * FROM oficial WHERE id_oficial = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $officer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El oficial existe, guardar datos en sesión
        $oficial = $result->fetch_assoc();
        $_SESSION['oficial'] = $oficial;
        header("Location: home.php");
        exit();
    } else {
        // El oficial no existe, redirigir de vuelta al index.php con mensaje de error
        header("Location: index.php?error=true");
        exit();
    }
}


$conn->close();
?>