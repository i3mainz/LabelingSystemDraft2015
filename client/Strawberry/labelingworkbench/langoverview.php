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
	<h1>Languages</h1>
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
<b>Supported Languages in UTF-8 encoding for prefLabel / note / definition / prefLang</b>
<br>
<ul>
<li>deutsch: <b>de</b></li>
<li>english: <b>en</b></li>
<li>français: <b>fr</b></li>
<li>español: <b>es</b></li>
<li>italiano: <b>it</b></li>
<li>polski: <b>pl</b></li>
<li>deutsch original: <b>de-x-orig</b></li>
<li><a href='http://www.iana.org/assignments/language-subtag-registry/language-subtag-registry' target='_blank'>more official language codes for altLabels</a></li>
</ul>";

$sel = GlobalVariables::getSelectOption("test");
echo "<p>language select example: ".$sel."</p>";

echo "</div>";

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