<?php
include("../../conn/conn.php");


// Crear o actualizar Proyecto
if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
    $idproyecto = $_POST['idproyecto']; // Puede estar vacío si es un nuevo proyecto
    $nombre = $_POST['nombre'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $idusuario = $_POST['idusuario'];
    $presupuesto = $_POST['presupuesto'];

    if (empty($nombre) || empty($fechainicio) || empty($fechafin) || empty($descripcion) || empty($estado) || empty($idusuario)|| empty($presupuesto)) {
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }


    if (empty($idproyecto) || $idproyecto == -1) {
        // Insertar nuevo proyecto
        $sql = "INSERT INTO tproyectos (nombre, fechainicio, fechafin, descripcion, estado, idusuario, presupuesto) 
                VALUES ('$nombre', '$fechainicio', '$fechafin', '$descripcion', '$estado', '$idusuario', '$presupuesto')";
    } else {
        // Actualizar proyecto existente
        $sql = "UPDATE tproyectos SET 
                    nombre='$nombre', 
                    fechainicio='$fechainicio', 
                    fechafin='$fechafin', 
                    descripcion='$descripcion', 
                    estado='$estado', 
                    idusuario='$idusuario', 
                    presupuesto='$presupuesto' 
                WHERE idproyecto=$idproyecto";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}



// Eliminar Proyecto
if (isset($_POST['action']) && $_POST['action'] == 'eliminar') {
    $idproyecto = $_POST['idproyecto'];
    $sql = "DELETE FROM tproyectos WHERE idproyecto = $idproyecto";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(
            [
                "success" => false,
                 "error" => mysqli_error($conn)
                 ]
        
        );
    }
    exit();
}

// Obtener Proyecto para Editar
$proyecto = null;
if ($_GET['action'] == 'editar') {
    $idproyecto = $_GET['idProyecto'];
    $resultado = mysqli_query($conn, "SELECT * FROM tproyectos WHERE idproyecto = $idproyecto");
    $proyecto = mysqli_fetch_assoc($resultado);
    echo json_encode($proyecto);
    exit();
}

if ($_GET['action'] == 'listarTareas') {
    $idproyecto = $_GET['idProyecto'];

    $resultado = mysqli_query($conn, "SELECT * FROM ttareas WHERE idproyecto = " . intval($idproyecto));
    $tareas = [];

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $tareas[] = $fila;
    }
    echo json_encode($tareas);
    exit();
}


if ($_GET['action'] == 'cargarUsuarios') {
   // Obtener lista de usuarios para el combo
    $resultado = mysqli_query($conn, "SELECT id, nombre,apellidos FROM tusuarios");

    $usuarios = [];

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $usuarios[] = $fila;
    }
    echo json_encode($usuarios);
    exit();
}


if ($_GET['action'] == 'cargarProyectos') {
    // Obtener Todos los Proyectos
     $resultado = mysqli_query($conn, "SELECT * FROM tproyectos");
  
      $proyectos = [];
  
      while ($fila = mysqli_fetch_assoc($resultado)) {
          $proyectos[] = $fila;
      }
      echo json_encode($proyectos);
      exit();
  }

