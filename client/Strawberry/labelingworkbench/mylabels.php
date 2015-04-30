<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//header
echo "
<body onLoad='init();'>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Create new Label / Connect to Vocabulary</h1>
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
<b>Expert: Triple Store Notes</b>
<br><br>
<a href='http://labeling.i3mainz.hs-mainz.de/ontology/#written' target='_blank'>The written triples you can find here</a>.
</i>
</p>
</info2>
<center>

<table border='0'>
<tr>
	<td></td>
	<td></td>
	<td><input type='button' value='Create' id='sendlabel' onclick='IO.getUUID(Config.UUID);'></td>
</tr>
<tr>
	<td><b>label appellation*&nbsp;&nbsp;&nbsp;</b></td>
	<td><input id='label' type='text' size='50' maxlength='50' onkeyup='fieldCheck()'></td>
	<td>
		<b>language </b><select id='languageLabel'></select>
	</td>
</tr>
<tr>
	<td><b>alternative appellation</b></td>
	<td><input id='altlabel' type='text' size='50' maxlength='256'></td>
	<td>
		<b>language </b><select id='languagealtlabel'></select>
	</td>
</tr>
<tr>
	<td><b>note</b></td>
	<td><input id='note' type='text' size='50' maxlength='256'></td>
	<td>
		<b>language </b><select id='languageNote'></select>
	</td>
</tr>
<tr>
	<td><b>definition</b></td>
	<td><input id='definition' type='text' size='50' maxlength='256'></td>
	<td>
		<b>language </b><select id='languageDefinition'></select>
	</td>
</tr>
</table>
</center>
<br>

<h1>My labels (appellation of prefLang)</h1>
<br>
<center>
<table border='0'>
	<tr>
	  <td>
		<!--<a href='javaScript:IO.sendSPARQLMyLabelsList(Config.SPARQL,SPARQL.mylabels)'>My Labels</a> | -->
		<span id='deletelabel'><i><a href='javaScript:deleteLabelQuestion(document.getElementById(\"labellist\").value)'>Delete Label</a></i></span> 
		<!--<a href='javaScript:IO.sendSPARQLLabelList(Config.SPARQL,SPARQL.labels)'>All Labels</a>-->
	  </td>
	  <td>
		<b>My Vocabularies</b>
	  </td>
	</tr>
	<tr>
	  <td><select id='labellist' size='10' style='width: 500px;' onclick='IO.sendSPARQL_LabelMetadata(Config.SPARQL,SPARQL.labmetadata)'></select></td>
	  <td><select id='vocabularylist' size='10' style='width: 500px;' onclick='IO.sendSPARQL_VocabularyMetadata(Config.SPARQL,SPARQL.vocmetadata)'></select></td>
	</tr>
</table>
</center>
<br>

<!--<h1>Look for existing prefLabels connected with a vocabulary</h1>
<br>
<center>
<b>SubString: </b>
<input id='searchstring' type='text' size='30' maxlength='30'>
<input type='button' value='Look for SubString' id='sendlookfor' onclick='IO.sendSPARQL_QuerySubStringLabels(Config.SPARQL,SPARQL.querylabel)'>
<br><br>
<center>
<span id='info3'></span>
</center>
<br>-->

<span id='mylabelfunctions'>
<h1>Connect/Disconnect vocabulary and label</h1>
<br>
<center>
<span id='connect_vl'><a href='javaScript:IO.getinputconnectVocabularyLabelTriple()'>Connect Label to Vocabulary</a></span> | 
<span id='disconnect_vl'><a href='javaScript:IO.getinputdisconnectVocabularyLabelTriple()'>Disconnect Label from Vocabulary</a></span>
</center>
</span>
<br>

<center>
<span id='info'></span>
</center>
<br>

<center>
<span id='info2'>
</span>
</center>
<br/>

<center>
<span id='labelsearchinfo'>
</span>
</center>
<br/>

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
	LS.GUI.loadGUI("labels");
});

////////////
// Create //
////////////

// IO.getUUID --> IO.labelCheck --> IO.sendLabelInput --> IO.sendSPARQLMyLabelsList

