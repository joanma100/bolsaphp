<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

if ( !function_exists('menu_superior') ) :
function menu_superior() {
	
	//echo "<div id=\"logo\"><a href=\"index.php\">BolsaPHP</a></div>";
	echo "<div id=\"logo\"><a href=\"index.php\"><img src=\"images/bolsaphp-logo.jpg\" border=\"0\"></a></div>";
	echo "<div id=\"menu-superior\">";
	echo "<ul>";
	echo "<li><a href=\"index.php\">".__("Página principal")."</a></li>";
	echo "<li><a href=\"index.php?log=1\">".__("Fisgón")."</a></li>";
	//echo "<li><a href=\"todo.php\">".__("Por hacer...")."</a></li>";
	echo "<li><a href=\"index.php?ordenes=1\">Órdenes</a></li>";
	echo "<li><a href=\"ranking.php\">".__("Ranking")."</a></li>";
	if (!$_SESSION["email"]) {
		echo "<li><a href=\"index.php?login=register\">".__("Registrarse")."</a></li>";
		echo "<li><a href=\"index.php?login=login\">".__("Login")."</a></li>";
	} else {
		echo "<li><a href=\"index.php?usuario=".$_SESSION["usuario"]."\">".$_SESSION["usuario"]."</a></li>";
		echo "<li><a href=\"index.php?login=logout\">".__("Salir")."</a></li>";
	}
	echo "</ul>";
	echo "</div>";
}
endif;


if ( !function_exists('caja_registro') ) :
function caja_registro() {
	global $mensaje_de_error;
	
	echo "<div id=\"login\">";
	if ($_POST["login_registro"] && $_POST["password_registro"] && $_POST["email_registro"]) {
		$mensaje_de_error=registra_usuario($_POST["login_registro"], $_POST["password_registro"], $_POST["email_registro"]);
		echo "<div>";
		if ($mensaje_de_error) { 
			echo "<b>".$mensaje_de_error."</b>"; 
		} else {
			echo "Registrado con éxito. Ya puedes hacer login con tu usuario.";
		}
		echo "</div>";
	} else {
		echo "<div class=\"login\">";
		if ($mensaje_de_error) { echo $mensaje_de_error; }
		echo "<form method=\"post\" action=\"index.php?login=register\">";
		echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">".__("Usuario")."</div>";
		echo "<div class=\"col2der\"><input type=\"text\" name=\"login_registro\" value=\"\" size=\"10\"></div>";
		echo "<div class=\"col2izq\">".__("Password")."</div>";
		echo "<div class=\"col2der\"><input type=\"password\" name=\"password_registro\" value=\"\" size=\"10\"></div>";
		echo "<div class=\"col2izq\">".__("E-mail")."</div>";
		echo "<div class=\"col2der\"><input type=\"text\" name=\"email_registro\" value=\"\" size=\"10\"></div>";
		echo "</div>"; // fin doscolumnas
		echo "<div><input type=\"Submit\" value=\"".__("Enviar")."\"></div>";
		echo "</form>";
		echo "</div>";
	}
	echo "</div>";
}
endif;



if ( !function_exists('registra_usuario') ) :
function registra_usuario($username, $password, $email) {
	global $db;
	
	if (user_exists($username)) { 
		$mensaje_de_error = "El usuario ".$username." ya existe"; 
	} else if (check_email($email)==0) {
		$mensaje_de_error = "El mail no es válido";
	} else if (email_exists($email)) {
 		$mensaje_de_error = "El mail ".$email." ya existe"; 
	} else {
		$SELECT="INSERT INTO usuarios ( usuario_login, usuario_password, usuario_email, usuario_nombre )";
		$SELECT.=" VALUES ( '".$username."', '".md5($password)."', '".$email."', '".$username."' )";
		$result = $db->get_results($SELECT);
		logea("registro ".$username, "", $_SESSION["usuario"]);
		
		//Creamos el ranking con un día atrás para que no obtenga beneficios de 60000 al actualizar el ranking hoy
		$SELECT ="INSERT INTO ranking ( ranking_usuario, ranking_saldo, ranking_invertido, ranking_total, ranking_beneficio_hoy, ranking_fecha ) ";
		$SELECT .= " VALUES ( '".$username."', '60000', '0', '60000', '0', CURDATE()-INTERVAL 1 DAY )";
		$result = $db->get_results($SELECT);
	}
	return $mensaje_de_error;
}
endif;

if ( !function_exists('caja_estadisticas') ) :
function caja_estadisticas() {
	global $db;
	$usuarios_registrados=$db->get_var("SELECT count(*) FROM usuarios ");
	$movimientos_dia=$db->get_var("SELECT count(*) FROM log WHERE log_fecha>=CURDATE()- INTERVAL 1 DAY AND log_tipo != 'CHAT' AND log_tipo != 'LOGIN' AND log_tipo != 'LOGOUT' ORDER BY 'log_fecha'");
	$usuarios_online=$db->get_var("SELECT count(distinct log_usuario_login) FROM log WHERE log_fecha>=NOW() - INTERVAL 15 MINUTE order by log_fecha DESC");
	$mas_invertido=$db->get_results("SELECT ticker,SUM(saldo) as suma_saldo FROM  carteras GROUP BY ticker ORDER BY suma_saldo desc LIMIT 1");
	$valores_carteras=$db->get_var("SELECT count(*) from carteras ");
	$grupos=$db->get_var("SELECT count(*) from grupos ");
	$usuario_ben30d=$db->get_results("SELECT `ranking_ben30d`, `ranking_usuario` FROM `ranking` where `ranking_fecha`=CURDATE() order by `ranking_ben30d` DESC LIMIT 1");
	echo "<div>";
	echo "<fieldset><legend>".__("Estadísticas")."</legend>";
	echo "<div class=\"doscolumnas\">";
	echo "<div class=\"col2izq\">".__("Usuarios").":</div>";
	echo "<div class=\"col2der\"> ".$usuarios_registrados."</div>";
	echo "<div class=\"col2izq\">Grupos:</div>";
	echo "<div class=\"col2der\"> ".$grupos."</div>";
	echo "<div class=\"col2izq\">".__("Usuarios online").":</div>";
	echo "<div class=\"col2der\"> ".$usuarios_online."</div>";
	echo "<div class=\"col2izq\">".__("En 24h").": </div>";
	echo "<div class=\"col2der\">".$movimientos_dia." movimientos</div>";
	echo "<div class=\"col2izq\">".__("Más invertido").":</div>";
	echo "<div class=\"col2der\"><a href=\"index.php?ticker=".$mas_invertido[0]->ticker."\">".$mas_invertido[0]->ticker."</a> -  ".number_format($mas_invertido[0]->suma_saldo, 2, ",", ".")." €</div>";
	echo "<div class=\"col2izq\">".__("En carteras").": </div>";
	echo "<div class=\"col2der\">".number_format($valores_carteras, 0, ",", ".")." Valores</div>";
	echo "<div class=\"col2izq\">".__("Mejor usuario").": </div>";
	echo "<div class=\"col2der\"><a href=\"index.php?usuario=".$usuario_ben30d[0]->ranking_usuario."\">".$usuario_ben30d[0]->ranking_usuario."</a> (".number_format($usuario_ben30d[0]->ranking_ben30d, 0, ",", ".")." €)</div>";
	echo "</div>"; // fin doscolumnas
	echo "</fieldset>";
	echo "</div>";
	
	
		echo "<div class=\"centrado\">";
		echo '<script type="text/javascript"><!--
		google_ad_client = "pub-6311366192077645";
		google_ad_width = 125;
		google_ad_height = 125;
		google_ad_format = "125x125_as_rimg";
		google_cpa_choice = "CAAQ7dvnzwEaCK3qHTlmuk6rKMO393M";
		google_ad_channel = "5143083987";
		//--></script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>';
		echo "</div>";
	
}
endif;

if ( !function_exists('caja_login') ) :
function caja_login() {
	global $mensaje_de_error;
	
	
	echo "<div id=\"login\">";
	if (!$_SESSION["email"]) {
		if ($mensaje_de_error) { echo "<ul><li>".$mensaje_de_error."</li></ul>"; }
		//echo "Acceso a usuarios registrados";
		echo "<div class=\"login\">";
		echo "<form method=\"post\" action=\"index.php?login=login\">";
		echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">".__("Usuario")."</div>";
		echo "<div class=\"col2der\"><input type=\"text\" name=\"login\" value=\"\" size=\"10\"></div>";
		echo "<div class=\"col2izq\">".__("Password")."</div>";
		echo "<div class=\"col2der\"><input type=\"password\" name=\"password\" value=\"\" size=\"10\"></div>";
		echo "</div>"; // fin doscolumnas
		echo "<div><input type=\"Submit\" value=\"".__("Enviar")."\"></div>";
		echo "</form>";
		echo "</div>";
		
	} 
	echo "</div>";
}
endif;

if ( !function_exists('ordena_compra') ) :
function ordena_compra($ticker, $acciones, $valor, $tipo_de_orden, $usuario) {
	global  $db, $config, $mensaje_de_error;

	if ($acciones<=0) { 
		$mensaje_de_error="No se pueden realizar ordenes de titulos negativos";
		return $mensaje_de_error;
	}
	if ($valor<=0) { 
		$mensaje_de_error="No se pueden realizar ordenes de valor negativo";
		return $mensaje_de_error;
	}
	$SELECT="INSERT INTO ordenes (usuario, ticker, acciones, tipo_orden, intencion, valor) values ('".$usuario."', '".$ticker."', '".$acciones."', '".$tipo_de_orden."', 'COMPRA', '".$valor."')";
	$result = $db->get_results($SELECT);
	$mensaje_de_error="Ordenada la compra de ".$acciones." títulos a ".$valor." € de ".$ticker;
	return $mensaje_de_error;
}
endif;

if ( !function_exists('ordena_venta') ) :
function ordena_venta($ticker, $acciones, $valor, $tipo_de_orden, $usuario) {
	global  $db, $config, $mensaje_de_error;
	$SELECT="INSERT INTO ordenes (usuario, ticker, acciones, tipo_orden, intencion, valor) values ('".$usuario."', '".$ticker."', '".$acciones."', '".$tipo_de_orden."', 'VENTA', '".$valor."')";
	$result = $db->get_results($SELECT);
	$mensaje_de_error="Ordenada la venta de ".$acciones." títulos a ".$valor." € de ".$ticker;
	return $mensaje_de_error;
}
endif;

