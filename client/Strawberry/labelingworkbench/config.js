// SET GUI
//var gui = new LS.GUI();

var Config = {};
////////////////////////////
// change properties here //
////////////////////////////
Config.SERVER = 'http://labeling.i3mainz.hs-mainz.de/';
Config.VOCABULARYPREFIX = "http://143.93.114.137/vocab#";
Config.INSTANCEHOST = "http://143.93.114.137/";
// change prefixes
Config.PREFIX_LABELINGSYSTEM = "http://143.93.114.137/vocab#";
Config.PREFIX_SKOS = "http://www.w3.org/2004/02/skos/core#";
Config.PREFIX_DCTERMS = "http://purl.org/dc/terms/";
Config.PREFIX_DCELEMENTS = "http://purl.org/dc/elements/1.1/";
Config.PREFIX_RDFS = "http://www.w3.org/2000/01/rdf-schema#";
Config.PREFIX_OWL = "http://www.w3.org/2002/07/owl#";
Config.PREFIX_RDF = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
// servlets
Config.REST = Config.SERVER+"rest/";
Config.LABELINGSERVER = Config.SERVER+"labelingserver/";
// instances and rest path
Config.Instance_PROJECT = Config.INSTANCEHOST+"project#";
Config.Instance_VOCABULARY = Config.INSTANCEHOST+"vocabulary#";
Config.Instance_LABEL = Config.INSTANCEHOST+"label#";
Config.Instance_LOG = Config.INSTANCEHOST+"log#";
Config.Instance_SPARQLENDPOINT = Config.INSTANCEHOST+"sparqlendpoint#";
Config.Instance_AGENT = Config.INSTANCEHOST+"agent#";
Config.Instance_GUI = Config.INSTANCEHOST+"gui#";
Config.Rest_VOCABS = Config.REST+"vocabs/";
Config.Rest_GUIS = Config.REST+"guis/";
Config.Rest_AGENTS = Config.REST+"agents/";
Config.Rest_SPARQLENDPOINTS = Config.REST+"sparqlendpoints/";
// LABELINGSERVER servlet
Config.SPARQL = Config.LABELINGSERVER + 'SPARQL';
Config.SPARQLConcepts = Config.LABELINGSERVER + 'SPARQLconcepts';
Config.Input = Config.LABELINGSERVER + 'Input';
Config.Delete = Config.LABELINGSERVER + 'Delete';
Config.Update = Config.LABELINGSERVER + "Update";
Config.UUID = Config.LABELINGSERVER + "getUUID";
Config.InputLabelCSV = Config.LABELINGSERVER + "InputLabelCSV";
Config.DeleteLabels = Config.LABELINGSERVER + "DeleteLabels";
Config.InputLog = Config.LABELINGSERVER + "InputLog";
Config.QueryDBpedia = Config.LABELINGSERVER + "querydbpedia";
Config.Dump = Config.LABELINGSERVER + "getDump";
Config.InputConcept = Config.LABELINGSERVER + "InputConcept";
Config.DeleteConcept = Config.LABELINGSERVER + "DeleteConcept";
Config.ReadSendRDF = Config.LABELINGSERVER + "ReadSendRDF";
Config.SVG = Config.LABELINGSERVER + "getTreeSVG";
Config.ProjectTree = Config.LABELINGSERVER + "getProjectTreeConcepts?name=";
Config.ProjectTreePublic = Config.LABELINGSERVER + "getProjectTreeConceptsPublic?name=";
Config.Relations = Config.LABELINGSERVER + "getRelations";
Config.AutoComplete = Config.LABELINGSERVER + "autocomplete";
Config.AutoCompleteCreator = Config.AutoComplete + "?creator=$creator";
Config.AutoCompleteVocabulary = Config.AutoComplete + "?vocabulary=$vocabulary";
Config.AutoComplete2 = Config.LABELINGSERVER + "autocomplete2";
Config.AutoComplete2Creator = Config.AutoComplete2 + "?creator=$creator";
Config.AutoComplete2Vocabulary = Config.AutoComplete2 + "?vocabulary=$vocabulary";
Config.JSONlabel = Config.LABELINGSERVER + "getLabel";
Config.JSONvocabulary = Config.LABELINGSERVER + "getVocabulary";
Config.JSONproject = Config.LABELINGSERVER + "getProject";
Config.JSONagent = Config.LABELINGSERVER + "getAgent";
Config.JSONgui = Config.LABELINGSERVER + "getGUI";
Config.BroaderNarrowerTree = Config.LABELINGSERVER + "getBroaderNarrowerTree";
// get instance URI (options: type[project;vocabulary;label] and brackets [true;false])
Config.Instance = function(type,item,brackets) {
	if (type=="project") {
		if (brackets) {
			return "<"+Config.Instance_PROJECT+item+">";
		} else {
			return Config.Instance_PROJECT+item;
		}
	} else if (type=="vocabulary") {
		if (brackets) {
			return "<"+Config.Instance_VOCABULARY+item+">";
		} else {
			return Config.Instance_VOCABULARY+item;
		}
	} else if (type=="label") {
		if (brackets) {
			return "<"+Config.Instance_LABEL+item+">";
		} else {
			return Config.Instance_LABEL+item;
		}
	} else if (type=="log") {
		if (brackets) {
			return "<"+Config.Instance_LOG+item+">";
		} else {
			return Config.Instance_LOG+item;
		}
	} else if (type=="sparqlendpoint") {
		if (brackets) {
			return "<"+Config.Instance_SPARQLENDPOINT+item+">";
		} else {
			return Config.Instance_SPARQLENDPOINT+item;
		}
	} else if (type=="gui") {
		if (brackets) {
			return "<"+Config.Instance_GUI+item+">";
		} else {
			return Config.Instance_GUI+item;
		}
	} else if (type=="agent") {
		if (brackets) {
			return "<"+Config.Instance_AGENT+item+">";
		} else {
			return Config.Instance_AGENT+item;
		}
	}
}
// get REST URI of Vocabulary (options: brackets[true;false] and download[true;false])
Config.RestVocabulary = function(vocabulary,brackets,download) {
	if (brackets) {
		if (download) {
			return "<"+Config.Rest_VOCABS+vocabulary+">";
		} else {
			return "<"+Config.Rest_VOCABS+vocabulary+".skos>";
		}
	} else {
		if (download) {
			return Config.Rest_VOCABS+vocabulary;
		} else {
			return Config.Rest_VOCABS+vocabulary+".skos";
		}
	}
}
// get REST URI of Label (options: brackets[true;false] and format[rdf;ttl])
Config.RestLabel = function(vocabulary,label,brackets,format) {
	if (brackets) {
		if (format) {
			return "<"+Config.Rest_VOCABS+vocabulary+"/"+label+"."+format+">";
		} else {
			return "<"+Config.Rest_VOCABS+vocabulary+"/"+label+">";
		}
	} else {
		if (format) {
			return Config.Rest_VOCABS+vocabulary+"/"+label+"."+format;
		} else {
			return Config.Rest_VOCABS+vocabulary+"/"+label;
		}
	}
}
// get ontology ITEM by PREFIX
Config.getPrefixItemOfOntology = function(ontology,item,brackets) {
	if (brackets) {
		if (ontology=="ls") {
			return "<" + Config.PREFIX_LABELINGSYSTEM + item + ">";
		} else if (ontology=="skos") {
			return "<" + Config.PREFIX_SKOS + item + ">";
		} else if (ontology=="dcterms") {
			return "<" + Config.PREFIX_DCTERMS + item + ">";
		} else if (ontology=="rdfs") {
			return "<" + Config.PREFIX_RDFS + item + ">";
		} else if (ontology=="owl") {
			return "<" + Config.PREFIX_OWL + item + ">";
		} else if (ontology=="dcelements") {
			return "<" + Config.PREFIX_DCELEMENTS + item + ">";
		} else if (ontology=="rdf") {
			return "<" + Config.PREFIX_RDF + item + ">";
		} else {
			return "<" + ontology + item + ">";
		}
	} else {
		if (ontology=="ls") {
			return Config.PREFIX_LABELINGSYSTEM + item;
		} else if (ontology=="skos") {
			return Config.PREFIX_SKOS + item;
		} else if (ontology=="dcterms") {
			return Config.PREFIX_DCTERMS + item;
		} else if (ontology=="rdfs") {
			return Config.PREFIX_RDFS + item;
		} else if (ontology=="owl") {
			return Config.PREFIX_OWL + item;
		} else if (ontology=="dcelements") {
			return "<" + Config.PREFIX_DCELEMENTS + item + ">";
		} else if (ontology=="rdf") {
			return "<" + Config.PREFIX_RDF + item + ">";
		} else {
			return ontology + item;
		}
	}
}
// get ontology ITEM (options: brackets [true;false])
// not to use!!!!
Config.Ontology = function(item,brackets) {
	if (brackets) {
		return "<"+Config.VOCABULARYPREFIX+item+">";
	} else {
		return Config.VOCABULARYPREFIX+item;
	}
}

