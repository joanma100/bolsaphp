<?php 
include('login.php');
header("Content-type: text/html; charset=utf-8");
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	echo '<head>' . "\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
	echo "<title>".__($config['titulo'])."</title>\n";
	echo '<meta name="generator" content="David Martín :: Suki_ :: ( http://sukiweb.net )" />' . "\n";
	echo '<meta name="keywords" content="'.$config['tags'].'" />' . "\n";
	echo '<style type="text/css" media="screen">@import "'.$config['css'].'";</style>' . "\n";


if (!$_GET[dias]) { $_GET[dias]=5; }
	echo "<div id=\"grafica\">";
	echo "<form name=\"form\" method=\"get\" action=\"grafica.php?ticker=".$_GET[ticker]."\">";
	echo "<input type=\"hidden\" name=\"ticker\" value=\"".$_GET[ticker]."\">";
	
	echo "<select name=\"dias\" OnChange=\"document.form.submit()\">";
	echo "<option name=\"dias\" value=\"1\" "; if ($_GET[dias]==1) { echo "selected"; } echo ">1 Día</option>";
	echo "<option name=\"dias\" value=\"5\" "; if ($_GET[dias]==5) { echo "selected"; } echo ">5 Días</option>";
	echo "<option name=\"dias\" value=\"30\" "; if ($_GET[dias]==30) { echo "selected"; } echo ">1 Mes</option>";
	echo "<option name=\"dias\" value=\"90\" "; if ($_GET[dias]==90) { echo "selected"; } echo ">3 Meses</option>";
	echo "<option name=\"dias\" value=\"180\" "; if ($_GET[dias]==180) { echo "selected"; } echo ">6 Meses</option>";
	echo "</select>";
	
	echo "[<INPUT name=\"mm3\" type=\"checkbox\"  "; if ($_GET[mm3]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> MM3] ";
	echo "[<INPUT name=\"mm10\" type=\"checkbox\"  "; if ($_GET[mm10]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> MM10] ";

	echo "[<INPUT name=\"cierre\" type=\"checkbox\"  "; if ($_GET[cierre]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> Cierre] ";
	echo "[<INPUT name=\"apertura\" type=\"checkbox\"  "; if ($_GET[apertura]) { echo "checked"; } echo " OnClick=\"document.form.submit()\"> Apertura] ";
	//echo "<input type=\"Submit\" value=\"Mostrar\">";
	
	echo "</form>";
	
		//mostramos las imáenes
		echo "<img src=\"chart.php?ticker=".$_GET[ticker]."&dias=".$_GET[dias]."&tam=gran&mm3=".$_GET[mm3]."&mm10=".$_GET[mm10]."&cierre=".$_GET[cierre]."&apertura=".$_GET[apertura]."\" width=\"600\" height=\"200\" alt=\"".$_GET[ticker]."\">";
		echo "<img src=\"volumen.php?ticker=".$_GET[ticker]."&dias=".$_GET[dias]."\" width=\"600\" height=\"100\" alt=\"".$_GET[ticker]."\">";

echo "</div>";
?>