// Guardar y editar tareas 
if (isset($_POST['action']) && $_POST['action'] == 'guardarTarea') {
    $idtarea = $_POST['idtarea'];
    $idproyecto = $_POST['idproyecto'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $idusuario = $_POST['idusuario'];
    $estadotarea = $_POST['estadotarea'];

    if (empty($idproyecto) || empty($titulo) || empty($descripcion) || empty($fechainicio) || empty($fechafin) || empty($idusuario) || empty($estadotarea)) {
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }

    if (!$idtarea) {
        $sql = "INSERT INTO ttareas (idproyecto, titulo, fechainicio, fechafin, descripcion, idusuario, estadotarea) 
                VALUES ('$idproyecto', '$titulo', '$fechainicio', '$fechafin', '$descripcion', '$idusuario', '$estadotarea')";
    } else {
        $sql = "UPDATE ttareas SET 
                    titulo='$titulo', 
                    fechainicio='$fechainicio', 
                    fechafin='$fechafin', 
                    descripcion='$descripcion', 
                    estadotarea='$estadotarea', 
                    idusuario='$idusuario'
                WHERE idtarea=$idtarea";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if ($_GET['action'] == 'obtenerTarea') {
    $idtarea = intval($_GET['idtarea']);

    $resultado = mysqli_query($conn, "SELECT * FROM ttareas WHERE idtarea = $idtarea");

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $tarea = mysqli_fetch_assoc($resultado);
        echo json_encode($tarea);
    } else {
        echo json_encode(["error" => "Tarea no encontrada"]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'eliminarTarea') {
    $idtarea = intval($_POST['idtarea']);

    $sql = "DELETE FROM ttareas WHERE idtarea = $idtarea";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if ($_GET['action'] == 'listarTodoMaterialesDisponibles') {
    $res = mysqli_query($conn, "SELECT idmaterial, nombre, preciou FROM tmateriales WHERE estado = 1");
    $materiales = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $materiales[] = $row;
    }
    echo json_encode($materiales);
    exit();
}

if ($_POST['action'] == 'asignarMaterialProyecto') {
    $idproyecto = $_POST['idproyecto'];
    $idmaterial = $_POST['idmaterial'];
    $cantidad = $_POST['cantidad'];

    // Obtener el precio unitario del material
    $resultado = mysqli_query($conn, "SELECT preciou FROM tmateriales WHERE idmaterial = $idmaterial AND estado = 1");
    $material = mysqli_fetch_assoc($resultado);

    if (!$material) {
        echo json_encode(["success" => false, "error" => "El material no existe o no está aprobado."]);
        exit();
    }

    $preciou = $material['preciou'];
    $preciott = $preciou * $cantidad;

    // Obtener el presupuesto total del proyecto
    $resProyecto = mysqli_query($conn, "SELECT presupuesto FROM tproyectos WHERE idproyecto = $idproyecto");
    $proyecto = mysqli_fetch_assoc($resProyecto);
    $presupuestoTotal = $proyecto['presupuesto'];

    // Calcular el total de materiales ya asignados
    $resGastado = mysqli_query($conn, "SELECT SUM(preciott) as totalGastado FROM tmaterialesproyecto WHERE idproyecto = $idproyecto");
    $gastado = mysqli_fetch_assoc($resGastado)['totalGastado'] ?? 0;

    // Calcular presupuesto restante
    $presupuestoRestante = $presupuestoTotal - $gastado;

    // Verificar si alcanza para este nuevo material
    if ($presupuestoRestante < $preciott) {
        echo json_encode([
            "success" => false,
            "error" => "El presupuesto restante del proyecto no es suficiente para este material. Quedan ₡" . number_format($presupuestoRestante, 2)
        ]);
        exit();
    }

    // Insertar el material en la tabla tmaterialesproyecto
    $sql = "INSERT INTO tmaterialesproyecto (idproyecto, idmaterial, cantidad, preciou, preciott) 
            VALUES ('$idproyecto', '$idmaterial', '$cantidad', '$preciou', '$preciott')";

    if (mysqli_query($conn, $sql)) {
        // Actualizar la cantidad en tmateriales
        mysqli_query($conn, "UPDATE tmateriales SET cantidad = cantidad - $cantidad WHERE idmaterial = $idmaterial");

        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }

    exit();
}


if ($_GET['action'] == 'listarMaterialesProyecto') {
    $idProyecto = $_GET['idProyecto'];

    $res = mysqli_query($conn, "
        SELECT mp.idmaterial, m.nombre, m.descrip, mp.cantidad, mp.preciou, mp.preciott
        FROM tmaterialesproyecto mp
        JOIN tmateriales m ON mp.idmaterial = m.idmaterial
        WHERE mp.idproyecto = $idProyecto
    ");

    $materiales = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $materiales[] = $row;
    }

    echo json_encode($materiales);
    exit();
}



if ($_POST['action'] == 'guardarGasto') {
    $idproyecto = $_POST['idproyecto']; 
    $nombreGasto = $_POST['nombreGasto'];
    $fechaGasto = $_POST['fechaGasto'];
    $descripcion = $_POST['descripcion'];
    $monto = $_POST['monto'];
    $comentario = $_POST['comentario'];
    $estado = 0;

    try {
        $sql = "INSERT INTO trefinanciero (NombreGasto, FechaGasto, Descripcion, monto, estado, comentario)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdis", $nombreGasto, $fechaGasto, $descripcion, $monto, $estado, $comentario);
        $stmt->execute();

        $idrefinanciero = $conn->insert_id;

        $sql2 = "INSERT INTO trefinancieroxproy (idproyecto, idrefinanciero) VALUES (?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ii", $idproyecto, $idrefinanciero);
        $stmt2->execute();
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => "ERROR"]);
    }
    exit();
}



