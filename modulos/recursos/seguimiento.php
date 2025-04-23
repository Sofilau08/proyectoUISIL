<?php
include("../../conn/conn.php");

// Obtener Todos del proyecto
$estado = isset($_GET['estado']) ? $_GET['estado'] : '0';
$usuarios = mysqli_query($conn, "SELECT id, nombre, apellidos, Identificacion, salario_hora FROM tusuarios WHERE estado = '$estado'");
$resultado = mysqli_query($conn, "SELECT * FROM trefinanciero WHERE estado = '$estado'");   
$material =  mysqli_query($conn, "SELECT * FROM tmateriales WHERE estado = '$estado'"); 
$recursoh = mysqli_query($conn, "SELECT tre.*, CONCAT(tu.nombre, ' ', tu.apellidos) as nombre_apellido 
    FROM trehumano as tre 
    JOIN tusuarios as tu ON tre.idusuario = tu.id 
    WHERE tre.estado = '$estado'");

if (isset($_POST['action']) && $_POST['action'] == 'aprobar') {
    $idrefinanciero = $_POST['idrefinanciero'];

    // Actualizar el estado del gasto a "1" (Aprobado)
    $sql = "UPDATE trefinanciero SET estado = '1' WHERE idrefinanciero = $idrefinanciero";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'denegar') {
    $idrefinanciero = $_POST['idrefinanciero'];
    $comentario = $_POST['comentario'];

    $sql = "UPDATE trefinanciero SET estado = '2', comentario = '$comentario' WHERE idrefinanciero = $idrefinanciero";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'aprobar_material') {
    $idmaterial = $_POST['idmaterial'];

    $sql = "UPDATE tmateriales SET estado = '1' WHERE idmaterial = $idmaterial";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'denegar_material') {
    $idmaterial = $_POST['idmaterial'];
    $comentario = $_POST['comentario'];

    $sql = "UPDATE tmateriales SET estado = '2', comentario = '$comentario' WHERE idmaterial = $idmaterial";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'aprobar_recurso') {
    $idrehumano = $_POST['idrehumano'];

    $sql = "UPDATE trehumano SET estado = '1' WHERE idrehumano = $idrehumano";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'denegar_recurso') {
    $idrehumano = $_POST['idrehumano'];
    $comentario = $_POST['comentario'];

    $sql = "UPDATE trehumano SET estado = '2', comentario = '$comentario' WHERE idrehumano = $idrehumano";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}   
?>

<?php include("../../template/top.php"); ?>

<div class="container mb-4">
    <div class="d-flex justify-content-start align-items-center" id="botonesEstado">
        <button class="btn btn-success mr-2" onclick="mostrarAprobados()">Aprobados</button>
        <button class="btn btn-danger mr-4" onclick="mostrarDenegados()">Denegados</button>
        <?php if ($estado == '1'): // Show "Generar Reporte" button only in "Aprobados" view ?>
            <a href="exportarExcel.php?estado=1" class="btn btn-primary">
                <i class="fa fa-download"></i> Generar Reporte
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="tituloMateriales">
                <?php 
                if ($estado == '0') {
                    echo "Material pendiente de aprobación";
                } elseif ($estado == '1') {
                    echo "Materiales Aprobados";
                } elseif ($estado == '2') {
                    echo "Materiales Denegados";
                }
                ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriales" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre del material</th>
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                            <th>Precio Total</th>
                            <th>Descripción</th>
                            <?php if ($estado == '2'): ?>
                                <th>Comentario</th>
                                <th>Acciones</th>
                            <?php else: ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($fila = mysqli_fetch_assoc($material)): ?>    
                        <tr>
                            <td><?= $fila['nombre'] ?></td>
                            <td><?= $fila['preciou'] ?></td>
                            <td><?= $fila['cantidad'] ?></td>
                            <td><?= $fila['preciott'] ?></td>
                            <td><?= $fila['descrip'] ?></td>
                            <?php if ($estado == '2'): ?>
                                <td><?= $fila['comentario'] ?></td>
                            <?php endif; ?>
                            <td>
                                <div>
                                    <?php if ($estado == '0'): // Pendientes ?>
                                        <button class="btn btn-sm btn-success"
                                            onclick="aprobarMaterial(<?= $fila['idmaterial'] ?>)">
                                            <i class="fa fa-check"></i> Aprobar
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="openDenegarModal(<?= $fila['idmaterial'] ?>, 'material')">
                                            <i class="fa fa-times"></i> Denegar
                                        </button>
                                    <?php elseif ($estado == '1'): // Aprobados ?>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="openDenegarModal(<?= $fila['idmaterial'] ?>, 'material')">
                                            <i class="fa fa-times"></i> Denegar
                                        </button>
                                    <?php elseif ($estado == '2'): // Denegados ?>
                                        <button class="btn btn-sm btn-success"
                                            onclick="aprobarMaterial(<?= $fila['idmaterial'] ?>)">
                                            <i class="fa fa-check"></i> Aprobar
                                        </button>
                                    <?php endif; ?>
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

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="tituloFinancieros">
                <?php 
                if ($estado == '0') {
                    echo "Gastos pendientes de aprobación";
                } elseif ($estado == '1') {
                    echo "Gastos Aprobados";
                } elseif ($estado == '2') {
                    echo "Gastos Denegados";
                }
                ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaFinancieros" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre del gasto</th>
                            <th>Fecha del gasto</th>
                            <th>Monto del gasto</th>
                            <th>Descripción</th>
                            <?php if ($estado == '2'): ?>
                                <th>Comentario</th>
                                <th>Acciones</th>
                            <?php elseif ($estado == '0' || $estado == '1'): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>    
                        <tr>
                            <td><?= $fila['NombreGasto'] ?></td>
                            <td><?= $fila['FechaGasto'] ?></td>
                            <td><?= $fila['monto'] ?></td>
                            <td><?= $fila['Descripcion'] ?></td>
                            <?php if ($estado == '2'): ?>
                                <td><?= $fila['comentario'] ?></td>
                            <?php endif; ?>
                            <td>
                                <div>
                                    <?php if ($estado == '0'): // Pendientes ?>
                                        <button class="btn btn-sm btn-success"
                                            onclick="aprobarGasto(<?= $fila['idrefinanciero'] ?>)">
                                            <i class="fa fa-check"></i> Aprobar
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="openDenegarModal(<?= $fila['idrefinanciero'] ?>, 'financiero')">
                                            <i class="fa fa-times"></i> Denegar
                                        </button>
                                    <?php elseif ($estado == '1'): // Aprobados ?>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="openDenegarModal(<?= $fila['idrefinanciero'] ?>, 'financiero')">
                                            <i class="fa fa-times"></i> Denegar
                                        </button>
                                    <?php elseif ($estado == '2'): // Denegados ?>
                                        <button class="btn btn-sm btn-success"
                                            onclick="aprobarGasto(<?= $fila['idrefinanciero'] ?>)">
                                            <i class="fa fa-check"></i> Aprobar
                                        </button>
                                    <?php endif; ?>
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

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="tituloRecursoHumano">
                <?php 
                if ($estado == '0') {
                    echo "Salarios pendientes de aprobación";
                } elseif ($estado == '1') {
                    echo "Salarios Aprobados";
                } elseif ($estado == '2') {
                    echo "Salarios Denegados";
                }
                ?>
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablarecuroh" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre y apellidos</th>
                            <th>Cédula</th>
                            <th>Salario Base</th>
                            <th>Horas Trabajadas</th>
                            <th>Salario Total</th>
                            <?php if ($estado == '2'): ?>
                                <th>Comentario</th>
                                <th>Acciones</th>
                            <?php elseif ($estado == '0' || $estado == '1'): ?>
                                <th>Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($fila = mysqli_fetch_assoc($recursoh)): ?>    
                        <tr>
                            <td><?= $fila['nombre_apellido'] ?></td>
                            <td><?= $fila['cedula'] ?></td>
                            <td><?= $fila['SalarioBase'] ?></td>
                            <td><?= $fila['HorasT'] ?></td>
                            <td><?= $fila['SalarioT'] ?></td>
                            <?php if ($estado === '2'): ?>
                                <td><?= $fila['comentario'] ?></td>
                            <?php endif; ?>
                            <td>
                                <div>
                                    <?php if ($estado == '0'): // Pendientes ?>
                                        <button class="btn btn-sm btn-success"
                                            onclick="aprobarRecurso(<?= $fila['idrehumano'] ?>)">
                                            <i class="fa fa-check"></i> Aprobar
                                        </button>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="openDenegarModal(<?= $fila['idrehumano'] ?>, 'recurso')">
                                            <i class="fa fa-times"></i> Denegar
                                        </button>
                                    <?php elseif ($estado == '1'): // Aprobados ?>
                                        <button class="btn btn-sm btn-danger"
                                            onclick="openDenegarModal(<?= $fila['idrehumano'] ?>, 'recurso')">
                                            <i class="fa fa-times"></i> Denegar
                                        </button>
                                    <?php elseif ($estado == '2'): // Denegados ?>
                                        
                                        <button class="btn btn-sm btn-success"
                                            onclick="aprobarRecurso(<?= $fila['idrehumano'] ?>)">
                                            <i class="fa fa-check"></i> Aprobar
                                        </button>
                                    <?php endif; ?>
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
        ], // Opciones de filas por página
        pageLength: 10, // Cantidad de filas por defecto
        responsive: true // Hace la tabla responsive
    });
    $("#tablaProyectos").addClass("text-right");
});

