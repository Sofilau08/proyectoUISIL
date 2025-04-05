<?php
include("../../conn/conn.php");

// Obtener lista de usuarios para el combo
$usuarios = mysqli_query($conn, "SELECT id, nombre,apellidos FROM tusuarios");

// Crear o actualizar material
if (isset($_POST['action']) && $_POST['action'] == 'guardar') {
    $idmaterial = $_POST['idmaterial']; // Puede estar vacío si es un nuevo material
    $nombre = $_POST['nombre'];
    $preciou = $_POST['preciou'];
    $cantidad = $_POST['cantidad'];
    $descrip = $_POST['descrip'];
    $preciott = $_POST['preciott'];

    if ($nombre == "" || $preciou == "" || $cantidad == "" || $descrip == "") {
        // Validar que todos los campos estén llenos
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }


    if (empty($idmaterial) || $idmaterial == -1) {
        // Insertar nuevo material
        
        $sql = "INSERT INTO tmateriales (nombre, preciou, cantidad, descrip, preciott) 
                VALUES ('$nombre', '$preciou', '$cantidad', '$descrip', '$preciott')";
    } else {
        // Actualizar material existente
        $sql = "UPDATE tmateriales SET 
                    nombre='$nombre', 
                    preciou='$preciou', 
                    cantidad='$cantidad', 
                    descrip='$descrip',
                    preciott='$preciott'
                WHERE idmaterial='$idmaterial'";

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
    $idmaterial = $_POST['idmaterial'];
    $sql = "DELETE FROM tmateriales WHERE idmaterial = $idmaterial";
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
    $idmaterial = $_GET['idmaterial'];
    $resultado = mysqli_query($conn, "SELECT * FROM tmateriales WHERE idmaterial = $idmaterial");
    $proyecto = mysqli_fetch_assoc($resultado);
    echo json_encode($proyecto);
    exit();
}

// Obtener Todos los Materiales
$resultado = mysqli_query($conn, "SELECT * FROM tmateriales");
?>

<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="text-right">
        <button class="btn btn-sm btn-secondary mb-3" onclick="abrirModalProyecto(-1)">
            <i class="fa fa-plus"></i> Agregar Material</button>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Materiales</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriales" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Precio Unitario</th>
                            <th>Precio Total</th>
                            <th>Cantidad</th>
                            <th>Descripcion</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= $fila['nombre'] ?></td>
                            <td><?= $fila['preciou'] ?></td>
                            <td><?= $fila['preciott'] ?></td>
                            <td><?= $fila['cantidad'] ?></td>
                            <td><?= $fila['descrip'] ?></td>
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirModalProyecto(<?= $fila['idmaterial'] ?>)">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="eliminarProyecto(<?= $fila['idmaterial'] ?>)">
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
                            <input type="hidden" id="idmaterial" name="idmaterial"
                                value="<?= $proyecto['idmaterial'] ?? '' ?>">
                            <div class="grid-container">
                                <div>
                                    <label>Nombre:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required
                                        value="<?= $proyecto['nombre'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Precio Unitario:</label>
                                    <input type="number" class="form-control" id="preciou" name="preciou" required
                                        value="<?= $proyecto['preciou'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>cantidad:</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" required
                                        value="<?= $proyecto['cantidad'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Precio Total:</label>
                                    <input type="number" class="form-control" id="preciott" name="preciott" readonly
                                        value="<?= $proyecto['preciott'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Descripcion:</label>
                                    <textarea class="form-control" id="descrip"
                                        name="descrip"> <?= $proyecto['descrip'] ?? '' ?></textarea>
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
    $("#modalProyecto input[name='idmaterial']").val(-1);
    $("#modalProyecto input[name='nombre']").val("");
    $("#modalProyecto input[name='preciou']").val("");
    $("#modalProyecto input[name='cantidad']").val("");
    $("#modalProyecto textarea[name='descrip']").val("");
    $("#modalProyecto input[name='preciott']").val(""); 


    $("#modalProyectoLabel").text("Editar material");
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Crear');
    // Cambiar el título del modal

    $("#modalProyectoLabel").text("Editar material");
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

    $("#modalProyectoLabel").text("Nuevo Proyecto");
}

function cargarProyectos() {
    $.get("materiales.php", function(data) {
        let nuevaTabla = $(data).find("#tablaProyectos tbody").html();
        $("#tablaProyectos tbody").html(nuevaTabla);
    });
}

//Funcion para abrir modal 
function abrirModalProyecto(idmaterial) {

    limpiarModal();

    if (idmaterial != -1) {
        $.get("materiales.php", {
                action: "editar",
                idmaterial: idmaterial
            })
            .done(function(data) {
                let proyecto = JSON.parse(data);

                // Llenar los campos del formulario en el modal
                $("#modalProyecto input[name='idmaterial']").val(proyecto.idmaterial);
                $("#modalProyecto input[name='nombre']").val(proyecto.nombre);
                $("#modalProyecto input[name='preciou']").val(proyecto.preciou);
                $("#modalProyecto input[name='cantidad']").val(proyecto.cantidad);
                $("#modalProyecto textarea[name='descrip']").val(proyecto.descrip);

                //calcula el precio total
                calcularPrecioTotal();

                // Cambiar el título del modal
                $("#modalProyectoLabel").text("Editar material");

                $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

            });
    }
    $("#modalProyecto").modal('show');

}

function guardarProyecto() {
    var id = $('#idmaterial').val();
    var nombre = $('#nombre').val();
    var preciou = $('#preciou').val();
    var cantidad = $('#cantidad').val();
    var descrip = $('#descrip').val();
    var preciott = $('#preciott').val();

    console.log({ id, nombre, preciou, cantidad, descrip, preciott }); 


    $.post("materiales.php", {
            action: "guardar", 
            idmaterial: id,
            nombre: nombre,
            preciou: preciou,
            cantidad: cantidad,
            descrip: descrip,
            preciott: preciott
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

function eliminarProyecto(idmaterial) {
    if (confirm('¿Eliminar Material?')) {
        $.post("materiales.php", {
                action: "eliminar",
                idmaterial: idmaterial
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("Se eliminó el material correctamente");
                    document.location.href = document.location.href;
                } else {
                    alert("Error: " + data.error);
                }
            });
        }
}

$(document).ready(function() {
    // calculo del precio total
    $('#preciou, #cantidad').on('input', function() {
        calcularPrecioTotal();
    });
});

function calcularPrecioTotal() {
    var precioUnitario = parseFloat($('#preciou').val()) || 0; 
    var cantidad = parseFloat($('#cantidad').val()) || 0; 
    var precioTotal = precioUnitario * cantidad;

    $('#preciott').val(precioTotal.toFixed(2)); 
}
</script>


<?php include("../../template/bottom.php"); ?>