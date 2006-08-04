<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

if ( !function_exists('menu_superior') ) :
function menu_superior() {
	
	echo "<div id=\"logo\"><a href=\"index.php\">BolsaPHP</a></div>";
	echo "<div id=\"menu-superior\">";
	echo "<ul>";
	echo "<li><a href=\"index.php\">".__("Página principal")."</a></li>";
	echo "<li><a href=\"index.php?log=1\">".__("Fisgón")."</a></li>";
	echo "<li><a href=\"todo.php\">".__("Por hacer...")."</a></li>";
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
		echo "<ul>";
		if ($mensaje_de_error) { 
			echo "<li><b>".$mensaje_de_error."</b></li>"; 
		} else {
			echo "<li>Registrado con éxito. Ya puedes hacer login con tu usuario.</li>";
		}
		echo "</ul>";
	} else {
		echo "<ul>";
		if ($mensaje_de_error) { echo $mensaje_de_error; }
		echo "<form method=\"post\" action=\"index.php?login=register\">";
		echo "<li>".__("Usuario")." <input type=\"text\" name=\"login_registro\" value=\"\" size=\"10\"></li>";
		echo "<li>".__("Password")." <input type=\"password\" name=\"password_registro\" value=\"\" size=\"10\"></li>";
		echo "<li>".__("E-mail")." <input type=\"text\" name=\"email_registro\" value=\"\" size=\"10\"></li>";
		echo "<li><input type=\"Submit\" value=\"".__("Enviar")."\"></li>";
		echo "</form>";
		echo "</ul>";
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
		logea("registro ".$username);
		
		//Creamos el ranking con un día atrás para que no obtenga beneficios de 60000 al actualizar el ranking hoy
		$SELECT ="INSERT INTO ranking ( ranking_usuario, ranking_saldo, ranking_invertido, ranking_total, ranking_beneficio_hoy, ranking_fecha ) ";
		$SELECT .= " VALUES ( '".$username."', '60000', '0', '60000', '0', CURDATE()-1 )";
		$result = $db->get_results($SELECT);
	}
	return $mensaje_de_error;
}
endif;

if ( !function_exists('caja_estadisticas') ) :
function caja_estadisticas() {
	global $db;
	$usuarios_registrados=$db->get_var("SELECT count(*) FROM usuarios ");
	$movimientos_dia=$db->get_var("SELECT count(*) FROM log WHERE log_fecha>=CURDATE()-1 ORDER BY 'log_fecha'");
	$mas_invertido=$db->get_results("SELECT ticker,SUM(saldo) as suma_saldo FROM  carteras GROUP BY ticker ORDER BY suma_saldo desc LIMIT 1");
	echo "<ul>".__("Estadísticas");
	echo "<li>".__("Usuarios registrados").": ".$usuarios_registrados."</li>";
	echo "<li>".__("En 24h").": ".$movimientos_dia." movimientos</li>";
	echo "<li><b>".__("Más invertido ahora")."</b>:</li>";
	echo "<li><a href=\"index.php?ticker=".$mas_invertido[0]->ticker."\">".$mas_invertido[0]->ticker."</a> -  ".number_format($mas_invertido[0]->suma_saldo, 2, ",", ".")." €</li>";
	echo '<li><a href="index.php?ticker='.$mas_invertido[0]->ticker.'">';
	echo "<img src=\"chart.php?ticker=".$mas_invertido[0]->ticker."&dias=1&tam=mini\" border=\"0\">";
	echo "</a></li>";
	echo "</ul>";
}
endif;

if ( !function_exists('caja_login') ) :
function caja_login() {
	global $mensaje_de_error;
	
	
	echo "<div id=\"login\">";
	if (!$_SESSION["email"]) {
		if ($mensaje_de_error) { echo "<ul><li>".$mensaje_de_error."</li></ul>"; }
		//echo "Acceso a usuarios registrados";
		echo "<ul>";
		echo "<form method=\"post\" action=\"index.php?login=login\">";
		echo "<li>".__("Usuario")." <input type=\"text\" name=\"login\" value=\"\" size=\"10\"></li>";
		echo "<li>".__("Password")." <input type=\"password\" name=\"password\" value=\"\" size=\"10\"></li>";
		echo "<li><input type=\"Submit\" value=\"".__("Enviar")."\"></li>";
		echo "</form>";
		echo "</ul>";
	} 
	echo "</div>";
}
endif;


