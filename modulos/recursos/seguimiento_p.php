<?php
include("../../conn/conn.php");

// Obtener la suma total de cada tabla
$resultado = mysqli_query($conn, "SELECT SUM(preciott) AS total_materiales FROM tmateriales WHERE estado = '1'");
$monto = mysqli_query($conn, "SELECT SUM(monto) AS total_financiero FROM trefinanciero WHERE estado = '1'");
$salariot = mysqli_query($conn, "SELECT SUM(SalarioT) AS total_salarios FROM trehumano WHERE estado = '1'");

// Calcular valores
$total_materiales = mysqli_fetch_assoc($resultado)['total_materiales'];
$total_financiero = mysqli_fetch_assoc($monto)['total_financiero'];
$total_salarios = mysqli_fetch_assoc($salariot)['total_salarios'];

$total_gastos = $total_materiales + $total_financiero + $total_salarios;
$presupuesto_restante = $total_presupuesto - $total_gastos;

// Generar alerta si el presupuesto restante es menor a un umbral
$alerta = $presupuesto_restante < 1000 ? "¡Atención! El presupuesto restante está cerca de agotarse." : "";
?>

<?php include("../../template/top.php"); ?>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Seguimiento del Presupuesto</h6>
        </div>
        <div class="card-body">
            <?php if ($alerta): ?>
                <div class="alert alert-warning" role="alert">
                    <?= $alerta ?>
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="tablaResumen" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Materiales</th>
                            <th>Financiero</th>
                            <th>Recurso Humano</th>
                            <th>Presupuesto</th>
                            <th>Presupuesto Restante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= number_format($total_materiales, 2) ?></td>
                            <td><?= number_format($total_financiero, 2) ?></td>
                            <td><?= number_format($total_salarios, 2) ?></td>
                            <td><?= number_format($total_presupuesto, 2) ?></td>
                            <td><?= number_format($presupuesto_restante, 2) ?></td>
                        </tr>
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
        $("#tablaResumen").DataTable({
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
            },
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            responsive: true
        });
    });
</script>

<?php include("../../template/bottom.php"); ?>