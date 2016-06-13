<?php 
require_once 'database.php';

function getDatosSubasta($subasta) {
        global $dbh;
        $query = $dbh->prepare("SELECT date_format(fecha_inicio,'%d-%m-%Y %H:%i:%s') as 'inicio'," .
                        "date_format(fecha_fin,'%d-%m-%Y %H:%i:%s') as 'fin',idSubasta,"  .
                        " date_format(fecha_roundRobin,'%d-%m-%Y %H:%i:%s') as 'fechaRoundRobin', " .
                        " idTipoSubasta, idEstado,precio,precioActual FROM Subasta " .
                        "WHERE idSubasta=:id");
        $query->bindValue(":id", (int)$subasta, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetch();

	$idUsuario = getIdUsuario($subasta);
	$nombreUsuario = getNombreUsuario($idUsuario);
	$resultado['usuario'] = $nombreUsuario;

	$tipo = getTipoSubasta($resultado['idTipoSubasta']);
	$resultado['tipoSubasta'] = $tipo;

	$estado = getEstado($resultado['idEstado']);
	$resultado['estadoSubasta'] = $estado;

        return $resultado;
}

function getNombreUsuario($id) {
        global $dbh;
        $query = $dbh->prepare("SELECT Nombre FROM Usuarios WHERE idUsuarios=:id");
        $query->bindValue(":id", (int)$id, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetch();
        return $resultado['Nombre'];
}

function getIdUsuario($subasta) {
	global $dbh;
	$query = $dbh->prepare("SELECT idUsuarios FROM Usuario_SUbasta WHERE idSUbasta=:id");
        $query->bindValue(":id", (int)$subasta, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetch();
        $id = $resultado['idUsuarios'];

	return $id;
}

function getTipo($subasta) {
        global $dbh;
        $query = $dbh->prepare("SELECT idTipoSubasta FROM Subasta WHERE idSubasta=:id");
        $query->bindValue(":id", (int)$subasta, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetch();

        return $resultado['idTipoSubasta'];
}

function getTipoSubasta($tipo) {
	global $dbh;
	$query = $dbh->prepare("SELECT DescripcionSubasta FROM TiposSubasta " .
                                " WHERE idtiposSubasta=:id");
        $query->bindValue(":id", (int)$tipo, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetch();
        $tipo = $resultado['DescripcionSubasta'];

	return $tipo;

}

function getEstado($estado) {
	global $dbh;
	$query = $dbh->prepare("SELECT descripcionEstado FROM Estados WHERE idEstados=:id");
	$query->bindValue(":id", (int)$estado, PDO::PARAM_INT);
	$query->execute();
	$resultado = $query->fetch();
	$estado = $resultado['descripcionEstado'];

	return $estado;
}

function construirTablaSubasta($datos) {
        $tabla = <<<EOF
        <table id="tablaSubasta">
                <caption>Datos de la subasta</caption>
                <tr>
                <th>Id</th>
                <th>Inicio</th>
                <th>Fin</th>
		<th>Fin Round Robin</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Precio Inicial</th>
		<th>Precio Actual</th>
                <th>Subastador</th>
                </tr>
                <tr>
                <td id="id">{$datos['idSubasta']}</td>
                <td id="inicio">{$datos['inicio']}</td>
                <td id="fin">{$datos['fin']}</td>
		<td id="fechaRoundRobin">{$datos['fechaRoundRobin']}</td>
                <td id="tipo">{$datos['tipoSubasta']}</td>
                <td id="estado">{$datos['estadoSubasta']}</td>
                <td id="precio">{$datos['precio']}</td>
		<td id="precioActual">{$datos['precioActual']}</td>
                <td>{$datos['usuario']}</td>
                </tr>
        </table>
EOF;
        return $tabla;

}

function getDatosLote($id) {
        global $dbh;
        $query = $dbh->prepare("SELECT idProducto FROM Lotes WHERE idSubasta=:subasta");
        $query->bindValue(":subasta", (int)$id, PDO::PARAM_INT);
        $query->execute();
        $resultado = $query->fetchAll();

	$productos = array();

	foreach ($resultado as $idProducto) {
        	$query = $dbh->prepare("SELECT date_format(fecha,'%d-%m-%Y %H:%i:%s') as 'fecha'," .
        	                        "idProductos, concepto, descripcion, " .
					" imagen FROM Productos " .
        	                        "WHERE idProductos=:producto");
        	$query->bindValue(":producto", (int)$idProducto[0], PDO::PARAM_INT);
        	$query->execute();
        	$resultado = $query->fetch();
		$productos[] = $resultado;
	}

        return $productos;
}

function construirTablaLote($datos){
        $tabla = <<<EOF
        <table id="tablaLote">
                <caption>Lote</caption>
                <tr>
                <th>Id</th>
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Fecha Inserción</th>
                <th>Imagen</th>
                </tr>
EOF;
        foreach ($datos as $producto) {
	       if ($producto['imagen'] == "") {
			$imagen = "";
		}
		else {
			$imagen = "<img src=\"{$producto['imagen']}\" height=\"50\" width=\"50\" />";
		}
                $tabla .= <<<EOF
                <tr>
                <td>{$producto['idProductos']}</td>
                <td>{$producto['concepto']}</td>
                <td>{$producto['descripcion']}</td>
                <td>{$producto['fecha']}</td>
                <td>{$imagen}</td>
                </tr>
EOF;
        }
        $tabla .= "</table>";
        return $tabla;

}


function getUsuario($nombre) {
	global $dbh;

	$query = $dbh->prepare("SELECT idUsuarios FROM Usuarios WHERE Nombre=:nombre");
	$query->bindValue(":nombre", $nombre, PDO::PARAM_STR);
	$query->execute();
	$resultado = $query->fetch();

	return $resultado['idUsuarios'];
}

function comprobarLogin() {
	session_start();
	session_write_close();

	if (isset($_SESSION['username']) && $_SESSION['perfil'] == 0) {
		return;
	}
	else {
		header("Location: index.php");
                die();
	}
}

function guardarLog($idUsuario, $idSubasta, $valorPuja) {
	global $dbh;

	$nombreUsuario = getNombreUsuario($idUsuario);
	$descripcion = "El usuario {$nombreUsuario} ha realizado una puja por valor de {$valorPuja}" .
			" en la subasta {$idSubasta}.";
	$query = $dbh->prepare("INSERT INTO Log (Descripcion, idUsuario, idSubasta) VALUES (:descripcion,:usuario,:subasta)");
	$query->bindValue(":descripcion", $descripcion, PDO::PARAM_STR);
	$query->bindValue(":usuario", $idUsuario, PDO::PARAM_INT);
	$query->bindValue(":subasta", (int)$idSubasta, PDO::PARAM_INT);

	$query->execute();
}
?>