if ( !function_exists('grafica_ticker') ) :
function grafica_ticker($ticker) {
	global $quotes, $db, $config;	
	
	if (!$_GET[dias]) { $_GET[dias]=5; }
	if (!$_GET[ticker]) { $_GET[ticker]=$ticker; }
	
	
	echo "<div id=\"grafico-ticker\">";
	
	echo "<form name=\"form\" method=\"get\" action=\"index.php?ticker=".$_GET[ticker]."\">";
	echo "<select name=\"ticker\" OnChange=\"document.form.submit()\">";
			
		foreach ($quotes as $ticker1) {
			echo "<option name=\"ticker\" value=\"".$ticker1."\" "; 
			if ($_GET[ticker]==$ticker1) { echo "selected"; }
			echo " >".empresa_ticker($ticker1)." (".$ticker1.")</option>";
		}
		echo "</select>";
	echo "</form>";

	echo '<script type="text/javascript" src="grafica.js.php?ticker='.$_GET[ticker].'"></script>';
	echo '</div>';
	
	
	
	$SELECT= "SELECT * from quotes WHERE ticker='".$_GET[ticker]."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
	$result = $db->get_results($SELECT);

	echo "<div id=\"datos-ticker\">";
		echo "<ul><b>".empresa_ticker($_GET[ticker])."</b>";
		echo "<li><div class=\"doscolumnas\">";
		echo "<div class=\"col2izq\">Valor:</div><div class=\"col2der\"> <b>".number_format($result[0]->valor, 2, ",", ".")." €</b></div>";
		echo "<div class=\"col2izq\">Fecha:</div><div class=\"col2der\"> <b>".$result[0]->fecha."</b></div>";
		echo "<div class=\"col2izq\">Hora:</div><div class=\"col2der\"> <b>".$result[0]->hora."</b></div>";
		echo "<div class=\"col2izq\">Volumen:</div><div class=\"col2der\"> <b>".number_format($result[0]->volumen, 0, "", ".")."</b></div>";
		echo "<div class=\"col2izq\">Cambio:</div><div class=\"col2der\"> <b>".$result[0]->cambio."</b></div>";
		echo "<div class=\"col2izq\">Apertura:</div><div class=\"col2der\"> <b>".number_format($result[0]->apertura, 2, ",", ".")." €</b></div>";
		echo "<div class=\"col2izq\">Cierre:</div><div class=\"col2der\"> <b>".number_format($result[0]->cierre, 2, ",", ".")." €</b></div>";
		echo "<div class=\"col2izq\">Valor bajo:</div><div class=\"col2der\"> <b>".number_format($result[0]->rango_dia_bajo, 2, ",", ".")." €</b></div>";
		echo "</div></li>";
		echo "</ul>";
	echo "</div>";
	
	

	echo "<div id=\"datos-ticker-cartera\">";
		if ($_POST["compraventa"]=="Comprar") {		
			clean_text($_POST[notas]);
			
			// Para comprobar la compra
			$SELECT="SELECT usuario_saldo, usuario_id from usuarios WHERE usuario_id='".$_SESSION["usuario_id"]."'";
			$saldo = $db->get_results($SELECT);
			$precio=$_POST[acciones]*$result[0]->valor;

			if ($saldo[0]->usuario_saldo>$precio) { 
				compra_ticker($_POST["ticker"], $_POST[acciones], $result[0]->valor, $_POST[notas]);
				$mensaje_de_error="<b>Compradas ".$_POST[acciones]." acciones a ".$result[0]->valor."</b>";
			} else {
				$mensaje_de_error="<b>No tienes suficiente saldo para comprar ".$_POST[acciones]." acciones</b>";
			}
		}
		if ($_POST["compraventa"]=="Vender") {
			clean_text($_POST[notas]);
			$SELECT="SELECT acciones, ticker, usuario FROM carteras WHERE usuario='".$_SESSION["usuario"]."' AND ticker='".$_POST["ticker"]."' ";
 			$acciones = $db->get_results($SELECT);
			
			if ($_POST[acciones]<=$acciones[0]->acciones) {	
				vende_ticker($_POST["ticker"], $_POST[acciones], $result[0]->valor,$_POST["notas"]);
				$mensaje_de_error="<b>Vendidas ".$_POST[acciones]." acciones a ".$result[0]->valor."</b>";
			} else {
				$mensaje_de_error="<b>No tienes suficientes acciones para vender de ".$_POST["ticker"]."</b>";
			}
		}

		//Sacamos los datos para la compra
		$SELECT="SELECT * FROM carteras WHERE ticker='".$_GET["ticker"]."' and usuario='".$_SESSION["usuario"]."'";
		$cartera = $db->get_results($SELECT);

		if ($mensaje_de_error) { echo $mensaje_de_error; }
		echo "<ul>Movimientos de <b>".empresa_ticker($_GET[ticker])."</b> en tu cartera.";
		echo "<li><form name=\"ticker-cartera\" method=\"post\" action=\"?ticker=".$_GET[ticker]."\">";
		echo "<input type=\"hidden\" name=\"ticker\" value=\"".$_GET[ticker]."\">";
		echo "<input type=\"text\" name=\"acciones\" value=\"";
		if ($cartera[0]->acciones<=0) { echo "100"; } else { echo $cartera[0]->acciones; }
		echo "\" size=\"10\" onKeyUp=calculaprecio(this.form)> Acciones</li>";
		echo "<li><input type=\"text\" name=\"valor\" READONLY value=\"".$result[0]->valor."\" size=\"10\">€ Valor</li>";
		echo "<li><input type=\"text\" name=\"total\" size=\"10\" value=\"".($result[0]->valor*100)."\" onKeyUp=\"calculaacciones(this.form)\">€ Total</li>";
		echo "<li>Pequeña anotación sobre este valor<br /><TEXTAREA ROWS=\"4\" COLS=\"25\" name=\"notas\">";
		if ($cartera[0]->notas) { echo $cartera[0]->notas; }
		echo "</TEXTAREA></li>";
		echo "<li><input type=\"submit\" name=\"compraventa\" value=\"Comprar\"> - <input type=\"submit\" name=\"compraventa\" value=\"Vender\"></form></li>";
		
		echo "Recuerda que todas las operaciones (compras y ventas) tienen un <b>".$config['comision']."%</b> de comisión.";
	echo "</div>";

		echo "<div id=\"datos-ticker\">";
		echo "<ul><b>Información externa sobre ".empresa_ticker($_GET[ticker])."</b>";
		echo "<li>";
		echo '<!-- Search Google -->
			<form method="get" action="http://www.google.es/custom" target="_blank">
			<input type="text" name="q" size="8" maxlength="255" value="'.empresa_ticker($_GET[ticker]).'"></input>
			<input type="submit" name="sa" value="Buscar en Google"></input>
			<input type="hidden" name="client" value="pub-6311366192077645"></input>
			<input type="hidden" name="forid" value="1"></input>
			<input type="hidden" name="channel" value="8596761390"></input>
			<input type="hidden" name="ie" value="UTF-8"></input>
			<input type="hidden" name="oe" value="UTF-8"></input>
			<input type="hidden" name="flav" value="0000"></input>
			<input type="hidden" name="sig" value="T3DuTUPxTIK5TiKY"></input>
			<input type="hidden" name="cof" value="GALT:#008000;GL:1;DIV:#336699;VLC:663399;AH:center;BGC:FFFFFF;LBGC:336699;ALC:0000FF;LC:0000FF;T:000000;GFNT:0000FF;GIMP:0000FF;FORID:1;"></input>
			<input type="hidden" name="hl" value="es"></input>
			</form>
			<!-- Search Google -->';
		echo "</li>";
		echo "<li><a href=\"http://www.google.com/finance?q=".empresa_ticker($_GET[ticker])."\" target=\"_blank\">Sobre ".empresa_ticker($_GET[ticker])." en Google Finance</a></li>";
		echo "<li><a href=\"http://es.finance.yahoo.com/q?s=".$_GET[ticker]."\" target=\"_blank\">Sobre ".empresa_ticker($_GET[ticker])." en Yahoo Finanzas</a></li>";
		echo "</ul>";
		echo "</div>";
				
	
		echo "<div id=\"datos-ticker-externo\">";
		echo "<ul><b>Añade ".empresa_ticker($_GET[ticker])." en tu web</b>";
		echo "<li>Para incluir una gráfica de este ticker en tu web, tan sólo copia y pega este código en ella.</li>";
		echo "<li><textarea><script type=\"text/javascript\" src=\"http://bolsaphp.sukiweb.net/bolsaphp.js.php?ticker=".$_GET[ticker]."\"></script></textarea></li>";
		echo "</ul>";
		echo "</div>";
		

	// Historico del ticker que estamos viendo
		echo "<div id=\"datos-ticker-historico\">";
		echo "<ul>Histórico de <b>".empresa_ticker($_GET[ticker])."</b></ul>";
		listado_log("",$_GET[ticker]);
		echo "</div>";
	
	


}
endif;


