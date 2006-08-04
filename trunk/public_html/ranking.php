<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

require("login.php");

cabecera();
menu_superior();

echo "<div id=\"central\">";

bloque_ads();


if (!$_GET["orden"]) { $orden="ranking_beneficio_hoy"; } else { $orden=$_GET["orden"]; }

// Ojo, el menos uno en CURDATE() es para ayer...
$SELECT="SELECT * from ranking WHERE ranking_fecha=CURDATE() ORDER by ".$orden." DESC LIMIT 0, 100"; 
$ranking=$db->get_results($SELECT);

echo "<div>Estos datos se actualizan cada vez que realizas una compra o venta</div>";

echo '<div class="listado-ranking-log">
		<div class="listado-ranking-usuario"><strong>Usuario</strong></div>
		<div class="listado-ranking-saldo"><strong><a href="ranking.php?orden=ranking_saldo">Saldo</a></strong></div>
		<div class="listado-ranking-invertido"><strong><a href="ranking.php?orden=ranking_invertido">Invertido</a></strong></div>
		<div class="listado-ranking-invertido"><strong><a href="ranking.php?orden=ranking_total">Total</a></strong></div>
		<div class="listado-ranking-beneficio-hoy"><strong><a href="ranking.php?orden=ranking_beneficio_hoy">Beneficio hoy</a></strong></div>
		</div>'."\n";

$row=0;
while (isset($ranking[$row]->ranking_id)) {
	
	echo '<div id="listado-'.$row.'" class="listado-ranking-log">';
	//Ponemos en negrita el usuario visitante para que se vea
	if ($ranking[$row]->ranking_usuario==$_SESSION["usuario"]) { echo "<b>"; }
	echo '<div class="listado-ranking-usuario"><a href="index.php?usuario='.$ranking[$row]->ranking_usuario.'">'.$ranking[$row]->ranking_usuario.'</a></div>';
	echo '<div class="listado-ranking-saldo">'.number_format($ranking[$row]->ranking_saldo, 2, ",", ".").' €</div>';
	echo '<div class="listado-ranking-invertido">'.number_format($ranking[$row]->ranking_invertido, 2, ",", ".").' €</div>';
	
	echo '<div class="listado-ranking-total">'.number_format($ranking[$row]->ranking_total, 2, ",", ".").' €</div>';
	echo '<div class="listado-ranking-beneficio-hoy">'.number_format($ranking[$row]->ranking_beneficio_hoy, 2, ",", ".").' €</div>';
	if ($ranking[$row]->ranking_usuario==$_SESSION["usuario"]) { echo "</b>"; }
	echo "</div>\n";
	$row++;
}
echo "</div>"; // end div central


// Mostramos el menú izquierda
echo '<div id="menu-izquierda">';
	if ($_SESSION["usuario_id"]!=0) {
		listado_cartera();
	} else {
		echo '<ul>Tu cartera
		<li>Sólo puedes ver tu cartera si eres usuario registrado</li>
		<li>Tu cartera muestra tus beneficios o pérdidas según tus compras y ventas</li>
		<li>Te recordamos que esto es un juego...</li>
		</ul>';
	}

	//	echo '<div id="visualizador">Selecciona una imagen para ver su gráfica</div>';

	caja_estadisticas();
echo "</div>";  // end div menu-izquierda





pie();
?>
