
<?php
	session_start();
?>
<!DOCTYPE html>
<html lang="es">
	 
		<head>
			<meta charset="utf-8"/>
			<meta description="Web de subastas"/>
			<title>Tu web de subastas</title>
		</head>
	 
	<body>
		 
		<header>
			<h1 align="center">Tu web de subastas</h1>
		</header>

		<?php if((isset($_SESSION['username']))){ ?>
			Bienvenido <?php echo $_SESSION['username'];?>
					<form action="logout.php" method="POST">
						<input type="submit" name="Submit" value="Logout">
					</form>

					<hr/>

					<form action="postor.php" method="POST">
						<input type="submit" name="Submit" value="Página de postor">
					</form>

					<form action="vendedor.php" method="POST">
						<input type="submit" name="Submit" value="Página de vendedor">
					</form>


					<form action="admin.php" method="POST">
						<input type="submit" name="Submit" value="Panel de Admin">
					</form>


		<?php } ?> 

	<!-- 	<hr/>

			<form action="reguser.php" method="POST">
		 
			<h3>¡Si no tienes una cuenta registrate ya!</h3>
			 
				<label for="username">Nombre de usuario: </label><br>
				<input type="text" required name="username" id="username" maxlength="10" />
				<br><br>
				  
				<label for="password">Password:</label><br>
				<input type="password" required name="password" id="password" maxlength="8"/><br>

				<label for="vendedor"></label>
				<input type="checkbox" name="vendedor" value="Si">Registrarme como vendedor<br>
				(En caso de no marcarse, se registrará como postor.)

				<br><br>

				<input type="submit" name="submit" value="Registrarme"/><br>

			</form> -->

			<div class="validar_login">
	 	
		 		<?php if(!(isset($_SESSION['username']))){ ?>


					<form action="reguser.php" method="POST">
				 
						<h3>¡Si no tienes una cuenta registrate ya!</h3>
					 
						<label for="username">Nombre de usuario: </label><br>
						<input type="text" required name="username" id="username" maxlength="10" />
						<br><br>
						  
						<label for="password">Password:</label><br>
						<input type="password" required name="password" id="password" maxlength="8"/><br>

						<label for="vendedor"></label>
						<input type="checkbox" name="vendedor" value="Si">Registrarme como vendedor<br>
						(En caso de no marcarse, se registrará como postor.)

						<br><br>

						<input type="submit" name="submit" value="Registrarme"/><br>

					</form>

					<hr/>
					<h3>Login</h3>

					<form action="login.php" method="POST">

						<label>Nombre Usuario:</label><br>
						<input name="username" required type="text" id="username" maxlength="10">
						<br><br>

						<label>Password:</label><br>
						<input name="password" required type="password" id="password" maxlength="8">
						<br><br>

						<input type="submit" name="Submit" value="Login">
					</form>
				<?php } ?>  

			</div>

				<hr/>

				<h2 align="center">Subastas activas</h2>

		<?php

			include('variables.php');

			$conexion = new mysqli($host_db, $user_db, $pass_db, $db_name);

			if (mysqli_connect_errno()) {
			    die("Error al conectar: ".mysqli_connect_error());
			}

			$result_items = $conexion->query("SELECT * FROM $tbl_items");
			$result_lotes = $conexion->query("SELECT * FROM $tbl_lotes");

			//Muestra las imagenes de "/fotos"
			while($row_items = $result_items->fetch_array(MYSQLI_ASSOC)){
				$date = date_create($row_items["fecha"]);
				echo "<p align=\"CENTER\">".$row_items["concepto"]."</p><br>";
				echo "<p align=\"CENTER\">".'<img src="'.$row_items["imagen"].'" width="200" heigth="200"/>'."</p><br>";
				echo "<p align=\"CENTER\">Fecha fin subasta: ".date_format($date, 'd/m/Y H:i:s')."</p><br><br>";
			}

			//ESTO CASCA
			// while($row_lotes = $result_lotes->fetch_array(MYSQLI_ASSOC)){
			// 	echo "soy un lote";
			// 	echo $row_lotes["idProducto"]."<br>";
			// 	echo '<img src="'.$row_items["imagen"].'" width="200" heigth="200"/>'."<br>";
			// 	echo $row_items["fecha_fin"]."<br><br>";
			// }
		?>
	</body>
</html>
