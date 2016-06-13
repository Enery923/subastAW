<html>
	<head>
		<script language="javascript" type="text/javascript">
        function grabarNuevoObjeto(){
          document.formularioNuevoObjeto.action="nuevoObjeto.php";
          document.formularioNuevoObjeto.submit();
        }

        function grabarNuevaSubasta(){
          document.formularioNuevaSubasta.action="nuevaSubasta.php";
          document.formularioNuevaSubasta.submit();
        }
    </script>
	</head>
	<body>

<?php
  session_start();

  if($_SESSION['perfil'] == '1'){
    echo 'Página de vendedor || TODO';
    echo "<br><a href='index.php'>Mainpage</a>";

     include('variables.php');
      $conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

      if (mysqli_connect_errno()) {
        die("Error al conectar: ".mysqli_connect_error());
      }
 

      //El id usuario se recibe por variable en la url, inseguro
      //$idUsuario=2;
      $consulta="SELECT idUsuarios FROM usuarios WHERE Nombre = '".$_SESSION['username']."'";
      if(!($resultado = mysqli_query($conexion,$consulta))){
        echo "Error de ejecución de consulta";
      }
      $row =  mysqli_fetch_assoc($resultado);
      $idUsuario= $row['idUsuarios'] ;

      
      $consulta="SELECT * FROM subasta WHERE idSubasta IN (SELECT idSUbasta FROM usuario_subasta WHERE idUsuarios =" . $idUsuario . ");";
/* Se ejecuta la consulta de selección.*/
      if(!($resultado = mysqli_query($conexion,$consulta))){
        echo "Error de ejecución de consulta";
      }
      echo "<h3>LISTA DE SUBASTAS</h3>";
      echo "<table border=\"1\"><tr><td>id</td><td>fecha inicio</td>
      <td>fecha round robin</td><td>fecha fin</td>
      <td>precio inicial</td><td>precio actual</td><td>estado</td><td>tipo subasta</td>";
      while ($row =  mysqli_fetch_assoc($resultado)) {
        /*id subasta, fecha inicio , round robin, fecha fin, precio, estado, tipo subasta*/
        echo "<tr><td width=100>" . $row['idSubasta'] . "</td>";
        echo "<td width=100>" . $row['fecha_inicio'] . "</td>";
        echo "<td width=100>" . $row['fecha_roundRobin'] . "</td>";
        echo "<td width=100>" . $row['fecha_fin'] . "</td>";
        echo "<td width=100>" . $row['precio'] . "</td>";
        echo "<td width=100>" . $row['precioActual'] . "</td>";
        echo "<td width=100>" . $row['idEstado'] . "</td>";
        echo "<td width=100>" . $row['idTipoSubasta'] . "</td></tr>";
      }
      echo "</table>";
    ?>
    <h3>AGREGAR UN OBJETO A LA BASE DE DATOS</h3>
    <form enctype="multipart/form-data" action="" method="post" name="formularioNuevoObjeto" id="formularioNuevoObjeto">
      <!-- Necesito :
      Text area a leer, ejemplo <textarea name="asunto" cols="50" rows="5" id="asunto"></textarea>
      un botón para agregar, otro para borrar?

      datos producto: idProductos, precio_inicio, concepto, descripction
      imagen(enlace), fecha -->
        <p>Nombre</p>
        <textarea rows="1" cols="20" name="concepto">max 20 caracteres</textarea>
        <p>Precio</p>
        <textarea cols="5" rows="1" name="precio_inicio">00</textarea><em><strong>.</strong></em><textarea cols="2" rows="1" name="precio_inicioCentimos">00</textarea>
        <p>Descripcion</p>
        <textarea rows="5" cols="20" name="description">max 100 caracteres</textarea><br>
        <input name="uploadedfile" type="file" /><br>
        <input name = "agregar_objeto" type="button" id="agregar_objeto" value="Agregar Objeto" onClick="javascript:grabarNuevoObjeto()">
      
    </form>
    
    <h3>GENERAR UNA NUEVA SUBASTA</h3>
    <form action="" method="post" name="formularioNuevaSubasta" id="formularioNuevaSubasta">
      <!-- ¿Select con la lista de objetos, puede no ser necesario asi que descartado de buenas a primeras?
      Un select con las distintas opciones de subasta puestas en la variable <option>

      Para la fecha limite se puede utilizar el siguiente código para seleccionar-->
      <h4>Lista de Objetos</h4>
      <?php
        $consulta="SELECT idProductos,concepto,precio_inicio FROM productos;";
        /* Se ejecuta la consulta de selección.*/
        if(!($resultado = mysqli_query($conexion,$consulta))){
          echo "Error de ejecución de consulta";
        }

        echo "<table><tr><td><td>Id</td><td>Concepto</td><td>Precio</td></tr>";
        while ($row =  mysqli_fetch_assoc($resultado)) {

          echo "<tr><td><input type =checkbox name=\"idProduct[]\" value=\"" . $row['idProductos'] . "\"><td>" . $row['idProductos'] . "</td><td>" . $row['concepto'] . "</td><td>" . $row['precio_inicio'] . "</td></tr>";
        }
        echo "</table>";
       
      ?>
      
    <h4>Tipo de Subasta</h4>
      <select name="tipoSubasta" id="tipoSubasta">
        <option value ="0">Dinamica descubierta ascendente</option>
        <option value ="1">Dinamica cubierta ascendente</option>
        <option value ="2">Dinamica descubierta descendente</option>
        <option value ="3">Dinamica cubierta descendente</option>
        <option value ="4">Dinamica holandesa</option>
        <option value ="5">Sobre cerrado ascendente</option>
        <option value ="6">Sobre cerrado descendente</option>
        <option value ="7">Round robin ascendente</option>
        <option value ="8">Round robin descendente</option>
      </select>
      <br>
      Precio Inicial <textarea cols="5" rows="1" name="precio">00</textarea><em><strong>.</strong></em><textarea cols="2" rows="1" name="precioCentimos">00</textarea>

      <h4>Fecha Round Robin</h4>
      <table cellspacing="0" cellpadding="2">
        <tr>
          <textarea rows="1" cols="4" name="fechaAñoRoundRobin">YYYY</textarea><em><strong>-</strong></em>
          <textarea rows="1" cols="2" name="fechaMesRoundRobin">MM</textarea><em><strong>-</strong></em>
          <textarea rows="1" cols="2" name="fechaDiaRoundRobin">DD</textarea>
        </tr>
        <tr>
          <textarea rows="1" cols="4" name="horaRoundRobin">HH</textarea><em><strong>.</strong></em>
          <textarea rows="1" cols="2" name="minutoRoundRobin">MM</textarea><em><strong>.</strong></em>
          <textarea rows="1" cols="2" name="segundoRoundRobin">SS</textarea>
        </tr>
          
      </table>

      <h4>Fecha Finalización</h4>
      <table cellspacing="0" cellpadding="2">
        <tr>
          <textarea rows="1" cols="4" name="fechaAñoFinal">YYYY</textarea><em><strong>-</strong></em>
          <textarea rows="1" cols="2" name="fechaMesFinal">MM</textarea><em><strong>-</strong></em>
          <textarea rows="1" cols="2" name="fechaDiaFinal">DD</textarea>
        </tr>
        <tr>
          <textarea rows="1" cols="4" name="horaFinal">HH</textarea><em><strong>.</strong></em>
          <textarea rows="1" cols="2" name="minutoFinal">MM</textarea><em><strong>.</strong></em>
          <textarea rows="1" cols="2" name="segundoFinal">SS</textarea>
        </tr>
          
      </table>
      <input name = "agregar_subasta" type="button" id="agregar_subasta" value="Agregar Subasta" onClick="javascript:grabarNuevaSubasta()">
    </form>
  </body>
<?php
  } else {
    echo 'No tienes permiso para ver esta página. Usa una cuenta de vendedor.';
    echo "<br><a href='index.php'>Logueate con una cuenta de postor</a>";
  }
?>     
</html>