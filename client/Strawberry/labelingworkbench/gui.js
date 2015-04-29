// Namespaces
LS = {};
LS.TERM = {};
LS.GUI = {};
// Variables
LS.GUI.id = null;
LS.GUI.creator = null;
LS.GUI.comment = null;
LS.GUI.label = null;
LS.GUI.prefLang = null;
LS.GUI.menuLang = null;
// Getter
LS.GUI.getId = function() {
	return LS.GUI.id;
}
LS.GUI.getCreator = function() {
	return LS.GUI.creator;
}
LS.GUI.getComment = function() {
	return LS.GUI.comment;
}
LS.GUI.getLabel = function() {
	return LS.GUI.label;
}
LS.GUI.getPrefLang = function() {
	return LS.GUI.prefLang;
}
LS.GUI.getMenuLang = function() {
	return LS.GUI.menuLang;
}
// Setter
LS.GUI.setId = function(ID) {
	LS.GUI.id = ID;
}
LS.GUI.setCreator = function(CREATOR) {
	LS.GUI.creator = CREATOR;
}
LS.GUI.setComment = function(COMMENT) {
	LS.GUI.comment = COMMENT;
}
LS.GUI.setLabel = function(LABEL) {
	LS.GUI.label = LABEL;
}
LS.GUI.setPrefLang = function(PREFLANG) {
	LS.GUI.prefLang = PREFLANG;
}
LS.GUI.setMenuLang = function(MENULANG) {
	LS.GUI.menuLang = MENULANG;
}
// Set Select Element for Language
LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement = function(SELECTID) {
	// GET ELEMENT
	var select = document.getElementById(SELECTID);
	// ADD OPTIONS
	var option1 = document.createElement("option");
	option1.text = "english";
	option1.value = "en";
	select.add(option1);
	var option2 = document.createElement("option");
	option2.text = "deutsch";
	option2.value = "de";
	select.add(option2);
	var option3 = document.createElement("option");
	option3.text = "français";
	option3.value = "fr";
	select.add(option3);
	var option4 = document.createElement("option");
	option4.text = "español";
	option4.value = "es";
	select.add(option4);
	var option5 = document.createElement("option");
	option5.text = "italiano";
	option5.value = "it";
	select.add(option5);
	var option6 = document.createElement("option");
	option6.text = "polski";
	option6.value = "pl";
	select.add(option6);
	var option7 = document.createElement("option");
	option7.text = "de-x-orig";
	option7.value = "de-x-orig";
	select.add(option7);
	// SELECT
	for (var i = 0; i < select.options.length; i++) {
		if (select.options[i].text === LS.GUI.prefLang) {
			select.selectedIndex = i;
			break;
		}
	}
}

