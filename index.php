<?php 
include("conn/conn.php");

include("template/rootTop.php"); ?>


<?php
include("../../conn/conn.php");

// Consulta de todos los proyectos
$queryProyectos = "SELECT idproyecto, nombre FROM tproyectos";
$resultProyectos = mysqli_query($conn, $queryProyectos);

$chartData = [];
$chartData[] = "['Proyecto', 'Progreso (%)', { role: 'annotation' }, { role: 'style' }]";

while ($proyecto = mysqli_fetch_assoc($resultProyectos)) {
    $idProyecto = $proyecto['idproyecto'];
    $nombreProyecto = $proyecto['nombre'];

    // Total tareas
    $queryTotalTareas = "SELECT COUNT(*) as total FROM ttareas WHERE idproyecto = $idProyecto";
    $totalTareas = mysqli_fetch_assoc(mysqli_query($conn, $queryTotalTareas))['total'];

    // Tareas completadas
    $queryCompletadas = "SELECT COUNT(*) as finalizado FROM ttareas WHERE idproyecto = $idProyecto AND estadotarea = 'finalizado'";
    $tareasCompletadas = mysqli_fetch_assoc(mysqli_query($conn, $queryCompletadas))['finalizado'];

    $progreso = ($totalTareas > 0) ? round(($tareasCompletadas / $totalTareas) * 100, 2) : 0;

    // Texto para anotación
    $anotacion = $progreso . "%";

    // Estilo de barra
    if ($progreso < 50) {
        $color = "color: red";
    } elseif ($progreso < 75) {
        $color = "color: orange";
    } else {
        $color = "color: green";
    }

    // Agregamos la fila con progreso, anotación y estilo
    $chartData[] = "['$nombreProyecto', $progreso, '$anotacion', '$color']";
}

$chartDataString = implode(",", $chartData);
?>

<?php include("../../template/top.php"); ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Progreso de los proyectos</h6>
    </div>
    <html>

    <head>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                <?php echo $chartDataString; ?>
            ]);

            var options = {
                title: 'Progreso de Proyectos',
                hAxis: {
                    title: 'Proyecto'
                },
                vAxis: {
                    title: 'Progreso (%)',
                    minValue: 0,
                    maxValue: 100
                },
                legend: {
                    position: 'none'
                },
                annotations: {
                    alwaysOutside: true,
                    textStyle: {
                        fontSize: 12,
                        color: '#000',
                        auraColor: 'none'
                    }
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
        </script>
    </head>

    <body>
        <div id="chart_div" style="width: 900px; height: 500px;"></div>
    </body>

    </html>
</div>
<?php include("../../template/bottom.php"); ?>


<?php include("template/bottom.php"); ?>