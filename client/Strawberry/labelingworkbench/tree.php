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
	<h1>My Project Tree</h1>
	<h2>User Function</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

//content
echo "
</div>
<div id='maingrey'>
<br>

<center>
<b>Show the Project Tree with Projects, Vocabularies, Labels and connected Concepts in a new window.</b>
<br>
<b>To open/close the tree, click on the nodes. To get information to a node just right click.</b>
<br>
<b>If you want to repaint the tree with other dimensions or change the user, use the app on the top right side.</b>
<br><br>
<h2><a id='L1' href='http://143.93.114.137/ProjectTree/tree.jsp?height=700&width=2000&name=$loggedInUser->displayname' target='_blank'>My Project Tree</a></h2>
<br><br>
<a id='L2' href='http://143.93.114.137/ProjectTree/tree.jsp?height=700&width=2000&name=$loggedInUser->displayname' target='_blank'><img src='tree_example.png' width='900' style='box-shadow: 10px 10px 10px grey;'></a>
<br><br>
</center>

</div>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');
$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});
function setLink(id, href) {
		var host = document.location.origin;
		return document.getElementById(id).href = host+href+user;
	}
setLink("L1","/ProjectTree/tree.jsp?height=700&width=2000&name=");
setLink("L2","/ProjectTree/tree.jsp?height=700&width=2000&name=");

</script>