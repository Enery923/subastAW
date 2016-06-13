<?php
require_once 'database.php';
require_once 'funciones.php';
error_reporting(E_ALL);
ini_set('display_errors',1);

function getSubasta($id) {
	global $dbh;
	$query = $dbh->prepare("SELECT idEstado,idTipoSubasta,precioActual FROM Subasta " .
				" WHERE idSubasta=:id");
	$query->bindValue(":id", (int)$id, PDO::PARAM_INT);
	$query->execute();
	$datos = $query->fetch();
	$datos['nombreEstado'] = getEstado($datos['idEstado']);

	return $datos;
}

function getPujas($id, $fecha, $tipo) {
	global $dbh;
	if (!($tipo == 0 || $tipo == 1 || $tipo == 2 || $tipo == 3 || $tipo == 4)) {
		return array();
	}
	$postores = $tipo == 0 || $tipo == 2 || $tipo == 4 ? ",idUsuario" : "";
	$query = $dbh->prepare("SELECT date_format(fecha,'%d-%m-%Y %H:%i:%s.%f') as 'fecha', ".
				"Cantidad {$postores} FROM Pujas " .
				"WHERE idSubasta=:subasta AND ".
				"fecha > :fecha " .
				"order by fecha desc");
	$query->bindValue(":subasta", (int)$id, PDO::PARAM_INT);
	$query->bindValue(":fecha", $fecha, PDO::PARAM_STR);
	$query->execute();
	$pujas = $query->fetchAll();
	foreach ($pujas as &$puja) {
                $puja['fecha'] = substr($puja['fecha'],0,-3);
        }

	if ($tipo == 0 || $tipo == 2) {
		$query = $dbh->prepare("SELECT Nombre FROM Usuarios WHERE idUsuarios=:id");
        	$query->bindParam(":id", $postor, PDO::PARAM_INT);
        	foreach ($pujas as &$puja) {
                	$postor = (int)$puja['idUsuario'];
			$query->execute();
			$name = $query->fetch();
			$puja['postor'] = $name['Nombre'];
		}
        }

	return $pujas;
}

function crearJSON($datos, $pujas) {
	$array = array();

	$array['estado'] = $datos['idEstado'];
	$array['nombreEstado'] = $datos['nombreEstado'];
	$array['precio'] = $datos['precioActual'];
	$array['pujas'] = array();

	if (!($datos['idTipoSubasta'] == 0 || $datos['idTipoSubasta'] == 1)){
		foreach ($pujas as $puja) {
			$array['pujas'][] = array("valor" => $puja['Cantidad'],
						  "fecha" => $puja['fecha']);
		}
	}
	else {
		foreach ($pujas as $puja) {
                        $array['pujas'][] = array("valor" => $puja['Cantidad'],
                                                  "fecha" => $puja['fecha'],
						  "postor" => $puja['postor']);
                }
	}

	$cadena = json_encode($array);

	return $cadena;
}

function procesar() {
	$id = $_GET['id'];
	$fecha = urldecode($_GET['fecha']);
	$datos = getSubasta($id);
	$pujas = getPujas($id, $fecha, $datos['idTipoSubasta']);
	$json = crearJSON($datos, $pujas);

	echo $json;
}

procesar();
?>
