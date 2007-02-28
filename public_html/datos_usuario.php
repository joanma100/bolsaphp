<?
require("login.php");
include_once("config.php");

if (!$_GET["dias"]) { $_GET["dias"]=30; }
// pillamos los valores 
$SELECT = "SELECT * from ranking WHERE ranking_usuario='".$_GET["usuario"]."' ";
$SELECT .= " AND ranking_fecha>CURDATE()- INTERVAL ".$_GET[dias]." DAY "; 
$SELECT .= " ORDER BY 'ranking_fecha' DESC ";


$result = $db->get_results($SELECT);

array_multisort($result, SORT_ASC, $result);
$row = 0;
while (isset($result[$row]->ranking_id)) {
	$saldo[$row]=$result[$row]->ranking_saldo;
	$invertido[$row]=$result[$row]->ranking_invertido;
	$total[$row]=$result[$row]->ranking_total;
	$fecha[$row]=$result[$row]->ranking_fecha;
	$beneficio[$row]=$result[$row]->ranking_beneficio_hoy;
	$row++;
}

?>
