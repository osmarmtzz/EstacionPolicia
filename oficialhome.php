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
    <!-- Contenido de la barra de navegación -->
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
                                            <a href='editar_caso.php?id={$row['id_caso']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='eliminar_caso.php?id={$row['id_caso']}' class='btn btn-sm btn-danger'>Eliminar</a>
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
                                            <a href='editar_delito.php?id={$row['id_delito']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='eliminar_delito.php?id={$row['id_delito']}' class='btn btn-sm btn-danger'>Eliminar</a>
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
                                            <a href='editar_victima.php?id={$row['id_victima']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='eliminar_victima.php?id={$row['id_victima']}' class='btn btn-sm btn-danger'>Eliminar</a>
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
                                            <a href='editar_sospechoso.php?id={$row['id_sospechoso']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='eliminar_sospechoso.php?id={$row['id_sospechoso']}' class='btn btn-sm btn-danger'>Eliminar</a>
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
                                            <a href='editar_evidencia.php?id={$row['id_evidencia']}' class='btn btn-sm btn-primary'>Editar</a>
                                            <a href='eliminar_evidencia.php?id={$row['id_evidencia']}' class='btn btn-sm btn-danger'>Eliminar</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>