IO.getUUID = function(url) {
        
	$.ajax({
		type: 'GET',
		url: url,
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(uuid) {
			
			uuid = uuid.replace("\n","");
			uuid = uuid.replace("\r","");
			
			var l = document.getElementById('label').value;
			var ll = document.getElementById('languageLabel').value;
			var a = document.getElementById('altlabel').value;
			var al = document.getElementById('languagealtlabel').value;
			var n = document.getElementById('note').value;
			var nl = document.getElementById('languageNote').value;
			var d = document.getElementById('definition').value;
			var dl = document.getElementById('languageDefinition').value;
			
			//console.log(uuid);
			
			var label = "INSERT DATA { ";
			//own ontology
			label += Config.Instance("label",uuid,true);
			label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
			label += Config.Ontology("Label",true);
			label += ".";
			// skos concept
			label += Config.Instance("label",uuid,true);
			label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
			label += "<http://www.w3.org/2004/02/skos/core#Concept> ";
			label += ".";
			// dcterms creator
			label += Config.Instance("label",uuid,true);
			label += "<http://purl.org/dc/terms/creator> ";
			label += Config.Instance("agent",user,true);
			label += ".";
			// dc creator
			label += Config.Instance("label",uuid,true);
			label += "<http://purl.org/dc/elements/1.1/creator> ";
			label += "\""+user+"\" ";
			label += ".";
			// dcterms date
			var date = new Date();
			var da = date.toISOString();
			label += Config.Instance("label",uuid,true);
			label += "<http://purl.org/dc/terms/date> ";
			label += "\""+da+"\" ";
			label += ".";
			// dcterms licence
			label += Config.Instance("label",uuid,true);
			label += "<http://purl.org/dc/terms/licence> ";
			label += "<http://creativecommons.org/licenses/by/4.0/> ";
			label += ".";
			// skos pref label
			label += Config.Instance("label",uuid,true);
			label += "<http://www.w3.org/2004/02/skos/core#prefLabel> ";
			label += "\""+l+"\"@"+ll+" ";
			label += ".";
			if (document.getElementById('altlabel').value != "") {
				// skos note
				label += Config.Instance("label",uuid,true);
				label += "<http://www.w3.org/2004/02/skos/core#altLabel> ";
				label += "\""+a+"\"@"+al+" ";
				label += ".";
			}
			if (document.getElementById('note').value != "") {
				// skos note
				label += Config.Instance("label",uuid,true);
				label += "<http://www.w3.org/2004/02/skos/core#note> ";
				label += "\""+n+"\"@"+nl+" ";
				label += ".";
			}
			if (document.getElementById('definition').value != "") {
				// skos definition
				label += Config.Instance("label",uuid,true);
				label += "<http://www.w3.org/2004/02/skos/core#definition> ";
				label += "\""+d+"\"@"+dl+" ";
				label += ".";
			}
			// ls identifier
			label += Config.Instance("label",uuid,true);
			label += Config.Ontology("identifier",true);
			label += "\""+uuid+"\" ";
			label += ".";
			// ls pref language
			label += Config.Instance("label",uuid,true);
			label += Config.Ontology("prefLang",true);
			label += "\""+ll+"\" ";
			label += ".";
			//end
			label += " }";
			
			//console.info(label);
			
			if (document.getElementById('label').value != "") {
				IO.labelCheck(Config.SPARQL, SPARQL.labelcheck_Label, label);
			} else {
				alert("no or false content!");
				return;
			}
		}
		
	});
	
}

IO.labelCheck = function(url, query, label) {
	
	var tmp = document.getElementById('label').value;
	var tmp2 = document.getElementById('languageLabel').value;
	var tmp3 = "\"" + tmp + "\"" + "@" + tmp2;
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
		
			console.log(output);
			
			try {
				output = JSON.parse(output);
			} catch (e) {
				console.log(e);
			} finally {
			}
			
			var r = false;
			// wenn nicht vorhanden
			if(typeof output.results.bindings[0] === 'undefined'){
				r = true;
			} else {
				vocuri = output.results.bindings[0].s.value;
				document.getElementById("label").style.backgroundColor = '#EEA9B8'; //red
				r=confirm("Vocabulary with label " + tmp3 + " already exists! Create a multiple one?");
			}
			if (r) {
				IO.sendLabelInput(Config.Update,label);
			} else {
			}
        }
    });
}

