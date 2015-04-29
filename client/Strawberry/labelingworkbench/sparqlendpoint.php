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
	<h1>Query SKOS Concepts from the Web</h1>
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
Here you can explore stored thesauri of different providers.
<br>
You can have a look at the concepts where a substring is included.
<br>
Therefore the prefLabels are browsed via a SPARQL endpoint.
<br>
Be careful because of the licenses of the different providers.
<br>
</i>
</p>
</info2>

<center>

<br>
<b>SubString in prefLabel: </b><input type='text' name='searchstr' id='searchstr' value='undefined' size='50'>
<br><br>
<b>Choose SPARQL Endpoint: </b><select id='endpoint' name='endpoint'>
<br><br>
<input type='button' value='Query Thesaurus' onclick='queryThesaurus()'>
<br><br>
</center>
</div>

<p>&nbsp;</p>
<div id='sparqlgrey'>
</div>

</div>";

//footer
echo "
</body>
</html>";


?>

<?php

function js_str($s)
{
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}
function js_array($array)
{
    $temp = array_map('js_str', $array);
    return '[' . implode(',', $temp) . ']';
}

?>

<?php
	
	include('config.php');

?>

<script>var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');</script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>
<script src="utils.js"></script>

<script>

var sparqlendpoints = [];
var sparqlresults;

IO = {};

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	//
	$.ajax({
        type: 'GET',
        url: Config.SPARQL,
        data: {query: SPARQL.sparqlendpoint, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
			console.error(errorThrown);
			alert(errorThrown);
        },
        success: function(json) {
			var bindings = json.results.bindings; 
			console.info(bindings);
			for (element in bindings) {
				// add endpoint names to select
				var select = document.getElementById("endpoint");
				var option = document.createElement("option");
				option.text = bindings[element].name.value;
				select.add(option);
				// save sparql endpoint and attributes in array [name,uri,query]
				sparqlendpoints[bindings[element].name.value] = [bindings[element].name.value, bindings[element].uri.value, bindings[element].query.value];
			}
			document.getElementById("endpoint").value = "Labeling System";
			setHiddenValues();
		}
    });
});

function setHiddenValues() {
	//var endpointname = document.getElementById("endpoint").value;
	//console.info(sparqlendpoints[endpointname]);
	//document.getElementById('sparql_name').value = sparqlendpoints[endpointname][0];
	//document.getElementById('sparql_url').value = sparqlendpoints[endpointname][1];
	//document.getElementById('sparql_query').value = sparqlendpoints[endpointname][2];
}

function queryThesaurus() {
	
	var endpointname = document.getElementById("endpoint").value;
	console.info(sparqlendpoints[endpointname]);
	var searchstr = document.getElementById('searchstr').value;
	var sparql_name = sparqlendpoints[endpointname][0];
	var sparql_url = sparqlendpoints[endpointname][1];
	var sparql_query = sparqlendpoints[endpointname][2];
	
	$.ajax({
        type: 'POST',
        url: 'GET_SPARQL_RESULTS.php',
        data: {searchstr: searchstr, sparql_name: sparql_name, sparql_url: sparql_url, sparql_query: sparql_query},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
			try {
				output = JSON.parse(output);
			} catch (e) {
				console.log(e);
			} finally {
			}
			
			sparqlresults = output;
			
			var sparqlgrey_header = "<br><center>";
			var sparqlgrey_footer = "</center><br>";
			var sparqlgrey_content = "";
			
			sparqlgrey_content += "<h1>results:</h1><br>";
			
			sparqlgrey_content += "<table border='1' align='center'><tr><th>label</th><th>concept</th><th>scheme</th><th>select</th></tr>";
			
			for (var i=0; i<output.length; i++) {       
				var line = "<tr>";
				line += "<td>"+output[i].label+"</td>";
				line += "<td><a href='"+output[i].concept+"' target='_blank'>Link</a></td>";
				line += "<td><a href='"+output[i].scheme+"' target='_blank'>Link</a></td>";
				line += "<td><a href='javaScript:selectLabel("+i+");'>SELECT</a></td>";
				line += "</tr>";
				sparqlgrey_content += line;
			}
			
			sparqlgrey_content += "</table>";
			
			sparqlgrey_content += "<br><br><b>selected value:</b><br><input id='label-sel' type='text' size='100' value='undefined' disabled /><br>";
			sparqlgrey_content += "<input id='concept-sel' type='text' size='100' value='undefined' disabled /><br><br>";
			sparqlgrey_content += "<b>label:</b><br><select id='labellist1' size='10' style='width: 500px;'></select><br><br>";
			
			sparqlgrey_content += "<h1>Connect Label and Concept</h1><span id='connect_lc'><input type='button' value='Connect Label with Concept' id='sendvocabulary' onclick='IO.setLabelConnection();'></span>skos:<select id='relation'><option value='closeMatch'>closeMatch</option><option value='exactMatch'>exactMatch</option><option value='relatedMatch'>relatedMatch</option><option value='narrowMatch'>narrowMatch</option><option value='broadMatch'>broadMatch</option></select></center><br>";
			
			document.getElementById("sparqlgrey").innerHTML = sparqlgrey_header+sparqlgrey_content+sparqlgrey_footer;
			
			document.getElementById("label-sel").value = sparqlresults[0].label;
			document.getElementById("concept-sel").value = sparqlresults[0].concept;
			
			// get label list
			
			document.getElementById('labellist1').options.length = 0;
			// query labels of user to fill the lists
			query = SPARQL.mylabels;
			query = query.replace('$creator',user);         
			query = encodeURIComponent(query);
			$.ajax({
				type: 'GET',
				url: Config.SPARQL,
				data: {query: query, format: 'json'},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				},
				success: function(output) {
					try {
						output = JSON.parse(output);
					} catch (e) {
						console.log(e);
					} finally {
					}
					var bindings = output.results.bindings; 
					for (var i=0; i<bindings.length; i++) {       
						var t = bindings[i];
						var val = "";
						var lang = "";
						for(var key in t.s) {
							if (key == "value") {
								val = t.s.value;
							}
							if (key == "xml:lang") {
								lang = t.s['xml:lang'];
							}		
						}
						var x = document.getElementById("labellist1");
						var option = document.createElement("option");
						option.text = "\"" + val + "\"" + "@" + lang;
						x.add(option);
						val = "";
						lang = "";
					}
				}
			});	
		}
	});
}

function selectLabel(index) {
	document.getElementById("label-sel").value = sparqlresults[index].label;
	document.getElementById("concept-sel").value = sparqlresults[index].concept;
}

IO.setLabelConnection = function() {
	if (document.getElementById('labellist1').value.indexOf("@")>-1 && document.getElementById('concept-sel').value.indexOf("http://")>-1) {
		var update = "";
		update = SPARQLUPDATE.sendrelation;
		update = update.replace("$pl",document.getElementById('labellist1').value);
		update = update.replace("$resource",document.getElementById('concept-sel').value);
		update = update.replace("$relation","http://www.w3.org/2004/02/skos/core#"+document.getElementById('relation').value);
		update = update.replace("$creator","\""+user+"\"");
		update = encodeURIComponent(update);
		$.ajax({
			beforeSend: function(req) {
				req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
			},
			type: 'POST',
			url: Config.Update,
			data: {update: update},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error(errorThrown);
				alert(errorThrown);
			},
			success: function(xml) {
				console.info('triple gespeichert');
				console.info(xml);
			}		
		});	
	} else {
		alert("no content in labellist");
	}
}

</script>

<?php require_once("models/footerline.php"); ?>