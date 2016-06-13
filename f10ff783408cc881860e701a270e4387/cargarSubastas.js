var peticion;


function cargar() {
	enviarPeticion();
}

function enviarPeticion() {
	var url;

        url = "getSubastas.php?nocache=" + Math.random();

        peticionCierre = new XMLHttpRequest();
        peticionCierre.onreadystatechange = procesar;
        peticionCierre.open("GET", url, true);
        peticionCierre.send(null);
}

function procesar() {
	if (peticion.readyState == 4 && peticion.status == 200) {
		json = JSON.parse(peticion.responseText);
	}
}
