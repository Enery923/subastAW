<?php
require_once 'database.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

$query = $dbh->prepare("SELECT idTipoSubasta,now() as 'ahora',fecha_inicio, fecha_fin, " .
			" fecha_roundRobin FROM Subasta WHERE idSubasta=:id");
$query->bindValue(":id", (int)$_GET['id'], PDO::PARAM_INT);
$query->execute();
$resultado = $query->fetch();
$array = array();
$array['inicio'] = $resultado['fecha_inicio'];
$array['fin'] = $resultado['fecha_fin'];
$array['finSegundo'] = $resultado['fecha_roundRobin'];
$array['ahora'] = $resultado['ahora'];
$array['tipo'] = $resultado['idTipoSubasta'];

$json = json_encode($array);
echo $json;
?>
