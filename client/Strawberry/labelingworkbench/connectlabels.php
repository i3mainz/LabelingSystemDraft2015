<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//header
echo "
<body onLoad='IO.sendSPARQL_SKOSLabels(Config.SPARQLConcepts,TS.labels); IO.sendSPARQLMyLabelsList(Config.SPARQL,SPARQL.mylabels);'>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Connect Label with a (stored) Concept</h1>
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

<center>
<table border='0'>
	<tr>
	  <td>
		<b>Stored Concepts</b>
	  </td>
	  <td>
		<b>My Labels</b>
	  </td>
	</tr>
	<tr>
	  <td><select id='conceptlist' size='10' style='width: 350px;' onChange='IO.sendSPARQL_SKOSConceptSchemeLabelMetadata(Config.SPARQLConcepts,TS.conceptschemelabelmetadata);'></select></td>
	  <td><select id='labellist' size='10' style='width: 350px;' onChange='IO.sendSPARQL_LabelMetadata(Config.SPARQL,SPARQL.labmetadata);'></select></td>
	</tr>
</table>
<br>

<h1>Connect/Disconnect Label and Concept</h1>
        <br>
        <span id='connect_ll'><a href='javaScript:IO.getinputconnectConceptLabelTriple();'>Connect Label to ConceptLabel</a> with </span>
        <select id='relation'>
            <!--<option value='skos:related'>skos:related</option>
            <option value='skos:broader'>skos:broader</option>
            <option value='skos:narrower'>skos:narrower</option>-->
			<option value='skos:closeMatch'>skos:closeMatch</option>
			<option value='skos:exactMatch'>skos:exactMatch</option>
			<option value='skos:relatedMatch'>skos:relatedMatch</option>
			<option value='skos:narrowMatch'>skos:narrowMatch</option>
			<option value='skos:broadMatch'>skos:broadMatch</option>
        </select>
</center>
<br>

<center>
<span id='info'></span>
</center>
<br>

<center>
<span id='info2'></span>
</center>
<br>

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

var IO = {};
var TS = {};
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

TS.concepts = "SELECT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#inScheme> ?o . } ORDER BY (?o)";
TS.mylabels = "SELECT DISTINCT ?s WHERE { ?l a <http://143.93.114.137/vocab#Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?s . ?l <http://purl.org/dc/terms/creator> \"$creator\" . } ORDER BY ASC(?s)";
TS.labmetadata = "SELECT DISTINCT ?s ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <http://143.93.114.137/vocab#Label> . ?s <http://purl.org/dc/terms/creator> \"$creator\". }";
TS.conceptschemelabelmetadata = "SELECT ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . }";

TS.labels = "SELECT ?o WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?o . ?s a <http://www.w3.org/2004/02/skos/core#Concept> . } ORDER BY (?o)";
TS.conceptschemeconceptmetadata = "SELECT ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/2004/02/skos/core#inScheme> ?a . FILTER (?s = <"+"$concept"+">) . }";
//TS.labelconcepts = "SELECT DISTINCT ?o WHERE { <$label> ?p ?o . FILTER (?p = <http://www.w3.org/2004/02/skos/core#related> || ?p = <http://www.w3.org/2004/02/skos/core#broader> || ?p = <http://www.w3.org/2004/02/skos/core#narrower>) . }";
TS.URL2Label = "SELECT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> \"$label\"@en . }";

TS.uriLab = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <http://143.93.114.137/vocab#Label> . ?s <http://purl.org/dc/terms/creator> \"$creator\". }";
TS.uriCon = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . }";

///////////
// Lists //
///////////

IO.sendSPARQL_SKOSLabels = function(url, query, callback, info) {
    
    query = encodeURIComponent(query);
        
    $.ajax({
        type: 'GET',
        url: url,
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
				//console.info(output);
			}
			
			clearConceptList();
            
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
				for(var key in t.o) {
						
						if (key == "value") {
							val = t.o.value;
						}
						if (key == "xml:lang") {
							lang = t.o['xml:lang'];
						}
						
				}
				
				var x = document.getElementById("conceptlist");
				var option = document.createElement("option");
				option.text = val + "@" + lang;
				x.add(option);
				
				val = "";
				lang = "";
				
			}
		}
    });
    
}

