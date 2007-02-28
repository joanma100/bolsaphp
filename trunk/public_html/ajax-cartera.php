<?php
include_once "login.php";
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>BolsaPHP</title>
<meta name="generator" content="David Martín :: Suki_ :: ( http://sukiweb.net )" />
<meta name="keywords" content="bolsaPHP, ibex35, bolsa, juego de bolsa" />
</head>';

$row=0;
$result[$row]->ticker=$_GET["ticker"];
$result[$row]->acciones=$_GET["acciones"];
$result[$row]->saldo=$_GET["saldo"];
$result[$row]->notas=$_GET["notas"];

$SELECT= "SELECT valor, timestamp FROM quotes WHERE ticker='".$result[$row]->ticker."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
$valor = $db->get_results($SELECT);
$valor_actual=$valor[0]->valor*$result[$row]->acciones;
$comision=(($valor_actual*$config['comision'])/100)*2;
echo "\r\n";
			
if ($valor_actual<$result[$row]->saldo) { echo "<fieldset class=\"baja\">"; }
else if ($valor_actual>$result[$row]->saldo) { echo "<fieldset class=\"sube\">"; }
else { echo "<fieldset>"; }
echo "<legend><a href=\"index.php?ticker=".$result[$row]->ticker."\">".$result[$row]->ticker."</a> ".$valor[0]->valor." € </legend>";

echo "<div class=\"doscolumnas\">";
echo "<div class=\"col2izq\">Acciones: </div><div class=\"col2der\">".$result[$row]->acciones."</div>";
echo "<div class=\"col2izq\">Invertido: </div><div class=\"col2der\">".number_format($result[$row]->saldo, 2, ",", ".")." €</div>";
echo "<div class=\"col2izq\">Actual: </div><div class=\"col2der\">".number_format($valor_actual, 2, ",", ".")." € </div>";
$diferencia=$valor_actual-$result[$row]->saldo;
			
$diferencia_porcentaje = (($valor_actual*100)/$result[$row]->saldo)-100;
echo "<div class=\"col2izq\">Diferencia %:</div><div class=\"col2der\">".number_format($diferencia_porcentaje, 2, ",", ".")." %</div>";
echo "<div class=\"col2izq\">Diferencia: </div><div class=\"col2der\">".number_format($diferencia, 2, ",", ".")." € </div>"; 
			
$alertas=$db->get_var("SELECT count(id) from alertas where usuario='".$_SESSION["usuario"]."' AND ticker='".$result[$row]->ticker."'");
$alertas_cumplidas=$db->get_var("SELECT count(id) from alertas where usuario='".$_SESSION["usuario"]."' AND ticker='".$result[$row]->ticker."' AND estado='AVISADO'");
//Cambiar al activar alertas
if ($alertas>=1) {
	echo "<div class=\"col2izq\">Alertas: </div><div class=\"col2der\">(<strong>".$alertas_cumplidas."</strong> / ".$alertas.")</div>";
}

if ($result[$row]->notas) {
	echo "<div class=\"col2izq\"><b>Tus notas:</b></div>";
	$notas=txt_shorter($result[$row]->notas);
	echo "<div class=\"col2der\"><a href=\"index.php?ticker=".$result[$row]->ticker."\">".$notas."</a>";
	echo "</div>";
}
echo "</div>"; // fin doscolumnas
echo "</fieldset>";

?>	