IO.sendLabelInput = function(url, input) {
    
    input = encodeURIComponent(input);
        
    $.ajax({
        beforeSend: function(req) {
            req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
	},
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
			document.getElementById('label').value = "";
			document.getElementById('note').value = "";
			document.getElementById('definition').value = "";
			document.getElementById('altlabel').value = "";
			fieldCheck();
            IO.sendSPARQLMyLabelsList(Config.SPARQL,SPARQL.mylabels);
        }
    });
}

////////////
// Delete //
////////////

//deteleLabelQuestion--> IO.getURI --> IO.sendLabelDelete(2x) --> IO.sendSPARQLMyLabelsList

function deleteLabelQuestion(label)
{
	if (document.getElementById('labellist').value.indexOf("@") > -1) {
	
		var x;
		var r=confirm("Delete Label " + label + " ?");
		
		if (r==true)
		{
			x="You pressed OK!";
			IO.getURI(Config.SPARQL, SPARQL.uriLab);
		}
		else
		{
			x="You pressed Cancel!";
		}
		//console.log(x);
	} else {
		alert("no content!");
	}
}

IO.getURI = function(url, query) {
	
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
			var laburi = output.results.bindings[0].s.value; 
			//console.log(laburi);
			
			var labels = " DELETE WHERE { ";
			labels += "<"+laburi+"> ";
			labels += "?p ";
			labels += "?o ";
			labels += ".";
			labels += "}";
			var labels2 = "DELETE WHERE { ";
			labels2 += "?s ";
			labels2 += "?v ";
			labels2 += "<"+laburi+"> ";
			labels2 += ".";
			labels2 += "}";
        
			if (document.getElementById('labellist').value != "") {
				IO.sendLabelDelete(Config.Update,labels,clearLabelList);
				IO.sendLabelDelete(Config.Update,labels2,clearLabelList);
			} else {
				alert("no content!");
				return;
			}
			
        }
    });
}

IO.sendLabelDelete = function(url, input, callback, info) {
    
    input = encodeURIComponent(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
			document.getElementById("info").innerHTML="";
			IO.sendSPARQLMyLabelsList(Config.SPARQL,SPARQL.mylabels);
        }
    });
}

///////////
// Lists //
///////////

IO.sendSPARQLLabelList = function(url, query, callback, info) {
        
    $('#deletelabel').hide();
	$('#mylabelfunctions').hide();
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

IO.sendSPARQLMyVocabularyList = function(url, query, callback, info) {
    
    $('#deletelabel').show();
        
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
            
            clearVocabularyList();
			
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
				
				var x = document.getElementById("vocabularylist");
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
					var link_Voc = false;
					var link_Con = false;
					var link_REST = false;
					var link_ConRel = "";
					var link_ConURI = "";
					var link_RESTURI = "";
					html_str += "<tr>";
					var identifier = "";
					
					for (var i=0; i<TS.output2.length; i++) {
						
						var split = TS.output2[i].split("__");
						
						if (split[1].indexOf("label#") != -1) {
							identifier = split[1].replace(Config.Instance_LABEL,"");
						} 
						
						if (link_Voc == false && link_Con == false && link_REST == false && split[1].indexOf("label#") == -1) {
							if (split[1].indexOf(Config.Ontology("belongsTo",false)) != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#inScheme") != -1) {
								html_str += "<td style='background-color:#C1FFC1'>"+split[1]+"</td>";
								link_Voc = true;
							} else if (split[1].indexOf("http://www.w3.org/2004/02/skos/core#related") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrower") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broader") != -1  || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#seeAlso") != -1 || split[1].indexOf("http://www.w3.org/2000/01/rdf-schema#isDefinedBy") != -1 || split[1].indexOf("http://www.w3.org/2002/07/owl#sameAs") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#closeMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#exactMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#relatedMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#narrowMatch") != -1 || split[1].indexOf("http://www.w3.org/2004/02/skos/core#broadMatch") != -1) {
								html_str += "<td style='background-color:#fffebe'>"+split[1]+"</td>";
								link_Con = true;
								link_ConRel = split[1];
							} else if (split[1].indexOf(Config.Ontology("sameAs",false)) != -1) {
								html_str += "<td style='background-color:#f2a83a'>"+split[1]+"</td>";
								link_REST = true;
								link_RESTURI = split[1];
							} else {
								//console.log(split[1]);
								html_str += "<td>"+split[1]+"</td>";
							}
						} else if (link_Voc == true && split[1].indexOf("label#") == -1) {
							var tmpv = split[1].replace(Config.Instance_VOCABULARY,"");
							html_str += "<td style='background-color:#C1FFC1'><a href=\"javaScript:IO.sendSPARQL_setVocID(Config.SPARQL, SPARQL.VocURItoLab, '"+split[1]+"')\">"+split[1]+"</a></td>";
							link_Voc = false;
						} else if (link_Con == true) {
							link_ConURI = split[1];
							html_str += "<td style='background-color:#fffebe'><a href=\"javaScript:IO.setRelID('"+link_ConURI+"', '"+link_ConRel+"')\">"+split[1]+"</a></td>";
							link_Con = false;
							link_ConRel = "";
							link_ConURI = "";
							link_RESTURI = "";
						} else if (link_REST == true) {
							link_RESTURI = split[1];
							html_str += "<td style='background-color:#f2a83a'><a href=\""+split[1]+"\" target=\"_blank\">"+split[1]+"</a></td>";
							link_REST = false;
							link_ConRel = "";
							link_ConURI = "";
							link_RESTURI = "";
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
					document.getElementById("info").innerHTML += "<br><span id='reldel'><select id='labelrelations'></select>&nbsp;<input type='button' value='Delete Relation' id='deleterelation' onclick='deleteRelationQuestion(document.getElementById(\"labelrelations\").value)'></span>";
					document.getElementById('labelrelations').options.length = 0;
					IO.sendSPARQL_LabelURI(Config.SPARQL, SPARQL.uriLab);
					
				}
				
				
			});
			
			if (document.getElementById('vocabularylist').value != "") {
				IO.sendSPARQL_VocabularyMetadata(Config.SPARQL,SPARQL.vocmetadata);
			}
			
		} else {
			alert("no content");
		}
	} else {
		alert("no content");
	}
}

