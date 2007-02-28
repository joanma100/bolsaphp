<?php 
if ($_GET["usuario"]) {
	require("login.php");

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>BolsaPHP</title>
	<meta name="generator" content="David Martín :: Suki_ :: ( http://sukiweb.net )" />
	<meta name="keywords" content="bolsaPHP, ibex35, bolsa, juego de bolsa" />
	<style type="text/css" media="screen">@import "http://bolsaphp.sukiweb.net/estilos.css";</style>
	<link rel="icon" href="/favicon.ico" type="image/x-icon" />';

	$SELECT="SELECT * FROM `carteras` WHERE `usuario`='".$_GET["usuario"]."' AND acciones>='1' ORDER BY ticker";
	$result=$db->get_results($SELECT);
	
	echo "Cartera de <a href=\"http://bolsaphp.sukiweb.net/index.php?usuario=".$_GET["usuario"]."\" target=\"_top\">".$_GET["usuario"]."</a> en <a href=\"http://bolsaphp.sukiweb.net\" target=\"_top\">BolsaPHP</a>";
	$row=0;
	while (isset($result[$row]->id)) {
		echo "<fieldset ";
			$valor=$db->get_row("SELECT valor , timestamp FROM quotes WHERE ticker='".$result[$row]->ticker."' ORDER BY 'timestamp' DESC LIMIT 0 , 1");
			$valor_actual=$valor->valor;
			if (($valor_actual*$result[$row]->acciones)>$result[$row]->saldo) {
				echo " class=\"sube\" ";
			} else if (($valor_actual*$result[$row]->acciones)<$result[$row]->saldo) {
				echo " class=\"baja\" ";
			}
		echo ">";
		
		echo "<legend><a href=\"http://bolsaphp.sukiweb.net/index.php?ticker=".$result[$row]->ticker."\" target=\"_top\" >".$result[$row]->ticker."</a> ".number_format($valor_actual, 2, ",", ".")." €  </legend>";
		
		echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">Acciones: </div><div class=\"col2der\">".$result[$row]->acciones."</div>";
		echo "<div class=\"col2izq\">Invertido: </div><div class=\"col2der\">".number_format($result[$row]->saldo, 2, ",", ".")." €</div>";
		echo "<div class=\"col2izq\">Actual: </div><div class=\"col2der\">".number_format(($valor_actual*$result[$row]->acciones), 2, ",", ".")." € </div>";
		$diferencia_porcentaje = ((($valor_actual*$result[$row]->acciones)*100)/$result[$row]->saldo)-100;
		echo "<div class=\"col2izq\">Diferencia %:</div><div class=\"col2der\">".number_format($diferencia_porcentaje, 2, ",", ".")." %</div>";
		$diferencia=($valor_actual*$result[$row]->acciones)-$result[$row]->saldo;
		echo "<div class=\"col2izq\">Diferencia: </div><div class=\"col2der\">".number_format($diferencia, 2, ",", ".")." € </div>"; 
		echo "</div>";
		echo "</fieldset>";

	$row++;
	}

}
?>