if ($_GET['action'] == 'listarGastos') {
    error_reporting(E_ALL); ini_set("display_errors", 1);
    $idproyecto = $_GET['idProyecto'];
    
    
    $resultado = mysqli_query($conn, "SELECT f.* FROM trefinancieroxproy fp JOIN trefinanciero f ON f.idrefinanciero = fp.idrefinanciero WHERE fp.idproyecto = " . intval($idproyecto));
    $gastos = [];

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $gastos[] = $fila;
    }
    echo json_encode($gastos);
    exit();
}

?>



<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="text-right">
        <button class="btn btn-sm btn-secondary mb-3" onclick="abrirModalProyecto(-1)">
            <i class="fa fa-plus"></i> Agregar proyecto</button>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Proyectos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaProyectos" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProyectos">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProyecto" tabindex="-1" role="dialog" aria-labelledby="modalProyectoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" class="modal-content" action="proyectos.php">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProyectoLabel">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow mb-4">

                    <div class="card-body">

                        <input type="hidden" id="idproyecto" name="idproyecto"
                            value="<?= $proyecto['idproyecto'] ?? '' ?>">
                        <div class="grid-container">
                            <div>
                                <label>Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required
                                    value="<?= $proyecto['nombre'] ?? '' ?>">
                            </div>
                            <div>

                                <label>Fecha Inicio:</label>
                                <input type="date" class="form-control" id="fechainicio" name="fechainicio" required
                                    value="<?= $proyecto['fechainicio'] ?? '' ?>">
                            </div>
                            <div>
                                <label>Fecha Fin:</label>
                                <input type="datetime-local" class="form-control" id="fechafin" name="fechafin"
                                    value="<?= $proyecto['fechafin'] ?? '' ?>">
                            </div>
                            <div>
                                <label>Descripción:</label>
                                <textarea class="form-control" id="descripcion"
                                    name="descripcion"> <?= $proyecto['descripcion'] ?? '' ?></textarea>
                            </div>
                            <div>
                                <label>Presupuesto:</label>
                                <input type="text" class="form-control" id="presupuesto" name="presupuesto" required
                                    value="<?= $proyecto['presupuesto'] ?? '' ?>">
                            </div>
                            <div>
                                <label>Estado:</label>
                                <select class="form-control" id="estado" name="estado">
                                    <option value="pendiente"
                                        <?= isset($proyecto['estado']) && $proyecto['estado'] == 'pendiente' ? 'selected' : '' ?>>
                                        Pendiente</option>
                                    <option value="en proceso"
                                        <?= isset($proyecto['estado']) && $proyecto['estado'] == 'en proceso' ? 'selected' : '' ?>>
                                        En Proceso</option>
                                    <option value="finalizado"
                                        <?= isset($proyecto['estado']) && $proyecto['estado'] == 'finalizado' ? 'selected' : '' ?>>
                                        Finalizado</option>
                                </select>
                            </div>
                            <div>
                                <label>Usuario:</label>
                                <select class="form-control" id="idusuario" name="idusuario" required>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success" type="button" id="btn-guardarProyecto"
                            onclick="guardarProyecto()">
                            <i class="fas fa-check"></i> Guardar
                        </button>
                        </button>
                    </div>

                </div>
            </div>

        </form>
    </div>
</div>

