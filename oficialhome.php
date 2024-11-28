<?php
session_start();
// Verificar que el usuario haya iniciado sesión como oficial
if (!isset($_SESSION['oficial']) || !is_array($_SESSION['oficial'])) {
    header("Location: index.php");
    exit();
}
// Obtener los datos del oficial desde la sesión
$oficial = $_SESSION['oficial'];

// Conexión a la base de datos

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "estacion_policia";

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultas para obtener datos de diferentes tablas
    $casos_query = "SELECT * FROM caso WHERE estado_caso != 'Cerrado' ORDER BY fecha_creacion_caso DESC LIMIT 5";
    $casos = $pdo->query($casos_query)->fetchAll(PDO::FETCH_ASSOC);

    $evidencias_query = "SELECT e.*, c.descripcion_caso 
                         FROM evidencia e 
                         JOIN caso c ON e.id_caso = c.id_caso 
                         ORDER BY e.fecha_registro_evidencia DESC LIMIT 5";
    $evidencias = $pdo->query($evidencias_query)->fetchAll(PDO::FETCH_ASSOC);

    $sospechosos_query = "SELECT * FROM sospechoso WHERE estado_arresto_sospechoso = 'No arrestado' LIMIT 5";
    $sospechosos = $pdo->query($sospechosos_query)->fetchAll(PDO::FETCH_ASSOC);

    $victimas_query = "SELECT * FROM victima WHERE estado_seguridad_victima = 'En riesgo' LIMIT 5";
    $victimas = $pdo->query($victimas_query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Oficial de Policía</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/oficialhome.css">

    <style>
        body {
            background-color: #f4f6f9;
        }
        .dashboard-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .dashboard-card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.075);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>Estación de Policía
            </a>
            <div class="navbar-text text-white ms-auto d-flex align-items-center">
            <a class="navbar-brand" href="#">Oficial</a>
   
            <span class="me-3">
                    <i class="fas fa-user me-2"></i> 
                    <?php echo htmlspecialchars($oficial['nombre_oficial'] ?? 'Oficial'); ?> 
                    <span class="badge bg-info text-dark ms-2">
                        ID: <?php echo htmlspecialchars($oficial['id_oficial'] ?? 'Desconocido'); ?>
                    </span>
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
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-chart-line me-2"></i>Panel de Control Policial
                </h1>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-folder-open text-primary"></i> Casos Activos
                        </h5>
                        <p class="card-text display-6"><?php echo count($casos); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-users text-warning"></i> Sospechosos
                        </h5>
                        <p class="card-text display-6"><?php echo count($sospechosos); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-exclamation-triangle text-danger"></i> Víctimas en Riesgo
                        </h5>
                        <p class="card-text display-6"><?php echo count($victimas); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <button class="btn btn-primary w-100 h-100" data-bs-toggle="modal" data-bs-target="#informeModal">
                    <i class="fas fa-file-medical me-2"></i>Crear Nuevo Informe
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-clipboard-list me-2"></i>Casos Recientes
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descripción</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($casos as $caso): ?>
                                    <tr>
                                        <td><?php echo $caso['id_caso']; ?></td>
                                        <td><?php echo substr($caso['descripcion_caso'], 0, 30) . '...'; ?></td>
                                        <td>
                                            <span class="badge 
                                            <?php 
                                            switch($caso['estado_caso']) {
                                                case 'Abierto': echo 'bg-success'; break;
                                                case 'En investigación': echo 'bg-warning'; break;
                                                case 'Cerrado': echo 'bg-secondary'; break;
                                                default: echo 'bg-info';
                                            }
                                            ?>">
                                                <?php echo $caso['estado_caso']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-fingerprint me-2"></i>Evidencias Registradas
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Caso</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evidencias as $evidencia): ?>
                                    <tr>
                                        <td><?php echo $evidencia['tipo_evidencia']; ?></td>
                                        <td><?php echo substr($evidencia['descripcion_caso'], 0, 20) . '...'; ?></td>
                                        <td><?php echo $evidencia['fecha_registro_evidencia']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear Informe -->
    <div class="modal fade" id="informeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-medical me-2"></i>Crear Nuevo Informe
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reportForm" action="guardar_informe.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Informe</label>
                                <select class="form-select" name="tipo_informe" required>
                                    <option value="evidencia">Evidencia</option>
                                    <option value="sospechoso">Sospechoso</option>
                                    <option value="victima">Víctima</option>
                                    <option value="caso">Caso</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Caso Relacionado</label>
                                <select class="form-select" name="id_caso" required>
                                    <?php foreach ($casos as $caso): ?>
                                    <option value="<?php echo $caso['id_caso']; ?>">
                                        Caso <?php echo $caso['id_caso']; ?> - 
                                        <?php echo substr($caso['descripcion_caso'], 0, 30); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <!-- Campos dinámicos se agregarían aquí con JavaScript -->
                        <div id="camposDinamicos"></div>
                        <div class="mb-3">
                            <label class="form-label">Descripción Detallada</label>
                            <textarea class="form-control" name="descripcion" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Guardar Informe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelector('select[name="tipo_informe"]').addEventListener('change', function() {
        const camposDinamicos = document.getElementById('camposDinamicos');
        camposDinamicos.innerHTML = ''; // Limpiar campos anteriores

        switch(this.value) {
            case 'evidencia':
                camposDinamicos.innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Evidencia</label>
                            <input type="text" class="form-control" name="tipo_evidencia" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lugar de Recolección</label>
                            <input type="text" class="form-control" name="lugar_evidencia" required>
                        </div>
                    </div>
                `;
                break;
            case 'sospechoso':
                camposDinamicos.innerHTML = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre del Sospechoso</label>
                            <input type="text" class="form-control" name="nombre_sospechoso" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" name="direccion_sospechoso" required>
                        </div>
                    </div>
                `;
                break;
            // Añadir más casos para otros tipos de informes
        }
    });
    </script>
</body>
</html>