function cargarProyectos() {
    $.get("seguimiento.php", function(data) {
        let nuevaTabla = $(data).find("#tablaProyectos tbody").html();
        $("#tablaProyectos tbody").html(nuevaTabla);
    });
}

function aprobarGasto(idrefinanciero) {
    if (confirm("¿Estás seguro de que deseas aprobar este gasto?")) {
        $.post("seguimiento.php", {
                action: "aprobar",
                idrefinanciero: idrefinanciero
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("El gasto ha sido aprobado correctamente.");
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert("Error: " + data.error);
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al aprobar el gasto.");
            });
    }
}

function denegarGasto(idrefinanciero) {
    if (confirm("¿Estás seguro de que deseas denegar este gasto?")) {
        $.post("seguimiento.php", {
                action: "denegar",
                idrefinanciero: idrefinanciero
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("El gasto ha sido denegado correctamente.");
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert("Error: " + data.error);
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al denegar el gasto.");
            });
    }
}
function aprobarRecurso(idrehumano) {
    if (confirm("¿Estás seguro de que deseas aprobar este recurso humano?")) {
        $.post("Seguimiento.php", {
                action: "aprobar_recurso",
                idrehumano: idrehumano
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("El recurso humano ha sido aprobado correctamente.");
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert("Error: " + data.error);
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al aprobar el recurso humano.");
            });
    }
}

