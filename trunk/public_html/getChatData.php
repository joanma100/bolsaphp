<?
/* 
XHTML live Chat
author: alexander kohlhofer
version: 1.0
http://www.plasticshore.com
http://www.plasticshore.com/projects/chat/
please let the author know if you put any of this to use
XHTML live Chat (including this code) is published under a creative commons license
license: http://creativecommons.org/licenses/by-nc-sa/2.0/
*/
require("login.php");

//Headers are sent to prevent browsers from caching.. IE is still resistent sometimes
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header( "Cache-Control: no-cache, must-revalidate" ); 
header( "Pragma: no-cache" );
header("Content-Type: text/html; charset=utf-8");

//if the request does not provide the id of the last know message the id is set to 0
if (!$_GET["lastID"]) {
	$lastID = 0;
} else {
	$lastID = $_GET["lastID"];
}




// retrieves all messages with an id greater than $lastID
getData($lastID);

// retrieves all messages with an id greater than $lastID
function getData($lastID) {
	global $db;
	$SELECT= "SELECT *, UNIX_TIMESTAMP(log_fecha) as timestamp_unix from log";
	$SELECT .=" WHERE log_descripcion != 'login' AND log_descripcion != 'logout' ";
	if ($lastID>=0) { $SELECT .=" AND log_id > '".$lastID."' "; } 
	$SELECT .=" ORDER BY log_id DESC LIMIT 0 , 40 ";
	$result = $db->get_results($SELECT);
	
	if ($result) {
		$row=0;
		
		array_multisort($result, SORT_ASC, $result);
		
		while (isset($result[$row]->log_id)) {
			$fecha=timestamp_to_fecha($result[$row]->timestamp_unix);
			echo $result[$row]->log_id." ---";
			echo $result[$row]->log_usuario_login." ---";
			echo $result[$row]->log_descripcion." ---";
			echo $fecha." ---";
			echo $result[$row]->log_tipo." ---";
			// --- is being used to separete the fields in the output
		$row++;
		}
	}

}
?>