<div class="modal fade" id="modalTareas" tabindex="-1" role="dialog" aria-labelledby="modalTareasLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTareasLabel">Tareas del Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="card shadow mb-4">
                    <div class="card-body">

                        <!-- Lista de tareas existentes -->
                        <div id="tareasLista" class="mb-3">
                            <!-- Aquí se inyectarán dinámicamente las tareas vía AJAX -->
                        </div>

                        <input type="hidden" id="idtarea" name="idtarea">
                        <input type="hidden" id="idproyectoTarea" name="idproyectoTarea">

                        <div class="grid-container">
                            <div>
                                <label for="tituloTarea">Título:</label>
                                <input type="text" class="form-control" id="tituloTarea" name="titulo" required>
                            </div>

                            <div>
                                <label for="descripcionTarea">Descripción:</label>
                                <textarea class="form-control" id="descripcionTarea" name="descripcion"
                                    rows="2"></textarea>
                            </div>

                            <div>
                                <label for="fechaInicioTarea">Fecha de Inicio:</label>
                                <input type="datetime-local" class="form-control" id="fechaInicioTarea"
                                    name="fechainicio">
                            </div>

                            <div>
                                <label for="fechaFinTarea">Fecha de Fin:</label>
                                <input type="datetime-local" class="form-control" id="fechaFinTarea" name="fechafin">
                            </div>

                            <div>
                                <label for="idusuario1">Asignar a Usuario:</label>
                                <select class="form-control" id="idusuario1" name="idusuario1" required>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>

                            <div>
                                <label for="estadoTarea">Estado:</label>
                                <select class="form-control" id="estadoTarea" name="estadotarea">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en proceso">En proceso</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-success" type="button" id="btn-guardarTarea" onclick="guardarTareas()">
                            <i class="fas fa-check"></i> Guardar Tarea
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalAsignarMaterial" tabindex="-1" role="dialog" aria-labelledby="modalLabelMaterial"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" class="modal-content" action="#">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelMaterial">Asignar Material al Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="materialesLista">
                    <!-- Aquí se inyectarán dinámicamente los materiales del proyecto vía AJAX -->
                </div>
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <input type="hidden" id="idproyecto_asignacion" name="idproyecto">
                        <div class="grid-container">
                            <div>
                                <label for="idmaterial">Material:</label>
                                <select class="form-control" id="idmaterial" name="idmaterial" required>
                                    <!-- Se cargarán los materiales dinámicamente -->
                                </select>
                            </div>

                            <div>
                                <label for="cantidad">Cantidad:</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="asignarMaterialAProyectoDesdeModal()">
                    <i class="fas fa-check"></i> Asignar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para agregar gasto -->
<div class="modal fade" id="agregarGastoModal" tabindex="-1" role="dialog" aria-labelledby="agregarGastoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarGastoModalLabel">Agregar Gasto al Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div id="gastosLista" class="mb-3">
                            <!-- Aquí se inyectarán dinámicamente los gastos vía AJAX -->
                        </div>
                        <div class="grid-container">

                            <input type="hidden" id="idproyectogastorefinanciero" name="idproyectogastorefinanciero">
                            <input type="hidden" id="idproyectogasto" name="idproyectogasto">
                            <!-- Nombre del Gasto -->
                            <div>
                                <label for="nombreGasto">Nombre del Gasto:</label>
                                <input type="text" class="form-control" id="nombreGasto" name="NombreGasto" required>
                            </div>

                            <!-- Fecha del Gasto -->
                            <div>
                                <label for="fechaGasto">Fecha del Gasto:</label>
                                <input type="datetime-local" class="form-control" id="fechaGasto" name="FechaGasto"
                                    required>
                            </div>

                            <!-- Descripción del Gasto -->
                            <div>
                                <label for="descripcion">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="Descripcion" rows="3"
                                    required></textarea>
                            </div>

                            <!-- Monto del Gasto -->
                            <div>
                                <label for="monto">Monto:</label>
                                <input type="number" class="form-control" id="monto" name="monto" step="0.01" min="0"
                                    required>
                            </div>


                            <!-- Comentario -->
                            <div>
                                <label for="comentario">Comentario:</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btn-guardarGastos" onclick="guardarGastos()">
                    <i class="fas fa-check"></i> Guardar Gasto
                </button>
            </div>
        </form>
    </div>