IO.sendSPARQLMyLabelsList = function(url, query, callback, info) {
        
    query = query.replace('$creator',user);
    query = encodeURIComponent(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            var outputObj;
			
			try {
				output = JSON.parse(output);
			} catch (e) {
				console.log(e);
			} finally {
				//console.info(output);
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
				
				var x = document.getElementById("labellist");
				var option = document.createElement("option");
				option.text = val + "@" + lang;
				x.add(option);
				
				val = "";
				lang = "";
				
			}
        }
    });
}

//////////////
// Metadata //
//////////////

IO.sendSPARQL_LabelMetadata = function(url, query, callback, info) {
    
    if (document.getElementById('labellist').value.indexOf("@") > -1) {
	
		var tmp = document.getElementById('labellist').value.replace("@","__");
		var tmp2 = tmp.split("__");
		var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
		query = query.replace("$label",tmp3);
		query = query.replace("$creator",user);
		query = encodeURIComponent(query);
			
		$.ajax({
			type: 'GET',
			url: url,
			data: {query: query, format: 'json'},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			success: function(output) {
				
				// set help array to null
					TS.vars.length = 0;
					TS.result.length = 0;
					TS.output.length = 0;
					TS.output2.length = 0;
					
					// html output
					html_str = "";
					html_str += "<h1>Label - "+document.getElementById('labellist').value+"</h1>";
					html_str += "<br>";
					html_str += "<table border='1' width='75%'>";
					html_str += "<colgroup>";
					html_str += "<col width='50%'>";
					html_str += "<col width='50%'>";
					html_str += "</colgroup>";
					html_str += "<tr>";
					
					// read JSON head-->vars objects to array (e.g. spo)
					try {
						output = JSON.parse(output);
					} catch (e) {
						console.log(e);
					} finally {
					}
					var varsj = output.head.vars;
					for (var i=0; i<varsj.length; i++) {
						TS.vars.push(varsj[i]);
					}
					
					// html output
					//for (var i=0; i<TS.vars.length; i++){
						//html_str += "<th style='background:#dddddd'>"+TS.vars[i]+"</th>";
					//}
					
					// html output
					for (var i=0; i<TS.vars.length; i++){
						if (TS.vars[i].indexOf("s") != -1) {
						} else {
							html_str += "<th style='background:#dddddd'>"+TS.vars[i]+"</th>";
						}
					}
					
					// html output
					html_str += "</tr>";
					html_str += "</tr>";
					
					// read JSON rasults-->bindings[i] objects to array with key (e.g. pppsssooo)
					try {
						output = JSON.parse(output);
					} catch (e) {
						console.log(e);
					} finally {
					}
					var bindings = output.results.bindings; 
					for (var i=0; i<bindings.length; i++) {       
						var t = bindings[i];
						for(var key in t) {
								// read xml:lang and set if key = value
								var language;
								var lang = t.value['xml:lang'];
								if (typeof lang === "undefined") {
									language = "";
								} else {
									language = "@" + t.value['xml:lang'];
									//console.log(key + "__" + t[key].value + language);
								}
							if (key == "value") {
								TS.result.push(key + "__" + t[key].value + language);
							} else {
								TS.result.push(key + "__" + t[key].value);
							}
						}
					}
					
					// sort sparql output like the vars e.g. ssspppooo
					var k = 0;
					for (var i=0; i<TS.vars.length; i++) {
						for (var j=0; j<TS.result.length; j++) {
							if (TS.result[j].indexOf(TS.vars[i]+"__") != -1){
								var split = TS.result[j].split("__");
								TS.output[k] = k + "__" + split[1];
								k++;
							}
						}
					}
				
					// sort output like the vars triples e.g. spospospo
					var k = -1;
					var l = (TS.output.length)/TS.vars.length;
					for (var i=0; i<l; i++) {
						if (TS.vars.length==1){
							TS.output2[k+1] = TS.output[i];
							k++;
						} else {
							for (var j=0; j<TS.vars.length; j++) {
								var tmp1 = k+1;
								var tmp2 = i+(j*l);
								TS.output2[tmp1] = TS.output[tmp2];
								k++;
							}
						}
					}
				
					// html output
					var link_Con = false;
					html_str += "<tr>";
					var identifier = "";
					
					for (var i=0; i<TS.output2.length; i++) {
						
						var split = TS.output2[i].split("__");
						
						if (split[1].indexOf("label#") != -1) {
							identifier = split[1].replace("http://143.93.114.137/label#","");
							//console.log(identifier);
						} 
						
						if (link_Con == false && split[1].indexOf("label#") == -1) {
							if (split[1].indexOf("http://www.w3.org/2004/02/skos/core#related") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrower") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broader") != -1 || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#seeAlso") != -1 || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#isDefinedBy") != -1 || split[1].indexOf("http://www.w3.org/2002/07/owl#sameAs") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#closematch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#exactMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrowMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#relatedMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broadMatch") != -1 ) {
								html_str += "<td style='background-color:#fffebe'>"+split[1]+"</td>";
								link_Con = true;
							} else {
								html_str += "<td>"+split[1]+"</td>";
							}
						//} else if (link_Con == true && split[1].indexOf("label#") == -1) {
						} else if (link_Con == true) {
							html_str += "<td style='background-color:#fffebe'><a href=\""+split[1]+"\" target=\"_blank\">"+split[1]+"</a></td>";
							link_Con = false;
						}
						
						
						if ((i+1)%TS.vars.length==0) {
							html_str += "</tr>";
							html_str += "<tr>";
						}
					}
					
					// html output
					html_str += "</tr>";
					html_str += "<tr style='background-color:#A9D0F5'><td><i>IDENTIFIER</i></td><td>"+identifier+"</td></tr>";
					html_str += "</table>";

					document.getElementById("info2").innerHTML=html_str;
					
				}
				
				
			});
	
	} else {
		alert("no content");
	}
}

