<?php
require_once 'database.php';
require_once 'update.php';
require_once 'funciones.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

comprobarLogin();

$resultado = $dbh->query("SELECT date_format(fecha_inicio,'%d-%m-%Y %H:%i:%s') as 'inicio'," .
			"date_format(fecha_fin,'%d-%m-%Y %H:%i:%s') as 'fin',idSubasta, " .
			" idTipoSubasta, date_format(fecha_RoundRobin,'%d-%m-%Y %H:%i:%s') as 'finDos',"  .
			"idEstado,precio,precioActual FROM Subasta");
$tabla = <<<EOF
<table id="tablaSubastas">
<caption>Lista de Subastas</caption>
<tr>
	<td>Inicio</td>
	<td>Fin</td>
	<td>Fin Round Robin</td>
	<td>Tipo</td>
	<td>Estado</td>
	<td>Precio Inicial</td>
	<td>Precio Actual</td>
	<td>Pujar</td>
</tr>
EOF;
while ($row = $resultado->fetch()) {
	$id = $row['idSubasta'];
	$inicio = $row['inicio'];
	$fin = $row['fin'];
	$finRoundRobin = $row['finDos'];
	$tipo = $row['idTipoSubasta'];
	$nombreTipo = getTipoSubasta($row['idTipoSubasta']);
	$estado = getEstado($row['idEstado']);
	$precio = $row['precio'];
	$precioActual = $row['precioActual'];
	if ($tipo == 0 || $tipo == 1 || $tipo ==2 || $tipo ==3 || $tipo == 4){
		$destino = "subastaDinamica.php";
	}
	else if($tipo == 5 || $tipo == 6) {
		$destino= "subastaSobreCerrado.php";
	}
	else if($tipo == 7 || $tipo == 8) {
		$destino = "subastaRoundRobin.php";
	}

	$tabla .= "<tr>";
	$tabla .= "<td>{$inicio}</td>";
	$tabla .= "<td>{$fin}</td>";
	$tabla .= "<td>{$finRoundRobin}</td>";
	$tabla .= "<td>{$nombreTipo}</td>";
	$tabla .= "<td>{$estado}</td>";
	$tabla .= "<td>{$precio}</td>";
	$tabla .= "<td>{$precioActual}</td>";
	$tabla .= <<<EOF
	<td class="celdaPujar">
	<form action="{$destino}" method="GET">
		<input type="hidden" name="subasta" value="$id" />
		<input type="submit" value="pujar" />
	</form>
	</td>
	</tr>
EOF;
}

$tabla .= "</table>";
echo <<<EOF
        <!doctype html>
        <html lang="es">
        <head>
                <meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="css/subastas.css" />
                <title>Listado de subastas</title>
        </head>
        <body>
	<nav id="menu">
		<ul id="enlaces">
			<li><a href="listaSubastas.php">Lista Subastas</a></li>
			<li><a href="user.php">Pagina Usuario</a></li>
		</ul>
	</nav>
	<div id="listaSubastas">
		{$tabla}
	</div>
	</body>
	</html>
EOF;
?>
