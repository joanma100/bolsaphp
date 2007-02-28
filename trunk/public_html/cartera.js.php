<?php
echo "var url = 'http://bolsaphp.sukiweb.net/cartera.php?usuario=' + bolsaphp_usuario;";
echo "	function write_iframe() {
	var span = document.getElementById(\"bolsaphp\");
	span.innerHTML='<iframe width=\"' + bolsaphp_width +'\" height=\"' + bolsaphp_height +'\" scrolling=\"' + bolsaphp_scrolling + '\" frameborder=\"' + bolsaphp_frameborder +'\" marginwidth=\"0\" marginheight=\"0\" vspace=\"0\" hspace=\"0\" allowtransparency=\"true\" src=\"'+url+'\"></iframe>';
	}
	
	document.write('<span id=\"bolsaphp\"><script type=\"text/javascript\">setTimeout(\"write_iframe()\",	300)</script></span>');
";
