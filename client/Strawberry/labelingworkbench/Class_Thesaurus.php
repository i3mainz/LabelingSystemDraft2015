<?php

	class Thesaurus {
		
		// word lists as array
		
		// example
		// $array = array ([0]=>de,[1]=>en,[2]=>fr,[3]=>es,[4]=>it,[5]=>pl);
		// protected static $??? = array("","","","","","");
		
		// overall functions
		protected static $f_overall = array("Allgemeine Funktionen","Overall Functions","","","","");
		protected static $home = array("Home & Sprachwechsel","home & language change","","","","");
		protected static $user_settings = array("Passwort ändern","change password","","","","");
		protected static $user_metadata = array("Benutzer Metadaten","user metadata","","","","");
		protected static $user_gui = array("Benutzer GUI wechseln","change user gui","","","","");
		protected static $user_guimod = array("Benutzer GUI ändern","modify user gui","","","","");
		protected static $documentation = array("Dokumentation","user manual","","","","");
		protected static $languages = array("Sprachabkürzungen","language overview","","","","");
		protected static $logout = array("Logout","logout","","","","");
		protected static $test = array("Testbereich","testarea","","","","");
		protected static $logs = array("100 letzte logs","100 last logs","","","","");
		protected static $template = array("Template","template","","","","");
		protected static $dump = array("Download RDF Dump","download RDF dump","","","","");
		protected static $dump2 = array("Download RDF Dump Concepts","download RDF dump concepts","","","","");
		// user functions
		protected static $f_user = array("User Funktionen","User Functions","","","","");
		protected static $create = array("Erstellung und Modifizierung","create and modify","","","","");
		protected static $projects = array("Projekte","my projects","","","","");
		protected static $vocabs = array("Vokabulare","my vocabularies","","","","");
		protected static $labels = array("Label","my labels","","","","");
		protected static $modlabels = array("Label modifizieren","modify label","","","","");
		protected static $csvlabels = array("Label CSV-Upload","label CSV upload","","","","");
		protected static $linking = array("Verlinken","linking","","","","");
		protected static $linksparql = array("Link Label (SPARQL)","link label (SPARQL)","","","","");
		protected static $linkresource = array("Link Label (Resource)","link label (ressource)","","","","");
		protected static $linkhierarchy = array("Link Label (Hierarchie)","link label (hierarchy)","","","","");
		protected static $linkstore = array("Link Label (TripleStore)","link label (store)","","","","");
		protected static $lookup = array("Lookup","lookup","","","","");
		protected static $dbpedia = array("DBpedia (beta)","DBpedia (beta)","","","","");
		protected static $geonames = array("Geonames (beta)","Geonames (beta)","","","","");
		protected static $reslookup = array("Resource Lookup (beta)","resource lookup (beta)","","","","");
		protected static $vis = array("Visualisierung und Suche","visualisation & search","","","","");
		protected static $labelsearch = array("Labelsuche","label search","","","","");
		protected static $labelgraphs = array("Label Graphen","my label graphs","","","","");
		protected static $tree = array("Project Tree","my project tree","","","","");
		// ontologis functions
		protected static $f_ontologist = array("Ontologist Funktionen","Ontologist Functions","","","","");
		protected static $inputsparql = array("SPARQL Endpoint","SPARQL endpoint","","","","");
		protected static $inputconcept = array("Input Concept Scheme","input concept scheme","","","","");
		// actor functions
		protected static $f_actor = array("Actor Funktionen","Actor Functions","","","","");
		protected static $rest = array("REST interface","REST interface","","","","");
		protected static $sparql = array("SPARQL Labels","SPARQL labels","","","","");
		
		///////////////
		// functions //
		///////////////
		
		// note: to call function put in a string as $word named like the array
		public function getWord($word) {
			if (GlobalVariables::$menuLanguage == "de") {
				$out = eval("return self::$".$word."[0];");
			} else if (GlobalVariables::$menuLanguage == "en") {
				$out = eval("return self::$".$word."[1];");
			} else if (GlobalVariables::$menuLanguage == "fr") {
				$out = eval("return self::$".$word."[2];");
			} else if (GlobalVariables::$menuLanguage == "es") {
				$out = eval("return self::$".$word."[3];");
			} else if (GlobalVariables::$menuLanguage == "it") {
				$out = eval("return self::$".$word."[4];");
			} else if (GlobalVariables::$menuLanguage == "pl") {
				$out = eval("return self::$".$word."[5];");
			} else {
				$out = null;
			}
			return $out;
		}
	
	}

?>