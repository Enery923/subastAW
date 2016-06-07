<?php
	session_start();

	if($_SESSION['perfil'] != "2"){
		echo 'No tienes permiso para ver esta página. Por favor entra con una cuenta de administrador.';
		echo "<br><a href='index.php'>Logueate con una cuenta de administrador</a>";

	} else {
?>
	<html>

		<head>
			<title>Panel de administrador</title>
		</head>

		<body>
			<h1>Panel de administrador</h1>
			<p>Hola todopoderoso admin, tú mandas aquí.</p>
		
			<form action="makeadmin.php" method="POST">
				<label for="username">Nombre de usuario: </label><br>
				<input type="text" required name="username" id="username" maxlength="10" />
				<input type="submit" name="submit" value="Hacer admin"/><br>
			</form>


			<a href=index.php>Mainpage</a><br>
			<a href=logout.php>Cerrar sesión</a>
		</body>
	</html>
<?php
	}
?>