if ( !function_exists('listado_quotes') ) :
function listado_quotes() {
	global $quotes, $db;
	
		include("inc/mini-img.js");
		echo '<div id="tipDiv" style="position:absolute; visibility:hidden; z-index:100"></div>';
	


	echo '<div class="listado-item">
		
		<div class="listado-ticker"><strong>Ticker</strong></div>
		<div class="listado-porcentaje-acciones"><strong>En bolsaPHP</strong></div>
		<div class="listado-valor"><strong>Valor</strong></div>
		<div class="listado-volumen"><strong>Volumen</strong></div>
		<div class="listado-cambio"><strong>Cambio</strong></div>
		<div class="listado-fecha"><strong>Fecha</strong></div>
		</div>'."\n";
	$SELECT="SELECT SUM(saldo) as total_acciones FROM  carteras";
	$acciones_carteras_total = $db->get_results($SELECT);
	
	$i=0;
	foreach ($quotes as $ticker) {
		$SELECT= "SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix from quotes WHERE ticker='".$ticker."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
		$result = $db->get_results($SELECT);
		
		// Esto es para sacar en bolsaphp
		$SELECT ="SELECT ticker,SUM(saldo) as suma_acciones FROM  carteras WHERE ticker='".$ticker."' GROUP BY ticker";
		$acciones_carteras = $db->get_results($SELECT);
		 
		$acciones_porcentaje = ($acciones_carteras[0]->suma_acciones*100)/$acciones_carteras_total[0]->total_acciones;


		echo '<div id="listado-'.$i.'" class="listado-item">';
		echo '<div class="listado-ticker"><a href="?ticker='.$ticker.'"';
		
			echo 'onmouseover="doTooltip(event,0,\''.$ticker.'\')" onmouseout="hideTip()"';
		
		echo ' >'.empresa_ticker($ticker).' </a></div>';
	
		echo '<div class="listado-porcentaje-acciones">'.number_format($acciones_porcentaje, 2, ",", ".").' %</div>';
			
			
		echo '<div class="listado-valor">'.number_format($result[0]->valor, 2, ",", ".").' €</div>';
		echo '<div class="listado-volumen">'.number_format($result[0]->volumen, "", "", ".").'</div>';
		if ($result[0]->cambio<0) {
		echo '<div class="listado-cambio-baja">'.$result[0]->cambio.'</div>';
		} else if ($result[0]->cambio>0) {
		echo '<div class="listado-cambio-sube">'.$result[0]->cambio.'</div>';
		} else {
		echo '<div class="listado-cambio">'.$result[0]->cambio.'</div>';
		}
		$fecha=timestamp_to_fecha($result[0]->timestamp_unix);
		//$timestamp = strtotime($result[0]->timestamp);
		
		echo '<div class="listado-fecha">'.$fecha.'</div>';
		echo "</div>\n";
		$i++;
	}
	
}
endif;