if ( !function_exists('grafica_ticker') ) :
function grafica_ticker($ticker) {
	global  $db, $config, $mensaje_de_error;	
	
	if (!$_GET[dias]) { $_GET[dias]=5; }
	if (!$_GET["ticker"]) { $_GET["ticker"]=$ticker; }
	
	
	echo "<div id=\"grafico-ticker\">";
	echo "<strong>".empresa_ticker($_GET["ticker"])."</strong>";

	echo '<script type="text/javascript" src="grafica.js.php?ticker='.$_GET["ticker"].'"></script>';
	echo '</div>';
	
	
	
	$SELECT= "SELECT * from quotes WHERE ticker='".$_GET["ticker"]."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
	$result = $db->get_results($SELECT);

	echo "<div id=\"datos-ticker\">";
		echo "<fieldset><legend><b>".empresa_ticker($_GET["ticker"])."</b></legend>";
		echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">Valor:</div><div class=\"col2der\"> <b>".number_format($result[0]->valor, 2, ",", ".")." €</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Fecha:</div><div class=\"col2der\"> <b>".$result[0]->fecha."</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Hora:</div><div class=\"col2der\"> <b>".$result[0]->hora."</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Volumen:</div><div class=\"col2der\"> <b>".number_format($result[0]->volumen, 0, "", ".")."</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Cambio:</div><div class=\"col2der\"> <b>".$result[0]->cambio."</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Apertura:</div><div class=\"col2der\"> <b>".number_format($result[0]->apertura, 2, ",", ".")." €</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Cierre:</div><div class=\"col2der\"> <b>".number_format($result[0]->cierre, 2, ",", ".")." €</b>&nbsp;</div>";
		echo "<div class=\"col2izq\">Valor bajo:</div><div class=\"col2der\"> <b>".number_format($result[0]->rango_dia_bajo, 2, ",", ".")." €</b>&nbsp;</div>";
		echo "</div>"; // fin doscolumnas
		echo "</fieldset>";
	echo "</div>";
	
	

	echo "<div id=\"datos-ticker-cartera\">";
		if ($_POST["compraventa"]=="Comprar") {		
			clean_text($_POST[notas]);
			$mensaje_de_error=ordena_compra($_POST["ticker"], $_POST[acciones], $_POST["valor"], $_POST["tipo_de_orden"], $_SESSION["usuario"]);
		}
		if ($_POST["compraventa"]=="Vender") {
			clean_text($_POST[notas]);
			$SELECT="SELECT acciones, ticker, usuario FROM carteras WHERE usuario='".$_SESSION["usuario"]."' AND ticker='".$_POST["ticker"]."' ";
 			$acciones = $db->get_results($SELECT);
			
			if ($_POST[acciones]<=$acciones[0]->acciones) {	
				$mensaje_de_error=ordena_venta($_POST["ticker"], $_POST[acciones], $_POST["valor"], $_POST["tipo_de_orden"], $_SESSION["usuario"]);
			} else {
				$mensaje_de_error="<b>No tienes suficientes acciones para vender de ".$_POST["ticker"]."</b>";
			}
		}
		if ($_POST["guarda-notas"]) {
			clean_text($_POST[notas]);
			$SELECT = "UPDATE carteras SET notas='".$_POST["notas"]."' WHERE ticker='".$_GET["ticker"]."' AND usuario='".$_SESSION["usuario"]."' ";
			$notas = $db->get_results($SELECT);
		}


		//Sacamos los datos para la compra
		$SELECT="SELECT * FROM carteras WHERE ticker='".$_GET["ticker"]."' and usuario='".$_SESSION["usuario"]."'";
		$cartera = $db->get_results($SELECT);

		if ($mensaje_de_error) { echo $mensaje_de_error; }


	
		$SELECT="SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix FROM ordenes WHERE usuario='".$_SESSION["usuario"]."' AND ticker='".$_GET["ticker"]."' ORDER BY timestamp ASC";
		$ordenes=$db->get_results($SELECT);
		$row=0;
		if ($ordenes[$row]->id) {
			echo "<fieldset><legend>Ordenes solicitadas de <b>".empresa_ticker($_GET["ticker"])."</b> .</legend>";
			
			echo "<div class=\"cuatrocolumnas\">";
			
			while (isset($ordenes[$row]->id)) {
				$fecha=timestamp_to_fecha($ordenes[$row]->timestamp_unix);
				echo "<div class=\"col4izq\">".$ordenes[$row]->intencion."</a></div>";
				echo "<div class=\"col4cen1\">".$ordenes[$row]->acciones." / ".$ordenes[$row]->valor." €</div>";
				echo "<div class=\"col4cen2\">".$ordenes[$row]->tipo_orden."</a></div>";
				echo "<div class=\"col4der\">".$fecha."</div>";

				if ($ordenes[$row]->intencion=="COMPRA") { $ordenes_de_compra++; }
				if ($ordenes[$row]->intencion=="VENTA") { $ordenes_de_venta++; }
			$row++;
			}
			echo "</div>";
			echo "<div>Todas las órdenes se borran al finalizar el día.</div>";
			echo "</fieldset>";
		}
			

		
		echo "<fieldset><legend>Movimientos de <b>".empresa_ticker($_GET["ticker"])."</b> en tu cartera.</legend>";


		echo "<form name=\"ticker-cartera\" method=\"post\" action=\"?ticker=".$_GET["ticker"]."\">";
		echo "<input type=\"hidden\" name=\"ticker\" value=\"".$_GET["ticker"]."\">";
		echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">Acciones:</div>";
		echo "<div class=\"col2der\"><input type=\"text\" name=\"acciones\" value=\"";
		if ($cartera[0]->acciones<=0) { echo "100"; } else { echo $cartera[0]->acciones; }
		echo "\" size=\"10\" onKeyUp=\"calculaprecio(this.form, ".$config['comision'].")\"></div>";
		echo "<div class=\"col2izq\">Valor (€)</div>";
		echo "<div class=\"col2der\"><input type=\"text\" name=\"valor\" value=\"".$result[0]->valor."\" size=\"10\"  onKeyUp=\"calculaprecio(this.form, ".$config['comision'].")\"></div>";
		echo "<div class=\"col2izq\">Comisión (%)</div><div class=\"col2der\"><input type=\"text\" name=\"comision\" READONLY value=\"".$config['comision']."\" size=\"10\"></div>";
		echo "<div class=\"col2izq\">Total (€)</div>";
		echo "<div class=\"col2der\"><input type=\"text\" name=\"total\" size=\"10\" value=\"";
		if ($cartera[0]->acciones<=0) { echo ($result[0]->valor*100); } else { echo ($result[0]->valor*$cartera[0]->acciones); }
		echo "\" onKeyUp=\"calculaacciones(this.form, ".$config['comision'].")\"></div>";
		echo "<div class=\"col2izq\">Tipo de orden</div>";
		echo "<div class=\"col2der\">";
		echo "<select name=\"tipo_de_orden\">";
		echo "<option name=\"tipo_de_orden\" value=\"LIMITADA\">Limitada</option>";
		//echo "<option name=\"tipo_de_orden\" value=\"POR LO MEJOR\">Por lo mejor</option>";
		//echo "<option name=\"tipo_de_orden\" value=\"CAMBIO FIJO\">Cambio fijo</option>";
		//echo "<option name=\"tipo_de_orden\" value=\"A MERCADO\">A mercado</option>";
		echo "</select>";
		echo "</div>";


		if ($_SESSION["email"]) { // Mostramos botones sólo si son usuarios registrados
			echo "<div class=\"col2izq\">Operación</div>";
			echo "<div class=\"col2der\">";
			
			if ($ordenes_de_compra<=2) { // no más de 3 ordenes de compra por ticker
				echo "<input type=\"submit\" name=\"compraventa\" value=\"Comprar\"> ";
			}
			if ($cartera[0]->acciones>0) {
				if ($ordenes_de_venta<=2) { // no más de 3 ordenes de venta por ticker
					echo " <input type=\"submit\" name=\"compraventa\" value=\"Vender\">"; 
				}
			}
			echo "</div>"; 
		} else {
			echo "<p>Para poder ordenar compras y ventas debes ser usuario registrado.</p>";
		}

		echo "</div></form>";
		echo "</fieldset>";
		if ($cartera[0]->notas) {
			echo "<fieldset><legend>Pequeña anotación sobre este valor</legend>";
			echo "<form name=\"ticker-cartera\" method=\"post\" action=\"?ticker=".$_GET["ticker"]."\">";
			echo "<TEXTAREA ROWS=\"4\" COLS=\"25\" name=\"notas\">";
			echo $cartera[0]->notas; 
			echo "</TEXTAREA>";
			
			echo "<input type=\"submit\" name=\"guarda-notas\" value=\"Grabar\">";
			echo "</form>";
			echo "</fieldset>";		
		} 





		// Alertas
		echo "<fieldset><legend>Alertas para ".$_GET["ticker"]."</legend>";
	
		//Crea nueva alerta
		if ($_POST["guarda-alerta"] AND $_POST["valor"]>="0" AND $_SESSION["email"]) {
			if ($_POST["condicion"]=="Mayor que") {
				$condicion=">=";
			} else {
				$condicion="<=";
			}
			$SELECT="INSERT INTO alertas (usuario, ticker, condicion, valor, estado) values ('".$_SESSION["usuario"]."', '".$_GET["ticker"]."', '".$condicion."', '".$_POST["valor"]."', 'ACTIVA')";
			$res = $db->get_results($SELECT);
			//echo $SELECT;
			echo "<div><p>Añadida una nueva alerta para ".$_GET["ticker"]."</p></div>";
		}
	
		//Elimina una alerta
		if ($_POST["elimina-alerta"]) {
			$SELECT="DELETE from alertas WHERE id='".$_POST["id_alerta"]."'";
			$res = $db->get_results($SELECT);
			echo "<div><p>Eliminada la alerta para ".$_GET["ticker"]."</p></div>";
		}
	
		
		
		$SELECT="SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix FROM alertas WHERE usuario='".$_SESSION["usuario"]."' AND ticker='".$_GET["ticker"]."' order by timestamp desc";
		$alertas = $db->get_results($SELECT);
		$row=0;
		while (isset($alertas[$row]->id)) {
		echo "<form name=\"alertas\" method=\"post\" action=\"?ticker=".$_GET["ticker"]."\">";
		echo "<input type=\"hidden\" name=\"id_alerta\" value=\"".$alertas[$row]->id."\">";
		echo "<div class=\"cuatrocolumnas\">";
			echo "<div class=\"col4izq\">";
			if ($alertas[$row]->condicion==">=") { echo "Mayor que"; } else { echo "Menor que"; }
			echo "</div>";
			echo "<div class=\"col4cen1\">".$alertas[$row]->valor." €</div>";
			echo "<div class=\"col4cen2\">";
				if ($alertas[$row]->estado=="AVISADO") { 
					$fecha=timestamp_to_fecha($alertas[$row]->timestamp_unix);
					echo $fecha;
				} else {
					echo "&nbsp;";
				}
			echo "</div>";
			echo "<div class=\"col4der\">";
			echo "<input type=\"submit\" name=\"elimina-alerta\" value=\"Eliminar\">";
			echo "</div>";
		echo "</div>";
		echo "</form>";
		$row++;
		}
	
		if (!$_SESSION["email"]) {
			echo "<p>Para poder crear alertas debes ser un usuario registrado. Puedes <a href=\"index.php?login=register\">registrarte aquí</a>.</p>";
		} else {
			$alertas_email=$db->get_var("SELECT usuario_alertas_email from usuarios where usuario_login='".$_SESSION["usuario"]."'");
	
			if ($alertas_email==0) { 
				echo "<p>Activa las <strong>alertas por e-mail</strong> en <a href=\"http://bolsaphp.sukiweb.net/index.php?usuario=".$_SESSION["usuario"]."\">tu perfil</a> para recibirlas por correo electrónico.</p>";
			} else {
				echo "<p>Crea una nueva alerta</p>";
			}
		}
		echo "<div class=\"cuatrocolumnas\">";
		echo "<div class=\"col4izq\"><strong>Condición</strong></div>";
		echo "<div class=\"col4cen1\"><strong>€</strong></div>";
		echo "<div class=\"col4cen2\"><strong>%</strong></div>";
		echo "<div class=\"col4der\">&nbsp;</div>";
		echo "</div>";
	
		echo "<form name=\"alerta\" method=\"post\" action=\"?ticker=".$_GET["ticker"]."\">";
		echo "<div class=\"cuatrocolumnas\">";
		echo "<div class=\"col4izq\">";
			echo "<select name=\"condicion\">";
			echo "<option value=\"Mayor que\">Mayor que</option>";
			echo "<option value=\"Menor que\">Menor que</option>";
			echo "</select>";
		echo "</div>";
	
		echo "<div class=\"col4cen1\">";
			echo "<input type=\"text\" name=\"valor\" value=\"".$result[0]->valor."\" onKeyUp=\"calculaporcentaje(this.form, ".$result[0]->valor.")\">";
		echo "</div>";
		
		echo "<div class=\"col4cen2\">";
			echo "<input type=\"text\" name=\"tantoporciento\" value=\"0.00\" onKeyUp=\"calculavalor(this.form, ".$result[0]->valor.")\">";
		echo "</div>";
	
		echo "<div class=\"col4der\">";
		echo "<input type=\"submit\" name=\"guarda-alerta\" value=\"Crear\">";
		echo "</div>";
		echo "</form>";
		echo "</div>";
		echo "</fieldset>";


		echo "<fieldset><legend>Información al momento en tu RSS</legend>";
		echo "<div>";
		echo "Utiliza el RSS para estar atento a este valor. ";
		echo "<a href=\"rss.php?ticker=".$_GET["ticker"]."\"><img src=\"images/rss.jpg\" border=\"0\"></a>";
		echo "</div>";
		echo "</fieldset>";


	
	echo "</div>";

		echo "<div id=\"datos-ticker\">";
		echo "<fieldset><legend><b>Información externa sobre ".empresa_ticker($_GET["ticker"])."</b></legend>";

		
		echo "<div><a href=\"http://www.google.com/finance?q=".empresa_ticker($_GET["ticker"])."\" target=\"_blank\">Sobre ".empresa_ticker($_GET["ticker"])." en Google Finance</a></div>";
		echo "<div><a href=\"http://es.finance.yahoo.com/q?s=".$_GET["ticker"]."\" target=\"_blank\">Sobre ".empresa_ticker($_GET["ticker"])." en Yahoo Finanzas</a></div>";
		enlaces_ads();
		echo "</fieldset>";
		echo "</div>";
		
			
	
		echo "<div id=\"datos-ticker-externo\">";
		echo "<fieldset><legend><b>Añade ".empresa_ticker($_GET["ticker"])." en tu web</b></legend>";
		echo "<div>Para incluir una gráfica de este ticker en tu web, tan sólo copia y pega este código en ella.</div>";
		echo "<div><textarea><script type=\"text/javascript\" src=\"http://bolsaphp.sukiweb.net/bolsaphp.js.php?ticker=".$_GET["ticker"]."\"></script></textarea></div>";
		echo "</fieldset>";
		echo "</div>";
		

	// Historico del ticker que estamos viendo
		echo "<div id=\"datos-ticker-historico\">";
		echo "<fieldset><legend>Histórico de <b>".empresa_ticker($_GET["ticker"])."</b></legend>";
		listado_log("",$_GET["ticker"]);
		echo "</fieldset></div>";
	
	


}
endif;


