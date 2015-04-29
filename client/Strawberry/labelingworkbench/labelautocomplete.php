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
	<h1>Label Autocomplete with Filters</h1>
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

<center><b>
<div>
	<input type='radio' class='radio' name='searchmethod' value='all' onChange='disable();'> all labels
    <input type='radio' class='radio' name='searchmethod' value='creator' checked='checked' onChange='disable();'> creator filter
    <input type='radio' class='radio' name='searchmethod' value='vocabulary' onChange='disable();'> vocabulary filter
	<br><br>
	<select class='list' id='vocabularylist' size='10' style='width: 500px;'></select>
	<select class='list' id='creatorlist' size='10' style='width: 500px;'></select>
</div></b><hr width='90%' />
</center>

<div style='position: relative; height: 100px;'>
	<input type='text' class='search' id='autocomplete' style='position: absolute; z-index: 2; background: transparent;'/>
	<input type='text' class='search' id='autocomplete-hint' disabled='disabled' style='color: #AAA; position: absolute; background: #FFF; z-index: 1;'/>
	<input type='button' class='button' name='reset' value='reset label WITH vocabulary' onclick='resetautocomplete();' id='reset'>
</div>
<div style='position: relative; height: 100px;'>
	<input type='text' class='search' id='autocomplete2' style='position: absolute; z-index: 2; background: transparent;'/>
	<input type='text' class='search' id='autocomplete2-hint' disabled='disabled' style='color: #AAA; position: absolute; background: #FFF; z-index: 1;'/>
	<input type='button' class='button' name='reset' value='reset label WITHOUT vocabulary' onclick='resetautocomplete2();' id='reset2'>
</div>

<div id='tables'></div>

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
<script type="text/javascript" src="jquery.autocomplete.js"></script>
<script src="config.js"></script>
<script src="utils.js"></script>

<style type="text/css">

.container { width: 800px; margin: 0 auto; }