IO.sendSPARQL_VocabularyMetadata = function(url, query, callback, info) {
    
    if (document.getElementById('vocabularylist').value.indexOf("@") > -1) {
	
		var tmp = document.getElementById('vocabularylist').value.replace("@","__");
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
					html_str += "<h1>Vocabulary - "+document.getElementById('vocabularylist').value+"</h1>";
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
					//html_str += "<tr>";
					//for (var i=0; i<TS.output2.length; i++) {
						//var split = TS.output2[i].split("__");
						//html_str += "<td>"+split[1]+"</td>";
						//if ((i+1)%TS.vars.length==0) {
							//html_str += "</tr>";
							//html_str += "<tr>";
						//}
					//}
					
					// html output
					var link_Lab = false;
					html_str += "<tr>";
					
					for (var i=0; i<TS.output2.length; i++) {
						
						var split = TS.output2[i].split("__");
						
						if (split[1].indexOf("vocabulary#") != -1) {
							identifier = split[1].replace(Config.Instance_VOCABULARY,"");
							//console.log(identifier);
						} 
						
						if (link_Lab == false && split[1].indexOf("vocabulary#") == -1) {
							if (split[1].indexOf(Config.Ontology("contains",false)) != -1) {
								html_str += "<td style='background-color:#C1FFC1'>"+split[1]+"</td>";
								link_Lab = true;
							} else {
								html_str += "<td>"+split[1]+"</td>";
							}
						} else if (link_Lab == true && split[1].indexOf("vocabulary#") == -1) {
							var tmpv = split[1].replace(Config.Instance_LABEL,"");
							html_str += "<td style='background-color:#C1FFC1'><a href=\"javaScript:IO.sendSPARQL_setLabID(Config.SPARQL, SPARQL.LabURItoLab, '"+split[1]+"')\">"+split[1]+"</a></td>";
							link_Lab = false;
						} 
						
						if ((i+1)%TS.vars.length==0) {
							html_str += "</tr>";
							html_str += "<tr>";
						}
					
					}
					
					// html output
					html_str += "</tr>";
					html_str += "</table>";

					document.getElementById("info2").innerHTML=html_str;
					
				}
				
				
			});
		
		} else {
			alert("no content");
		}
	} else {
		alert("no content");
	}
}