if ( !function_exists('listado_cartera') ) :
function listado_cartera() {
	global $db, $config;
	echo "<div id=\"listado-cartera\">\r\n";
	$SELECT="SELECT usuario_saldo, usuario_id, usuario_email FROM usuarios WHERE usuario_id='".$_SESSION["usuario_id"]."'";
	$saldo = $db->get_results($SELECT);
	$SELECT="SELECT SUM(saldo) as saldototal FROM carteras WHERE usuario='".$_SESSION["usuario"]."' AND acciones>='1'";
	$invertido = $db->get_results($SELECT);

	$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($saldo[0]->usuario_email)."&amp;size=50";
	
	echo "<ul>";
	echo "<a href=\"index.php?usuario=".$_SESSION["usuario"]."\"><img src=\"".$grav_url."\" alt=\"".$usuario."\" align=\"right\" border=\"0\"></a>";
	echo "Perfil de <b><a href=\"index.php?usuario=".$_SESSION["usuario"]."\">".$_SESSION["usuario"]."</a></b> (<a href=\"index.php?log=".$_SESSION["usuario"]."\">Historial</a>)";
	echo "<li><div class=\"doscolumnas\">";
	echo "<div class=\"col2izq\">Saldo:</div><div class=\"col2der\"><b>".number_format($saldo[0]->usuario_saldo, 2, ",", ".")."</b> €</div>";
	echo "<div class=\"col2izq\">Invertido:</div><div class=\"col2der\"> <b>".number_format($invertido[0]->saldototal, 2, ",", ".")."</b> €</div>";
	$total=$saldo[0]->usuario_saldo+$invertido[0]->saldototal;
	echo "<div class=\"col2izq\">Total:</div><div class=\"col2der\"> <b>".number_format($total, 2, ",", ".")."</b> €</div>";
	echo "</li>";
	echo "</ul>";
	echo "<ul>Tu cartera:";


		$SELECT="SELECT * FROM carteras WHERE usuario='".$_SESSION["usuario"]."' AND acciones>='1' ORDER BY ticker";
		$result = $db->get_results($SELECT);
		$row=0;
		while (isset($result[$row]->id)) {
			$SELECT= "SELECT valor, timestamp FROM quotes WHERE ticker='".$result[$row]->ticker."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
			$valor = $db->get_results($SELECT);
			$valor_actual=$valor[0]->valor*$result[$row]->acciones;
			$comision=(($valor_actual*$config['comision'])/100)*2;
			echo "\r\n";
			if ($valor_actual<$result[$row]->saldo) { echo "<ul class=\"baja\">"; }
			else if ($valor_actual>$result[$row]->saldo) { echo "<ul class=\"sube\">"; }
			else { echo "<ul>"; }
			echo "<a href=\"index.php?ticker=".$result[$row]->ticker."\">".$result[$row]->ticker."</a> ".$valor[0]->valor." €";

			echo "<li><div class=\"doscolumnas\">";
			echo "<div class=\"col2izq\">Acciones: </div><div class=\"col2der\">".$result[$row]->acciones."</div>";
			echo "<div class=\"col2izq\">Invertido: </div><div class=\"col2der\">".number_format($result[$row]->saldo, 2, ",", ".")." €</div>";
			echo "<div class=\"col2izq\">Actual: </div><div class=\"col2der\">".number_format($valor_actual, 2, ",", ".")." € </div>";
			$diferencia=$valor_actual-$result[$row]->saldo;
			
			$diferencia_porcentaje = (($valor_actual*100)/$result[$row]->saldo)-100;
			echo "<div class=\"col2izq\">Diferencia %:</div><div class=\"col2der\">".number_format($diferencia_porcentaje, 2, ",", ".")." %</div>";
			echo "<div class=\"col2izq\">Diferencia: </div><div class=\"col2der\">".number_format($diferencia, 2, ",", ".")." € </div>"; 
			
			
			
			//echo "<li>Comisión: ".number_format($comision, 2, ",", ".")." € </li>";
			if ($result[$row]->notas) {
				echo "<div class=\"col2izq\"><b>Tus notas:</b></div>";
				$notas=txt_shorter($result[$row]->notas);
				echo "<div class=\"col2der\"><a href=\"index.php?ticker=".$result[$row]->ticker."\">".$notas."</a>";
				echo "</div>";
			}
			echo "</li>";
			echo "</ul>";	
			$row++;
		}
		
	echo "</ul>";
	echo "</div>\r\n"; //end div listado-cartera
}
endif;