if ( !function_exists('listado_quotes') ) :
function listado_quotes() {
	global $db;
	
	echo "<div class=\"filtro\">";
	if ($_POST["mercado"]=="IBEX35") { $_SESSION["filtro_mercado"]="IBEX35"; }
	if ($_POST["mercado"]=="Todos") { $_SESSION["filtro_mercado"]="Todos"; }
	if ($_POST["orden"]=="venta") { $_SESSION["filtro_orden"]="venta"; }
	if ($_POST["orden"]=="compra") { $_SESSION["filtro_orden"]="compra"; }
	if ($_POST["orden"]=="nombre") { $_SESSION["filtro_orden"]="nombre"; }
	
	echo "<form method=\"post\" action=\"?\">";
	echo "Mercado: ";
	echo "<select name=\"mercado\">";
	echo "<option value=\"IBEX35\"";
	if ($_SESSION["filtro_mercado"]=="IBEX35") { echo " SELECTED "; }
	echo ">Ibex 35</option>";
	echo "<option value=\"Todos\"";
	if ($_SESSION["filtro_mercado"]=="Todos") { echo " SELECTED "; }
	echo ">Todos</option>";
	echo "</select>";
	echo "&nbsp;&nbsp;";

	echo "Orden: ";
	echo "<select name=\"orden\">";
	echo "<option name=\"orden\" value=\"nombre\"";
	if ($_SESSION["filtro_orden"]=="nombre") { echo " SELECTED "; }
	echo ">Nombre</option>";
	echo "<option name=\"orden\" value=\"compra\"";
	if ($_SESSION["filtro_orden"]=="compra") { echo " SELECTED "; }
	echo ">Recomendado compra</option>";
	echo "<option name=\"orden\" value=\"venta\"";
	if ($_SESSION["filtro_orden"]=="venta") { echo " SELECTED "; }
	echo ">Recomendado venta</option>";
	echo "</select>";

	echo "&nbsp;&nbsp;";
	echo "<input type=\"submit\" name=\"filtrar\" value=\"Filtrar\">";
	echo "</form>";
	echo "</div>";
	
		include("inc/mini-img.js");
		echo '<div id="tipDiv" style="position:absolute; visibility:hidden; z-index:100"></div>';
	
	


	echo '<div class="listado-item">
		
		<div class="listado-ticker"><strong>Ticker</strong></div>
		<div class="listado-porcentaje-acciones"><strong>Cambio %</strong></div>
		<div class="listado-valor"><strong>Valor</strong></div>
		<div class="listado-volumen"><strong>Volumen</strong></div>
		<div class="listado-cambio"><strong>Cambio</strong></div>
		<div class="listado-fecha"><strong>Fecha</strong></div>
		</div>'."\n";
	$SELECT="SELECT SUM(saldo) as total_acciones FROM  carteras";
	$acciones_carteras_total = $db->get_results($SELECT);
	
	

	$SELECT="SELECT ticker FROM valores ";
	if ($_SESSION["filtro_mercado"]=="IBEX35") {
		$SELECT .=" WHERE mercado='IBEX35' ";
	}
	if ($_SESSION["filtro_orden"]=="venta") {
		$SELECT .=" order by b_venta desc";
	} else if ($_SESSION["filtro_orden"]=="compra") {
		$SELECT .=" order by b_compra desc";
	} else {
		$SELECT .=" order by nombre_empresa asc";
	}


	$valores = $db->get_results($SELECT);
	$row=0;
	//foreach ($quotes as $ticker) {
	while (isset($valores[$row]->ticker)) {
		$SELECT= "SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix from quotes WHERE ticker='".$valores[$row]->ticker."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
		$result = $db->get_results($SELECT);
		
		
		// Comprobamos para hacer la división
		if ($result[0]->valor>0 AND $result[0]->apertura>0) {
			$porcentaje=(($result[0]->valor*100)/$result[0]->apertura)-100;
		}


		echo "<script>listadoquotes('".$valores[$row]->ticker."', '0');</script>";
		echo '<div id="listado-'.$valores[$row]->ticker.'" class="listado-item">';
		include("cache/".$valores[$row]->ticker.".html");

		echo "</div>\n";
		$row++;
	}
	
}
endif;