function denegarRecurso(idrehumano) {
    if (confirm("¿Estás seguro de que deseas denegar este recurso humano?")) {
        $.post("Seguimiento.php", {
                action: "denegar_recurso",
                idrehumano: idrehumano
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("El recurso humano ha sido denegado correctamente.");
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert("Error: " + data.error);
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al denegar el recurso humano.");
            });
    }
}

function aprobarMaterial(idmaterial) {
    if (confirm("¿Estás seguro de que deseas aprobar este material?")) {
        $.post("Seguimiento.php", {
                action: "aprobar_material",
                idmaterial: idmaterial
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("El material ha sido aprobado correctamente.");
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert("Error: " + data.error);
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al aprobar el material.");
            });
    }
}

function denegarMaterial(idmaterial) {
    if (confirm("¿Estás seguro de que deseas denegar este material?")) {
        $.post("Seguimiento.php", {
                action: "denegar_material",
                idmaterial: idmaterial
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("El material ha sido denegado correctamente.");
                    location.reload(); // Recargar la página para actualizar la tabla
                } else {
                    alert("Error: " + data.error);
                }
            })
            .fail(function(xhr, status, error) {
                console.error("Error en la solicitud:", error);
                alert("Ocurrió un error al denegar el material.");
            });
    }
}

function mostrarPendientes() {
    $.get("Seguimiento.php", { estado: '0' }) // Estado 0 para pendientes
        .done(function(data) {
            let nuevaTablaMateriales = $(data).find("#tablaMateriales tbody").html();
            $("#tablaMateriales tbody").html(nuevaTablaMateriales);

            let nuevaTablaFinancieros = $(data).find("#tablaFinancieros tbody").html();
            $("#tablaFinancieros tbody").html(nuevaTablaFinancieros);

            let nuevaTablaRecursoH = $(data).find("#tablarecuroh tbody").html();
            $("#tablarecuroh tbody").html(nuevaTablaRecursoH);

            $("#tituloMateriales").text("Pendientes de aprobación Material");
            $("#tituloFinancieros").text("Pendientes de aprobación Dato Financiero");
            $("#tituloRecursoHumano").text("Pendientes de aprobación Recurso Humano");

            // Cambiar los botones
            $("#botonesEstado").html(`
                <button class="btn btn-success mr-2" onclick="mostrarAprobados()">Aprobados</button>
                <button class="btn btn-danger" onclick="mostrarDenegados()">Denegados</button>
            `);
        })
        .fail(function(xhr, status, error) {
            console.error("Error al cargar los pendientes:", error);
            alert("Ocurrió un error al cargar los registros pendientes.");
        });
}

