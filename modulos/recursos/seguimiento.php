<?php
include("../../conn/conn.php");

// Obtener Todos del proyecto
$usuarios = mysqli_query($conn, "SELECT id, nombre, apellidos, Identificacion, salario_hora FROM tusuarios");
$resultado = mysqli_query($conn, "SELECT * FROM trefinanciero");   
$material =  mysqli_query($conn, "SELECT * FROM tmateriales"); 
$recursoh = mysqli_query($conn, "SELECT tre.*, CONCAT(tu.nombre, ' ', tu.apellidos) as nombre_apellido FROM trehumano as tre, tusuarios as tu WHERE tre.idusuario = tu.id");
    

if (isset($_POST['action']) && $_POST['action'] == 'aprobar') {
    $idrefinanciero = $_POST['idrefinanciero'];

    // Actualizar el estado del gasto a "Aprobado"
    $sql = "UPDATE trefinanciero SET estado = 'Aprobado' WHERE idrefinanciero = $idrefinanciero";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'denegar') {
    $idrefinanciero = $_POST['idrefinanciero'];

    // Actualizar el estado del gasto a "Denegado"
    $sql = "UPDATE trefinanciero SET estado = 'Denegado' WHERE idrefinanciero = $idrefinanciero";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
    }
    exit();
}
?>

<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Datos de materiales</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaMateriales" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre del material</th>
                            <th>Precio unitario</th>
                            <th>cantidad</th>
                            <th>precio Total</th>
                            <th>Descripcion</th>
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
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-success"
                                        onclick="aprobarGasto(<?= $fila['idmaterial'] ?>)">
                                        <i class="fa fa-check"></i> Aprobar
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="denegarGasto(<?= $fila['idmaterial'] ?>)">
                                        <i class="fa fa-times"></i> Denegar
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

<div class="container">
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
                                    <button class="btn btn-sm btn-success"
                                        onclick="aprobarGasto(<?= $fila['idrefinanciero'] ?>)">
                                        <i class="fa fa-check"></i> Aprobar
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="denegarGasto(<?= $fila['idrefinanciero'] ?>)">
                                        <i class="fa fa-times"></i> Denegar
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

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Datos de recurso humano</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tablarecuroh" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre y apellidos</th>
                            <th>Cedula</th>
                            <th>Salario Base</th>
                            <th>Horas Trabajadas</th>
                            <th>Salario Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php while ($fila = mysqli_fetch_assoc($recursoh   )): ?>    
                        <tr>
                            <td><?= $fila['nombre_apellido'] ?></td>
                            <td><?= $fila['cedula'] ?></td>
                            <td><?= $fila['SalarioBase'] ?></td>
                            <td><?= $fila['HorasT'] ?></td>
                            <td><?= $fila['SalarioT'] ?></td>
                            <td>
                                <div>
                                    <button class="btn btn-sm btn-success"
                                        onclick="aprobarGasto(<?= $fila['idrehumano'] ?>)">
                                        <i class="fa fa-check"></i> Aprobar
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="denegarGasto(<?= $fila['idrehumano'] ?>)">
                                        <i class="fa fa-times"></i> Denegar
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

</script>


<?php include("../../template/bottom.php"); ?>