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
	<h1>Modify a Label</h1>
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
<b>Notes</b>
<br><br>
You can change your label if you add new prefLabels / altLabels / notes / definitions in other languages or delete an existing one. You can also change the prefered Language of a label. It is only possible to store a prefLabel appellation in one language one time. A double value is not allowed. You can also add or change the note or definition (for each language one of them). You can add or change the altLabels (max. 10) in all kind of language and appellation variants.
<br>
</i>
</p>
</info2>

<div style='position: relative; height: 100px;'>
	<input type='text' class='search' id='autocomplete2' style='position: absolute; z-index: 2; background: transparent;'/ onclick='reset(\"true\");'>
	<input type='text' class='search' id='autocomplete2-hint' disabled='disabled' style='color: #AAA; position: absolute; background: #FFF; z-index: 1;'/>
	<input type='button' class='button' name='reset' value='reset' onclick='reset(\"true\");'>
</div>

<br>
<center>
<p>
    <b>edit: </b>
	<input type='checkbox' id='prefLabel-modify' value='prefLabel' onchange='activateFields()' disabled> prefLabel &nbsp;
	<input type='checkbox' id='altLabel-modify' value='altLabel' onchange='activateFields()' disabled> altLabel &nbsp;
    <input type='checkbox' id='note-modify' value='note' onchange='activateFields()' disabled> note &nbsp;
    <input type='checkbox' id='definition-modify' value='definition' onchange='activateFields()' disabled> definition &nbsp;
	<input type='checkbox' id='prefLang-modify' value='prefLang' onchange='activateFields()' disabled> prefLang
</p>

<b>prefLabel:</b><br>
<input id='label-pl-de' type='text' size='100' value='' disabled /> @ de
<br>
<input id='label-pl-en' type='text' size='100' value='' disabled /> @ en
<br>
<input id='label-pl-fr' type='text' size='100' value='' disabled /> @ fr
<br>
<input id='label-pl-es' type='text' size='100' value='' disabled /> @ es
<br>
<input id='label-pl-it' type='text' size='100' value='' disabled /> @ it
<br>
<input id='label-pl-pl' type='text' size='100' value='' disabled /> @ pl
<br>
<input id='label-pl-de-x-orig' type='text' size='100' value='' disabled /> @ de-x-orig
<br>
<br><br>
<b>altLabel:</b> <input type='button' id='plus' value='+' onclick='addAltLabel()' disabled><br>
<div id='altLabelDIV'>
</div>
<br><br>
<b>note:</b><br>
<textarea id='label-note-de' cols='70' rows='2' text='' disabled /></textarea> @ de 
<br>
<textarea id='label-note-en' cols='70' rows='2' text='' disabled /></textarea> @ en 
<br>
<textarea id='label-note-fr' cols='70' rows='2' text='' disabled /></textarea> @ fr 
<br>
<textarea id='label-note-es' cols='70' rows='2' text='' disabled /></textarea> @ es 
<br>
<textarea id='label-note-it' cols='70' rows='2' text='' disabled /></textarea> @ it 
<br>
<textarea id='label-note-pl' cols='70' rows='2' text='' disabled /></textarea> @ pl 
<br>
<textarea id='label-note-de-x-orig' cols='70' rows='2' text='' disabled /></textarea> @ de-x-orig 
<br>
<br><br>
<b>definition:</b><br>
<textarea id='label-definition-de' cols='70' rows='2' text='' disabled /></textarea> @ de 
<br>
<textarea id='label-definition-en' cols='70' rows='2' text='' disabled /></textarea> @ en 
<br>
<textarea id='label-definition-fr' cols='70' rows='2' text='' disabled /></textarea> @ fr 
<br>
<textarea id='label-definition-es' cols='70' rows='2' text='' disabled /></textarea> @ es 
<br>
<textarea id='label-definition-it' cols='70' rows='2' text='' disabled /></textarea> @ it 
<br>
<textarea id='label-definition-pl' cols='70' rows='2' text='' disabled /></textarea> @ pl 
<br>
<textarea id='label-definition-de-x-orig' cols='70' rows='2' text='' disabled /></textarea> @ de-x-orig 
<br>
<br>
<b>prefLang:</b><br>
<input id='label-prefLang' type='text' size='10' value='' maxlength='2' disabled />
<br><br>
<input type='button' id='sendmodify' value='send changes' onclick='sendModify()'>
<br><br>
<hr width='90%'/>
<br>
<input id='deletelabels-input' type='text' size='100' value='' />
<input type='button' id='deletelabels-button' value='deletelabels' onclick='deleteLabels()'>
</center>
</span>
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

