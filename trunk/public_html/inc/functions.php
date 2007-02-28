<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE
if ($_POST) {
	$posts= array_keys($_POST);
	for ($c=0;$c<count($_POST);$c++) {
		$_POST[$posts[$c]] = ereg_replace("<", "&lt;", $_POST[$posts[$c]]);
		$_POST[$posts[$c]] = ereg_replace(">", "&gt;", $_POST[$posts[$c]]);
		//echo "<br />de: ".$_POST[$posts[$c]]." a ".$posts2[$c];
		}
	}

function cabecera() {
	global $config;
	header("Content-type: text/html; charset=utf-8");
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">'."\n";
	echo '<head>' . "\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
	echo "<title>".__($config['titulo']);
	if ($_GET["ticker"]) { echo " - ".empresa_ticker($_GET["ticker"]); }
	echo "</title>\n";
	echo '<meta name="generator" content="David Martín :: Suki_ :: ( http://sukiweb.net )" />' . "\n";
	echo '<meta name="keywords" content="'.$config['tags'].'" />' . "\n";
	echo '<style type="text/css" media="screen">@import "'.$config['css'].'";</style>' . "\n";
	echo '<link rel="icon" href="/favicon.ico" type="image/x-icon" />' . "\n";
	echo '<script type="text/javascript" src="inc/xmlhttp.js"></script>';
	
	if ($_GET["ticker"]) {
		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS 2.0\" href=\"http://bolsaphp.sukiweb.net/rss.php?ticker=".$_GET["ticker"]."\" />";
	} else if  ($_GET["usuario"]) {
		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS 2.0\" href=\"http://bolsaphp.sukiweb.net/rss.php?usuario=".$_GET["usuario"]."\" />";
	} else {
		echo "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS 2.0\" href=\"http://bolsaphp.sukiweb.net/rss.php\" />";
	}	

	if ($_GET["log"]=="1") {
		echo '<script src="inc/chat.js" language="JavaScript" type="text/javascript"></script>';
	}
	echo '</head>' . "\n";
	echo '<body id="home"><div id="container">';
}

function pie() {
	//Cierra container div
	
	echo "</div>";
	echo "<div id=\"pie\">Por: <a href=\"http://sukiweb.net\">David Martín :: Suki_ ::</a></div>";
	
	echo "</body></html>";
}


function logea($descripcion, $tipo="", $usuario) {
	global $db;
	$ip=check_ip_behind_proxy();
	if ($tipo) {
		$SELECT = "INSERT INTO log (log_usuario_id, log_usuario_login, log_descripcion, log_ip, log_tipo) VALUES ('".$_SESSION["usuario_id"]."', '".$usuario."', '".$descripcion."', '".$ip."', '".$tipo."')";
	} else {
		$SELECT = "INSERT INTO log (log_usuario_id, log_usuario_login, log_descripcion, log_ip) VALUES ('".$_SESSION["usuario_id"]."', '".$usuario."', '".$descripcion."', '".$ip."')";
	}
	$result = $db->get_results($SELECT);
}

function empresa_ticker($ticker) {
	global $db;
	$res=$db->get_results("SELECT nombre_empresa FROM valores WHERE ticker='$ticker'");
	return $res[0]->nombre_empresa;
}

function user_exists($username) {
	global $db;
	$res=$db->get_var("SELECT count(*) FROM usuarios WHERE usuario_login='$username'");
	if ($res>0) return true;
	return false;
}

function email_exists($email) {
	global $db;
	$res=$db->get_var("SELECT count(*) FROM usuarios WHERE usuario_email='$email'");
	if ($res>0) return $res;
	return false;
}

function check_email($email) {
	return preg_match('/^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9_\-\.]+\.[a-zA-Z]{2,4}$/', $email);
}

function txt_shorter($string, $len=40) {
	if (strlen($string) > $len)
		$string = substr($string, 0, $len-3) . "...";
	return $string;
}

function clean_text($string) {
	return htmlspecialchars(strip_tags(trim($string)));
}

function save_text_to_html($string) {
	$string= text_to_html($string);
	$string = preg_replace("/\r\n|\r|\n/", "\n<br />\n", $string);
	return $string;
}

function text_to_html($string) {
	return preg_replace('/([hf][tps]{2,4}:\/\/[^ \t\n\r\]\(\)]+[^ .\t,\n\r\(\)"\'\]])/', '<a href="$1" rel="nofollow">$1</a>', $string);
}

function timestamp_to_fecha($timestamp)
{
	if($timestamp > time())
	//we don't handle future dates
	return date('Y-m-d H:i', $timestamp);
	elseif($timestamp > mktime(0,0,0))
	//since midnight so it's today
	return "Hoy ".date('H:i', $timestamp);
	elseif($timestamp > mktime(0,0,0) - 86400)
	//since midnight 1 day ago so it's yesterday
	return "Ayer ".date('H:i', $timestamp);
	elseif($timestamp > mktime(0,0,0) - 86400*7)
	//since midnight 7 days ago so it's this week
	return date('l H:i', $timestamp);
	elseif($timestamp > mktime(0,0,0,1,1))
	//since 1st Jan so it's this year
	return date('F j', $timestamp);
	else
	//ages ago!
	return date('F Y', $timestamp);
}