</div>


<style>
.grid-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.grid-table {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    text-align: center;
}

.grid-header {
    font-weight: bold;
    background-color: #ddd;
    padding: 5px;
}
</style>

<script>
// Para poner la tabla en español 
$(document).ready(function() {
    $("#tablaProyectos").DataTable({
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
        },
        paging: true, // Habilita la paginación
        searching: true, // Habilita el cuadro de búsqueda
        ordering: true, // Permite ordenar columnas
        info: true, // Muestra información de la tabla
        lengthMenu: [
            [5, 10, 25, 50],
            [5, 10, 25, 50]
        ], // Opciones de filas por página.
        pageLength: 10, // Cantidad de filas por defecto
        responsive: true // Hace la tabla responsive
    });
    $("#tablaProyectos").addClass("text-right");
});

function limpiarModal() {

    $("#modalProyecto input[name='idproyecto']").val(-1);
    $("#modalProyecto input[name='nombre']").val("");
    $("#modalProyecto input[name='fechainicio']").val("");
    $("#modalProyecto input[name='fechafin']").val("");
    $("#modalProyecto textarea[name='descripcion']").val("");
    $("#modalProyecto select[name='estado']").val("");
    $("#modalProyecto select[name='idusuario']").val("");
    $("#modalProyecto select[name='presupuesto']").val("");

    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Crear');
    // Cambia el título del modal
    $("#modalProyectoLabel").text("Nuevo Proyecto");
}

