<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");
require_once("models/header.php");

//header
echo "
<body onLoad='init();'>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Connect Label to Ressources</h1>
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
<b>Expert: Predicate Notes</b>
<br><br>
A triple of the form <b>S <a href='http://www.w3.org/TR/rdf-schema#ch_seealso' target='_blank'>rdfs:seeAlso</a> O</b> states that the resource O may provide additional information about S.<br>It may be possible to retrieve representations of O from the Web, but this is not required.<br>When such representations may be retrieved, no constraints are placed on the format of those representations.
<br><br>
A triple of the form <b>S <a href='http://www.w3.org/TR/rdf-schema#ch_isdefinedby' target='_blank'>rdfs:isDefinedBy</a> O</b> states that the resource O defines S.<br>It may be possible to retrieve representations of O from the Web, but this is not required.<br>When such representations may be retrieved, no constraints are placed on the format of those representations. 
<br><br>
Such an <b><a href='http://www.w3.org/TR/owl-ref#sameAs-def' target='_blank'>owl:sameAs</a></b> statement indicates that two URI references actually refer to the same thing: the individuals have the same \"identity\". 
<br><br>
Just SKOS <b><a href='http://www.w3.org/TR/2009/REC-skos-reference-20090818/#mapping'
  target='_blank'>Mapping relations</a></b> to links concepts to other ConceptSchemes are available. <b>
<br>
<a href='http://www.w3.org/TR/2009/REC-skos-reference-20090818/#semantic-relations'
  target='_blank'>Semantic Relations</a></b> for Concepts in the same Concept Scheme are available on a different page. 
<br>
</i>
</p>
</info2>
<br>

<center>
<table border='0'>
	
	<tr align='center'>
	  <td><b>subject</b></td>
	</tr>
	<tr align='center'>
	  <td><select id='labellist' size='10' style='width: 500px;' onclick='IO.sendSPARQL_LabelMetadata(Config.SPARQL,SPARQL.labmetadata)'></select></td>
	</tr>
	
	<tr align='center'>
	  <td><br><b>predicate</b></td>
	</tr>
	<tr align='center'>
		<td>
			<select id='predicate'>
				<option value='http://www.w3.org/2000/01/rdf-schema#seeAlso'>rdfs:seeAlso</option>
				<option value='http://www.w3.org/2000/01/rdf-schema#isDefinedBy'>rdfs:isDefinedBy</option>
				<option value='http://www.w3.org/2002/07/owl#sameAs'>owl:sameAs</option>
				<!--<option value='http://www.w3.org/2004/02/skos/core#related'>skos:related</option>
				<option value='http://www.w3.org/2004/02/skos/core#broader'>skos:broader</option>
				<option value='http://www.w3.org/2004/02/skos/core#narrower'>skos:narrower</option>-->
				<option value='http://www.w3.org/2004/02/skos/core#closeMatch'>skos:closeMatch</option>
				<option value='http://www.w3.org/2004/02/skos/core#exactMatch'>skos:exactMatch</option>
				<option value='http://www.w3.org/2004/02/skos/core#relatedMatch'>skos:relatedMatch</option>
				<option value='http://www.w3.org/2004/02/skos/core#narrowMatch'>skos:narrowMatch</option>
				<option value='http://www.w3.org/2004/02/skos/core#broadMatch'>skos:broadMatch</option>
			</select>
		</td>
	</tr>
	
	<tr align='center'>
	  <td><br><b>object (ressource URI)</b></td>
	</tr>
	<tr align='center'>
	<td><input id='ressource' type='text' size='75' maxlength='150' style='color: #424242; font-family: Courier New;'></td>
	</tr>
</table>
</center>
<br>

<center>
<span id='connect_vl'><input type='button' value='Connect Label and Ressource' id='sendressource' onclick='IO.getinputconnectRessourceLabelTriple();'></span>
</center>
</span>
<br>

<center>
<span id='info'></span>
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

var internalID = -1;
var IO = {};
var TS = {};
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];
var GLOBAL = {};
GLOBAL.selectedURL = "";

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

///////////
// Lists //
///////////

