<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//header
echo "
<body>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
<br>
<h1>Welcome to the Labeling System</h1>
<h2>idea by i3mainz and IEG</h2>
<br>
<div id='left-nav'>";
	
//navigation
include("left-nav.php");

//content
echo "
</div>

<div id='maingrey'>
<br><br><br>
<center>
<h111>Username: <i>$loggedInUser->displayname</i></h111>
<br>
<h111>Email: <i>$loggedInUser->email</i></h111>
<br>
<h111>Role: <i>$loggedInUser->title</i></h111>
<br><br><br>
<a href='http://www.ieg-mainz.de' target='_blank'><img src='models/site-templates/images/ieg.gif' height='200'></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='http://www.i3mainz.hs-mainz.de' target='_blank'><img src='models/site-templates/images/i3mainz-logo.png' height='200'></a>
</center>
<br><br>
<center>
<br>
<b>used GUI*&nbsp;&nbsp;</b></td>
<input id='gui' type='text' size='50' maxlength='256' disabled>
</center>
<br><br><br>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>

<script>var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');</script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});
</script>
