<?php
include("../../conn/conn.php");
date_default_timezone_set('America/Costa_Rica');

// Obtener idusuario desde cookie
$idusuario = $idUsuario;

if (!$idusuario) {
    echo "<script>alert('No se ha detectado usuario.');</script>";
    exit();
}

// Crear o actualizar tiquete
if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
    ini_set("display_errors", 1);


    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $prioridad = $_POST['prioridad'];
    $estado = $_POST['estado'];
    $fechainicio = date('Y-m-d H:i');
    if ($titulo == '' ||  $descripcion == '' ||  $prioridad == '' ||  $estado == '') {
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }

    if (empty($idticket) || $idticket == -1) {
        $sql = "INSERT INTO ttickets (idusuario, titulo, descripcion, categoria, prioridad, estado, fechainicio, idusuario_informador)
                VALUES ('$idusuario', '$titulo', '$descripcion', '$categoria', '$prioridad', '$estado', '$fechainicio', '$idusuario')";
    } else {
        $sql = "UPDATE ttickets SET
                    titulo = '$titulo',
                    descripcion = '$descripcion',
                    categoria = '$categoria',
                    prioridad = '$prioridad',
                    estado = '$estado',
                WHERE idticket = $idticket";
    }

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

// Eliminar tiquete
if (isset($_POST['action']) && $_POST['action'] == 'eliminar') {
    $idticket = $_POST['idticket'];
    mysqli_query($conn, "DELETE FROM tcomentarios WHERE idticket = $idticket");
    $sql = "DELETE FROM ttickets WHERE idticket = $idticket";
    echo json_encode(["success" => mysqli_query($conn, $sql)]);
    exit();
}

// Agregar comentario
if (isset($_POST['action']) && $_POST['action'] == 'comentar') {
    ini_set("display_errors", 1);
    $fecha = date('Y-m-d H:i'); // Fecha actual con minutos
    $idticket = $_POST['idticket'];
    $comentario = $_POST['comentario'];
    $sql = "INSERT INTO tcomentarios (idticket, idusuario, fecha, comentario) VALUES ('$idticket', '$idusuario', '$fecha', '$comentario')";
    echo json_encode(["success" => mysqli_query($conn, $sql)]);
    exit();
}

// Obtener comentarios de un tiquete
if (isset($_GET['action']) && $_GET['action'] == 'comentarios') {
    $idticket = $_GET['idticket'];
    $sql = "SELECT c.comentario, c.fecha, u.nombre FROM tcomentarios c
            JOIN tusuarios u ON c.idusuario = u.id
            WHERE c.idticket = $idticket ORDER BY c.fecha ASC";
    $res = mysqli_query($conn, $sql);
    $datos = mysqli_fetch_all($res, MYSQLI_ASSOC);
    echo json_encode($datos);
    exit();
}

// Obtener info de tiquete para edición
if (isset($_GET['action']) && $_GET['action'] == 'editar') {
    $idticket = $_GET['idticket'];
    $res = mysqli_query($conn, "SELECT * FROM ttickets WHERE idticket = $idticket");
    echo json_encode(mysqli_fetch_assoc($res));
    exit();
}

// Mostrar todos los tiquetes
$tiquetes = mysqli_query($conn, "SELECT * FROM ttickets");
include("../../template/top.php");
?>
<script>
    const idUsuarioActual = <?= json_encode($idusuario) ?>;
</script>