.autocomplete-suggestions { border: 1px solid #999; background: #FFF; cursor: default; overflow: auto; -webkit-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); -moz-box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); box-shadow: 1px 4px 3px rgba(50, 50, 50, 0.64); }
.autocomplete-suggestion { padding: 2px 5px; white-space: nowrap; overflow: hidden; }
.autocomplete-no-suggestion { padding: 2px 5px;}
.autocomplete-selected { background: #F0F0F0; }
.autocomplete-suggestions strong { font-weight: bold; color: #000; }

input.search { font-size: 28px; padding: 10px; border: 1px solid #888; display: relative; margin: 20px 20px; font-family: sans-serif; line-height: 1.6em; width: 800px;}

select.list { font-size: 16px; padding: 10px; border: 1px solid #CCC; display: relative; margin: 20px 20px; font-family: sans-serif; line-height: 1.6em;}
input.radio { padding: 10px; border: 1px solid #CCC; display: relative; margin: 10px 30px;}
input.button { font-size: 28px; padding: 8px; display: relative; margin: 20px 860px; font-family: sans-serif; line-height: 1.6em; color: #000;}

select.list[disabled] {
  background-color: #EBEBE4;
}

table { margin-left: 20px; margin-bottom: 20px;}

</style>

<script>

var vocabularyIdentifiers = {};

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	//
	document.getElementById('creatorlist').options.length = 0;
	// query creator of labels to fill the creator-list
	query = SPARQL.creatorsOfLabels;
	query = encodeURIComponent(query);
	$.ajax({
		type: 'GET',
		url: Config.SPARQL,
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
				var binding = bindings[i];
				var val = "";
				var lang = "";
				for(var key in binding.creator) {
					if (key == "value") {
						val = binding.creator.value;
					}
				}
				var x = document.getElementById("creatorlist");
				var option = document.createElement("option");
				option.text = val;
				x.add(option);
				val = "";
			}
			// init creator-list with active user
			document.getElementById("creatorlist").value = user;
			document.getElementById('creatorlist').disabled = false;
			document.getElementById('vocabularylist').disabled = true;
			
			//get vocabularylist
			document.getElementById('vocabularylist').options.length = 0;
			// query labels of published vocabularies and identifier of all vocabularies (hidden or not) to fill the list
			query = SPARQL.vocabularyLabelsAndIdentifier;
			query = encodeURIComponent(query);
			$.ajax({
				type: 'GET',
				url: Config.SPARQL,
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
						var binding = bindings[i];
						var val = "";
						var lang = "";
						for(var key in binding.vocabularylabel) {
							if (key == "value") {
								val = binding.vocabularylabel.value;
							}
							if (key == "xml:lang") {
								lang = binding.vocabularylabel['xml:lang'];
							}		
						}
						var x = document.getElementById("vocabularylist");
						var option = document.createElement("option");
						option.text = "\"" + val + "\"" + "@" + lang;
						x.add(option);
						// set hidden vocabulary identifier list
						for(var key in binding.vocabularyidentifier) {
							if (key == "value") {
								vocabularyIdentifiers["\"" + val + "\"" + "@" + lang] = binding.vocabularyidentifier.value;
							}	
						}
						// reset
						val = "";
						lang = "";
					}
					// init vocabulary-list with first entry
					document.getElementById("vocabularylist").selectedIndex = 0;
				}
			});
		}
	});
});

// Initialize ajax autocomplete
// more at http://www.devbridge.com/sourcery/components/jquery-autocomplete
$('#autocomplete').autocomplete({
	minChars: 2,
	showNoSuggestionNotice: true,
	noSuggestionNotice: 'Sorry, no matching results',
	//serviceUrl: Config.AutoComplete,
	serviceUrl: function () {
		var searchmethod = $('input[name="searchmethod"]:checked').val();
		if (searchmethod=="all") {
			return Config.AutoComplete;
		} else if (searchmethod=="creator") {
			return Config.AutoCompleteCreator.replace("$creator",document.getElementById("creatorlist").value);
		} if (searchmethod=="vocabulary") {
			return Config.AutoCompleteVocabulary.replace("$vocabulary",vocabularyIdentifiers[document.getElementById("vocabularylist").value]);
		}
	},
	lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
		var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
		return re.test(suggestion.value);
	},
	onSelect: function(suggestion) {
		//console.info(suggestion);
		/*console.info(suggestion.value);
		console.info(suggestion.data);
		console.info(suggestion.label);
		console.info(suggestion.creator);*/
		
		table1 = "";
		table1 += "<p><table border='1' width='90%' align='left'>";
		table1 += "<tr>";
		table1 += "<th colspan='2'>autocomplete label metadata</th>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label match</td><td>"+suggestion.value+"</td>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label identifier</td><td>"+suggestion.id+"</td>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label language</td><td>"+suggestion.prefLang+"</td>";
		table1 += "</tr>"
		table1 += "<tr>";
		table1 += "<td>label uri</td><td><a href='"+suggestion.label+"' target='_blank'>"+suggestion.label+"</a></td>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label creator</td><td>"+suggestion.creator+"</td>";
		table1 += "</tr>";
		for (var i=0; i<suggestion.concepts.length; i++) {
			table1 += "<tr>";
			table1 += "<td>concept</td><td><a href='"+suggestion.concepts[i].concept+"' target='_blank'>"+suggestion.concepts[i].concept+"</a></td>";
			table1 += "</tr>";
		}
		for (var i=0; i<suggestion.prefLabels.length; i++) {
			table1 += "<tr>";
			table1 += "<td>prefLabel</td><td>"+suggestion.prefLabels[i].prefLabel+"</td>";
			table1 += "</tr>";
		}
		try {
			for (var i=0; i<suggestion.altLabels.length; i++) {
				table1 += "<tr>";
				table1 += "<td>altLabel</td><td>"+suggestion.altLabels[i].altLabel+"</td>";
				table1 += "</tr>";
			}
		} catch (e) {
			console.log(e);
		} finally {
		}
		table1 += "</table></p>";
		document.getElementById('tables').innerHTML = table1;

		$.ajax({
			type: 'GET',
			url:  Config.JSONlabel,
			data: {id: suggestion.id},
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
				var data = output.data[0];
				console.info(data);

				table2 = "";
				table2 += "<p><table border='1' width='90%' align='left'>";
				table2 += "<tr>";
				table2 += "<th colspan='2'>label data</th>";
				table2 += "</tr>";
				
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>id</td><td>"+data.id+"</td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>url</td><td><a href='"+data.label+"' target='_blank'>"+data.label+"</a></td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>label language</td><td>"+data.prefLang+"</td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>creator</td><td>"+data.creator+"</td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>date</td><td>"+data.date+"</td>";
				table2 += "</tr>";
				for (var i = 0; i < data.concepts.length; i++) {
					table2 += "<tr style='background-color:#ccccee'>";
					table2 += "<td>concept</td><td><a href='"+data.concepts[i].concept+"' target='_blank'>"+data.concepts[i].concept+"</a></td>";
					table2 += "</tr>";
				}
				for (var i = 0; i < data.prefLabels.length; i++) {
					table2 += "<tr style='background-color:#eebbff'>";
					table2 += "<td>prefLabel</td><td>"+data.prefLabels[i].prefLabel+"</td>";
					table2 += "</tr>";
				}
				// optional values
				if (data.altLabels.length==0) {
					table2 += "<tr style='background-color:#ddff33'>";
					table2 += "<td>altLabel</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.altLabels.length; i++) {
						table2 += "<tr style='background-color:#ddff33'>";
						table2 += "<td>altLabel</td><td>"+data.altLabels[i].altLabel+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.notes.length==0) {
					table2 += "<tr style='background-color:#ddff33'>";
					table2 += "<td>note</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.notes.length; i++) {
						table2 += "<tr style='background-color:#ddff33'>";
						table2 += "<td>note</td><td>"+data.notes[i].note+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.definitions.length==0) {
					table2 += "<tr style='background-color:#ddff33'>";
					table2 += "<td>definition</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.definitions.length; i++) {
						table2 += "<tr style='background-color:#ddff33'>";
						table2 += "<td>definition</td><td>"+data.definitions[i].definition+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.broader.length==0) {
					table2 += "<tr style='background-color:#99ff00'>";
					table2 += "<td>broader concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.broader.length; i++) {
						table2 += "<tr style='background-color:#99ff00'>";
						table2 += "<td>broader concept</td><td>"+data.broader[i].broader+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.narrower.length==0) {
					table2 += "<tr style='background-color:#99ff00'>";
					table2 += "<td>narrower concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.narrower.length; i++) {
						table2 += "<tr style='background-color:#99ff00'>";
						table2 += "<td>narrower concept</td><td>"+data.narrower[i].narrower+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.related.length==0) {
					table2 += "<tr style='background-color:#99ff00'>";
					table2 += "<td>related concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.related.length; i++) {
						table2 += "<tr style='background-color:#99ff00'>";
						table2 += "<td>related concept</td><td>"+data.related[i].related+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.broadMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>broadMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.broadMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>broadMatch concept</td><td>"+data.broadMatch[i].broadMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.narrowMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>narrowMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.narrowMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>narrowMatch concept</td><td>"+data.narrowMatch[i].narrowMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.relatedMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>relatedMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.relatedMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>relatedMatch concept</td><td>"+data.relatedMatch[i].relatedMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.closeMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>closeMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.closeMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>closeMatch concept</td><td>"+data.closeMatch[i].closeMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.exactMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>exactMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.exactMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>exactMatch concept</td><td>"+data.exactMatch[i].exactMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.seeAlso.length==0) {
					table2 += "<tr style='background-color:#99ffff'>";
					table2 += "<td>seeAlso resource</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.seeAlso.length; i++) {
						table2 += "<tr style='background-color:#99ffff'>";
						table2 += "<td>seeAlso resource</td><td>"+data.seeAlso[i].seeAlso+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.sameAs.length==0) {
					table2 += "<tr style='background-color:#99ffff'>";
					table2 += "<td>sameAs resource</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.sameAs.length; i++) {
						table2 += "<tr style='background-color:#99ffff'>";
						table2 += "<td>sameAs resource</td><td>"+data.sameAs[i].sameAs+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.isDefinedBy.length==0) {
					table2 += "<tr style='background-color:#99ffff'>";
					table2 += "<td>isDefinedBy resource</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.isDefinedBy.length; i++) {
						table2 += "<tr style='background-color:#99ffff'>";
						table2 += "<td>isDefinedBy resource</td><td>"+data.isDefinedBy[i].isDefinedBy+"</td>";
						table2 += "</tr>";
					}
				}
				for (var i = 0; i < data.vocabularyIDs.length; i++) {
					table2 += "<tr style='background-color:#ffffff'>";
					table2 += "<td>related vocabulary</td><td><a href='"+Config.JSONvocabulary+"?id="+data.vocabularyIDs[i].vocabularyID+"' target='_blank'>"+data.vocabularyIDs[i].vocabularyID+"</a></td>";
					table2 += "</tr>";
				}
				
				table2 += "</table></p>";
				
				document.getElementById('tables').innerHTML += table2;
				
				/*for (var i = 0; i < bindings.length; i++) {
					// predicate					
					if (bindings[i].p) {
						predicate = bindings[i].p.value;
					}
					// object
					if (bindings[i].o.type == "uri") {
						object = bindings[i].o.value;
					} else if (bindings[i].o.type == "literal" && bindings[i].o['xml:lang']) {
						object = bindings[i].o.value + "@" + bindings[i].o['xml:lang'];
					} else if (bindings[i].o.type == "literal") {
						object = bindings[i].o.value;
					}
					
					if (predicate.indexOf("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")!=-1) {
						//table2 += "<tr>";
						//table2 += "<td>is a</td><td>"+object+"</td>";
						//table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#prefLabel")!=-1) {
						table2 += "<tr>";
						table2 += "<td>preference label</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#altLabel")!=-1) {
						table2 += "<tr>";
						table2 += "<td>alternative label</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#note")!=-1) {
						table2 += "<tr>";
						table2 += "<td>note</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#definition")!=-1) {
						table2 += "<tr>";
						table2 += "<td>definition</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://purl.org/dc/terms/creator")!=-1) {
						table2 += "<tr>";
						table2 += "<td>creator</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://purl.org/dc/terms/date")!=-1) {
						table2 += "<tr>";
						table2 += "<td>date</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://143.93.114.137/vocab#identifier")!=-1) {
						table2 += "<tr>";
						table2 += "<td>identifier</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#related")!=-1) {
						table2 += "<tr bgcolor='orange'>";
						table2 += "<td>skos:related</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#broader")!=-1) {
						table2 += "<tr bgcolor='orange'>";
						table2 += "<td>skos:broader</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#narrower")!=-1) {
						table2 += "<tr bgcolor='orange'>";
						table2 += "<td>skos:narrower</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#narrowMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:narrowMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#broadMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:broadMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#relatedMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:relatedMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#closeMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:closeMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#exactMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:exactMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2000/01/rdf-schema#seeAlso")!=-1) {
						table2 += "<tr bgcolor='lightblue'>";
						table2 += "<td>rdfs:seeAlso</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2000/01/rdf-schema#isDefinedBy")!=-1) {
						table2 += "<tr bgcolor='lightblue'>";
						table2 += "<td>owl:sameAs</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2002/07/owl#sameAs")!=-1) {
						table2 += "<tr bgcolor='lightblue'>";
						table2 += "<td>owl:sameAs</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else {
						//console.log(i+" -- "+predicate+" -- "+object);
					}
				}*/
				
			}
		});
	},
	onHint: function(hint) {
		$('#autocomplete-hint').val(hint);
	},
	onInvalidateSelection: function() {
		console.info('You selected: none');
	}
});

