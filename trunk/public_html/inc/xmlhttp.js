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



function listadoquotes(ticker, t) {
	if (t!=0) { 
		random=Math.floor(Math.random() * 9999)
		ajax = myXMLHttpRequest();
		urlquotes = "cache/"+ticker+".html?"+random;
		ajax.open("GET", urlquotes, true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				fade_out(ticker, 0);
				document.getElementById('listado-'+ticker).innerHTML =  ajax.responseText;
			}		
		}
		ajax.send(null);
	}
	tiemporandom=Math.floor(Math.random() * 1200) * 100; // 120 segundos
	tiempo = tiemporandom + 120000; // 120 segundos
	t=t+1;
	setTimeout('listadoquotes("'+ticker +'", '+t+');', tiempo);
}

function fade_out(ticker, i) {
	if (i==0) {
		document.getElementById('listado-'+ticker).style.backgroundColor = ' #FFFF66';
		setTimeout('fade_out("'+ticker +'", 1);', 300);
	}
	if (i==1) {
		document.getElementById('listado-'+ticker).style.backgroundColor = ' #FFFF99';
		setTimeout('fade_out("'+ticker +'", 2);', 300);
	}
	if (i==2) {
		document.getElementById('listado-'+ticker).style.backgroundColor = ' #FFFFCC';
		setTimeout('fade_out("'+ticker +'", 3);', 300);
	}
	if (i==3) {
		document.getElementById('listado-'+ticker).style.backgroundColor = ' white';
	}
}



function listadocarteras(ticker, acciones, saldo, notas) {
	ajax = myXMLHttpRequest();
	urlcartera = "ajax-cartera.php?ticker="+ticker+"&acciones="+acciones+"&saldo="+saldo+"&notas="+notas;
	ajax.open("GET", urlcartera, true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			fade_out_cartera(ticker, 0);
			document.getElementById('cartera-'+ticker).innerHTML =  ajax.responseText;
		}		
	}
	ajax.send(null);
	tiemporandom=Math.floor(Math.random() * 12000) * 100; // 120 segundos
	tiempo = tiemporandom + 240000; // 120 segundos
	setTimeout('listadocarteras("'+ticker +'", "'+acciones+'", "'+saldo+'", "'+notas+'");', tiempo);
}

function fade_out_cartera(ticker, i) {
	if (i==0) {
		document.getElementById('cartera-'+ticker).style.backgroundColor = ' #FFFF66';
		setTimeout('fade_out_cartera("'+ticker +'", 1);', 300);
	}
	if (i==1) {
		document.getElementById('cartera-'+ticker).style.backgroundColor = ' #FFFF99';
		setTimeout('fade_out_cartera("'+ticker +'", 2);', 300);
	}
	if (i==2) {
		document.getElementById('cartera-'+ticker).style.backgroundColor = ' #FFFFCC';
		setTimeout('fade_out_cartera("'+ticker +'", 3);', 300);
	}
	if (i==3) {
		document.getElementById('cartera-'+ticker).style.backgroundColor = ' white';
	}
}


function muestragrafica (ticker, dias, tam) {
	ajax = myXMLHttpRequest();
	url = "test.php?ticker=" + ticker + "&amp;dias=" + dias + "&amp;tam=";
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



function calculaacciones(form, comision)
{
  var comision = parseFloat(comision, 2);
  var valor = parseFloat(form.valor.value, 2);
  var total = parseFloat(form.total.value, 2);
  tasa = (total * comision) / 100;
  acciones = (total - tasa) / valor;
  form.acciones.value = parseInt(acciones);
}

function calculaprecio(form, comision)
{
  var comision = parseFloat(comision, 2);
  var valor = parseFloat(form.valor.value, 2);
  var acciones = parseFloat(form.acciones.value, 2);
  tasa = ((valor * acciones) * comision) / 100;
  total = (valor * acciones) + tasa; 
  total = round(total);
  form.total.value = parseFloat(total, 2);
}


function calculaporcentaje(form, valoractual)
{
	
  var valoractual = parseFloat(valoractual, 2);
  var valor = parseFloat(form.valor.value, 2);
  tantoporciento = ((valor * 100) / valoractual) - 100;
  tantoporciento = round(tantoporciento);
  form.tantoporciento.value = parseFloat(tantoporciento, 2);
}

function calculavalor(form, valoractual)
{
	
  var valoractual = parseFloat(valoractual, 2);
  var tantoporciento = parseFloat(form.tantoporciento.value, 2);
  valor = (((tantoporciento + 100) * valoractual) / 100);
  valor = round(valor);
  form.valor.value = parseFloat(valor, 2);
}


function round(value) {
       var rnum = value;
       var rlength = 2;
       if (rnum > 8191 && rnum < 10485) {
               rnum = rnum-5000;
               var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
       } else {
               var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
       }
       return newnumber;
}