var Conf = {};
///////////////////////////////////////////
// change properties for info pages here //
///////////////////////////////////////////
Conf.Ontology = 'http://labeling.i3mainz.hs-mainz.de/vocab';
Conf.SERVER = 'http://labeling.i3mainz.hs-mainz.de/';
Conf.REST = Conf.SERVER + "rest/";
Conf.INFOHOST = "http://143.93.114.137/";
Conf.INSTANCEHOST = "http://143.93.114.137/";
Conf.VOCABULARYPREFIX = "http://143.93.114.137/vocab#";
Conf.INSTANCE_AGENTS = Conf.INFOHOST + 'agent';
Conf.CLASS_AGENT = 'http://xmlns.com/foaf/0.1/Person';
Conf.REST_AGENTS = Conf.REST+"agents/";
Conf.INSTANCE_GUIS = Conf.INFOHOST + 'gui';
Conf.CLASS_GUI = Conf.VOCABULARYPREFIX + 'GUI';
Conf.REST_GUIS = Conf.REST+"guis/";
Conf.INSTANCE_SPARQLENDPOINTS = Conf.INFOHOST + 'sparqlendpoint';
Conf.CLASS_SPARQLENDPOINT = Conf.VOCABULARYPREFIX + 'SPARQLendpoint';
Conf.REST_SPARQLENDPOINTS = Conf.REST+"sparqlendpoints/";
Conf.INSTANCE_PROJECTS = Conf.INFOHOST + 'project';
Conf.CLASS_PROJECT = Conf.VOCABULARYPREFIX + 'Project';
Conf.INSTANCE_VOCABULARIES = Conf.INFOHOST + 'vocabulary';
Conf.INSTANCE_VOCABULARY = Conf.INSTANCEHOST + "vocabulary#";
Conf.CLASS_VOCABULARY = Conf.VOCABULARYPREFIX + 'Vocabulary';
Conf.REST_VOCABULARIES = Conf.REST+"vocabs/";
Conf.INSTANCE_LABELS = Conf.INFOHOST + 'label';
Conf.CLASS_LABEL = Conf.VOCABULARYPREFIX + 'Label';