IO.sendSPARQL_QuerySubStringLabels = function(url, query, callback, info) {
    
    query = query.replace("$ss",document.getElementById('searchstring').value);
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
				html_str += "<h1>prefLabel - Look for <i>"+document.getElementById('searchstring').value+"</i></h1>";
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
				for (var i=0; i<TS.vars.length; i++){
					if (TS.vars[i].indexOf("s") != -1 || TS.vars[i].indexOf("vocabulary") != -1) {
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
							var lang = t.preflabel['xml:lang'];
							if (typeof lang === "undefined") {
								language = "";
							} else {
								language = "@" + t.preflabel['xml:lang'];
								//console.log(key + "__" + t[key].value + language);
							}
						if (key == "preflabel") {
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
				html_str += "<tr>";
				
				var labelURI = false;
				var vocabularyURI = false;
				var labelURI_str = "";
				var vocabularyURI_str = "";
				var prefLabel_str = "";
				var creator_str = "";
				
				//if (link_Lab == false) {
				
				for (var i=0; i<TS.output2.length; i++) {
					
					var split = TS.output2[i].split("__");
					
					if (split[1].indexOf(Config.Instance_VOCABULARY) != -1) {
						//console.log(split[1]);
						vocabularyURI = true;
						vocabularyURI_str = split[1].replace(Config.Instance_VOCABULARY,"");
					} else if (split[1].indexOf(Config.Instance_LABEL) != -1) {
						//console.log(split[1]);
						labelURI = true;
						labelURI_str = split[1].replace(Config.Instance_LABEL,"");
					} else {
						creator_str = split[1];
						//html_str += "<td>"+split[1]+"</td>";
					}
					
					if ((i+1)%4==0 && vocabularyURI && labelURI) {
						//console.log("http://143.93.114.137/rest/voc/"+vocabularyURI_str+"/"+labelURI_str+".rdf");
						//html_str += "<td align='center'><a href='http://143.93.114.137/rest/voc/"+vocabularyURI_str+"/"+labelURI_str+".rdf' target='_blank'>"+prefLabel_str+"</a></td>";
						html_str += "<td align='center'><a href=\"javaScript:IO.sendSPARQL_LabelSearchMetadata(Config.SPARQL,SPARQL.labmetadataSearch, '"+creator_str+"', '"+prefLabel_str+"', '"+labelURI_str+"', '"+vocabularyURI_str+"')\">"+prefLabel_str+"</a></td>";
						html_str += "<td align='center'>"+creator_str+"</td>";
						labelURI = false;
						vocabularyURI = false;
						labelURI_str = "";
						vocabularyURI_str = "";
						prefLabel_str = "";
						creator_str = "";
					}
					
					if ((i+1)%TS.vars.length==0) {
						html_str += "</tr>";
						html_str += "<tr>";
					}
				}
				
				// html output
				html_str += "</tr>";
				html_str += "</table>";

				document.getElementById("info3").innerHTML=html_str;
				
			}
			
			
		});
		
	} else {
		alert("no content");
	}
	
}

///////////////
// Relations //
///////////////

IO.sendSPARQL_LabelURI = function(url, query, callback, info) {
	
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
			var laburi = output.results.bindings[0].s.value; 
			//console.log(laburi);
		
			IO.sendSPARQL_getLabelRelations(Config.SPARQL, SPARQL.labelRelations, laburi);
		}

    });
	
}

IO.sendSPARQL_getLabelRelations = function(url, query, laburi, mode) {
	
	query = query.replace("$label", laburi);
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
			
			var val = [];
			var rel = [];
			
			try {
				output = JSON.parse(output);
			} catch (e) {
				console.log(e);
			} finally {
			}
			var bindings = output.results.bindings; 
			
			if (bindings.length > 0) {
				
				for (var i=0; i<bindings.length; i++) {       
					
					var t = bindings[i];
					
					for(var key in t.s) {
						if (key == "value") {
							GLOBAL.selectedURL = t.s.value;
						}	
					}
					
					for(var key in t.o) {
						if (key == "value") {
							val[i] = t.o.value;
						}	
					}
					
					for(var key in t.p) {
						if (key == "value") {
							if (t.p.value.indexOf("http://www.w3.org/2004/02/skos/core#") != -1) {
								rel[i] = t.p.value.replace("http://www.w3.org/2004/02/skos/core#","skos:");
							} else if (t.p.value.indexOf("http://www.w3.org/2000/01/rdf-schema#") != -1) {
								rel[i] = t.p.value.replace("http://www.w3.org/2000/01/rdf-schema#","rdfs:");
							} else if (t.p.value.indexOf("http://www.w3.org/2002/07/owl#") != -1) {
								rel[i] = t.p.value.replace("http://www.w3.org/2002/07/owl#","owl:");
							} 
						}	
					}
					
					var x = document.getElementById("labelrelations");
					var option = document.createElement("option");
					option.text = rel[i]+" "+val[i];
					x.add(option);
					
				}
			} else {
				$('#reldel').hide();
			}
			
		}
	});
	
}

