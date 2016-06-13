<?php
require_once 'database.php';
require_once 'funciones.php';
error_reporting(E_ALL);
ini_set('display_errors',1);


function getDatosPujas() {
	global $dbh;
	$usuario = (int)getUsuario($_SESSION['username']);
	$query = $dbh->prepare("SELECT * FROM Pujas WHERE idUsuario=:usuario");
	$query->bindValue(":usuario", $usuario, PDO::PARAM_INT);
	$query->execute();

	$pujas = $query->fetchAll();

	return $pujas;
}

function construirTablaPujas($pujas) {
	$tabla = <<<EOF
        <table id="tablaPujas">
                <caption>Pujas</caption>
                <tr>
                <th>Cantidad</th>
                <th>Fecha</th>
		<th>Subasta</th>
                </tr>
EOF;

	foreach ($pujas as $puja) {
		$tabla .= <<<EOF
                <tr>
                <td>{$puja['Cantidad']}</td>
                <td>{$puja['fecha']}</td>
                <td>{$puja['idSubasta']}</td>
                </tr>
EOF;
	}

	$tabla .= "</table>";
        return $tabla;
}

function crearPagina() {
	$pujas = getDatosPujas();
	$tablaPujas = construirTablaPujas($pujas);

	echo <<<EOF
        <!doctype html>
        <html lang="es">
        <head>
                <meta charset="UTF-8">
                <link rel="stylesheet" type="text/css" href="css/subastas.css" />
                <title>Postor</title>
        </head>
        <body>
		<nav id="menu">
                <ul id="enlaces">
                        <li><a href="listaSubastas.php">Lista Subastas</a></li>
			<li><a href="user.php">Pagina Usuario</a></li>
                </ul>
        	</nav>
                <div id="contenidoSubasta">
                {$tablaPujas}
		</div>
	</body>
	</html>
EOF;

}

comprobarLogin();
crearPagina();

?>