if ( !function_exists('listado_cartera') ) :
function listado_cartera() {
	global $db, $config;
	echo "<div id=\"listado-cartera\">\r\n";

	// Caja novedades
	caja_novedades();
	
	$SELECT="SELECT usuario_saldo, usuario_id, usuario_email, usuario_grupo FROM usuarios WHERE usuario_id='".$_SESSION["usuario_id"]."'";
	$saldo = $db->get_results($SELECT);
	$SELECT="SELECT SUM(saldo) as saldototal FROM carteras WHERE usuario='".$_SESSION["usuario"]."' AND acciones>='1'";
	$invertido = $db->get_results($SELECT);

	
	echo "<div>";
	echo "<fieldset><legend>Perfil de <b><a href=\"index.php?usuario=".$_SESSION["usuario"]."\">".$_SESSION["usuario"]."</a></b></legend>";
	echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\"> <a href=\"index.php?log=".$_SESSION["usuario"]."\">Historial</a> </div>";
		echo "<div class=\"col2der\">&nbsp;";
		echo "<a href=\"index.php?usuario=".$_SESSION["usuario"]."\"><img src=\"".gravatar($_SESSION["usuario"], "50")."\" alt=\"".$usuario."\" title=\"".$usuario."\" align=\"right\" border=\"0\"></a>";
		echo "</div>";
		
	if ($saldo[0]->usuario_grupo) {
		echo "<div class=\"col2izq\">&nbsp;Grupo:</div>";
		$nombre_grupo=$db->get_var("SELECT grupo_nombre FROM grupos WHERE grupo_id='".$saldo[0]->usuario_grupo."'"); 
		echo "<div class=\"col2der\">";
		
		echo "<b>&nbsp;<a href=\"ranking.php?grupo=".$nombre_grupo."\">".$nombre_grupo."</a></b>";
		if ($_SESSION["usuario"]==$db->get_var("SELECT grupo_admin FROM grupos WHERE grupo_id='".$saldo[0]->usuario_grupo."'")) {
			$solicitudes=$db->get_var("SELECT count(*) from grupos_solicitud WHERE grupo_id='".$saldo[0]->usuario_grupo."'");
			if ($solicitudes>=1) { 
				echo "<br /><a href=\"index.php?usuario=".$_SESSION["usuario"]."\">".$solicitudes." pendientes</a>"; 
			}
		}
		echo " </div>";
	}	

		echo "<div class=\"col2izq\">&nbsp;Saldo:</div>";
		echo "<div class=\"col2der\">&nbsp;<b>".number_format($saldo[0]->usuario_saldo, 2, ",", ".")."</b> €</div>";
		echo "<div class=\"col2izq\">&nbsp;Invertido:</div><div class=\"col2der\"> <b>".number_format($invertido[0]->saldototal, 2, ",", ".")."</b> €</div>";
		$total=$saldo[0]->usuario_saldo+$invertido[0]->saldototal;
		echo "<div class=\"col2izq\">&nbsp;Total:</div><div class=\"col2der\">&nbsp; <b>".number_format($total, 2, ",", ".")."</b> €</div>";
	echo "</div>";
	echo "</fieldset>";
	echo "</div>";

	// Caja Karma
	caja_karma(); 


	echo "<a href=\"rss.php?usuario=".$_SESSION["usuario"]."\"><img src=\"images/rss.jpg\" border=\"0\"></a> Tu cartera: ";


		$SELECT="SELECT * FROM carteras WHERE usuario='".$_SESSION["usuario"]."' AND acciones>='1' ORDER BY ticker";
		$result = $db->get_results($SELECT);
		$row=0;
		while (isset($result[$row]->id)) {

			echo "<div id=\"cartera-".$result[$row]->ticker."\">";
			
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
			echo "</div>"; // Fin div cartera (ajax)
			$row++;
		}
	
	



	echo "</div>\r\n"; //end div listado-cartera
}
endif;

if ( !function_exists('compra_ticker') ) :
function compra_ticker($ticker, $acciones, $valor, $usuario) {
	global $db, $config;

	if (!is_numeric($acciones) OR !is_numeric($valor)) { 
		$mensaje_de_error="<b>Sólo valores numericos en estos campos.</b>";
		return $mensaje_de_error;
	 }

	if ($acciones<=0 OR $valor<=0) { 
		$mensaje_de_error="<b>Sólo valores positivos en estos campos.</b>";
		return $mensaje_de_error; 
	}

	$SELECT ="SELECT * FROM carteras WHERE ticker='".$ticker."' AND usuario='".$usuario."'";
	$result = $db->get_results($SELECT);
	
	if (!$result[0]->id) {	
		$saldo=$acciones*$valor;
		$notas = "Adquiridas ".$acciones." a ".$valor." € ";
		$SELECT = "INSERT INTO carteras ( ticker, saldo, acciones, usuario, notas) ";
		$SELECT .= "VALUES ( '".$ticker."', '".$saldo."', '".$acciones."', '".$usuario."', '".$notas."' )";
		$result = $db->get_results($SELECT);
		
		// y actualizamos el saldo del usuario
		
		//Calculamos el porcetaje de comisión
		$comision=($saldo*$config['comision'])/100;
		$saldo=$saldo+$comision;
		
		$SELECT = "UPDATE usuarios SET usuario_saldo=usuario_saldo-".$saldo." WHERE usuario_login='".$usuario."' ";
		$result = $db->get_results($SELECT);
		logea("Compra ".$acciones." de <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> a ".$valor." €", $ticker, $usuario);
		$mensaje_de_error="<b>Compradas ".$acciones." acciones a ".$valor." €</b>";
		actualiza_ranking($usuario);
		
		//Actualizamos el karma del valor
		$karma_usuario=$db->get_var("SELECT usuario_karma FROM usuarios WHERE usuario_login='".$usuario."'");
		$total_gastado=$acciones*$valor;
		$karma=($karma_usuario*$total_gastado)/1000;
		$SELECT = "UPDATE valores SET b_compra=b_compra+".$karma.", b_venta=b_venta-".$karma." WHERE ticker='".$ticker."' ";
		$result = $db->get_results($SELECT);


	} else {	
		$notas = "Adquiridas ".$acciones." a ".$valor." € ";
		$saldo=$acciones*$valor;
		$SELECT = "UPDATE carteras SET saldo=saldo+".$saldo.", acciones=acciones+".$acciones.", notas=CONCAT(notas,'\n','".$notas."')  WHERE ticker='".$ticker."' AND usuario='".$usuario."' ";
		$result = $db->get_results($SELECT);
		
		// y actualizamos el saldo del usuario
		
		//Calculamos el porcetaje de comisión
		$comision=($saldo*$config['comision'])/100;
		$saldo=$saldo+$comision;
		
		$SELECT = "UPDATE usuarios SET usuario_saldo=usuario_saldo-".$saldo." WHERE usuario_login='".$usuario."' ";
		$result = $db->get_results($SELECT);
		logea("Compra ".$acciones." de <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> a ".$valor." €", $ticker, $usuario);
		$mensaje_de_error="<b>Compradas ".$acciones." acciones a ".$valor." €</b>";
		actualiza_ranking($usuario);

		//Actualizamos el karma del valor
		$karma_usuario=$db->get_var("SELECT usuario_karma FROM usuarios WHERE usuario_login='".$usuario."'");
		$total_gastado=$acciones*$valor;
		$karma=($karma_usuario*$total_gastado)/1000;
		$SELECT = "UPDATE valores SET b_compra=b_compra+".$karma.", b_venta=b_venta-".$karma." WHERE ticker='".$ticker."' ";
		$result = $db->get_results($SELECT);

	}
	return $mensaje_de_error;
}
endif;

if ( !function_exists('vende_ticker') ) :
function vende_ticker($ticker, $acciones, $valor, $usuario) {
	global $db, $config;

	if (!is_numeric($acciones) OR !is_numeric($valor)) { 
		$mensaje_de_error="<b>Sólo números en estos campos.</b>";
		return $mensaje_de_error;
	 }

	if ($acciones<=0 OR $valor<=0) { 
		$mensaje_de_error="<b>Sólo valores positivos en estos campos.</b>";
		return $mensaje_de_error; 
	}

	$saldo=$acciones*$valor;
	
	$SELECT = "UPDATE carteras SET saldo=saldo-".$saldo.", acciones=acciones-".$acciones." WHERE ticker='".$ticker."' AND usuario='".$usuario."' ";
	$result = $db->get_results($SELECT);
		
	// y actualizamos el saldo del usuario
	//Calculamos el porcetaje de comisión
	$comision=($saldo*$config['comision'])/100;
	$saldo=$saldo-$comision;
	$SELECT = "UPDATE usuarios SET usuario_saldo=usuario_saldo+".$saldo." WHERE usuario_login='".$usuario."' ";
	$result = $db->get_results($SELECT);
	logea("Vende ".$acciones." de <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> a ".$valor." €", $ticker, $usuario);
	$mensaje_de_error="<b>Vendidas ".$acciones." acciones a ".$valor." €</b>";
	actualiza_ranking($usuario);

	//Actualizamos el karma del valor
	$karma_usuario=$db->get_var("SELECT usuario_karma FROM usuarios WHERE usuario_login='".$usuario."'");
	$total_gastado=$acciones*$valor;
	$karma=($karma_usuario*$total_gastado)/1000;
	$SELECT = "UPDATE valores SET b_compra=b_compra-".$karma.", b_venta=b_venta+".$karma." WHERE ticker='".$ticker."' ";
	$result = $db->get_results($SELECT);

	//Comprobamos si este ticker ya no tiene acciones, si es así, lo borramos.
	$SELECT="SELECT id, acciones FROM carteras WHERE ticker='".$ticker."' AND usuario='".$usuario."' AND acciones<=0";
	$result = $db->get_results($SELECT);
	if ($result[0]->id) {
		$SELECT="DELETE from carteras WHERE id=".$result[0]->id;
 		$result = $db->get_results($SELECT);
	}
	return $mensaje_de_error;
}
endif;

if ( !function_exists('listado_log') ) :
function listado_log($usuario = "", $ticker="") {
	global  $db;
	
	echo '<div class="listado-log-log">
		<div class="listado-log-usuario"><strong>Usuario</strong></div>
		<div class="listado-log-accion"><strong>Acción</strong></div>
		<div class="listado-log-fecha"><strong>Fecha</strong></div>
		</div>'."\n";
	
	
	$SELECT= "SELECT *, UNIX_TIMESTAMP(log_fecha) as timestamp_unix from log";
	if ($usuario) { $SELECT .=" WHERE log_usuario_login='".$usuario."' AND log_descripcion != 'login' AND log_descripcion != 'logout'"; 
	} else  if ($ticker) { $SELECT .=" WHERE log_descripcion LIKE '%".$ticker."%' ";
	} else { $SELECT .=" WHERE log_descripcion != 'login' AND log_descripcion != 'logout' "; }
	$SELECT .= " AND log_tipo != 'CHAT' ";
	$SELECT .=" ORDER BY 'log_fecha' DESC LIMIT 0 , 30 ";
	$result = $db->get_results($SELECT);
	$row=0;
	while (isset($result[$row]->log_id)) {
		
		echo '<div id="listado-'.$row.'" class="listado-log-log">';
		echo '<div class="listado-log-usuario">';
		echo '<a href="index.php?usuario='.$result[$row]->log_usuario_login.'">';
		
		echo '<img src="'.gravatar($result[$row]->log_usuario_login, "15").' alt="'.$result[$row]->log_usuario_login.'" align="left" border="0" > ';
		echo $result[$row]->log_usuario_login.'</a>';
		echo '</div>';
		echo '<div class="listado-log-accion">'.$result[$row]->log_descripcion.'</div>';
				
		$fecha=timestamp_to_fecha($result[$row]->timestamp_unix);
		
		echo '<div class="listado-log-fecha">'.$fecha.'</div>';
		echo "</div>\n";
		$row++;
	}
	
}
endif;

