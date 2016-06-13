var peticion;
var timeoutHolandesa;
var peticionCierre;
var peticionHolandesa;
var fecha;
var idSubasta;
var intervalo;
var tablaPujas;
var inputPuja;
var celdaEstado;
var celdaPrecio;

function inicializarGlobales() {
        celdaPrecio = document.getElementById("precioActual");
        celdaEstado = document.getElementById("estado");
        inputPuja = document.getElementById("puja");
        tablaPujas = document.getElementById("tablaPujas");
        idSubasta = document.getElementById("id").innerHTML;
        intervalo = 3000;
	actualizarFecha();
}

function cargar(){
	inicializarGlobales();
	getFechaUpdate();
	actualizarDatos();
}

function actualizarFecha() {
	var cadenaFecha;
        var a;
	var rows;

	rows = tablaPujas.rows;

	if (rows.length > 1) {
		cadenaFecha = tablaPujas.rows[1].cells[1].innerHTML;
        	a = cadenaFecha.split(/[- :.]/);
        	fecha = new Date(a[2], a[1]-1, a[0], a[3], a[4], a[5], a[6]);
	}
	else {
		fecha="";
	}
}

function getFechaUpdate() {
	var url;

	url = "fecha.php?nocache=" + Math.random();
	url += "&id=" + idSubasta;

	peticionCierre = new XMLHttpRequest();
        peticionCierre.onreadystatechange = procesaFecha;
        peticionCierre.open("GET", url, true);
        peticionCierre.send(null);
}

function procesaFecha() {
	var cadenaFecha;
	var a;
	var fin;
	var ahora;
	var inicio;
	var segsInicio;
	var segsAhora;
	var json;
	var tiempoCierre;
	var tiempoHolandesa;

	if (peticionCierre.readyState == 4 && peticionCierre.status == 200) {
		json = JSON.parse(peticionCierre.responseText);
		a = json.fin.split(/[- :.]/);
		fin = new Date(a[0], a[1]-1,a[2],a[3],a[4],a[5]);
		a = json.ahora.split(/[- :.]/);
		ahora = new Date(a[0], a[1]-1,a[2],a[3],a[4],a[5]);
		if (json.tipo == 4) {
			a = json.inicio.split(/[- :.]/);
			inicio = new Date(a[0], a[1]-1,a[2],a[3],a[4],a[5]);
			segsInicio = inicio.getSeconds();
			segsAhora = ahora.getSeconds();
			if (segsInicio > segsAhora) {
				tiempoHolandesa = segsInicio - segsAhora;
			}
			else if (segsInicio < segsAhora) {
				tiempoHolandesa = 60 - segsAhora + segsInicio;
			}
			else {
				tiempoHolandesa = 0;
			}
			tiempoHolandesa *= 1000;
			timeoutHolandesa = setTimeout(updateHolandesa, tiempoHolandesa);
		}

		tiempoCierre = fin.getTime() - ahora.getTime();
		setTimeout(update, tiempoCierre + 1000);
	}
}

function update() {
	var url;

        url = "update.php?nocache=" + Math.random();
	url += "&subasta=" + idSubasta;

        peticionCierre = new XMLHttpRequest();
        peticionCierre.open("GET", url, true);
        peticionCierre.send(null);
}

function updateHolandesa() {
	var url;

        url = "update.php?nocache=" + Math.random();
        url += "&subasta=" + idSubasta;

        peticionHolandesa = new XMLHttpRequest();
	peticionHolandesa.onreadystatechange = repetir;
        peticionHolandesa.open("GET", url, true);
        peticionHolandesa.send(null);
}

function repetir() {
	if (peticionHolandesa.readyState == 4 && peticionHolandesa.status == 200) {
		timeoutHolandesa = setTimeout(updateHolandesa, 60000);
	}
}
function actualizarDatos() {
	var cadenaFecha;
	var url;
	if (fecha != "") {
		cadenaFecha = dateToString();
		cadenaFecha = encodeURIComponent(cadenaFecha);
	}
	else {
		cadenaFecha = "";
	}
	url = "actualizar.php?nocache=" + Math.random() + "&id=" + idSubasta;
	url += "&fecha=" + cadenaFecha;
	
	peticion = new XMLHttpRequest();
        peticion.onreadystatechange = procesarDatos;
        peticion.open("GET", url, true);
	peticion.send(null);
}

function dateToString() {
	var cadena = "";
	var year;
	var mes;
	var dia;
	var horas;
	var minutos;
	var segundos;
	var milisegundos;
	
	year = fecha.getFullYear().toString();
	mes = (fecha.getMonth() + 1).toString();
	mes = mes.length == 1 ? "0" + mes : mes;
	dia = fecha.getDate().toString();
	dia = dia.length == 1 ? "0" + dia : dia;
	horas = fecha.getHours().toString();
        horas = horas.length == 1 ? "0" + horas : horas;
        minutos = fecha.getMinutes().toString();
        minutos = minutos.length == 1 ? "0" + minutos : minutos;
	segundos = fecha.getSeconds().toString();
	segundos = segundos.length == 1 ? "0" + segundos : segundos;
	milisegundos = fecha.getMilliseconds().toString();
	milisegundos = milisegundos.length == 1 ? "00" + milisegundos : milisegundos;
	milisegundos = milisegundos.length == 2 ? "0" + milisegundos : milisegundos;

	cadena += year + "-" + mes + "-" + dia + " ";
	cadena += horas + ":" + minutos + ":" + segundos + "." + milisegundos;

	return cadena;
}



function procesarDatos() {
	var json;
	var abierta;

	if (peticion.readyState == 4 && peticion.status == 200) {
		json = JSON.parse(peticion.responseText);
		modificarPrecio(json.precio);
		abierta = modificarEstado(json.estado, json.nombreEstado);
		modificarPujas(json.pujas);
		if (abierta) {
			setTimeout(actualizarDatos, intervalo);
		}
		else {
			clearTimeout(timeoutHolandesa);
		}			
	}
}

function modificarEstado(estado, nombreEstado) {
        var form;
        var parrafo;
        var padre;
        var texto;

        celdaEstado.innerHTML = nombreEstado;

        if (estado != 3) {
                form = inputPuja.parentNode;
                padre = form.parentNode;
                parrafo = document.createElement("p");
                texto = document.createTextNode("Subasta cerrada.");
                parrafo.appendChild(texto);
                padre.replaceChild(parrafo, form);

                return false;
        }
        return true;
}

function modificarPrecio(precio) {
	var min;
	var max;

	celdaPrecio.innerHTML = precio;

	min = inputPuja.getAttribute("min");
	max = inputPuja.getAttribute("max");

	if (!max){
		inputPuja.setAttribute("min", precio);
	}
	else if (min == 0) {
		inputPuja.setAttribute("max", precio);
	}

	if (inputPuja.readOnly) {
		inputPuja.value = precio;
	}
}

function modificarPujas(pujas) {
	var celdaFecha;
	var celdaValor;
	var celdaPostor;
	var fila;

	for (var i = 0; i < pujas.length; i++) {
		fila = tablaPujas.insertRow(1);
		celdaValor = fila.insertCell(0);
		celdaFecha = fila.insertCell(1);

		celdaValor.innerHTML = pujas[i].valor;
		celdaFecha.innerHTML = pujas[i].fecha;

		if (pujas[i].postor) {
			celdaPostor = fila.insertCell(2);
			celdaPostor.innerHTML = pujas[i].postor;
		}
	}

	if (pujas.length > 0) {
		actualizarFecha();
	}
}