Conf.SPARQL_getVocabulariesOfLabels = "SELECT * WHERE { ?s <http://143.93.114.137/vocab#contains> <http://143.93.114.137/label#$label> . }";

/***************
 *
 * SPARQL-QUERY
 *
***************/
var SPARQL = {};
SPARQL.logs = "SELECT * WHERE { ?date <"+Config.VOCABULARYPREFIX+"login> ?creator } ORDER BY DESC(?date) LIMIT 100";
SPARQL.logs_all = "SELECT * WHERE { ?date <"+Config.VOCABULARYPREFIX+"login> ?creator } ORDER BY DESC(?date)";
SPARQL.myprojects = "SELECT DISTINCT ?s WHERE { ?l a <"+Config.VOCABULARYPREFIX+"Project> . ?l <http://www.w3.org/2000/01/rdf-schema#label> ?s . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . } ORDER BY ASC(?s)";
SPARQL.prometadata = "SELECT DISTINCT ?s ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s a <"+Config.VOCABULARYPREFIX+"Project> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\" .}";
SPARQL.uri = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s a <"+Config.VOCABULARYPREFIX+"Project> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.myvocabularies = "SELECT DISTINCT ?s WHERE { ?l a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?l <http://www.w3.org/2000/01/rdf-schema#label> ?s . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . } ORDER BY ASC(?s)";
SPARQL.uriVoc = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.uriPro = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s a <"+Config.VOCABULARYPREFIX+"Project> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.vocmetadata = "SELECT DISTINCT ?s ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\" .}";
SPARQL.ProURItoLab = "SELECT DISTINCT ?s WHERE { <$uri> <http://www.w3.org/2000/01/rdf-schema#label> ?s . }";
SPARQL.VocURItoLab = "SELECT DISTINCT ?s WHERE { <$uri> <http://www.w3.org/2000/01/rdf-schema#label> ?s . }";
SPARQL.mylabels = "SELECT DISTINCT ?s WHERE { ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?s . ?l <"+Config.VOCABULARYPREFIX+"prefLang> ?prefLang . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . FILTER(LANGMATCHES(LANG(?s), ?prefLang)) } ORDER BY ASC(?s)";
SPARQL.uriLab = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <"+Config.VOCABULARYPREFIX+"Label> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.labmetadata = "SELECT DISTINCT ?s ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <"+Config.VOCABULARYPREFIX+"Label> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.labvoc = "SELECT DISTINCT ?s WHERE { ?s <"+Config.VOCABULARYPREFIX+"contains> $l }";
SPARQL.labelRelations = "SELECT DISTINCT ?s ?o ?p WHERE { ?s ?p ?o. <$label> ?p ?o . FILTER (?p = <http://www.w3.org/2004/02/skos/core#related> || ?p = <http://www.w3.org/2004/02/skos/core#broader> || ?p = <http://www.w3.org/2004/02/skos/core#narrower> || ?p = <http://www.w3.org/2000/01/rdf-schema#seeAlso> || ?p = <http://www.w3.org/2000/01/rdf-schema#isDefinedBy> || ?p = <http://www.w3.org/2002/07/owl#sameAs> || ?p = <http://www.w3.org/2004/02/skos/core#closeMatch> || ?p = <http://www.w3.org/2004/02/skos/core#exactMatch> || ?p = <http://www.w3.org/2004/02/skos/core#relatedMatch> || ?p = <http://www.w3.org/2004/02/skos/core#narrowMatch> || ?p = <http://www.w3.org/2004/02/skos/core#broadMatch> ) . }";
SPARQL.LabURItoLab = "SELECT DISTINCT ?s WHERE { <$uri> <http://www.w3.org/2004/02/skos/core#prefLabel> ?s . }";
SPARQL.querylabel = "SELECT DISTINCT ?preflabel ?vocabulary ?creator ?s WHERE { ?s a <http://www.w3.org/2004/02/skos/core#Concept> . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?preflabel . ?s <http://www.w3.org/2004/02/skos/core#inScheme> ?vocabulary . ?s <http://purl.org/dc/elements/1.1/creator> ?creator . FILTER(regex(?preflabel, \"$ss\", \"i\")) . } ORDER BY ASC(?preflabel)";
SPARQL.labmetadataSearch = "SELECT DISTINCT ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <"+Config.VOCABULARYPREFIX+"Label> . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.labelcheck_Voc = "SELECT DISTINCT ?s WHERE { ?s a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.labelcheck_Label = "SELECT DISTINCT ?s WHERE { ?s a <"+Config.VOCABULARYPREFIX+"Label> . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.labelcheck = "SELECT DISTINCT ?s WHERE { ?s a <"+Config.VOCABULARYPREFIX+"Label> . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";

SPARQL.querynames = "SELECT DISTINCT ?o WHERE { ?s <http://purl.org/dc/elements/1.1/creator> ?o . } ORDER BY ASC(?o)";



SPARQL.sparqlendpoint = "SELECT ?name ?uri ?query WHERE { ?sparql <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <"+Config.VOCABULARYPREFIX+"SPARQLendpoint> . ?sparql <"+Config.VOCABULARYPREFIX+"sparqlname> ?name . ?sparql <"+Config.VOCABULARYPREFIX+"sparqlxmluri> ?uri . ?sparql <"+Config.VOCABULARYPREFIX+"sparqlquery> ?query. } ORDER BY ?name";

SPARQL.labelbroaderlabel = "SELECT DISTINCT ?pl ?pl2 WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> ?pl2 . ?l <http://www.w3.org/2004/02/skos/core#broader> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#narrower> ?l . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?pl . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . } ORDER BY ASC(?pl)";
SPARQL.labelnarrowerlabel = "SELECT DISTINCT ?pl ?pl2 WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> ?pl2 . ?l <http://www.w3.org/2004/02/skos/core#narrower> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#broader> ?l . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?pl . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . } ORDER BY ASC(?pl)";
SPARQL.labelrelatedlabel = "SELECT DISTINCT ?pl ?pl2 WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> ?pl2 . ?l <http://www.w3.org/2004/02/skos/core#related> ?l2 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?pl . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . } ORDER BY ASC(?pl)";
SPARQL.labelcheck_Project = "SELECT DISTINCT ?s WHERE { ?s a <"+Config.VOCABULARYPREFIX+"Project> . ?s <http://www.w3.org/2000/01/rdf-schema#label> $label . ?s <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";
SPARQL.labelproperties = "SELECT ?l ?prefLabel ?note ?definition ?altLabel WHERE { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?prefLabel . OPTIONAL { ?l <http://www.w3.org/2004/02/skos/core#altLabel> ?altLabel . } OPTIONAL { ?l <http://www.w3.org/2004/02/skos/core#definition> ?definition . } OPTIONAL { ?l <http://www.w3.org/2004/02/skos/core#note> ?note . } ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\". }";

SPARQL.labelmetadataByIdentifier = "SELECT DISTINCT ?p ?o WHERE { ?label ?p ?o . ?label <"+Config.VOCABULARYPREFIX+"identifier> \"$identifier\". }";

SPARQL.creatorsOfLabels = "SELECT DISTINCT ?creator WHERE { ?label a <"+Config.VOCABULARYPREFIX+"Label> . ?label <http://purl.org/dc/elements/1.1/creator> ?creator . } ORDER BY ASC(?creator)";

SPARQL.vocabularyIndentifierByLabelAndCreator = "SELECT DISTINCT ?creator WHERE { ?label a <"+Config.VOCABULARYPREFIX+"Label> . ?label <http://purl.org/dc/elements/1.1/creator> ?creator . } ORDER BY ASC(?creator)";
SPARQL.vocabularyLabelsAndIdentifier = "SELECT DISTINCT ?vocabularylabel ?vocabularyidentifier WHERE { ?vocabulary <"+Config.VOCABULARYPREFIX+"state> 'public' . ?vocabulary <"+Config.VOCABULARYPREFIX+"identifier> ?vocabularyidentifier . ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?vocabulary <http://www.w3.org/2000/01/rdf-schema#label> ?vocabularylabel . ?vocabulary <http://purl.org/dc/elements/1.1/creator> ?creator . } ORDER BY ASC(?vocabularylabel)";

// instaces
SPARQL.getVocabulariesOfLabels = "SELECT * WHERE { ?s <http://143.93.114.137/vocab#contains> <http://143.93.114.137/label#$label> . }";

/*****************
 *
 * SPARQL-UPDATE
 *
*****************/
var SPARQLUPDATE = {};
// broader and narrower
SPARQLUPDATE.sendbroader = "INSERT { ?l <http://www.w3.org/2004/02/skos/core#broader> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#narrower> ?l . } WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> $pl2 . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l2 <http://purl.org/dc/elements/1.1/creator> $creator. ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl1 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
SPARQLUPDATE.sendnarrower = "INSERT { ?l <http://www.w3.org/2004/02/skos/core#narrower> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#broader> ?l . } WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> $pl2 . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l2 <http://purl.org/dc/elements/1.1/creator> $creator. ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl1 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
SPARQLUPDATE.sendrelated = "INSERT { ?l <http://www.w3.org/2004/02/skos/core#related> ?l2 . } WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> $pl2 . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l2 <http://purl.org/dc/elements/1.1/creator> $creator. ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl1 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
SPARQLUPDATE.sendrelation = "INSERT { ?l <$relation> <$resource> . } WHERE { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
SPARQLUPDATE.deletebroader = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#broader> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#narrower> ?l . } WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> $pl2 . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l2 <http://purl.org/dc/elements/1.1/creator> $creator. ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl1 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
SPARQLUPDATE.deletenarrower = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#narrower> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#broader> ?l . } WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> $pl2 . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l2 <http://purl.org/dc/elements/1.1/creator> $creator. ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl1 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
SPARQLUPDATE.deleterelated = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#related> ?l2 . } WHERE { ?l2 <http://www.w3.org/2004/02/skos/core#prefLabel> $pl2 . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l2 <http://purl.org/dc/elements/1.1/creator> $creator. ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl1 . ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> $creator. }";
// modify label


var TS = {};
TS.mylabels = "SELECT DISTINCT ?s WHERE { ?l a <http://143.93.114.137/vocab#Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?s . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\" . } ORDER BY ASC(?s)";
TS.labelconcepts = "SELECT DISTINCT ?o WHERE { <$label> ?p ?o . FILTER (?p = <http://www.w3.org/2004/02/skos/core#related> || ?p = <http://www.w3.org/2004/02/skos/core#broader> || ?p = <http://www.w3.org/2004/02/skos/core#narrower>) . }";
TS.labelmetadata = "SELECT DISTINCT ?s ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <http://143.93.114.137/vocab#Label> . }";
TS.uriLab = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <http://143.93.114.137/vocab#Label> . }";

/*******
 *     *
 * NEW *
 *     *
********/

// PROJECT
SPARQLUPDATE.insertProjectByIdentifier = "INSERT DATA { $data }";
SPARQL.checkProject = "SELECT DISTINCT ?project WHERE { ?project a <"+Config.VOCABULARYPREFIX+"Project> . ?project <http://www.w3.org/2000/01/rdf-schema#label> $label . ?project <http://purl.org/dc/elements/1.1/creator> '$creator'. }";
SPARQLUPDATE.deleteProjectByIdentifier = "DELETE { ?project ?p1 ?o . ?s ?p2 ?project . } WHERE { ?project ?p1 ?o . OPTIONAL { ?s ?p2 ?project } ?project a <"+Config.VOCABULARYPREFIX+"Project> . ?project <"+Config.VOCABULARYPREFIX+"identifier> '$projectid' . }";
SPARQL.projectLabelAndIdentifierByCreator = "SELECT DISTINCT ?label ?identifier WHERE { ?project <"+Config.VOCABULARYPREFIX+"identifier> ?identifier . ?project <http://www.w3.org/2000/01/rdf-schema#label> ?label . ?project <http://purl.org/dc/elements/1.1/creator> '$creator'.  ?project a <"+Config.VOCABULARYPREFIX+"Project> . } ORDER BY ASC(?label)";

// VOCABULARY
SPARQLUPDATE.insertVocabularyByIdentifier = "INSERT DATA { $data }";
SPARQL.checkVocabulary = "SELECT DISTINCT ?vocabulary WHERE { ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?vocabulary <http://www.w3.org/2000/01/rdf-schema#label> $label . ?vocabulary <http://purl.org/dc/elements/1.1/creator> '$creator'. }";
SPARQLUPDATE.deleteVocabularyByIdentifier = "DELETE { ?vocabulary ?p1 ?o . ?s ?p2 ?vocabulary . } WHERE { ?vocabulary ?p1 ?o . OPTIONAL { ?s ?p2 ?vocabulary } ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?vocabulary <"+Config.VOCABULARYPREFIX+"identifier> '$vocabularyid' . }";
SPARQL.vocabularyLabelAndIdentifierByCreator = "SELECT DISTINCT ?vocabularylabel ?vocabularyidentifier WHERE { ?vocabulary <"+Config.VOCABULARYPREFIX+"identifier> ?vocabularyidentifier . ?vocabulary <http://www.w3.org/2000/01/rdf-schema#label> ?vocabularylabel . ?vocabulary <http://purl.org/dc/elements/1.1/creator> '$creator'.  ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . } ORDER BY ASC(?vocabularylabel)";
SPARQLUPDATE.deleteVocabularyProjectConnectionByIdentifiers = "DELETE { ?project <"+Config.VOCABULARYPREFIX+"contains> ?vocabulary . ?vocabulary <"+Config.VOCABULARYPREFIX+"belongsTo> ?project . } WHERE { ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?vocabulary <"+Config.VOCABULARYPREFIX+"identifier> '$vocabularyid' . ?project a <"+Config.VOCABULARYPREFIX+"Project> . ?project <"+Config.VOCABULARYPREFIX+"identifier> '$projectid' .}";
SPARQLUPDATE.createVocabularyProjectConnectionByIdentifiers = "INSERT DATA { <"+Config.Instance_PROJECT+"$projectid> <"+Config.VOCABULARYPREFIX+"contains> <"+Config.Instance_VOCABULARY+"$vocabularyid> . <"+Config.Instance_VOCABULARY+"$vocabularyid> <"+Config.VOCABULARYPREFIX+"belongsTo> <"+Config.Instance_PROJECT+"$projectid> . }";
SPARQLUPDATE.hideVocabularyByIdentifier = "DELETE { ?vocabulary <"+Config.VOCABULARYPREFIX+"state> ?state } WHERE { ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?vocabulary <"+Config.VOCABULARYPREFIX+"identifier> '$vocabularyid' . }";
SPARQLUPDATE.publishVocabularyByIdentifier = "INSERT DATA { <"+Config.Instance_VOCABULARY+"$vocabularyid> <"+Config.VOCABULARYPREFIX+"state> 'public' }";

// LABEL
SPARQLUPDATE.insertLabelByIdentifier = "INSERT DATA { $data }";
SPARQL.checklabel = "SELECT DISTINCT ?label WHERE { ?label a <"+Config.VOCABULARYPREFIX+"Label> . ?label <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s <http://purl.org/dc/elements/1.1/creator> '$creator' . }";
SPARQLUPDATE.deleteLabelByIdentifier = "DELETE { ?label ?p1 ?o . ?s ?p2 ?label . } WHERE { ?label ?p1 ?o . OPTIONAL { ?s ?p2 ?label } ?label a <"+Config.VOCABULARYPREFIX+"Label> . ?label <"+Config.VOCABULARYPREFIX+"identifier> '$labelid' . }";
SPARQL.labelPrefLabelAndIdentifierByCreator = "SELECT DISTINCT ?preflabellabel ?labelidentifier WHERE { ?label <"+Config.VOCABULARYPREFIX+"identifier> ?labelidentifier . ?label <http://www.w3.org/2004/02/skos/core#prefLabel> ?preflabellabel . ?label <http://purl.org/dc/elements/1.1/creator> '$creator'. ?label a <"+Config.VOCABULARYPREFIX+"Label> . } ORDER BY ?labelidentifier ?preflabellabel";
SPARQLUPDATE.deleteLabelVocabularyConnectionByIdentifiers = "DELETE { ?vocabulary <"+Config.VOCABULARYPREFIX+"contains> ?label . ?label <"+Config.VOCABULARYPREFIX+"belongsTo> ?vocabulary . } WHERE { ?vocabulary a <"+Config.VOCABULARYPREFIX+"Vocabulary> . ?vocabulary <"+Config.VOCABULARYPREFIX+"identifier> '$vocabularyid' . ?label a <"+Config.VOCABULARYPREFIX+"Label> . ?label <"+Config.VOCABULARYPREFIX+"identifier> '$labelid' .}";
SPARQLUPDATE.createLabelVocabularyConnectionByIdentifiers = "INSERT DATA { <"+Config.Instance_VOCABULARY+"$vocabularyid> <"+Config.VOCABULARYPREFIX+"contains> <"+Config.Instance_LABEL+"$labelid> . <"+Config.Instance_LABEL+"$labelid> <"+Config.VOCABULARYPREFIX+"belongsTo> <"+Config.Instance_VOCABULARY+"$vocabularyid> . }";
SPARQLUPDATE.createExternalRelationByIdentifier = "INSERT { ?label <$relation> <$resource> . } WHERE { ?label <"+Config.VOCABULARYPREFIX+"identifier> '$identifier' . ?label a <"+Config.VOCABULARYPREFIX+"Label> . }";
SPARQLUPDATE.deleteExternalRelationByIdentifier = "DELETE { ?label <$relation> <$resource> . } WHERE { ?label <"+Config.VOCABULARYPREFIX+"identifier> '$identifier' . ?label a <"+Config.VOCABULARYPREFIX+"Label> . }";
SPARQLUPDATE.createBroaderByIdentifier = "INSERT { ?l <http://www.w3.org/2004/02/skos/core#broader> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#narrower> ?l . } WHERE { ?l2 <"+Config.VOCABULARYPREFIX+"identifier> '$identifier2' . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l <"+Config.VOCABULARYPREFIX+"identifier> '$identifier1' . }";
SPARQLUPDATE.createNarrowerByIdentifier = "INSERT { ?l <http://www.w3.org/2004/02/skos/core#narrower> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#broader> ?l . } WHERE { ?l2 <"+Config.VOCABULARYPREFIX+"identifier> '$identifier2' . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l <"+Config.VOCABULARYPREFIX+"identifier> '$identifier1' . }";
SPARQLUPDATE.createRelatedByIdentifier = "INSERT { ?l <http://www.w3.org/2004/02/skos/core#related> ?l2 . } WHERE { ?l2 <"+Config.VOCABULARYPREFIX+"identifier> '$identifier2' . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l <"+Config.VOCABULARYPREFIX+"identifier> '$identifier1' . }";
SPARQLUPDATE.deleteBroaderByIdentifier = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#broader> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#narrower> ?l . } WHERE { ?l2 <"+Config.VOCABULARYPREFIX+"identifier> '$identifier2' . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l <"+Config.VOCABULARYPREFIX+"identifier> '$identifier1' . }";
SPARQLUPDATE.deleteNarrowerByIdentifier = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#narrower> ?l2 . ?l2 <http://www.w3.org/2004/02/skos/core#broader> ?l . } WHERE { ?l2 <"+Config.VOCABULARYPREFIX+"identifier> '$identifier2' . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l <"+Config.VOCABULARYPREFIX+"identifier> '$identifier1' . }";
SPARQLUPDATE.deleteRelatedByIdentifier = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#related> ?l2 . } WHERE { ?l2 <"+Config.VOCABULARYPREFIX+"identifier> '$identifier2' . ?l2 a <"+Config.VOCABULARYPREFIX+"Label> . ?l <"+Config.VOCABULARYPREFIX+"identifier> '$identifier1' . }";
SPARQL.checkPrefLabelLanguages = "SELECT DISTINCT * WHERE { ?l a <"+Config.VOCABULARYPREFIX+"Label> . ?l <http://purl.org/dc/elements/1.1/creator> \"$creator\". { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $de . } UNION { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $en . } UNION { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $fr . } UNION { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $es . } UNION { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $it . } UNION { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> $pl . } }";

SPARQLUPDATE.deleteLabelProperties = "DELETE { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?p . ?l <http://www.w3.org/2004/02/skos/core#altLabel> ?a . ?l <http://www.w3.org/2004/02/skos/core#note> ?n . ?l <http://www.w3.org/2004/02/skos/core#definition> ?d . ?l <"+Config.VOCABULARYPREFIX+"prefLang> ?lang . } WHERE { ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?p . OPTIONAL { ?l <http://www.w3.org/2004/02/skos/core#altLabel> ?a . } OPTIONAL { ?l <http://www.w3.org/2004/02/skos/core#note> ?n . } OPTIONAL {?l <http://www.w3.org/2004/02/skos/core#definition> ?d . } ?l <"+Config.VOCABULARYPREFIX+"prefLang> ?lang . ?l <"+Config.VOCABULARYPREFIX+"identifier> $identifier . ?l a <"+Config.VOCABULARYPREFIX+"Label> . }";

SPARQLUPDATE.insertLabelProperties = "INSERT DATA { $prefLabel $altLabel $note $definition $prefLang }";

// PROJECT TREE
SPARQL.getCreators = "SELECT DISTINCT ?creator WHERE { ?subject <http://purl.org/dc/elements/1.1/creator> ?creator . } ORDER BY ASC(?creator)";

// SPARQL ENDPOINT
SPARQLUPDATE.deleteSparqlEndpointByName = "DELETE WHERE { ?se <"+Config.VOCABULARYPREFIX+"sparqlname> '$sparqlname'; ?property ?value }";

// LOOKUP
SPARQL.getLinks = "SELECT DISTINCT * WHERE { ?label ?property <$URI>. ?label a <"+Config.VOCABULARYPREFIX+"Label> . }";

// AGENT
SPARQLUPDATE.insertAgentByIdentifier = "INSERT DATA { $data }";
SPARQLUPDATE.deleteAgentProperties = "DELETE { ?a <http://xmlns.com/foaf/0.1/title> ?t . ?a <http://xmlns.com/foaf/0.1/firstName> ?f . ?a <http://xmlns.com/foaf/0.1/lastName> ?l . ?a <http://xmlns.com/foaf/0.1/mbox> ?m . ?a <http://xmlns.com/foaf/0.1/status> ?s . ?a <http://xmlns.com/foaf/0.1/topic_interest> ?ti . ?a <http://xmlns.com/foaf/0.1/workplaceHomepage> ?wh . ?a <http://xmlns.com/foaf/0.1/workInfoHomepage> ?wih . ?a <http://xmlns.com/foaf/0.1/homepage> ?h . } WHERE { ?a <http://xmlns.com/foaf/0.1/title> ?t . ?a <http://xmlns.com/foaf/0.1/firstName> ?f . ?a <http://xmlns.com/foaf/0.1/lastName> ?l . ?a <http://xmlns.com/foaf/0.1/mbox> ?m . OPTIONAL { ?a <http://xmlns.com/foaf/0.1/status> ?s . } OPTIONAL { ?a <http://xmlns.com/foaf/0.1/topic_interest> ?ti . } OPTIONAL { ?a <http://xmlns.com/foaf/0.1/workplaceHomepage> ?wh . } OPTIONAL { ?a <http://xmlns.com/foaf/0.1/workInfoHomepage> ?wih . } OPTIONAL { ?a <http://xmlns.com/foaf/0.1/homepage> ?h . } ?a <http://xmlns.com/foaf/0.1/accountName> '$user' . ?a a <http://xmlns.com/foaf/0.1/Person> . }";

// GUI
SPARQL.guiLabelAndIndentifierByCreator = "SELECT DISTINCT ?gui ?label ?identifier WHERE { ?gui <"+Config.VOCABULARYPREFIX+"identifier> ?identifier . ?gui <http://www.w3.org/2000/01/rdf-schema#label> ?label . ?gui a <"+Config.VOCABULARYPREFIX+"GUI> . ?gui <"+Config.VOCABULARYPREFIX+"GUIcreator> '$creator' . } ORDER BY ?label";
SPARQL.guiLabelAndIndentifier = "SELECT DISTINCT ?gui ?label ?identifier WHERE { ?gui <"+Config.VOCABULARYPREFIX+"identifier> ?identifier . ?gui <http://www.w3.org/2000/01/rdf-schema#label> ?label . ?gui a <"+Config.VOCABULARYPREFIX+"GUI> . } ORDER BY ?label";
SPARQLUPDATE.insertGUIByIdentifier = "INSERT DATA { $data }";
SPARQLUPDATE.deleteGUIActorConnectionByIdentifiers = "DELETE { ?agent <"+Config.VOCABULARYPREFIX+"hasGUI> ?gui . ?gui <"+Config.VOCABULARYPREFIX+"isGUIof> ?agent . } WHERE { ?gui a <"+Config.VOCABULARYPREFIX+"GUI> . ?gui <"+Config.VOCABULARYPREFIX+"identifier> '$gui' . ?agent a <http://xmlns.com/foaf/0.1/Person> . ?agent <http://xmlns.com/foaf/0.1/accountName> '$accountName' .}";
SPARQLUPDATE.deleteGUIPropertiesByIdentifiers = "DELETE { ?gui <"+Config.VOCABULARYPREFIX+"GUIprefLang> ?pl . ?gui <"+Config.VOCABULARYPREFIX+"GUImenuLang> ?ml . } WHERE { ?gui <"+Config.VOCABULARYPREFIX+"GUIprefLang> ?pl . ?gui <"+Config.VOCABULARYPREFIX+"GUImenuLang> ?ml . ?gui <"+Config.VOCABULARYPREFIX+"identifier> '$identifier' . }";
SPARQLUPDATE.insertGUIPropertiesByIdentifier = "INSERT DATA { $data }";
SPARQLUPDATE.deleteGUIByIdentifier = "DELETE { ?gui ?p ?o . } WHERE { ?gui ?p ?o . ?gui <http://143.93.114.137/vocab#identifier> '$identifier' . }";