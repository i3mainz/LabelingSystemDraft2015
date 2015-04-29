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
	<h1>Resource Lookup</h1>
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
look for labels that are linked to a well known URI
<br>
</i>
</p>
</info2>

<br>
<center>
<input id='resource-input' type='text' size='50' value='' />
<input type='button' id='resource-button' value='query resource' onclick='queryResource()'>
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
#resource-input { font-size: 28px; padding: 10px; border: 1px solid #888; margin: 20px 20px; font-family: sans-serif; line-height: 1.6em; width: 600px;}
#resource-button { font-size: 28px; padding: 8px; font-family: sans-serif; line-height: 1.6em; color: #000;}
</style>

<script>

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

function queryResource() {
	query = SPARQL.getLinks;
	query = query.replace('$URI',document.getElementById("resource-input").value);
	console.info(query);
    query = encodeURIComponent(query);
	$.ajax({
		type: 'GET',
		url:  Config.SPARQL,
		data: {query: query, format: 'json'},
		error: function(jqXHR, textStatus, errorThrown) {
			document.getElementById("resource-input").style.backgroundColor = 'red';
			alert(errorThrown);
		},
		success: function(output) {
			try {
				output = JSON.parse(output);
			} catch (e) {
				console.log(e);
			} finally {
			}
			var result_str = "";
			result_str += "<table style='width:80%' border='1'>";
			if (output.results.bindings.length>0) {
				document.getElementById("resource-input").style.backgroundColor = 'lightgreen';
				result_str += "<tr>";
				result_str += "<th>";
				result_str += "label";
				result_str += "</th>";
				result_str += "<th>";
				result_str += "relation";
				result_str += "</th>";
				result_str += "</tr>";
				for (var i = 0; i < output.results.bindings.length; i++) {
					result_str += "<tr>";
					result_str += "<td>";
					result_str += "<a href='"+output.results.bindings[i].label.value+"' target='_blank'>"+output.results.bindings[i].label.value+"</a>";
					result_str += "</td>";
					result_str += "<td>";
					result_str += "<a href='"+output.results.bindings[i].property.value+"' target='_blank'>"+output.results.bindings[i].property.value+"</a>";
					result_str += "</td>";
					result_str += "</tr>";
				}
			} else {
				document.getElementById("resource-input").style.backgroundColor = 'red';
				result_str += "<tr>";
				result_str += "<td>";
				result_str += "<b>no entry</b>";
				result_str += "</td>";
				result_str += "</tr>";
			}
			result_str += "</table>";
			result_str += "<br>";
			$('#resultDIV').html('<center>'+result_str+'</center>');
		}
	});
}

</script>