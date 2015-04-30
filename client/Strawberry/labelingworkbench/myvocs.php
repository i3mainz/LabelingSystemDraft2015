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
	<h1>Create new Vocabulary / Connect to Project / Show and Hide</h1>
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
	<td><input type='button' value='Create' id='sendvocabulary' onclick='IO.getUUID(Config.UUID);'></td>
</tr>
<tr>
	<td><b>vocabulary name*&nbsp;&nbsp;&nbsp;</b></td>
	<td><input id='label' type='text' size='50' maxlength='50' onkeyup='fieldCheck()'></td>
	<td>
		<b>language </b><select id='languageLabel'></select>
	</td>
</tr>
<tr>
	<td><b>comment </b></td>
	<td><input id='comment' type='text' size='50' maxlength='256'></td>
	<td>
		<b>language </b><select id='languageComment'></select>
	</td>
</tr>
</table>
</center>
<br>


<h1>My vocabularies</h1>
<br>
<center>
<table border='0'>
	<tr>
	  <td>
		<span id='deletevocabulary'><i><a href='javaScript:deleteVocabularyQuestion(document.getElementById(\"vocabularylist\").value)'>Delete Vocabulary</a></i></span> 
		<span id='deletevocabulary'> | <i><a href='javaScript:ExportQuestion(document.getElementById(\"vocabularylist\").value,0)'>Export as RDF</a></i> | <i><a href='javaScript:ExportQuestion(document.getElementById(\"vocabularylist\").value,1)'>Export as CSV</a></i></span> 
	  </td>
	  <td>
		<b>My Projects</b>
	  </td>
	</tr>
	<tr>
	  <td><select id='vocabularylist' size='10' style='width: 500px;' onclick='IO.sendSPARQL_VocabularyMetadata(Config.SPARQL,SPARQL.vocmetadata)'></select></td>
	  <td><select id='projectlist' size='10' style='width: 500px;' onclick='IO.sendSPARQL_ProjectMetadata(Config.SPARQL,SPARQL.prometadata)'></select></td>
	</tr>
</table>
</center>
<br>  

<span id='myvocfunctions'>
<h1>Connect/Disconnect project and vocabulary</h1>
<br>
<center>
<span id='connect_pv'><a href='javaScript:IO.getinputconnectProjectVocabularyTriple()'>Connect Vocabulary to Project</a></span> | 
<span id='disconnect_pv'><a href='javaScript:IO.getinputdisconnectProjectVocabularyTriple()'>Disconnect Vocabulary from Project</a></span>
</center>
<br>

<h1>Publish/Hide vocabulary</h1>
<br>
<center>
<span id='publish_voc'><a href='javaScript:IO.getinputpublishVocabularyTriple()'>Publish Vocabulary</a></span> | 
<span id='hide_voc'><a href='javaScript:IO.getinputhideVocabularyTriple()'>Hide Vocabulary</a></span>
</center>
</span>
<br>

<center>
<span id='info'></span>
<br>
<input id='setpub' type='text' size='30' maxlength='30' disabled value='select vocabulary'>
<input id='setpro' type='text' size='30' maxlength='30' disabled value='select vocabulary'>
</center>
<br>

<center>
<span id='info2'></span>
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

var IO = {};
var TS = {};
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI("vocabularies");
});

////////////
// Create //
////////////

