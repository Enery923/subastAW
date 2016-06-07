<?php
	session_start();

	if($_SESSION['perfil'] == '1'){
	 	echo 'Página de vendedor || TODO';
		echo "<br><a href='index.php'>Mainpage</a>";
	} else {
		echo 'No tienes permiso para ver esta página. Usa una cuenta de vendedor.';
		echo "<br><a href='index.php'>Logueate con una cuenta de postor</a>";
	}
?>
