<?php 
echo '<a href="http://bolsaphp.sukiweb.net/index.php?ticker='.$_GET[ticker].'" target="_top">';
echo "<img src=\"http://bolsaphp.sukiweb.net/chart.php?ticker=".$_GET[ticker]."\" border=\"0\">";
echo "</a>";
?>