<div class="container">
    <div class="text-right">
        <!-- Botón para abrir el modal de crear tiquete -->
        <button class="btn btn-sm btn-secondary mb-3" onclick="abrirModalTiquete(-1)">
            <i class="fa fa-plus"></i> Nuevo Tiquete
        </button>
    </div>

    <!-- Tarjeta que contiene la tabla de tiquetes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Tiquetes</h6>
        </div>
        <div class="card-body">
            <!-- Tabla con los tiquetes -->
            <table class="table table-bordered" id="tablaTickets">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                        <th>Creado por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = mysqli_fetch_assoc($tiquetes)): ?>
                        <tr>
                            <td><?= $fila['titulo'] ?></td>
                            <td><?= ucfirst($fila['categoria']) ?></td>
                            <td><?= ucfirst($fila['prioridad']) ?></td>
                            <td><?= ucfirst($fila['estado']) ?></td>
                            <td><?= $fila['fechainicio'] ?></td>
                            <td><?= $fila['nombre'] ?> <?= $fila['apellidos'] ?></td>
                            <td>
                                <!-- Botón para abrir detalles del tiquete -->
                                <button class="btn btn-sm btn-info" onclick="verComentarios(<?= $fila['idticket'] ?>)">
                                    <i class="fas fa-comments"></i> Detalles
                                </button>
                                <!-- Botón para editar el tiquete -->
                                <button class="btn btn-sm btn-secondary" onclick="abrirModalTiquete(<?= $fila['idticket'] ?>)">
                                    <i class="fas fa-pencil-alt"></i> Editar
                                </button>
                                <!-- Botón para eliminar el tiquete -->
                                <button class="btn btn-sm btn-danger" onclick="eliminarTiquete(<?= $fila['idticket'] ?>)">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tiquete -->
<div class="modal fade" id="modalTiquete" tabindex="-1" role="dialog" aria-labelledby="modalTiqueteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content" onsubmit="return false;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTiqueteLabel">Nuevo Tiquete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idticket" name="idticket">

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
                </div>

                <div class="form-group">
                    <label>Categoría</label>
                    <select class="form-control" id="categoria" name="categoria" required>
                        <option value="hardware">Hardware</option>
                        <option value="software">Software</option>
                        <option value="redes">Redes</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Prioridad</label>
                    <select class="form-control" id="prioridad" name="prioridad" required>
                        <option value="alta">Alta</option>
                        <option value="media">Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control" id="estado" name="estado" required>
                        <option value="abierto">Abierto</option>
                        <option value="en proceso">En Proceso</option>
                        <option value="cerrado">Cerrado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="datetime-local" class="form-control" id="fechafin" name="fechafin">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btn-guardarTiquete" onclick="guardarTiquete()">
                    <i class="fas fa-check"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver comentarios -->
<div class="modal fade" id="modalComentarios" tabindex="-1" role="dialog" aria-labelledby="modalComentariosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComentariosLabel">Comentarios del Tiquete</h5>
                <div id="contenedorComentarios" class="mt-3" style="max-height: 300px; overflow-y: auto;"></div>

            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="agregar_comentario">
                <input type="hidden" id="idticket_comentarios" name="idticket">
                <div class="form-group mt-3">
                    <label for="comentario">Agregar un Comentario:</label>
                    <textarea id="comentario" class="form-control" name="comentario" required></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" id="btn-guardarComentario" onclick="guardarComentario()">
                    <i class="fas fa-check"></i> Guardar Comentario
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .comentario-propio {
        text-align: right;
        margin-bottom: 10px;
    }

    .comentario-otro {
        text-align: left;
        margin-bottom: 10px;
    }

    .comentario-burbuja {
        display: inline-block;
        padding: 10px 15px;
        border-radius: 20px;
        max-width: 75%;
        background-color: #e3f2fd;
        font-size: 0.9rem;
        box-shadow: 0px 1px 3px rgba(0,0,0,0.1);
    }

    .comentario-propio .comentario-burbuja {
        background-color: #c8e6c9;
        color: #000;
    }

</style>

