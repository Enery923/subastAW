<?php

	session_start();
	unset ($SESSION['username']);
	session_destroy();

	include('variables.php');

	$conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);
 
	if ($conexion->connect_error) {
		die("La conexion falló: " . $conexion->connect_error);
	}

	$username = $_POST['username'];
	$password = $_POST['password'];
	$tipo_registro = $_POST['vendedor'];
	$hash = password_hash($password, PASSWORD_BCRYPT);
	 
	$buscar_user = "SELECT * FROM $tbl_name WHERE $row_tbl_name_user = '$username'";	 
	$result_user = $conexion->query($buscar_user);	
	$count = mysqli_num_rows($result_user);

	if ($count == 1) {
		echo "<br />". "El nombre de usuario ya está en uso." . "<br />";	 
		echo "<a href='index.php'>Introduzca otro nombre.</a>";
		// Registramos como vendedor
	} else if ($tipo_registro == 'Si') {
		$query = "INSERT INTO $tbl_name ($row_tbl_name_user, $row_tbl_name_pwd, $row_tbl_name_permisos) VALUES ('$_POST[username]', '$hash', '1')";	 
		if (!mysqli_query($conexion, $query)) {
			die('Error: Problema con la base de datos' . mysqli_error());
			echo "Error al crear el usuario." . "<br />";
		} else {
			echo "<br />" . "<h2>" . "Usuario vendedor creado con éxito." . "</h2>";
			echo "<h4>" . "Bienvenido a Subastas vendedor " . $_POST['username'] . "</h4>" . "\n\n";
			echo "<h5>" . "¡Vuelve a la página principal y loguéate para participar! " . "<a href='index.php'>Login</a>" . "</h5>";
		}
		// Registramos como usuario normal (postor)
	} else {
		$query = "INSERT INTO $tbl_name ($row_tbl_name_user, $row_tbl_name_pwd) VALUES ('$_POST[username]', '$hash')";	 
		if (!mysqli_query($conexion, $query)) {
			die('Error: Problema con la base de datos' . mysqli_error());
			echo "Error al crear el usuario." . "<br />";
		} else {
			echo "<br />" . "<h2>" . "Usuario normal creado con éxito." . "</h2>";
			echo "<h4>" . "Bienvenido a Subastas postor " . $_POST['username'] . "</h4>" . "\n\n";
			echo "<h5>" . "¡Vuelve a la página principal y loguéate para participar! " . "<a href='index.php'>Login</a>" . "</h5>";
		}
	}
	mysqli_close($conexion);
?>
