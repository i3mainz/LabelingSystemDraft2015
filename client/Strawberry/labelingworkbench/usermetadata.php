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
	<h1>User Metadata</h1>
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
<b>Expert: Set Agent Metadata</b>
<br><br>You will be modeled as a FOAF web resource (for more information click the link).
</i>
</p>
</info2>
<center>

<table border='0'>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_accountName' target='_blank'>foaf:accountName*</a>&nbsp;&nbsp;</b></td>
	<td><input id='accountName' type='text' size='50' maxlength='256' disabled></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_title' target='_blank'>foaf:title*</a>&nbsp;&nbsp;</b></td>
	<td><select id='title'><option value='Mr.'>Mr.</option><option value='Mrs.'>Mrs.</option><option value='Ms.'>Ms.</option><option value='Dr.'>Dr.</option><option value='Prof.'>Prof.</option></select></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_firstName' target='_blank'>foaf:firstName*</a>&nbsp;&nbsp;</b></td>
	<td><input id='firstName' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_lastName' target='_blank'>foaf:lastName*</a>&nbsp;&nbsp;</b></td>
	<td><input id='lastName' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_mbox' target='_blank'>foaf:mbox* [email]</a>&nbsp;&nbsp;</b></td>
	<td><input id='mbox' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_status' target='_blank'>foaf:status [string]</a>&nbsp;&nbsp;</b></td>
	<td><input id='status' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_topic_interest' target='_blank'>foaf:topic_interest [web]</a>&nbsp;&nbsp;</b></td>
	<td><input id='topic_interest' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_workplaceHomepage' target='_blank'>foaf:workplaceHomepage [web]</a>&nbsp;&nbsp;</b></td>
	<td><input id='workplaceHomepage' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_workInfoHomepage' target='_blank'>foaf:workInfoHomepage [web]</a>&nbsp;&nbsp;</b></td>
	<td><input id='workInfoHomepage' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td><b><a href='http://xmlns.com/foaf/spec/#term_homepage' target='_blank'>foaf:homepage [web]</a>&nbsp;&nbsp;</b></td>
	<td><input id='homepage' type='text' size='50' maxlength='256' onkeyup='fieldCheck()'></td>
</tr>
<tr>
	<td></td>
	<td><input type='button' value='modify' id='sendAgent' onclick='sendAgent();'></td>
