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
	<h1>DBpedia Lookup</h1>
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
dbpedia keyword lookup
<br>
</i>
</p>
</info2>

<br>
<center>
<input id='dbpedia-input' type='text' size='50' value='' />
<select id='dbpedia-class'>
	<option value=''></option>
	<option value='Place' selected>Place</option>
	<option value='Person'>Person</option>
	<option value='Work'>Work</option>
	<option value='Species'>Species</option>
	<option value='Organisation'>Organisation</option>
</select>
<input type='button' id='dbpedia-button' value='query DBpedia' onclick='queryDBpedia()'>
<br><br>
<div id='resultDIV'></div>
<br><br>
</center>
</span>
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
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script type="text/javascript" src="jquery.autocomplete.js"></script>
<script src="config.js"></script>
<script src="utils.js"></script>

<style type="text/css">
#dbpedia-input { font-size: 28px; padding: 10px; border: 1px solid #888; margin: 20px 20px; font-family: sans-serif; line-height: 1.6em; width: 600px;}
#dbpedia-button { font-size: 28px; padding: 8px; font-family: sans-serif; line-height: 1.6em; color: #000;}
#dbpedia-class { font-size: 24px; padding: 8px; font-family: sans-serif; line-height: 1.6em; color: #000;}
</style>

<script>

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

function queryDBpedia() {
	$.ajax({
		type: 'GET',
		url:  Config.QueryDBpedia,
		data: {QueryClass: document.getElementById("dbpedia-class").value, QueryString: document.getElementById("dbpedia-input").value},
		error: function(jqXHR, textStatus, errorThrown) {
			document.getElementById("dbpedia-input").style.backgroundColor = 'lightred';
			alert(errorThrown);
		},
		success: function(output) {
			//document.getElementById("dbpedia-input").value = "";
			document.getElementById("dbpedia-input").style.backgroundColor = 'lightgreen';
			var result_str = "";
			for (var i = 0; i < output.results.length; i++) {
				result_str += "<table style='width:80%' border='1'>";
				result_str += "<tr>";
				result_str += "<td style='background-color:#FFE4E1'>";
				result_str += "<b><a href='"+output.results[i].URI+"' target='_blank'>"+output.results[i].URI+"</a></b>";
				result_str += "</td>";
				result_str += "</tr>";
				result_str += "<tr>";
				result_str += "<td style='background-color:#FFE4E1'>";
				result_str += "<a href='"+output.results[i].wiki+"' target='_blank'>"+output.results[i].wiki+"</a>";
				result_str += "</td>";
				result_str += "</tr>";
				for (var j = 0; j < output.results[i].labels.length; j++) {
					result_str += "<tr>";
					result_str += "<td style='background-color:#AFEEEE'>";
					result_str += output.results[i].labels[j].label;
					result_str += "</td>";
					result_str += "</tr>";
				}
				for (var j = 0; j < output.results[i].comments.length; j++) {
					result_str += "<tr>";
					result_str += "<td style='background-color:#C1FFC1'>";
					result_str += "<i>"+output.results[i].comments[j].comment+"</i>";
					result_str += "</td>";
					result_str += "</tr>";
				}
				result_str += "</table>";
				result_str += "<br>";
			}
			$('#resultDIV').html('<center>'+result_str+'</center>');
		}
	});
}

</script>