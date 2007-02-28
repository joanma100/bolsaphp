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

$name = $_POST["n"]; //name from the form in index.html
$text = $_POST["c"];	//comment from the form in index.html

//some weird conversion of the data inputed
$name = str_replace("\'","'",$name);
$name = str_replace("'","\'",$name);
$text = str_replace("\'","'",$text);
$text = str_replace("'","\'",$text);
$text = str_replace("---"," - - ",$text);
$text = str_replace("listado-log-log", "listado log log", $text);
$name = str_replace("---"," - - ",$name);


//the message is cut of after 500 letters
if (strlen($text) > 500) {
	$text = substr($text,0,500); 
}

//to allow for linebreaks a space is inserted every 50 letters
$text = preg_replace("/([^\s]{50})/","$1 ",$text);

//the name is shortened to 30 letters
if (strlen($name) > 30) {
	$name = substr($name, 0,30); 
}

//only if a name and a message have been provides the information is added to the db
if ($name != '' && $text != '') {
	addData($name,$text); //adds new data to the database
	//getID(50); //some database maintenance
}

//adds new data to the database
function addData($name,$text) {
	global $db;	
	logea($text, 'CHAT', $_SESSION["usuario"]);
	$result=$db->get_results("select * from log where log_tipo = 'CHAT' order by log_fecha desc limit 60,10");
	$row=0;
	while (isset($result[$row]->log_id)) {
		$res=$db->get_var("delete from log where log_id= '".$result[$row]->log_id."'");
	$row++;
	}
}



//returns the id of a message at a certain position
function getID($position) {
	$res=$db->get_var("SELECT * FROM log ORDER BY id DESC LIMIT ".$position.",1");
	
	if ($id) {
		//deleteEntries($id); //deletes all message prior to a certain id
	}
}

//deletes all message prior to a certain id
function deleteEntries($id) {
	$sql = 	"DELETE FROM chat WHERE id < ".$id;
	$conn = getDBConnection();
	$results = mysql_query($sql, $conn);
	if (!$results || empty($results)) {
		//echo 'There was an error deletig the entries';
		end;
	}
}
?>