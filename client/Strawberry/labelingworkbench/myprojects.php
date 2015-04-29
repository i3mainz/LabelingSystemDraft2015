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
	<h1>Create new Project</h1>
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
	<td><input type='button' value='Create' id='sendproject' onclick='IO.getUUID(Config.UUID);'></td>
</tr>
<tr>
	<td><b>project name*&nbsp;&nbsp;&nbsp;</b></td>
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

<h1>My projects</h1>
<center>
<table border='0'>
	<tr>
	  <td>
		<!--<a href='javaScript:IO.sendSPARQLMyProjectList(Config.SPARQL,SPARQL.myprojects)'>My Projects</a> |-->
		<span id='deleteproject'><i><a href='javaScript:deleteProjectQuestion(document.getElementById(\"projectlist\").value)'>Delete Project</a></i></span>
		<!--<a href='javaScript:IO.sendSPARQLProjectList(Config.SPARQL,SPARQL.projects)'>All Projects</a>-->
	  </td>
	</tr>
	<tr>
	  <td><select id='projectlist' size='10' style='width: 500px;' onclick='IO.sendSPARQL_ProjectMetadata(Config.SPARQL,SPARQL.prometadata)'></select></td>
	</tr>
</table>
</center>
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

var IO = {};
var TS = {};
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI("project");
});

////////////
// Create //
////////////

// IO.getUUID --> IO.labelCheck --> IO.sendProjectInput --> IO.sendSPARQLMyProjectList

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
			
			var project = "INSERT DATA { ";
			project += Config.Instance("project",uuid,true);
			project += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
			project += Config.Ontology("Project",true);
			project += ".";
			// dc creator
			project += Config.Instance("project",uuid,true);
			project += "<http://purl.org/dc/elements/1.1/creator> ";
			project += "\""+user+"\" ";
			project += ".";
			// dcterms creator
			project += Config.Instance("project",uuid,true);
			project += "<http://purl.org/dc/terms/creator> ";
			project += Config.Instance("agent",user,true);
			project += ".";
			// dcterms date
			var date = new Date();
			var d = date.toISOString();
			project += Config.Instance("project",uuid,true);
			project += "<http://purl.org/dc/terms/date> ";
			project += "\""+d+"\" ";
			project += ".";
			// rdfs label
			project += Config.Instance("project",uuid,true);
			project += "<http://www.w3.org/2000/01/rdf-schema#label> ";
			project += "\""+l+"\"@"+ll+" ";
			project += ".";
			// ls identifier
			project += Config.Instance("project",uuid,true);
			project += Config.Ontology("identifier",true);
			project += "\""+uuid+"\" ";
			project += ".";
			if (document.getElementById('comment').value != "") {
				// rdfs comment
				project += Config.Instance("project",uuid,true);
				project += "<http://www.w3.org/2000/01/rdf-schema#comment> ";
				project += "\""+c+"\"@"+cl+" ";
				project += ".";
			}
			project += "}";
			
			if (document.getElementById('label').value != "") {
				IO.labelCheck(Config.SPARQL, SPARQL.labelcheck_Project, project);
			}
				
		}
		
		
	});
		
}

IO.labelCheck = function(url, query, project) {
	
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
			
				projecturi = output.results.bindings[0].s.value; 
				document.getElementById("label").style.backgroundColor = '#EEA9B8'; //red
				alert("Project with label " + tmp3 + " exists!" );
				
			} catch (e) {
			
				IO.sendProjectInput(Config.Update,project);
			
			}
			
        }
    });
}

IO.sendProjectInput = function(url, input) {
	
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
			IO.sendSPARQLMyProjectList(Config.SPARQL,SPARQL.myprojects);
        }
    });
}

////////////
// Delete //
////////////

//deteleProjectQuestion--> IO.getURI --> IO.sendProjectDelete(2x) --> IO.sendSPARQLMyProjectList

function deleteProjectQuestion(project)
{
	if (document.getElementById('projectlist').value.indexOf("@") > -1) {
	
		var x;
		var r=confirm("Delete Project " + project + " ?");
		
		if (r==true)
		{
			x="You pressed OK!";
			IO.getURI(Config.SPARQL, SPARQL.uri);
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
	
	var tmp = document.getElementById('projectlist').value.replace("@","__");
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
			
			var projecturi = output.results.bindings[0].s.value; 
			
			var project = "DELETE WHERE { ";
			project += "<"+projecturi+"> ";
			project += "?p ";
			project += "?o ";
			project += ".";
			project += "}";
			var project2 = "DELETE WHERE { ";
			project2 += "?s ";
			project2 += "?v ";
			project2 += "<"+projecturi+"> ";
			project2 += ".";
			project2 += "}";
        
			if (document.getElementById('projectlist').value != "") {
				IO.sendProjectDelete(Config.Update,project,clearProjectList);
				IO.sendProjectDelete(Config.Update,project2,clearProjectList);
			} else {
				alert("no content!");
				return;
			}
        }
    });
}

IO.sendProjectDelete = function(url, input, callback, info) {
    
    //input = mask(input);
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
			IO.sendSPARQLMyProjectList(Config.SPARQL,SPARQL.myprojects);
        }
    });
}

///////////
// Lists //
///////////

IO.sendSPARQLProjectList = function(url, query, callback, info) {
    
    $('#deleteproject').hide();
    //query = mask(query);
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

IO.sendSPARQLMyProjectList = function(url, query, callback, info) {
    
    $('#deleteproject').show();
        
    query = query.replace('$creator',user);
                        
    //query = mask(query);
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
					html_str += "<h1>Metadata - "+document.getElementById('projectlist').value+"</h1>";
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
					html_str += "<tr>";
					var identifier = "";
					for (var i=0; i<TS.output2.length; i++) {
						var split = TS.output2[i].split("__");
						if (split[1].indexOf("project#") != -1) {
							identifier = split[1].replace(Config.Instance_PROJECT,"");
							//console.log(identifier);
						} else {
							html_str += "<td>"+split[1]+"</td>";
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
		
		} else {
			alert("no content");
		}
	} else {
		alert("no content");
	}
}

//////////
// more //
//////////

function init() {
    fieldCheck();
    IO.sendSPARQLMyProjectList(Config.SPARQL,SPARQL.myprojects);
}

function clearProjectList() {
    document.getElementById('projectlist').options.length = 0;
}

function mask(text) {
    var replacer = new RegExp(" ", "g");
    text = text.replace(replacer, "%20");
    var replacer2 = new RegExp(":", "g");
    text = text.replace(replacer2, "%3A");
    var replacer5 = new RegExp("{", "g");
    text = text.replace(replacer5, "%7B");
    var replacer6 = new RegExp("}", "g");
    text = text.replace(replacer6, "%7D");
    var replacer7 = new RegExp("/", "g");
    text = text.replace(replacer7, "%2F");
    var replacer9 = new RegExp("<", "g");
    text = text.replace(replacer9, "%3C");
    var replacer10 = new RegExp(">", "g");
    text = text.replace(replacer10, "%3E");
    var replacer11 = new RegExp("#", "g");
    text = text.replace(replacer11, "%23");
    return text;
}

function fieldCheck() {
    
    var empty_l = false;
    var special = false;
    
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
       document.getElementById("sendproject").style.visibility = 'visible';
    } else {
        document.getElementById("sendproject").style.visibility = 'hidden';
    }
	
}

</script>