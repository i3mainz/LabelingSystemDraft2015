package de.i3mainz.ls.fileinput;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.identifier.UniqueIdentifier;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.ConfigException;
import de.i3mainz.ls.rdfutils.exceptions.CsvExistanceException;
import de.i3mainz.ls.rdfutils.exceptions.CsvLabelImportException;
import de.i3mainz.ls.rdfutils.exceptions.CsvLanguageException;
import de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException;
import de.i3mainz.ls.rdfutils.exceptions.UniqueIdentifierException;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.http.Part;
import org.jdom.JDOMException;
import org.json.simple.JSONObject;
import org.openrdf.query.BindingSet;
import org.openrdf.query.MalformedQueryException;
import org.openrdf.query.QueryEvaluationException;
import org.openrdf.query.UpdateExecutionException;
import org.openrdf.repository.RepositoryException;

/**
 * CLASS (implements Runnable) to import a CSV file to the triplestore
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 30.04.2015
 */
public class CSV implements Runnable {
//public class CSV {

	public static String JSON_STRING = "";

	public static String CSV_Input() {
		return "";
	}

	/**
	 * import the Label to triplestore and build triples
	 *
	 * @param tokens
	 * @param creator
	 * @return
	 * @throws IOException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.UniqueIdentifierException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.CsvLabelImportException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException
	 */
	public static String importLabel(String[] tokens, String creator) throws IOException, UniqueIdentifierException, CsvLabelImportException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		String uuid = UniqueIdentifier.getUUIDforLabel();
		try {
			//vocabulary[0];prefLabel[1];altLabel[2];note[3];definition[4];prefLang[5];
			//broader[6];narrower[7];related[8];broadMatch[9];narrowMatch[10];relatedMatch[11];
			//closeMatch[12];exactMatch[13];seeAlso[14];isDefinedBy[15];sameAs[16];internalID[17]
			String vocabulary = tokens[0];
			String[] prefLabel = tokens[1].split(";");
			String[] altLabel = tokens[2].split(";");
			String[] note = tokens[3].split(";");
			String[] definition = tokens[4].split(";");
			String prefLang = tokens[5];
			String[] broadMatch = tokens[9].split(";");
			String[] narrowMatch = tokens[10].split(";");
			String[] relatedMatch = tokens[11].split(";");
			String[] closeMatch = tokens[12].split(";");
			String[] exactMatch = tokens[13].split(";");
			String[] seeAlso = tokens[14].split(";");
			String[] isDefinedBy = tokens[15].split(";");
			String[] sameAs = tokens[16].split(";");
			/////////////////
			// input label //
			/////////////////
			String label = "INSERT DATA { ";
			// ls label
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("rdf", "type", true) + " ";
			label += Config.getPrefixItemOfOntology("ls", "Label", true) + " ";
			label += ". ";
			// skos concept
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("rdf", "type", true) + " ";
			label += "<http://www.w3.org/2004/02/skos/core#Concept> ";
			label += ". ";
			// dcelements creator
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ";
			label += "\"" + creator + "\" ";
			label += ". ";
			// dcterms creator
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("dcterms", "creator", true) + " ";
			label += Config.Instance("agent", creator, true) + " ";
			label += ". ";
			// dcterms date
			Calendar cal = Calendar.getInstance();
			Date time = cal.getTime();
			DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZ");
			String d = formatter.format(time);
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("dcterms", "date", true) + " ";
			label += "\"" + d + "\" ";
			label += ". ";
			// dcterms licence
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("dcterms", "licence", true) + " ";
			label += "<" + Config.LICENCE + "> ";
			label += ". ";
			// ls identifier
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("ls", "identifier", true) + " ";
			label += "\"" + uuid + "\" ";
			label += ". ";
			// ls prefLang
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ";
			label += "\"" + prefLang + "\" ";
			label += ". ";
			// skos prefLabel (multiple, obligatory)
			for (int i = 0; i < prefLabel.length; i = i + 2) {
				label += Config.Instance("label", uuid, true) + " ";
				label += Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ";
				label += "\"" + prefLabel[i] + "\"@" + prefLabel[i + 1] + " ";
				label += ". ";
			}
			// skos altLabel (multiple, optional)
			if (altLabel.length >= 1 && !altLabel[0].equals("")) {
				for (int i = 0; i < altLabel.length; i = i + 2) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ";
					label += "\"" + altLabel[i] + "\"@" + altLabel[i + 1] + " ";
					label += ". ";
				}
			}
			// skos note (multiple, optional)
			if (note.length >= 1 && !note[0].equals("")) {
				for (int i = 0; i < note.length; i = i + 2) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "note", true) + " ";
					label += "\"" + note[i] + "\"@" + note[i + 1] + " ";
					label += ". ";
				}
			}
			// skos definition (multiple, optional)
			if (definition.length >= 1 && !definition[0].equals("")) {
				for (int i = 0; i < definition.length; i = i + 2) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "definition", true) + " ";
					label += "\"" + definition[i] + "\"@" + definition[i + 1] + " ";
					label += ". ";
				}
			}
			// links to resources
			if (broadMatch.length >= 1 && !broadMatch[0].equals("")) {
				for (String broadMatch1 : broadMatch) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "broadMatch", true) + " ";
					label += "<" + broadMatch1 + "> ";
					label += ". ";
				}
			}
			if (narrowMatch.length >= 1 && !narrowMatch[0].equals("")) {
				for (String narrowMatch1 : narrowMatch) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "narrowMatch", true) + " ";
					label += "<" + narrowMatch1 + "> ";
					label += ". ";
				}
			}
			if (relatedMatch.length >= 1 && !relatedMatch[0].equals("")) {
				for (String relatedMatch1 : relatedMatch) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "relatedMatch", true) + " ";
					label += "<" + relatedMatch1 + "> ";
					label += ". ";
				}
			}
			if (closeMatch.length >= 1 && !closeMatch[0].equals("")) {
				for (String closeMatch1 : closeMatch) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "closeMatch", true) + " ";
					label += "<" + closeMatch1 + "> ";
					label += ". ";
				}
			}
			if (exactMatch.length >= 1 && !exactMatch[0].equals("")) {
				for (String exactMatch1 : exactMatch) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("skos", "exactMatch", true) + " ";
					label += "<" + exactMatch1 + "> ";
					label += ". ";
				}
			}
			if (seeAlso.length >= 1 && !seeAlso[0].equals("")) {
				for (String seeAlso1 : seeAlso) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("rdfs", "seeAlso", true) + " ";
					label += "<" + seeAlso1 + "> ";
					label += ". ";
				}
			}
			if (isDefinedBy.length >= 1 && !isDefinedBy[0].equals("")) {
				for (String definedBy : isDefinedBy) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", true) + " ";
					label += "<" + definedBy + "> ";
					label += ". ";
				}
			}
			if (sameAs.length >= 1 && !sameAs[0].equals("")) {
				for (String sameA : sameAs) {
					label += Config.Instance("label", uuid, true) + " ";
					label += Config.getPrefixItemOfOntology("owl", "sameAs", true) + " ";
					label += "<" + sameA + "> ";
					label += ". ";
				}
			}
			//connections
			label += Config.Instance("vocabulary", vocabulary, true) + " ";
			label += Config.getPrefixItemOfOntology("ls", "contains", true) + " ";
			label += Config.Instance("label", uuid, true) + " ";
			label += ". ";
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("ls", "belongsTo", true) + " ";
			label += Config.Instance("vocabulary", vocabulary, true) + " ";
			label += ". ";
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("skos", "inScheme", true) + " ";
			label += Config.Instance("vocabulary", vocabulary, true) + " ";
			label += ". ";
			label += Config.Instance("label", uuid, true) + " ";
			label += Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ";
			label += Config.RestLabel(vocabulary, uuid, true, null) + " ";
			label += ". ";
			//end
			label += "}";
			//output and send
			SesameConnect.SPARQLupdate("labelingsystem", label);
		} catch (RepositoryException ex) {
			throw new CsvLabelImportException("[InputLabelCSV.java | importLabel()] " + ex.toString());
		} catch (MalformedQueryException ex) {
			throw new CsvLabelImportException("[InputLabelCSV.java | importLabel()] " + ex.toString());
		} catch (UpdateExecutionException ex) {
			throw new CsvLabelImportException("[InputLabelCSV.java | importLabel()] " + ex.toString());
		}
		return uuid;
	}

	/**
	 * FAKE import the Label to triplestore and build triples
	 *
	 * @param tokens
	 * @param creator
	 * @return
	 * @throws UniqueIdentifierException
	 */
	public static String importLabelVALIDATOR(String[] tokens, String creator) throws UniqueIdentifierException {
		return UniqueIdentifier.getUUIDforLabel();
	}

	public static void SPARQLupdateVALIDATOR(String repositoryID, String updateString) {
		// DO NOTHING
	}

	/**
	 * returns true if vocabulary exists
	 *
	 * @param voc
	 * @param creator
	 * @return
	 * @throws IOException
	 * @throws JDOMException
	 * @throws org.openrdf.repository.RepositoryException
	 * @throws org.openrdf.query.MalformedQueryException
	 * @throws org.openrdf.query.QueryEvaluationException
	 * @throws de.i3mainz.ls.exceptions.SesameSparqlException
	 * @throws de.i3mainz.ls.exceptions.ConfigException
	 * @throws de.i3mainz.ls.exceptions.CsvExistenceException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException
	 */
	public static boolean vocabularyExistenceCheck(String voc, String creator) throws IOException, JDOMException, RepositoryException, MalformedQueryException, QueryEvaluationException, SesameSparqlException, ConfigException, CsvExistanceException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		try {
			String vocquery = "SELECT * WHERE { "
					+ "?v a " + Config.getPrefixItemOfOntology("ls", "Vocabulary", true) + " . "
					+ "?v " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " \"" + creator + "\" . "
					+ "FILTER (?v = " + Config.Instance("vocabulary", voc, true) + ") . }";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> voc_true = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "v");
			// wenn vocab vorhanden und von creator erstellt
			return voc_true.size() > 0;
		} catch (RepositoryException e) {
			throw new CsvExistanceException();
		} catch (MalformedQueryException e) {
			throw new CsvExistanceException();
		} catch (QueryEvaluationException e) {
			throw new CsvExistanceException();
		}
	}

	/**
	 * returns true if label exists
	 *
	 * @param pref
	 * @param lang
	 * @param creator
	 * @return
	 * @throws IOException
	 * @throws JDOMException
	 * @throws org.openrdf.repository.RepositoryException
	 * @throws org.openrdf.query.MalformedQueryException
	 * @throws org.openrdf.query.QueryEvaluationException
	 * @throws de.i3mainz.ls.exceptions.SesameSparqlException
	 * @throws de.i3mainz.ls.exceptions.ConfigException
	 * @throws de.i3mainz.ls.exceptions.CsvExistenceException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 */
	public static boolean labelExistanceCheck(String pref, String lang, String creator) throws IOException, JDOMException, RepositoryException, MalformedQueryException, QueryEvaluationException, SesameSparqlException, ConfigException, CsvExistanceException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		try {
			String labquery = "SELECT * WHERE { "
					+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
					+ "?l " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " \"" + creator + "\" . "
					+ "?l " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " \"" + pref + "\"@" + lang + " . }";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> lab_true = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "l");
			// wenn vocab vorhanden und von creator erstellt
			return lab_true.size() > 0;
		} catch (RepositoryException e) {
			throw new CsvExistanceException("[InputLabelCSV.java | labelExistanceCheck(): " + e + "]");
		} catch (MalformedQueryException e) {
			throw new CsvExistanceException("[InputLabelCSV.java | labelExistanceCheck(): " + e + "]");
		} catch (QueryEvaluationException e) {
			throw new CsvExistanceException("[InputLabelCSV.java | labelExistanceCheck(): " + e + "]");
		}
	}

	/**
	 * returns true if a language appears twice
	 *
	 * @param languagestring
	 * @return
	 * @throws de.i3mainz.ls.exceptions.CsvLanguageException
	 */
	public static boolean doubleLanguageCheck(String languagestring) throws CsvLanguageException {
		try {
			boolean result = false;
			String[] ls = languagestring.split(";");
			HashSet set = new HashSet();
			if (ls.length > 1) {
				for (int i = 0; i < ls.length; i = i + 2) {
					if (set.contains(ls[i + 1])) {
						result = true;
						break;
					} else {
						set.add(ls[i + 1]);
					}
				}
			}
			return result;
		} catch (Exception e) {
			throw new CsvLanguageException();
		}
	}

	/**
	 * checks if language is allowed
	 *
	 * @param string
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.CsvLanguageException
	 */
	public static boolean languageCheck(String string) throws CsvLanguageException {
		try {
			return string.equals("de") || string.equals("en") || string.equals("fr") || string.equals("it") || string.equals("es") || string.equals("pl");
		} catch (Exception e) {
			throw new CsvLanguageException();
		}
	}

	/**
	 * Utility method to get file name from HTTP header content-disposition
	 *
	 * @param part
	 * @return
	 */
	public static String getFileName(Part part) throws FileNotFoundException {
		try {
			String contentDisp = part.getHeader("content-disposition");
			System.out.println("content-disposition header= " + contentDisp);
			String[] tokens = contentDisp.split(";");
			for (String token : tokens) {
				if (token.trim().startsWith("filename")) {
					return token.substring(token.indexOf("=") + 2, token.length() - 1);
				}
			}
			return "";
		} catch (Exception e) {
			throw new FileNotFoundException();
		}
	}

	/**
	 * check csv data and import it to the triplestore
	 *
	 * @param csvContent
	 * @param creator
	 * @param validator
	 * @param check
	 * @return
	 * @throws CsvLabelImportException
	 */
	public static String Input(String csvContent, String creator, boolean validator, boolean check) throws CsvLabelImportException {
		JSONObject jsonobj_query = new JSONObject(); // {}
		String JSON_OUT = "";
		String ids = "";
		int errors = 0;
		int relationerrors = 0;
		int warnings = 0;
		int importedlabels = 0;
		int importedrelations = 0;
		try {
			//vocabulary[0];prefLabel[1];altLabel[2];note[3];definition[4];prefLang[5];
			//broader[6];narrower[7];related[8];broadMatch[9];narrowMatch[10];relatedMatch[11];
			//closeMatch[12];exactMatch[13];seeAlso[14];isDefinedBy[15];sameAs[16];internalID[17]
			String[] csvLine = csvContent.split("\r\n");
			Map<Integer, String> labels = new HashMap<Integer, String>();
			/*
			 * check and import labels 
			 */
			boolean error = false;
			for (int i = 1; i < csvLine.length; i++) {
				InputLabelCSV.currentStep = InputLabelCSV.currentStep + 1;
				InputLabelCSV.status = ((double) InputLabelCSV.currentStep / (double) InputLabelCSV.maxSteps) * 100;
				System.out.println((double) InputLabelCSV.currentStep + " | " + (double) InputLabelCSV.maxSteps + " | " + (double) InputLabelCSV.status + " | label");
				if (validator) {
					InputLabelCSV.action = "check and import labels (check)...";
				} else {
					InputLabelCSV.action = "check and import labels...";
				}
				try {
					String[] tokens = csvLine[i].split("[\t]");
					if (tokens.length == 18) {
						// vocabulary check
						if (tokens[0].equals("")) {
							error = true;
							jsonobj_query.put(i, "ignored: no required vocabulary in line " + i);
						}
						if (CSV.vocabularyExistenceCheck(tokens[0], creator) == false) {
							error = true;
							jsonobj_query.put(i, "ignored: vocabulary not found in line " + i);
						}
						// prefLabel check
						if (tokens[1].equals("")) {
							error = true;
							jsonobj_query.put(i, "ignored: no required prefLabel in line " + i);
						} else {
							// ckeck if just one prefLabel for one language (same with note and definition)
							String[] elements = tokens[1].split(";");
							for (int j = 0; j < elements.length; j++) {
								if (j % 2 != 0) {
									if (elements[j].equals("de") || elements[j].equals("en") || elements[j].equals("fr") || elements[j].equals("it") || elements[j].equals("es") || elements[j].equals("pl")) {
									} else {
										error = true;
										jsonobj_query.put(i, "ignored: wrong prefLabel language in line " + i);
									}
								}
							}
							if (CSV.doubleLanguageCheck(tokens[1])) {
								error = true;
								jsonobj_query.put(i, "ignored: multiple prefLabel language in line " + i);
							}
						}
						if (tokens[1].split(";").length % 2 != 0) {
							error = true;
							jsonobj_query.put(i, "ignored: prefLabel language error in line " + i);
						}
						// altLabel check (optional, multiple languages allowed)
						if (!tokens[2].equals("")) {
							String[] elements = tokens[2].split(";");
							if (tokens[2].split(";").length % 2 != 0) {
								error = true;
								jsonobj_query.put(i, "ignored: altLabel language error in line " + i);
							}
							/*for (int j = 0; j < elements.length; j++) {
							 if (j % 2 != 0) {
							 if (languageCheck(elements[j])) {
							 } else {
							 error = true;
							 jsonobj_query.put(i, "ignored: altLabel wrong language in line " + i);
							 }
							 }
							 }*/
							/*if (doubleLanguageCheck(tokens[2])) {
							 error = true;
							 jsonobj_query.put(i, "ignored: multiple altLabel language in line " + i);
							 }*/
						}
						// note check (optional)
						if (!tokens[3].equals("")) {
							String[] elements = tokens[3].split(";");
							if (tokens[3].split(";").length % 2 != 0) {
								error = true;
								jsonobj_query.put(i, "ignored: note language error in line " + i);
							}
							for (int j = 0; j < elements.length; j++) {
								if (j % 2 != 0) {
									if (CSV.languageCheck(elements[j])) {
									} else {
										error = true;
										jsonobj_query.put(i, "ignored: wrong note language in line " + i);
									}
								}
							}
							if (CSV.doubleLanguageCheck(tokens[3])) {
								error = true;
								jsonobj_query.put(i, "ignored: multiple note language in line " + i);
							}
						}
						// definition check (optional)
						if (!tokens[4].equals("")) {
							String[] elements = tokens[4].split(";");
							if (tokens[4].split(";").length % 2 != 0) {
								error = true;
								jsonobj_query.put(i, "ignored: definition language error in line " + i);
							}
							for (int j = 0; j < elements.length; j++) {
								if (j % 2 != 0) {
									if (CSV.languageCheck(elements[j])) {
									} else {
										error = true;
										jsonobj_query.put(i, "ignored: wrong definition language in line " + i);
									}
								}
							}
							if (CSV.doubleLanguageCheck(tokens[4])) {
								error = true;
								jsonobj_query.put(i, "ignored: multiple definition language in line " + i);
							}
						}
						// prefLang check
						if (tokens[5].equals("")) {
							error = true;
							jsonobj_query.put(i, "ignored: no required prefLang in line " + i);
						} else {
							if (CSV.languageCheck(tokens[5])) {
							} else {
								error = true;
								jsonobj_query.put(i, "ignored: wrong prefLang language in line " + i);
							}
						}
						// broadMatch check (optional)
						if (!tokens[9].equals("")) {
							String[] elements = tokens[9].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// narrowMatch check (optional)
						if (!tokens[10].equals("")) {
							String[] elements = tokens[10].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// relatedMatch check (optional)
						if (!tokens[11].equals("")) {
							String[] elements = tokens[11].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// closeMatch check (optional)
						if (!tokens[12].equals("")) {
							String[] elements = tokens[12].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// exactMatch check (optional)
						if (!tokens[13].equals("")) {
							String[] elements = tokens[13].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// seeAlso check (optional)
						if (!tokens[14].equals("")) {
							String[] elements = tokens[14].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// isDefinedBy check (optional)
						if (!tokens[15].equals("")) {
							String[] elements = tokens[15].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						// sameAs check (optional)
						if (!tokens[16].equals("")) {
							String[] elements = tokens[16].split(";");
							for (String element : elements) {
								if (!element.contains("http://")) {
									error = true;
									jsonobj_query.put(i, "ignored: no HTTP web resource in line " + i);
								}
							}
						}
						if (!error) {
							// notice if label exists
							String[] prefLabel = tokens[1].split(";");
							int warn = 0;
							for (int k = 0; k < prefLabel.length; k = k + 2) {
								if (CSV.labelExistanceCheck(prefLabel[k], prefLabel[k + 1], creator)) {
									jsonobj_query.put(i + "-warning-" + warn, "warning: prefLabel " + prefLabel[k] + "@" + prefLabel[k + 1] + " exists in line " + i);
									warnings++;
									warn++;
								}
							}
							//import to triplestore or validate
							if (!tokens[17].equals("")) {
								if (Integer.parseInt(tokens[17]) < 1) {
									// hierarchy check
									jsonobj_query.put(i, "ignored: id smaller than 1 or not numeric in " + i);
								} else {
									if (validator) {
										// fake CSV.importLabel call
										String labelID = CSV.importLabelVALIDATOR(tokens, creator);
										if (!labelID.equals("")) { // if label is imported
											labels.put(Integer.parseInt(tokens[17]), labelID);
											importedlabels++;
										}
									} else {
										String labelID = CSV.importLabel(tokens, creator);
										if (!labelID.equals("")) { // if label is imported
											labels.put(Integer.parseInt(tokens[17]), labelID);
											jsonobj_query.put(i, labelID);
											importedlabels++;
											if (ids.equals("")) {
												ids += labelID;
											} else {
												ids += ";" + labelID;
											}
										}
									}
								}
							}
						} else {
							errors++;
						}
					} else {
						jsonobj_query.put(i, "ignored: no required id in line " + i);
					}
				} catch (Exception e) {
					jsonobj_query.put(i, "ignored: " + e.toString() + " in line " + i);
				} finally {
					error = false;
				}
			}

			/*
			 * check and import relations 
			 */
			for (int i = 1; i < csvLine.length; i++) {
				InputLabelCSV.currentStep = InputLabelCSV.currentStep + 1;
				InputLabelCSV.status = ((double) InputLabelCSV.currentStep / (double) InputLabelCSV.maxSteps) * 100;
				System.out.println((double) InputLabelCSV.currentStep + " | " + (double) InputLabelCSV.maxSteps + " | " + (double) InputLabelCSV.status + " | relation");
				if (validator) {
					InputLabelCSV.action = "check and import relations (check)...";
				} else {
					InputLabelCSV.action = "check and import relations...";
				}
				String[] tokens = csvLine[i].split("[\t]");
				if (tokens.length == 18) {
					try {
						if (!tokens[6].equals("")) {
							String[] split = tokens[6].split(";");
							for (int ii = 0; ii < split.length; ii++) {
								if (Integer.parseInt(split[ii]) < 1) {
									jsonobj_query.put(i + "-relation-b", "ignored: broader id smaller than 1 or not numeric in line " + i);
									relationerrors++;
								}
							}
						}
						if (!tokens[7].equals("")) {
							String[] split = tokens[7].split(";");
							for (int ii = 0; ii < split.length; ii++) {
								if (Integer.parseInt(split[ii]) < 1) {
									jsonobj_query.put(i + "-relation-n", "ignored: narrower id smaller than 1 or not numeric in line " + i);
									relationerrors++;
								}
							}
						}
						if (!tokens[8].equals("")) {
							String[] split = tokens[8].split(";");
							for (int ii = 0; ii < split.length; ii++) {
								if (Integer.parseInt(split[ii]) < 1) {
									jsonobj_query.put(i + "-relation-r", "ignored: related id smaller than 1 or not numeric in line " + i);
									relationerrors++;
								}
							}
						}
					} catch (Exception e) {
						jsonobj_query.put(i + "-relation", "ignored: " + e.toString() + " in line " + i);
						relationerrors++;
					}
					// check if label is correct
					boolean labelimported = false;
					String labelUUID = "";
					int internalID = Integer.parseInt(tokens[17]);
					for (Object key : labels.keySet()) {
						Object value = labels.get(key);
						if (internalID == Integer.parseInt(key.toString())) {
							labelimported = true;
							labelUUID = value.toString();
						}
					}
					// action of broader realation
					if (!tokens[6].equals("")) {
						try {
							String[] split = tokens[6].split(";");
							for (int ii = 0; ii < split.length; ii++) {
								// check if broader label exists
								boolean labelimported_broader = false;
								String labelUUID_broader = "";
								int broaderID = Integer.parseInt(split[ii]);
								for (Object key : labels.keySet()) {
									Object value = labels.get(key);
									if (broaderID == Integer.parseInt(key.toString())) {
										labelimported_broader = true;
										labelUUID_broader = value.toString();
									}
								}
								if (labelimported_broader) {
									try {
										String label = "INSERT DATA { ";
										// broader
										label += Config.Instance("label", labelUUID, true) + " ";
										label += Config.getPrefixItemOfOntology("skos", "broader", true) + " ";
										label += Config.Instance("label", labelUUID_broader, true) + " ";
										label += ". ";
										// narrower
										label += Config.Instance("label", labelUUID_broader, true) + " ";
										label += Config.getPrefixItemOfOntology("skos", "narrower", true) + " ";
										label += Config.Instance("label", labelUUID, true) + " ";
										label += ". ";
										// end
										label += "}";
										if (validator) {
											CSV.SPARQLupdateVALIDATOR("labelingsystem", label);
										} else {
											SesameConnect.SPARQLupdate("labelingsystem", label);
										}
										importedrelations++;
									} catch (Exception ex) {
										throw new IllegalArgumentException("[InputLabelCSV.java | processRequest()] " + ex.toString());
									}
									if (!validator) {
										jsonobj_query.put(i + "-relation-b", "relation imported");
									}
								} else {
									jsonobj_query.put(i + "-relation-b", "ignored: wrong broader-relation id in " + i);
									relationerrors++;
								}
							}
						} catch (Exception e) {
							jsonobj_query.put(i + "-relation-b", "ignored: " + e.toString() + " in line " + i);
							relationerrors++;
						}
					}
					if (!tokens[7].equals("")) { // action of narrower realation
						try {
							String[] split = tokens[7].split(";");
							for (int ii = 0; ii < split.length; ii++) {
								// check if narrower  label exists
								boolean labelimported_narrower = false;
								String labelUUID_narrower = "";
								int narrowerID = Integer.parseInt(split[ii]);
								for (Object key : labels.keySet()) {
									Object value = labels.get(key);
									if (narrowerID == Integer.parseInt(key.toString())) {
										labelimported_narrower = true;
										labelUUID_narrower = value.toString();
									}
								}
								if (labelimported_narrower) {
									try {
										String label = "INSERT DATA { ";
										// narrower
										label += Config.Instance("label", labelUUID, true) + " ";
										label += Config.getPrefixItemOfOntology("skos", "narrower", true) + " ";
										label += Config.Instance("label", labelUUID_narrower, true) + " ";
										label += ". ";
										// broader
										label += Config.Instance("label", labelUUID_narrower, true) + " ";
										label += Config.getPrefixItemOfOntology("skos", "broader", true) + " ";
										label += Config.Instance("label", labelUUID, true) + " ";
										label += ". ";
										// end
										label += "}";
										if (validator) {
											CSV.SPARQLupdateVALIDATOR("labelingsystem", label);
										} else {
											SesameConnect.SPARQLupdate("labelingsystem", label);
										}
										importedrelations++;
									} catch (RepositoryException e) {
										throw new CsvLabelImportException(e.toString());
									} catch (MalformedQueryException e) {
										throw new CsvLabelImportException(e.toString());
									} catch (UpdateExecutionException e) {
										throw new CsvLabelImportException(e.toString());
									}
									if (!validator) {
										jsonobj_query.put(i + "-relation-n", "relation imported");
									}
								} else {
									jsonobj_query.put(i + "-relation-n", "ignored: wrong narrower-relation id in " + i);
									relationerrors++;
								}
							}
						} catch (Exception e) {
							jsonobj_query.put(i + "-relation-n", "ignored: " + e.toString() + " in line " + i);
							relationerrors++;
						}
					}
					if (!tokens[8].equals("")) { // action of related realation
						try {
							String[] split = tokens[8].split(";");
							for (int ii = 0; ii < split.length; ii++) {
								// check if related label exists
								boolean labelimported_related = false;
								String labelUUID_related = "";
								int relatedID = Integer.parseInt(split[ii]);
								for (Object key : labels.keySet()) {
									Object value = labels.get(key);
									if (relatedID == Integer.parseInt(key.toString())) {
										labelimported_related = true;
										labelUUID_related = value.toString();
									}
								}
								if (labelimported_related) {
									try {
										String label = "INSERT DATA { ";
										// related
										label += Config.Instance("label", labelUUID, true) + " ";
										label += Config.getPrefixItemOfOntology("skos", "related", true) + " ";
										label += Config.Instance("label", labelUUID_related, true) + " ";
										label += ". ";
										label += "}";
										if (validator) {
											CSV.SPARQLupdateVALIDATOR("labelingsystem", label);
										} else {
											SesameConnect.SPARQLupdate("labelingsystem", label);
										}
										importedrelations++;
									} catch (RepositoryException e) {
										throw new CsvLabelImportException("[InputLabelCSV.java] " + e.toString());
									} catch (MalformedQueryException e) {
										throw new CsvLabelImportException("[InputLabelCSV.java] " + e.toString());
									} catch (UpdateExecutionException e) {
										throw new CsvLabelImportException("[InputLabelCSV.java] " + e.toString());
									}
									if (!validator) {
										jsonobj_query.put(i + "-relation-r", "relation imported");
									}
								} else {
									jsonobj_query.put(i + "-relation-r", "ignored: wrong related-relation id in " + i);
									relationerrors++;
								}
							}
						} catch (Exception e) {
							jsonobj_query.put(i + "-relation-r", "ignored: " + e.toString() + " in line " + i);
							relationerrors++;
						}
					} // end action of broader realation
				} else {
					jsonobj_query.put(i, "ignored: no required id in line " + i);
					relationerrors++;
				} // end length query
			}
			if (validator) {
				jsonobj_query.put("errors", errors);
				jsonobj_query.put("relationerrors", relationerrors);
				jsonobj_query.put("warnings", warnings);
				jsonobj_query.put("importedlabels", importedlabels);
				jsonobj_query.put("importedrelations", importedrelations);
				if (errors == 0 && relationerrors == 0) {
					jsonobj_query.put("success", "true");
				} else {
					jsonobj_query.put("success", "false");
				}
			} else {
				jsonobj_query.put("ids", ids);
				jsonobj_query.put("errors", errors);
				jsonobj_query.put("relationerrors", relationerrors);
				jsonobj_query.put("warnings", warnings);
				jsonobj_query.put("importedlabels", importedlabels);
				jsonobj_query.put("importedrelations", importedrelations);
				if (errors == 0 && relationerrors == 0) {
					jsonobj_query.put("success", "true");
				} else {
					jsonobj_query.put("success", "false");
				}
			}
			// pretty print JSON output (Gson)
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			JSON_OUT = gson.toJson(jsonobj_query);
			InputLabelCSV.status = 100.0; // finish
			InputLabelCSV.action = "done!"; // finish
			System.out.println("=================================");
			JSON_STRING = JSON_OUT;
			return JSON_OUT;
		} catch (Exception e) {
			throw new CsvLabelImportException();
		}
	}

	@Override
	/**
	 * Runnable RUN method to let it run
	 */
	public void run() {
		try {
			if (InputLabelCSV.validator) {
				// Input Test
				Input(InputLabelCSV.csvContent, InputLabelCSV.creator, InputLabelCSV.validator, true);
			} else {
				// Input Test
				String JSON = Input(InputLabelCSV.csvContent, InputLabelCSV.creator, true, false);
				if (JSON.contains("\"success\": \"true\"")) {
					// Real Input
					Input(InputLabelCSV.csvContent, InputLabelCSV.creator, false, false);
				}
			}
		} catch (CsvLabelImportException ex) {
			Logger.getLogger(CSV.class.getName()).log(Level.SEVERE, null, ex);
		}
	}

}
