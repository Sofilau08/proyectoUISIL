<?php
include("../../conn/conn.php");

$estado = isset($_GET['estado']) ? $_GET['estado'] : '1';

// Query data for financiero, materiales, and recursos humanos
$queryFinanciero = "SELECT * FROM trefinanciero WHERE estado = '$estado'";
$queryMateriales = "SELECT * FROM tmateriales WHERE estado = '$estado'";
$queryRecursosHumanos = "SELECT tre.*, CONCAT(tu.nombre, ' ', tu.apellidos) as nombre_apellido 
                         FROM trehumano as tre 
                         JOIN tusuarios as tu ON tre.idusuario = tu.id 
                         WHERE tre.estado = '$estado'";

$resultFinanciero = mysqli_query($conn, $queryFinanciero);
$resultMateriales = mysqli_query($conn, $queryMateriales);
$resultRecursosHumanos = mysqli_query($conn, $queryRecursosHumanos);

if (!$resultFinanciero || !$resultMateriales || !$resultRecursosHumanos) {
    die("Error al ejecutar la consulta: " . mysqli_error($conn));
}

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=reporte_aprobados.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr><th colspan='5'>Datos Financieros</th></tr>";
echo "<tr>
        <th>ID</th>
        <th>Nombre del gasto</th>
        <th>Fecha del gasto</th>
        <th>Monto</th>
        <th>Descripción</th>
    </tr>";
while ($row = mysqli_fetch_assoc($resultFinanciero)) {
    echo "<tr>
            <td>{$row['idrefinanciero']}</td>
            <td>{$row['NombreGasto']}</td>
            <td>{$row['FechaGasto']}</td>
            <td>{$row['monto']}</td>
            <td>{$row['Descripcion']}</td>
        </tr>";
}

echo "<tr><th colspan='5'>Materiales</th></tr>";
echo "<tr>
        <th>ID</th>
        <th>Nombre del material</th>
        <th>Precio unitario</th>
        <th>Cantidad</th>
        <th>Descripción</th>
    </tr>";
while ($row = mysqli_fetch_assoc($resultMateriales)) {
    echo "<tr>
            <td>{$row['idmaterial']}</td>
            <td>{$row['nombre']}</td>
            <td>{$row['preciou']}</td>
            <td>{$row['cantidad']}</td>
            <td>{$row['descrip']}</td>
        </tr>";
}

echo "<tr><th colspan='6'>Recursos Humanos</th></tr>";
echo "<tr>
        <th>ID</th>
        <th>Nombre y apellidos</th>
        <th>Cédula</th>
        <th>Salario Base</th>
        <th>Horas Trabajadas</th>
        <th>Salario Total</th>
    </tr>";
while ($row = mysqli_fetch_assoc($resultRecursosHumanos)) {
    echo "<tr>
            <td>{$row['idrehumano']}</td>
            <td>{$row['nombre_apellido']}</td>
            <td>{$row['cedula']}</td>
            <td>{$row['SalarioBase']}</td>
            <td>{$row['HorasT']}</td>
            <td>{$row['SalarioT']}</td>
        </tr>";
}

echo "</table>";
?>
