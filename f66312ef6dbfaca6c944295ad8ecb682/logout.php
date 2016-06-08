<?php
	session_start();

	include('variables.php');

	$conexion = mysqli_connect($host_db, $user_db, $pass_db, $db_name);

	if ($conexion->connect_error) {
		die("La conexion falló: " . $conexion->connect_error);
	}

	$username = $_SESSION['username'];

	$queryLog = "INSERT INTO $tbl_log ($row_tbl_log_descripcion, $row_tbl_log_user) VALUES ('Usuario $username se ha deslogueado.', (SELECT $row_tbl_name_id FROM $tbl_name WHERE $row_tbl_name_user = '$username'))";
	if(!mysqli_query($conexion, $queryLog)){
		echo "Error guardando el log.";
		echo mysqli_error($conexion);
	}

	unset ($SESSION['username']);
	session_destroy();
	
	header('Location: http://localhost/phpLogin/index.php');
?>
