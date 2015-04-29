<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header_geonames.php");

//header
echo "
<body onload='initGeonames()'>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Geonames Lookup</h1>
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

<info2>
<p align='center'>
<b>Notes</b>
<br><br>
geonames autocomplete (jeoQuery)<br>
<br>
</p>
</info2>

<br>
<center>
<input class='left' name='location' id='location' />
<br><br>
<div id='resultDIV'></div>
<br><br>
</center>
<br>

</div>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>

<script>var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');</script>
<script>
$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

var setCity = function(city) {
	console.info(city);
	var result_str = "";
	result_str += '<table style=\"width:80%\" border=\"1\">';
	result_str += '<tr>';
	result_str += '<td>';
	result_str += "<b><a href='http://sws.geonames.org/"+city.geonameId+"' target=\"_blank\">http://sws.geonames.org/"+city.geonameId+"</a></b>";
	result_str += '</td>';
	result_str += '</tr>';
	result_str += '<tr>';
	result_str += '<td>';
	result_str += "<b><a href='http://sws.geonames.org/"+city.geonameId+"/about.rdf' target=\"_blank\">http://sws.geonames.org/"+city.geonameId+"/about.rdf</a></b>";
	result_str += '</td>';
	result_str += '</tr>';
	result_str += '</table>';
	result_str += '<br>';
	$('#resultDIV').html('<center>'+result_str+'</center>');
};
</script>