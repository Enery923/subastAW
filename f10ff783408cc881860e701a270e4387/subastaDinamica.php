<?php
require_once 'database.php';
require_once 'update.php';
require_once 'funciones.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

function getDatosPujas($id, $tipo) {
	global $dbh;
	$usuario = $tipo == 1 || $tipo == 3 ? "" : ",idUsuario";
	$query = $dbh->prepare("SELECT date_format(fecha,'%d-%m-%Y %H:%i:%s.%f') as 'fecha',".
				  "Cantidad {$usuario} FROM Pujas WHERE idSubasta=:subasta " .
				   "ORDER BY fecha desc");
	$query->bindValue(":subasta", (int)$id, PDO::PARAM_INT);
	$query->execute();
	$resultado = $query->fetchAll();
	foreach ($resultado as &$puja) {
		$puja['fecha'] = substr($puja['fecha'],0,-3);
	}
	return $resultado;
}

function construirTablaPujas($datos, $tipo) {
	$postor = $tipo == 1 || $tipo == 3 ? "" : "<th>Postor</th>";
	$tabla = <<<EOF
        <table id="tablaPujas">
		<caption>Pujas</caption>
                <tr>
                <th>Cantidad</th>
                <th>Fecha</th>
		{$postor}
                </tr>
EOF;
	foreach($datos as $puja) {
		if ($tipo == 1 || $tipo == 3) {
			$postor = "";
		}
		else {
			$postor = "<td>".getNombreUsuario($puja['idUsuario'])."</td>";
		}
		$tabla .= <<<EOF
		<tr>
		<td>{$puja['Cantidad']}</td>
		<td>{$puja['fecha']}</td>
		{$postor}
		</tr>
EOF;
	}

	$tabla .= "</table>";
	return $tabla;
}

function construirForm($subasta,$tipo, $valor, $estado) {
	if ($estado == 3) {
		$url = "?subasta={$subasta}";
		$form = "<form id=\"form\" action=\"{$url}\" method=\"post\" >";
		if ($tipo == 1 || $tipo == 0) {
			$valor = $valor == "" ? "0" : $valor;
			$form .= "<input name=\"valor\" id=\"puja\" step=\"0.01\" " .
			"type=\"number\" min=\"{$valor}\" required />";
		}
		elseif ($tipo == 2 || $tipo == 3) {
			$form .= "<input name=\"valor\" id=\"puja\" step=\"0.01\" " .
			"type=\"number\" min=\"0\" max=\"{$valor}\" required />";
		}
		elseif ($tipo == 4) {
                	$form .= "<input name=\"valor\" id=\"puja\" step=\"0.01\" " .
			 "type=\"number\" min=\"0\" max=\"{$valor}\"  " .
			 "value=\"{$valor}\"  readonly />";
        	}
		$form .= "<input type=\"submit\" value=\"pujar\" />";
		$form .= "</form>";
	}
	else if($estado == 7) {
		$form = "<p id=\"form\">Subasta no iniciada.</p>";
	}
	else {
		$form = "<p id=\"form\">Subasta cerrada</p>";
	}
	return $form;
}

function construirScript($estado) {
        if ($estado == 3){
                $script = "<script type=\"text/javascript\" src=\"ajax.js\"></script>";
        }
        else {
                $script = "";
        }
        return $script;
}


function crearPagina($mensaje) {
	$id = $_GET['subasta'];

	$subasta = getDatosSubasta($id);
	$tablaSubasta = construirTablaSubasta($subasta);
	$lote = getDatosLote($id);
	$tablaLote = construirTablaLote($lote);
	$pujas = getDatosPujas($id, $subasta['idTipoSubasta']);
	$tablaPujas = construirTablaPujas($pujas, $subasta['idTipoSubasta']);
	$form = construirForm($id, $subasta['idTipoSubasta'], $subasta['precioActual'],$subasta['idEstado']);
	$script = construirScript($subasta['idEstado']);
	echo <<<EOF
	<!doctype html>
        <html lang="es">
        <head>
                <meta charset="UTF-8">
		{$script}
		<link rel="stylesheet" type="text/css" href="css/subastas.css" />
                <title>Subasta</title>
        </head>
        <body onload="cargar()">
		<div id="contenidoSubasta">
                {$tablaSubasta}
		{$tablaLote}
		{$tablaPujas}
		{$form}
		</div>
		<footer id="aviso">{$mensaje}<br>
		<a href="listaSubastas.php">Volver al listado</a>
		</footer>
        </body>
        </html>
EOF;
}

