<?php
    include 'conexion.php';

    //$codigo=$_GET['codigo'];
    
    //$consulta = "SELECT * FROM articulos WHERE codigo='$codigo'";
    $consulta = "SELECT * FROM app_rol ";

    $resultado = $conexion -> query($consulta);

    echo "================================================";
    while ($fila=$resultado -> fetch_array()){
        $articulos[] = array_map('utf8_encode', $fila);
        //echo $articulos[0];
    }

    echo "================================================";

    echo json_encode($articulos);
    $resultado -> close();


?>