<?php

	session_start();

	// OJO EN INTEGRACION
	include('variables.php');

	$conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

	if ($conexion->connect_error) {
		die("La conexion falló: " . $conexion->connect_error);
	}

	$username = $_POST['username'];
	$password = $_POST['password'];
	
	$sql_users = "SELECT * FROM $tbl_name WHERE $row_tbl_name_user = '$username'";
	$result_user = $conexion->query($sql_users);

	if ($result_user->num_rows === 0){
		echo "Username no registrado en la base de datos.";
		echo "<br><a href='index.php'>¡Registrate ya!</a>";

	} else if ($result_user->num_rows === 1) {
		$row = $result_user->fetch_array(MYSQLI_ASSOC);
		$sql_permisos = "SELECT permisos FROM $tbl_name WHERE $row_tbl_name_user = '$username'";
		$result_permiso = $conexion->query($sql_permisos);
		$row_perm = $result_permiso->fetch_array(MYSQLI_ASSOC);

		//POSTOR A.K.A. USUARIO NORMAL
		if (password_verify($password, $row['password']) && $row_perm['permisos'] == 0) {		 
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $username;
			$_SESSION['perfil'] = 0;
			$_SESSION['start'] = time();
			$_SESSION['expire'] = $_SESSION['start'] + (5 * 60);

			echo "Bienvenido postor " . $_SESSION['username'];
			echo "<br><br><a href=user.php>Página de usuario</a>";
			echo "<br><br><a href=index.php>Página principal</a>";

			//header("Location: index.php");

			//VENDEDOR
		} else if (password_verify($password, $row['password']) && $row_perm['permisos'] == 1){
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $username;
			$_SESSION[perfil] = 1;
			$_SESSION['start'] = time();
			$_SESSION['expire'] = $_SESSION['start'] + (5 * 60);

			echo "Bienvenido vendedor " . $_SESSION['username'];
			echo "<br><br><a href=postor.php>Página de postor</a>";
			echo "<br><br><a href=index.php>Página principal</a>";

			//ADMIN
		} else if (password_verify($password, $row['password']) && $row_perm['permisos'] == 2){
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $username;
			$_SESSION['perfil'] = 2;
			$_SESSION['start'] = time();
			$_SESSION['expire'] = $_SESSION['start'] + (0.5 * 60);

			echo "Bienvenido administrador " . $_SESSION['username'];
			echo "<br><br><a href=admin.php>Panel del administrador</a>";
			echo "<br><br><a href=index.php>Página principal</a>";

		} else {
			echo "Username o Password estan incorrectos.";
			echo "<br><a href='index.php'>Volver a Intentarlo</a>";
		}
	}
	mysqli_close($conexion);
?>
