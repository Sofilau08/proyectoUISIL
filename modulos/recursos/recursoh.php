<?php
include("../../conn/conn.php");

// Obtener lista de usuarios para el combo
$usuarios = mysqli_query($conn, "SELECT id, nombre, apellidos, Identificacion, salario_hora FROM tusuarios");

// Crear o actualizar material
if (isset($_POST['action']) && $_POST['action'] == 'guardar') { 
    error_log("Datos recibidos: " . print_r($_POST, true)); 

    $idrehumano = $_POST['idrehumano'];
    $idusuario  = $_POST['idusuario'];
    $cedula = $_POST['cedula'];
    $HorasT = $_POST['HorasT'];
    $SalarioT = $_POST['SalarioT'];
    $SalarioBase = $_POST['SalarioBase'];

    $usuarioQuery = mysqli_query($conn, "SELECT CONCAT(nombre, ' ', apellidos) AS nombre_apellido FROM tusuarios WHERE id = $idusuario");
    if (!$usuarioQuery) {
        error_log("Error en la consulta de usuario: " . mysqli_error($conn)); // Depuración
        echo json_encode(["success" => false, "error" => "Error al obtener el usuario."]);
        exit();
    }
    $usuario = mysqli_fetch_assoc($usuarioQuery);
    $nombre_apellido = $usuario['nombre_apellido'];

    if ($nombre_apellido == "" || $cedula == "" || $HorasT == "" || $SalarioBase == ""|| $SalarioT == "") {
        // Validar que todos los campos estén llenos
        echo json_encode(["success" => false, "error" => "Todos los campos son obligatorios."]);
        exit();
    }


    if (empty($idrehumano) || $idrehumano == -1) {
        // Insertar nuevo material
        
        $sql = "INSERT INTO trehumano (nombre_apellido, cedula, HorasT, SalarioT, SalarioBase) 
                VALUES ('$nombre_apellido', '$cedula', '$HorasT', '$SalarioT', '$SalarioBase')";
    } else {
        // Actualizar material existente
        $sql = "UPDATE trehumano SET 
                    nombre_apellido='$nombre_apellido', 
                    cedula='$cedula',   
                    HorasT='$HorasT', 
                    SalarioT='$SalarioT',
                    SalarioBase='$SalarioBase'
                WHERE idrehumano='$idrehumano'";

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
    $idrehumano = $_POST['idrehumano'];
    $sql = "DELETE FROM trehumano WHERE idrehumano = $idrehumano";
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
    $idrehumano = $_GET['idrehumano'];
    $resultado = mysqli_query($conn, "SELECT * FROM trehumano WHERE idrehumano = $idrehumano");
    $proyecto = mysqli_fetch_assoc($resultado); 
    $proyecto['idusuario'] = $proyecto['idusuario'] ?? null;

    echo json_encode($proyecto);
    exit();
}

// Obtener Todos los Materiales
$resultado = mysqli_query($conn, "SELECT * FROM trehumano");
?>

<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="text-right">
        <button class="btn btn-sm btn-secondary mb-3" onclick="abrirModalProyecto(-1)">
            <i class="fa fa-plus"></i> Agregar recurso humano</button>
    </div>
    <div class="card shadow mb-4">  
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Datos de recursos humanos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriales" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre y apellidos del trabajador</th>
                            <th>cedula</th>
                            <th>Salario Base</th>
                            <th>Horas trabajadas</th>
                            <th>Salario Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['nombre_apellido']) ?></td>
                            <td><?= htmlspecialchars($fila['cedula']) ?></td>
                            <td><?= htmlspecialchars($fila['SalarioBase']) ?></td>
                            <td><?= htmlspecialchars($fila['HorasT']) ?></td>
                            <td><?= htmlspecialchars($fila['SalarioT']) ?></td>
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-secondary"
                                        onclick="abrirModalProyecto(<?= $fila['idrehumano'] ?>)">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="eliminarProyecto(<?= $fila['idrehumano'] ?>)">
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
        <form method="POST" class="modal-content" action="recursoh.php">
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
                            <input type="hidden" id="idrehumano" name="idrehumano"
                            value="<?= $proyecto['idrehumano'] ?? '' ?>">
                            <div class="grid-container">
                                <div> <label>Nombre y Apellidos:</label>
                                    <select class="form-control" id="idusuario" name="idusuario" required onchange="actualizarCedula()">
                                        <?php while ($user = mysqli_fetch_assoc($usuarios)): ?>
                                            <option value="<?= $user['id'] ?>"
                                                    data-cedula="<?= htmlspecialchars($user['Identificacion']) ?>"
                                                    data-salario="<?= htmlspecialchars($user['salario_hora']) ?>"
                                                    <?= isset($proyecto['idusuario']) && $proyecto['id'] == $user['id'] ? 'selected' : '' ?>>
                                                <?= $user['nombre'] ?> <?= $user['apellidos'] ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div>
                                <label>Cédula:</label>
                                    <input type="text" class="form-control" id="cedula" name="cedula" readonly
                                    value="<?= isset($proyecto['identificacion']) ? htmlspecialchars($proyecto['identificacion']) : '' ?>">
                                </div>
                                <div>
                                    <label>Salario Base:</label>
                                    <input type="number" class="form-control" id="SalarioBase" name="SalarioBase" readonly
                                    value="<?= $proyecto['SalarioBase'] ?? '' ?>">                                
                                </div>
                                <div>
                                    <label>Horas trabajadas:</label>
                                    <input type="number" class="form-control" id="HorasT" name="HorasT" required
                                    value="<?= $proyecto['HorasT'] ?? '' ?>">
                                </div>
                                <div>
                                    <label>Salario Total:</label>
                                    <input type="number" class="form-control" id="SalarioT"name="SalarioT" readonly
                                    value ="<?= $proyecto['SalarioT'] ?? '' ?>">
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

    function actualizarCedula() {
        var usuarioSeleccionado = $('#idusuario').find(':selected');
        var cedula = usuarioSeleccionado.data('cedula');
        var salarioHora = usuarioSeleccionado.data('salario');
        var nombreApellido = usuarioSeleccionado.text(); 

        $('#cedula').val(cedula); 
        $('#SalarioBase').val(salarioHora); 
        $('#nombre_apellido').val(nombreApellido);
    }

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

    $("#modalProyecto input[name='idrehumano']").val(-1);
    $("#modalProyecto input[name='idusuario']").val("").change();
    $("#modalProyecto input[name='cedula']").val("");
    $("#modalProyecto textarea[name='HorasT']").val("");
    $("#modalProyecto input[name='SalarioBase']").val(""); 
    $("#modalProyecto input[name='SalarioT']").val(""); 

    $("#modalProyectoLabel").text("Editar recurso humano");
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Crear');
    // Cambiar el título del modal

    $("#modalProyectoLabel").text("Editar recurso humano");
    $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');

    $("#modalProyectoLabel").text("Nuevo recurso humano");
}

