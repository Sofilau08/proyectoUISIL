<?php include("../../conn/conn.php"); ?>
<?php
if (isset($_COOKIE['usuario'])) {
    $idusuario = $_COOKIE['usuario'];
    // ya podés usar $idusuario para consultas o mostrar info
} else {
    // redirigir si no hay cookie (usuario no ha iniciado sesión)
    header('Location: login.php');
    exit();
}
?>

<?php $resultado = mysqli_query($conn, "SELECT * FROM ttareas WHERE idusuario = $idusuario AND estadotarea != 'finalizado'");




$stmtCheck = $conn->prepare("SELECT estadotarea FROM ttareas WHERE idtarea = ?");
$stmtCheck->bind_param("i", $idtarea);
$stmtCheck->execute();
$stmtCheck->bind_result($estadoActual);
$stmtCheck->fetch();
$stmtCheck->close();

if ($estadoActual == 'finalizado') {
    echo json_encode(["success" => false, "error" => "No se puede modificar una tarea finalizada"]);
    exit();
}


if ($_POST['action'] == 'actualizar_tarea') {
    try{
        $idtarea = $_POST['idtarea'];
        $comentario = $_POST['comentario']; 
        $estadotarea = $_POST['estadotarea'];

        $stmt = $conn->prepare("UPDATE ttareas SET comentario_respuesta = ?, estadotarea = ? WHERE idtarea = ?");
        $stmt->bind_param("ssi", $comentario, $estadotarea, $idtarea);
        
        $stmt->execute();

        echo json_encode(["success" => true]);
    
    } catch(Exception $e){
        echo json_encode(["success" => false, "error" => "ERROR"]);
    }
    exit();

}
?>

<?php include("../../template/top.php"); ?>

<!-- Bootstrap CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
.borde-verde {
    border: 4px solid #198754 !important;
    border-radius: 6px;
}

.borde-rojo {
    border: 4px solid #dc3545 !important;
    border-radius: 6px;
}
</style>

<div class="container mb-4">
    <div class="d-flex justify-content-start align-items-center" id="botonesEstado">
        <a class="btn btn-success mr-2" href="tareasfinalizadas.php">
            <i class="fas fa-history"></i> Historial de Tareas
        </a>
    </div>
</div>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tareas</h6>
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionTareas">
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>

            <?php
                $hoy = date("Y-m-d");
                $bordeClase = (strtotime($fila['fechafin']) >= strtotime($hoy)) ? "borde-verde" : "borde-rojo";
                $id = $fila['idtarea'];
                $headingId = "heading$id";
                $collapseId = "collapse$id";
                $esFinalizado = $fila['estadotarea'] == 'finalizado';
        $disabledAttr = $esFinalizado ? 'disabled' : '';
            ?>
            <div class="accordion-item <?= $bordeClase ?> mb-3">
                <h2 class="accordion-header" id="<?= $headingId ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                        <strong><?= $fila['titulo'] ?></strong> - <?= $fila['fechafin'] ?>
                    </button>
                </h2>
                <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>"
                    data-bs-parent="#accordionTareas">
                    <div class="accordion-body">
                        <p><strong>Descripción:</strong> <?= $fila['descripcion'] ?></p>
                        <p><strong>Fecha Inicio:</strong> <?= $fila['fechainicio'] ?></p>
                        <p><strong>Fecha Fin:</strong> <?= $fila['fechafin'] ?></p>

                        <form class="form-actualizar-tarea" data-id="<?= $fila['idtarea'] ?>">
                            <div class="mb-3">
                                <label for="comentarioRespuesta" class="form-label">Comentario de Respuesta</label>
                                <textarea class="form-control" id="comentarioRespuesta<?= $id ?>"
                                    name="comentario_respuesta" rows="3" placeholder="Escribe aquí tu comentario..."
                                    <?= $disabledAttr ?>><?= $fila['comentario_respuesta'] ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Estado de la Tarea:</strong></label>
                                <select class="form-select estadotarea" id="estadoTarea<?= $id ?>" <?= $disabledAttr ?>>
                                    <option value="pendiente"
                                        <?= $fila['estadotarea'] == 'pendiente' ? 'selected' : '' ?>>
                                        Pendiente</option>
                                    <option value="en proceso"
                                        <?= $fila['estadotarea'] == 'en proceso' ? 'selected' : '' ?>>En Proceso
                                    </option>
                                    <option value="finalizado"
                                        <?= $fila['estadotarea'] == 'finalizado' ? 'selected' : '' ?>>Finalizado
                                    </option>
                                </select>
                            </div>

                            <button type="button" class="btn btn-success" id="btn-guardarGastos"
                                onclick="guardarCambiosTarea(<?= $id ?>)" <?= $disabledAttr ?>>
                                <i class="fas fa-check"></i> Guardar cambios
                            </button>

                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

    </div>
</div>



<script>
function guardarCambiosTarea(idtarea) {
    var estadotarea = $('#estadoTarea' + idtarea).val();
    var comentario_respuesta = $('#comentarioRespuesta' + idtarea).val();

    $.post("tareas.php", {
        action: "actualizar_tarea",
        idtarea: idtarea,
        comentario: comentario_respuesta,
        estadotarea: estadotarea
    }, function(response) {
        console.log("Respuesta del servidor:", response);
        try {
            let data = JSON.parse(response);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Tarea actualizada!',
                    text: 'La tarea se actualizó correctamente.',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    location
                .reload(); 
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar tarea',
                    text: data.error || 'Ocurrió un problema al actualizar la tarea.'
                });
            }
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: 'Error inesperado',
                text: 'No se pudo interpretar la respuesta del servidor.'
            });
        }
    });
}
</script>


<?php include("../../template/bottom.php"); ?>