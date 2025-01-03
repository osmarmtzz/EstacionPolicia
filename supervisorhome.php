<?php
session_start();

// Verificar si la sesión del supervisor está activa y es válida
if (!isset($_SESSION['supervisor']) || !is_array($_SESSION['supervisor'])) {
    header("Location: index.php");
    exit();
}

// Obtener los datos del supervisor desde la sesión
$supervisor = $_SESSION['supervisor'];
$id_supervisor = $supervisor['id_supervisor'];  // Acceder al ID desde el arreglo

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estacion_policia";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Recuperar datos del supervisor (aunque ya están en la sesión, puedes usarlos para validar)
$sql_supervisor = "SELECT * FROM supervisor WHERE id_supervisor = ?";
$stmt = $conn->prepare($sql_supervisor);
$stmt->bind_param("i", $id_supervisor);
$stmt->execute();
$result_supervisor = $stmt->get_result();

if ($result_supervisor->num_rows > 0) {
    $supervisor = $result_supervisor->fetch_assoc();
} else {
    echo "Supervisor no encontrado.";
    exit();
}

// Función para obtener datos de cualquier tabla
function getDatosTabla($conn, $tabla) {
    $sql = "SELECT * FROM $tabla";
    return $conn->query($sql);
}

// Procesar formularios de inserción (como en tu código original)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_oficial'])) {
        $nombre = $_POST['nombre_oficial'];
        $rango = $_POST['rango_oficial'];
        $años = $_POST['años_servicio'];
        $estacion = $_POST['id_estacion'];
        
        $sql = "INSERT INTO oficial (nombre_oficial, rango_oficial, años_servicio_oficial, id_estacion) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $rango, $años, $estacion);
        $stmt->execute();
    }
    // Insertar nuevo caso
    elseif (isset($_POST['add_caso'])) {
        $descripcion = $_POST['descripcion_caso'];
        $fecha = $_POST['fecha_creacion'];
        $estado = $_POST['estado_caso'];
        $estacion = $_POST['id_estacion'];
        
        $sql = "INSERT INTO caso (descripcion_caso, fecha_creacion_caso, estado_caso, id_estacion) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $descripcion, $fecha, $estado, $estacion);
        $stmt->execute();
    }
    // Otras inserciones como en tu código original...
    header("Location: supervisorhome.php");
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
        <a class="navbar-brand" href="#">Supervisor</a>

            <span class="me-3">
                <i class="fas fa-user me-2"></i>
                <?php echo htmlspecialchars($supervisor['nombre_supervisor']); ?>
                <span class="badge bg-light text-dark ms-2">ID: <?php echo htmlspecialchars($supervisor['id_supervisor']); ?></span>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white">Oficiales</h5>
            <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal" data-bs-target="#modalAddOficial">
                <i class="fas fa-plus"></i> Añadir Oficial
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr class="text-uppercase">
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
                                    <a href='oficial/editar_oficial.php?id={$row['id_oficial']}' class='btn btn-sm btn-primary'>
                                        <i class='fas fa-edit'></i> Editar
                                    </a>
                                    <a href='oficial/eliminar_oficial.php?id={$row['id_oficial']}' class='btn btn-sm btn-danger'>
                                        <i class='fas fa-trash'></i> Eliminar
                                    </a>
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
                                    <a href='casos/editar_casos.php?id={$row['id_caso']}' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i>Editar</a>
                                    <a href='casos/eliminar_casos.php?id={$row['id_caso']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i>Eliminar</a>
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
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
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
                                    <a href='delitos/editar_delitos.php?id={$row['id_delito']}' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i>Editar</a>
                                    <a href='delitos/eliminar_delitos.php?id={$row['id_delito']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i>Eliminar</a>
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
        <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
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
                                    <a href='victimas/editar_victimas.php?id={$row['id_victima']}' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i>Editar</a>
                                    <a href='victimas/eliminar_victimas.php?id={$row['id_victima']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i>Eliminar</a>
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
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Sospechosos</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddSospechoso">
                <i class="fas fa-plus"></i> Añadir Sospechoso
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
                                    <a href='sospechosos/editar_sospechoso.php?id={$row['id_sospechoso']}' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i>Editar</a>
                                    <a href='sospechosos/eliminar_sospechoso.php?id={$row['id_sospechoso']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i>Eliminar</a>
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
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Evidencias</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddEvidencia">
                <i class="fas fa-plus"></i> Añadir Evidencia
            </button>
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
                                    <a href='evidencia/editar_evidencia.php?id={$row['id_evidencia']}' class='btn btn-sm btn-primary'><i class='fas fa-edit'></i>Editar</a>
                                    <a href='evidencia/eliminar_evidencia.php?id={$row['id_evidencia']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i>Eliminar</a>
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