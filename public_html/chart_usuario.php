<?
require('lib/chart.php');
include_once('datos_usuario.php');
//width=18 height=12
if (!$_GET[tam]) { $width=600; $height=200; } 
if ($_GET[tam]=="gran") { $width=600; $height=200; } 
if ($_GET[tam]=="peq") { $width=300; $height=100; }
if ($_GET[tam]=="mini") { $width=200; $height=100; }

// Creamos el tamaño de la imagen
$chart = new chart($width, $height);
	// Para cuando quiera poner cache de imágenes
	//$chart = new chart($width, $height, "ticker+tamaño+hora+minuto");

//$chart->set_background_color("transparent", "ForestGreen");
//$chart->add_legend($_GET[ticker], "green");


if ($_GET["beneficio"]) {
// Dibujamos el beneficio de color amarillo
$chart->plot($beneficio, false, "yellow", "gradient", "green", 0 );
}

if ($_GET["invertido"]) {
// Dibujamos el invertido de color azul
$chart->plot($invertido, false, "red");
}
if ($_GET["saldo"]) {
// Dibujamos el saldo de color verde
$chart->plot($saldo, false, "green");
}

if ($_GET["total"]) {
// Dibujamos el total de color azul
$chart->plot($total, false, "blue");
}

// Elegimos que texto mostrar abajo
if ($_GET[dias]==1) {
	$chart->set_x_ticks ($hora, $format = "text");
} else {
	$chart->set_x_ticks ($fecha, $format = "text");
}

// Muestra con fuentes internas
//$chart->set_font("", "internal");


//$chart->plot($volumen, false, "black");
//$chart->add_legend("mm3", "red");
//$chart->set_labels("http://sukiweb.net", "Valor");
$chart->set_title($_GET["usuario"]." -  http://sukiweb.net");
$chart->stroke();
?>

