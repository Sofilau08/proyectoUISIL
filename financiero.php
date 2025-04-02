<!--coneccion a la base de datos y plantilla-->
<?php 
include("conn/conn.php");

include("template/rootTop.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
<h1 class="h3 mb-0 text-gray-800">Financiero</h1>
<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
        class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
</div>
<!-- Guardar los datos -->
<?php
if (isset($_POST['action']) && $_POST['action'] == 'guardar_financia' ) { 
    $gas = $_POST['gas'];
    $tgas = $_POST['tgas'];
    $mongas = $_POST['mongas'];
    $mongas = $_POST['des'];

    $sql = "INSERT INTO cliente (identificacion, tgas, telefono, mongas, estado) VALUES ('$gas', '$tgas', '$mongas', '$des')";
    $query = mysqli_query($conn, $sql);

    exit();
}
?>
<!-- Agregar los datos -->
    <div class="container-fluid">
    <div class="row">
        <div class="col-3">

            <div class="mb-3">
            <label for="gas" class="form-label">Fecha del Gasto</label>
            <input type="text" class="form-control" id="gas"required>
            </div>
            </div>

            <div class="col-3">
            <div class="mb-3">
            <label for="tgas" class="form-label">Tipo de Gasto</label>
            <input type="text" class="form-control" id="tgas"required>
            </div>
            </div>

            <div class="col-3">
            <div class="mb-3">
            <label for="mongas" class="form-label">Monto Gastado</label>
            <input type="text" class="form-control" id="mongas"required>
            </div>
            </div>

            <div class="col-6">
            <div class="mb-3">
            <label for="des" class="form-label">Descripción</label>
            <textarea type="text" class="form-control" id="des"required></textarea>
            </div>
            </div>

            <button class="btn btn-sm btn-block btn-secondary" onclick="guardarfinanciero()">Guardar</button>        
        
    <script>
        // Funcion para guardar los datos   
        function guardarfinanciero (){

            var gas = $('#gas').val();
            var tgas = $('#tgas').val();
            var mongas = $('#mongas').val();
            var des = $('#des').val();

            $.post( "materiales.php", { action: "guardar_financia", gas: gas, tgas: tgas, mongas: mongas, des: des })
            .done(function( data ) {
                cargarmateriales();
                $('#gas').val('');
                $('#tgas').val('');   
                $('#mongas').val('');
                $('#des').val('');
            });
        }
    </script>                       

<?php include("template/bottom.php"); ?>