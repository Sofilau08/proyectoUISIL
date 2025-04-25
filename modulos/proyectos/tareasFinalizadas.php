<?php include("../../conn/conn.php"); ?>
<?php
if (isset($_COOKIE['usuario'])) {
    $idusuario = $_COOKIE['usuario'];
} else {
    header('Location: login.php');
    exit();
}

$resultado = mysqli_query($conn, "SELECT * FROM ttareas WHERE idusuario = $idusuario AND estadotarea = 'finalizado'");
?>

<?php include("../../template/top.php"); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="container mb-4">
    <a href="tareas.php" class="btn btn-primary mb-3">
        <i class="fas fa-arrow-left"></i> Volver a tareas pendientes
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tareas Finalizadas</h6>
    </div>
    <div class="card-body">
        <div class="accordion" id="accordionFinalizadas">
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
            <?php
                $id = $fila['idtarea'];
                $headingId = "headingFinal$id";
                $collapseId = "collapseFinal$id";
            ?>
            <div class="accordion-item mb-3 border border-success rounded">
                <h2 class="accordion-header" id="<?= $headingId ?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#<?= $collapseId ?>" aria-expanded="false" aria-controls="<?= $collapseId ?>">
                        <strong><?= $fila['titulo'] ?></strong> - <?= $fila['fechafin'] ?>
                    </button>
                </h2>
                <div id="<?= $collapseId ?>" class="accordion-collapse collapse" aria-labelledby="<?= $headingId ?>"
                    data-bs-parent="#accordionFinalizadas">
                    <div class="accordion-body">
                        <p><strong>Descripción:</strong> <?= $fila['descripcion'] ?></p>
                        <p><strong>Fecha Inicio:</strong> <?= $fila['fechainicio'] ?></p>
                        <p><strong>Fecha Fin:</strong> <?= $fila['fechafin'] ?></p>
                        <p><strong>Comentario:</strong> <?= $fila['comentario_respuesta'] ?></p>
                        <p><strong>Estado:</strong> <?= $fila['estadotarea'] ?></p>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include("../../template/bottom.php"); ?>
