<?
require('lib/chart.php');
include_once('datos.php');
//width=18 height=12
if (!$_GET[tam]) { $width=200; $height=100; } 
if ($_GET[tam]=="gran") { $width=600; $height=200; } 
if ($_GET[tam]=="peq") { $width=300; $height=100; }
if ($_GET[tam]=="mini") { $width=200; $height=100; }

// Creamos el tamaño de la imagen
//$chart = new chart($width, $height);

$hora=date("i");
$nombrecache="ticker-".$_GET["ticker"]."-".$_GET["tam"]."-".$_GET["dias"]."-".$hora;
$chart = new chart($width, $height, $nombrecache.".png");


// Elegimos que media móvil dibujar
if ($_GET[mm3]) { 
	$chart->plot($mm3, false, "red");
	$chart->add_legend("MM3", "red");
}

if ($_GET[mm10]) { 
	$chart->plot($mm10, false, "green");
	$chart->add_legend("MM10", "green");
}

//Dibujamos el cierre
if ($_GET[cierre]) {
	$chart->plot($cierre, false, "black", "cross", false, 4);
}

//Dibujamos apertura
if ($_GET[apertura]) {
	$chart->plot($apertura, false, "gray", "box", "black");
}

// Dibujamos el valor de color azul
$chart->plot($valor, false, "blue");


// Dibujamos el rango día bajo
//$chart->plot($rango_dia_bajo, false, "black", "points");

// Elegimos que texto mostrar abajo
if ($_GET[dias]==1) {
	$chart->set_x_ticks ($hora, $format = "text");
} else {
	$chart->set_x_ticks ($fecha, $format = "text");
}

$chart->set_title($_GET[ticker]." -  http://sukiweb.net");
$chart->stroke();
?>