// IO.getUUID --> IO.labelCheck --> IO.sendVocabularyInput --> IO.sendSPARQLMyVocabularyList

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
			var c = document.getElementById('comment').value;
			var cl = document.getElementById('languageComment').value;
			
			//console.log(uuid);
			
			var vocabulary = "INSERT DATA { ";
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
			vocabulary += Config.Ontology("Vocabulary",true);
			vocabulary += ".";
			// dc creator
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/elements/1.1/creator> ";
			vocabulary += "\""+user+"\" ";
			vocabulary += ".";
			// dcterms creator
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/terms/creator> ";
			vocabulary += Config.Instance("agent",user,true);
			vocabulary += ".";
			// dcterms date
			var date = new Date();
			var d = date.toISOString();
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/terms/date> ";
			vocabulary += "\""+d+"\" ";
			vocabulary += ".";
			// dcterms licence
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/terms/licence> ";
			vocabulary += "<http://creativecommons.org/licenses/by/4.0/> ";
			vocabulary += ".";
			// rdfs label
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://www.w3.org/2000/01/rdf-schema#label> ";
			vocabulary += "\""+l+"\"@"+ll+" ";
			vocabulary += ".";
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/terms/title> ";
			vocabulary += "\""+l+"\"@"+ll+" ";
			vocabulary += ".";
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/terms/description> ";
			vocabulary += "\""+l+"\"@"+ll+" ";
			vocabulary += ".";
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://purl.org/dc/elements/1.1/language> ";
			vocabulary += "\""+ll+"\" ";
			vocabulary += ".";
			// ls identifier
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += Config.Ontology("identifier",true);
			vocabulary += "\""+uuid+"\" ";
			vocabulary += ".";
			//concept scheme
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
			vocabulary += "<http://www.w3.org/2004/02/skos/core#ConceptScheme> ";
			vocabulary += ".";
			if (document.getElementById('comment').value != "") {
				// rdfs comment
				vocabulary += Config.Instance("vocabulary",uuid,true);
				vocabulary += "<http://www.w3.org/2000/01/rdf-schema#comment> ";
				vocabulary += "\""+c+"\"@"+cl+" ";
				vocabulary += ".";
			}
			// sameAs REST-API
			vocabulary += Config.Instance("vocabulary",uuid,true);
			vocabulary += Config.Ontology("sameAs",true);
			vocabulary += "<"+Config.Rest_VOCABS+uuid+"> ";
			vocabulary += ".";
			//end
			vocabulary += "}";
			
			if (document.getElementById('label').value != "") {
				IO.labelCheck(Config.SPARQL, SPARQL.labelcheck_Voc, vocabulary);
			}
		}
		
	});
		
}


IO.labelCheck = function(url, query, vocabulary) {
	
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
		
			try {
				output = JSON.parse(output);
			} catch (e) {
				console.log(e);
			} finally {
			}
			
			var projecturi;
			
			try {
			
				try {
					output = JSON.parse(output);
				} catch (e) {
					console.log(e);
				} finally {
				}
				
				vocuri = output.results.bindings[0].s.value; 
				document.getElementById("label").style.backgroundColor = '#EEA9B8'; //red
				alert("Vocabulary with label " + tmp3 + " exists!" );
				
			} catch (e) {
			
				IO.sendVocabularyInput(Config.Update,vocabulary);
			
			}
			
        }
    });
}

IO.sendVocabularyInput = function(url, input) {
    
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
			document.getElementById('comment').value = "";
			fieldCheck();
            IO.sendSPARQLMyVocabularyList(Config.SPARQL,SPARQL.myvocabularies);
        }
    });
}

////////////
// Delete //
////////////

//deteleVocabularyQuestion--> IO.getURI --> IO.sendVocabularyDelete(2x) --> IO.sendSPARQLMyVocabularyList

function deleteVocabularyQuestion(vocab)
{
	if (document.getElementById('vocabularylist').value.indexOf("@") > -1) {
	
		var x;
		var r=confirm("Delete Vocabulary " + vocab + " ?");
		
		if (r==true)
		{
			x="You pressed OK!";
			IO.getURI(Config.SPARQL, SPARQL.uriVoc);
		}
		else
		{
			x="You pressed Cancel!";
		}
		//console.log(x);
		
	} else {
		alert('no content!');
	}
}

IO.getURI = function(url, query) {
	
	var tmp = document.getElementById('vocabularylist').value.replace("@","__");
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
			
			var vocuri = output.results.bindings[0].s.value; 
			//console.log(vocuri);
			
			var vocabulary = "DELETE WHERE { ";
			
			vocabulary += "<"+vocuri+"> ";
			vocabulary += "?p ";
			vocabulary += "?o ";
			vocabulary += ".";
			
			vocabulary += "}";
			
			var vocabulary2 = "DELETE WHERE { ";
			
			vocabulary2 += "?s ";
			vocabulary2 += "?v ";
			vocabulary2 += "<"+vocuri+"> ";
			vocabulary2 += ".";
		
			vocabulary2 += "}";
        
			if (document.getElementById('vocabularylist').value != "") {
				IO.sendVocabularyDelete(Config.Update,vocabulary,clearVocabularyList);
				IO.sendVocabularyDelete(Config.Update,vocabulary2,clearVocabularyList);
			} else {
				alert("no content!");
				return;
			}
			
        }
    });
}

