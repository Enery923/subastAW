<?php
require_once 'database.php';

function getDatosSubastas() {
	global $dbh;

	$query->prepare("SELECT * FROM Subasta");
	$query->execute();

	$subastas = $query->fetchAll();

	return $subastas;
}

function crearJSON($subastas) {
	$array = array();
	$array['subastas'] = array();

	foreach ($subastas as $subasta) {
		$array['subastas'][] = array();
	}

	$cadena = json_encode($array);

	return $cadena;
}

function procesar() {
	$subastas = getDatosSubastas();
	$json = creearJSON($subastas);

	echo $json;
}