function insertarPuja() {
	global $dbh;
	session_start();
	session_write_close();
	$valor = $_POST['valor'];
	$postor = (int)getUsuario($_SESSION['username']);
	$subasta = $_GET['subasta'];
	$tipo = getTipo($subasta);
	$comparar = $tipo == 1 || $tipo == 0 ? ">=" : "<=";
	$aviso =  "Puja no válida";

	if ($tipo == 4) {
		return insertarPujaHolandesa($postor);
	}

	$dbh->exec("SET autocommit=0");
	$dbh->exec("LOCK TABLES Pujas WRITE,Pujas as p1 WRITE, Subasta WRITE");

	$query = $dbh->prepare("INSERT INTO Pujas(idUsuario, idSubasta, Cantidad) " .
		      " SELECT :postor, :subasta, :valor FROM dual " .
		      " WHERE NOT EXISTS (SELECT * FROM Pujas as p1 " .
		      " WHERE Cantidad {$comparar} convert(:valordos,decimal(12,2)) AND  " . 
		      " idSubasta=:subastados) AND " .
		      " EXISTS (SELECT * FROM Subasta WHERE fecha_fin > now()  " .
		      " AND idSubasta=:subastatres)");
	$query->bindValue(":postor", $postor, PDO::PARAM_INT);
	$query->bindValue(":subasta", (int)$subasta, PDO::PARAM_INT);
	$query->bindValue(":valor" , $valor, PDO::PARAM_STR);
	$query->bindValue(":valordos" , $valor, PDO::PARAM_STR);
	$query->bindValue(":subastados", (int)$subasta, PDO::PARAM_INT);
	$query->bindValue(":subastatres", (int)$subasta, PDO::PARAM_INT);
	$query->execute();

	$exito = false;
	if ($exito = $query->rowCount() == 1) {
		$query = $dbh->prepare("UPDATE Subasta SET precioActual=:valor WHERE idSubasta=:id");
		$query->bindValue(":valor", $valor, PDO::PARAM_STR);
		$query->bindValue(":id", (int)$subasta, PDO::PARAM_INT);
		$query->execute();
		$aviso = "Puja exitosa";
	}

	$dbh->exec("UNLOCK TABLES");
	$dbh->exec("SET autocommit=1");

	if ($exito) {
		guardarLog($postor, $subasta, $valor);
	}

	return $aviso;
}

function insertarPujaHolandesa() {
	global $dbh;

	session_start();
        session_write_close();
	$postor = (int)getUsuario($_SESSION['username']);
	$aviso = "Puja no válida";

	$dbh->exec("SET autocommit=0");
        $dbh->exec("LOCK TABLES Pujas WRITE,Pujas as p1 WRITE, Subasta WRITE");

	$query = $dbh->prepare("SELECT precioActual FROM Subasta WHERE idSubasta=:id");
	$query->bindValue(":id", (int)$_GET['subasta'], PDO::PARAM_INT);
	$query->execute();
	$resultado = $query->fetch();
	$precio = $resultado['precioActual'];

	$query = $dbh->prepare("INSERT INTO Pujas (idSubasta,Cantidad,idUsuario) " .
				" SELECT :subasta,:valor,:postor FROM dual " .
				"WHERE EXISTS (SELECT * FROM Subasta WHERE fecha_fin > now() " .
				" AND idSubasta=:subastados) AND NOT EXISTS (" .
				" SELECT * FROM Pujas as p1 WHERE idSubasta=:subastatres)");
	$query->bindValue(":subasta", (int)$_GET['subasta'], PDO::PARAM_INT);
	$query->bindValue(":valor", $precio, PDO::PARAM_STR);
	$query->bindValue(":postor", $postor, PDO::PARAM_INT);
	$query->bindValue(":subastados", (int)$_GET['subasta'], PDO::PARAM_INT);
	$query->bindValue(":subastatres", (int)$_GET['subasta'], PDO::PARAM_INT);
	$query->execute();

	$exito = false;
	if($exito = $query->rowCount() === 1) {
		$query = $dbh->prepare("UPDATE Subasta SET estado=4,precioActual=:precio WHERE idSubasta=:id");
        	$query->bindValue(":id", (int)$_GET['subasta'], PDO::PARAM_INT);
		$query->bindValue(":precio", $precio, PDO::PARAM_STR);
        	$query->execute();
		$aviso = "Puja exitosa";
	}

	$dbh->exec("UNLOCK TABLES");
        $dbh->exec("SET autocommit=1");

	if ($exito) {
		guardarLog($postor, $_GET['subasta'], $precio);
	}

	return $aviso;
}

comprobarLogin();
if (isset($_POST['valor'])) {
	$resultado = insertarPuja();
	session_start();
	$_SESSION['resultado'] = $resultado;
	session_write_close();
	header("Location: subastaDinamica.php?subasta={$_GET['subasta']}");
	die();
}

$mensaje ="";
session_start();
if (isset($_SESSION['resultado'])) {
	$mensaje = $_SESSION['resultado'];
	unset($_SESSION['resultado']);
}
session_write_close();
crearPagina($mensaje);
?>
