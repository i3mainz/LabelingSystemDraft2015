<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>
<script src="utils.js"></script>

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
	<h1>Upload/Delete SKOS SPARQL endpoint</h1>
	<h2>Ontologist Function</h2>
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
EXAMPLES: <a href='http://labeling.i3mainz.hs-mainz.de/ontology/index.htm#initial' target='_blank'>look at the initial values</a>
<br>
</i>
</p>
</info2>
<br>
<center>
<b>endpoint name: </b><input id='sparqlname' type='text' size='50' maxlength='200'>
<br>
<b>SPARQL XML-URI: </b><input id='sparqlxmluri' type='text' size='50' maxlength='250'>
<br>
<b>SPARQL query: </b><input id='sparqlquery' type='text' size='50'>
<br>
<br>
<span id='upload_ep'><a href='javaScript:IO.inputSPARQLendpoint()'>Upload new SPARQL endpoint </a></span> | 
<span id='delete_ep'><a href='javaScript:IO.deleteSPARQLendpoint()'>Delete SPARQL endpoint (please type in \"endpoint name\")</a></span>
<br><br>
</center>
<br>
</center>

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

IO = {};

IO.inputSPARQLendpoint = function() {
	if (document.getElementById('sparqlname').value != "" && document.getElementById('sparqlxmluri').value != "" && document.getElementById('sparqlquery').value != "") {
		$.ajax({
			type: 'GET',
			url: Config.UUID,
			error: function(jqXHR, textStatus, errorThrown) {
				console.error(errorThrown);
				alert(errorThrown);
			},
			success: function(uuid) {
				//console.info(uuid);
				// start triple
				var sparqlendpoint = "INSERT DATA { ";
				// typing
				sparqlendpoint += Config.Instance("sparqlendpoint",uuid,true);
				sparqlendpoint += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
				sparqlendpoint += Config.Ontology("SPARQLendpoint",true);
				sparqlendpoint += ". ";
				// sameAs REST-API
				sparqlendpoint += Config.Instance("sparqlendpoint",uuid,true);
				sparqlendpoint += Config.Ontology("sameAs",true);
				sparqlendpoint += "<"+Config.Rest_SPARQLENDPOINTS+uuid+"> ";
				sparqlendpoint += ". ";
				// name
				sparqlendpoint += Config.Instance("sparqlendpoint",uuid,true);
				sparqlendpoint += Config.Ontology("sparqlname",true);
				sparqlendpoint += "\""+document.getElementById('sparqlname').value+"\" ";
				sparqlendpoint += ". ";
				// uri
				sparqlendpoint += Config.Instance("sparqlendpoint",uuid,true);
				sparqlendpoint += Config.Ontology("sparqlxmluri",true);
				sparqlendpoint += "\""+document.getElementById('sparqlxmluri').value+"\" ";
				sparqlendpoint += ". ";
				// query
				sparqlendpoint += Config.Instance("sparqlendpoint",uuid,true);
				sparqlendpoint += Config.Ontology("sparqlquery",true);
				sparqlendpoint += "\""+document.getElementById('sparqlquery').value+"\" ";
				sparqlendpoint += ". ";
				// end triple
				sparqlendpoint += "}";
				//console.info(sparqlendpoint);
				// mask triples
				sparqlendpoint = encodeURIComponent(sparqlendpoint);	
				//console.info(sparqlendpoint);
				$.ajax({
					beforeSend: function(req) {
					req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
				},
					type: 'POST',
					url: Config.Update,
					data: {update: sparqlendpoint},
					error: function(jqXHR, textStatus, errorThrown) {
						console.error(errorThrown);
						alert(errorThrown);
					},
					success: function(xml) {
						alert("concept saved");
					}
				});				
			}
		});
	} else {
		console.error("no three values");
		alert("no three values");
	}		
}

IO.deleteSPARQLendpoint = function() {
    if (document.getElementById('sparqlname').value!=null && document.getElementById('sparqlname').value!="") {
        // SPARQL delete statement
		var sparqlendpoint = "DELETE WHERE { ?se "+Config.Ontology("sparqlname",true)+"'"+document.getElementById('sparqlname').value+"'; ?property ?value }";
        console.info(sparqlendpoint);
		sparqlendpoint = encodeURIComponent(sparqlendpoint);
		//console.info(sparqlendpoint);
		$.ajax({
			beforeSend: function(req) {
			req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
		},
			type: 'POST',
			url: Config.Update,
			data: {update: sparqlendpoint},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error(errorThrown);
				alert(errorThrown);
			},
			success: function(xml) {
				alert("concept removed");
			}
		});	
    } else {
        console.error("no name");
		alert("no name");
    }	
}

</script>