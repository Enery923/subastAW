<html>
  <head>
  <script language="javascript" type="text/javascript">
    function volver(){
      document.retorno.submit();
    }
  </script>
  </head>
  <body onLoad="javascript:volver();">
  <?php

  session_start();

include('variables.php');
  $conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

    if (mysqli_connect_errno()) {
        die("Error al conectar: ".mysqli_connect_error());
    }
 
  $dir_subida = '../img/';
  $fichero_subido = $dir_subida . basename($_FILES['uploadedfile']['name']);

  if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $fichero_subido)) {
    echo "El fichero es válido y se subió con éxito.\n";
} else {
    echo "¡Posible ataque de subida de ficheros!\n";
}

  $consulta="INSERT INTO `productos`(`precio_inicio`, `concepto`, `descripcion`, `idEstado`,`imagen`) 
          VALUES ('".$_POST["precio_inicio"].".".$_POST["precio_inicioCentimos"]."','".$_POST["concepto"]."','".$_POST["description"]."','0','".$fichero_subido."');";
// Se ejecuta la consulta.
  if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta";
  }
// Se liberan recursos
  @mysqli_free_result($resultado);

  $consulta="SELECT idUsuarios FROM usuarios WHERE Nombre = '".$_SESSION['username']."'";
      if(!($resultado = mysqli_query($conexion,$consulta))){
        echo "Error de ejecución de consulta";
      }
      $row =  mysqli_fetch_assoc($resultado);
      $idUsuario=. $row['idUsuarios'] .;

    //  $idUsuario = 2;

  $consulta="SELECT MAX(`idProductos`) FROM `productos`";
      if(!($resultado = mysqli_query($conexion,$consulta))){
        echo "Error de ejecución de consulta";
      }
      $row =  mysqli_fetch_assoc($resultado);
      //echo $row['MAX(`idProductos`)'];
      $idProducto = $row['MAX(`idProductos`)'];

  @mysqli_free_result($resultado);
  // ESTADO QUITADO USERNAME ".$_SESSION['username']."
  $consulta="INSERT INTO `log`(`Descripcion`, `idUsuario`, `idProducto`) 
          VALUES ('El usuario " .$idUsuario. " ha registrado un nuevo producto "
                .$idProducto."', '".$idUsuario."','".$idProducto."')";
// Se ejecuta la consulta.
  if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta";
  }
// Se liberan recursos y se cierra la base de datos.
  @mysqli_free_result($resultado);


  mysqli_close($conexion);
  ?>
  <form action="subastadorPerfil.php" name="retorno" id="retorno" method="post">
    <input type="hidden" name="" id="">
  </form>
  </body>
</html>