IO.sendVocabularyDelete = function(url, input, callback, info) {
    
    input = encodeURIComponent(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            document.getElementById("setpub").style.backgroundColor = '#ffffff'; //white
			document.getElementById("setpub").value = 'select vocabulary';
			document.getElementById("setpro").style.backgroundColor = '#ffffff'; //white
			document.getElementById("setpro").value = 'select vocabulary';
			document.getElementById("info").innerHTML="";
			IO.sendSPARQLMyVocabularyList(Config.SPARQL,SPARQL.myvocabularies);
        }
    });
}

////////////
// Export //
////////////

//ExportQuestion--> IO.getVocURI

function ExportQuestion(vocab,mode)
{
	if (document.getElementById('vocabularylist').value.indexOf("@") > -1) {
	
		//var x;
		//var r=confirm("Delete Vocabulary " + vocab + " ?");
		
		//if (r==true)
		//{
			//x="You pressed OK!";
			IO.getVocURI(Config.SPARQL, SPARQL.uriVoc,mode);
		//}
		//else
		//{
			//x="You pressed Cancel!";
		//}
		//console.log(x);
		
	} else {
		alert('no content!');
	}
}

IO.getVocURI = function(url, query,mode) {
	
	var tmp = document.getElementById('vocabularylist').value.replace("@","__");
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
			var vocuri = output.results.bindings[0].s.value; 
			vocuri = vocuri.replace(Config.Instance_VOCABULARY,Config.Rest_VOCABS);
			if (mode==0) {
				vocuri += ".skos";
			} else if (mode==1) {
				vocuri += ".csv";
			}
			post_to_url_GET(vocuri);
        }
    });
}

///////////
// Lists //
///////////

IO.sendSPARQLMyVocabularyList = function(url, query, callback, info) {
	
		$('#deletevocabulary').show();
		$('#myvocfunctions').show();
			
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

IO.sendSPARQLVocabularyList = function(url, query, callback, info) {
	
	$('#deletevocabulary').hide();
	$('#myvocfunctions').hide();
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
            TS.bindings.length = 0;
			
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

IO.sendSPARQLMyProjectList = function(url, query, callback, info) {
    
    $('#deleteproject').show();
        
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
            
            clearProjectList();
			
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
				
				var x = document.getElementById("projectlist");
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
					var link_Pro = false;
					var link_REST = false;
					html_str += "<tr>";
					var identifier = "";
					var b1 = false;
					var b2 = false;
					
					for (var i=0; i<TS.output2.length; i++) {
						
						var split = TS.output2[i].split("__");
						
						if (split[1].indexOf("vocabulary#") != -1) {
							identifier = split[1].replace(Config.Instance_VOCABULARY,"");
							//console.log(identifier);
						} 
						
						if (link_Pro == false && split[1].indexOf("vocabulary#") == -1 && split[1].indexOf("rest") == -1) {
							if (split[1].indexOf(Config.Ontology("belongsTo",false)) != -1) {
								html_str += "<td style='background-color:#C1FFC1'>"+split[1]+"</td>";
								//console.info("<td style='background-color:#C1FFC1'>"+split[1]+"</td>");
								link_Pro = true;
							} else if (split[1].indexOf(Config.Ontology("sameAs",false)) != -1) {
								html_str += "<td style='background-color:#f2a83a'>"+split[1]+"</td>";
								//console.info("<td style='background-color:#f2a83a'>"+split[1]+"</td>");
								link_REST = true;
							} else {
								html_str += "<td>"+split[1]+"</td>";
								//console.log("<td>"+split[1]+"</td>");
							}
						} else if (link_Pro == true && split[1].indexOf("vocabulary#") == -1) {
							var tmpv = split[1].replace(Config.Instance_PROJECT,"");
							html_str += "<td style='background-color:#C1FFC1'><a href=\"javaScript:IO.sendSPARQL_setProID(Config.SPARQL, SPARQL.ProURItoLab, '"+split[1]+"')\">"+split[1]+"</a></td>";
							link_Pro = false;
						} else if (link_REST == true && link_Pro == false) {
							html_str += "<td style='background-color:#f2a83a'><a href=\""+split[1]+"\" target=\"_blank\">"+split[1]+"</a></td>";
							link_REST = false;
						} 
						
						if ((i+1)%TS.vars.length==0) {
							html_str += "</tr>";
							html_str += "<tr>";
						}
						
						var s1 = split[1];
						if (s1.indexOf(Config.Ontology("belongsTo",false))!=-1) {
							b1=true;
						}
						if (s1.indexOf("public")!=-1) {
							b2=true;
						}
					}
					
					
					if (b1){
						//console.log("project");
						document.getElementById("setpro").style.visibility = "";
						document.getElementById("setpro").style.backgroundColor = '#C1FFC1'; //green
						document.getElementById("setpro").value = "vocab connected to project";
					} else {
						//console.log("no-project");
						document.getElementById("setpro").style.visibility = "";
						document.getElementById("setpro").style.backgroundColor = '#EEA9B8'; //red
						document.getElementById("setpro").value = "vocab not connected to project";
					}
					if (b2){
						//console.log("publish");
						document.getElementById("setpub").style.visibility = "";
						document.getElementById("setpub").style.backgroundColor = '#C1FFC1'; //green
						document.getElementById("setpub").value = "vocabulary published";
					} else {
						//console.log("hidden");
						document.getElementById("setpub").style.visibility = "";
						document.getElementById("setpub").style.backgroundColor = '#EEA9B8'; //red
						document.getElementById("setpub").value = "vocabulary hidden";
					}
					
					
					// html output
					html_str += "</tr>";
					html_str += "</table>";

					document.getElementById("info").innerHTML=html_str;
					
				}
				
				
			});
			
			if (document.getElementById('projectlist').value != "") {
				IO.sendSPARQL_ProjectMetadata(Config.SPARQL,SPARQL.prometadata);
			}
		
		} else {
			alert("no content");
		}
	} else {
		alert('no content!');
	}
}

