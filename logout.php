<?php 

setcookie("usuario", '', time() - 36000);
header('location: login.php');