function gravatar($usuario, $tam)
{
	global $db;
	$default = "http://bolsaphp.sukiweb.net/images/avatar.gif";
	$res=$db->get_var("SELECT usuario_email, usuario_login FROM usuarios WHERE usuario_login='$usuario'");
	$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($res)."&amp;default=".urlencode($default)."&amp;size=".$tam;
	return $grav_url;
}

function beneficios($usuario, $dias) {
	global $db;
	$SELECT="SELECT SUM(ranking_beneficio_hoy) as beneficio from ranking WHERE ranking_usuario='".$usuario."' AND ranking_fecha>=CURDATE()- INTERVAL ".$dias." DAY";
	$res = $db->get_var($SELECT);
	return $res;
}

function envia_mail($usuario, $subject, $body) {
	global $db;
	$headers="From: alertas@bolsaphp.sukiweb.net\r\n";
	$headers.="MIME-Version: 1.0\r\n";
	$headers.="Content-Type: text/plain; charset=UTF-8;\r\n";

	$email=$db->get_var("SELECT usuario_email FROM usuarios WHERE usuario_login='$usuario'");
	if (mail($email, $subject, $body, $headers)) {
		// el mail se ha enviado correctamente
		
	}
}

function comprueba_alertas($ticker, $valor) {
	global $db;

	// Comprobamos las Mayor que
	$SELECT="SELECT * from alertas WHERE ticker='".$ticker."' AND condicion='>=' AND valor<='".$valor."' AND estado='ACTIVA'";
	$res = $db->get_results($SELECT);
	$row=0;
	while (isset($res[$row]->id)) {
		$alertas_email=$db->get_var("SELECT usuario_alertas_email from usuarios where usuario_login='".$res[$row]->usuario."'");
		if ($alertas_email==1) {
			envia_mail($res[$row]->usuario, "[BolsaPHP] Alerta de ".$ticker, "Se ha disparado una alerta programada:\r\n\r\n La alerta estaba programada para avisar al igualar o superar ".$res[$row]->valor." €.\r\n Las acciones de ".$ticker." están actualmente a ".$valor." €.  \r\n \r\n http://bolsaphp.sukiweb.net/index.php?ticker=".$ticker." \r\n\r\n"); 
			$alerta=$db->get_var("UPDATE alertas SET estado='AVISADO' where id='".$res[$row]->id."'");
			echo "Enviando mail a ".$res[$row]->usuario;
		}
	$row++;
	}

	// Comprobamos las Menor que
	$SELECT="SELECT * from alertas WHERE ticker='".$ticker."' AND condicion='<=' AND valor>='".$valor."' AND estado='ACTIVA'";
	$res = $db->get_results($SELECT);
	$row=0;
	while (isset($res[$row]->id)) {
		$alertas_email=$db->get_var("SELECT usuario_alertas_email from usuarios where usuario_login='".$res[$row]->usuario."'");
		if ($alertas_email==1) {
			envia_mail($res[$row]->usuario, "[BolsaPHP] Alerta de ".$ticker, "Se ha disparado una alerta programada:\r\n\r\n La alerta estaba programada para avisar al igualar o bajar de  ".$res[$row]->valor." €.\r\n Las acciones de ".$ticker." están actualmente a ".$valor." €.  \r\n \r\n http://bolsaphp.sukiweb.net/index.php?ticker=".$ticker." \r\n\r\n"); 
			$alerta=$db->get_var("UPDATE alertas SET estado='AVISADO' where id='".$res[$row]->id."'");
			echo "Enviando mail a ".$res[$row]->usuario;
		}
	$row++;
	}


}

function grupo_exists($grupo_nombre) {
	global $db;
	$res=$db->get_var("SELECT count(*) FROM grupos WHERE grupo_nombre='$grupo_nombre'");
	if ($res>0) return true;
	return false;
}

function resetea_usuario($usuario) {
	global $db;
	$res=$db->get_var("DELETE FROM `ranking` WHERE `ranking_usuario`='".$usuario."'");
	$res=$db->get_var("DELETE FROM `ordenes` WHERE `usuario`='".$usuario."'");
	$res=$db->get_var("DELETE FROM `carteras` WHERE `usuario`='".$usuario."'");
	$res=$db->get_var("UPDATE `usuarios` SET `usuario_saldo` = '60000', `usuario_karma`='0' WHERE `usuario_login` ='".$usuario."' LIMIT 1 ;");
	logea("Cuenta reseteada", "", $usuario);
}

function aleatorio($precio, $diferencia) {
	$aleatorio=rand(0, 100);
	$precio=($precio+1)+$diferencia;
	if ($aleatorio<=$precio) { return true; } else { return false; }
}

?>
