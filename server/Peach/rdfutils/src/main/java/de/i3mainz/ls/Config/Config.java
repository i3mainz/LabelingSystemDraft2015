package de.i3mainz.ls.Config;

import de.i3mainz.ls.rdfutils.exceptions.ConfigException;

/**
 * CLASS to set global settings
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 13.04.2015
 */
public class Config {

	// change properties here
	public final static String SERVER = "http://labeling.i3mainz.hs-mainz.de/";
	public final static String INSTANCEHOST = "http://143.93.114.137/";
	public final static String TRIPLESTORE_SERVER = "http://labeling.i3mainz.hs-mainz.de/openrdf-sesame";
	public final static String FILE_STORE_PATH = "/tmp/";
	public final static String FILE_STORE_PATH_PUBLIC = "/usr/share/apache-tomcat-7.0.50/webapps/labelingserver/";
	// change prefixes here
	public final static String PREFIX_LABELINGSYSTEM = "http://143.93.114.137/vocab#";
	public final static String PREFIX_SKOS = "http://www.w3.org/2004/02/skos/core#";
	public final static String PREFIX_DCTERMS = "http://purl.org/dc/terms/";
	public final static String PREFIX_DCELEMENTS = "http://purl.org/dc/elements/1.1/";
	public final static String PREFIX_RDFS = "http://www.w3.org/2000/01/rdf-schema#";
	public final static String PREFIX_OWL = "http://www.w3.org/2002/07/owl#";
	public final static String PREFIX_RDF = "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
	public final static String PREFIX_FOAF = "http://xmlns.com/foaf/0.1/";

	// instances and rest path
	public final static String REST = Config.SERVER + "rest/";
	public final static String Rest_VOCABS = REST + "vocabs/";
	public final static String LABELINGSERVER = Config.SERVER + "labelingserver/";
	public final static String Instance_PROJECT = INSTANCEHOST + "project#";
	public final static String Instance_VOCABULARY = INSTANCEHOST + "vocabulary#";
	public final static String Instance_LABEL = INSTANCEHOST + "label#";
	public final static String Instance_LOG = INSTANCEHOST + "log#";
	public final static String Instance_SPARQLENDPOINT = INSTANCEHOST + "sparqlendpoint#";
	public final static String Instance_AGENT = INSTANCEHOST + "agent#";
	public final static String Instance_GUI = INSTANCEHOST + "gui#";
	
	// security properties
	public final static String LOGIN = "";
	public final static String PASSWORD = "";

	/**
	 * get instance URI (options: type[project;vocabulary;label] and brackets
	 * [true;false])
	 *
	 * @param type
	 * @param item
	 * @param brackets
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 */
	public static String Instance(String type, String item, boolean brackets) throws ConfigException {
		try {
			if ("project".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_PROJECT + item + ">";
				} else {
					return Config.Instance_PROJECT + item;
				}
			} else if ("vocabulary".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_VOCABULARY + item + ">";
				} else {
					return Config.Instance_VOCABULARY + item;
				}
			} else if ("label".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_LABEL + item + ">";
				} else {
					return Config.Instance_LABEL + item;
				}
			} else if ("log".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_LOG + item + ">";
				} else {
					return Config.Instance_LOG + item;
				}
			} else if ("sparqlendpoint".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_SPARQLENDPOINT + item + ">";
				} else {
					return Config.Instance_SPARQLENDPOINT + item;
				}
			} else if ("agent".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_AGENT + item + ">";
				} else {
					return Config.Instance_AGENT + item;
				}
			} else if ("gui".equals(type)) {
				if (brackets) {
					return "<" + Config.Instance_GUI + item + ">";
				} else {
					return Config.Instance_GUI + item;
				}
			} else {
				return "error";
			}
		} catch (Exception e) {
			throw new ConfigException("[" + Config.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * get REST URI of Vocabularies (options: brackets[true;false])
	 * @param brackets
	 * @return
	 * @throws ConfigException 
	 */
	public static String RestVocabularies(boolean brackets) throws ConfigException {
		try {
			if (brackets) {
				return "<" + Config.Rest_VOCABS + ">";
			} else {
				return Config.Rest_VOCABS;
			}
		} catch (Exception e) {
			throw new ConfigException("[" + Config.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}
	
	/**
	 * get REST URI of Vocabulary (options: brackets[true;false] and
	 * download[true;false])
	 *
	 * @param vocabulary
	 * @param brackets
	 * @param download
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 */
	public static String RestVocabulary(String vocabulary, boolean brackets, boolean download) throws ConfigException {
		try {
			if (brackets) {
				if (download) {
					return "<" + Config.Rest_VOCABS + vocabulary + ".skos>";
				} else {
					return "<" + Config.Rest_VOCABS + vocabulary + "/>";
				}
			} else {
				if (download) {
					return Config.Rest_VOCABS + vocabulary + ".skos";
				} else {
					return Config.Rest_VOCABS + vocabulary + "/";
				}
			}
		} catch (Exception e) {
			throw new ConfigException("[" + Config.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * get REST URI of Label (options: brackets[true;false] and format[rdf;ttl])
	 *
	 * @param vocabulary
	 * @param label
	 * @param brackets
	 * @param format
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 */
	public static String RestLabel(String vocabulary, String label, boolean brackets, String format) throws ConfigException {
		try {
			if (brackets) {
				if (format != null) {
					return "<" + Config.Rest_VOCABS + vocabulary + "/" + label + "." + format + ">";
				} else {
					return "<" + Config.Rest_VOCABS + vocabulary + "/" + label + "/>";
				}
			} else {
				if (format != null) {
					return Config.Rest_VOCABS + vocabulary + "/" + label + "." + format;
				} else {
					return Config.Rest_VOCABS + vocabulary + "/" + label + "/";
				}
			}
		} catch (Exception e) {
			throw new ConfigException("[" + Config.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * get ontology ITEM
	 *
	 * @param ontology (ls;skos;dcterms;rdfs)
	 * @param item
	 * @param brackets (true;false)
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 */
	public static String getPrefixItemOfOntology(String ontology, String item, boolean brackets) throws ConfigException {
		try {
			if (brackets) {
				switch (ontology) {
					case "ls":
						return "<" + Config.PREFIX_LABELINGSYSTEM + item + ">";
					case "skos":
						return "<" + Config.PREFIX_SKOS + item + ">";
					case "dcterms":
						return "<" + Config.PREFIX_DCTERMS + item + ">";
					case "rdfs":
						return "<" + Config.PREFIX_RDFS + item + ">";
					case "owl":
						return "<" + Config.PREFIX_OWL + item + ">";
					case "dcelements":
						return "<" + Config.PREFIX_DCELEMENTS + item + ">";
					case "rdf":
						return "<" + Config.PREFIX_RDF + item + ">";
					case "foaf":
						return "<" + Config.PREFIX_FOAF + item + ">";	
					default:
						return "<" + ontology + item + ">";
				}
			} else {
				switch (ontology) {
					case "ls":
						return Config.PREFIX_LABELINGSYSTEM + item;
					case "skos":
						return Config.PREFIX_SKOS + item;
					case "dcterms":
						return Config.PREFIX_DCTERMS + item;
					case "rdfs":
						return Config.PREFIX_RDFS + item;
					case "owl":
						return Config.PREFIX_OWL + item;
					case "dcelements":
						return Config.PREFIX_DCELEMENTS + item;
					case "rdf":
						return Config.PREFIX_RDF + item;
					case "foaf":
						return Config.PREFIX_FOAF + item;
					default:
						return ontology + item;
				}
			}
		} catch (Exception e) {
			throw new ConfigException("[" + Config.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

}