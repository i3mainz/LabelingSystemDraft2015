<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Prevent the user visiting the logged in page if he is not logged in
if(!isUserLoggedIn()) { header("Location: login.php"); die(); }

require_once("models/header.php");

//header
echo "
<body>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Documentation</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

echo "
</div>
<div id='maingrey'>";

echo "
<br>
<b>FAQ:</b>
<br>
<ul>
<li><a href='http://labeling.i3mainz.hs-mainz.de/faq' target='_blank'>FAQ - frequently asked questions</a></li>
</ul>
<br>
<b>Download GUI User Manual as PDF:</b>
<br>
<ul>
<li><a href='http://labeling.i3mainz.fh-mainz.de/share/_ls_doku_v13.pdf' target='_blank'>Version 1.3 (24/03/2015)</a></li>
<li><a href='http://labeling.i3mainz.fh-mainz.de/share/_ls_doku_v12.pdf' target='_blank'>Version 1.2 (23/07/2014)</a></li>
<li><a href='http://labeling.i3mainz.fh-mainz.de/share/_ls_doku_v11.pdf' target='_blank'>Version 1.1 (21/05/2014)</a></li>
<li><a href='http://labeling.i3mainz.fh-mainz.de/share/_ls_doku_v10.pdf' target='_blank'>Version 1.0 (01/04/2014)</a></li>
</ul>
<br>
<b>Applications</b>
<br>
<ul>
<li><a href='http://labeling.i3mainz.hs-mainz.de/rest-api' target='_blank'>REST API</a></li>
<li><a href='http://labeling.i3mainz.hs-mainz.de/sparql-api' target='_blank'>SPARQL API</a></li>
<li><a href='http://labeling.i3mainz.hs-mainz.de/technology' target='_blank'>Technology</a></li>
</ul>
<b>Vocabulary and Ontology Usage</b>
<br>
<ul>
<li><a href='http://labeling.i3mainz.hs-mainz.de/vocab/' target='_blank'>Vocabulary</a></li>
<li><a href='http://labeling.i3mainz.hs-mainz.de/ontology' target='_blank'>Ontology Usage</a></li>
</ul>
<br>


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
<script src="config.js"></script>
<script>
$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});
</script>