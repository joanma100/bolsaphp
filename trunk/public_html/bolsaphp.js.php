<?php
echo "var url = 'http://bolsaphp.sukiweb.net/grafica-externa.php?ticker=".$_GET["ticker"]."';";
echo "	function write_iframe() {
	var span = document.getElementById(\"bolsaphp\");
	span.innerHTML='<iframe width=\"210\" height=\"100\" scrolling=\"no\" frameborder=\"0\" marginwidth=\"0\" marginheight=\"0\" vspace=\"0\" hspace=\"0\" allowtransparency=\"true\" src=\"'+url+'\"></iframe>';
	}
	
	document.write('<span id=\"bolsaphp\" style=\"width: 210px; height: 100px; border: none; padding: 0; margin: 0; background: transparent ; \"><script type=\"text/javascript\">setTimeout(\"write_iframe()\",	200)</script></span>');
";
