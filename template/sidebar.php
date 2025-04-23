<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
    <img src="<?=$base_url?>img/logopc_blanco.png" class="img-fluid">
</a>
<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item active">
    <a class="nav-link" href="../../modulos/proyectos/grafica.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Inicio</span></a>
</li>


<?php
$sql = "SELECT * FROM tprivilegios WHERE estado = 1 ORDER BY nombre ASC";
$query = mysqli_query($conn, $sql);
while($row=mysqli_fetch_assoc($query)){
    $idPrivilegio = $row['id'];
    $lotiene = 0;
    $sqlUsuario = "SELECT * FROM tprivilegiosusuario WHERE idUsuario = $idUsuario AND idPrivilegio = $idPrivilegio";
    $queryUsuario = mysqli_query($conn, $sqlUsuario);
    if (mysqli_num_rows($queryUsuario) != 0){
    ?>
    <li class="nav-item active">
        <a class="nav-link" href="<?=$base_url.$row['url']?>">
            <i class="fa <?=$row['icono']?>"></i>
            <span><?=$row['nombre']?></span></a>
    </li>
    <?php
    }
}

/*

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Menú
</div>

<!-- Nav Item - Pages Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
        aria-expanded="true" aria-controls="collapseTwo">
        <i class="fas fa-fw fa-cog"></i>
        <span>Elementos</span>
    </a>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Components:</h6>
            <a class="collapse-item" href="video.php">Video</a>
            <a class="collapse-item" href="mapa.php">Mapa</a>
        </div>
    </div>
</li>

<!-- Nav Item - Utilities Collapse Menu -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
        aria-expanded="true" aria-controls="collapseUtilities">
        <i class="fas fa-fw fa-wrench"></i>
        <span>Utilities</span>
    </a>
    <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Custom Utilities:</h6>
            <a class="collapse-item" href="utilities-color.html">Colors</a>
            <a class="collapse-item" href="utilities-border.html">Borders</a>
            <a class="collapse-item" href="utilities-animation.html">Animations</a>
            <a class="collapse-item" href="utilities-other.html">Other</a>
        </div>
    </div>
</li>

*/
?>
<li class="nav-item active">
    <a class="nav-link" href="<?=$base_url?>logout.php">
    <i class="fa fa-lock"></i>
        <span>Cerrar sesión</span></a>
</li>
</ul>