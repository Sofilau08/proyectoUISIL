<?php include("../../conn/conn.php"); 

if ($_POST['action'] == 'agregar_usuario'){
    ?>

    <label for="identificacion">Identificación</label>
    <input type="text" class="form-control" id="identificacion" maxlength="20" required>
    </div>
    <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" class="form-control" id="nombre" maxlength="25" required>
    </div>
    <div class="form-group">
    <label for="apellidos">Apellidos</label>
    <input type="text" class="form-control" id="apellidos" maxlength="75" required>
    </div>
    <div class="form-group">
    <label for="telefono">Teléfono</label>
    <input type="text" class="form-control" id="telefono" maxlength="20" required>
    </div>
    <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" maxlength="100" required>
    </div>
    <div class="form-group">
    <label for="usuario">Usuario</label>
    <input type="text" class="form-control" id="usuario" maxlength="15" required>
    </div>
    <div class="form-group">
    <label for="password">Contraseña</label>
    <input type="password" class="form-control" id="password" maxlength="270" required>
    </div>
    <div class="form-group">
    <label for="direccion">Dirección</label>
    <textarea class="form-control" id="direccion" rows="3" required></textarea>
    </div>
    <div class="form-group">
    <label for="fechaNac">Fecha de Nacimiento</label>
    <input type="date" class="form-control" id="fechaNac" required>
    </div> 
    <button type="submit" onclick="guardarUsuario()" class="btn btn-primary">Enviar</button>

    <?php 
    exit();
}

if ($_POST['action'] == 'editar_usuario'){
    $id = $_POST['param'];

    $sqlUsuario = "SELECT * FROM tusuarios WHERE id = $id";
    $queryUsuario = mysqli_query($conn, $sqlUsuario);

    while($rowUsuario=mysqli_fetch_assoc($queryUsuario)){
        ?>
            <input type="hidden" name="id" id="editar_id" value="<?=$rowUsuario['id']?>">
            <label for="identificacion">Identificación</label>
            <input type="text" class="form-control" id="editar_identificacion" maxlength="20" required value="<?=$rowUsuario['identificacion']?>">
            </div>
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" class="form-control" id="editar_nombre" maxlength="25" required  value="<?=$rowUsuario['nombre']?>">
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" class="form-control" id="editar_apellidos" maxlength="75" required  value="<?=$rowUsuario['apellidos']?>">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" class="form-control" id="editar_telefono" maxlength="20" required  value="<?=$rowUsuario['telefono']?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="editar_email" maxlength="100" required  value="<?=$rowUsuario['email']?>">
            </div>
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control" id="editar_usuario" maxlength="15" required  value="<?=$rowUsuario['usuario']?>">
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="editar_password" maxlength="270" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección</label>
                <textarea class="form-control" id="editar_direccion" rows="3" required ><?=$rowUsuario['direccion']?></textarea>
            </div>
            <div class="form-group">
                <label for="fechaNac">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="editar_fechaNac" required  value="<?=$rowUsuario['fechaNac']?>">
            </div> 
            <button type="submit" onclick="modificarUsuario()" class="btn btn-primary">Modificar</button>


        <script>
        function modificarUsuario(){
            var id = $('#editar_id').val();
            var identificacion = $('#editar_identificacion').val();
            var nombre = $('#editar_nombre').val();
            var apellidos = $('#editar_apellidos').val();
            var telefono = $('#editar_telefono').val();
            var email = $('#editar_email').val();
            var usuario = $('#editar_usuario').val();
            var password = $('#editar_password').val();
            var direccion = $('#editar_direccion').val();
            var fNac = $('#editar_fNac').val();

            $.post("usuarios.php", { action: "modificar_usuario", 
                id: id,
                identificacion: identificacion,
                nombre: nombre, 
                apellidos: apellidos,
                telefono: telefono,
                email: email,
                usuario: usuario,    
                password: password,
                direccion: direccion,
                fNac: fNac
            })
            .done(function( data ) {
                cargarUsuarios();
                $('#modal').modal('hide');
            });
        }
        </script>
        <?php 
    }

    exit();
}

if ($_POST['action'] == 'eliminar_usuario'){
    $id = $_POST['id'];

    $sql = "UPDATE tusuarios SET estado = 2 WHERE id = $id";
    $query = mysqli_query($conn, $sql);

    exit();
}

if ($_POST['action'] == 'cargar_usuarios'){

    $sql = "SELECT * FROM tusuarios WHERE estado = 1";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) == 0){
        ?>
        <div class="row">
            <div class="col-12 text-center">
                No hay usuarios.
            </div>
        </div>
        <?php
    }else{
        ?>
        <table class="table table-sm">
            <thead>
                <th></th>
                <th>Identificación</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Teléfono</th>
                <th>Email</th>
            </thead>
            <tbody>
                <?php while($row=mysqli_fetch_assoc($query)){ ?>
                    <tr>
                        <th>
                            <button class="btn btn-sm btn-secondary" onclick="cargarModal('editar_usuario', 'Editar usuario', <?=$row['id']?>, 'md')"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?=$row['id']?>)"><i class="fa fa-trash"></i></button>
                            <button class="btn btn-sm btn-secondary" onclick="cargarModal('asignar_privilegios', 'Asignar privilegios', <?=$row['id']?>, 'md')"><i class="fa fa-bars"></i></button>
                        </th>
                        <td><?=$row['identificacion']?></td>
                        <td><?=$row['nombre'].' '.$row['apellidos']?></td>
                        <td><?=$row['usuario']?></td>
                        <td><?=$row['telefono']?></td>
                        <td><?=$row['email']?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
    }

    exit();
}

