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
 
  $consulta="SELECT idUsuarios FROM usuarios WHERE Nombre = '".$_SESSION['username']."'";
      if(!($resultado = mysqli_query($conexion,$consulta))){
        echo "Error de ejecución de consulta";
      }
      $row =  mysqli_fetch_assoc($resultado);
      $idUsuario=. $row['idUsuarios'] .;

      //$idUsuario = 2;

// Se monta la consulta para grabar una nueva cita.
  if(in_array($_POST["tipoSubasta"], $arrayName = array('7','8'))){
    $consulta="INSERT INTO `subasta`(`fecha_fin`,`fecha_roundRobin`,`precio`,`precioActual`,`idEstado`,`idTipoSubasta`) 
          VALUES ('".$_POST["fechaAñoFinal"]."-".$_POST["fechaMesFinal"]."-".$_POST["fechaDiaFinal"]." ".$_POST["horaFinal"].":".$_POST["minutoFinal"].":".$_POST["segundoFinal"]."',
            '".$_POST["fechaAñoRoundRobin"]."-".$_POST["fechaMesRoundRobin"]."-".$_POST["fechaDiaRoundRobin"]." ".$_POST["horaRoundRobin"].":".$_POST["minutoRoundRobin"].":".$_POST["segundoRoundRobin"]."',
            '".$_POST["precio"].".".$_POST["precioCentimos"]."','".$_POST["precio"].".".$_POST["precioCentimos"]."','3','".$_POST["tipoSubasta"]."');";
  } else {
    $consulta="INSERT INTO `subasta`(`fecha_fin`, `precio`, `precioActual`, `idEstado`, `idTipoSubasta`) 
          VALUES ('".$_POST["fechaAñoFinal"]."-".$_POST["fechaMesFinal"]."-".$_POST["fechaDiaFinal"]." ".$_POST["horaFinal"].":".$_POST["minutoFinal"].":".$_POST["segundoFinal"]."'
            ,'".$_POST["precio"].".".$_POST["precioCentimos"]."','".$_POST["precio"].".".$_POST["precioCentimos"]."','3','".$_POST["tipoSubasta"]."');";
  }
// Se ejecuta la consulta.
  if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta al generar la subasta";
  }
// Se liberan recursos y se cierra la base de datos.
  @mysqli_free_result($resultado);

  $consulta = "SELECT MAX(`idSubasta`) FROM `subasta`;";
  if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta al coger el id subasta mas grande";
  }
  $row =  mysqli_fetch_assoc($resultado);
  $idSubasta= $row['MAX(`idSubasta`)'] ;
   @mysqli_free_result($resultado);

  $consulta="INSERT INTO `lotes` (`idProducto`,`idSubasta`) VALUES ";

  foreach ($_POST['idProduct'] as $id) {
    # code...
    //echo $id;
     $consulta .= "(".$id.",".$idSubasta."),";
  }

  $consulta = substr($consulta, 0,-1);
  //echo $consulta;
  if(!($resultado = mysqli_query($conexion,$consulta))){
        echo "Error de ejecución de consulta en los lotes";
  }
  @mysqli_free_result($resultado);
  

  $consulta="INSERT INTO `usuario_subasta`(`idUsuarios`,`idSUbasta`) VALUES ('".$idUsuario."','".$idSubasta."')";
  if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta en usuario_subasta";
    }
  @mysqli_free_result($resultado);

  // ".$_SESSION['username']." FALTA AGREGARLO
  $consulta="INSERT INTO `log`(`Descripcion`, `idUsuario`, `idSubasta`) 
          VALUES ('El usuario ".$idUsuario. " ha registrado una nueva subasta "
                .$idSubasta."', '".$idUsuario."','".$idSubasta."')";
// Se ejecuta la consulta.
  if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta en el log";
  }
// Se liberan recursos y se cierra la base de datos.
  @mysqli_free_result($resultado);

  mysqli_close($conexion);
  ?>
  <form action="vendedor.php" name="retorno" id="retorno" method="post">
    <input type="hidden" name="" id="">
  </form>
  </body>
</html>