</tr>
</table>
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
document.getElementById("accountName").value = user;
// read information from triplestore and GUI data
$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	getAgent();
});
// send new information
sendAgent = function() {
	update = SPARQLUPDATE.deleteAgentProperties;
	update = update.replace("$user",user);
	update = encodeURIComponent(update);
	$.ajax({
		type: 'POST',
		url: Config.Update,
		data: {update: update},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(output) {
			if (document.getElementById('firstName').value == "" || document.getElementById('lastName').value == "" || document.getElementById('mbox').value == "" || document.getElementById('mbox').value.indexOf("@") == -1 || (document.getElementById('topic_interest').value != "" && document.getElementById('topic_interest').value.substring(0,7) != "http://") || (document.getElementById('workplaceHomepage').value != "" && document.getElementById('workplaceHomepage').value.substring(0,7) != "http://") || (document.getElementById('workInfoHomepage').value != "" && document.getElementById('workInfoHomepage').value.substring(0,7) != "http://") || (document.getElementById('homepage').value != "" && document.getElementById('homepage').value.substring(0,7) != "http://") ) {
				console.error("wrong input");
			} else {
				var agent = "";
				// ls:Agent
				agent += Config.Instance("agent",user,true);
				agent += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
				agent += "<http://xmlns.com/foaf/0.1/Person>" ;
				agent += ". ";
				// foaf:accountName
				agent += Config.Instance("agent",user,true);
				agent += "<http://xmlns.com/foaf/0.1/accountName> ";
				agent += "\""+document.getElementById('accountName').value+"\" ";
				agent += ". ";
				// foaf:title
				agent += Config.Instance("agent",user,true);
				agent += "<http://xmlns.com/foaf/0.1/title> ";
				agent += "\""+document.getElementById('title').value+"\" ";
				agent += ". ";
				// foaf:firstName
				agent += Config.Instance("agent",user,true);
				agent += "<http://xmlns.com/foaf/0.1/firstName> ";
				agent += "\""+document.getElementById('firstName').value+"\" ";
				agent += ". ";
				// foaf:lastName
				agent += Config.Instance("agent",user,true);
				agent += "<http://xmlns.com/foaf/0.1/lastName> ";
				agent += "\""+document.getElementById('lastName').value+"\" ";
				agent += ". ";
				if (document.getElementById('mbox').value.indexOf("@") != -1) {
					// foaf:mbox
					agent += Config.Instance("agent",user,true);
					agent += "<http://xmlns.com/foaf/0.1/mbox> ";
					agent += "<mailto:"+document.getElementById('mbox').value+"> ";
					agent += ". ";
				}
				if (document.getElementById('status').value != "") {
					// foaf:status
					agent += Config.Instance("agent",user,true);
					agent += "<http://xmlns.com/foaf/0.1/status> ";
					agent += "\""+document.getElementById('status').value+"\" ";
					agent += ". ";
				}
				if (document.getElementById('topic_interest').value != "" && document.getElementById('topic_interest').value.substring(0,7) == "http://") {
					// foaf:topic_interest
					agent += Config.Instance("agent",user,true);
					agent += "<http://xmlns.com/foaf/0.1/topic_interest> ";
					agent += "<"+document.getElementById('topic_interest').value+"> ";
					agent += ". ";
				}
				if (document.getElementById('workplaceHomepage').value != "" && document.getElementById('workplaceHomepage').value.substring(0,7) == "http://") {
					// foaf:workplaceHomepage
					agent += Config.Instance("agent",user,true);
					agent += "<http://xmlns.com/foaf/0.1/workplaceHomepage> ";
					agent += "<"+document.getElementById('workplaceHomepage').value+"> ";
					agent += ". ";
				}
				if (document.getElementById('workInfoHomepage').value != "" && document.getElementById('workInfoHomepage').value.substring(0,7) == "http://") {
					// foaf:workInfoHomepage
					agent += Config.Instance("agent",user,true);
					agent += "<http://xmlns.com/foaf/0.1/workInfoHomepage> ";
					agent += "<"+document.getElementById('workInfoHomepage').value+"> ";
					agent += ". ";
				}
				if (document.getElementById('homepage').value != "" && document.getElementById('homepage').value.substring(0,7) == "http://") {
					// foaf:homepage
					agent += Config.Instance("agent",user,true);
					agent += "<http://xmlns.com/foaf/0.1/homepage> ";
					agent += "<"+document.getElementById('homepage').value+"> ";
					agent += ". ";
				}
				// in triplestore
				//<http://143.93.114.137/gui#5436e10616f840859e29c0ab0876114c> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#GUI> .
				//<http://143.93.114.137/gui#5436e10616f840859e29c0ab0876114c> <http://143.93.114.137/vocab#identifier> "5436e10616f840859e29c0ab0876114c" .
				//<http://143.93.114.137/gui#5436e10616f840859e29c0ab0876114c> <http://www.w3.org/2000/01/rdf-schema#label> "default" .
				//<http://143.93.114.137/gui#5436e10616f840859e29c0ab0876114c> <http://www.w3.org/2000/01/rdf-schema#comment> "Default GUI" .
				//<http://143.93.114.137/gui#5436e10616f840859e29c0ab0876114c> <http://143.93.114.137/vocab#sameAs> <http://labeling.i3mainz.hs-mainz.de/rest/guis/5436e10616f840859e29c0ab0876114c> .
				// connect with default gui
				// ls:hasGUI
				//agent += Config.Instance("agent",user,true);
				//agent += Config.Ontology("hasGUI",true);
				//agent += Config.Instance("gui","5436e10616f840859e29c0ab0876114c",true);
				//agent += ". ";
				// ls:isGUIfrom
				//agent += Config.Instance("gui","5436e10616f840859e29c0ab0876114c",true);
				//agent += Config.Ontology("isGUIof",true);
				//agent += Config.Instance("agent",user,true);
				//agent += ". ";
				var update = "";
				update = SPARQLUPDATE.insertAgentByIdentifier;
				update = update.replace("$data",agent);
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
						getAgent();
						alert("saved!");
					}
				});
			}
		}
	});
}
// read information from triplestore and GUI data
getAgent = function() {
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
			// SET fields from Triplestore
			document.getElementById("title").value = output.data[0].title;
			document.getElementById("firstName").value = output.data[0].firstName;
			document.getElementById("lastName").value = output.data[0].lastName;
			document.getElementById("mbox").value = output.data[0].mbox.replace("mailto:","");
			document.getElementById("status").value = output.data[0].status;
			document.getElementById("topic_interest").value = output.data[0].topic_interest;
			document.getElementById("workplaceHomepage").value = output.data[0].workplaceHomepage;
			document.getElementById("workInfoHomepage").value = output.data[0].workInfoHomepage;
			document.getElementById("homepage").value = output.data[0].homepage;
			fieldCheck();
			// LOAD GUI
			/*$.ajax({
				type: 'GET',
				url: Config.JSONgui,
				data: {id: output.data[0].gui},
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
					document.getElementById("gui").value = output.data[0].label;
					document.getElementById("guicomment").value = output.data[0].comment;
				}
			});*/
		}
	});
}