if ( !function_exists('fisgon') ) :
function fisgon($usuario = "", $ticker="") {
	global  $db;
	$usuarios_online=$db->get_var("SELECT count(distinct log_usuario_login) FROM log WHERE log_fecha>=NOW() - INTERVAL 15 MINUTE order by log_fecha DESC");

	echo "<div class=\"chat\">";
	echo "Inversores conectados: <b>".$usuarios_online."</b>";
	echo "<form id=\"chatForm\" name=\"chatForm\" onsubmit=\"return false;\" action=\"\">";
	echo "<input type=\"hidden\" name=\"name\" id=\"name\" />";

	echo "<input type=\"text\" size=\"30\" maxlength=\"500\" name=\"chatbarText\" id=\"chatbarText\" onblur=\"checkStatus('');\" onfocus=\"checkStatus('active');\" />";

	echo "<input onclick=\"sendComment();\" type=\"submit\" id=\"submit\" name=\"submit\" value=\"Chat\" />";
	echo "</form>";
	echo "</div>";
	
	echo '<div class="listado-log-log">
		<div class="listado-log-usuario"><strong>Usuario</strong></div>
		<div class="listado-log-accion"><strong>Acción</strong></div>
		<div class="listado-log-fecha"><strong>Fecha</strong></div>
		</div>'."\n";
	
	echo '<div id="outputList"><span class="name"></span></div>';
	
}
endif;


if ( !function_exists('bloque_ads') ) :
function bloque_ads() {
	echo "<div id=\"ads\">";
	
	echo "</div>";
}
endif;

if ( !function_exists('enlaces_ads') ) :
function enlaces_ads() {
	echo "<div><p>";
	
	echo "</p></div>";
	
}
endif;

if ( !function_exists('actualiza_ranking') ) :
function actualiza_ranking($usuario) {
	global  $db, $config;
	$SELECT="SELECT * FROM usuarios WHERE usuario_login='".$usuario."'";
	
	$result = $db->get_results($SELECT);
	$row=0;
	
	while (isset($result[$row]->usuario_id)) {
			$SELECT="SELECT SUM(saldo) as invertido FROM carteras WHERE usuario='".$result[$row]->usuario_login."' AND acciones>='1'";
	
			$invertido = $db->get_results($SELECT);
			$ranking_total=$result[$row]->usuario_saldo+$invertido[0]->invertido;
			
			$SELECT="SELECT ranking_total FROM ranking WHERE ranking_usuario='".$result[$row]->usuario_login."' AND ranking_fecha=CURDATE()-INTERVAL 1 DAY ";
			$total_ayer = $db->get_results($SELECT);
			if (!$total_ayer) { 
				$beneficio_hoy=0; 
			} else {
				$beneficio_hoy = $ranking_total-$total_ayer[0]->ranking_total;
			}
			//Comprobamos si hoy ya tiene ranking
			$SELECT="SELECT ranking_fecha, ranking_usuario FROM ranking WHERE ranking_fecha=CURDATE() AND ranking_usuario='".$usuario."'";
			$comp = $db->get_results($SELECT);
			if (empty($comp[0]->ranking_usuario)) {
				$SELECT="INSERT INTO ranking ( ranking_usuario, ranking_saldo, ranking_invertido, ranking_total, ranking_beneficio_hoy, ranking_ben30d, ranking_fecha ) ";
				$SELECT .= " VALUES ( '".$result[$row]->usuario_login."', '".$result[$row]->usuario_saldo."', '".$invertido[0]->invertido."', '".$ranking_total."', '".$beneficio_hoy."', '".beneficios($usuario, "30")."', CURDATE() )";
				$actualiza_ranking = $db->get_results($SELECT);
			} else { //Si ya existe, hacemos update.
				$SELECT="UPDATE ranking SET ranking_saldo='".$result[$row]->usuario_saldo."', ranking_invertido='".$invertido[0]->invertido."', ranking_total='".$ranking_total."', ranking_beneficio_hoy='".$beneficio_hoy."',
				ranking_ben30d='".beneficios($usuario, "30")."' WHERE ranking_usuario='".$usuario."' AND ranking_fecha=CURDATE()";
				$actualiza_ranking = $db->get_results($SELECT);
			}
		$row++;
	}
}
endif;

