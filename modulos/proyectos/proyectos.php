<?php
include("../../conn/conn.php");

// Obtener lista de usuarios para el combo
$usuarios = mysqli_query($conn, "SELECT id, nombre,apellidos FROM tusuarios");

// Crear o actualizar Proyecto
if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
    $idproyecto = $_POST['idproyecto']; // Puede estar vacío si es un nuevo proyecto
    $nombre = $_POST['nombre'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $idusuario = $_POST['idusuario'];

    if (empty($nombre) || empty($fechainicio) || empty($fechafin) || empty($descripcion) || empty($estado) || empty($idusuario)) {
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }


    if (empty($idproyecto) || $idproyecto == -1) {
        // Insertar nuevo proyecto
        $sql = "INSERT INTO tproyectos (nombre, fechainicio, fechafin, descripcion, estado, idusuario) 
                VALUES ('$nombre', '$fechainicio', '$fechafin', '$descripcion', '$estado', '$idusuario')";
    } else {
        // Actualizar proyecto existente
        $sql = "UPDATE tproyectos SET 
                    nombre='$nombre', 
                    fechainicio='$fechainicio', 
                    fechafin='$fechafin', 
                    descripcion='$descripcion', 
                    estado='$estado', 
                    idusuario='$idusuario' 
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
$idproyecto = $_GET['idproyecto'];
    $resultado = $conexion->query("SELECT * FROM tareas WHERE idproyecto = $idproyecto");

    $tareas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $tareas[] = $fila;
    }
    echo json_encode($tareas);
    exit();
}

// Obtener Todos los Proyectos
$resultado = mysqli_query($conn, "SELECT * FROM tproyectos");
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
                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= $fila['nombre'] ?></td>
                            <td><?= $fila['fechainicio'] ?></td>
                            <td><?= $fila['fechafin'] ?></td>
                            <td><?= $fila['estado'] ?></td>
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-success"
                                        onclick="cargarTareas(<?= $fila['idproyecto'] ?>)">
                                        <i class="fa fa-tasks"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirModalProyecto(<?= $fila['idproyecto'] ?>)">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="if(confirm('¿Eliminar proyecto?')) eliminarProyecto(<?= $fila['idproyecto'] ?>)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
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
                        <form method="POST">
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
                                        <?php while ($user = mysqli_fetch_assoc($usuarios)): ?>
                                        <option value="<?= $user['id'] ?>"
                                            <?= isset($proyecto['idusuario']) && $proyecto['idusuario'] == $user['id'] ? 'selected' : '' ?>>
                                            <?= $user['nombre'] ?> <?= $user['apellidos'] ?>
                                        </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>

                        </form>
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

<div class="modal fade" id="modalTareas" tabindex="-1" role="dialog" aria-labelledby="modalTareasLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tareas del Proyecto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Lista de tareas existentes -->
                <div id="tareasLista">
                    <!-- Aquí se inyectarán dinámicamente las tareas vía AJAX -->
                </div>
                <hr>
                <!-- Formulario para agregar o editar una tarea -->
                <form id="formTarea">
                    <input type="hidden" id="idtarea" name="idtarea">
                    <input type="hidden" id="idproyectoTarea" name="idproyecto">
                    <div class="form-group">
                        <label for="tituloTarea">Título</label>
                        <input type="text" class="form-control" id="tituloTarea" name="titulo" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcionTarea">Descripción</label>
                        <textarea class="form-control" id="descripcionTarea" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="fechaEntregaTarea">Fecha de Entrega</label>
                        <input type="date" class="form-control" id="fechaEntregaTarea" name="fecha_entrega">
                    </div>
                    <div class="form-group">
                        <label for="estadoTarea">Estado</label>
                        <select class="form-control" id="estadoTarea" name="estado">
                            <option value="pendiente">Pendiente</option>
                            <option value="en proceso">En proceso</option>
                            <option value="completada">Completada</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-success" onclick="guardarTarea()">Guardar Tarea</button>
                </form>
            </div>
        </div>
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

    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Crear');
    // Cambia el título del modal
    $("#modalProyectoLabel").text("Nuevo Proyecto");
}

function cargarProyectos() {
    $.get("proyectos.php", function(data) {
        let nuevaTabla = $(data).find("#tablaProyectos tbody").html();
        $("#tablaProyectos tbody").html(nuevaTabla);
    });
}

cargarProyectos();

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

    $.post("proyectos.php", {
            action: "guardar", // Usamos el mismo action
            idproyecto: id,
            nombre: nombre,
            fechainicio: fechainicio,
            fechafin: fechafin,
            descripcion: descripcion,
            estado: estado,
            idusuario: idusuario
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

function abrirModalAgregarTareas(idTarea) {

    if (idTarea != -1) {
        $.get("proyectos.php", {
                action: "agregar_tarea",
                idTarea: idTarea
            })
            .done(function(data) {
                let tarea = JSON.parse(data);

                // Llenar los campos del formulario en el modal
                $("#modalProyecto input[name='idproyecto']").val(proyecto.idproyecto);
                $("#modalProyecto input[name='nombre']").val(proyecto.nombre);
                $("#modalProyecto input[name='fechainicio']").val(proyecto.fechainicio);
                $("#modalProyecto input[name='fechafin']").val(proyecto.fechafin);
                $("#modalProyecto textarea[name='descripcion']").val(proyecto.descripcion);
                $("#modalProyecto select[name='estado']").val(proyecto.estado);
                $("#modalProyecto select[name='idusuario']").val(proyecto.idusuario);

                // Cambiar el título del modal
                $("#modalProyectoLabel").text("Editar Proyecto");

                $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

            });
    }
    $("#modalProyecto").modal('show');

}

function cargarTareas(idproyecto) {
    $.get("proyectos.php", { action: "listarTareas", idproyecto: idproyecto }, function(data) {
        let tareas = JSON.parse(data);
        let html = "";
        tareas.forEach(tarea => {
            html += `<div class="card p-2 mb-2">
                        <h5>${tarea.titulo}</h5>
                        <p>${tarea.descripcion}</p>
                        <p><strong>Estado:</strong> ${tarea.estado}</p>
                     </div>`;
        });
        $("#tareasLista").html(html);
    });
}

function guardarTarea() {
    let datos = {
        action: "guardarTarea",
        idtarea: $("#idtarea").val() || 0,
        idproyecto: $("#idproyectoTarea").val(),
        titulo: $("#tituloTarea").val(),
        descripcion: $("#descripcionTarea").val(),
        fecha_entrega: $("#fechaEntregaTarea").val(),
        estado: $("#estadoTarea").val()
    };

    $.post("proyectos.php", datos, function(res) {
        if (res === "ok") {
            $("#modalAgregarTarea").modal("hide");
            cargarTareas(datos.idproyecto);
        }
    });
}


</script>


<?php include("../../template/bottom.php"); ?>