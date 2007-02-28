<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

require("login.php");

cabecera();
menu_superior();

echo "<div id=\"central\">";
bloque_ads();


echo "<ol><b>Tareas por hacer y sugerencias</b> (en ningún orden concreto)<br />Si quieres puedes <a href=\"http://sukiweb.net/archivos/2006/06/22/bolsaphp-juego-de-bolsa/#comment\">tu comentario o sugerencia</a>. Gracias.";
echo "<li><strike>Pequeñas anotaciones en cada valor de las carteras</strike></li>";
echo "<li><strike>Comprobar los cálculos y redondeos de euros</strike></li>";
echo "<li><strike>Implementar muchos detalles con más dinamismo</strike> Progresivo</li>";
echo "<li>Sistema para reportar bugs</li>";
echo "<li><strike>Añadir un <i>Enlaza este valor</i></strike></li>";
echo "<li>RSS de tu cartera</li>";
echo "<li>Alarmas (al mail) de valores</li>";
echo "<li><strike>Implementar cookies en el login para no tener que identificarse cada vez que entras</strike></li>";
echo "<li><strike>Añadir los nombres de empresas a los valores</strike></li>";
echo "<li><strike>Página del perfil del usuario, donde modificar algunas cosas</strike></li>";
echo "<li><strike>Gráficas pequeñas en el listado principal de valores, por petición del usuario (ajax)</strike></li>";
echo "<li><strike>Pequeña gráfica del valor más vendido en la caja de estadísticas</strike></li>";
echo "<li>Consultar por la licencia más apropiada y liberar las fuentes <i>a la GPL</i></li>";
echo "<li>Revisar el tema de seguridad</li>";
echo "<li>Cache de las imágenes de un tiempo prudente</li>";
echo "<li>Ordenación de valores por los distintos campos. (trukulo)</li>";
echo "<li>Cambios de puntos y percentiles. (trukulo)</li>";
echo "<li><strike>Botón comprar y vender en las acciones poseidas.</strike> (trukulo)</li>";
echo "<li>Ordenes de compra y venta cuando se alcancen máximos y mínimos, y con un
periodo de tiempo de caducidad de orden. (trukulo)</li>";
echo "<li>Gráfica del global de la bolsa con datos del ibex35 general. (trukulo)</li>";
echo "<li>Datos de otras bolsas, como el nimei, el nasdac... (trukulo)</li>";
echo "<li><strike>Costes de transacción de compras y ventas</strike> (vricci)</li>";
echo "<li>Consultar por acceso a datos en tiempo real para no tener delay en los datos de la bbdd</li>";
echo "<li>Al pulsar sobre login poner el cursor en usuario. (kynom)</li>";
echo "<li>Error al comprar acciones si el campo Nº acciones está vacío. (kynom)</li>";
echo "<li><strike>Ranking ordenado por quién gana más y con más información añadida</strike> (kynom)</li>";
echo "<li>Hacer los tickers de las carteras desplegables, para poner a gusto de usuario y que no se haga muy larga a simple vista (kynom)</li>";
echo "<li><strike>Mostrar en las estadísticas el más comprado por euros, no por cantidad de acciones</strike> (trukulo)</li>";
echo "<li>RSS de noticias del ticker que estés viendo en cuestión (kalvin)</li>";
echo "<li><strike>Refresco del fisgón cada minuto automático hasta implementarlo con ajax</strike></li>";
echo "<li><strike>Eliminar los eventos <i>login</i> y <i>logout</i> del fisgón</strike> (kynom)</li>";
echo "<li><strike>Dar tamaño al tag img en las gráficas grandes de los tikers</strike> (Xisco)</li>";
echo "<li>Botón para votar (mantener - comprar) o ( vender ) por los jugadores, para obtener una opinión tipo karma de los valores en tiempo real</li> (David)";
echo "<li><strike>Historico de tickers sobre compras y ventas en bolsaPGP</strike> (David)</li>";
echo "<li><strike>Optimizar el sistema de recogida de datos para una actualización más rápida</strike> (David)</li>";
echo "<li>Sistema de recomendación (mantener - comprar) o (vender) recomendad por bolsaPHP (David)</li>";
echo "<li><strike>Optimizar el sistema de ranking para actualización en tiempo real</strike> (David)</li>";
echo "</ol>";


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


	caja_estadisticas();
echo "</div>";  // end div menu-izquierda





pie();
?>