if ( !function_exists('compra_ticker') ) :
function compra_ticker($ticker, $acciones, $valor, $notas) {
	global $db, $config;
	$SELECT ="SELECT * FROM carteras WHERE ticker='".$ticker."' AND usuario='".$_SESSION["usuario"]."'";
	$result = $db->get_results($SELECT);
	
	if (!$result[0]->id) {	
		$saldo=$acciones*$valor;
		
		$SELECT = "INSERT INTO carteras ( ticker, saldo, acciones, usuario, notas) ";
		$SELECT .= "VALUES ( '".$ticker."', '".$saldo."', '".$acciones."', '".$_SESSION["usuario"]."', '".$notas."' )";
		$result = $db->get_results($SELECT);
		
		// y actualizamos el saldo del usuario
		
		//Calculamos el porcetaje de comisión
		$comision=($saldo*$config['comision'])/100;
		$saldo=$saldo+$comision;
		
		$SELECT = "UPDATE usuarios SET usuario_saldo=usuario_saldo-".$saldo." WHERE usuario_login='".$_SESSION["usuario"]."' ";
		$result = $db->get_results($SELECT);
		logea("Compra ".$acciones." de <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> a ".$valor." €");
		actualiza_ranking($_SESSION["usuario"]);
	} else {	
		$saldo=$acciones*$valor;
		$SELECT = "UPDATE carteras SET saldo=saldo+".$saldo.", acciones=acciones+".$acciones.", notas='".$notas."' WHERE ticker='".$ticker."' AND usuario='".$_SESSION["usuario"]."' ";
		$result = $db->get_results($SELECT);
		
		// y actualizamos el saldo del usuario
		
		//Calculamos el porcetaje de comisión
		$comision=($saldo*$config['comision'])/100;
		$saldo=$saldo+$comision;
		
		$SELECT = "UPDATE usuarios SET usuario_saldo=usuario_saldo-".$saldo." WHERE usuario_login='".$_SESSION["usuario"]."' ";
		$result = $db->get_results($SELECT);
		logea("Compra ".$acciones." de <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> a ".$valor." €");
		actualiza_ranking($_SESSION["usuario"]);
	}
}
endif;