if ( !function_exists('datos_usuario') ) :
function datos_usuario($usuario) {
	global  $db, $config;	
	
	//Definimos lo que mostrar por defecto
	if (!$_GET["dias"]) { $_GET["dias"]=30; }	
	if (!$_GET["beneficio"]) { $_GET["beneficio"]=TRUE; }	

	echo "<div id=\"grafico-ticker\">";
	echo "<form name=\"form\" method=\"get\" action=\"index.php?usuario=".$usuario."\">";
	echo "<input type=\"hidden\" name=\"usuario\" value=\"".$usuario."\">";
	
	echo "<select name=\"dias\" OnChange=\"document.form.submit()\">";
	echo "<option name=\"dias\" value=\"7\" "; if ($_GET[dias]==7) { echo "selected"; } echo ">7 Días</option>";
	echo "<option name=\"dias\" value=\"15\" "; if ($_GET[dias]==15) { echo "selected"; } echo ">15 Días</option>";
	echo "<option name=\"dias\" value=\"30\" "; if ($_GET[dias]==30) { echo "selected"; } echo ">1 Mes</option>";
	echo "<option name=\"dias\" value=\"90\" "; if ($_GET[dias]==90) { echo "selected"; } echo ">3 Meses</option>";
	echo "<option name=\"dias\" value=\"180\" "; if ($_GET[dias]==180) { echo "selected"; } echo ">6 Meses</option>";
	echo "</select>";
	
	echo "[<INPUT name=\"total\" type=\"checkbox\"  "; if ($_GET[total]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> Total] ";
	echo "[<INPUT name=\"saldo\" type=\"checkbox\"  "; if ($_GET[saldo]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> Saldo] ";

	echo "[<INPUT name=\"invertido\" type=\"checkbox\"  "; if ($_GET[invertido]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> Invertido] ";
	echo "[<INPUT name=\"beneficio\" type=\"checkbox\"  "; if ($_GET[beneficio]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> Beneficio] ";
	
	echo "</form>";
		
	echo "<img src=\"chart_usuario.php?usuario=".$usuario."&dias=".$_GET["dias"]."&total=".$_GET["total"]."&saldo=".$_GET["saldo"]."&invertido=".$_GET["invertido"]."&beneficio=".$_GET["beneficio"]."\" border=\"0\">";
	echo "</div>";
	
	if ($_POST["cambiapassword"]) {
		if (!$_POST["password1"]) {
			$error="No puedes dejar un password en blanco";
		}
		if (!$_POST["password2"]) {
			$error="No puedes dejar un password en blanco";
		}
		if ($_POST["password1"]!=$_POST["password2"]) {
			$error="Debes teclear dos veces el mismo password";
		}

		if ($error) {
			echo $error;
		} else {
			if ($_POST["password1"]==$_POST["password2"]) {
				$SELECT = "UPDATE usuarios SET usuario_password='".md5($_POST["password1"])."' WHERE usuario_login='".$_SESSION["usuario"]."' ";
				$result = $db->get_results($SELECT);
				echo "Clave cambiada con éxito";
			}
		} 
	}

	if ($_POST["cambiadatos"]) {
		$SELECT = "UPDATE usuarios SET usuario_nombre='".$_POST["usuario_nombre"]."', usuario_url='".$_POST["usuario_url"]."', usuario_email='".$_POST["usuario_email"]."', usuario_alertas_email='".$_POST["usuario_alertas_email"]."' WHERE usuario_login='".$_SESSION["usuario"]."' ";
		$result = $db->get_results($SELECT);

		echo "Cambiados los datos con éxito";
	}

	if ($_POST["crear_nuevo_grupo"]) {
		if (grupo_exists($_POST["nombre_nuevo_grupo"])) { 
			echo "El grupo ya existe.";
		} else if (!$_POST["nombre_nuevo_grupo"]) {
			echo "Debes especificar un nombre de grupo.";
		} else {
			$SELECT = "INSERT into grupos (grupo_nombre, grupo_admin) VALUES ('".$_POST["nombre_nuevo_grupo"]."', '".$_SESSION["usuario"]."')";
			$result = $db->get_results($SELECT);
			
			$grupo_id = $db->get_var("SELECT grupo_id FROM grupos WHERE grupo_nombre='".$_POST["nombre_nuevo_grupo"]."'");
			$result = $db->get_results("UPDATE usuarios SET usuario_grupo='".$grupo_id."' WHERE usuario_login='".$_SESSION["usuario"]."' ");
			
			echo "Se ha creado el nuevo grupo.";
			 logea("Crea el grupo <a href=\"ranking.php?grupo=".$_POST["nombre_nuevo_grupo"]."\">".$_POST["nombre_nuevo_grupo"]."</a>", "GRUPO", $_SESSION["usuario"]);
		}
	}

	if ($_POST["solicitar_grupo"]) {
		$grupo_nombre = $db->get_var("SELECT grupo_nombre FROM grupos WHERE grupo_id='".$_POST["grupo_id"]."'");
		$SELECT = "INSERT into grupos_solicitud (usuario_login, grupo_nombre, grupo_id) VALUES ('".$_SESSION["usuario"]."', '".$grupo_nombre."', '".$_POST["grupo_id"]."')";
		$result = $db->get_results($SELECT);
		echo "Se ha enviado la solicitud de unión al grupo.";
	}
	
	if ($_POST["cancelar_solicitud"]) {
		$SELECT="DELETE from grupos_solicitud WHERE usuario_login='".$_SESSION["usuario"]."'";
		$result = $db->get_results($SELECT);
		echo "Se ha eliminado la solicitud de unión al grupo.";
	}
	if ($_POST["denegar_peticion"]) {
		$SELECT="DELETE from grupos_solicitud WHERE usuario_login='".$_POST["usuario_solicitud"]."' AND grupo_id='".$_POST["grupo_id"]."'";
		$result = $db->get_results($SELECT);
		echo "Se ha denegado la petición de unión al grupo del usuario ".$_POST["usuario_solicitud"].".";
	}

	if ($_POST["aceptar_peticion"]) {
		$result = $db->get_results("UPDATE usuarios SET usuario_grupo='".$_POST["grupo_id"]."' WHERE usuario_login='".$_POST["usuario_solicitud"]."' ");

		$SELECT="DELETE from grupos_solicitud WHERE usuario_login='".$_POST["usuario_solicitud"]."' AND grupo_id='".$_POST["grupo_id"]."'";
		$result = $db->get_results($SELECT);
		echo "Se ha aceptado la petición de unión al grupo del usuario ".$_POST["usuario_solicitud"].".";
	}

	if ($_POST["expulsar_usuario"]) {
		$result = $db->get_results("UPDATE usuarios SET usuario_grupo=NULL WHERE usuario_login='".$_POST["expulsar_usuario_login"]."' ");
	}
	
	if ($_POST["eliminar_grupo"]) {
		$result = $db->get_results("UPDATE usuarios SET usuario_grupo=NULL WHERE usuario_login='".$_SESSION["usuario"]."' ");
		$result = $db->get_results("DELETE from grupos WHERE grupo_id='".$_POST["grupo_id"]."' ");
		echo "Eliminado el grupo.";

	}


	$SELECT="SELECT usuario_login, usuario_fecha, usuario_email, usuario_nombre, usuario_url, usuario_saldo, usuario_alertas_email, usuario_grupo from usuarios WHERE usuario_login='".$usuario."'";
	$result = $db->get_results($SELECT);
	
	$grav_url = gravatar($result[0]->usuario_login, 50);	

	echo "<div id=\"datos-ticker\">";
		echo "<fieldset><legend><b>Datos de ".$usuario."</b></legend> ";
		echo "<div class=\"doscolumnas\">";
		if ($usuario==$_SESSION["usuario"]) {	
			echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
			echo "<div class=\"col2izq\"><a href=\"http://www.gravatar.com/signup.php\">Foto de Gravatar.com</a></div>";
			echo "<div class=\"col2der\"><img src=\"".$grav_url."\" alt=\"".$usuario."\" align=\"right\">&nbsp;</div>";
			echo "<div class=\"col2izq\"> Nombre:</div>";
			echo "<div class=\"col2der\"><input type=\"text\" name=\"usuario_nombre\" value=\"".$result[0]->usuario_nombre."\"></div>";
			echo "<div class=\"col2izq\"> Web:</div>";
			echo "<div class=\"col2der\"><input type=\"text\" name=\"usuario_url\" value=\"".$result[0]->usuario_url."\"></div>";
			echo "<div class=\"col2izq\"> E-mail: </div>"; 
			echo "<div class=\"col2der\"><input type=\"text\" name=\"usuario_email\" value=\"".$result[0]->usuario_email."\"></div>";
			
			echo "<div class=\"col2izq\"> Alertas por E-mail: </div>";
			echo "<div class=\"col2der\">";
			echo "<select name=\"usuario_alertas_email\">";
			echo "<option value=\"0\"";
			if ($result[0]->usuario_alertas_email==0) { echo " SELECTED "; } 
			echo ">No recibir correos</option>";
			echo "<option value=\"1\" ";
			if ($result[0]->usuario_alertas_email==1) { echo " SELECTED "; }
			echo ">Recibir alertas por correo</option>";
			echo "</select>";
			echo "</div>";

			echo "<div class=\"col2izq\"> &nbsp; </div>";
			echo "<div class=\"col2der\"><input type=\"submit\" name=\"cambiadatos\" value=\"Guardar\"></div>";
			echo "</form>";
			echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
			echo "<div class=\"col2izq\">Nuevo Password <br />(teclea 2 veces el mismo)</div>";
			echo "<div class=\"col2der\"><input type=\"password\" name=\"password1\" value=\"\"></div>";
			echo "<div class=\"col2der\"><input type=\"password\" name=\"password2\" value=\"\"></div>";
			echo "<div class=\"col2izq\"> &nbsp; </div>";
			echo "<div class=\"col2der\"><input type=\"submit\" name=\"cambiapassword\" value=\"Cambiar Password\"></div>";
			echo "</form>";
		} else {
			echo "<div class=\"col2izq\"> &nbsp; </div>";
			echo "<div class=\"col2der\"><img src=\"".$grav_url."\" alt=\"".$usuario."\" align=\"right\">&nbsp;</div>";
			echo "<div class=\"col2izq\"> Nombre: </div>";
			echo " <div class=\"col2der\"><b>".$result[0]->usuario_nombre."</b>&nbsp;</div>";
			echo "<div class=\"col2izq\"> Web: </div>";
			echo " <div class=\"col2der\">&nbsp;<b>".text_to_html($result[0]->usuario_url)."</b>&nbsp;</div>";

			if ($result[0]->usuario_grupo) {
				$nombre_grupo=$db->get_var("SELECT grupo_nombre FROM grupos WHERE grupo_id='".$result[0]->usuario_grupo."'");

				echo "<div class=\"col2izq\"> Grupo: </div>";
				echo " <div class=\"col2der\">&nbsp;<a href=\"ranking.php?grupo=".$nombre_grupo."\">".$nombre_grupo."</a>&nbsp;</div>";
			}
		}
		echo "<div class=\"col2izq\"> Saldo actual: </div>";
		echo "<div class=\"col2der\"><b>".number_format($result[0]->usuario_saldo, 0, "", ".")." €</b>&nbsp;</div>";

		echo "<div class=\"col2izq\"> Beneficio últimos ".$_GET[dias]." días: </div>";
		echo "<div class=\"col2der\"><b>".beneficios($usuario, $_GET[dias])." € </b></div>";
		echo "</div>"; //cierra doscolumnas
		echo "</fieldset>";

	if ($usuario==$_SESSION["usuario"]) {
		echo "<fieldset><legend><b>Resetear la cuenta</b></legend> ";
		echo "<div>";
		if ($_POST["reseteacuenta"]) {
			resetea_usuario($_SESSION["usuario"]);
			echo "<p>Tu cuenta ha sido reseteada.</p>";

		} else {
			echo "<p><strong>ATENCIÓN:</strong></p>";
			echo "<p>Al resetear la cuenta perderás todos los valores de tu cartera y tu saldo volverá al punto de partida (60.000 €).</p>";
			echo "<p>Resetea tu cuenta si estás totalmente seguro de que quieres perder todas tus inversiones y tu beneficio acumulado.</p>";
			
			echo "<form method=\"post\" action=\"?usuario=".$usuario."\" onsubmit='return confirm(\"¿Estas seguro de que deseas resetear tu cuenta?\")'>";
			echo "<p><input type=\"submit\" name=\"reseteacuenta\" value=\"Resetear mi cuenta\"></p>";
			echo "</form>";
		}	
		echo "</div>";
		echo "</fieldset>";
	}
	
	echo "</div>";	

	


	echo "<div id=\"datos-ticker-cartera\">";
		if ($usuario==$_SESSION["usuario"]) {
		echo "<fieldset><legend><b>Grupo</b></legend>";
		if (!$result[0]->usuario_grupo) {
			$solicitudes=$db->get_results("SELECT * from grupos_solicitud WHERE usuario_login='".$_SESSION["usuario"]."'");
			if ($solicitudes) {
				echo "<div>Tienes una solicitud de unión enviada al grupo <strong>".$solicitudes[0]->grupo_nombre."</strong>. Entrarás a formar parte del grupo cuando su administrador acepte tu solicitud.</div>";

				echo "<div>Si lo deseas, puedes cancelar esa solicitud para poder unirte a otro grupo o crear el tuyo propio.</div>";
				echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
				echo "<input type=\"submit\" name=\"cancelar_solicitud\" value=\"Cancelar Solicitud\">";
				echo "</form>";
				
			} else {
				echo "<div>Todavía no perteneces a ningún grupo. <br />Puedes unirte a uno existente.";
				echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
				echo "<div class=\"col2izq\">Nombre del grupo:</div>";
				echo "<div class=\"col2der\"><select name=\"grupo_id\">";
				$grupos=$db->get_results("SELECT grupo_nombre, grupo_id FROM grupos");
				$row=0;
				while (isset($grupos[$row]->grupo_id)) {
					echo "<option name=\"grupo_id\" value=\"".$grupos[$row]->grupo_id."\">".$grupos[$row]->grupo_nombre."</option>";
				$row++;
				}
				
				echo "</select>";
				echo "</div>";
				echo "<div class=\"col2izq\">&nbsp;</div>";
				echo "<div class=\"col2der\"><input type=\"submit\" name=\"solicitar_grupo\" value=\"Enviar solicitud\"></div>";
				echo "</form>";
				
		
				echo "También puedes crear un nuevo grupo.";
		
				echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
				echo "<div class=\"col2izq\">Nombre del grupo:</div>";
				echo "<div class=\"col2der\"><input type=\"text\" name=\"nombre_nuevo_grupo\"></div>";
				echo "<div class=\"col2izq\">&nbsp;</div>";
				echo "<div class=\"col2der\"><input type=\"submit\" name=\"crear_nuevo_grupo\" value=\"Crear grupo\"></div>";
				echo "</form>";
				echo "</div>";
				}	
			
		} else {
			$nombre_grupo=$db->get_var("SELECT grupo_nombre FROM grupos WHERE grupo_id='".$result[0]->usuario_grupo."'");
			if ($_SESSION["usuario"]==$db->get_var("SELECT grupo_admin FROM grupos WHERE grupo_id='".$result[0]->usuario_grupo."'")) { 
				echo "<div><strong>Administrador del grupo <a href=\"ranking.php?grupo=".$nombre_grupo."\">".$nombre_grupo."</a></strong></div>";

				$SELECT="SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix FROM grupos_solicitud WHERE grupo_id='".$result[0]->usuario_grupo."' order by timestamp desc";
				$solicitudes=$db->get_results($SELECT);
				if ($solicitudes[0]->usuario_login) {
					echo "<div>Solicitudes pendientes de aprobación.</div>";
				}
				$row=0;
				while (isset($solicitudes[$row]->usuario_login)) {
					$fecha=timestamp_to_fecha($solicitudes[$row]->timestamp_unix);
					echo "<div class=\"cuatrocolumnas\">";
					echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
					echo "<input type=\"hidden\" name=\"usuario_solicitud\" value=\"".$solicitudes[$row]->usuario_login."\">";
					echo "<input type=\"hidden\" name=\"grupo_id\" value=\"".$result[0]->usuario_grupo."\">";
					echo "<div class=\"col4izq\"><a href=\"index.php=?usuario=".$solicitudes[$row]->usuario_login."\">".$solicitudes[$row]->usuario_login."</a></div>";
					echo "<div class=\"col4cen1\">".$fecha."</div>";
					echo "<div class=\"col4cen2\"><input type=\"submit\" name=\"denegar_peticion\" value=\"Denegar\"></div>";
					echo "<div class=\"col4der\"><input type=\"submit\" name=\"aceptar_peticion\" value=\"Aceptar\"></div>";
					echo "</form>";
					echo "</div>";
				$row++;
				}
				echo "<br />";
				echo "<div>Usuarios que pertenecen actualmente al grupo.</div>";
				$SELECT="SELECT usuario_login from usuarios WHERE usuario_grupo='".$result[0]->usuario_grupo."' order by usuario_login ASC";
				$usuarios_grupo=$db->get_results($SELECT);
				$row=0;
				while (isset($usuarios_grupo[$row]->usuario_login)) {
					echo "<div class=\"doscolumnas\">";
					echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
					echo "<input type=\"hidden\" name=\"expulsar_usuario_login\" value=\"".$usuarios_grupo[$row]->usuario_login."\">";
					echo "<div class=\"col2izq\"><a href=\"index.php?usuario=".$usuarios_grupo[$row]->usuario_login."\">".$usuarios_grupo[$row]->usuario_login."</a></div>";
					echo "<div class=\"col2der\">";
					if ($usuarios_grupo[$row]->usuario_login!=$_SESSION["usuario"]) {
						echo "<input type=\"submit\" name=\"expulsar_usuario\" value=\"Expulsar\">";
					} else {
						echo "<strong>Administrador</strong>";
					}
					echo "</div>";
					echo "</form>";
					echo "</div>";
				$row++;
				}	
				if ($row==1) { //Sólo está el admin en el grupo
					echo "<br />";
					echo "<div>Este grupo sólo tiene un miembro. Cuando contenga más de un miembro, no podrá eliminarse el grupo.</div>";
					echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
					echo "<input type=\"hidden\" name=\"grupo_id\" value=\"".$result[0]->usuario_grupo."\">";
					echo "<input type=\"submit\" name=\"eliminar_grupo\" value=\"Eliminar Grupo\">";
					echo "</form>";
				}


			} else {
				echo "<div>Ya perteneces a un grupo. Si lo deseas, puedes dejar de pertenecer al grupo ahora.";
				echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
				echo "<input type=\"submit\" name=\"salir_del_grupo\" value=\"Salir del grupo\">";
				echo "</form>";
				echo "</div>";
			}
		}
		echo "</fieldset>";
	}

	$SELECT="SELECT * from carteras WHERE usuario='".$usuario."'";
	$result = $db->get_results($SELECT);

	echo "<fieldset><legend>La cartera de <b>".$usuario."</b></legend>";
	echo "<div class=\"doscolumnas\">";
	echo "<div class=\"col2izq\">RSS de esta cartera:</div>";
	echo "<div class=\"col2der\"><a href=\"rss.php?usuario=".$_GET["usuario"]."\"><img src=\"images/rss.jpg\" border=\"0\"></a></div>";

	$row=0;
	while (isset($result[$row]->id)) {
		echo "<div class=\"col2izq\"><a href=\"index.php?ticker=".$result[$row]->ticker."\">".empresa_ticker($result[$row]->ticker)." </a></div>";
		echo "<div class=\"col2der\"> ".$result[$row]->acciones." Acciones</div>";
	$row++;
	}
	echo "</div>"; // fin doscolumnas
	echo "</fieldset>";


	echo "<fieldset><legend>Esta cartera en tu web</legend>";
	echo "<div>";
	echo "Para poder ver esta cartera en tu web, copia y pega este código en ella.";
	echo "<textarea rows=\"10\">";
	echo '<script type="text/javascript"><!--
	bolsaphp_usuario = "'.$usuario.'";
	bolsaphp_width = "150";
	bolsaphp_height = "400";
	bolsaphp_frameborder = "0";
	bolsaphp_scrolling = "no";
	//--></script>

	<script type="text/javascript" src="http://bolsaphp.sukiweb.net/cartera.js.php"></script>';
	echo "</textarea>";
	echo "</div>";
	echo "</fieldset>";
	

	echo "</div>";

	

	// Historico del usuario que estamos viendo
	echo "<div id=\"datos-ticker-historico\">";
	echo "<ul>Histórico de <b>".$usuario."</b></ul>";
	listado_log($usuario);
	echo "</div>";

}
endif;


