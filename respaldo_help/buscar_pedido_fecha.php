<?php
    include 'conexion.php';

    $fecha=$_GET['fecha'];
    

   ///Aqui cambiar por el nombre de la tabla real de pedidos
  // $consulta = "SELECT * FROM mysql_littleboss.tbl_pedido_prueba where entrega = '$fecha';";

   //select max(codigo) codigo,descripcion ,fecharegistrop ,fechaEntrega ,cantidad ,costoEnvio, cliente, pagototal 
   //from pedido where fecharegistrop = '12/08/2021' limit 1;
   //$consulta = "SELECT * FROM mysql_littleboss.tbl_pedido_prueba where entrega = '$fecha';";
   $consulta = "select max(codigo) codigo,descripcion ,fecharegistrop ,fechaEntrega ,cantidad ,costoEnvio, cliente, pagototal from pedido 
   where fecharegistrop = '$fecha' and codigo = (select  max(codigo) from pedido where fecharegistrop = '$fecha');";
    
    $resultado = $conexion -> query($consulta);

    while ($fila=$resultado -> fetch_array()){
        $articulos[] = array_map('utf8_encode', $fila);

    }

    echo json_encode($articulos);
    $resultado -> close();


?>