IO.sendSPARQL_ProjectMetadata = function(url, query, callback, info) {
    
    if (document.getElementById('projectlist').value.indexOf("@") > -1) {
	
		var tmp = document.getElementById('projectlist').value.replace("@","__");
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
					html_str += "<h1>Project - "+document.getElementById('projectlist').value+"</h1>";
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
					//html_str += "</tr>";
					
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
					var link_Voc = false;
					html_str += "<tr>";
					var identifier = "";
					
					for (var i=0; i<TS.output2.length; i++) {
						
						var split = TS.output2[i].split("__");
						
						if (split[1].indexOf("project#") != -1) {
							identifier = split[1].replace(Config.Instance_PROJECT,"");
							//console.log(identifier);
						} 
						
						if (link_Voc == false && split[1].indexOf("project#") == -1) {
							if (split[1].indexOf(Config.Ontology("contains",false)) != -1) {
								html_str += "<td style='background-color:#C1FFC1'>"+split[1]+"</td>";
								link_Voc = true;
							} else {
								html_str += "<td>"+split[1]+"</td>";
							}
						} else if (link_Voc == true && split[1].indexOf("project#") == -1) {
							var tmpv = split[1].replace(Config.Instance_VOCABULARY,"");
							html_str += "<td style='background-color:#C1FFC1'><a href=\"javaScript:IO.sendSPARQL_setVocID(Config.SPARQL, SPARQL.VocURItoLab, '"+split[1]+"')\">"+split[1]+"</a></td>";
							link_Voc = false;
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

/////////////
// updates //
/////////////

// IO.getinputconnectProjectVocabularyTriple --> IO.getURIVoc --> IO.getURIPro --> IO.sendUpdateCallback --> IO.sendSPARQL_VocabularyMetadata

IO.getinputconnectProjectVocabularyTriple = function() {
	
	IO.getURIVoc(Config.SPARQL, SPARQL.uriVoc, "connect");
    
}

IO.getinputdisconnectProjectVocabularyTriple = function() {

	IO.getURIVoc(Config.SPARQL, SPARQL.uriVoc, "disconnect");
	
}

IO.getinputpublishVocabularyTriple = function() {

	IO.getURIVoc(Config.SPARQL, SPARQL.uriVoc, "publish");

}

IO.getinputhideVocabularyTriple = function() {
	
	IO.getURIVoc(Config.SPARQL, SPARQL.uriVoc, "hide");

}

IO.getURIVoc = function(url, query, mode) {
	
	if (document.getElementById('vocabularylist').value.indexOf("@") > -1) {
	
		var tmp = document.getElementById('vocabularylist').value.replace("@","__");
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
				var vocuri = output.results.bindings[0].s.value; 
				
				if (mode == "connect") {
					IO.getURIPro(Config.SPARQL, SPARQL.uriPro, vocuri, mode);
				} else if (mode == "publish") {
					IO.getURIPro("", "", vocuri, mode);
				} else if (mode == "hide") {
					IO.getURIPro("", "", vocuri, mode);
				} else if (mode == "disconnect") {
					IO.getURIPro(Config.SPARQL, SPARQL.uriPro, vocuri, mode);
				}
				
			}
		});
	} else {
		alert("no content!");
	}
}

IO.getURIPro = function(url, query, vocuri, mode) {
	
	if (mode == "connect") {
		
		var tmp = document.getElementById('projectlist').value.replace("@","__");
		
		if (tmp != "") {
		
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
					var prouri = output.results.bindings[0].s.value; 
					
					var v = document.getElementById('vocabularylist').value;
					var p = document.getElementById('projectlist').value;
					if (v!=null && p!=null && v!="" && p!="") {
						var tri = "INSERT DATA { ";
						tri += "<"+prouri+"> ";
						tri += Config.Ontology("contains",true);
						tri += "<"+vocuri+">";
						tri += ". ";
						tri += "<"+vocuri+"> ";
						tri += Config.Ontology("belongsTo",true);
						tri += "<"+prouri+">";
						tri += ". }";
						IO.sendUpdateCallback(Config.Update,tri,IO.sendSPARQL_VocabularyMetadata);
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
		
		var tmp = document.getElementById('projectlist').value.replace("@","__");
		
		if (tmp != "") {
		
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
					
					var prouri = output.results.bindings[0].s.value;
					
					var v = document.getElementById('vocabularylist').value;
					var p = document.getElementById('projectlist').value;
					if (v!=null && p!=null && v!="" && p!="") {
						var tri = "DELETE DATA { ";
						tri += "<"+prouri+"> ";
						tri += Config.Ontology("contains",true);
						tri += "<"+vocuri+">";
						tri += ". ";
						tri += "<"+vocuri+"> ";
						tri += Config.Ontology("belongsTo",true);
						tri += "<"+prouri+">";
						tri += ". }";
						IO.sendUpdateCallback(Config.Update,tri,IO.sendSPARQL_VocabularyMetadata);
					} else {
						alert("no content!");
					}
				}
			});
		} else {
			alert("no content!");
		}
	
	}
	
	if (mode == "publish") {
	
		var v = document.getElementById('vocabularylist').value;
		if (v!=null && v!="") {
			var pub = "INSERT DATA { ";
			pub += "<"+vocuri+"> ";
			pub += Config.Ontology("state",true);
			pub += "\"public\"";
			pub += ". }";
			IO.sendUpdateCallback(Config.Update,pub,IO.sendSPARQL_VocabularyMetadata);
			} else {
				alert("no content!");
			}
	
	}
	
	if (mode == "hide") {
	
		var v = document.getElementById('vocabularylist').value;
		if (v!=null && v!="") {
			var pub = "DELETE DATA { ";
			pub += "<"+vocuri+"> ";
			pub += Config.Ontology("state",true);
			pub += "\"public\"";
			pub += ". }";
			IO.sendUpdateCallback(Config.Update,pub,IO.sendSPARQL_VocabularyMetadata);
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
            callback(Config.SPARQL,SPARQL.vocmetadata);
        }
    });
}