var oldvalues = {};
var feld = -1;
var maxfields = 10; // i=0

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

// Initialize ajax autocomplete
// more at http://www.devbridge.com/sourcery/components/jquery-autocomplete
$('#autocomplete2').autocomplete({
	minChars: 2,
	showNoSuggestionNotice: true,
	noSuggestionNotice: 'Sorry, no matching results',
	//serviceUrl: Config.AutoComplete,
	serviceUrl: function () {
		var searchmethod = $("creator").val();
			return Config.AutoComplete2Creator.replace("$creator",user);
	},
	lookupFilter: function(suggestion, originalQuery, queryLowerCase) {
		var re = new RegExp('\\b' + $.Autocomplete.utils.escapeRegExChars(queryLowerCase), 'gi');
		return re.test(suggestion.value);
	},
	onSelect: function(suggestion) {
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
					//console.info(output);
				}
				setFields(output);
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

function setFields(output) {
	// activate checkboxes
	document.getElementById("prefLabel-modify").disabled=false;
	document.getElementById("altLabel-modify").disabled=false;
	document.getElementById("note-modify").disabled=false;
	document.getElementById("definition-modify").disabled=false;
	document.getElementById("prefLang-modify").disabled=false;
	feld = 0;
	var data = output.data[0];
	//console.info(data);
	// init params
	oldvalues = {};
	document.getElementById("label-pl-de").value = "";
	document.getElementById("label-pl-en").value = "";
	document.getElementById("label-pl-fr").value = "";
	document.getElementById("label-pl-es").value = "";
	document.getElementById("label-pl-it").value = "";
	document.getElementById("label-pl-pl").value = "";
	document.getElementById("label-pl-de-x-orig").value = "";
	oldvalues['label-pl-de'] = "";
	oldvalues['label-pl-en'] = "";
	oldvalues['label-pl-fr'] = "";
	oldvalues['label-pl-es'] = "";
	oldvalues['label-pl-it'] = "";
	oldvalues['label-pl-pl'] = "";
	oldvalues['label-pl-de-x-orig'] = "";
	document.getElementById("label-note-de").value = "";
	document.getElementById("label-note-en").value = "";
	document.getElementById("label-note-fr").value = "";
	document.getElementById("label-note-es").value = "";
	document.getElementById("label-note-it").value = "";
	document.getElementById("label-note-pl").value = "";
	document.getElementById("label-note-de-x-orig").value = "";
	oldvalues['label-note-de'] = "";
	oldvalues['label-note-en'] = "";
	oldvalues['label-note-fr'] = "";
	oldvalues['label-note-es'] = "";
	oldvalues['label-note-it'] = "";
	oldvalues['label-note-pl'] = "";
	oldvalues['label-note-de-x-orig'] = "";
	document.getElementById("label-definition-de").value = "";
	document.getElementById("label-definition-en").value = "";
	document.getElementById("label-definition-fr").value = "";
	document.getElementById("label-definition-es").value = "";
	document.getElementById("label-definition-it").value = "";
	document.getElementById("label-definition-pl").value = "";
	document.getElementById("label-definition-de-x-orig").value = "";
	oldvalues['label-definition-de'] = "";
	oldvalues['label-definition-en'] = "";
	oldvalues['label-definition-fr'] = "";
	oldvalues['label-definition-es'] = "";
	oldvalues['label-definition-it'] = "";
	oldvalues['label-definition-pl'] = "";
	oldvalues['label-definition-de-x-orig'] = "";
	document.getElementById("label-prefLang").value = "";
	oldvalues['label-prefLang'] = "";
	oldvalues['label-creator'] = "";
	oldvalues['label-date'] = "";
	oldvalues['label-id'] = "";
	oldvalues['label-uri'] = "";
	// start parsing
	oldvalues['label-creator'] = data.creator;
	oldvalues['label-date'] = data.date;
	oldvalues['label-id'] = data.id;
	oldvalues['label-uri'] = data.label;
	for (var i=0; i<data.prefLabels.length; i++) {
		//console.info(data.prefLabels[i].prefLabel);
		if (data.prefLabels[i].prefLabel.indexOf("@de")!="-1") {
			//console.log(data.prefLabels[i].prefLabel.replace("@de","").replace(/"/g, ''));
			document.getElementById("label-pl-de").value = data.prefLabels[i].prefLabel.replace("@de","").replace(/"/g, '');
			oldvalues['label-pl-de'] = data.prefLabels[i].prefLabel.replace("@de","").replace(/"/g, '');
		}
		if (data.prefLabels[i].prefLabel.indexOf("@en")!="-1") {
			document.getElementById("label-pl-en").value = data.prefLabels[i].prefLabel.replace("@en","").replace(/"/g, '');
			oldvalues['label-pl-en'] = data.prefLabels[i].prefLabel.replace("@en","").replace(/"/g, '');
		}
		if (data.prefLabels[i].prefLabel.indexOf("@fr")!="-1") {
			document.getElementById("label-pl-fr").value = data.prefLabels[i].prefLabel.replace("@fr","").replace(/"/g, '');
			oldvalues['label-pl-fr'] = data.prefLabels[i].prefLabel.replace("@fr","").replace(/"/g, '');
		}
		if (data.prefLabels[i].prefLabel.indexOf("@es")!="-1") {
			document.getElementById("label-pl-es").value = data.prefLabels[i].prefLabel.replace("@es","").replace(/"/g, '');
			oldvalues['label-pl-es'] = data.prefLabels[i].prefLabel.replace("@es","").replace(/"/g, '');
		}
		if (data.prefLabels[i].prefLabel.indexOf("@it")!="-1") {
			document.getElementById("label-pl-it").value = data.prefLabels[i].prefLabel.replace("@it","").replace(/"/g, '');
			oldvalues['label-pl-it'] = data.prefLabels[i].prefLabel.replace("@it","").replace(/"/g, '');
		}
		if (data.prefLabels[i].prefLabel.indexOf("@pl")!="-1") {
			document.getElementById("label-pl-pl").value = data.prefLabels[i].prefLabel.replace("@pl","").replace(/"/g, '');
			oldvalues['label-pl-pl'] = data.prefLabels[i].prefLabel.replace("@pl","").replace(/"/g, '');
		}
		if (data.prefLabels[i].prefLabel.indexOf("@de-x-orig")!="-1") {
			document.getElementById("label-pl-de-x-orig").value = data.prefLabels[i].prefLabel.replace("@de-x-orig","").replace(/"/g, '');
			oldvalues['label-pl-de-x-orig'] = data.prefLabels[i].prefLabel.replace("@de-x-orig","").replace(/"/g, '');
		}
	}
	for (var i=0; i<data.notes.length; i++) {
		//console.info(data.notes[i].note);
		if (data.notes[i].note.indexOf("@de")!="-1") {
			document.getElementById("label-note-de").value = data.notes[i].note.replace("@de","").replace(/"/g, '');
			oldvalues['label-note-de'] = data.notes[i].note.replace("@de","").replace(/"/g, '');
		}
		if (data.notes[i].note.indexOf("@en")!="-1") {
			document.getElementById("label-note-en").value = data.notes[i].note.replace("@en","").replace(/"/g, '');
			oldvalues['label-note-en'] = data.notes[i].note.replace("@en","").replace(/"/g, '');
		}
		if (data.notes[i].note.indexOf("@fr")!="-1") {
			document.getElementById("label-note-fr").value = data.notes[i].note.replace("@fr","").replace(/"/g, '');
			oldvalues['label-note-fr'] = data.notes[i].note.replace("@fr","").replace(/"/g, '');
		}
		if (data.notes[i].note.indexOf("@es")!="-1") {
			document.getElementById("label-note-es").value = data.notes[i].note.replace("@es","").replace(/"/g, '');
			oldvalues['label-note-es'] = data.notes[i].note.replace("@es","").replace(/"/g, '');
		}
		if (data.notes[i].note.indexOf("@it")!="-1") {
			document.getElementById("label-note-it").value = data.notes[i].note.replace("@it","").replace(/"/g, '');
			oldvalues['label-note-it'] = data.notes[i].note.replace("@it","").replace(/"/g, '');
		}
		if (data.notes[i].note.indexOf("@pl")!="-1") {
			document.getElementById("label-note-pl").value = data.notes[i].note.replace("@pl","").replace(/"/g, '');
			oldvalues['label-note-pl'] = data.notes[i].note.replace("@pl","").replace(/"/g, '');
		}
		if (data.notes[i].note.indexOf("@de-x-orig")!="-1") {
			document.getElementById("label-note-de-x-orig").value = data.notes[i].note.replace("@de-x-orig","").replace(/"/g, '');
			oldvalues['label-note-de-x-orig'] = data.notes[i].note.replace("@de-x-orig","").replace(/"/g, '');
		}
	}
	for (var i=0; i<data.definitions.length; i++) {
		//console.info(data.definitions[i].definition);
		if (data.definitions[i].definition.indexOf("@de")!="-1") {
			document.getElementById("label-definition-de").value = data.definitions[i].definition.replace("@de","").replace(/"/g, '');
			oldvalues['label-definition-de'] = data.definitions[i].definition.replace("@de","").replace(/"/g, '');
		}
		if (data.definitions[i].definition.indexOf("@en")!="-1") {
			document.getElementById("label-definition-en").value = data.definitions[i].definition.replace("@en","").replace(/"/g, '');
			oldvalues['label-definition-en'] = data.definitions[i].definition.replace("@en","").replace(/"/g, '');
		}
		if (data.definitions[i].definition.indexOf("@fr")!="-1") {
			document.getElementById("label-definition-fr").value = data.definitions[i].definition.replace("@fr","").replace(/"/g, '');
			oldvalues['label-definition-fr'] = data.definitions[i].definition.replace("@fr","").replace(/"/g, '');
		}
		if (data.definitions[i].definition.indexOf("@es")!="-1") {
			document.getElementById("label-definition-es").value = data.definitions[i].definition.replace("@es","").replace(/"/g, '');
			oldvalues['label-definition-es'] = data.definitions[i].definition.replace("@es","").replace(/"/g, '');
		}
		if (data.definitions[i].definition.indexOf("@it")!="-1") {
			document.getElementById("label-definition-it").value = data.definitions[i].definition.replace("@it","").replace(/"/g, '');
			oldvalues['label-definition-it'] = data.definitions[i].definition.replace("@it","").replace(/"/g, '');
		}
		if (data.definitions[i].definition.indexOf("@pl")!="-1") {
			document.getElementById("label-definition-pl").value = data.definitions[i].definition.replace("@pl","").replace(/"/g, '');
			oldvalues['label-definition-pl'] = data.definitions[i].definition.replace("@pl","").replace(/"/g, '');
		}
		if (data.definitions[i].definition.indexOf("@de-x-orig")!="-1") {
			document.getElementById("label-definition-de-x-orig").value = data.definitions[i].definition.replace("@de-x-orig","").replace(/"/g, '');
			oldvalues['label-definition-de-x-orig'] = data.definitions[i].definition.replace("@de-x-orig","").replace(/"/g, '');
		}
	}
	document.getElementById("label-prefLang").value = data.prefLang;
	oldvalues['label-prefLang'] = data.prefLang;
	
	document.getElementById("altLabelDIV").innerHTML = "";
	for (var i=0; i<data.altLabels.length; i++) {
		var altLabel = data.altLabels[i].altLabel.split("@")[0];
		var language = data.altLabels[i].altLabel.split("@")[1];
		document.getElementById("altLabelDIV").innerHTML += "<p><input type='text' id='label-altLabel-" +feld+ "' value='" + altLabel.replace(/"/g, '') + "' disabled size='100'> @ <input id='label-altLabel-lang-"+feld+"' type='text' size='10' value='"+language+"' maxlength='10' disabled /></p>";
		oldvalues['label-altLabel-'+feld] = altLabel.replace(/"/g, '');
		oldvalues['label-altLabel-lang-'+feld] = language;
		feld++;
	}
	/*feld -= data.altLabels.length;
	for (var i=0; i<data.altLabels.length; i++) {
		var language = data.altLabels[i].altLabel.split("@")[1];
		document.getElementById("label-altLabel-lang-"+feld).value = language;
		oldvalues['label-altLabel-lang-'+feld] = language;
		feld++;
	}*/
}

function sendModify() {
	console.info("start modify label");
	// check if one prefLabel was choosen
	if (document.getElementById("label-pl-de").value=="" && document.getElementById("label-pl-en").value=="" && document.getElementById("label-pl-fr").value=="" && document.getElementById("label-pl-es").value=="" && document.getElementById("label-pl-it").value=="" && document.getElementById("label-pl-pl").value=="" && document.getElementById("label-prefLang").value=="" && document.getElementById("label-pl-de-x-orig").value=="") {
		reset(false);
		console.error("[ERROR] Value error!");
		alert("[ERROR] Value error!");
	} else {
		console.info("min. one prefLabel exists");
		query = SPARQL.checkPrefLabelLanguages;
		// check if a new prefLabel is created and if it exists
		if (document.getElementById("label-pl-de").value==oldvalues["label-pl-de"]) {
			query = query.replace('$de',"\"\"@de");
		} else {
			query = query.replace('$de',"\""+document.getElementById("label-pl-de").value+"\"@de");
		}
		if (document.getElementById("label-pl-en").value==oldvalues["label-pl-en"]) {
			query = query.replace('$en',"\"\"@en");
		} else {
			query = query.replace('$en',"\""+document.getElementById("label-pl-en").value+"\"@en");
		}
		if (document.getElementById("label-pl-fr").value==oldvalues["label-pl-fr"]) {
			query = query.replace('$fr',"\"\"@fr");
		} else {
			query = query.replace('$fr',"\""+document.getElementById("label-pl-fr").value+"\"@fr");
		}
		if (document.getElementById("label-pl-es").value==oldvalues["label-pl-es"]) {
			query = query.replace('$es',"\"\"@es");
		} else {
			query = query.replace('$es',"\""+document.getElementById("label-pl-es").value+"\"@es");
		}
		if (document.getElementById("label-pl-it").value==oldvalues["label-pl-it"]) {
			query = query.replace('$it',"\"\"@it");
		} else {
			query = query.replace('$it',"\""+document.getElementById("label-pl-it").value+"\"@it");
		}
		if (document.getElementById("label-pl-pl").value==oldvalues["label-pl-pl"]) {
			query = query.replace('$pl',"\"\"@pl");
		} else {
			query = query.replace('$pl',"\""+document.getElementById("label-pl-pl").value+"\"@pl");
		}
		if (document.getElementById("label-pl-de-x-orig").value==oldvalues["label-pl-de-x-orig"]) {
			query = query.replace('$de-x-orig',"\"\"@de-x-orig");
		} else {
			query = query.replace('$de-x-orig',"\""+document.getElementById("label-pl-de-x-orig").value+"\"@de-x-orig");
		}
		query = query.replace('$creator',user);
		console.info("query if new labels are existing");
		console.info(query);
		query = encodeURIComponent(query);  
		// check if a new prefLabel is created and if it exists (send query)
		$.ajax({
			type: 'GET',
			url: Config.SPARQL,
			data: {query: query, format: 'json'},
			error: function(jqXHR, textStatus, errorThrown) {
				reset(false);
				alert(errorThrown);
				console.error(errorThrown);
			},
			success: function(output) {
				var bindings = output.results.bindings; 
				if (output.results.bindings.length==0) { // label is new and does not exist
					update = SPARQLUPDATE.deleteLabelProperties;
					update = update.replace("$identifier","\""+oldvalues['label-id']+"\"");
					console.info("delete old information");
					console.info(update);
					update = encodeURIComponent(update);
					// send delete
					$.ajax({
						beforeSend: function(req) {
							req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
						},
						type: 'POST',
						url: Config.Update,
						data: {update: update},
						error: function(jqXHR, textStatus, errorThrown) {
							reset(false);
							console.error(errorThrown);
							alert(errorThrown);
						},
						success: function(output) {
							// create new triples
							update = SPARQLUPDATE.insertLabelProperties;
							var prefLabel = "";
							var altLabel = "";
							var note = "";
							var definition = "";
							var prefLang = "";
							// prefLang
							if (document.getElementById("label-prefLang").value!="") {
								var pl = document.getElementById("label-prefLang").value;
								if (document.getElementById("label-pl-"+pl).value != "") {
									prefLang += "<"+oldvalues['label-uri']+"> "+ Config.Ontology("prefLang",true) +" \""+document.getElementById("label-prefLang").value+"\" .";
								} else {
									// defaultvalue english
									prefLang += "<"+oldvalues['label-uri']+">" + Config.Ontology("prefLang",true) + "\""+"en"+"\"";
								}
							}
							// prefLabel
							if (document.getElementById("label-pl-de").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-de").value+"\"@de .";
							}
							if (document.getElementById("label-pl-en").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-en").value+"\"@en .";
							}
							if (document.getElementById("label-pl-fr").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-fr").value+"\"@fr .";
							}
							if (document.getElementById("label-pl-es").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-es").value+"\"@es .";
							}
							if (document.getElementById("label-pl-it").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-it").value+"\"@it .";
							}
							if (document.getElementById("label-pl-pl").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-pl").value+"\"@pl .";
							}
							if (document.getElementById("label-pl-de-x-orig").value!="") {
								prefLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#prefLabel> \""+document.getElementById("label-pl-de-x-orig").value+"\"@de-x-orig .";
							}
							// altLabels
							for (var i=0; i<maxfields; i++) {
								try {
									if (document.getElementById("label-altLabel-"+i).value!="") {
										altLabel += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#altLabel> \""+document.getElementById("label-altLabel-"+i).value+"\"@"+document.getElementById("label-altLabel-lang-"+i).value+" .";
									}
								} catch (e) {
								} finally {
								}
							}
							// notes
							if (document.getElementById("label-note-de").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-de").value+"\"@de .";
							}
							if (document.getElementById("label-note-en").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-en").value+"\"@en .";
							}
							if (document.getElementById("label-note-fr").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-fr").value+"\"@fr .";
							}
							if (document.getElementById("label-note-es").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-es").value+"\"@es .";
							}
							if (document.getElementById("label-note-it").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-it").value+"\"@it .";
							}
							if (document.getElementById("label-note-pl").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-pl").value+"\"@pl .";
							}
							if (document.getElementById("label-note-de-x-orig").value!="") {
								note += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#note> \""+document.getElementById("label-note-de-x-orig").value+"\"@de-x-orig .";
							}
							// definitions
							if (document.getElementById("label-definition-de").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-de").value+"\"@de .";
							}
							if (document.getElementById("label-definition-en").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-en").value+"\"@en .";
							}
							if (document.getElementById("label-definition-fr").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-fr").value+"\"@fr .";
							}
							if (document.getElementById("label-definition-es").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-es").value+"\"@es .";
							}
							if (document.getElementById("label-definition-it").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-it").value+"\"@it .";
							}
							if (document.getElementById("label-definition-pl").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-pl").value+"\"@pl .";
							}
							if (document.getElementById("label-definition-de-x-orig").value!="") {
								definition += "<"+oldvalues['label-uri']+"> <http://www.w3.org/2004/02/skos/core#definition> \""+document.getElementById("label-definition-de-x-orig").value+"\"@de-x-orig .";
							}
							// replace values
							update = update.replace("$prefLabel",prefLabel);
							update = update.replace("$altLabel",altLabel);
							update = update.replace("$note",note);
							update = update.replace("$definition",definition);
							update = update.replace("$prefLang",prefLang);
							console.info("send new information");
							console.info(update);
							update = encodeURIComponent(update);
							// send insert
							$.ajax({
								beforeSend: function(req) {
									req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
								},
								type: 'POST',
								url: Config.Update,
								data: {update: update},
								error: function(jqXHR, textStatus, errorThrown) {
									reset(false);
									console.error(errorThrown);
									alert(errorThrown);
								},
								success: function(output) {
									reset(false);
									console.info("label changed!");
									alert("label changed!");
									// get label information 
									$.ajax({
										type: 'GET',
										url:  Config.JSONlabel,
										data: {id: oldvalues['label-id']},
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
											setFields(output);
										}
									});
								}
							});	
						}
					});			
				} else { // label exists
					reset(false);
					console.error("[ERROR] label exists!");
					alert("[ERROR] label exists!");
					// get label information 
					$.ajax({
						type: 'GET',
						url:  Config.JSONlabel,
						data: {id: oldvalues['label-id']},
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
							setFields(output);
						}
					});
				}
			}
		});
	}
}

function activateFields() {
	//true
	if (document.getElementById("prefLabel-modify").checked==true) {
		document.getElementById("label-pl-de").disabled = false;
		document.getElementById("label-pl-en").disabled = false;
		document.getElementById("label-pl-fr").disabled = false;
		document.getElementById("label-pl-es").disabled = false;
		document.getElementById("label-pl-it").disabled = false;
		document.getElementById("label-pl-pl").disabled = false;
		document.getElementById("label-pl-de-x-orig").disabled = false;
	} 
	if (document.getElementById("altLabel-modify").checked==true) {
		document.getElementById("plus").disabled = false;
		try {
			document.getElementById("label-altLabel-0").disabled = false;
			document.getElementById("label-altLabel-lang-0").disabled = false;
			document.getElementById("label-altLabel-1").disabled = false;
			document.getElementById("label-altLabel-lang-1").disabled = false;
			document.getElementById("label-altLabel-2").disabled = false;
			document.getElementById("label-altLabel-lang-2").disabled = false;
			document.getElementById("label-altLabel-3").disabled = false;
			document.getElementById("label-altLabel-lang-3").disabled = false;
			document.getElementById("label-altLabel-4").disabled = false;
			document.getElementById("label-altLabel-lang-4").disabled = false;
			document.getElementById("label-altLabel-5").disabled = false;
			document.getElementById("label-altLabel-lang-5").disabled = false;
			document.getElementById("label-altLabel-6").disabled = false;
			document.getElementById("label-altLabel-lang-6").disabled = false;
			document.getElementById("label-altLabel-7").disabled = false;
			document.getElementById("label-altLabel-lang-7").disabled = false;
			document.getElementById("label-altLabel-8").disabled = false;
			document.getElementById("label-altLabel-lang-8").disabled = false;
			document.getElementById("label-altLabel-9").disabled = false;
			document.getElementById("label-altLabel-lang-9").disabled = false;
		} catch (e) {
		}
	}
	if (document.getElementById("note-modify").checked==true) {
		document.getElementById("label-note-de").disabled = false;
		document.getElementById("label-note-en").disabled = false;
		document.getElementById("label-note-fr").disabled = false;
		document.getElementById("label-note-es").disabled = false;
		document.getElementById("label-note-it").disabled = false;
		document.getElementById("label-note-pl").disabled = false;
		document.getElementById("label-note-de-x-orig").disabled = false;
	}
	if (document.getElementById("definition-modify").checked==true) {
		document.getElementById("label-definition-de").disabled = false;
		document.getElementById("label-definition-en").disabled = false;
		document.getElementById("label-definition-fr").disabled = false;
		document.getElementById("label-definition-es").disabled = false;
		document.getElementById("label-definition-it").disabled = false;
		document.getElementById("label-definition-pl").disabled = false;
		document.getElementById("label-definition-de-x-orig").disabled = false;
	}
	if (document.getElementById("prefLang-modify").checked==true) {
		document.getElementById("label-prefLang").disabled = false;
	}
	// false
	if (document.getElementById("prefLabel-modify").checked==false) {
		document.getElementById("label-pl-de").disabled = true;
		document.getElementById("label-pl-en").disabled = true;
		document.getElementById("label-pl-fr").disabled = true;
		document.getElementById("label-pl-es").disabled = true;
		document.getElementById("label-pl-it").disabled = true;
		document.getElementById("label-pl-pl").disabled = true;
		document.getElementById("label-pl-de-x-orig").disabled = true;
	} 
	if (document.getElementById("altLabel-modify").checked==false) {
		document.getElementById("plus").disabled = true;
		try {
			document.getElementById("label-altLabel-0").disabled = true;
			document.getElementById("label-altLabel-lang-0").disabled = true;
			document.getElementById("label-altLabel-1").disabled = true;
			document.getElementById("label-altLabel-lang-1").disabled = true;
			document.getElementById("label-altLabel-2").disabled = true;
			document.getElementById("label-altLabel-lang-2").disabled = true;
			document.getElementById("label-altLabel-3").disabled = true;
			document.getElementById("label-altLabel-lang-3").disabled = true;
			document.getElementById("label-altLabel-4").disabled = true;
			document.getElementById("label-altLabel-lang-4").disabled = true;
			document.getElementById("label-altLabel-5").disabled = true;
			document.getElementById("label-altLabel-lang-5").disabled = true;
			document.getElementById("label-altLabel-6").disabled = true;
			document.getElementById("label-altLabel-lang-6").disabled = true;
			document.getElementById("label-altLabel-7").disabled = true;
			document.getElementById("label-altLabel-lang-7").disabled = true;
			document.getElementById("label-altLabel-8").disabled = true;
			document.getElementById("label-altLabel-lang-8").disabled = true;
			document.getElementById("label-altLabel-9").disabled = true;
			document.getElementById("label-altLabel-lang-9").disabled = true;
		} catch (e) {
		}
	}
	if (document.getElementById("note-modify").checked==false) {
		document.getElementById("label-note-de").disabled = true;
		document.getElementById("label-note-en").disabled = true;
		document.getElementById("label-note-fr").disabled = true;
		document.getElementById("label-note-es").disabled = true;
		document.getElementById("label-note-it").disabled = true;
		document.getElementById("label-note-pl").disabled = true;
		document.getElementById("label-note-de-x-orig").disabled = true;
	} 
	if (document.getElementById("definition-modify").checked==false) {
		document.getElementById("label-definition-de").disabled = true;
		document.getElementById("label-definition-en").disabled = true;
		document.getElementById("label-definition-fr").disabled = true;
		document.getElementById("label-definition-es").disabled = true;
		document.getElementById("label-definition-it").disabled = true;
		document.getElementById("label-definition-pl").disabled = true;
		document.getElementById("label-definition-de-x-orig").disabled = true;
	}
	if (document.getElementById("prefLang-modify").checked==false) {
		document.getElementById("label-prefLang").disabled = true;
	}
}

function reset(autocompletevalue) {
	if(autocompletevalue) {
		document.getElementById('autocomplete2').value = "";
	}
	// prefLang
	document.getElementById("label-prefLang").value = "";
	// prefLabel
	document.getElementById("label-pl-de").value = "";
	document.getElementById("label-pl-en").value = "";
	document.getElementById("label-pl-fr").value = "";
	document.getElementById("label-pl-es").value = "";
	document.getElementById("label-pl-it").value = "";
	document.getElementById("label-pl-pl").value = "";
	document.getElementById("label-pl-de-x-orig").value = "";
	// definitions
	document.getElementById("label-definition-de").value = "";
	document.getElementById("label-definition-en").value = "";
	document.getElementById("label-definition-fr").value = "";
	document.getElementById("label-definition-es").value = "";
	document.getElementById("label-definition-it").value = "";
	document.getElementById("label-definition-pl").value = "";
	document.getElementById("label-definition-de-x-orig").value = "";
	// notes
	document.getElementById("label-note-de").value = "";
	document.getElementById("label-note-en").value = "";
	document.getElementById("label-note-fr").value = "";
	document.getElementById("label-note-es").value = "";
	document.getElementById("label-note-it").value = "";
	document.getElementById("label-note-pl").value = "";
	document.getElementById("label-note-de-x-orig").value = "";
	// altLabels
	document.getElementById("altLabelDIV").innerHTML = "";
	// checkboxes
	document.getElementById("prefLabel-modify").checked=false;
	document.getElementById("altLabel-modify").checked=false;
	document.getElementById("note-modify").checked=false;
	document.getElementById("definition-modify").checked=false;
	document.getElementById("prefLang-modify").checked=false;
	document.getElementById("prefLabel-modify").disabled=true;
	document.getElementById("altLabel-modify").disabled=true;
	document.getElementById("note-modify").disabled=true;
	document.getElementById("definition-modify").disabled=true;
	document.getElementById("prefLang-modify").disabled=true;
	activateFields();
}

function addAltLabel() {
	if(feld<maxfields) {
		document.getElementById("altLabelDIV").innerHTML += "<p><input type='text' id='label-altLabel-" +feld+ "' value='' size='100'> @ <input id='label-altLabel-lang-"+feld+"' type='text' size='10' value='' maxlength='10' /></p>";
		oldvalues['label-altLabel-'+feld] = "";
		oldvalues['label-altLabel-lang-'+feld] = "";
		feld++;
	} else {
		alert("to many altlabels");
	}
	for (var i=0; i<feld; i++) {
		try {
			document.getElementById("label-altLabel-lang-"+i).value = oldvalues['label-altLabel-lang-'+i];
		} catch (e) {
		} finally {
		}
	}
}

function deleteLabels() {
	$.ajax({
		type: 'POST',
		url:  Config.DeleteLabels,
		data: {ids: document.getElementById("deletelabels-input").value},
		error: function(jqXHR, textStatus, errorThrown) {
			document.getElementById("deletelabels-input").style.backgroundColor = 'lightred';
			alert(errorThrown);
		},
		success: function(output) {
			console.info("delete sucessful: "+document.getElementById("deletelabels-input").value);
			alert("delete sucessful: "+document.getElementById("deletelabels-input").value);
			document.getElementById("deletelabels-input").value = "";
			document.getElementById("deletelabels-input").style.backgroundColor = 'lightgreen';
		}
	});
}

</script>