if ( !function_exists('caja_karma') ) :
function caja_karma() {
	global  $db, $config;
	// Caja Karma 
	$SELECT ="SELECT * FROM `valores` WHERE b_compra>100 ORDER BY `b_compra` DESC LIMIT 5 ";
	$result = $db->get_results($SELECT);
	$row=0;
	if ($result[0]->id) {
		echo "<fieldset class=\"sube\">";
		echo "<legend>Recomendado: Compra</legend>";
	echo "<div class=\"doscolumnas\">";	
		echo "<div class=\"col2izq\">Valor</div>";
		echo "<div class=\"col2der\">Karma</div>";
		while (isset($result[$row]->id)) {
			echo "<div class=\"col2izq\"><a href=\"index.php?ticker=".$result[$row]->ticker."\">".$result[$row]->ticker."</a></div>";
			echo "<div class=\"col2der\">".$result[$row]->b_compra."</div>";
		$row++;
		}
	echo "</div>";
		echo "</fieldset>";
	}

	$SELECT ="SELECT * FROM `valores`  WHERE b_venta>100 ORDER BY `b_venta` DESC LIMIT 5 ";
	$result = $db->get_results($SELECT);
	$row=0;
	if ($result[0]->id) {
		echo "<fieldset class=\"baja\">";
		echo "<legend>Recomendado: Venta</legend>";
	echo "<div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">Valor</div>";
		echo "<div class=\"col2der\">Karma</div>";	
		while (isset($result[$row]->id)) {
			echo "<div class=\"col2izq\"><a href=\"index.php?ticker=".$result[$row]->ticker."\">".$result[$row]->ticker."</a></div>";
			echo "<div class=\"col2der\">".$result[$row]->b_venta."</div>";
		$row++;
		}
	echo "</div>";
		echo "</fieldset>";
	}	
}
endif;

if ( !function_exists('caja_usuario_no_registrado') ) :
function caja_usuario_no_registrado() {
	echo '<fieldset><legend>Tu cartera</legend>
	<p><a href="http://sukiweb.net/archivos/2006/06/22/bolsaphp-juego-de-bolsa/">¿Que es BolsaPHP?</a></p>
	<p><a href="index.php?login=register">Regístrate</a>, obtendrás 60.000 euros para invertir en BolsaPHP y consigue ser uno de los mejores en el <a href="ranking.php">Ranking</a></p>
	</fieldset>';
}
endif;

if ( !function_exists('ranking_usuarios') ) :
function ranking_usuarios() {
	global $db, $config;

	echo "<div>[ <strong> Ranking de usuarios registrados</strong> ] [ <a href=\"ranking.php?grupos=1\">Ranking de grupos</a> ]</div>";
	if (!$_GET["orden"]) { $orden="ranking_beneficio_hoy"; } else { $orden=$_GET["orden"]; }

	// Ojo, el menos uno en CURDATE() es para ayer...
	$SELECT="SELECT * from ranking WHERE ranking_fecha=CURDATE() ";
	$SELECT .=" ORDER by ".$orden." DESC LIMIT 0, 100"; 
	
	$ranking=$db->get_results($SELECT);
	
	echo '<div class="listado-ranking-log">
			<div class="listado-ranking-top"><strong>Top</strong></div>
			<div class="listado-ranking-usuario"><strong>Usuario</strong></div>
			<div class="listado-ranking-saldo"><strong><a href="ranking.php?orden=ranking_saldo">Saldo</a></strong></div>
			<div class="listado-ranking-invertido"><strong><a href="ranking.php?orden=ranking_invertido">Invertido</a></strong></div>
			<div class="listado-ranking-ben30d"><strong><a href="ranking.php?orden=ranking_ben30d">Ben. 30D</a></strong></div>
			<div class="listado-ranking-beneficio-hoy"><strong><a href="ranking.php?orden=ranking_beneficio_hoy">Ben. hoy</a></strong></div>
			</div>'."\n";
	
	$row=0;
	while (isset($ranking[$row]->ranking_id)) {
		
		echo '<div id="listado-'.$row.'" class="listado-ranking-log">';
		echo '<div id="listado-'.$row.'" class="listado-ranking-top">'.($row+1).'</div>';
		//Ponemos en negrita el usuario visitante para que se vea
		if ($ranking[$row]->ranking_usuario==$_SESSION["usuario"]) { echo "<b>"; }
		echo '<div class="listado-ranking-usuario"><a href="index.php?usuario='.$ranking[$row]->ranking_usuario.'">'.$ranking[$row]->ranking_usuario.'</a></div>';
		echo '<div class="listado-ranking-saldo">'.number_format($ranking[$row]->ranking_saldo, 2, ",", ".").' €</div>';
		echo '<div class="listado-ranking-invertido">'.number_format($ranking[$row]->ranking_invertido, 2, ",", ".").' €</div>';
		
		echo '<div class="listado-ranking-ben30d">'.number_format($ranking[$row]->ranking_ben30d, 2, ",", ".").' €</div>';
		echo '<div class="listado-ranking-beneficio-hoy">'.number_format($ranking[$row]->ranking_beneficio_hoy, 2, ",", ".").' €</div>';
		if ($ranking[$row]->ranking_usuario==$_SESSION["usuario"]) { echo "</b>"; }
		echo "</div>\n";
		$row++;
	}
}
endif;