// terms in 5 languages [de;en;fr;es;it]
// overall functions //
LS.TERM.t_f_overall = ["Allgemeine Funktionen","Overall Functions","","",""];
LS.TERM.t_home = ["Home","home","","",""];
LS.TERM.t_user_settings = ["Passwort ändern","change password","","",""];
LS.TERM.t_user_metadata = ["Benutzer Metadaten","user metadata","","",""];
LS.TERM.t_user_gui = ["Benutzer GUI wechseln","change user gui","","",""];
LS.TERM.t_user_guimod = ["Benutzer GUI modifizieren","modify user gui","","",""];
LS.TERM.t_user_guicreate = ["Benutzer GUI erstellen","create user gui","","",""];
LS.TERM.t_documentation = ["Dokumentation","documentation","","",""];
LS.TERM.t_languages = ["Sprachabkürzungen","language overview","","",""];
LS.TERM.t_logout = ["Logout","logout","","",""];
LS.TERM.t_test = ["Testbereich","testarea","","",""];
LS.TERM.t_logs = ["100 letzte logs","100 last logs","","",""];
LS.TERM.t_template = ["Template","template","","",""];
LS.TERM.t_dump = ["Download RDF Dump","download RDF dump","","",""];
LS.TERM.t_dump2 = ["Download RDF Dump Concepts","download RDF dump concepts","","",""];
// user functions
LS.TERM.t_f_user = ["User Funktionen","User Functions","","",""];
LS.TERM.t_create = ["Erstellung und Modifizierung","create and modify","","",""];
LS.TERM.t_projects = ["Projekte","my projects","","",""];
LS.TERM.t_vocabs = ["Vokabulare","my vocabularies","","",""];
LS.TERM.t_labels = ["Label","my labels","","",""];
LS.TERM.t_modlabels = ["Label modifizieren","modify label","","",""];
LS.TERM.t_csvlabels = ["Label CSV-Upload","label CSV upload","","",""];
LS.TERM.t_linking = ["Verlinken","linking","","",""];
LS.TERM.t_linksparql = ["Link Label (SPARQL)","link label (SPARQL)","","",""];
LS.TERM.t_linkresource = ["Link Label (Resource)","link label (ressource)","","",""];
LS.TERM.t_linkhierarchy = ["Link Label (Hierarchie)","link label (hierarchy)","","",""];
LS.TERM.t_linkstore = ["Link Label (TripleStore)","link label (store)","","",""];
LS.TERM.t_lookup = ["Lookup","lookup","","",""];
LS.TERM.t_dbpedia = ["DBpedia (beta)","DBpedia (beta)","","",""];
LS.TERM.t_geonames = ["Geonames (beta)","Geonames (beta)","","",""];
LS.TERM.t_reslookup = ["Resource Lookup (beta)","resource lookup (beta)","","",""];
LS.TERM.t_vis = ["Visualisierung und Suche","visualisation & search","","",""];
LS.TERM.t_labelsearch = ["Labelsuche","label search","","",""];
LS.TERM.t_labelgraphs = ["Label Graphen","my label graphs","","",""];
LS.TERM.t_tree = ["Project Tree","my project tree","","",""];
// ontologis functions
LS.TERM.t_f_ontologist = ["Ontologist Funktionen","Ontologist Functions","","",""];
LS.TERM.t_inputsparql = ["SPARQL Endpoint","SPARQL endpoint","","",""];
LS.TERM.t_inputconcept = ["Input Concept Scheme","input concept scheme","","",""];
// actor functions
LS.TERM.t_f_actor = ["Actor Funktionen","Actor Functions","","",""];
LS.TERM.t_rest = ["REST interface","REST interface","","",""];
LS.TERM.t_sparql = ["SPARQL Labels","SPARQL labels","","",""];
// get Word in language
LS.TERM.getWord = function(WORD) {
	var out = "";
	if (LS.GUI.getMenuLang()=="deutsch") {
		out = window['LS']['TERM'][WORD][0];
	} else if (LS.GUI.getMenuLang()=="english") {
		out = window['LS']['TERM'][WORD][1];
	} else if (LS.GUI.getMenuLang()=="english") {
		out = window['LS']['TERM'][WORD][1];
	} else if (LS.GUI.getMenuLang()=="français") {
		out = window['LS']['TERM'][WORD][1];
	} else if (LS.GUI.getMenuLang()=="español") {
		out = window['LS']['TERM'][WORD][1];
	} else if (LS.GUI.getMenuLang()=="italiano") {
		out = window['LS']['TERM'][WORD][1];
	}
	document.getElementById(WORD).innerHTML = out;
}
// LOAD GUI
LS.GUI.loadGUI = function(page) {
	// LOAD TEMPLATE OF AGENT
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
			$.ajax({
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
					// set GUI values in instance
					LS.GUI.setId(output.data[0].id);
					LS.GUI.setCreator(output.data[0].creator);
					LS.GUI.setComment(output.data[0].comment);
					LS.GUI.setLabel(output.data[0].label);
					LS.GUI.setPrefLang(output.data[0].prefLang);
					LS.GUI.setMenuLang(output.data[0].menuLang);
					try {
						// set language labels
						if ($('#t_f_overall').length>0) LS.TERM.getWord("t_f_overall");
						if ($('#t_home').length>0) LS.TERM.getWord("t_home");
						if ($('#t_user_metadata').length>0) LS.TERM.getWord("t_user_metadata");
						if ($('#t_user_gui').length>0) LS.TERM.getWord("t_user_gui");
						if ($('#t_user_guimod').length>0) LS.TERM.getWord("t_user_guimod");
						if ($('#t_user_guimod').length>0) LS.TERM.getWord("t_user_guicreate");
						if ($('#t_user_settings').length>0) LS.TERM.getWord("t_user_settings");
						if ($('#t_documentation').length>0) LS.TERM.getWord("t_documentation");
						if ($('#t_languages').length>0) LS.TERM.getWord("t_languages");
						if ($('#t_logout').length>0) LS.TERM.getWord("t_logout");
						if ($('#t_test').length>0) LS.TERM.getWord("t_test");
						if ($('#t_logs').length>0) LS.TERM.getWord("t_logs");
						if ($('#t_template').length>0) LS.TERM.getWord("t_template");
						if ($('#t_dump').length>0) LS.TERM.getWord("t_dump");
						if ($('#t_dump2').length>0) LS.TERM.getWord("t_dump2");
						if ($('#t_f_user').length>0) LS.TERM.getWord("t_f_user");
						if ($('#t_create').length>0) LS.TERM.getWord("t_create");
						if ($('#t_projects').length>0) LS.TERM.getWord("t_projects");
						if ($('#t_vocabs').length>0) LS.TERM.getWord("t_vocabs");
						if ($('#t_labels').length>0) LS.TERM.getWord("t_labels");
						if ($('#t_modlabels').length>0) LS.TERM.getWord("t_modlabels");
						if ($('#t_csvlabels').length>0) LS.TERM.getWord("t_csvlabels");
						if ($('#t_linking').length>0) LS.TERM.getWord("t_linking");
						if ($('#t_linksparql').length>0) LS.TERM.getWord("t_linksparql");
						if ($('#t_linkresource').length>0) LS.TERM.getWord("t_linkresource");
						if ($('#t_linkhierarchy').length>0) LS.TERM.getWord("t_linkhierarchy");
						if ($('#t_linkstore').length>0) LS.TERM.getWord("t_linkstore");
						if ($('#t_lookup').length>0) LS.TERM.getWord("t_lookup");
						if ($('#t_dbpedia').length>0) LS.TERM.getWord("t_dbpedia");
						if ($('#t_geonames').length>0) LS.TERM.getWord("t_geonames");
						if ($('#t_reslookup').length>0) LS.TERM.getWord("t_reslookup");
						if ($('#t_vis').length>0) LS.TERM.getWord("t_vis");
						if ($('#t_labelsearch').length>0) LS.TERM.getWord("t_labelsearch");
						if ($('#t_labelgraphs').length>0) LS.TERM.getWord("t_labelgraphs");
						if ($('#t_tree').length>0) LS.TERM.getWord("t_tree");
						if ($('#t_f_ontologist').length>0) LS.TERM.getWord("t_f_ontologist");
						if ($('#t_inputsparql').length>0) LS.TERM.getWord("t_inputsparql");
						if ($('#t_inputconcept').length>0) LS.TERM.getWord("t_inputconcept");
						if ($('#t_f_actor').length>0) LS.TERM.getWord("t_f_actor");
						if ($('#t_rest').length>0) LS.TERM.getWord("t_rest");
						if ($('#t_sparql').length>0) LS.TERM.getWord("t_sparql");
						// set display
						if ($('#gui').length>0) document.getElementById("gui").value = LS.GUI.getLabel();
						console.info("GUI loaded");
						// set preflanguage dropdown lists
						if (page=="project") {
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageLabel");
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageComment");
						} else if (page=="vocabularies") {
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageLabel");
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageComment");
						} else if (page=="labels") {
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageLabel");
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languagealtlabel");
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageNote");
							LS.GUI.addOptionsAndSetPreferedLanguageOptionToSelectElement("languageDefinition");
						}
					} catch (e) {
						console.error(e);
					}
				}
			});	
		}
	});
}