IO.sendSPARQL_SKOSConceptSchemeLabelMetadata = function(url, query, callback, info) {
    
    if (document.getElementById('conceptlist').value.indexOf("@") > -1) {
	
		var tmp = document.getElementById('conceptlist').value.replace("@","__");
		var tmp2 = tmp.split("__");
		var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
		query = query.replace("$label",tmp3);
		query = encodeURIComponent(query);
			
		$.ajax({
			type: 'GET',
			url: url,
			data: {query: query, format: 'json'},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			success: function(output) {
				
				// set help array to null
					TS.vars.length = 0;
					TS.result.length = 0;
					TS.output.length = 0;
					TS.output2.length = 0;
					
					// html output
					html_str = "";
					html_str += "<h1>Concept - "+document.getElementById('conceptlist').value+"</h1>";
					html_str += "<br>";
					html_str += "<table border='1' width='75%'>";
					html_str += "<colgroup>";
					html_str += "<col width='50%'>";
					html_str += "<col width='50%'>";
					html_str += "</colgroup>";
					html_str += "<tr>";
					
					// read JSON head-->vars objects to array (e.g. spo)
					try {
						output = JSON.parse(output);
					} catch (e) {
						console.log(e);
					} finally {
					}
					var varsj = output.head.vars;
					for (var i=0; i<varsj.length; i++) {
						TS.vars.push(varsj[i]);
					}
					
					// html output
					//for (var i=0; i<TS.vars.length; i++){
						//html_str += "<th style='background:#dddddd'>"+TS.vars[i]+"</th>";
					//}
					
					// html output
					for (var i=0; i<TS.vars.length; i++){
						if (TS.vars[i].indexOf("s") != -1) {
						} else {
							html_str += "<th style='background:#dddddd'>"+TS.vars[i]+"</th>";
						}
					}
					
					// html output
					html_str += "</tr>";
					html_str += "</tr>";
					
					// read JSON rasults-->bindings[i] objects to array with key (e.g. pppsssooo)
					try {
						output = JSON.parse(output);
					} catch (e) {
						console.log(e);
					} finally {
					}
					var bindings = output.results.bindings;
					for (var i=0; i<bindings.length; i++) {       
						var t = bindings[i];
						for(var key in t) {
								// read xml:lang and set if key = value
								var language;
								var lang = t.value['xml:lang'];
								if (typeof lang === "undefined") {
									language = "";
								} else {
									language = "@" + t.value['xml:lang'];
									//console.log(key + "__" + t[key].value + language);
								}
							if (key == "value") {
								TS.result.push(key + "__" + t[key].value + language);
							} else {
								TS.result.push(key + "__" + t[key].value);
							}
						}
					}
					
					// sort sparql output like the vars e.g. ssspppooo
					var k = 0;
					for (var i=0; i<TS.vars.length; i++) {
						for (var j=0; j<TS.result.length; j++) {
							if (TS.result[j].indexOf(TS.vars[i]+"__") != -1){
								var split = TS.result[j].split("__");
								TS.output[k] = k + "__" + split[1];
								k++;
							}
						}
					}
				
					// sort output like the vars triples e.g. spospospo
					var k = -1;
					var l = (TS.output.length)/TS.vars.length;
					for (var i=0; i<l; i++) {
						if (TS.vars.length==1){
							TS.output2[k+1] = TS.output[i];
							k++;
						} else {
							for (var j=0; j<TS.vars.length; j++) {
								var tmp1 = k+1;
								var tmp2 = i+(j*l);
								TS.output2[tmp1] = TS.output[tmp2];
								k++;
							}
						}
					}
				
					// html output
					var link_Con = false;
					html_str += "<tr>";
					
					for (var i=0; i<TS.output2.length; i++) {
						
						var split = TS.output2[i].split("__");
						
						if (link_Con == false) {
							if (split[1].indexOf("http://www.w3.org/2004/02/skos/core#related") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrower") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broader") != -1 || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#seeAlso") != -1 || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#isDefinedBy") != -1 || split[1].indexOf("http://www.w3.org/2002/07/owl#sameAs") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#closematch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#exactMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrowMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#relatedMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broadMatch") != -1 ) {
								html_str += "<td style='background-color:#fffebe'>"+split[1]+"</td>";
								link_Con = true;
							} else {
								html_str += "<td>"+split[1]+"</td>";
							}
						} else if (link_Con == true) {
							html_str += "<td style='background-color:#fffebe'><a href=\""+split[1]+"\" target=\"_blank\">"+split[1]+"</a></td>";
							link_Con = false;
						}
						
						
						if ((i+1)%TS.vars.length==0) {
							html_str += "</tr>";
							html_str += "<tr>";
						}
					}
					
					// html output
					html_str += "</tr>";
					html_str += "</table>";

					document.getElementById("info").innerHTML=html_str;
				
			}
			
			
		});
		
	}
}

