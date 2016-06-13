var peticion;
var peticionCierre;
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
}

function cargar(){
	inicializarGlobales();
	getFechaUpdate();
	actualizarDatos();
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
	var json;
	var tiempoCierre;

	if (peticionCierre.readyState == 4 && peticionCierre.status == 200) {
		json = JSON.parse(peticionCierre.responseText);
		a = json.fin.split(/[- :.]/);
		fin = new Date(a[0], a[1]-1,a[2],a[3],a[4],a[5]);
		a = json.ahora.split(/[- :.]/);
		ahora = new Date(a[0], a[1]-1,a[2],a[3],a[4],a[5]);
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

function actualizarDatos() {
	var cadenaFecha;
	var url;

	url = "actualizar.php?nocache=" + Math.random() + "&id=" + idSubasta;
	url += "&fecha=";
	
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
		if (abierta) {
			setTimeout(actualizarDatos, intervalo);
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
		if (inputPuja) {
                        form = inputPuja.parentNode;
                        padre = form.parentNode;
                        parrafo = document.createElement("p");
                        parrafo.setAttribute("id", "form");
                        texto = document.createTextNode("Subasta cerrada.");
                        parrafo.appendChild(texto);
                        padre.replaceChild(parrafo, form);
                }
                else {
                        texto = document.createTextNode("Subasta cerrada");
                        parrafo = document.getElementById("form");
                        parrafo.replaceChild(texto, parrafo.lastChild);
                }
                return false;
        }
        return true;
}

function modificarPrecio(precio) {
	var min;
	var max;

	celdaPrecio.innerHTML = precio;

	if (!inputPuja) {
		return;
	}	

	min = inputPuja.getAttribute("min");
	max = inputPuja.getAttribute("max");

	if (!max){
		inputPuja.setAttribute("min", precio);
	}
	else if (min == 0) {
		inputPuja.setAttribute("max", precio);
	}
}
