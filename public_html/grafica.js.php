<?php
echo "var url = 'grafica.php?ticker=".$_GET["ticker"]."';";
echo "	function write_iframe() {
	var span = document.getElementById(\"bolsaphp\");
	span.innerHTML='<iframe width=\"100%\" height=\"350\" scrolling=\"no\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" vspace=\"0\" hspace=\"0\" allowtransparency=\"true\" src=\"'+url+'\"></iframe>';
	}
	
	document.write('<span id=\"bolsaphp\" style=\"width: 100%; height: 350px;border: none; padding: 0; margin: 0; background: transparent; \"><script type=\"text/javascript\">setTimeout(\"write_iframe()\",	300)</script></span>');
";