function deleteRelationQuestion(relation)
{
	var x;
	var r=confirm("Delete Label " + relation + " ?");
	
	if (r==true)
	{
		x="You pressed OK!";
		
		var tmp = relation.split(" ");
		
		var pred = "";
		
		if (tmp[0].indexOf("skos:") != -1) {
			pred = tmp[0].replace("skos:","http://www.w3.org/2004/02/skos/core#");
		} else if (tmp[0].indexOf("rdfs:") != -1) {
			pred = tmp[0].replace("rdfs:","http://www.w3.org/2000/01/rdf-schema#");
		} else if (tmp[0].indexOf("owl:") != -1) {
			pred = tmp[0].replace("owl:","http://www.w3.org/2002/07/owl#");
		}
		
		var reldel = "{ ";
		reldel += "<"+GLOBAL.selectedURL+"> ";
		reldel += "<"+pred+"> ";
		reldel += "<"+tmp[1]+"> ";
		reldel += ".";
		reldel += "}";
		
		if (pred.indexOf("narrower") != -1) {
			var reldel = "DELETE DATA { ";
			reldel += "<"+GLOBAL.selectedURL+"> ";
			reldel += "<"+pred+"> ";
			reldel += "<"+tmp[1]+"> ";
			reldel += ". ";
			reldel += "<"+tmp[1]+"> ";
			reldel += "<"+pred.replace("narrower","broader")+"> ";
			reldel += "<"+GLOBAL.selectedURL+"> ";
			reldel += ".";
			reldel += "}";
		} else if (pred.indexOf("broader") != -1) {
			var reldel = "DELETE DATA { ";
			reldel += "<"+GLOBAL.selectedURL+"> ";
			reldel += "<"+pred+"> ";
			reldel += "<"+tmp[1]+"> ";
			reldel += ". ";
			reldel += "<"+tmp[1]+"> ";
			reldel += "<"+pred.replace("broader","narrower")+"> ";
			reldel += "<"+GLOBAL.selectedURL+"> ";
			reldel += ".";
			reldel += "}";
		} else {
			var reldel = "DELETE DATA { ";
			reldel += "<"+GLOBAL.selectedURL+"> ";
			reldel += "<"+pred+"> ";
			reldel += "<"+tmp[1]+"> ";
			reldel += ".";
			reldel += "}";
		}
		
		IO.sendRelationDelete(Config.Update,reldel);
	}
	else
	{
		x="You pressed Cancel!";
	}

}

IO.sendRelationDelete = function(url, input, callback, info) {
    
    input = encodeURIComponent(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
			document.getElementById("info").innerHTML="";
			IO.sendSPARQL_LabelMetadata(Config.SPARQL,SPARQL.labmetadata);
        }
    });
}

/////////////
// updates //
/////////////

// IO.getinputconnectProjectVocabularyTriple --> IO.getURIVoc --> IO.getURIPro --> IO.sendUpdateCallback --> IO.sendSPARQL_VocabularyMetadata

IO.getinputconnectVocabularyLabelTriple = function() {
	
	IO.getURILab(Config.SPARQL, SPARQL.uriLab, "connect");
    
}

IO.getinputdisconnectVocabularyLabelTriple = function() {

	IO.getURILab(Config.SPARQL, SPARQL.uriLab, "disconnect");
	
}

IO.getURILab = function(url, query, mode) {
	
	if (document.getElementById('labellist').value.indexOf("@") > -1) {
	
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
				var laburi = output.results.bindings[0].s.value; 
				//console.log(laburi);
				
				IO.getURIVoc(Config.SPARQL, SPARQL.uriVoc, laburi, mode);

			}
		});
	} else {
		alert("no content!");
	}
}