function cargarProyectos() {
    $.get("proyectos.php", {
            action: "cargarProyectos"
        })
        .done(function(data) {
            let proyectos = JSON.parse(data);
            let html = '';

            if (proyectos.length === 0) {
                html = '<p class="text-muted">No hay proyectos.</p>';
            } else {
                proyectos.forEach(function(proyecto) {
                    html += `
                        <tr>
                            <td>${proyecto.nombre}</td>
                            <td>${proyecto.fechainicio}</td>
                            <td>${proyecto.fechafin}</td>
                            <td>${proyecto.estado}</td>
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-success"
                                        onclick="abrirModalAgregarTareas(${proyecto.idproyecto})">
                                        <i class="fa fa-tasks"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirModalProyecto(${proyecto.idproyecto})">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                     <button class="btn btn-sm btn-warning"
                                        onclick="abrirModalAsignarMaterial(${proyecto.idproyecto})">
                                        <i class="fa fa-cubes"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirModalAsignarGasto(${proyecto.idproyecto})">
                                        <i class="fa fa-credit-card"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="if(confirm('¿Eliminar proyecto?')) eliminarProyecto(${proyecto.idproyecto})">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }

            $('#tbodyProyectos').html(html);
        });

}


function abrirModalProyecto(idProyecto) {

    limpiarModal();

    if (idProyecto != -1) {
        $.get("proyectos.php", {
                action: "editar",
                idProyecto: idProyecto
            })
            .done(function(data) {
                let proyecto = JSON.parse(data);

                // Llenar los campos del formulario en el modal
                $("#modalProyecto input[name='idproyecto']").val(proyecto.idproyecto);
                $("#modalProyecto input[name='nombre']").val(proyecto.nombre);
                $("#modalProyecto input[name='fechainicio']").val(proyecto.fechainicio);
                $("#modalProyecto input[name='fechafin']").val(proyecto.fechafin);
                $("#modalProyecto textarea[name='descripcion']").val(proyecto.descripcion);
                $("#modalProyecto select[name='estado']").val(proyecto.estado);
                $("#modalProyecto select[name='idusuario']").val(proyecto.idusuario);
                $("#modalProyecto input[name='presupuesto']").val(proyecto.presupuesto);

                // Cambiar el título del modal
                $("#modalProyectoLabel").text("Editar Proyecto");

                $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

            });
    }
    $("#modalProyecto").modal('show');

}

function guardarProyecto() {
    var id = $('#idproyecto').val();
    var nombre = $('#nombre').val();
    var fechainicio = $('#fechainicio').val();
    var fechafin = $('#fechafin').val();
    var descripcion = $('#descripcion').val();
    var estado = $('#estado').val();
    var idusuario = $('#idusuario').val();
    var presupuesto = $('#presupuesto').val();

    $.post("proyectos.php", {
            action: "guardar", // Usamos el mismo action
            idproyecto: id,
            nombre: nombre,
            fechainicio: fechainicio,
            fechafin: fechafin,
            descripcion: descripcion,
            estado: estado,
            idusuario: idusuario,
            presupuesto: presupuesto
        })
        .done(function(response) {
            let data = JSON.parse(response);
            if (data.success) {
                $('#modalProyecto').modal('hide');
                cargarProyectos();
            } else {
                alert("Error: " + data.error);
            }
        });
}

function eliminarProyecto(idproyecto) {

    $.post("proyectos.php", {
            action: "eliminar",
            idproyecto: idproyecto
        })
        .done(function(response) {
            let data = JSON.parse(response);
            if (data.success) {
                alert("Se eliminó el proyecto correctamente");
                cargarProyectos();
            } else {
                alert("Error: " + data.error);
            }
        });
}

function limpiarFormularioTarea() {
    $("#idtarea").val('');
    $("#tituloTarea").val('');
    $("#descripcionTarea").val('');
    $("#fechaInicioTarea").val('');
    $("#fechaFinTarea").val('');
    $("#idusuario1").val('');
    $("#estadoTarea").val('pendiente');
    $("#btn-guardarTarea").html('<i class="fas fa-save"></i> Guardar Tarea');
}


function abrirModalAgregarTareas(idProyecto) {
    $('#idproyectoTarea').val(idProyecto);

    // Usamos la nueva función para cargar las tareas
    cargarTareas(idProyecto);

    // Limpiar los campos del formulario de tarea
    limpiarFormularioTarea();

    // Mostrar el modal
    $("#modalTareas").modal('show');
}


function cargarUsuario() {
    $.get("proyectos.php", {
            action: "cargarUsuarios"
        })
        .done(function(data) {
            let usuarios = JSON.parse(data);
            let html = '';

            if (usuarios.length === 0) {
                html = '<p class="text-muted">No hay usuarios.</p>';
            } else {
                usuarios.forEach(function(usuario) {
                    html += `
                          <option value="${usuario.id}">
                                ${usuario.nombre} ${usuario.apellidos} 
                            </option>
                    `;
                });
            }

            $('#idusuario').html(html);
            $('#idusuario1').html(html);
        });
}

function cargarTareas(idProyecto) {
    $.get("proyectos.php", {
        action: "listarTareas",
        idProyecto: idProyecto
    }).done(function(data) {
        let tareas = JSON.parse(data);
        let html = '';

        if (tareas.length === 0) {
            html = '<p class="text-muted">No hay tareas registradas para este proyecto.</p>';
        } else {
            tareas.forEach(function(tarea) {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">${tarea.titulo}</h5>
                            <p class="card-text">${tarea.descripcion}</p>
                            <p class="card-text">
                                <strong>Inicio:</strong> ${tarea.fechainicio}<br>
                                <strong>Fin:</strong> ${tarea.fechafin}<br>
                                <strong>Estado Tarea:</strong> ${tarea.estadotarea}
                            </p>
                            <button class="btn btn-sm btn-primary" onclick="editarTarea(${tarea.idtarea})">Editar</button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarTarea(${tarea.idtarea})">Eliminar</button>
                        </div>
                    </div>
                `;
            });
        }

        $('#tareasLista').html(html);
    });
}

function guardarTareas() {
    var id = $('#idproyectoTarea').val();
    var idtarea = $('#idtarea').val();
    var titulo = $('#tituloTarea').val();
    var descripcion = $('#descripcionTarea').val();
    var fechainicio = $('#fechaInicioTarea').val();
    var fechafin = $('#fechaFinTarea').val();
    var idusuario = $('#idusuario1').val();
    var estadotarea = $('#estadoTarea').val();

    $.post("proyectos.php", {
        action: "guardarTarea",
        idproyecto: id,
        idtarea: idtarea,
        titulo: titulo,
        descripcion: descripcion,
        fechainicio: fechainicio,
        fechafin: fechafin,
        idusuario: idusuario,
        estadotarea: estadotarea
    }, function(response) {
        let data = JSON.parse(response);
        if (data.success) {
            $('#modalTareas').modal('hide');
        } else {
            alert("Error: " + data.error);
        }
    });
}


