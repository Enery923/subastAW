<?php
require_once 'database.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

function update() {
	global $dbh;
	$subasta = isset($_GET['subasta']) ? (int)$_GET['subasta'] : "";
	if ($subasta === "") {
		updateAll();
	}
	else {
		updateSubasta($subasta);
	}
}

function updateSubasta($subasta) {
	global $dbh;
	$query = $dbh->prepare("SELECT idTipoSubasta FROM Subasta WHERE idSubasta=:id");
	$query->bindValue(":id", (int)$subasta, PDO::PARAM_INT);
	$query->execute();
	$resultado = $query->fetch();

	$tipo = $resultado['idTipoSubasta'];

	updatePrecioActual($subasta);
	updateEspera($subasta);
	updateAbrir($subasta);
	if ($tipo == 0 || $tipo == 1 || $tipo == 2 || $tipo == 3) {
		updateCerrar($subasta);
	}
	else if ($tipo == 4) {
		updateCerrar($subasta);
		updateHolandesa($subasta);
	}
	else if ($tipo == 5 || $tipo == 6) {
		updateCerrar($subasta);
	}
	else if($tipo == 7 || $tipo == 8) {
		updateRoundRobin($subasta);
	}
}

function updatePrecioActual($subasta) {
	global $dbh;
	$dbh->exec("UPDATE Subasta SET precioActual=precio WHERE " .
			"precioActual IS NULL AND precio IS NOT NULL " .
			" AND idSubasta={$subasta}");
}

function updateEspera($subasta) {
	global $dbh;
	$dbh->exec("UPDATE Subasta SET idEstado=7 WHERE now() < fecha_inicio AND " .
			"  idSubasta={$subasta}");
}

function updateAbrir($subasta) {
	global $dbh;
	$dbh->exec("UPDATE Subasta SET idEstado=3 WHERE now() < fecha_fin AND  " .
			" now() > fecha_inicio AND idSubasta={$subasta}");
}

function updateCerrar($subasta) {
	global $dbh;
	$dbh->exec("UPDATE Subasta SET idEstado=4 WHERE now() > fecha_fin " .
			" AND idSubasta={$subasta}");
}

function updateHolandesa($subasta) {
	global $dbh;
	$dbh->exec("UPDATE Subasta SET idEstado=4 WHERE EXISTS(SELECT * FROM Pujas " .
                        " WHERE idSubasta={$subasta}) AND idSubasta={$subasta}");
	$dbh->exec("UPDATE Subasta SET precioActual=precio - 100*TIMESTAMPDIFF(MINUTE, fecha_inicio, now()) " .
		    "  WHERE idSubasta={$subasta} AND fecha_fin > now() AND  " .
		    " idEstado!=4 AND idEstado!=7");
	$dbh->exec("UPDATE Subasta SET precioActual=0 WHERE precioActual<0 " .
		 "AND idSubasta={$subasta} AND fecha_fin > now()");
}

function updateRoundRobin($subasta) {
	global $dbh;
	$dbh->exec("UPDATE Subasta SET idEstado=6 WHERE now() > fecha_fin AND " .
			" now() < fecha_roundRobin AND idSubasta={$subasta}");
	$dbh->exec("UPDATE Subasta SET idEstado=4 WHERE now() > fecha_roundRobin AND " .
			"  idSubasta={$subasta}");
}

function updateAll() {
	global $dbh;

	$dbh->exec("UPDATE Subasta SET precioActual=precio WHERE " .
                        "precioActual IS NULL AND precio IS NOT NULL ");
	$dbh->exec("UPDATE Subasta SET idEstado=7 WHERE now() < fecha_inicio");
	$dbh->exec("UPDATE Subasta SET idEstado=3 WHERE now() < fecha_fin AND now() > fecha_inicio");
	$dbh->exec("UPDATE Subasta SET idEstado=4 WHERE now() > fecha_fin");
	$dbh->exec("UPDATE Subasta SET idEstado=6 WHERE now() > fecha_fin AND " .
			" now() < fecha_roundRobin AND (idTipoSubasta=7 OR idTipoSubasta=8)");
	$dbh->exec("UPDATE Subasta SET idEstado=4 WHERE now() > fecha_roundRobin AND" .
			" (idTipoSubasta=7 OR idTipoSubasta=8)");
	$dbh->exec("UPDATE Subasta s JOIN Pujas p " .
                   "ON s.idSubasta=p.idSubasta SET idEstado=4 WHERE idTipoSubasta=4");
	$dbh->exec("UPDATE Subasta SET precioActual=precio - 100*TIMESTAMPDIFF(MINUTE, fecha_inicio, now()) " .
                    "  WHERE idTipoSubasta=4 AND fecha_fin > now() AND idEstado!=4 AND idEstado!=7");
	$dbh->exec("UPDATE Subasta SET precioActual=0 WHERE precioActual<0 AND " .
                        " idTipoSubasta=4 AND fecha_fin > now()");
}

update();
?>
