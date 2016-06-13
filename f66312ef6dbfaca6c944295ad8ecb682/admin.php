<?php
	session_start();

	if($_SESSION['perfil'] != "2"){
		echo 'No tienes permiso para ver esta página. Por favor entra con una cuenta de administrador.';
		echo "<br><a href='index.php'>Logueate con una cuenta de administrador</a>";

	} else {
?>
	<!DOCTYPE html>
	<html>

		<head>
			<title>Panel de administrador</title>
		</head>

		<body>
			<h1 align="center">Panel de administrador</h1>
		
				<h3>Gestión de usuarios</h3>
			<form action="makeadmin.php" method="POST">
				<label for="username">Nombre de usuario: </label><br>
				<input type="text" required name="username" id="username" maxlength="10" />
				<input type="submit" name="submit" value="Hacer admin"/><br>
			</form>

			<form action="deleteadmin.php" method="POST">
				<label for="username">Nombre de usuario: </label><br>
				<input type="text" required name="username" id="username" maxlength="10" />
				<input type="submit" name="submit" value="Borrar admin"/><br>
			</form>

				<h3>Consultas a la BBDD</h3>
				<!-- Esto no va -->
			<form action="consultasFecha.php" method="POST">
				<label for="Fecha">Fecha (dd/MM/AAAA): </label><br>
				<input type="date" name="fecha">
				<input type="submit" name="submit" value="Consulta fecha"/><br>
<!-- 				<?php 
					if (isset($_POST['submit'])) { 
						$textoarea=getFechas($_POST['submit']);
					}
				?> --> 
			</form>
			
			<form action="getPostor.php" method="POST">
				<label for="username">Postor: </label><br>
				<input type="text" required name="username" id="username" maxlength="10" />
				<input type="submit" name="submit" value="Consulta Postor"/><br>
				<?php 
					if (isset($_POST['submit'])) { 
						$textoarea=getPostor($_POST['submit']);
					}
				?> 
				</form>

<!-- 			<form action="" method="POST">
				<label for="username">Postor: </label><br>
				<input type="text" required name="username" id="username" maxlength="10" />
				<input type="submit" name="submit" value="Consulta Postor"/><br>
				<?php 
					if (isset($_POST['submit'])) { 
						$textoarea=getPostor($_POST['submit']);
					}
				?> 
			</form> -->

			<form action="" method="POST">
				<label for="username">Subasta: </label><br>
				<input type="text" required name="idSubasta" id="idSubasta" maxlength="10" />
				<input type="submit" name="submit" value="Consulta Subasta"/><br>
				<?php
					if (isset($_POST['submit'])) { 
						$textoarea=getSubastas($_POST['submit']);
					}
				?> 
			</form>

			<form action="" method="POST">
				<label for="prod">Producto: </label><br>
				<input type="text" required name="idProducto" id="idProducto" maxlength="10" />
				<input type="submit" name="submit" value="Consulta Producto"/><br>
				<?php
					if (isset($_POST['submit'])) { 
						$textoarea=getProducto($_POST['submit']);
					}
				?> 
			</form>
			<a href=index.php>Mainpage</a><br>
			<!--<a href=logout.php>Cerrar sesión</a>-->
		</body>
	</html>
<?php
	}
?>