// Initialize ajax autocomplete
// more at http://www.devbridge.com/sourcery/components/jquery-autocomplete
$('#autocomplete2').autocomplete({
	minChars: 2,
	showNoSuggestionNotice: true,
	noSuggestionNotice: 'Sorry, no matching results',
	//serviceUrl: Config.AutoComplete,
	serviceUrl: function () {
		var searchmethod = $('input[name="searchmethod"]:checked').val();
		if (searchmethod=="all") {
			return Config.AutoComplete2;
		} else if (searchmethod=="creator") {
			return Config.AutoComplete2Creator.replace("$creator",document.getElementById("creatorlist").value);
		} if (searchmethod=="vocabulary") {
			return Config.AutoComplete2Vocabulary.replace("$vocabulary",vocabularyIdentifiers[document.getElementById("vocabularylist").value]);
		}
	},
	lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
		var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
		return re.test(suggestion.value);
	},
	onSelect: function(suggestion) {
		//console.info(suggestion);
		/*console.info(suggestion.value);
		console.info(suggestion.data);
		console.info(suggestion.label);
		console.info(suggestion.creator);*/
		
		table1 = "";
		table1 += "<p><table border='1' width='90%' align='left'>";
		table1 += "<tr>";
		table1 += "<th colspan='2'>autocomplete label metadata</th>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label match</td><td>"+suggestion.value+"</td>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label identifier</td><td>"+suggestion.id+"</td>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label language</td><td>"+suggestion.prefLang+"</td>";
		table1 += "</tr>"
		table1 += "<tr>";
		table1 += "<td>label uri</td><td><a href='"+suggestion.label+"' target='_blank'>"+suggestion.label+"</a></td>";
		table1 += "</tr>";
		table1 += "<tr>";
		table1 += "<td>label creator</td><td>"+suggestion.creator+"</td>";
		table1 += "</tr>";
		for (var i=0; i<suggestion.prefLabels.length; i++) {
			table1 += "<tr>";
			table1 += "<td>prefLabel</td><td>"+suggestion.prefLabels[i].prefLabel+"</td>";
			table1 += "</tr>";
		}
		try {
			for (var i=0; i<suggestion.altLabels.length; i++) {
				table1 += "<tr>";
				table1 += "<td>altLabel</td><td>"+suggestion.altLabels[i].altLabel+"</td>";
				table1 += "</tr>";
			}
		} catch (e) {
			console.log(e);
		} finally {
		}
		table1 += "</table></p>";
		document.getElementById('tables').innerHTML = table1;

		$.ajax({
			type: 'GET',
			url:  Config.JSONlabel,
			data: {id: suggestion.id},
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
				var data = output.data[0];
				console.info(data);

				table2 = "";
				table2 += "<p><table border='1' width='90%' align='left'>";
				table2 += "<tr>";
				table2 += "<th colspan='2'>label data</th>";
				table2 += "</tr>";
				
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>id</td><td>"+data.id+"</td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>url</td><td><a href='"+data.label+"' target='_blank'>"+data.label+"</a></td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>label language</td><td>"+data.prefLang+"</td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>creator</td><td>"+data.creator+"</td>";
				table2 += "</tr>";
				table2 += "<tr style='background-color:#ccccee'>";
				table2 += "<td>date</td><td>"+data.date+"</td>";
				table2 += "</tr>";
				for (var i = 0; i < data.prefLabels.length; i++) {
					table2 += "<tr style='background-color:#eebbff'>";
					table2 += "<td>prefLabel</td><td>"+data.prefLabels[i].prefLabel+"</td>";
					table2 += "</tr>";
				}
				// optional values
				if (data.altLabels.length==0) {
					table2 += "<tr style='background-color:#ddff33'>";
					table2 += "<td>altLabel</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.altLabels.length; i++) {
						table2 += "<tr style='background-color:#ddff33'>";
						table2 += "<td>altLabel</td><td>"+data.altLabels[i].altLabel+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.notes.length==0) {
					table2 += "<tr style='background-color:#ddff33'>";
					table2 += "<td>note</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.notes.length; i++) {
						table2 += "<tr style='background-color:#ddff33'>";
						table2 += "<td>note</td><td>"+data.notes[i].note+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.definitions.length==0) {
					table2 += "<tr style='background-color:#ddff33'>";
					table2 += "<td>definition</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.definitions.length; i++) {
						table2 += "<tr style='background-color:#ddff33'>";
						table2 += "<td>definition</td><td>"+data.definitions[i].definition+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.broader.length==0) {
					table2 += "<tr style='background-color:#99ff00'>";
					table2 += "<td>broader concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.broader.length; i++) {
						table2 += "<tr style='background-color:#99ff00'>";
						table2 += "<td>broader concept</td><td>"+data.broader[i].broader+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.narrower.length==0) {
					table2 += "<tr style='background-color:#99ff00'>";
					table2 += "<td>narrower concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.narrower.length; i++) {
						table2 += "<tr style='background-color:#99ff00'>";
						table2 += "<td>narrower concept</td><td>"+data.narrower[i].narrower+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.related.length==0) {
					table2 += "<tr style='background-color:#99ff00'>";
					table2 += "<td>related concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.related.length; i++) {
						table2 += "<tr style='background-color:#99ff00'>";
						table2 += "<td>related concept</td><td>"+data.related[i].related+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.broadMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>broadMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.broadMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>broadMatch concept</td><td>"+data.broadMatch[i].broadMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.narrowMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>narrowMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.narrowMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>narrowMatch concept</td><td>"+data.narrowMatch[i].narrowMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.relatedMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>relatedMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.relatedMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>relatedMatch concept</td><td>"+data.relatedMatch[i].relatedMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.closeMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>closeMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.closeMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>closeMatch concept</td><td>"+data.closeMatch[i].closeMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.exactMatch.length==0) {
					table2 += "<tr style='background-color:#99ffaa'>";
					table2 += "<td>exactMatch concept</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.exactMatch.length; i++) {
						table2 += "<tr style='background-color:#99ffaa'>";
						table2 += "<td>exactMatch concept</td><td>"+data.exactMatch[i].exactMatch+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.seeAlso.length==0) {
					table2 += "<tr style='background-color:#99ffff'>";
					table2 += "<td>seeAlso resource</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.seeAlso.length; i++) {
						table2 += "<tr style='background-color:#99ffff'>";
						table2 += "<td>seeAlso resource</td><td>"+data.seeAlso[i].seeAlso+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.sameAs.length==0) {
					table2 += "<tr style='background-color:#99ffff'>";
					table2 += "<td>sameAs resource</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.sameAs.length; i++) {
						table2 += "<tr style='background-color:#99ffff'>";
						table2 += "<td>sameAs resource</td><td>"+data.sameAs[i].sameAs+"</td>";
						table2 += "</tr>";
					}
				}
				if (data.isDefinedBy.length==0) {
					table2 += "<tr style='background-color:#99ffff'>";
					table2 += "<td>isDefinedBy resource</td><td><i>no value</i></td>";
					table2 += "</tr>";
				} else {
					for (var i = 0; i < data.isDefinedBy.length; i++) {
						table2 += "<tr style='background-color:#99ffff'>";
						table2 += "<td>isDefinedBy resource</td><td>"+data.isDefinedBy[i].isDefinedBy+"</td>";
						table2 += "</tr>";
					}
				}
				for (var i = 0; i < data.vocabularyIDs.length; i++) {
					table2 += "<tr style='background-color:#ffffff'>";
					table2 += "<td>related vocabulary</td><td><a href='"+Config.JSONvocabulary+"?id="+data.vocabularyIDs[i].vocabularyID+"' target='_blank'>"+data.vocabularyIDs[i].vocabularyID+"</a></td>";
					table2 += "</tr>";
				}
				
				table2 += "</table></p>";
				
				document.getElementById('tables').innerHTML += table2;
				
				/*for (var i = 0; i < bindings.length; i++) {
					// predicate					
					if (bindings[i].p) {
						predicate = bindings[i].p.value;
					}
					// object
					if (bindings[i].o.type == "uri") {
						object = bindings[i].o.value;
					} else if (bindings[i].o.type == "literal" && bindings[i].o['xml:lang']) {
						object = bindings[i].o.value + "@" + bindings[i].o['xml:lang'];
					} else if (bindings[i].o.type == "literal") {
						object = bindings[i].o.value;
					}
					
					if (predicate.indexOf("http://www.w3.org/1999/02/22-rdf-syntax-ns#type")!=-1) {
						//table2 += "<tr>";
						//table2 += "<td>is a</td><td>"+object+"</td>";
						//table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#prefLabel")!=-1) {
						table2 += "<tr>";
						table2 += "<td>preference label</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#altLabel")!=-1) {
						table2 += "<tr>";
						table2 += "<td>alternative label</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#note")!=-1) {
						table2 += "<tr>";
						table2 += "<td>note</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#definition")!=-1) {
						table2 += "<tr>";
						table2 += "<td>definition</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://purl.org/dc/terms/creator")!=-1) {
						table2 += "<tr>";
						table2 += "<td>creator</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://purl.org/dc/terms/date")!=-1) {
						table2 += "<tr>";
						table2 += "<td>date</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://143.93.114.137/vocab#identifier")!=-1) {
						table2 += "<tr>";
						table2 += "<td>identifier</td><td>"+object+"</td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#related")!=-1) {
						table2 += "<tr bgcolor='orange'>";
						table2 += "<td>skos:related</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#broader")!=-1) {
						table2 += "<tr bgcolor='orange'>";
						table2 += "<td>skos:broader</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#narrower")!=-1) {
						table2 += "<tr bgcolor='orange'>";
						table2 += "<td>skos:narrower</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#narrowMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:narrowMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#broadMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:broadMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#relatedMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:relatedMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#closeMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:closeMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2004/02/skos/core#exactMatch")!=-1) {
						table2 += "<tr bgcolor='lightgreen'>";
						table2 += "<td>skos:exactMatch</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2000/01/rdf-schema#seeAlso")!=-1) {
						table2 += "<tr bgcolor='lightblue'>";
						table2 += "<td>rdfs:seeAlso</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2000/01/rdf-schema#isDefinedBy")!=-1) {
						table2 += "<tr bgcolor='lightblue'>";
						table2 += "<td>owl:sameAs</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else if (predicate.indexOf("http://www.w3.org/2002/07/owl#sameAs")!=-1) {
						table2 += "<tr bgcolor='lightblue'>";
						table2 += "<td>owl:sameAs</td><td><a href='"+object+"' target='_blank'>"+object+"</a></td>";
						table2 += "</tr>";
					} else {
						//console.log(i+" -- "+predicate+" -- "+object);
					}
				}*/
				
			}
		});
	},
	onHint: function(hint) {
		$('#autocomplete2-hint').val(hint);
	},
	onInvalidateSelection: function() {
		console.info('You selected: none');
	}
});

function disable() {
	var searchmethod = $('input[name="searchmethod"]:checked').val();
	if (searchmethod=="all") {
		document.getElementById('creatorlist').disabled = true;
		document.getElementById('vocabularylist').disabled = true;
		document.getElementById('autocomplete2').disabled = false;
		document.getElementById('reset2').disabled = false;
	} else if (searchmethod=="creator") {
		document.getElementById('creatorlist').disabled = false;
		document.getElementById('vocabularylist').disabled = true;
		document.getElementById('autocomplete2').disabled = false;
		document.getElementById('reset2').disabled = false;
	} else if (searchmethod=="vocabulary") {
		document.getElementById('creatorlist').disabled = true;
		document.getElementById('vocabularylist').disabled = false;
		document.getElementById('autocomplete2').disabled = true;
		document.getElementById('reset2').disabled = true;
	} 
}

function resetautocomplete() {
	document.getElementById('autocomplete').value = "";
	document.getElementById('tables').innerHTML = "";
}
function resetautocomplete2() {
	document.getElementById('autocomplete2').value = "";
	document.getElementById('tables').innerHTML = "";
}


</script>