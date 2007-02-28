<?
require('lib/chart.php');
include_once('datos.php');

//$chart = new chart(600, 100);

$hora=date("i");
$nombrecache="volumen-".$_GET["ticker"]."-".$_GET["dias"]."-".$hora;
$chart = new chart(600, 100, $nombrecache.".png");

$chart->plot($volumen, false, "blue", "impulse");



$chart->set_x_ticks ($fecha, $format = "text");
$chart->set_title($_GET[ticker]." -  http://sukiweb.net");
$chart->stroke();
?>

