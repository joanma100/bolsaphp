<?
require('lib/chart.php');
include_once('datos.php');

$chart = new chart(600, 100);


$chart->plot($volumen, false, "blue", "impulse");

$chart->set_x_ticks ($fecha, $format = "text");
$chart->set_title($_GET[ticker]." -  http://sukiweb.net");
$chart->stroke();
?>