IO.sendSPARQLMyLabelsList = function(url, query, callback, info) {
    
    $('#deletelabel').show();
	$('#mylabelfunctions').show();
        
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
            
            clearLabelList();
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
							//console.log(val);
						}
						if (key == "xml:lang") {
							lang = t.s['xml:lang'];
							//console.log(lang);
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
    
    var tmp = document.getElementById('labellist').value.replace("@","__");
	var tmp2 = tmp.split("__");
	var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
	query = query.replace("$label",tmp3);
	query = query.replace("$creator",user);
	query = encodeURIComponent(query);
	
	if (query != "SELECT%20?verb%20?value%20WHERE%20%7B%20%3C%3E%20?verb%20?value%20%7D") {
        
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
				html_str = "<br><hr width='85%' align='center' /><br>";
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
				var link_ConRel = "";
				html_str += "<tr>";
				var identifier = "";
				
				for (var i=0; i<TS.output2.length; i++) {
					
					var split = TS.output2[i].split("__");
					
					if (split[1].indexOf("label#") != -1) {
						identifier = split[1].replace(Config.Instance_LABEL,"");
					} 
					
					if (link_Con == false && split[1].indexOf("label#") == -1) {
						//if (split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#") != -1 || split[1].indexOf("http://www.w3.org/2002/07/owl#") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#related") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrower") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broader") != -1) {
						if (split[1].indexOf("http://www.w3.org/2004/02/skos/core#related") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrower") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broader") != -1  || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#") != -1 || split[1].indexOf("http://www.w3.org/2002/07/owl#") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#closeMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#exactMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#relatedMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrowMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broadMatch") != -1) {
							html_str += "<td style='background-color:#fffebe'>"+split[1]+"</td>";
							link_Con = true;
							link_ConRel = split[1];
						} else {
							html_str += "<td>"+split[1]+"</td>";
						}
					//} else if (link_Con == true && split[1].indexOf("label#") == -1) {
					} else if (link_Con == true) {
						link_ConURI = split[1];
						html_str += "<td style='background-color:#fffebe'><a href=\""+split[1]+"\" target='_blank'>"+split[1]+"</a></td>";
						link_Con = false;
						link_ConRel = "";
						link_ConURI = "";
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

				document.getElementById("info").innerHTML=html_str;
				
			}
			
			
		});
		
	} else {
		alert("no content");
	}
}

/////////////
// updates //
/////////////

// IO.getinputconnectProjectVocabularyTriple --> IO.getURIVoc --> IO.getURIPro --> IO.sendUpdateCallback --> IO.sendSPARQL_VocabularyMetadata

IO.getinputconnectRessourceLabelTriple = function() {
	
	IO.getURILab(Config.SPARQL, SPARQL.uriLab, "connect");
    
}

IO.getURILab = function(url, query, mode) {
	
	var tmp = document.getElementById('labellist').value.replace("@","__");
	var tmp2 = tmp.split("__");
	var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
	query = query.replace("$label",tmp3);
	query = query.replace('$creator',user);
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
			
			if (document.getElementById('labellist').value != "") {
				var laburi = output.results.bindings[0].s.value; 
			} else {
				alert("no valid content!");
			}
			
			var predicate = document.getElementById('predicate').value;
			var ressource = document.getElementById('ressource').value;

			if (ressource!=null && laburi!=null && ressource!="" && laburi!="" && ressource.indexOf("http://") != -1) {
				
				var tri = "INSERT DATA { ";
				tri += "<"+laburi+"> "; //label
				tri += "<"+predicate+"> ";
				tri += "<"+ressource+">"; //label rest/vocabs
				tri += ". ";
				tri += " }";
					
				
				
				IO.sendUpdateCallback(Config.Update,tri,IO.sendSPARQL_LabelMetadata);
				
			} else {
				alert("no valid content!");
			}

        }
    });
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

//////////
// more //
//////////

function init() {
    IO.sendSPARQLMyLabelsList(Config.SPARQL,SPARQL.mylabels);
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

//////

IO.sendSPARQL_setVocID = function(url, query, uri) {
	
	query = query.replace('$uri',uri);                 
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
			}
           			
			var bindings = output.results.bindings; 
			for (var i=0; i<bindings.length; i++) {       
				var t = bindings[i];
				//console.log(t);
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
				
				document.getElementById("vocabularylist").value = val + "@" + lang;
				IO.sendSPARQL_VocabularyMetadata(Config.SPARQL,SPARQL.vocmetadata);
				
			}
        }
    });
}

IO.sendSPARQL_setLabID = function(url, query, uri) {
	
	query = query.replace('$uri',uri);                 
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
			}
            
			var bindings = output.results.bindings; 
			for (var i=0; i<bindings.length; i++) {       
				var t = bindings[i];
				//console.log(t);
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
				
				document.getElementById("labellist").value = val + "@" + lang;
				IO.sendSPARQL_LabelMetadata(Config.SPARQL,SPARQL.labmetadata);
				
			}
        }
    });
}

</script>