function mostrarAprobados() {
    $.get("Seguimiento.php", { estado: '1' }) // Estado 1 para aprobados
        .done(function(data) {
            let nuevaTablaMateriales = $(data).find("#tablaMateriales tbody").html();
            $("#tablaMateriales tbody").html(nuevaTablaMateriales);

            let nuevaTablaFinancieros = $(data).find("#tablaFinancieros tbody").html();
            $("#tablaFinancieros tbody").html(nuevaTablaFinancieros);
            
            let nuevaTablaRecursoH = $(data).find("#tablarecuroh tbody").html();
            $("#tablarecuroh tbody").html(nuevaTablaRecursoH);

            $("#tituloMateriales").text("Materiales Aprobados");
            $("#tituloFinancieros").text("Datos Financieros Aprobados");
            $("#tituloRecursoHumano").text("Recursos Humanos Aprobados");

            // Cambiar los botones
            $("#botonesEstado").html(`
                <button class="btn btn-primary mr-2" onclick="mostrarPendientes()">Pendientes</button>
                <button class="btn btn-danger mr-4" onclick="mostrarDenegados()">Denegados</button>
                <a href="exportarExcel.php?estado=1" class="btn btn-primary">
                    <i class="fa fa-download"></i> Generar Reporte
                </a>
            `);
        })
        .fail(function(xhr, status, error) {
            console.error("Error al cargar los aprobados:", error);
            alert("Ocurrió un error al cargar los registros aprobados.");
        });
}

function mostrarDenegados() {
    $.get("Seguimiento.php", { estado: '2' }) // Estado 2 para denegados
        .done(function(data) {
            let nuevaTablaMateriales = $(data).find("#tablaMateriales tbody").html();
            $("#tablaMateriales tbody").html(nuevaTablaMateriales);

            let nuevaTablaFinancieros = $(data).find("#tablaFinancieros tbody").html();
            $("#tablaFinancieros tbody").html(nuevaTablaFinancieros);

            let nuevaTablaRecursoH = $(data).find("#tablarecuroh tbody").html();
            $("#tablarecuroh tbody").html(nuevaTablaRecursoH);

            $("#tituloMateriales").text("Materiales Denegados");
            $("#tituloFinancieros").text("Datos Financieros Denegados");
            $("#tituloRecursoHumano").text("Recursos Humanos Denegados");

            // Cambiar los botones
            $("#botonesEstado").html(`
                <button class="btn btn-primary mr-2" onclick="mostrarPendientes()">Pendientes</button>
                <button class="btn btn-success" onclick="mostrarAprobados()">Aprobados</button>
            `);
        })
        .fail(function(xhr, status, error) {
            console.error("Error al cargar los denegados:", error);
            alert("Ocurrió un error al cargar los registros denegados.");
        });
}

function openDenegarModal(id, type) {
    $('#denegarModal').modal('show');
    $('#denegarModal').data('id', id);
    $('#denegarModal').data('type', type);
}

function submitDenegar() {
    const id = $('#denegarModal').data('id');
    const type = $('#denegarModal').data('type');
    const comentario = $('#comentarioDenegar').val();

    let action = '';
    if (type === 'financiero') action = 'denegar';
    else if (type === 'material') action = 'denegar_material';
    else if (type === 'recurso') action = 'denegar_recurso';

    $.post("Seguimiento.php", {
            action: action,
            idrefinanciero: type === 'financiero' ? id : undefined,
            idmaterial: type === 'material' ? id : undefined,
            idrehumano: type === 'recurso' ? id : undefined,
            comentario: comentario
        })
        .done(function(response) {
            let data = JSON.parse(response);
            if (data.success) {
                alert("El registro ha sido denegado correctamente.");
                location.reload();
            } else {
                alert("Error: " + data.error);
            }
        })
        .fail(function(xhr, status, error) {
            console.error("Error en la solicitud:", error);
            alert("Ocurrió un error al denegar el registro.");
        });
}
</script>

<!-- Modal for Denying -->
<div class="modal fade" id="denegarModal" tabindex="-1" role="dialog" aria-labelledby="denegarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="denegarModalLabel">Denegar Registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="comentarioDenegar">Comentario</label>
                    <textarea class="form-control" id="comentarioDenegar" rows="3" placeholder="Escribe el motivo de la denegación"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="submitDenegar()">Denegar</button>
            </div>
        </div>
    </div>
</div>

<?php include("../../template/bottom.php"); ?>