function editarTarea(idtarea) {
    $.get("proyectos.php", {
        action: "obtenerTarea",
        idtarea: idtarea
    }).done(function(data) {
        let tarea = JSON.parse(data);

        // Llenar el formulario con los datos de la tarea
        $("#idtarea").val(tarea.idtarea);
        $("#idproyectoTarea").val(tarea.idproyecto); // por si lo necesitás para guardar
        $("#tituloTarea").val(tarea.titulo);
        $("#descripcionTarea").val(tarea.descripcion);
        $("#fechaInicioTarea").val(tarea.fechainicio);
        $("#fechaFinTarea").val(tarea.fechafin);
        $("#idusuario1").val(tarea.idusuario);
        $("#estadoTarea").val(tarea.estadotarea);

        // Cambiar el texto del botón a "Actualizar Tarea"
        $("#btn-guardarTarea").html('<i class="fas fa-check"></i> Actualizar Tarea');
    });
}

function eliminarTarea(idtarea) {
    if (confirm("¿Estás seguro de que deseas eliminar esta tarea?")) {
        $.post("proyectos.php", {
            action: "eliminarTarea",
            idtarea: idtarea
        }).done(function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                // Recargar tareas al eliminar
                let idProyecto = $("#idproyectoTarea").val();
                cargarTareas(idProyecto);
            } else {
                alert("Error al eliminar la tarea: " + res.error);
            }
        });
    }
}

function abrirModalAsignarMaterial(idProyecto) {
    $('#idproyecto_asignacion').val(idProyecto);
    cargarMateriales(idProyecto);
    $.get('proyectos.php', {
        action: 'listarTodoMaterialesDisponibles'
    }, function(data) {
        const materiales = JSON.parse(data);
        let options = '<option value="">Seleccione un material</option>';

        materiales.forEach(mat => {
            options += `<option value="${mat.idmaterial}">${mat.nombre} - ₡${mat.preciou}</option>`;
        });

        $('#idmaterial').html(options);
        $('#cantidad').val(1);
        $('#modalAsignarMaterial').modal('show');
    });
}

function asignarMaterialAProyectoDesdeModal() {
    const idproyecto = $('#idproyecto_asignacion').val();
    const idmaterial = $('#idmaterial').val();
    const cantidad = $('#cantidad').val();

    if (!idmaterial || !cantidad || cantidad <= 0) {
        alert("Debe seleccionar un material y una cantidad válida.");
        return;
    }

    $.post("proyectos.php", {
        action: "asignarMaterialProyecto",
        idproyecto: idproyecto,
        idmaterial: idmaterial,
        cantidad: cantidad
    }).done(function(response) {
        const data = JSON.parse(response);
        if (data.success) {
            $('#modalAsignarMaterial').modal('hide');
            alert("Material asignado correctamente.");
            location.reload();
        } else {
            alert("Error: " + data.error);
        }
    });
}

function cargarMateriales(idProyecto) {
    $.get("proyectos.php", {
        action: "listarMaterialesProyecto",
        idProyecto: idProyecto
    }).done(function(data) {
        let materiales = JSON.parse(data);
        let html = '';

        if (materiales.length === 0) {
            html = '<p class="text-muted">No hay materiales asignados a este proyecto.</p>';
        } else {
            materiales.forEach(function(m) {
                html += `
                    <div class="card mb-2">
                        <div class="card-body">
                            <h6 class="card-title">${m.nombre}</h6>
                            <p class="card-text">
                                <strong>Descripción:</strong> ${m.descrip}<br>
                                <strong>Cantidad:</strong> ${m.cantidad}<br>
                                <strong>Precio unitario:</strong> ₡${m.preciou}<br>
                                <strong>Total:</strong> ₡${m.preciott}
                            </p>
                        </div>
                    </div>
                `;
            });
        }

        $('#materialesLista').html(html);
    });
}

