<!-- Conexión a la base de datos y plantilla -->
<?php 
include("conn/conn.php");
include("template/rootTop.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Financiero</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
    </a>
</div>

<!-- Guardar los datos -->
<?php
if (isset($_POST['action']) && $_POST['action'] == 'guardar_financiero') { 
    $nom = $_POST['nom'];
    $apel = $_POST['apel'];
    $ced = $_POST['ced'];
    $salario_b = $_POST['salario_base'];
    $horas_t = $_POST['horas_t'];
    $salario_pagar = $_POST['salario_pagar'];

    $sql = "INSERT INTO cliente (identificacion, apel, telefono, horas_t, estado) VALUES ('$ced', '$apel', '', '$horas_t', '$salario_pagar')";
    $query = mysqli_query($conn, $sql);
    exit();
}
?>

<!-- Agregar los datos -->
<div class="container-fluid">
    <div class="row">
        <div class="col-3">
            <?php
            $sql = "SELECT id FROM tusuarios";
            $resultado = $conn->query($sql);
            ?>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Seleccionar ID Usuario
                </button>
                <ul class="dropdown-menu">
                    <?php
                    if ($resultado->num_rows > 0) {
                        while ($fila = $resultado->fetch_assoc()) {
                            echo '<li><a class="dropdown-item" href="#" onclick="cargarDatosUsuario(' . $fila["id"] . ')">ID: ' . $fila["id"] . '</a></li>';
                        }
                    } else {
                        echo '<li><a class="dropdown-item" href="#">No hay usuarios</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-3">
            <div class="mb-3">
                <label for="ced" class="form-label">Número de cédula</label>
                <input type="text" class="form-control" id="ced" readonly>
            </div>
        </div>
        <div class="col-3">
            <div class="mb-3">
                <label for="nom" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nom" readonly>
            </div>
        </div>
        <div class="col-3">
            <div class="mb-3">
                <label for="apel" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apel" readonly>
            </div>
        </div>
        <div class="col-3">
            <div class="mb-3">
                <label for="salario_base" class="form-label">Salario Base</label>
                <input type="text" class="form-control" id="salario_base" readonly>
            </div>
        </div>
        <div class="col-3">
            <div class="mb-3">
                <label for="horas_t" class="form-label">Horas Trabajadas</label>
                <input type="text" class="form-control" id="horas_t" required>
            </div>
        </div>
        <div class="col-6">
            <div class="mb-3">
                <label for="salario_pagar" class="form-label">Salario a Pagar</label>
                <textarea class="form-control" id="salario_pagar" required></textarea>
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-sm btn-block btn-secondary" onclick="guardarfinanzas()">Guardar</button>
        </div>
    </div>
</div>

<script>
    function guardarfinanzas() {
        var nom = $('#nom').val();
        var apel = $('#apel').val();
        var ced = $('#ced').val();
        var salario_base = $('#salario_base').val();
        var horas_t = $('#horas_t').val();
        var salario_pagar = $('#salario_pagar').val();

        $.post("financiero.php", {
            action: "guardar_financiero",
            nom: nom,
            apel: apel,
            ced: ced,
            salario_base: salario_base,
            horas_t: horas_t,
            salario_pagar: salario_pagar
        })
        .done(function(data) {
            $('#nom, #apel, #ced, #salario_base, #horas_t, #salario_pagar').val('');
        });
    }

    function cargarDatosUsuario(idUsuario) {
        fetch('obtener_usuario.php?id=' + idUsuario)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('ced').value = data.cedula;
                    document.getElementById('nom').value = data.nombre;
                    document.getElementById('apel').value = data.apellidos;
                    document.getElementById('salario_base').value = data.salario_base;
                }
            })
            .catch(error => console.error('Error al obtener datos:', error));
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include("template/bottom.php"); ?>
