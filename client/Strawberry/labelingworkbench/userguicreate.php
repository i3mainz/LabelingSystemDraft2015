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
	<h1>User GUI - Create new GUI</h1>
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
<b>Expert: Create new GUI</b>
<br><br>Create a new GUI with your properties.
</i>
</p>
</info2>
<center>

<b>Choose properties: </b>
<p><b>label*&nbsp;&nbsp;&nbsp;</b><input id='label' type='text' size='50' maxlength='50' onkeyup='fieldCheck()'></p>
<p><b>comment*&nbsp;&nbsp;&nbsp;</b><input id='comment' type='text' size='50' maxlength='50' onkeyup='fieldCheck()'></p>
<p><b>prefLang*&nbsp;&nbsp;&nbsp;</b><select id='prefLang' name='prefLang'><option value='english'>english</option><option value='deutsch'>deutsch</option><option value='français'>français</option><option value='español'>español</option><option value='italiano'>italiano</option></select></p>
<p><b>menuLang*&nbsp;&nbsp;&nbsp;</b><select id='menuLang' name='menuLang'><option value='english'>english</option><option value='deutsch'>deutsch</option></select></p>
<p><input type='button' value='create' id='sendGUI' onclick='sendGUI();'></p>

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
	fieldCheck();
});
// send new information
sendGUI = function() {
	$.ajax({
		type: 'GET',
		url: Config.UUID,
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(uuid) {
			var gui = "";
			// ls:GUI
			gui += Config.Instance("gui",uuid,true);
			gui += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
			gui += Config.Ontology("GUI",true);
			gui += ". ";
			// ls:identifier
			gui += Config.Instance("gui",uuid,true);
			gui += Config.Ontology("identifier",true);
			gui += "\""+uuid+"\" ";
			gui += ". ";
			// ls:sameAs
			gui += Config.Instance("gui",uuid,true);
			gui += Config.Ontology("sameAs",true);
			gui += "<"+Config.Rest_GUIS+uuid+"> ";
			gui += ". ";
			// rdfs:label
			gui += Config.Instance("gui",uuid,true);
			gui += "<http://www.w3.org/2000/01/rdf-schema#label> ";
			gui += "\""+document.getElementById('label').value+"\" ";
			gui += ". ";
			// rdfs:comment
			gui += Config.Instance("gui",uuid,true);
			gui += "<http://www.w3.org/2000/01/rdf-schema#comment> ";
			gui += "\""+document.getElementById('comment').value+"\" ";
			gui += ". ";
			// ls:GUIcreator
			gui += Config.Instance("gui",uuid,true);
			gui += Config.Ontology("GUIcreator",true);
			gui += "\""+user+"\" ";
			gui += ". ";
			//properties
			// foaf:lastName
			// ls:GUImenuLang
			gui += Config.Instance("gui",uuid,true);
			gui += Config.Ontology("GUImenuLang",true);
			gui += "\""+document.getElementById('menuLang').value+"\" ";
			gui += ". ";
			// ls:GUIprefLang
			gui += Config.Instance("gui",uuid,true);
			gui += Config.Ontology("GUIprefLang",true);
			gui += "\""+document.getElementById('prefLang').value+"\" ";
			gui += ". ";
			var update = "";
			update = SPARQLUPDATE.insertGUIPropertiesByIdentifier;
			update = update.replace("$data",gui);
			update = encodeURIComponent(update);
			$.ajax({
				type: 'POST',
				url: Config.Update,
				data: {update: update},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown);
				},
				success: function(output) {
					alert("saved!");
				}
			});
		}
	});
}
function fieldCheck() {
	var l = false;
	var c = false;
	if (document.getElementById("label").value != "") {
		document.getElementById("label").style.backgroundColor = '#C1FFC1'; //green
		l = true;
    } else {
		document.getElementById("label").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById("comment").value != "") {
		document.getElementById("comment").style.backgroundColor = '#C1FFC1'; //green
		c = true;
    } else {
		document.getElementById("comment").style.backgroundColor = '#EEA9B8'; //red
    }
	if (l && c) {
		document.getElementById("sendGUI").style.visibility = 'visible';
	 } else {
		document.getElementById("sendGUI").style.visibility = 'hidden';
	 }
}
</script>