function fieldCheck() {
	var f = false;
	var l = false;
	var m = false;
	var s = true;
	var ti = false;
	var we = false;
	var wih = false;
	var h = false;
	document.getElementById("status").style.backgroundColor = '#C1FFC1'; //green
	if (document.getElementById("firstName").value != "") {
		document.getElementById("firstName").style.backgroundColor = '#C1FFC1'; //green
		f = true;
    } else {
		document.getElementById("firstName").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById("lastName").value != "") {
		document.getElementById("lastName").style.backgroundColor = '#C1FFC1'; //green
		l = true;
    } else {
		document.getElementById("lastName").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById("mbox").value != "" && document.getElementById('mbox').value.indexOf("@") != -1) {
		document.getElementById("mbox").style.backgroundColor = '#C1FFC1'; //green
		m = true;
	} else {
		document.getElementById("mbox").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById('topic_interest').value == "" || document.getElementById('topic_interest').value.substring(0,7) == "http://") {
		document.getElementById("topic_interest").style.backgroundColor = '#C1FFC1'; //green
		ti = true;
    } else {
		document.getElementById("topic_interest").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById('workInfoHomepage').value == "" || document.getElementById('workInfoHomepage').value.substring(0,7) == "http://") {
		document.getElementById("workInfoHomepage").style.backgroundColor = '#C1FFC1'; //green
		wih = true;
    } else {
		document.getElementById("workInfoHomepage").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById('workplaceHomepage').value == "" || document.getElementById('workplaceHomepage').value.substring(0,7) == "http://") {
		document.getElementById("workplaceHomepage").style.backgroundColor = '#C1FFC1'; //green
		wh = true;
    } else {
		document.getElementById("workplaceHomepage").style.backgroundColor = '#EEA9B8'; //red
    }
	if (document.getElementById('homepage').value == "" || document.getElementById('homepage').value.substring(0,7) == "http://") {
		document.getElementById("homepage").style.backgroundColor = '#C1FFC1'; //green
		h = true;
    } else {
		document.getElementById("homepage").style.backgroundColor = '#EEA9B8'; //red
    }
	if (f && l && m && s && ti && wh && wih && h) {
		document.getElementById("sendAgent").style.visibility = 'visible';
	 } else {
		document.getElementById("sendAgent").style.visibility = 'hidden';
	 }
}
</script>
