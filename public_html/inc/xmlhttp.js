<script type="text/javascript">
function myXMLHttpRequest ()
{
	var xmlhttplocal;
	try {
		xmlhttplocal = new ActiveXObject ("Msxml2.XMLHTTP")}
	catch (e) {
		try {
			xmlhttplocal = new ActiveXObject ("Microsoft.XMLHTTP")
		}
		catch (E) {
			xmlhttplocal = false;
		}
  	}
	if (!xmlhttplocal && typeof XMLHttpRequest != 'undefined') {
		try {
			var xmlhttplocal = new XMLHttpRequest ();
		}
		catch (e) {
	  		var xmlhttplocal = false;
			alert ('couldn\'t create xmlhttp object');
		}
	}
	return (xmlhttplocal);
}


function muestragrafica (ticker, dias, tam) {
	ajax = myXMLHttpRequest();
	url = "test.php?ticker=" + ticker + "&dias=" + dias + "&tam=";
	ajax.open("GET", url, true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			visualizador.innerHTML = ajax.responseText;
		}
		 else {
			visualizador.innerHTML = 'Cargando…';
		}
	}
	ajax.send(null);
}


function calculaprecio(form)
{
  var valor = parseFloat(form.valor.value, 2);
  var acciones = parseInt(form.acciones.value);
  total = valor * acciones;
  form.total.value = parseFloat(total, 2);
}

function calculaacciones(form)
{
  var valor = parseFloat(form.valor.value, 2);
  var total = parseFloat(form.total.value, 2);
  acciones = total / valor;
  form.acciones.value = parseInt(acciones);
}

</script>