<?php include("../../conn/conn.php"); 

if ($_POST['action'] == 'agregar_privilegio'){
    ?>

    <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" id="nombre" maxlength="25" required>
    </div>
    <div class="form-group">
    <label for="url">URL</label>
    <input type="text" class="form-control" id="url" maxlength="75" required>
    </div>
    <div class="form-group">
    <label for="icono">Ícono</label>
    <input type="text" class="form-control" id="icono" maxlength="75" required>
    </div>
    <button type="submit" onclick="guardarPrivilegio()" class="btn btn-primary">Guardar privilegio</button>

    <?php 
    exit();
}

if ($_POST['action'] == 'editar_privilegio'){
    $id = $_POST['param'];

    $sqlPrivilegio = "SELECT * FROM tprivilegios WHERE id = $id";
    $queryPrivilegio = mysqli_query($conn, $sqlPrivilegio);

    while($rowPrivilegio=mysqli_fetch_assoc($queryPrivilegio)){
        ?>
            <input type="hidden" name="id" id="editar_id" value="<?=$rowPrivilegio['id']?>">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="editar_nombre" maxlength="25" required  value="<?=$rowPrivilegio['nombre']?>">
            </div>
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" class="form-control" id="editar_url" maxlength="75" required  value="<?=$rowPrivilegio['url']?>">
            </div>
            <div class="form-group">
                <label for="icono">Ícono</label>
                <input type="text" class="form-control" id="editar_icono" maxlength="20" required  value="<?=$rowPrivilegio['icono']?>">
            </div>
            <button type="submit" onclick="modificarPrivilegio()" class="btn btn-primary">Modificar privilegio</button>


        <script>
        function modificarPrivilegio(){
            var id = $('#editar_id').val();
            var nombre = $('#editar_nombre').val();
            var url = $('#editar_url').val();
            var icono = $('#editar_icono').val();

            $.post("privilegios.php", { action: "modificar_privilegio", 
                id: id,
                nombre: nombre, 
                url: url,
                icono: icono
            })
            .done(function( data ) {
                cargarPrivilegios();
                $('#modal').modal('hide');
            });
        }
        </script>
        <?php 
    }

    exit();
}

if ($_POST['action'] == 'eliminar_privilegio'){
    $id = $_POST['id'];

    $sql = "UPDATE tprivilegios SET estado = 2 WHERE id = $id";
    $query = mysqli_query($conn, $sql);

    exit();
}

if ($_POST['action'] == 'cargar_privilegios'){

    $sql = "SELECT * FROM tprivilegios WHERE estado = 1";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) == 0){
        ?>
        <div class="row">
            <div class="col-12 text-center">
                No hay privilegios.
            </div>
        </div>
        <?php
    }else{
        ?>
        <table class="table table-sm">
            <thead>
                <th></th>
                <th>Id</th>
                <th>Nombre</th>
                <th>URL</th>
            </thead>
            <tbody>
                <?php while($row=mysqli_fetch_assoc($query)){ ?>
                    <tr>
                        <th>
                            <button class="btn btn-sm btn-secondary" onclick="cargarModal('editar_privilegio', 'Editar privilegio', <?=$row['id']?>, 'md')"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarPrivilegio(<?=$row['id']?>)"><i class="fa fa-trash"></i></button>
                        </th>
                        <td><?=$row['id']?></td>
                        <td><i class="fa <?=$row['icono']?>"></i> <?=$row['nombre']?></td>
                        <td><?=$row['url']?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
    }

    exit();
}

if ($_POST['action'] == 'guardar_privilegio'){
    $nombre = $_POST['nombre'];
    $url = $_POST['url'];
    $icono = $_POST['icono'];

    echo $sql = "INSERT tprivilegios VALUES (null, '$nombre', '$url', '$icono', 1)";
    $query = mysqli_query($conn, $sql);

    exit();
}

if ($_POST['action'] == 'modificar_privilegio'){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $url = $_POST['url'];
    $icono = $_POST['icono'];
    
    $sql = "UPDATE tprivilegios SET 
    nombre = '$nombre', 
    url = '$url', 
    icono = '$icono' 
    WHERE id = $id";
    
    $query = mysqli_query($conn, $sql);

    exit();
}

?>


<?php include("../../template/top.php"); ?>

<div class="text-right"><button class="btn btn-sm btn-secondary mb-3" onclick="cargarModal('agregar_privilegio', 'Agregar privilegio')"><i class="fa fa-plus"></i> Agregar privilegio</button></div>
<div class="row">
    <div class="col-12">
        <div id="privilegios"></div>
    </div>
</div>

<script>
function cargarPrivilegios(){
    $.post("privilegios.php", { action: "cargar_privilegios" })
    .done(function( data ) {
        $('#privilegios').html(data);
    });
}

cargarPrivilegios();

function guardarPrivilegio(){
    var nombre = $('#nombre').val();
    var url = $('#url').val();
    var icono = $('#icono').val();
    
    $.post("privilegios.php", { action: "guardar_privilegio", 
        nombre: nombre, 
        url: url,
        icono: icono
    })
    .done(function( data ) {
        cargarPrivilegios();
        $('#modal').modal('hide');
    });
}

function eliminarPrivilegio(id){
    Swal.fire({
    title: "¿Estás seguro(a)?",
    text: "Vas a eliminar un privilegio",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, eliminalo"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("usuarios.php", { action: "eliminar_privilegio", id: id })
            .done(function( data ) {
                cargarPrivilegios();
            });
        }
    });
}
</script>

<?php include("../../template/bottom.php"); ?>