function cargarProyectos() {
    $.get("recursoh.php", function(data) {
        let nuevaTabla = $(data).find("#tablaProyectos tbody").html();
        $("#tablaProyectos tbody").html(nuevaTabla);
    });
}

//Funcion para abrir modal 
function abrirModalProyecto(idrehumano) {

    if (idrehumano != -1) {
        $.get("recursoh.php", {
                action: "editar",
                idrehumano: idrehumano
            })
            .done(function(data) {
                let proyecto = JSON.parse(data);

                // Llenar los campos del formulario en el modal
                $("#idrehumano").val(proyecto.idrehumano);
                $("#idusuario").val(proyecto.idusuario).change();
                $("#cedula").val(proyecto.cedula);
                $("#HorasT").val(proyecto.HorasT);
                $("#SalarioBase").val(proyecto.SalarioBase);
                $("#SalarioT").val(proyecto.SalarioT);

                // Cambiar el título del modal
                $("#modalProyectoLabel").text("Editar recurso humano");
                $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Actualizar');
            });
    } else {
        // Cambiar el título del modal para un nuevo recurso humano
        $("#modalProyectoLabel").text("Nuevo recurso humano");
        $("#btn-guardarProyecto").html('<i class="fas fa-check"></i> Guardar');
    }

    // Mostrar el modal
    $("#modalProyecto").modal('show');
}

function guardarProyecto() {
    console.log("Función guardarProyecto ejecutada");

    var id = $('#idrehumano').val();
    var idusuario = $('#idusuario').val();
    var cedula = $('#cedula').val();
    var HorasT = $('#HorasT').val();
    var SalarioT = $('#SalarioT').val();
    var SalarioBase = $('#SalarioBase').val();

    console.log({ id, idusuario, cedula, HorasT, SalarioT, SalarioBase }); 


    $.post("recursoh.php", {
            action: "guardar", 
            idrehumano: id,
            idusuario: idusuario,
            cedula: cedula,
            HorasT: HorasT,
            SalarioT: SalarioT,
            SalarioBase: SalarioBase
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
        })
        .fail(function(xhr, status, error) {
            console.error("Error en la solicitud:", error); // Depuración
            alert("Ocurrió un error al guardar el proyecto.");
        });
}

function eliminarProyecto(idrehumano) {
    if (confirm('¿Seguro que desea eliminar el recurso humano?')) {
        $.post("recursoh.php", {
                action: "eliminar",
                idrehumano: idrehumano
            })
            .done(function(response) {
                let data = JSON.parse(response);
                if (data.success) {
                    alert("Se eliminó el recurso humano correctamente");
                    document.location.href = document.location.href;
                } else {
                    alert("Error: " + data.error);
                }
            });
        }
}
$(document).ready(function() {
    // Ejecutar el cálculo cuando cambien los valores de salario base o horas trabajadas
    $('#SalarioBase, #HorasT').on('input', function() {
        calcularSalarioTotal();
    });
});

function calcularSalarioTotal() {
    var salarioBase = parseFloat($('#SalarioBase').val()) || 0; // Si está vacío, usa 0
    var horasTrabajadas = parseFloat($('#HorasT').val()) || 0; // Si está vacío, usa 0

    // Calcular el salario total
    var salarioTotal = salarioBase * horasTrabajadas;

    // Actualizar el campo de salario total
    $('#SalarioT').val(salarioTotal.toFixed(2)); // Mostrar con 2 decimales
}

</script>


<?php include("../../template/bottom.php"); ?>