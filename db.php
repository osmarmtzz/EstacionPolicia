<?php
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
    $sql = "SELECT * FROM oficial WHERE id_oficial = '$officer_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // El oficial existe, iniciar sesión
        header("Location: hola.html"); // Ajusta el archivo según tu sistema
        exit();
    } else {
        // El oficial no existe, redirigir con mensaje de error
        header("Location: index.html?error=true");
        exit();
    }
}

$conn->close();
?>
