<?php
include("../../conn/conn.php");

// Crear o actualizar material
if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
    $idrefinanciero = $_POST['idrefinanciero']; // Puede estar vacío si es un nuevo material
    $NombreGasto = $_POST['NombreGasto'];
    $FechaGasto = $_POST['FechaGasto'];
    $Descripcion = $_POST['Descripcion'];
    $monto = $_POST['monto'];

    if ($NombreGasto == "" || $FechaGasto == "" || $monto == ""|| $Descripcion == "") {
        // Validar que todos los campos estén llenos
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }


    if (empty($idrefinanciero) || $idrefinanciero == -1) {
        // Insertar nuevo material
        
        $sql = "INSERT INTO trefinanciero (NombreGasto, FechaGasto, Descripcion, monto) 
                VALUES ('$NombreGasto', '$FechaGasto', '$Descripcion', '$monto')";
    } else {
        // Actualizar material existente
        $sql = "UPDATE trefinanciero SET 
                    NombreGasto='$NombreGasto', 
                    FechaGasto='$FechaGasto', 
                    Descripcion='$Descripcion',
                    monto='$monto'
                WHERE idrefinanciero='$idrefinanciero'";

    }
    error_log($sql); 


    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}


// Eliminar material
if (isset($_POST['action']) && $_POST['action'] == 'eliminar') {
    $idrefinanciero = $_POST['idrefinanciero'];
    $sql = "DELETE FROM trefinanciero WHERE idrefinanciero = $idrefinanciero";
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
    $idrefinanciero = $_GET['idrefinanciero'];
    $resultado = mysqli_query($conn, "SELECT * FROM trefinanciero WHERE idrefinanciero = $idrefinanciero");
    $proyecto = mysqli_fetch_assoc($resultado);
    echo json_encode($proyecto);
    exit();
}

// Obtener Todos los Materiales
$resultado = mysqli_query($conn, "SELECT * FROM trefinanciero");
?>

<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="text-right">
        <button class="btn btn-sm btn-secondary mb-3" onclick="abrirModalProyecto(-1)">
            <i class="fa fa-plus"></i> Agregar Financiero</button>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Datos Financieros</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriales" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre del gasto</th>
                            <th>Fecha del gasto</th>
                            <th>Monto del gasto</th>
                            <th>Descripcion</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= $fila['NombreGasto'] ?></td>
                            <td><?= $fila['FechaGasto'] ?></td>
                            <td><?= $fila['monto'] ?></td>
                            <td><?= $fila['Descripcion'] ?></td>
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirModalProyecto(<?= $fila['idrefinanciero'] ?>)">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="eliminarProyecto(<?= $fila['idrefinanciero'] ?>)">
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
                            <input type="hidden" id="idrefinanciero" name="idrefinanciero"
                                value="<?= $proyecto['idrefinanciero'] ?? '' ?>">
                            <div class="grid-container">
                                <div>
                                    <label>Nombre del gasto:</label>
                                    <input type="text" class="form-control" id="NombreGasto" name="NombreGasto" required
                                        value="<?= $proyecto['NombreGasto'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Fecha del gasto:</label>
                                    <input type="date" class="form-control" id="FechaGasto" name="FechaGasto" required
                                        value="<?= $proyecto['FechaGasto'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Monto:</label>
                                    <input type="number" class="form-control" id="monto" name="monto" required
                                        value="<?= $proyecto['monto'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Descripcion:</label>
                                    <textarea class="form-control" id="Descripcion"
                                        name="Descripcion"> <?= $proyecto['Descripcion'] ?? '' ?></textarea>
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

function limpiarModal() {
    $("#modalProyecto input[name='idrefinanciero']").val(-1);
    $("#modalProyecto input[name='NombreGasto']").val("");
    $("#modalProyecto input[name='FechaGasto']").val("");
    $("#modalProyecto textarea[name='Descripcion']").val("");
    $("#modalProyecto input[name='monto']").val(""); 


    $("#modalProyectoLabel").text("Editar Financiero");
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Crear');
    // Cambiar el título del modal

    $("#modalProyectoLabel").text("Editar Financiero");
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

    $("#modalProyectoLabel").text("Nuevo Financiero");
}

function cargarProyectos() {
    $.get("financiero.php", function(data) {
        let nuevaTabla = $(data).find("#tablaProyectos tbody").html();
        $("#tablaProyectos tbody").html(nuevaTabla);
    });
}

//Funcion para abrir modal 
function abrirModalProyecto(idrefinanciero) {

    limpiarModal();

    if (idrefinanciero != -1) {
        $.get("financiero.php", {
                action: "editar",
                idrefinanciero: idrefinanciero
            })
            .done(function(data) {
                let proyecto = JSON.parse(data);

                // Llenar los campos del formulario en el modal
                $("#modalProyecto input[name='idrefinanciero']").val(proyecto.idrefinanciero);
                $("#modalProyecto input[name='NombreGasto']").val(proyecto.NombreGasto);
                $("#modalProyecto input[name='FechaGasto']").val(proyecto.FechaGasto);
                $("#modalProyecto input[name='monto']").val(proyecto.monto);
                $("#modalProyecto textarea[name='Descripcion']").val(proyecto.Descripcion);

                // Cambiar el título del modal
                $("#modalProyectoLabel").text("Editar Financiero");

                $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

            });
    }
    $("#modalProyecto").modal('show');

}

function guardarProyecto() {
    var id = $('#idrefinanciero').val();
    var NombreGasto = $('#NombreGasto').val();
    var FechaGasto = $('#FechaGasto').val();
    var Descripcion = $('#Descripcion').val();
    var monto = $('#monto').val();

    console.log({ id, NombreGasto, FechaGasto, Descripcion, monto }); 


    $.post("financiero.php", {
            action: "guardar", 
            idrefinanciero: id,
            NombreGasto: NombreGasto,
            FechaGasto: FechaGasto,
            Descripcion: Descripcion,
            monto: monto
        })
        .done(function(response) {
            let data = JSON.parse(response);
            if (data.success) {
                $('#modalProyecto').modal('hide');
                //cargarProyectos();
                document.location.href = document.location.href;
            } else {
                alert("Error: " + data.error);
            }
        });
}

function eliminarProyecto(idrefinanciero) {
    if (confirm('¿Seguro que desea eliminar el dato Financiero?')) {
        $.post("financiero.php", {
                action: "eliminar",
                idrefinanciero: idrefinanciero
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("Se eliminó el dato financiero correctamente");
                    document.location.href = document.location.href;
                } else {
                    alert("Error: " + data.error);
                }
            });
        }
}

</script>


<?php include("../../template/bottom.php"); ?>