<script>
    
    let ticketActual = 0;
    $(document).ready(function() {
        $('#tablaTickets').DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            }
        });
    });

    function abrirModalTicket() {
        $('#modalTicket form')[0].reset();
        $('#modalTicket').modal('show');
    }

    function limpiarModal() {
        $("#modalTiquetes input[name='titulo']").val(-1);
        $("#modalTiquetes input[name='descipcion']").val("");
        $("#modalTiquetes input[name='categoria']").val("");
        $("#modalTiquetes input[name='prioridad']").val("");

        $("#btn-guardarTiquete").html('<i class="fas fa-check"></i> Crear');
        // Cambiar el título del modal
        $("#modalTiquetesLabel").text("Nuevo Proyecto");
    }

    function cargarTiquetes() {
        $.get("crearTiquete.php", function(data) {
            let nuevaTabla = $(data).find("#tablaTiquetes tbody").html();
            $("#tablaTiquetes tbody").html(nuevaTabla);
        });
    }

    function abrirModalTiquete(idticket) {
        limpiarModal();

        if (idticket != -1) {
            $.get("crearTiquete.php", {
                    action: "editar",
                    idticket: idticket
                })
                .done(function(data) {
                    let ticket = JSON.parse(data);

                    $("#idticket").val(ticket.idticket);
                    $("#titulo").val(ticket.titulo);
                    $("#descripcion").val(ticket.descripcion);
                    $("#categoria").val(ticket.categoria);
                    $("#prioridad").val(ticket.prioridad);
                    $("#estado").val(ticket.estado);

                    $("#modalTiqueteLabel").text("Editar Tiquete");
                    $("#btn-guardarTiquete").html('<i class="fas fa-check"></i> Actualizar');
                });
        } else {
            $("#modalTiqueteLabel").text("Nuevo Tiquete");
            $("#btn-guardarTiquete").html('<i class="fas fa-check"></i> Crear');
        }

        $("#modalTiquete").modal('show'); // <- Esta línea es la que abre el modal
    }


    function guardarTiquete() {
        var idticket = $('#idticket').val();
        var titulo = $('[name=titulo]').val();
        var descripcion = $('[name=descripcion]').val();
        var categoria = $('[name=categoria]').val();
        var prioridad = $('[name=prioridad]').val();
        var estado = $('[name=estado]').val();
        var fechainicio = $('[name=fechafin]').val();

        $.post("crearTiquete.php", {
                action: "guardar", // Usamos el mismo action
                titulo: titulo,
                descripcion: descripcion,
                prioridad: prioridad,
                categoria: categoria,
                fechainicio: fechainicio,
                estado: estado
            })
        .done(function(response) {
            console.log(response);
            let data = JSON.parse(response);
            if (data.success) {
                $('#modalTiquetes').modal('hide');
                cargarTiquetes();
            } else {
                alert("Error: " + data.error);
            }
        });
    }
    
    function guardarComentario() {
        var comentario = $('#comentario').val();
        $.post('crearTiquete.php', {
            action: 'comentar',
            idticket : ticketActual,
            comentario: comentario
        })
        .done(function(response) {
        console.log(response);
        let data = JSON.parse(response);
        if (data.success) {
            $('#comentario').val('');
            verComentarios();
        } else {
            alert("Error: " + data.error);
        }
        });
    }

    function eliminarTiquete(idticket) {
        if (!confirm('¿Seguro de eliminar este tiquete?')) return;
        $.post('crearTiquete.php', {
            action: 'eliminar',
            idticket: idticket
        }, function(r) {
            const res = JSON.parse(r);
            if (res.success) location.reload();
            else alert('Error eliminando');
        });
    }

    function verComentarios(idticket, comentario) {
    ticketActual = idticket;
    comentario = comentario
    $.get('crearTiquete.php?action=comentarios&idticket=' + idticket, function(data) {
        const comentarios = JSON.parse(data);
        let html = comentarios.map(c => {
            const clase = (c.idusuario == idUsuarioActual) ? 'comentario-propio' : 'comentario-otro';
            return `
                <div class="${clase}">
                    <div class="comentario-burbuja">
                        <strong>${c.nombre}</strong> <small>${c.fecha}</small><br>
                        ${c.comentario}
                    </div>
                </div>
            `;
        }).join('');
        $('#contenedorComentarios').html(html);
        $('#modalComentarios').modal('show');
    });
}


</script>

<?php include("../../template/bottom.php"); ?>
