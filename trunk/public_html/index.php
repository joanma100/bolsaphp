<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

require("login.php");

cabecera();

menu_superior();


echo "<div id=\"central\">";
if ($_GET["login"]=="login") {
	caja_login();
} else if ($_GET["login"]=="register") {
	// Formulario de registro.
	caja_registro();
} else {
	bloque_ads();
}



// Mostramos el cuerpo
if ($_GET[ticker]) {
	grafica_ticker($_GET[ticker]);
} else if ($_GET[log]) {
	if ($_GET[log]!=1) { listado_log($_SESSION[usuario]); } else { listado_log(); }
} else if ($_GET[usuario]) {
	//echo "usuario";
	datos_usuario($_GET["usuario"]);
} else {
	listado_quotes();
}
echo "<br /><br /><br />";
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
echo "<br /><br /><br />";
echo "</div>";  // end div menu-izquierda







/*
if ($_SESSION[usuario]) {
	echo "<div>";
	echo "<ul>";
	$SELECT= "SELECT * from carteras WHERE usuario='".$_SESSION[usuario]."' ";
	$result = $db->get_results($SELECT);
	$row=0;
	while (isset($result[$row]->id)) {
	echo "<li>".$result[$row]->ticker." - ".$result[$row]->valor." - ".$result[$row]->acciones."</li>";
	$row++;
	}
	echo "</ul>";
	echo "</div>";
}
*/

//echo '<script type="text/javascript" src="http://embed.technorati.com/embed/i8enwwkfm6.js"></script>';
pie();
?>

