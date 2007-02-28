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
	if ($_GET[log]!=1) { listado_log($_SESSION[usuario]); } else { fisgon(); }
} else if ($_GET["usuario"] AND $_GET["usuario"]!="anonimo") {
	//echo "usuario";
	datos_usuario($_GET["usuario"]);
} else if ($_GET["ordenes"]) {
	ordenes_en_cola();
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
		caja_usuario_no_registrado();
		caja_novedades();
		caja_karma(); 
	}


//	caja_estadisticas();
echo "<br /><br /><br />";
echo "</div>";  // end div menu-izquierda

pie();
?>