if ( !function_exists('ranking_todos_los_grupos') ) :
function ranking_todos_los_grupos() {
	global $db, $config;
	echo "<div>[ <a href=\"ranking.php\">Ranking de usuarios registrados</a> ] [ <strong>Ranking de grupos</strong> ]</div>";

	$SELECT="SELECT count(DISTINCT `usuario_login`) as cantidad_usuarios, `usuario_grupo` from usuarios  group by usuario_grupo order by cantidad_usuarios DESC";
	$result=$db->get_results($SELECT);
	$row=0;
	echo "<div class=\"cuatrocolumnas\">";
	echo "<div class=\"col4izq\"><strong>Nombre del grupo</strong></div>";
	echo "<div class=\"col4cen1\"><strong>Usuarios</strong></div>";
	echo "<div class=\"col4cen2\"><strong>Administrador</strong></div>";
	echo "<div class=\"col4der\">&nbsp;</div>";
	echo "</div>";

	while (isset($result[$row]->cantidad_usuarios)) {
		$grupo_nombre=$db->get_var("SELECT grupo_nombre from grupos where grupo_id='".$result[$row]->usuario_grupo."'");
		$grupo_admin=$db->get_var("SELECT grupo_admin from grupos where grupo_id='".$result[$row]->usuario_grupo."'");
		if ($grupo_nombre) {
			echo "<div class=\"cuatrocolumnas\">";
			echo "<div class=\"col4izq\"><a href=\"ranking.php?grupo=".$grupo_nombre."\">".$grupo_nombre."</a></div>";
			echo "<div class=\"col4cen1\">".$result[$row]->cantidad_usuarios."</div>";
			echo "<div class=\"col4cen2\"><a href=\"index.php?usuario=".$grupo_admin."\">".$grupo_admin."</a></div>";
			echo "<div class=\"col4der\">&nbsp;</div>";
			echo "</div>";
		}
	$row++;
	}
}
endif;


if ( !function_exists('ranking_grupo') ) :
function ranking_grupo($grupo_id, $grupo_nombre, $grupo_admin) {
	global $db, $config;
	echo "<div>[ <a href=\"ranking.php\">Ranking de usuarios registrados</a> ] [ <a href=\"ranking.php?grupos=1\">Ranking de grupos</a> ]</div>";

	$grupo=$db->get_results("select * from grupos where grupo_nombre='".$_GET["grupo"]."'");
	echo "<div>Ranking del grupo de usuarios <strong><a href=\"?grupo=".$grupo[0]->grupo_nombre."\">".$grupo[0]->grupo_nombre."</a></strong>.</div>";
	
	if (!$_GET["orden_grupo"]) { $orden_grupo="usuario_karma"; } else { $orden_grupo=$_GET["orden_grupo"]; }
	
	$SELECT ="SELECT usuario_login, usuario_karma, usuario_nombre, usuario_url, usuario_saldo, usuario_grupo FROM usuarios WHERE usuario_grupo='".$grupo_id."' ";
	$SELECT .=" ORDER by ".$orden_grupo." DESC "; 
	
	$ranking=$db->get_results($SELECT);
	
	echo '<div class="listado-ranking-log">
			<div class="listado-ranking-top"><strong>Top</strong></div>
			<div class="listado-ranking-usuario"><strong>Usuario</strong></div>
			<div class="listado-ranking-saldo"><strong><a href="ranking.php?grupo='.$grupo_nombre.'&orden_grupo=usuario_saldo">Saldo</a></strong></div>
			<div class="listado-ranking-invertido"><strong>Invertido</strong></div>
			<div class="listado-ranking-ben30d"><strong>Ben. 30D (<a href="ranking.php?grupo='.$grupo_nombre.'&orden_grupo=usuario_karma">%</a>)</strong></div>
			<div class="listado-ranking-beneficio-hoy"><strong>Ben. hoy</strong></div>
			</div>'."\n";
	
	$row=0;
	while (isset($ranking[$row]->usuario_login)) {
		$ranking_usuario=$db->get_results("SELECT `ranking_ben30d`, `ranking_invertido`, `ranking_beneficio_hoy` FROM `ranking` where `ranking_fecha`=CURDATE() AND ranking_usuario='".$ranking[$row]->usuario_login."' LIMIT 1");
		
		echo '<div id="listado-'.$row.'" class="listado-ranking-log">';
		echo '<div id="listado-'.$row.'" class="listado-ranking-top">'.($row+1).'</div>';
		//Ponemos en negrita el usuario visitante para que se vea
		if ($ranking[$row]->usuario_login==$_SESSION["usuario"]) { echo "<b>"; }
		echo '<div class="listado-ranking-usuario"><a href="index.php?usuario='.$ranking[$row]->usuario_login.'">'.$ranking[$row]->usuario_login.'</a></div>';
		echo '<div class="listado-ranking-saldo">'.number_format($ranking[$row]->usuario_saldo, 2, ",", ".").' €</div>';
		echo '<div class="listado-ranking-invertido">'.number_format($ranking_usuario[0]->ranking_invertido, 2, ",", ".").' €</div>';
		
		echo '<div class="listado-ranking-ben30d">'.number_format($ranking_usuario[0]->ranking_ben30d, 2, ",", ".").' € ('.$ranking[$row]->usuario_karma.' %)</div>';
		echo '<div class="listado-ranking-beneficio-hoy">'.number_format($ranking_usuario[0]->ranking_beneficio_hoy, 2, ",", ".").' €</div>';
		if ($ranking[$row]->usuario_login==$_SESSION["usuario"]) { echo "</b>"; }
		echo "</div>\n";

		$total_grupo_saldo=$total_grupo_saldo+$ranking[$row]->usuario_saldo;
		$total_grupo_invertido=$total_grupo_invertido+$ranking_usuario[0]->ranking_invertido;
		$total_grupo_ben30d=$total_grupo_ben30d+$ranking_usuario[0]->ranking_ben30d;
		$total_grupo_beneficio_hoy=$total_grupo_beneficio_hoy+$ranking_usuario[0]->ranking_beneficio_hoy;

		$row++;
	}

	echo "<br />";
	echo '<div id="listado-'.$row.'" class="listado-ranking-log">';
	echo '<div id="listado-'.$row.'" class="listado-ranking-top">&nbsp;</div>';
	echo '<div class="listado-ranking-usuario"><a href="ranking.php?grupo='.$grupo_nombre.'">'.$grupo_nombre.'</a></div>';
	echo '<div class="listado-ranking-saldo"><strong>'.number_format($total_grupo_saldo, 2, ",", ".").' €</strong></div>';
	echo '<div class="listado-ranking-invertido"><strong>'.number_format($total_grupo_invertido, 2, ",", ".").' €</strong></div>';
	echo '<div class="listado-ranking-ben30d"><strong>'.number_format($total_grupo_ben30d, 2, ",", ".").' €</strong></div>';
	echo '<div class="listado-ranking-beneficio-hoy"><strong>'.number_format($total_grupo_beneficio_hoy, 2, ",", ".").' €</strong></div>';

	echo '</div>';

}
endif;


if ( !function_exists('ordenes_en_cola') ) :
function ordenes_en_cola() {
	global $db, $config;
	
	echo "<div>Órdenes en cola</div>";
	echo "<div><a href=\"http://bolsaphp.sukiweb.net/index.php?ordenes=todas\">Todas las órdenes</a></div>";
	echo "<div id=\"datos-ticker\">";
	echo "<fieldset><legend>Compras</legend>";
	echo "<div class=\"cuatrocolumnas\">";
	echo "<div class=\"col4izq\"><strong>Ticker</strong></div>";
	echo "<div class=\"col4cen1\"><strong>Acc. / Precio</strong></div>";
	echo "<div class=\"col4cen2\"><strong>Usuario</strong></div>";
	echo "<div class=\"col4der\"><strong>Fecha</strong></div>";
	$SELECT="SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix FROM ordenes ";
	$SELECT .=" WHERE intencion='COMPRA' ";
	if ($_GET["ordenes"]!="todas") { $SELECT .=" AND usuario='".$_SESSION["usuario"]."' "; }
	$SELECT .=" ORDER BY timestamp ASC";
	$compra=$db->get_results($SELECT);
	$row=0;
	while (isset($compra[$row]->id)) {
		$fecha=timestamp_to_fecha($compra[$row]->timestamp_unix);
		echo "<div class=\"col4izq\"><a href=\"index.php?ticker=".$compra[$row]->ticker."\">".$compra[$row]->ticker."</a></div>";
		echo "<div class=\"col4cen1\">".$compra[$row]->acciones." / ".$compra[$row]->valor." €</div>";
		echo "<div class=\"col4cen2\"><a href=\"index.php?usuario=".$compra[$row]->usuario."\">".$compra[$row]->usuario."</a></div>";
		echo "<div class=\"col4der\">".$fecha."</div>";
	$row++;
	}
	echo "</div>";
	echo "</fieldset>";
	echo "</div>";

	echo "<div id=\"datos-ticker-cartera\">";
	echo "<fieldset><legend>Ventas</legend>";
	echo "<div class=\"cuatrocolumnas\">";
	echo "<div class=\"col4izq\"><strong>Ticker</strong></div>";
	echo "<div class=\"col4cen1\"><strong>Acc. / Precio</strong></div>";
	echo "<div class=\"col4cen2\"><strong>Usuario</strong></div>";
	echo "<div class=\"col4der\"><strong>Fecha</strong></div>";

	$SELECT="SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix FROM ordenes ";
	$SELECT .=" WHERE intencion='VENTA' ";
	if ($_GET["ordenes"]!="todas") { $SELECT .=" AND usuario='".$_SESSION["usuario"]."' "; }
	$SELECT .=" ORDER BY timestamp ASC";
	$venta=$db->get_results($SELECT);
	$row=0;
	while (isset($venta[$row]->id)) {
		$fecha=timestamp_to_fecha($venta[$row]->timestamp_unix);
		echo "<div class=\"col4izq\"><a href=\"index.php?ticker=".$venta[$row]->ticker."\">".$venta[$row]->ticker."</a></div>";
		echo "<div class=\"col4cen1\">".$venta[$row]->acciones." / ".$venta[$row]->valor." €</div>";
		echo "<div class=\"col4cen2\"><a href=\"index.php?usuario=".$venta[$row]->usuario."\">".$venta[$row]->usuario."</a></div>";
		echo "<div class=\"col4der\">".$fecha."</div>";
		
	$row++;
	}
	echo "</div>";
	echo "</fieldset>";
	echo "</div>";
	

}
endif;


if ( !function_exists('caja_novedades') ) :
function caja_novedades() {
	echo "<fieldset>";
	echo "<legend>Novedades</legend>";
	echo "<p>Ahora en la página principal los valores se actualizan automáticamente. Explicación y comentarios en \"<a href=\"http://sukiweb.net/archivos/2007/02/24/bolsaphp-con-refresco-automatico/\">BolsaPHP con refresco automático</a>\"</p>";
	echo "</fieldset>";
}
endif;
?>
