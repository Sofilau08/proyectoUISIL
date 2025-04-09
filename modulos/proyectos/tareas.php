<?php include("../../conn/conn.php");
?>


<?php

// Obtener todas las tareas
$resultado = mysqli_query($conn, "SELECT * FROM ttareas");

?>

<?php include("../../template/top.php"); ?>


<div class="container">
    <h2 class="text-center my-4">Lista de Tareas</h2>
    <div class="row">
        <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
            <?php
                // Obtener la fecha actual
                $hoy = date("Y-m-d");

                // Definir color del borde
                $colorBorde = (strtotime($fila['fechafin']) >= strtotime($hoy)) ? "border-success" : "border-danger";
            ?>
            <div class="col-md-4 mb-3">
                <div class="card <?= $colorBorde ?>" style="border-width: 3px;">
                    <div class="card-body">
                        <h5 class="card-title"><?= $fila['titulo'] ?></h5>
                        <p class="card-text"><strong>Descripcion: </strong><?= $fila['descripcion'] ?></p>
                        <p><strong>Fecha Inicio:</strong> <?= $fila['fechainicio'] ?></p>
                        <p><strong>Fecha Fin:</strong> <?= $fila['fechafin'] ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>


<?php include("../../template/bottom.php"); ?>


<?php include("../../template/bottom.php"); ?>