<?php
include("../../conn/conn.php");

// Obtener lista de usuarios para el combo
$usuarios = mysqli_query($conn, "SELECT id, nombre,apellidos FROM tusuarios");

// Crear Proyecto
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $idusuario = $_POST['idusuario'];

    $sql = "INSERT INTO tproyectos (nombre, fechainicio, fechafin, descripcion, estado, idusuario) VALUES ('$nombre', '$fechainicio', '$fechafin', '$descripcion', '$estado', '$idusuario')";
    mysqli_query($conn, $sql);
    header("Location: proyectos.php");
}

// Eliminar Proyecto
if (isset($_GET['eliminar'])) {
    $idproyecto = $_GET['eliminar'];
    $sql = "DELETE FROM tproyectos WHERE idproyecto = $idproyecto";
    mysqli_query($conn, $sql);
    header("Location: proyectos.php");
}

// Obtener Proyecto para Editar
$proyecto = null;
if (isset($_GET['editar'])) {
    $idproyecto = $_GET['editar'];
    $resultado = mysqli_query($conn, "SELECT * FROM tproyectos WHERE idproyecto = $idproyecto");
    $proyecto = mysqli_fetch_assoc($resultado);
}

// Actualizar Proyecto
if (isset($_POST['actualizar'])) {
    $idproyecto = $_POST['idproyecto'];
    $nombre = $_POST['nombre'];
    $fechainicio = $_POST['fechainicio'];
    $fechafin = $_POST['fechafin'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];
    $idusuario = $_POST['idusuario'];

    $sql = "UPDATE tproyectos SET nombre='$nombre', fechainicio='$fechainicio', fechafin='$fechafin', descripcion='$descripcion', estado='$estado', idusuario='$idusuario' WHERE idproyecto=$idproyecto";
    mysqli_query($conn, $sql);
    header("Location: proyectos.php");
}

// Obtener Todos los Proyectos
$resultado = mysqli_query($conn, "SELECT * FROM tproyectos");
?>

<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gestión de Proyectos</h6>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="idproyecto" value="<?= $proyecto['idproyecto'] ?? '' ?>">
                <div class="grid-container">
                    <div>
                        <label>Nombre:</label>
                        <input type="text" class="form-control" name="nombre" required value="<?= $proyecto['nombre'] ?? '' ?>">
                    </div>
                    <div>
                    
                        <label>Fecha Inicio:</label>
                        <input type="date" class="form-control" name="fechainicio" required value="<?= $proyecto['fechainicio'] ?? '' ?>">
                    </div>
                    <div>
                        <label>Fecha Fin:</label>
                        <input type="datetime-local" class="form-control" name="fechafin" value="<?= $proyecto['fechafin'] ?? '' ?>">
                    </div>
                    <div>
                        <label>Descripción:</label>
                        <textarea class="form-control" name="descripcion"> <?= $proyecto['descripcion'] ?? '' ?></textarea>
                    </div>
                    <div>
                        <label>Estado:</label>
                        <select class="form-control" name="estado">
                            <option value="pendiente"
                                <?= isset($proyecto['estado']) && $proyecto['estado'] == 'pendiente' ? 'selected' : '' ?>>
                                Pendiente</option>
                            <option value="en proceso"
                                <?= isset($proyecto['estado']) && $proyecto['estado'] == 'en proceso' ? 'selected' : '' ?>>
                                En Proceso</option>
                            <option value="finalizado"
                                <?= isset($proyecto['estado']) && $proyecto['estado'] == 'finalizado' ? 'selected' : '' ?>>
                                Finalizado</option>
                        </select>
                    </div>
                    <div>
                        <label>Usuario:</label>
                        <select class="form-control" name="idusuario" required>
                            <?php while ($user = mysqli_fetch_assoc($usuarios)): ?>
                            <option value="<?= $user['id'] ?>"
                                <?= isset($proyecto['idusuario']) && $proyecto['idusuario'] == $user['id'] ? 'selected' : '' ?>>
                                <?= $user['nombre'] ?> <?= $user['apellidos'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit"  name="<?= $proyecto ? 'actualizar' : 'crear' ?>">
                    <?= $proyecto ? 'Actualizar' : 'Crear' ?>
                </button>
            </form>
        </div>
    </div>

    <h3>Lista de Proyectos</h3>
    <div class="grid-table">
        <div class="grid-header">Nombre</div>
        <div class="grid-header">Fecha Inicio</div>
        <div class="grid-header">Fecha Fin</div>
        <div class="grid-header">Estado</div>
        <div class="grid-header">Acciones</div>
        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
        <div><?= $fila['nombre'] ?></div>
        <div><?= $fila['fechainicio'] ?></div>
        <div><?= $fila['fechafin'] ?></div>
        <div><?= $fila['estado'] ?></div>
        <div>
            <button class="btn btn-sm btn-secondary"
                onclick="window.location.href='proyectos.php?editar=<?= $fila['idproyecto'] ?>'">
                <i class="fa fa-pen"></i>
            </button>
            <button class="btn btn-sm btn-danger"
                onclick="if(confirm('¿Eliminar proyecto?')) window.location.href='proyectos.php?eliminar=<?= $fila['idproyecto'] ?>'">
                <i class="fa fa-trash"></i>
            </button>
        </div>
        <?php endwhile; ?>
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

<?php include("../../template/bottom.php"); ?>