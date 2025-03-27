<?php 
include("conn/conn.php");

include("template/rootTop.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
<h1 class="h3 mb-0 text-gray-800">Materiales</h1>
<a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
        class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
</div>

<?php
if (isset($_POST['action']) && $_POST['action'] == 'guardar_material' ) { 
    $nom = $_POST['nom'];
    $pu = $_POST['pu'];
    $pu = $_POST['pt'];
    $can = $_POST['can'];
    $can = $_POST['des'];

    $sql = "INSERT INTO cliente (identificacion, pu, telefono, can, estado) VALUES ('$nom', '$pu', '$pt', '$can', '$des')";
    $query = mysqli_query($conn, $sql);

    exit();
}
?>

    <div class="container-fluid">
    <div class="row">
        <div class="col-3">

            <div class="mb-3">
            <label for="nom" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nom"required>
            </div>
            </div>

            <div class="col-3">
            <div class="mb-3">
            <label for="pu" class="form-label">Precio Unitario</label>
            <input type="text" class="form-control" id="pu"required>
            </div>
            </div>
            
            <div class="col-3">
            <div class="mb-3">
            <label for="pt" class="form-label">Precio Total</label>
            <input type="text" class="form-control" id="pt"required>
            </div>
            </div>

            <div class="col-3">
            <div class="mb-3">
            <label for="can" class="form-label">Cantidad</label>
            <input type="text" class="form-control" id="can"required>
            </div>
            </div>

            <div class="col-6">
            <div class="mb-3">
            <label for="des" class="form-label">Descripción</label>
            <textarea type="text" class="form-control" id="des"required></textarea>
            </div>
            </div>
            <button class="btn btn-sm btn-block btn-secondary" onclick="guardarmaterial()">Guardar</button>
        
        

    <script>
        function guardarmaterial (){

            var nom = $('#nom').val();
            var pu = $('#pu').val();
            var pt = $('#pt').val();
            var can = $('#can').val();
            var des = $('#des').val();

            $.post( "materiales.php", { action: "guardar_material", nom: nom, pu: pu, pt: pt, can: can, des: des })
            .done(function( data ) {
                cargarmateriales();
                $('#nom').val('');
                $('#pu').val('');   
                $('#pt').val('');
                $('#can').val('');
                $('#des').val('');
            });
        }
    </script>                       

<?php include("template/bottom.php"); ?>