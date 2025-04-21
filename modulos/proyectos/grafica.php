
<?php
// Incluir la conexión a la base de datos
include("../../conn/conn.php");

// Realizar la consulta a la base de datos
$query = "SELECT nombre, fechainicio, estado FROM tproyectos";
$result = mysqli_query($conn, $query);

// Crear un array para almacenar los datos
$chartData = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Formateamos la fecha para que sea más fácil de mostrar en el gráfico
    $nombre = $row['nombre'];
    $fechainicio = $row['fechainicio'];
    $estado = $row['estado'];

    // Agregar un número al estado
    $estadoNum = 0;
    if ($estado == 'completado') {
        $estadoNum = 1;
    } else if ($estado == 'pendiente') {
        $estadoNum = 2;
    } else if ($estado == 'en proceso') {
        $estadoNum = 3;
    } else if ($estado == 'cerrado') {
        $estadoNum = 4;
    }

    // Añadir los valores al array de datos nombre, fecha de inicio, estado num
    $chartData[] = "['$nombre', new Date('$fechainicio'), $estadoNum]";
}

// Convertir el array a un formato de JavaScript (array en formato string)
$chartDataString = implode(",", $chartData);
?>




<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart', 'bar']});
      google.charts.setOnLoadCallback(drawVisualization);

      function drawVisualization() {
        // Datos de la base de datos procesados por PHP
        var data = google.visualization.arrayToDataTable([
          ['Proyecto', 'Fecha de Inicio', 'Estado'], // encabezados de la tabla
          <?php echo $chartDataString; ?> // los datos que pasamos desde PHP
        ]);

        var options = {
          title: 'Proyectos',
          hAxis: {
            title: 'Proyecto'
          },
          vAxis: {
           0: {title: 'Fecha de Inicio'},
           1: {format: 'yyyy-MM-dd'} 
          },
          seriesType: 'bars',
          series: {
            0: {type: 'line', targetAxisIndex: 0},  // Serie para fecha de inicio linea
            1: {type: 'bars', targetAxisIndex: 1}   // Serie para el estado barras
          },
          // Usamos un eje independiente para la serie de estado
          vAxes: {
            1: {title: 'Estado'}  // estado
          }
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <div id="chart_div" style="width: 900px; height: 500px;"></div>
  </body>
</html>


