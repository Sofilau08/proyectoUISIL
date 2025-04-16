<?php
include("../../conn/conn.php");

// Obtener lista de usuarios para el combo
$usuarios = mysqli_query($conn, "SELECT id, nombre,apellidos FROM tusuarios");

// Crear o actualizar Proyecto
if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
    $idproyecto = $_POST['idproyecto'];
    $nombre = $_POST['nombre'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $idusuario = $_POST['idusuario'];
    $presupuesto = $_POST['presupuesto'];

    if (
        empty($nombre) || empty($fechainicio) || empty($fechafin) ||
        empty($descripcion) || empty($estado) || empty($idusuario) || empty($presupuesto)
    ) {
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

if ($_GET['action'] == 'listarTareas') {
    $idproyecto = $_GET['idproyecto'];

    $resultado = mysqli_query($conn, "SELECT * FROM ttareas WHERE idproyecto = " . intval($idproyecto));
    $tareas = [];

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $tareas[] = $fila;
    }
    echo json_encode($tareas);
    exit();
}

    
if ($_POST['action'] == 'guardarTarea') {
    $idtarea = $_POST['idtarea'];
    $idproyecto = $_POST['idproyecto'];
    $titulo = $_POST['titulo'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];
    $estadoproyecto = $_POST['estadoproyecto'];
    $idusuario = $_POST['idusuario'];
    $estadotarea = $_POST['estadotarea'];

    if ($idtarea == 0) {
        $stmt = $conexion->prepare("INSERT INTO ttareas (idproyecto, titulo, fechainicio, fechafin, descripcion, estadoproyecto, idusuario, estadotarea) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssis", $idproyecto, $titulo, $fechainicio, $fechafin, $descripcion, $estadoproyecto, $idusuario, $estadotarea);
    } else {
        $stmt = $conexion->prepare("UPDATE ttareas SET idproyecto=?, titulo=?, fechainicio=?, fechafin=?, descripcion=?, estadoproyecto=?, idusuario=?, estadotarea=? WHERE idtarea=?");
        $stmt->bind_param("isssssisi", $idproyecto, $titulo, $fechainicio, $fechafin, $descripcion, $estadoproyecto, $idusuario, $estadotarea, $idtarea);
    }

    $stmt->execute();
    echo "ok";
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
                                        onclick="abrirModalAgregarTareas(<?= $fila['idproyecto'] ?>)">
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

<!-- Modal -->
<div class="modal fade" id="modalProyecto" tabindex="-1" role="dialog" aria-labelledby="modalProyectoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <!-- FORM PRINCIPAL -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProyectoLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <!-- FORMULARIO ÚNICO -->
                        <input type="hidden" id="idproyecto" name="idproyecto">
                        <div class="grid-container">
                            <div>
                                <label>Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div>
                                <label>Fecha Inicio:</label>
                                <input type="date" class="form-control" id="fechainicio" name="fechainicio" required>
                            </div>
                            <div>
                                <label>Fecha Fin:</label>
                                <input type="datetime-local" class="form-control" id="fechafin" name="fechafin">
                            </div>
                            <div>
                                <label>Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                            </div>
                            <div>
                                <label>Presupuesto:</label>
                                <input type="text" class="form-control" id="presupuesto" name="presupuesto" required>
                            </div>
                            <div>
                                <label>Estado:</label>
                                <select class="form-control" id="estado" name="estado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en proceso">En Proceso</option>
                                    <option value="finalizado">Finalizado</option>
                                </select>
                            </div>
                            <div>
                                <label>Usuario:</label>
                                <select class="form-control" id="idusuario" name="idusuario" required>
                                    <?php while ($user = mysqli_fetch_assoc($usuarios)): ?>
                                        <option value="<?= $user['id'] ?>">
                                            <?= $user['nombre'] ?> <?= $user['apellidos'] ?>
                                        </option>
                                    <?php endwhile; ?>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalTareas" tabindex="-1" role="dialog" aria-labelledby="modalTareasLabel"
    aria-hidden="true">
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
                    <input type="hidden" id="idproyectoTarea" name="idproyectoTarea">

                    <div class="form-group">
                        <label for="tituloTarea">Título</label>
                        <input type="text" class="form-control" id="tituloTarea" name="titulo" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcionTarea">Descripción</label>
                        <textarea class="form-control" id="descripcionTarea" name="descripcion" rows="2"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="fechaInicioTarea">Fecha de Inicio</label>
                        <input type="datetime-local" class="form-control" id="fechaInicioTarea" name="fechainicio">
                    </div>

                    <div class="form-group">
                        <label for="fechaFinTarea">Fecha de Fin</label>
                        <input type="datetime-local" class="form-control" id="fechaFinTarea" name="fechafin">
                    </div>

                    <div class="form-group">
                        <label for="estadoProyectoTarea">Estado del Proyecto</label>
                        <select class="form-control" id="estadoProyectoTarea" name="estadoproyecto">
                            <option value="pendiente">Pendiente</option>
                            <option value="en proceso">En proceso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="idusuarioTarea">Asignar a Usuario</label>
                        <select class="form-control" id="idusuario" name="idusuario" required>
                            <?php while ($user = mysqli_fetch_assoc($usuarios)): ?>
                            <option value="<?= $user['id'] ?>"
                                <?= isset($proyecto['idusuario']) && $proyecto['idusuario'] == $user['id'] ? 'selected' : '' ?>>
                                <?= $user['nombre'] ?> <?= $user['apellidos'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>


                    <div class="form-group">
                        <label for="estadoTarea">Estado de la Tarea</label>
                        <select class="form-control" id="estadoTarea" name="estadotarea">
                            <option value="pendiente">Pendiente</option>
                            <option value="en proceso">En proceso</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-success" id="btn-guardarTarea" onclick="guardarTarea()">Guardar
                        Tarea</button>
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
    $("#modalProyecto select[name='presupuesto']").val("");

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
                $("#modalProyecto select[name='presupuesto']").val(proyecto.presupuesto);

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

function cargarTareas(idproyecto) {
    $.get("proyectos.php", {
        action: "listarTareas",
        idproyecto: idproyecto // corregido el nombre del parámetro
    }, function(data) {
        try {
            let tareas = JSON.parse(data);

            if (!Array.isArray(tareas)) {
                console.error("Respuesta inesperada:", tareas);
                $("#tareasLista").html("<p>No se encontraron tareas.</p>");
                return;
            }

            let html = "";
            tareas.forEach(tarea => {
                html += `<div class="card p-2 mb-2">
                            <h5>${tarea.titulo}</h5>
                            <p>${tarea.descripcion}</p>
                            <p><strong>Estado:</strong> ${tarea.estado}</p>
                         </div>`;
            });
            $("#tareasLista").html(html);
        } catch (err) {
            console.error("Error al procesar las tareas:", err, data);
            $("#tareasLista").html("<p>Error al cargar las tareas.</p>");
        }
    });
}


function guardarTarea() {

    var idtarea = $('#idtarea').val();
    var idproyecto = $('#idproyectoTarea').val();
    var titulo = $('#tituloTarea').val();
    var fechainicio = $('#fechainicioTarea').val();
    var fechafin = $('#fechafinTarea').val();
    var descripcion = $('#descripcionTarea').val();
    var estado = $('#estadoProyectoTarea').val();
    var idusuario = $('#idusuarioTarea').val();
    var estadotarea = $('#estadotarea').val();

    $.post("proyectos.php", {
            action: "guardarTarea",
            idtarea: idtarea,
            idproyecto: idproyecto,

            titulo: titulo,
            fechainicio: fechainicio,
            fechafin: fechafin,
            descripcion: descripcion,
            estadoproyecto: estado,
            idusuario: idusuario,
            estadotarea: estadotarea,

        })
        .done(function(response) {
            let data = JSON.parse(response);
            if (data.success) {
                $('#modalTareas').modal('hide');
                cargarTareas();
            } else {
                alert("Error: " + data.error);
            }
        });

}

function abrirModalAgregarTareas(idProyecto) {
    cargarTareas(idProyecto);
    $("#modalTareas").modal('show');

}

</script>



<?php include("../../template/bottom.php"); ?>