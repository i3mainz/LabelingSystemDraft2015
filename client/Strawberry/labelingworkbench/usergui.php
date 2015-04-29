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
	<h1>User GUI - Select</h1>
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

<info2>
<p align='center'>
<b>Expert: Select User GUI</b>
<br><br>Select your prefered User GUI.
</i>
</p>
</info2>
<center>

<b>Choose GUI: </b>
<p><select id='gui' name='gui' /></p>
<p><input type='button' value='change' id='sendGUI' onclick='sendGUI();'></p>

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
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>

<script>
var guis = []; // hidden values
// read information from triplestore and GUI data
$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	//
	query = SPARQL.guiLabelAndIndentifier;
	query = encodeURIComponent(query);
	// LOAD TEMPLATES
	$.ajax({
		type: 'GET',
		url: Config.SPARQL,
		data: {query: query, format: 'json'},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(output) {
			var bindings = output.results.bindings;
			try {
				bindings = JSON.parse(bindings);
			} catch (e) {
				console.log(e);
			} finally {
			}
			console.log(bindings);
			for (element in bindings) {
				// add endpoint names to select
				var select = document.getElementById("gui");
				var option = document.createElement("option");
				option.text = bindings[element].label.value;
				select.add(option);
				// save gui and attributes in array [label,uri,identifier]
				guis[bindings[element].label.value] = [bindings[element].label.value, bindings[element].gui.value, bindings[element].identifier.value];
				//console.log(guis);
			}
			// LOAD TEMPLATE OF AGENT
			$.ajax({
				type: 'GET',
				url: Config.JSONagent,
				data: {id: user},
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
					for (element in guis) {
						if (guis[element][2] == output.data[0].gui) {
							document.getElementById("gui").value = guis[element][0];
						}
					}
				}
			});
		}
	});
});
// send new information
sendGUI = function() {
	// LOAD TEMPLATE OF AGENT
	$.ajax({
		type: 'GET',
		url: Config.JSONagent,
		data: {id: user},
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
			var update = "";
			update = SPARQLUPDATE.deleteGUIActorConnectionByIdentifiers;
			update = update.replace("$gui",output.data[0].gui);
			update = update.replace("$accountName",user);
			update = encodeURIComponent(update);
			$.ajax({
				type: 'POST',
				url: Config.Update,
				data: {update: update},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				},
				success: function(output) {
					// SET new GUI
					var gui = "";
					// ls:hasGUI
					gui += Config.Instance("agent",user,true);
					gui += Config.Ontology("hasGUI",true);
					gui += Config.Instance("gui",guis[document.getElementById("gui").value][2],true);
					gui += ". ";
					// ls:isGUIfrom
					gui += Config.Instance("gui",guis[document.getElementById("gui").value][2],true);
					gui += Config.Ontology("isGUIof",true);
					gui += Config.Instance("agent",user,true);
					gui += ". ";
					var update = "";
					update = SPARQLUPDATE.insertAgentByIdentifier;
					update = update.replace("$data",gui);
					console.info(update);
					update = encodeURIComponent(update);
					$.ajax({
						type: 'POST',
						url: Config.Update,
						data: {update: update},
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
							console.info("load GUI");
							LS.GUI.loadGUI();
							alert("saved!");
						}
					});
				}
			});
		}
	});
}
</script>