if ($_POST['action'] == 'guardar_usuario'){
    $identificacion = $_POST['identificacion'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $password = hash('sha256', $_POST['password']);
    $direccion = $_POST['direccion'];
    $fNac = $_POST['fNac'];

    $sql = "INSERT tusuarios VALUES (null, '$identificacion', '$nombre', '$apellidos', '$telefono', '$email', '$usuario', '$password', '$direccion', '$fNac', '', '', 1)";
    $query = mysqli_query($conn, $sql);

    exit();
}

if ($_POST['action'] == 'modificar_usuario'){
    $id = $_POST['id'];
    $identificacion = $_POST['identificacion'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $usuario = $_POST['usuario'];
    $direccion = $_POST['direccion'];
    $fNac = $_POST['fNac'];

    if ($_POST['password'] != ''){
        $password = hash('sha256', $_POST['password']);
        $sql = "UPDATE tusuarios SET 
        identificacion = '$identificacion', 
        nombre = '$nombre', 
        apellidos = '$apellidos', 
        telefono = '$telefono', 
        email = '$email', 
        usuario = '$usuario', 
        password = '$password',
        direccion = '$direccion', 
        fechaNac = '$fNac' 
        WHERE id = $id";
    }else{
        $sql = "UPDATE tusuarios SET 
        identificacion = '$identificacion', 
        nombre = '$nombre', 
        apellidos = '$apellidos', 
        telefono = '$telefono', 
        email = '$email', 
        usuario = '$usuario', 
        direccion = '$direccion', 
        fechaNac = '$fNac' 
        WHERE id = $id";
    }
    
    $query = mysqli_query($conn, $sql);

    exit();
}


if ($_POST['action'] == 'asignar_privilegios'){
    $id = $_POST['param'];

    $sql = "SELECT * FROM tprivilegios WHERE estado = 1";
    $query = mysqli_query($conn, $sql);

    ?>
    <table class="table table-sm">
        <thead>
            <th></th>
            <th>Nombre de privilegio</th>
        </thead>
    <?php 
    while($row=mysqli_fetch_assoc($query)){
        $idPrivilegio = $row['id'];
        $lotiene = 0;
        $sqlUsuario = "SELECT * FROM tprivilegiosusuario WHERE idUsuario = $id AND idPrivilegio = $idPrivilegio";
        $queryUsuario = mysqli_query($conn, $sqlUsuario);
        if (mysqli_num_rows($queryUsuario) != 0){
            $lotiene = 1;
        }
        ?>
        <tr>
            <td><input type="checkbox" <?php if ($lotiene == 1){echo 'checked';}?> onclick="asignarPrivilegio(<?=$id?>, <?=$idPrivilegio?>)" name="privilegio_<?=$row['id']?>" id="privilegio_<?=$row['id']?>"></td>
            <td><?=$row['nombre']?></td>
        </tr>
        <?php 
    }
    ?>
    </table>
    <?php 
    exit();
}

if ($_POST['action'] == 'asignar_privilegio'){
    $idUsuarioPrivilegio = $_POST['idUsuario'];
    $idPrivilegio = $_POST['idPrivilegio'];
    $estado = $_POST['estado'];

    if ($estado == 1){
        $sql = "INSERT INTO tprivilegiosusuario VALUES ($idUsuarioPrivilegio, $idPrivilegio)";
    }else{
        $sql = "DELETE FROM tprivilegiosusuario WHERE idUsuario = $idUsuarioPrivilegio AND idPrivilegio = $idPrivilegio";
    }
    $query = mysqli_query($conn, $sql);
    exit();
}

?>


<?php include("../../template/top.php"); ?>

<div class="text-right"><button class="btn btn-sm btn-secondary mb-3" onclick="cargarModal('agregar_usuario', 'Agregar usuario')"><i class="fa fa-plus"></i> Agregar usuario</button></div>
<div class="row">
    <div class="col-12">
        <div id="usuarios"></div>
    </div>
</div>

<script>
function cargarUsuarios(){
    $.post("usuarios.php", { action: "cargar_usuarios" })
    .done(function( data ) {
        $('#usuarios').html(data);
    });
}

cargarUsuarios();

function guardarUsuario(){
    var identificacion = $('#identificacion').val();
    var nombre = $('#nombre').val();
    var apellidos = $('#apellidos').val();
    var telefono = $('#telefono').val();
    var email = $('#email').val();
    var usuario = $('#usuario').val();
    var password = $('#password').val();
    var direccion = $('#direccion').val();
    var fNac = $('#fNac').val();

    $.post("usuarios.php", { action: "guardar_usuario", 
        identificacion: identificacion,
        nombre: nombre, 
        apellidos: apellidos,
        telefono: telefono,
        email: email,
        usuario: usuario,    
        password: password,
        direccion: direccion,
        fNac: fNac
    })
    .done(function( data ) {
        cargarUsuarios();
        $('#modal').modal('hide');
    });
}

function eliminarUsuario(id){
    Swal.fire({
    title: "¿Estás seguro(a)?",
    text: "Vas a eliminar un usuario",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, eliminalo"
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("usuarios.php", { action: "eliminar_usuario", id: id })
            .done(function( data ) {
                cargarUsuarios();
            });
        }
    });
    
}

function asignarPrivilegio(idUsuario, idPrivilegio){
    if($("#privilegio_"+idPrivilegio).is(':checked')) { 
        var estado = 1;
    }else{
        var estado = 0;
    }

    $.post("usuarios.php", { action: "asignar_privilegio", idUsuario: idUsuario, idPrivilegio: idPrivilegio, estado: estado })
    .done(function(data){
        //nothing
    });
}

</script>

<?php include("../../template/bottom.php"); ?>