IO.getURIVoc = function(url, query, laburi, mode) {
	
	if (mode == "connect") {
		
		var tmp = document.getElementById('vocabularylist').value.replace("@","__");
		
		if (tmp != "") {
		
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
					var vocuri = output.results.bindings[0].s.value;
					//console.log(vocuri);
					
					var v = document.getElementById('vocabularylist').value;
					var p = document.getElementById('labellist').value;
					if (v!=null && p!=null && v!="" && p!="") {
						var tri = "INSERT DATA { ";
						// sameAs REST-API
						tri += "<"+laburi+"> ";
						tri += Config.Ontology("sameAs",true);
						var vocid = vocuri.split("#")[1];
						var labid = laburi.split("#")[1];
						tri += Config.RestLabel(vocid,labid,true);
						tri += ". ";
						// internal links
						tri += "<"+vocuri+"> ";
						tri += Config.Ontology("contains",true);
						tri += "<"+laburi+">";
						tri += ". ";
						tri += "<"+laburi+"> ";
						tri += Config.Ontology("belongsTo",true);
						tri += "<"+vocuri+">";
						tri += ". ";
						tri += "<"+laburi+"> ";
						tri += "<http://www.w3.org/2004/02/skos/core#inScheme> ";
						tri += "<"+vocuri+">";
						tri += ". }";
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
	
	if (mode == "disconnect") {
		
		var tmp = document.getElementById('vocabularylist').value.replace("@","__");
		
		if (tmp != "") {
		
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
					var vocuri = output.results.bindings[0].s.value; 
					//console.log(vocuri);
					
					var v = document.getElementById('vocabularylist').value;
					var p = document.getElementById('labellist').value;
					if (v!=null && p!=null && v!="" && p!="") {
						var tri = "DELETE DATA { ";
						// sameAs REST-API
						tri += "<"+laburi+"> ";
						tri += Config.Ontology("sameAs",true);
						var vocid = vocuri.split("#")[1];
						var labid = laburi.split("#")[1];
						tri += Config.RestLabel(vocid,labid,true);
						tri += ". ";
						// internal links
						tri += "<"+vocuri+"> ";
						tri += Config.Ontology("contains",true);
						tri += "<"+laburi+">";
						tri += ". ";
						tri += "<"+laburi+"> ";
						tri += Config.Ontology("belongsTo",true);
						tri += "<"+vocuri+">";
						tri += ". ";
						tri += "<"+laburi+"> ";
						tri += "<http://www.w3.org/2004/02/skos/core#inScheme> ";
						tri += "<"+vocuri+">";
						tri += ". }";
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
	IO.sendSPARQLMyVocabularyList(Config.SPARQL,SPARQL.myvocabularies);
    IO.sendSPARQLMyLabelsList(Config.SPARQL,SPARQL.mylabels);
    fieldCheck();
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

function clearVocabularyList() {
    document.getElementById('vocabularylist').options.length = 0;
}

function fieldCheck() {
    
    var empty_l = false;
    
    if (document.getElementById("label").value != "") {
        empty_l = false;
    } else {
        empty_l = true;
    }
    
    if (empty_l == false) {
        document.getElementById("label").style.backgroundColor = '#C1FFC1'; //green
    } else {
		document.getElementById("label").style.backgroundColor = '#EEA9B8'; //red
	}
	
	if (empty_l == false) {
       document.getElementById("sendlabel").style.visibility = 'visible';
    } else {
        document.getElementById("sendlabel").style.visibility = 'hidden';
    }
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
            
            //console.log(output);
            
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
            
            //console.log(output);
			
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

IO.setRelID = function(concept, relation) {
	
	if (relation.indexOf("http://www.w3.org/2004/02/skos/core#") != -1) {
		document.getElementById("labelrelations").value = new String(relation.replace("http://www.w3.org/2004/02/skos/core#","skos:") + " " + concept);
	} else if (relation.indexOf("http://www.w3.org/2002/07/owl#") != -1) {
		document.getElementById("labelrelations").value = new String(relation.replace("http://www.w3.org/2002/07/owl#","owl:") + " " + concept)
	} else if (relation.indexOf("http://www.w3.org/2000/01/rdf-schema#") != -1) {
		document.getElementById("labelrelations").value = new String(relation.replace("http://www.w3.org/2000/01/rdf-schema#","rdfs:") + " " + concept);
	}
	
}

</script>