function limpiarModalGasto() {
    $('#idproyectogastorefinanciero').val(-1);
    $('#idproyectogasto').val('');
    $('#nombreGasto').val('');
    $('#monto').val('');
    $('#descripcion').val('');
    $('#fechaGasto').val('');
    $('#observaciones').val('');
    $('#estado').val('');
    $('#comentario').val('');
}

function abrirModalAsignarGasto(idProyecto) {
    limpiarModalGasto();
    alert(idProyecto);
    $('#idproyectogastorefinanciero').val(-1);
    $('#idproyectogasto').val(idProyecto);
    cargarGastos(idProyecto);
    $('#agregarGastoModal').modal('show');
}



function cargarGastos(idProyecto) {
    $.get("proyectos.php", {
        action: "listarGastos",
        idProyecto: idProyecto
    }).done(function(data) {
        let gastos = JSON.parse(data);
        let html = '';

        if (gastos.length === 0) {
            html = '<p class="text-muted">No hay gastos registrados para este proyecto.</p>';
        } else {
            gastos.forEach(function(gasto) {
                let estadoGasto = '';
                let claseEstado = '';

                switch (parseInt(gasto.estado)) {
                    case 0:
                        estadoGasto = 'Pendiente';
                        claseEstado = 'bg-warning text-dark';
                        break;
                    case 1:
                        estadoGasto = 'Aprobado';
                        claseEstado = 'bg-success';
                        break;
                    case 2:
                        estadoGasto = 'Denegado';
                        claseEstado = 'bg-danger';
                        break;
                }

                html += `
        <div class="card mb-2 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">${gasto.NombreGasto}</h5>
                <p class="card-text">${gasto.FechaGasto}</p>
                <p class="card-text">
                    <strong>Descripción:</strong> ${gasto.Descripcion}<br>
                    <strong>Monto:</strong> ₡${parseFloat(gasto.monto).toFixed(2)}<br>
                    <strong>Estado:</strong> <span class="badge ${claseEstado}">${estadoGasto}</span><br>
                    <strong>Comentario:</strong> ${gasto.comentario}
                </p>
            </div>
        </div>
    `;
            });

        }

        $('#gastosLista').html(html);
    });
}

function obtenerGasto(idProyecto) {
    $.get("proyectos.php", {
        action: "obtenerGasto",
        idProyecto: idProyecto
    }).done(function(data) {
        let gasto = JSON.parse(data);

        $("#idproyectogastorefinanciero").val(gasto.idrefinanciero);
        $("#idproyectogasto").val(gasto.idproyecto);
        $("#nombreGasto").val(gasto.NombreGasto);
        $("#fechaGasto").val(gasto.FechaGasto);
        $("#descripcion").val(gasto.Descripcion);
        $("#monto").val(gasto.monto);
        $("#comentario").val(gasto.comentario);


        $("#btn-guardarGastos").html('<i class="fas fa-check"></i> Actualizar Gasto');
    });
}

function guardarGastos() {
    var idproyecto = $('#idproyectogasto').val();
    var nombreGasto = $('#nombreGasto').val();
    var fechaGasto = $('#fechaGasto').val();
    var descripcion = $('#descripcion').val();
    var monto = $('#monto').val();
    var estado = 0;
    var comentario = $('#comentario').val();

    $.post("proyectos.php", {
        action: "guardarGasto",
        idproyecto: idproyecto,
        nombreGasto: nombreGasto,
        fechaGasto: fechaGasto,
        descripcion: descripcion,
        monto: monto,
        estado: estado,
        comentario: comentario
    }, function(response) {
        console.log("Respuesta del servidor:", response);
        try {
            let data = JSON.parse(response);
            if (data.success) {
                $('#agregarGastoModal').modal('hide');
                alert("Gasto guardado correctamente");
                cargarGastos(idproyecto);
            } else {
                alert("Error: " + data.error);
            }
        } catch (e) {
            alert("Respuesta inesperada: " + e);
        }
    });
}


cargarProyectos();
cargarUsuario();
</script>


<?php include("../../template/bottom.php"); ?>