/////////////
// updates //
/////////////

// IO.getinputconnectConceptLabelTriple --> IO.getURIVoc --> IO.getURIPro --> IO.sendUpdateCallback --> IO.sendSPARQL_VocabularyMetadata

IO.getinputconnectConceptLabelTriple = function() {
	
	IO.getURILab(Config.SPARQL, SPARQL.uriLab);
    
}

IO.getURILab = function(url, query, mode) {
	
	if (document.getElementById('labellist').value.indexOf("@") > -1) {
	
		var tmp = document.getElementById('labellist').value.replace("@","__");
		var tmp2 = tmp.split("__");
		var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
		query = query.replace("$label",tmp3);
		query = query.replace("$creator",user);
		query = encodeURIComponent(query);
			
		$.ajax({
			beforeSend: function(req) {
				req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
		},
			type: 'GET',
			url: url,
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
				
				var laburi = output.results.bindings[0].s.value; 
				
				IO.getURICon(Config.SPARQLConcepts, TS.uriCon, laburi);
				
			}
		});
	} else {
		alert("no content!");
	}
}

IO.getURICon = function(url, query, laburi) {
		
		var tmp = document.getElementById('conceptlist').value.replace("@","__");
		
		if (tmp != "") {
		
			var tmp2 = tmp.split("__");
			var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
			query = query.replace("$label",tmp3);
			query = encodeURIComponent(query);
				
			$.ajax({
				beforeSend: function(req) {
					req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
			},
				type: 'GET',
				url: url,
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
					
					var conuri = output.results.bindings[0].s.value; 
					
					var c = document.getElementById('conceptlist').value;
					var l = document.getElementById('labellist').value;
					var rel = document.getElementById('relation').value.replace("skos:","http://www.w3.org/2004/02/skos/core#");
					
					if (c!=null && l!=null && c!="" && l!="") {
						var tri = "INSERT DATA { ";
						tri += "<"+laburi+"> ";
						tri += "<"+rel+"> ";
						tri += "<"+conuri+">";
						tri += " }";
						
						console.log(tri);
						IO.sendUpdateCallback(Config.Update,tri,IO.sendSPARQL_LabelMetadata);
						
					} else {
						alert("no content!");
					}
				}
			});
		} else {
			alert("no content!");
		}
	
}

IO.sendUpdateCallback = function(url, up, callback, info) {
    
    up = encodeURIComponent(up);
        
    $.ajax({
        beforeSend: function(req) {
            req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
	},
        type: 'POST',
        url: url,
        data: {update: up},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            callback(Config.SPARQL,SPARQL.labmetadata);
        }
    });
}

//////////////


function clearConceptList() {
    document.getElementById('conceptlist').options.length = 0;
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

</script>