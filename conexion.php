<?php
    $hostname='localhost';
    $database='db_app_dislexia';
    $username='root';
    $password='';

    $conexion=new mysqli($hostname,$username,$password,$database);
    if($conexion->connect_errno){
        echo "Lo sentimos, el sitio web está experimentando problemas.";
    }

?>