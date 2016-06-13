<?php
	include('variables.php');

	$conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);
 
	if ($conexion->connect_error) {
		die("La conexion falló: " . $conexion->connect_error);
	}

	$username = $_POST['username'];
	echo $username;

	$buscar_user = "SELECT * FROM $tbl_name WHERE $row_tbl_name_user = '$username'";	 
	$result_user = $conexion->query($buscar_user);	
	$count = mysqli_num_rows($result_user);

	if ($count == 0) {
		echo "<br />". "El nombre de usuario no existe." . "<br />";	 
		echo "<a href='admin.php'>Panel de admin.</a>";

	} else if ($count == 1) {
		$query = "UPDATE $tbl_name SET $row_tbl_name_permisos='0' WHERE $row_tbl_name_user='$username'"
;
		if (!mysqli_query($conexion, $query)) {
			die('Error: Problema con la base de datos' . mysqli_error());
		}

		echo "El usuario $username ha sido borrado."."\n";
		echo "<a href='admin.php'>Panel de admin.</a>";

	} else {
		echo "La base de datos contiene más de 1 usuario con ese nombre. Algo va mal.";
	}

	mysql_close($conexion);
?>