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
	<h1>User GUI - Modify/DELETE</h1>
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
<b>Expert: Modify or delete your User GUI</b>
<br><br>Modify or delete your User GUI.
</i>
</p>
</info2>
<center>

<b>Choose GUI: </b>
<p><select id='gui' name='gui' /></p>
<p><input type='button' value='modify' id='modifyGUI' onclick='modifyGUI();'><input type='button' value='delete' id='deleteGUI' onclick='deleteGUI();'></p>

<p id='elements'></p>

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
	query = SPARQL.guiLabelAndIndentifierByCreator;
	query = query.replace("$creator",user);
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
			//console.log(bindings);
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
			// LOAD TEMPLATES OF AGENT
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
// get information
modifyGUI = function() {
	// LOAD SELECTED GUI INFO
	$.ajax({
		type: 'GET',
		url: Config.JSONgui,
		data: {id: guis[document.getElementById("gui").value][2]},
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
			// if gui creator = login user
			if (user==output.data[0].creator) {
				document.getElementById("elements").innerHTML = "<center><b>Change Parameter</b><br><br><b>menulang</b>&nbsp;&nbsp;&nbsp;<select id='menulang' name='menulang' /></select><br><br><b>preflang</b>&nbsp;&nbsp;&nbsp;<select id='preflang' name='preflang' /></select><br><br><input type='button' value='change' id='changeGUI' onclick='changeGUI();'></center>";
				// menu language
				var select = document.getElementById("menulang");
				var option = document.createElement("option");
				option.text = "english";
				select.add(option);
				var option = document.createElement("option");
				option.text = "deutsch";
				select.add(option);
				document.getElementById("menulang").value = output.data[0].menuLang;
				// preferenced language
				var select = document.getElementById("preflang");
				var option = document.createElement("option");
				option.text = "english";
				select.add(option);
				var option = document.createElement("option");
				option.text = "deutsch";
				select.add(option);
				var option = document.createElement("option");
				option.text = "français";
				select.add(option);
				var option = document.createElement("option");
				option.text = "español";
				select.add(option);
				var option = document.createElement("option");
				option.text = "italiano";
				select.add(option);
				document.getElementById("preflang").value = output.data[0].prefLang;
			} else {
				document.getElementById("elements").innerHTML = "<center><h2>Not possible to change</h2></center>";
			}
		}
	});
}
// change information
changeGUI = function() {
	update = SPARQLUPDATE.deleteGUIPropertiesByIdentifiers;
	update = update.replace("$identifier",guis[document.getElementById("gui").value][2]);
	update = encodeURIComponent(update);
	$.ajax({
		type: 'POST',
		url: Config.Update,
		data: {update: update},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(output) {
			data = "";
			data += "<http://143.93.114.137/gui#" + guis[document.getElementById("gui").value][2] + "> <http://143.93.114.137/vocab#GUIprefLang> " + "'" + document.getElementById("preflang").value + "' .";
			data += "<http://143.93.114.137/gui#" + guis[document.getElementById("gui").value][2] + "> <http://143.93.114.137/vocab#GUImenuLang> " + "'" + document.getElementById("menulang").value + "' .";
			update = SPARQLUPDATE.insertGUIPropertiesByIdentifier;
			update = update.replace("$data",data);
			update = encodeURIComponent(update);
			$.ajax({
				type: 'POST',
				url: Config.Update,
				data: {update: update},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				},
				success: function(output) {
					console.info("load GUI");
					LS.GUI.loadGUI();
					alert("change sucessfully done");
				}
			});
		}
	});
}
// delete GUI
deleteGUI = function() {
	var x;
	var r=confirm("Delete GUI " + document.getElementById("gui").value + " ?");
	if (r)
	{
		var update = "";
		update = SPARQLUPDATE.deleteGUIByIdentifier;
		update = update.replace("$identifier",guis[document.getElementById("gui").value][2]);
		update = encodeURIComponent(update);
		$.ajax({
			type: 'POST',
			url: Config.Update,
			data: {update: update},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			success: function(output) {
				document.getElementById("gui").options.length = 0;
				query = SPARQL.guiLabelAndIndentifierByCreator;
				query = query.replace("$creator",user);
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
						// LOAD TEMPLATES OF AGENT
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
			}
		});
	}
}
</script>
