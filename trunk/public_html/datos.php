<?
require("login.php");
include_once("config.php");

if (!$_GET["dias"]) { 
	 $SELECT = "SELECT * from quotes WHERE ticker='".$_GET["ticker"]."' ";
	 $SELECT .= " ORDER BY 'timestamp' DESC ";
	 $SELECT .= " LIMIT 0 , 60";
} else if ($_GET["dias"]>=30) {
	// Si pedimos 30 días o más
	$SELECT="SELECT * FROM quotes, (SELECT MAX(timestamp) as timestampmax FROM quotes WHERE ticker='".$_GET["ticker"]."' AND fecha>=CURDATE()- INTERVAL ".$_GET["dias"]." DAY GROUP BY fecha ORDER BY id ASC) maxtime  WHERE maxtime.timestampmax=quotes.timestamp AND ticker='".$_GET["ticker"]."' ";
	 
} else {
	// pillamos los valores 
	$SELECT = "SELECT * from quotes WHERE ticker='".$_GET["ticker"]."' ";
	$SELECT .= " AND fecha>CURDATE()- INTERVAL ".$_GET["dias"]." DAY ";
	$SELECT .= " ORDER BY 'timestamp' DESC ";
}

$result = $db->get_results($SELECT);
array_multisort($result, SORT_ASC, $result);
$row = 0;
while (isset($result[$row]->id)) {
	$valor[$row]=$result[$row]->valor;
	$volumen[$row]=$result[$row]->volumen;
	$fecha[$row]=$result[$row]->fecha;
	$hora[$row]=$result[$row]->hora;
	$rango_dia_bajo[$row]=$result[$row]->rango_dia_bajo;
	$cierre[$row]=$result[$row]->cierre;
	$apertura[$row]=$result[$row]->apertura;
	$row++;
}

//creamos mm3
$i=0;
foreach ($valor as $dia=> $f) {
	$mm3[$i]=mm3($valor, $dia);
	$i++;
}

//creamos mm10
$i=0;
foreach ($valor as $dia=> $f) {
	$mm10[$i]=mm10($valor, $dia);
	$i++;
}


function mm3($mes, $dia) {
	if (!$mes[$dia-2] OR !$mes[$dia-1]) {
		$mm3=$mes[$dia];
	} else {
		$mm3=($mes[$dia-2]+$mes[$dia-1]+$mes[$dia])/3;
	}
	return $mm3;
}

function mm10($mes, $dia) {
	if (!$mes[$dia-9] OR !$mes[$dia-8] OR !$mes[$dia-7] OR !$mes[$dia-6] OR !$mes[$dia-5] OR !$mes[$dia-4] OR!$mes[$dia-3] OR !$mes[$dia-2] OR !$mes[$dia-1]) {
		$mm10=$mes[$dia];
	} else {
		$mm10=($mes[$dia-9]+$mes[$dia-8]+$mes[$dia-7]+$mes[$dia-6]+$mes[$dia-5]+$mes[$dia-4]+$mes[$dia-3]+$mes[$dia-2]+$mes[$dia-1]+$mes[$dia])/10;
	}
	return $mm10;
}

?>
