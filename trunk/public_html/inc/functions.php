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
	echo '<head>' . "\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
	echo "<title>".__($config['titulo'])."</title>\n";
	echo '<meta name="generator" content="David Martín :: Suki_ :: ( http://sukiweb.net )" />' . "\n";
	echo '<meta name="keywords" content="'.$config['tags'].'" />' . "\n";
	echo '<style type="text/css" media="screen">@import "'.$config['css'].'";</style>' . "\n";
	echo '<link rel="icon" href="/favicon.ico" type="image/x-icon" />' . "\n";
	
	include ("inc/xmlhttp.js");
	/*
	if ($_GET["login"]=="login") {
		echo '<script><!-- ';
		//echo 'function sf(){document.login.focus();}';
		echo 'function sf() { alert("test"); }';
		echo ' // --></script>';
	}
	*/

	if ($_GET["log"]=="1") {
		echo "<meta http-equiv=\"refresh\" content=\"60\">";
	}
	echo '</head>' . "\n";
	echo '<body id="home" ';
	/*
	if ($_GET["login"]=="login") {
		echo 'onLoad="sf()"';
		}
	*/
	echo '><div id="container">';
}

function pie() {
	//Cierra container div
	
	echo "</div>";
	echo "<div id=\"pie\">Por: <a href=\"http://sukiweb.net\">David Martín :: Suki_ ::</a></div>";

	echo "</body>";
}
/*
function _e($text) {
	//de momento sólo devolvemos el texto.
	//Ya veré como hago lo de las traducciones.
	return $text;
}
*/

function logea($descripcion) {
	global $db;
	$ip=check_ip_behind_proxy();
	$SELECT = "INSERT INTO log (log_usuario_id, log_usuario_login, log_descripcion, log_ip) VALUES ('".$_SESSION["usuario_id"]."', '".$_SESSION["usuario"]."', '".$descripcion."', '".$ip."')";
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
	//$string = strip_tags(trim($string));
	//$string= htmlspecialchars(trim($string));
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
	return __("Hoy")." ".date('H:i', $timestamp);
	elseif($timestamp > mktime(0,0,0) - 86400)
	//since midnight 1 day ago so it's yesterday
	return __("Ayer")." ".date('H:i', $timestamp);
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

?>