if ( !function_exists('vende_ticker') ) :
function vende_ticker($ticker, $acciones, $valor, $notas) {
	global $db, $config;

	$saldo=$acciones*$valor;
	
	$SELECT = "UPDATE carteras SET saldo=saldo-".$saldo.", acciones=acciones-".$acciones.", notas='".$notas."' WHERE ticker='".$ticker."' AND usuario='".$_SESSION["usuario"]."' ";
	$result = $db->get_results($SELECT);
		
	// y actualizamos el saldo del usuario
	//Calculamos el porcetaje de comisión
	$comision=($saldo*$config['comision'])/100;
	$saldo=$saldo-$comision;
	$SELECT = "UPDATE usuarios SET usuario_saldo=usuario_saldo+".$saldo." WHERE usuario_login='".$_SESSION["usuario"]."' ";
	$result = $db->get_results($SELECT);
	logea("Vende ".$acciones." de <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> a ".$valor." €");
	actualiza_ranking($_SESSION["usuario"]);

	//Comprobamos si este ticker ya no tiene acciones, si es así, lo borramos.
	$SELECT="SELECT id, acciones FROM carteras WHERE ticker='".$ticker."' AND usuario='".$_SESSION["usuario"]."' AND acciones<=0";
	$result = $db->get_results($SELECT);
	if ($result[0]->id) {
		$SELECT="DELETE from carteras WHERE id=".$result[0]->id;
 		$result = $db->get_results($SELECT);
		logea("Eliminado <a href=\"index.php?ticker=".$ticker."\">".$ticker."</a> de la cartera");
	}
}
endif;

if ( !function_exists('listado_log') ) :
function listado_log($usuario = "", $ticker="") {
	global $quotes, $db;
	
	echo '<div class="listado-log-log">
		<div class="listado-log-usuario"><strong>Usuario</strong></div>
		<div class="listado-log-accion"><strong>Acción</strong></div>
		<div class="listado-log-fecha"><strong>Fecha</strong></div>
		</div>'."\n";
	
	
	$SELECT= "SELECT *, UNIX_TIMESTAMP(log_fecha) as timestamp_unix from log";
	if ($usuario) { $SELECT .=" WHERE log_usuario_login='".$usuario."' AND log_descripcion != 'login' AND log_descripcion != 'logout'"; 
	} else  if ($ticker) { $SELECT .=" WHERE log_descripcion LIKE '%".$ticker."%' ";
	} else { $SELECT .=" WHERE log_descripcion != 'login' AND log_descripcion != 'logout' "; }
	$SELECT .=" ORDER BY 'log_fecha' DESC LIMIT 0 , 30 ";
	$result = $db->get_results($SELECT);
	$row=0;
	while (isset($result[$row]->log_id)) {
		echo '<div id="listado-'.$row.'" class="listado-log-log">';
		echo '<div class="listado-log-usuario"><a href="index.php?usuario='.$result[$row]->log_usuario_login.'">'.$result[$row]->log_usuario_login.'</a></div>';
		echo '<div class="listado-log-accion">'.$result[$row]->log_descripcion.'</div>';
				
		$fecha=timestamp_to_fecha($result[$row]->timestamp_unix);
		//$timestamp = strtotime($result[0]->timestamp);
		
		echo '<div class="listado-log-fecha">'.$fecha.'</div>';
		echo "</div>\n";
		$row++;
	}
	
}
endif;

