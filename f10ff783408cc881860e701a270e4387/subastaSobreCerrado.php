<?php
require_once 'database.php';
require_once 'update.php';
require_once 'funciones.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

function haPujado($subasta) {
	global $dbh;
	session_start();
        session_write_close();
        $postor = (int)getUsuario($_SESSION['username']);

	$query = $dbh->prepare("SELECT EXISTS(SELECT * FROM Pujas WHERE " .
			     "idSubasta=:subasta AND idUsuario=:postor)");
	$query->bindValue(":subasta", (int)$subasta, PDO::PARAM_INT);
	$query->bindValue(":postor", $postor, PDO::PARAM_INT);
	$query->execute();
	$resultado = $query->fetch();
	$haPujado = $resultado[0] == 1;

	return $haPujado;
}

function construirForm($subasta, $tipo, $valor, $estado) {
	if ($estado == 3) {
		$url = "?subasta={$subasta}";
		$form = "<form id=\"form\" action=\"{$url}\" method=\"post\" >";
		if ($tipo == 5) {
			$valor = $valor == "" ? "0" : $valor;
			$form .= "<input name=\"valor\" id=\"puja\" step=\"0.01\" " .
			"type=\"number\" min=\"{$valor}\" required />";
		}
		elseif ($tipo == 6) {
			$form .= "<input name=\"valor\" id=\"puja\" step=\"0.01\" " .
			"type=\"number\" min=\"0\" max=\"{$valor}\" required />";
		}
		$form .= "<input type=\"submit\" value=\"pujar\" />";
		$form .= "</form>";

		if (haPujado($subasta)) {
                	$form = "<p id=\"form\">Ya has hecho una puja</p>";
        	}
	}
	else if($estado == 7) {
		$form = "<p id=\"form\">Subasta no iniciada</p>";
	}
	else {
		$form = "<p id=\"form\">Subasta cerrada.</p>";
	}
	return $form;
}

function construirScript($estado) {
        if ($estado != 7 || $estado != 4){
                $script = "<script type=\"text/javascript\" src=\"ajaxSobreCerrado.js\"></script>";
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
		{$form}
		</div>
		<footer>{$mensaje}<br>
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
	$comparar = $tipo == 5 ? ">=" : "<=";
	$aviso = "Puja no válida";

	$dbh->exec("SET autocommit=0");
	$dbh->exec("LOCK TABLES Pujas WRITE,Pujas as p1 WRITE, Subasta WRITE");

	$query = $dbh->prepare("INSERT INTO Pujas(idUsuario, idSubasta, Cantidad) " .
		      " SELECT :postor, :subasta, :valor FROM dual " .
		      " WHERE NOT EXISTS (SELECT * FROM Pujas as p1 " .
		      " WHERE Cantidad {$comparar} convert(:valordos,decimal(12,2)) " .
		      " AND idSubasta=:subastados OR idUsuario=:postordos " .
		      " AND idSubasta=:subastacuatro) AND " .
		      " EXISTS (SELECT * FROM Subasta WHERE fecha_fin > now()  " .
		      " AND idSubasta=:subastatres)");
	$query->bindValue(":postor", $postor, PDO::PARAM_INT);
	$query->bindValue(":subasta", (int)$subasta, PDO::PARAM_INT);
	$query->bindValue(":valor" , $valor, PDO::PARAM_STR);
	$query->bindValue(":postordos", $postor, PDO::PARAM_INT);
	$query->bindValue(":valordos" , $valor, PDO::PARAM_STR);
	$query->bindValue(":subastados", (int)$subasta, PDO::PARAM_INT);
	$query->bindValue(":subastatres", (int)$subasta, PDO::PARAM_INT);
	$query->bindValue(":subastacuatro", (int)$subasta, PDO::PARAM_INT);
	$query->execute();

	$exito = false;
	if ($exito = $query->rowCount() ==1) {
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

comprobarLogin();
if (isset($_POST['valor'])) {
	$resultado = insertarPuja();
	session_start();
        $_SESSION['resultado'] = $resultado;
        session_write_close();
	header("Location: subastaSobreCerrado.php?subasta={$_GET['subasta']}");
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
