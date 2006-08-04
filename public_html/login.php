<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE

require "config.php";

include_once "inc/ez_sql_core.php";
include_once "inc/ez_sql_mysql.php";
require "inc/functions.php";
require "inc/plugfunctions.php";
require "inc/check_behind_proxy.php";
require "inc/l10n.php";

$db = new ezSQL_mysql($config['db_user'],$config['db_password'],$config['db_database'],$config['db_server']);

load_default_textdomain();
session_start();



if ($_GET["login"]=="logout") {
	logea("logout");
	$SELECT = "SELECT * FROM usuarios "
	."WHERE usuario_login='".$_SESSION['usuario']."' "
	."AND usuario_id='".$_SESSION['usuario_id']."'";
	$result = $db->get_results($SELECT);
	$strCookie=base64_encode(join(':',
					array(
						$result[0]->usuario_login,
						md5($config['domain'].$result[0]->usuario_login.$result[0]->usuario_id.$result[0]->usuario_email)
						)
					)
				);
	
	
	
	setcookie("bolsaPHP", $strCookie, time() - 3600);
	unset($_SESSION['usuario']);
	unset($_SESSION['nombre']);
	unset($_SESSION['email']);
	unset($_SESSION['url']);
	unset($_SESSION['usuario_id']);
	header("Cache-Control: no-cache, must-revalidate");
		header("Location: index.php");
		header("Expires: " . gmdate("r", time()-3600));
		header("ETag: \"logingout" . time(). "\"");
		die;

} else if  ( $_POST[login] && $_POST[password] ) {
	$SELECT = "SELECT * FROM usuarios "
	."WHERE usuario_login='".$_POST[login]."' "
	."AND usuario_password='".md5($_POST[password])."'";

	
	$result = $db->get_results($SELECT);
	
	if ( $result[0]->usuario_login )
	{
		
		$_SESSION["usuario"] = $result[0]->usuario_login;
		$_SESSION["nombre"] = $result[0]->usuario_nombre;
		$_SESSION["email"] = $result[0]->usuario_email;
		$_SESSION["url"] = $result[0]->usuario_url;
		$_SESSION["usuario_id"] = $result[0]->usuario_id;
		

		$strCookie=base64_encode(join(':',
					array(
						$result[0]->usuario_login,
						md5($config['domain'].$result[0]->usuario_login.$result[0]->usuario_id.$result[0]->usuario_email)
						)
					)
				);
		$time = time() + 3600000; 
		
		setcookie("bolsaPHP", $strCookie, $time);

		logea("login");
		
	} else {
	$mensaje_de_error=__("Algún error en su usuario o su password.");
	}
} 


//Si existe la cookie, la comprobamos y metemos los datos de la sesión...
if(!empty($_COOKIE['bolsaPHP'])  && !$_SESSION["email"]) {
	$userInfo=explode(":", base64_decode($_COOKIE['bolsaPHP']));
	if ($userInfo[0]!="") {
		$SELECT="SELECT usuario_login, usuario_id, usuario_email FROM usuarios WHERE usuario_login='".$userInfo[0]."'";
		$result=$db->get_results($SELECT);
		$test=md5($config['domain'].$result[0]->usuario_login.$result[0]->usuario_id.$result[0]->usuario_email);
		
		//echo "<br />Test=".$test;
		
		if ($test==$userInfo[1] && !$_SESSION["email"]) {
			
		$_SESSION["usuario"] = $result[0]->usuario_login;
		$_SESSION["nombre"] = $result[0]->usuario_nombre;
		$_SESSION["email"] = $result[0]->usuario_email;
		$_SESSION["url"] = $result[0]->usuario_url;
		$_SESSION["usuario_id"] = $result[0]->usuario_id;
			
			$strCookie=base64_encode(join(':',
					array(
						$result[0]->usuario_login,
						md5($config['domain'].$result[0]->usuario_login.$result[0]->usuario_id.$result[0]->usuario_email)
						)
					)
				);

		$time = time() + 3600000; 
		
		setcookie("bolsaPHP", $strCookie, $time);
		
	
		
		//echo "llegamos aqui";	
		//echo "<br />".$_SESSION["usuario"]." = ".$result[0]->usuario_login;
		
		} 

	}	
} 


	// si no se ha logeado, metemos el user anónimo en la sesión.
	if (!$_SESSION["usuario"]) {
			
			$_SESSION["usuario"] = "anonimo";
			$_SESSION["nombre"] = "anonimo";
			//$_SESSION["usuario_id"] = "0";
	}



?>