if ( !function_exists('bloque_ads') ) :
function bloque_ads() {
	global $config;
	echo "<div id=\"ads\">";
	
	//echo "<b>Última actualización:</b> Se ha bajado el precio de las comisiones a un ".$config['comision']."%, gracias a la información de algunos usuarios, buscando una mayor jugabilidad y no alejandonos mucho de la realidad. :) <br /><br />Felices beneficios.";
	
	echo '<script type="text/javascript"><!--
		google_ad_client = "pub-6311366192077645";
		google_ad_width = 728;
		google_ad_height = 90;
		google_ad_format = "728x90_as";
		google_ad_type = "text";
		google_ad_channel ="9799508016";
		google_color_border = "CCCCCC";
		google_color_bg = "F0F0F0";
		google_color_link = "000000";
		google_color_text = "333333";
		google_color_url = "666666";
		//--></script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>';
	
	echo "</div>";
}
endif;

if ( !function_exists('actualiza_ranking') ) :
function actualiza_ranking($usuario) {
	global  $db, $config;
	$SELECT="SELECT * FROM usuarios WHERE usuario_login='".$usuario."'";
	
	$result = $db->get_results($SELECT);
	$row=0;
	//echo "<ol>";
	while (isset($result[$row]->usuario_id)) {
	//	echo "<li>".$result[$row]->usuario_login;
	//		echo "<ul>";
			$SELECT="SELECT SUM(saldo) as invertido FROM carteras WHERE usuario='".$result[$row]->usuario_login."' AND acciones>='1'";
	//		echo "<li>".$SELECT."</li>";
			$invertido = $db->get_results($SELECT);
			$ranking_total=$result[$row]->usuario_saldo+$invertido[0]->invertido;
			
			$SELECT="SELECT ranking_total FROM ranking WHERE ranking_usuario='".$result[$row]->usuario_login."' AND ranking_fecha=CURDATE()-INTERVAL 1 DAY ";
			$total_ayer = $db->get_results($SELECT);
			$beneficio_hoy = $ranking_total-$total_ayer[0]->ranking_total;
	//		echo "<li>".$SELECT."</li>";
			
			//Comprobamos si hoy ya tiene ranking
			$SELECT="SELECT ranking_fecha, ranking_usuario FROM ranking WHERE ranking_fecha=CURDATE() AND ranking_usuario='".$usuario."'";
	//		echo "<li>".$SELECT."</li>";
			$comp = $db->get_results($SELECT);
			if (empty($comp[0]->ranking_usuario)) {
				$SELECT="INSERT INTO ranking ( ranking_usuario, ranking_saldo, ranking_invertido, ranking_total, ranking_beneficio_hoy, ranking_fecha ) ";
				$SELECT .= " VALUES ( '".$result[$row]->usuario_login."', '".$result[$row]->usuario_saldo."', '".$invertido[0]->invertido."', '".$ranking_total."', '".$beneficio_hoy."', CURDATE() )";
	//			echo "<li>".$SELECT."</li>";
				$actualiza_ranking = $db->get_results($SELECT);
			} else { //Si ya existe, hacemos update.
				$SELECT="UPDATE ranking SET ranking_saldo='".$result[$row]->usuario_saldo."', ranking_invertido='".$invertido[0]->invertido."', ranking_total='".$ranking_total."', ranking_beneficio_hoy='".$beneficio_hoy."' WHERE ranking_usuario='".$usuario."' AND ranking_fecha=CURDATE()";
				$actualiza_ranking = $db->get_results($SELECT);
	//			echo "<li>".$SELECT."</li>";	
			}
	//		echo "</ul>";
	//	echo "</li>";
		$row++;
	}
}
endif;

