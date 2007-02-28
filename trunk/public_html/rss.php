<?php
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// David Martín :: Suki_ :: <david at sukiweb dot net>.
// GNU GENERAL PUBLIC LICENSE
require("login.php");
$fecha=date('D, d M Y H:i:s +0000');
header('Content-type: text/xml; charset=UTF-8, true');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";;
echo "<!-- generator=\"bolsaphp.sukiweb.net\" -->\r\n";
echo "<rss version=\"2.0\">\r\n";
echo "<channel>\r\n";
echo "<title>BolsaPHP ";
if ($_GET["usuario"]) { echo "- Cartera de ".$_GET["usuario"]; }
if ($_GET["ticker"]) { echo "- ".empresa_ticker($_GET["ticker"]); }
echo "</title>\r\n";
echo "<link>http://bolsaphp.sukiweb.net</link>\r\n";
echo "<description>David Martín :: Suki_ ::</description>\r\n";
echo "<pubDate>".$fecha."</pubDate>\r\n";
echo "<generator>http://bolsaphp.sukiweb.net</generator>\r\n";
echo "<language>es</language>\r\n";

if ($_GET["ticker"]) {
	$SELECT= "SELECT * from quotes WHERE ticker='".$_GET["ticker"]."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
	$result = $db->get_results($SELECT);
	
	if ($result[0]->id) {
		// Item
		echo "<item>\r\n";
		echo "<title>".empresa_ticker($result[0]->ticker)."</title>\r\n";
		echo "<link>http://bolsaphp.sukiweb.net/index.php?ticker=".$result[0]->ticker."</link>\r\n";
		echo "<pubDate>".$result[0]->fecha." ".$result[0]->hora."</pubDate>\r\n";
		echo "<category>IBEX35</category>\r\n";
		echo "<description><![CDATA[";
		echo '<a href="http://bolsaphp.sukiweb.net/index.php?ticker='.$result[0]->ticker.'" target="_top">';
		echo "<img src=\"http://bolsaphp.sukiweb.net/chart.php?ticker=".$result[0]->ticker."\" border=\"0\">";
		echo "</a>";
		echo "<p>".empresa_ticker($result[0]->ticker)." (".$result[0]->ticker.") ";
		echo number_format($result[0]->valor, 2, ",", ".")." € </p>";
		echo "]]></description>\r\n";
		echo "</item>\r\n";
	}
	
} else if ($_GET["usuario"]) {
	$SELECT="SELECT * FROM carteras WHERE usuario='".$_GET["usuario"]."' AND acciones>='1' ORDER BY ticker";
	$result = $db->get_results($SELECT);
	$row=0;
	while (isset($result[$row]->id)) {
		echo "<item>\r\n";
		echo "<title>".empresa_ticker($result[$row]->ticker)."</title>\r\n";
		echo "<link>http://bolsaphp.sukiweb.net/index.php?ticker=".$result[$row]->ticker."</link>\r\n";
		//echo "<pubDate>".$result[0]->fecha." ".$result[0]->hora."</pubDate>\r\n";
		echo "<category>IBEX35</category>\r\n";
		echo "<description><![CDATA[";
		echo '<a href="http://bolsaphp.sukiweb.net/index.php?ticker='.$result[$row]->ticker.'" target="_top">';
		echo "<img src=\"http://bolsaphp.sukiweb.net/chart.php?ticker=".$result[$row]->ticker."\" border=\"0\">";
		echo "</a>";
		echo "<p>".empresa_ticker($result[$row]->ticker)." (".$result[$row]->ticker.") ";
		echo "Invertidos ".number_format($result[$row]->saldo, 2, ",", ".")." € (".number_format($result[$row]->acciones, 2, ",", ".")." Acciones) </p>";
		echo "]]></description>\r\n";
		echo "</item>\r\n";

	$row++;
	}	


} else {
	$SELECT="SELECT ticker FROM valores ";
	$valores = $db->get_results($SELECT);
	$row=0;
	while (isset($valores[$row]->ticker)) {
	
		$SELECT= "SELECT *, UNIX_TIMESTAMP(timestamp) as timestamp_unix from quotes WHERE ticker='".$valores[$row]->ticker."' ORDER BY 'timestamp' DESC LIMIT 0 , 1 ";
		$result = $db->get_results($SELECT);
			// Item
			echo "<item>\r\n";
			echo "<title>".empresa_ticker($result[0]->ticker)." ".number_format($result[0]->valor, 2, ",", ".")." €</title>\r\n";
			echo "<link>http://bolsaphp.sukiweb.net/index.php?ticker=".$result[0]->ticker."</link>\r\n";
			echo "<pubDate>".$result[0]->fecha." ".$result[0]->hora."</pubDate>\r\n";
			echo "<category>IBEX35</category>\r\n";
			echo "<description><![CDATA[";
			echo '<a href="http://bolsaphp.sukiweb.net/index.php?ticker='.$result[0]->ticker.'" target="_top">';
			echo "<img src=\"http://bolsaphp.sukiweb.net/chart.php?ticker=".$result[0]->ticker."\" border=\"0\">";
			echo "</a>";
			echo "<p>".empresa_ticker($result[0]->ticker)." (".$result[0]->ticker.") ";
			echo number_format($result[0]->valor, 2, ",", ".")." € </p>";
			echo "]]></description>\r\n";
			echo "</item>\r\n";
	$row++;	
	}
}

echo "</channel>\r\n";
echo "</rss>\r\n";

?>