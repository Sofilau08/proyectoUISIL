<?php
include("../../conn/conn.php");
date_default_timezone_set('America/Costa_Rica');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener idusuario desde cookie
$idUsuario = $idUsuario;

if (!$idUsuario) {
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
    $fechainicio = date('Y-m-d H:i');

    if ($titulo == '' ||  $descripcion == '' ||  $prioridad == '' ||  $categoria == '') {
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }

    if (empty($idticket) || $idticket == -1) {
        $sql = "INSERT INTO ttickets (titulo, descripcion, categoria, prioridad, estado, fechainicio, idusuario_informador)
                VALUES ('$titulo', '$descripcion', '$categoria', '$prioridad', 'abierto', '$fechainicio', '$idUsuario')";
    } else {
        $sql = "UPDATE ttickets SET
                    titulo = '$titulo',
                    descripcion = '$descripcion',
                    categoria = '$categoria',
                    prioridad = '$prioridad',
                    estado = '$estado',
                WHERE idticket = $idticket";
        echo "<script>alert('Tiquete actualizado correctamente.');</script>";
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
    $sql = "INSERT INTO tcomentarios (idticket, idusuario, fecha, comentario) 
    VALUES ('$idticket', '$idUsuario', '$fecha', '$comentario')";

    echo json_encode(["success" => mysqli_query($conn, $sql)]);
    exit();
}

// Obtener comentarios de un tiquete
if (isset($_GET['action']) && $_GET['action'] == 'comentarios') {
    $idticket = $_GET['idticket'];
    $sql = "SELECT c.comentario, c.fecha, c.idusuario, u.nombre FROM tcomentarios c
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

// Mostrar todos los tiquetes de ese usuario 
$tiquetes = mysqli_query($conn, "
    SELECT T0.*, T1.nombre, T1.apellidos 
    FROM ttickets T0
    JOIN tusuarios T1 ON T0.idusuario_informador = T1.id
");

include("../../template/top.php");
?>
<script>
    const idUsuarioActual = <?= json_encode($idUsuario) ?>;
</script>

<!-- Tarjeta que contiene la tabla de tiquetes -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h2 class="m-0 font-weight-bold text-primary">Bienvenido a la Base de Conocimiento</h2>
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
                            <!-- Botón para abrir detalles y comentarios del tiquete -->
                            <button class="btn btn-sm btn-info" onclick="verComentarios(<?= $fila['idticket'] ?>)">
                                <i class="fas fa-comments"></i> Detalles
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
        box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
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
        $("#modalTiquete input[name='titulo']").val("");
        $("#modalTiquete input[name='descripcion']").val("");
        $("#modalTiquete input[name='categoria']").val("");
        $("#modalTiquete input[name='prioridad']").val("");
        $("#modalTiquete input[name='estado']").val("");
    }

    function cargarTiquetes() {
        $.get("crearTiquete.php", function(data) {
            let nuevaTabla = $(data).find("#tablaTickets tbody").html();
            // Destruir DataTable actual
            $('#tablaTickets').DataTable().destroy();
            // Reemplazar contenido
            $("#tablaTickets tbody").html(nuevaTabla);
            // Re-inicializar DataTable
            $('#tablaTickets').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                }
            });
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

        $.post("crearTiquete.php", {
                action: "guardar", // Usamos el mismo action
                titulo: titulo,
                descripcion: descripcion,
                prioridad: prioridad,
                categoria: categoria
            })
            .done(function(response) {
                console.log(response);
                let data = JSON.parse(response);
                if (data.success) {
                    alert("Tiquete guardado exitosamente.");
                    $('#modalTiquete').modal('hide');
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
                idticket: ticketActual,
                comentario: comentario
            })
            .done(function(response) {
                console.log(response);
                let data = JSON.parse(response);
                if (data.success) {
                    alert("Comentario guardado exitosamente.");
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

    function verComentarios(idticket) {
        ticketActual = idticket;
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