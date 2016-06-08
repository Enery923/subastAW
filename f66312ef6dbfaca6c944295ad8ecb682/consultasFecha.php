<?php
	session_start();

	if($_SESSION['perfil'] != "2"){
		echo 'No tienes permiso para ver esta página. Por favor entra con una cuenta de administrador.';
		echo "<br><a href='index.php'>Logueate con una cuenta de administrador</a>";

	} else {
?>
	<html>
		<head>
			<h1 align="center">Subastas ordenadas por fecha</h1>
		</head>

		<body>
			
		</body>
	</html>
<?php
	}
?>