if ( !function_exists('datos_usuario') ) :
function datos_usuario($usuario) {
	global $quotes, $db, $config;	
	
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
	//echo "<input type=\"Submit\" value=\"Mostrar\">";
	
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
				//echo $SELECT;
				echo "Clave cambiada con éxito";
			}
		} 
	}

	if ($_POST["cambiadatos"]) {
		$SELECT = "UPDATE usuarios SET usuario_nombre='".$_POST["usuario_nombre"]."', usuario_url='".$_POST["usuario_url"]."', usuario_email='".$_POST["usuario_email"]."' WHERE usuario_login='".$_SESSION["usuario"]."' ";
		$result = $db->get_results($SELECT);
		echo "Cambiados los datos con éxito";
	}

	
	$SELECT="SELECT usuario_login, usuario_fecha, usuario_email, usuario_nombre, usuario_url, usuario_saldo from usuarios WHERE usuario_login='".$usuario."'";
	$result = $db->get_results($SELECT);
	
	$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($result[0]->usuario_email)."&amp;size=50";
	
	
	echo "<div id=\"datos-ticker\">";
		echo "<ul><b>Datos de ".$usuario."</b> ";
		if ($usuario==$_SESSION["usuario"]) {
			echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
			echo "<li><img src=\"".$grav_url."\" alt=\"".$usuario."\" align=\"right\"> <br /> <a href=\"http://www.gravatar.com/signup.php\">Foto de Gravatar.com</a>";
			echo "<li> Nombre:</li>";
			echo "<li><input type=\"text\" name=\"usuario_nombre\" value=\"".$result[0]->usuario_nombre."\"></li>";
			echo "<li>Web:</li>";
			echo "<li><input type=\"text\" name=\"usuario_url\" value=\"".$result[0]->usuario_url."\"></li>";
			echo "<li>E-mail:"; 
			echo "<li><input type=\"text\" name=\"usuario_email\" value=\"".$result[0]->usuario_email."\">";
			echo "<input type=\"submit\" name=\"cambiadatos\" value=\"Guardar\"></li>";
			echo "</form>";
			echo "<form method=\"post\" action=\"?usuario=".$usuario."\">";
			echo "<li>Nuevo Password (teclea 2 veces el mismo)</li>";
			echo "<li><input type=\"password\" name=\"password1\" value=\"\"></li>";
			echo "<li><input type=\"password\" name=\"password2\" value=\"\">";
			echo "<input type=\"submit\" name=\"cambiapassword\" value=\"Cambiar Password\"></li>";
			echo "</form>";
		} else {
			echo "<li><img src=\"".$grav_url."\" alt=\"".$usuario."\" align=\"right\"> Nombre: <b>".$result[0]->usuario_nombre."</b></li>";
			echo "<li>Web: <b>".text_to_html($result[0]->usuario_url)."</b></li>";
		}
		echo "<li>Fecha registro: <b>".$result[0]->usuario_fecha."</b></li>";
		echo "<li>Saldo actual: <b>".number_format($result[0]->usuario_saldo, 0, "", ".")." €</b></li>";
		$SELECT="SELECT SUM(ranking_beneficio_hoy) as beneficio from ranking WHERE ranking_usuario='".$usuario."' AND ranking_fecha>=CURDATE()- INTERVAL ".$_GET[dias]." DAY";
		$result2 = $db->get_results($SELECT);
		echo "<li>Beneficio últimos ".$_GET[dias]." días: <b>".number_format($result2[0]->beneficio, 0, "", ".")." € </b></li>";
		echo "</ul>";
	echo "</div>";
	
	$SELECT="SELECT * from carteras WHERE usuario='".$usuario."'";
	$result = $db->get_results($SELECT);

	echo "<div id=\"datos-ticker-cartera\">";
	echo "<ul>La cartera de <b>".$usuario."</b>";
	$row=0;
	while (isset($result[$row]->id)) {
		echo "<li><a href=\"index.php?ticker=".$result[$row]->ticker."\">".empresa_ticker($result[$row]->ticker)." </a> (".$result[$row]->acciones." Acciones)</li>";
	$row++;
	}
	echo "</ul>";
	echo "</div>";

	

	// Historico del usuario que estamos viendo
	echo "<div id=\"datos-ticker-historico\">";
	echo "<ul>Histórico de <b>".$usuario."</b></ul>";
	listado_log($usuario);
	echo "</div>";

}
endif;
?>