//////////
// more //
//////////

function init() {
    fieldCheck();
    IO.sendSPARQLMyProjectList(Config.SPARQL,SPARQL.myprojects);
    IO.sendSPARQLMyVocabularyList(Config.SPARQL,SPARQL.myvocabularies);
}

function clearVocabularyList() {
    document.getElementById('vocabularylist').options.length = 0;
}

function clearProjectList() {
    document.getElementById('projectlist').options.length = 0;
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
       document.getElementById("sendvocabulary").style.visibility = 'visible';
    } else {
        document.getElementById("sendvocabulary").style.visibility = 'hidden';
    }
	
}

function post_to_url_POST(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}

function post_to_url_GET(path, params, method) {
    method = method || "get"; // Set method to get by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }

    document.body.appendChild(form);
    form.submit();
}

//////

IO.sendSPARQL_setProID = function(url, query, uri) {
	
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
				
				document.getElementById("projectlist").value = val + "@" + lang;
				IO.sendSPARQL_ProjectMetadata(Config.SPARQL,SPARQL.prometadata);
				
			}
        }
    });
}

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
				
				//console.log(document.getElementById("vocabularylist").value = val + "@" + lang);
				document.getElementById("vocabularylist").value = val + "@" + lang;
				IO.sendSPARQL_VocabularyMetadata(Config.SPARQL,SPARQL.vocmetadata);
				
			}
        }
    });
}


</script>
