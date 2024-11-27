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

// Procesar formularios de inserción
// Procesar formularios de inserción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insertar nuevo oficial
    if (isset($_POST['add_oficial'])) {
        $nombre = $_POST['nombre_oficial'];
        $rango = $_POST['rango_oficial'];
        $años = $_POST['años_servicio'];
        $estacion = $_POST['id_estacion'];
        
        $sql = "INSERT INTO oficial (nombre_oficial, rango_oficial, años_servicio_oficial, id_estacion) 
                VALUES ('$nombre', '$rango', $años, $estacion)";
        $conn->query($sql);
    }
    // Insertar nuevo caso
    elseif (isset($_POST['add_caso'])) {
        $descripcion = $_POST['descripcion_caso'];
        $fecha = $_POST['fecha_creacion'];
        $estado = $_POST['estado_caso'];
        $estacion = $_POST['id_estacion'];
        
        $sql = "INSERT INTO caso (descripcion_caso, fecha_creacion_caso, estado_caso, id_estacion) 
                VALUES ('$descripcion', '$fecha', '$estado', $estacion)";
        $conn->query($sql);
    }
    // Insertar nuevo delito
    elseif (isset($_POST['add_delito'])) {
        $nombre_delito = $_POST['nombre_delito'];
        $descripcion_delito = $_POST['descripcion_delito'];
        $categoria_delito = $_POST['categoria_delito'];

        $sql = "INSERT INTO delito (nombre_delito, descripcion_delito, categoria_delito) 
                VALUES ('$nombre_delito', '$descripcion_delito', '$categoria_delito')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: supervisorhome.php");  // Redirige a la página de delitos
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    // Insertar nueva víctima
    elseif (isset($_POST['add_victima'])) {
        $nombre_victima = $_POST['nombre_victima'];
        $direccion_victima = $_POST['direccion_victima'];
        $estado_seguridad_victima = $_POST['estado_seguridad_victima'];

        $sql = "INSERT INTO victima (nombre_victima, direccion_victima, estado_seguridad_victima) 
                VALUES ('$nombre_victima', '$direccion_victima', '$estado_seguridad_victima')";
        
        if ($conn->query($sql) === TRUE) {
            // Si la inserción es exitosa, redirigir o mostrar mensaje de éxito
            header("Location: supervisorhome.php");  // Redirige a la página de víctimas
            exit();
        } else {
            // Si hay un error en la consulta
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    
    // Recargar la página para mostrar los nuevos datos
    header("Location: supervisorhome.php");  // Cambia esta URL por la correcta en tu proyecto
    exit();
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
        <!-- Sección de Oficiales -->
        <div class="col-md-12 mb-4">
            <div class="card dashboard-card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Oficiales</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddOficial">
                            <i class="fas fa-plus"></i> Añadir Oficial
                        </button>
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
                                    <th>Acciones</th>
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
                                    echo "<td>
                                            <a href='oficial/editar_oficial.php?id={$row['id_oficial']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='oficial/eliminar_oficial.php?id={$row['id_oficial']}' class='btn btn-sm btn-danger'>Eliminar</a>
                                        </td>";
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
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddCaso">
                            <i class="fas fa-plus"></i> Añadir Caso
                        </button>
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
                                    <th>Acciones</th>
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
                                    echo "<td>
                                            <a href='casos/editar_casos.php?id={$row['id_caso']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='casos/eliminar_casos.php?id={$row['id_caso']}' class='btn btn-sm btn-danger'>Eliminar</a>
                                        </td>";
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
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Delitos</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddDelito">
                <i class="fas fa-plus"></i> Añadir Delito
            </button>
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
                            <th>Acciones</th>
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
                            echo "<td>
                                    <a href='delitos/editar_delitos.php?id={$row['id_delito']}' class='btn btn-sm btn-primary'>Editar</a>
                                    <a href='delitos/eliminar_delitos.php?id={$row['id_delito']}' class='btn btn-sm btn-danger'>Eliminar</a>
                                </td>";
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
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Víctimas</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddVictima">
                <i class="fas fa-plus"></i> Añadir Víctima
            </button>
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
                            <th>Acciones</th>
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
                            echo "<td>
                                    <a href='victimas/editar_victimas.php?id={$row['id_victima']}' class='btn btn-sm btn-primary'>Editar</a>
                                    <a href='victimas/eliminar_victimas.php?id={$row['id_victima']}' class='btn btn-sm btn-danger'>Eliminar</a>
                                </td>";
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
                                    <th>Acciones</th>
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
                                    echo "<td>
                                            <a href='sospechosos/editar_sospechoso.php?id={$row['id_sospechoso']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='sospechosos/eliminar_sospechoso.php?id={$row['id_sospechoso']}' class='btn btn-sm btn-danger'>Eliminar</a>
                                        </td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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
                                    <th>Acciones</th>
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
                                    echo "<td>
                                            <a href='evidencia/editar_evidencia.php?id={$row['id_evidencia']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='evidencia/eliminar_evidencia.php?id={$row['id_evidencia']}' class='btn btn-sm btn-danger'>Eliminar</a>
                                        </td>";
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
<!-- Modal para añadir Oficial -->
<div class="modal fade" id="modalAddOficial" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Nuevo Oficial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre_oficial" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rango</label>
                            <input type="text" class="form-control" name="rango_oficial" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Años de Servicio</label>
                            <input type="number" class="form-control" name="años_servicio" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estación</label>
                            <select class="form-control" name="id_estacion" required>
                                <?php
                                $result = getDatosTabla($conn, 'estacion');
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id_estacion']}'>{$row['nombre_estacion']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" name="add_oficial" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para añadir Caso -->
    <div class="modal fade" id="modalAddCaso" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Nuevo Caso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion_caso" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" name="fecha_creacion" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-control" name="estado_caso" required>
                                <option value="Abierto">Abierto</option>
                                <option value="En investigación">En investigación</option>
                                <option value="Cerrado">Cerrado</option>
                                <option value="Archivado">Archivado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Estación</label>
                            <select class="form-control" name="id_estacion" required>
                                <?php
                                $result = getDatosTabla($conn, 'estacion');
                                while($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id_estacion']}'>{$row['nombre_estacion']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" name="add_caso" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para añadir Delito -->
<div class="modal fade" id="modalAddDelito" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Añadir Nuevo Delito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Delito</label>
                        <input type="text" class="form-control" name="nombre_delito" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion_delito" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <input type="text" class="form-control" name="categoria_delito" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="add_delito" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal para añadir Víctima -->
<div class="modal fade" id="modalAddVictima" tabindex="-1" aria-labelledby="modalAddVictimaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddVictimaLabel">Añadir Nueva Víctima</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre_victima" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion_victima" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado de Seguridad</label>
                        <select class="form-control" name="estado_seguridad_victima" required>
                            <option value="Protegida">Protegida</option>
                            <option value="En riesgo">En riesgo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" name="add_victima" class="btn btn-danger">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>