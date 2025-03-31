<?php 
ini_set("display_errors", 0);

function getClientIP() {
    $ipAddress = 'UNKNOWN';

    if (isset($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // HTTP_X_FORWARDED_FOR can contain a comma-separated list of IPs
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        foreach ($ipList as $ip) {
            $ip = trim($ip); // Just to be safe, trim any spaces
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $ipAddress = $ip;
                break;
            }
        }
    } elseif (isset($_SERVER['HTTP_X_FORWARDED']) && filter_var($_SERVER['HTTP_X_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED']) && filter_var($_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ipAddress = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
        $ipAddress = $_SERVER['REMOTE_ADDR'];
    }

    return $ipAddress;
}

$server = "localhost";
$user = "root";
$pass = "";

$database = "proyectcore1";

$conn = mysqli_connect($server, $user, $pass);
mysqli_select_db($conn, $database);

$base_url = "http://localhost/proyectoUISIL/";

if (isset($_COOKIE['usuario']) and $_COOKIE['usuario'] != ''){
    $idUsuario = $_COOKIE['usuario'];
    $tokenCookie = $_COOKIE['token'];

    $sql = "SELECT * FROM tusuarios WHERE id = $idUsuario";
    $query = mysqli_query($conn, $sql);
    while($row=mysqli_fetch_assoc($query)){
        $nombreUsuario = $row['nombre'];
        $token = $row['token'];
        $lastIP = $row['lastIP'];
    }

    if ($tokenCookie != $token){
        header('location: '.$base_url.'logout.php');
    }

    if (getClientIP() != $lastIP){
        header('location: '.$base_url.'logout.php');
    }
}else{
    if (basename($_SERVER['REQUEST_URI']) != 'login.php'){
        header('location: '.$base_url.'login.php');
    }
}