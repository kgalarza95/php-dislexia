 <?php
    include 'conexion.php';

     $nombre=$_POST['nombre'];
     $codigo=$_POST['codigo'];
     $precio=$_POST['precio'];
     $cantidad=$_POST['cantidad'];
     $descripcion=$_POST['descripcion'];

    $consulta = "UPDATE articulos SET nombre = '".$nombre."', precio = '".$precio."', cantidad = '".$cantidad."', descripcion = '".$descripcion."' WHERE codigo = '".$codigo."'";
    mysqli_query($conexion,$consulta)or die (mysqli_error());
    mysqli_close($conexion);



    /*$codigo=$_POST['codigo'];
    $descripcion=$_POST['descripcion'];
    $fecharegistrop=$_POST['fecharegistrop'];
    $fechaEntrega=$_POST['fechaEntrega'];
    $cantidad=$_POST['cantidad'];
    $costoEnvio=$_POST['costoEnvio'];
    $cliente=$_POST['cliente'];
    $pagototal=$_POST['pagototal'];

   $consulta = "UPDATE pedido SET descripcion = '".$descripcion."', fecharegistrop = '".$fecharegistrop."', fechaEntrega = '".$fechaEntrega."', cantidad = '".$cantidad."', costoEnvio = '".$costoEnvio."', cliente = '".$cliente."', pagototal = '".$pagototal."' WHERE codigo = '".$codigo."'";
   mysqli_query($conexion,$consulta)or die (mysqli_error());
   mysqli_close($conexion);*/
?>