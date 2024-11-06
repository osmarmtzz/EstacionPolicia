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

// Función para obtener datos de cualquier tabla
function getDatosTabla($conn, $tabla) {
    $sql = "SELECT * FROM $tabla";
    return $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estación de Policía - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/supervisorhome.css">

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Estación de Policía</a>
        <div class="navbar-text text-white ms-auto d-flex align-items-center">
            <span class="me-3">
                <i class="fas fa-user me-2"></i>
                <?php echo htmlspecialchars($oficial['nombre_oficial']); ?> 
                <span class="badge bg-light text-dark ms-2">ID: <?php echo htmlspecialchars($oficial['id_oficial']); ?></span>
            </span>
            <form method="POST" action="logout.php">
                <button type="submit" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>

    <div class="container mt-4">
        <div class="row">
            <!-- Sección de Oficiales -->
            <div class="col-md-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Oficiales</h5>
                        
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Rango</th>
                                        <th>Años de Servicio</th>
                                        <th>Estación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = getDatosTabla($conn, 'oficial');
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['id_oficial']}</td>";
                                        echo "<td>{$row['nombre_oficial']}</td>";
                                        echo "<td>{$row['rango_oficial']}</td>";
                                        echo "<td>{$row['años_servicio_oficial']}</td>";
                                        echo "<td>{$row['id_estacion']}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            

            <!-- Sección de Casos -->
            <div class="col-md-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Casos</h5>
                       
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Estación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = getDatosTabla($conn, 'caso');
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['id_caso']}</td>";
                                        echo "<td>{$row['descripcion_caso']}</td>";
                                        echo "<td>{$row['fecha_creacion_caso']}</td>";
                                        echo "<td>{$row['estado_caso']}</td>";
                                        echo "<td>{$row['id_estacion']}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Delitos -->
        <div class="col-md-12 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Delitos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Categoría</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = getDatosTabla($conn, 'delito');
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['id_delito']}</td>";
                                    echo "<td>{$row['nombre_delito']}</td>";
                                    echo "<td>{$row['descripcion_delito']}</td>";
                                    echo "<td>{$row['categoria_delito']}</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Víctimas -->
        <div class="col-md-12 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Víctimas</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Estado de Seguridad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = getDatosTabla($conn, 'victima');
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['id_victima']}</td>";
                                    echo "<td>{$row['nombre_victima']}</td>";
                                    echo "<td>{$row['direccion_victima']}</td>";
                                    echo "<td>{$row['estado_seguridad_victima']}</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Sospechosos -->
        <div class="col-md-12 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Sospechosos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Estado de Arresto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result = getDatosTabla($conn, 'sospechoso');
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$row['id_sospechoso']}</td>";
                                    echo "<td>{$row['nombre_sospechoso']}</td>";
                                    echo "<td>{$row['direccion_sospechoso']}</td>";
                                    echo "<td>{$row['estado_arresto_sospechoso']}</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

   

            <!-- Sección de Evidencias -->
            <div class="col-md-12 mb-4">
                <div class="card dashboard-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Evidencias</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                        <th>Lugar</th>
                                        <th>Caso</th>
                                        <th>Oficial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = getDatosTabla($conn, 'evidencia');
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$row['id_evidencia']}</td>";
                                        echo "<td>{$row['tipo_evidencia']}</td>";
                                        echo "<td>{$row['descripcion_evidencia']}</td>";
                                        echo "<td>{$row['fecha_registro_evidencia']}</td>";
                                        echo "<td>{$row['lugar_recolectada_evidencia']}</td>";
                                        echo "<td>{$row['id_caso']}</td>";
                                        echo "<td>{$row['id_oficial']}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>