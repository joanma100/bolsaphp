<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

require("login.php");

cabecera();
menu_superior();

echo "<div id=\"central\">";

bloque_ads();




if (!$_GET["grupo"]) { 
	//nada de momento
} else { 
	$grupo=$db->get_results("select * from grupos where grupo_nombre='".$_GET["grupo"]."'");
	if ($grupo[0]->grupo_id) {
		// nada de momento
	} else {
		echo "<div>No existe ningún grupo con ese nombre.</div>";
		$_GET["grupo"]="";
	}
}

if ($_GET["grupos"]==1) {
	ranking_todos_los_grupos();
} else if ($grupo[0]->grupo_id) {
	ranking_grupo($grupo[0]->grupo_id, $grupo[0]->grupo_nombre, $grupo[0]->grupo_admin);

} else {
	ranking_usuarios();
} 





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



	caja_estadisticas();
echo "</div>";  // end div menu-izquierda





pie();
?>
