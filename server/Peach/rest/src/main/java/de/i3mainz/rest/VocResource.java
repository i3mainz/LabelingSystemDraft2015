package de.i3mainz.rest;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.rdfutils.RDF;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.exceptions.ConfigException;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import de.i3mainz.ls.rdfutils.exceptions.RdfException;
import de.i3mainz.ls.rdfutils.exceptions.ResourceNotAvailableException;
import de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;
import org.jdom.JDOMException;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.openrdf.query.BindingSet;
import org.openrdf.query.MalformedQueryException;
import org.openrdf.query.QueryEvaluationException;
import org.openrdf.repository.RepositoryException;

/**
 * REST CLASS of resource vocabs
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.03.2015
 */
@Path("/vocabs")
public class VocResource {

	/**
	 * XML list of vocabularies
	 *
	 * @return XML
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getVocabulariesXML() throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT ?label ?identifier WHERE { "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier . "
					+ "?s a " + Config.getPrefixItemOfOntology("ls", "Vocabulary", true) + " . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "state", true) + " \"public\" . "
					+ "?s " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label . "
					+ "}";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "label");
			List<String> ids = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "identifier");
			String xml = "";
			xml += "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" + "\n";
			xml += "<collection id=\"vocabs\" href=\"" + Config.RestVocabularies(false) + "\">" + "\n";
			for (int i = 0; i < ids.size(); i++) {
				xml += "\t" + "<vocabulary id=\"" + ids.get(i) + "\" "
						+ "label=\"" + labels.get(i).replaceAll("\"", "") + "\" "
						+ "href=\"" + Config.RestVocabulary(ids.get(i), false, false) + "\"/>" + "\n";
			}
			xml += "</collection>";
			return Response.ok(xml).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * XML list of labels
	 *
	 * @param voc
	 * @return XML
	 * @throws java.io.IOException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 * @throws org.jdom.JDOMException
	 * @throws org.openrdf.query.MalformedQueryException
	 * @throws org.openrdf.query.QueryEvaluationException
	 * @throws org.openrdf.repository.RepositoryException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}")
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getLabelsXML(@PathParam("voc") String voc) throws IOException, JDOMException, ConfigException, RepositoryException, MalformedQueryException, QueryEvaluationException, SesameSparqlException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT ?label WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label . "
					+ "}";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> voclabel = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "label");
			if (voclabel.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			String xml = "";
			xml += "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>" + "\n";
			xml += "<collection id=\"vocabs\" href=\"" + Config.RestVocabularies(false) + "\">" + "\n";
			xml += "\t" + "<vocabulary id=\"" + voc + "\" "
					+ "label=\"" + voclabel.get(0).replaceAll("\"", "") + "\" "
					+ "href=\"" + Config.RestVocabulary(voc, false, false) + "\">" + "\n";
			String labelquery = "SELECT * WHERE { "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier . "
					+ Config.Instance("vocabulary", voc, true) + " " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?s . "
					+ "?s " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?label . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
					+ "FILTER(LANGMATCHES(LANG(?label), ?prefLang))"
					+ "}";
			List<BindingSet> label_result = SesameConnect.SPARQLquery("labelingsystem", labelquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "label");
			List<String> ids = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "identifier");
			if (labels.size() < 1) {
				xml += "\t\t" + "<label/>" + "\n";
			} else {
				for (int i = 0; i < ids.size(); i++) {
					xml += "\t\t" + "<label id=\"" + ids.get(i) + "\" "
							+ "label=\"" + labels.get(i).replaceAll("\"", "") + "\" "
							+ "href=\"" + Config.RestLabel(voc, ids.get(i), false, null) + "\"/>" + "\n";
				}
			}
			xml += "\t" + "</vocabulary>" + "\n";
			xml += "</collection>";
			return Response.ok(xml).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of vocabulary
	 *
	 * @param voc
	 * @return RDF/XML
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.rdf")
	@Produces("application/rdf+xml;charset=UTF-8")
	public Response getConceptSchemeRDF(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("vocabulary", voc, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/Turtle representation of vocabulary
	 *
	 * @param voc
	 * @return RDF/Turtle
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.ttl")
	@Produces("text/turtle;charset=UTF-8")
	public Response getConceptSchemeTTL(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("vocabulary", voc, false), predicates.get(i), objects.get(i));
			}
			return Response.ok(rdf.getModel("Turtle")).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}

	}

	/**
	 * N3 representation of vocabulary
	 *
	 * @param voc
	 * @return N3
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.n3")
	@Produces("text/n3;charset=UTF-8")
	public Response getConceptSchemeN3(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("vocabulary", voc, false), predicates.get(i), objects.get(i));
			}
			return Response.ok(rdf.getModel("N-Triples")).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}

	}

	/**
	 * JSON-LD representation of vocabulary
	 *
	 * @param voc
	 * @return JSON-ID
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.jsonld")
	@Produces("application/ld+json;charset=UTF-8")
	public Response getConceptSchemeJSONLD(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("vocabulary", voc, false), predicates.get(i), objects.get(i));
			}
			return Response.ok(rdf.getModel("JSON-LD")).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}

	}

	/**
	 * RDF/XML representation of vocabulary as download file
	 *
	 * @param voc
	 * @return RDF/XML
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.skos")
	@Produces("application/rdf+xml;charset=UTF-8")
	public Response getConceptSchemeSKOS(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String vocquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("vocabulary", voc, false), predicates.get(i), objects.get(i));
			}
			String labelquery = "SELECT ?identifier WHERE { "
					+ Config.Instance("vocabulary", voc, true) + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?label. "
					+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier . "
					+ "} "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> label_result = SesameConnect.SPARQLquery("labelingsystem", labelquery);
			List<String> predicates_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "identifier");
			for (String predicates_label1 : predicates_label) {
				String labquery = "SELECT * WHERE { " + Config.Instance("label", predicates_label1, true) + " ?p ?o. } " + "ORDER BY ASC(?p)";
				List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
				List<String> predicates_lab = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
				List<String> objects_lab = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
				for (int j = 0; j < predicates_lab.size(); j++) {
					rdf.setModelTriple(Config.Instance("label", predicates_label1, false), predicates_lab.get(j), objects_lab.get(j));
				}
			}
			String RDFoutput = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").header("Content-disposition", "attachment;filename=" + voc + ".rdf").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * CSV representation of vocabulary as CSV download file
	 *
	 * @param voc
	 * @return CSV
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.tsv")
	@Produces("text/tsv;charset=UTF-8")
	public Response getConceptSchemeCSV(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		String output = "vocabularyID\tprefLabel\taltLabel\tnote\tdefinition\tprefLang\tbroader\tnarrower\trelated\tbroadMatch\tnarrowMatch\trelatedMatch\tcloseMatch\texactMatch\tseeAlso\tisDefinedBy\tsameAs\tinternalID\n";
		Map map = new HashMap<Integer, String>();
		try {
			// SELECT LABELS OF VOVABULARY
			String vocquery = "SELECT ?identifier WHERE { "
					+ Config.Instance("vocabulary", voc, true) + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?label. "
					+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier. "
					+ " } ";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "identifier");
			if (labels.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			// WRITE TO HASHMAP
			for (int j = 0; j < labels.size(); j++) {
				map.put((j + 1), labels.get(j));
			}
			// QUERY LABEL OBJECT
			for (int j = 0; j < labels.size(); j++) {
				String url = Config.LABELINGSERVER + "getLabel?id=" + labels.get(j);
				URL obj = new URL(url);
				HttpURLConnection con = (HttpURLConnection) obj.openConnection();
				con.setRequestMethod("GET");
				BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));
				String inputLine;
				String response = "";
				while ((inputLine = in.readLine()) != null) {
					response += inputLine;
				}
				in.close();
				// PARSE JSON AND WRITE OUTPUT
				JSONParser JSONparser = new JSONParser();
				JSONObject jsonObject = (JSONObject) JSONparser.parse(response);
				JSONArray data_array = (JSONArray) jsonObject.get("data");
				JSONObject data_object = (JSONObject) data_array.get(0);
				// vocabulary
				output += voc + "\t";
				// prefLabels
				JSONArray prefLabels_array = (JSONArray) data_object.get("prefLabels");
				for (Object prefLabel1 : prefLabels_array) {
					JSONObject prefLabels_object = (JSONObject) prefLabel1;
					String prefLabel = (String) prefLabels_object.get("prefLabel");
					output += prefLabel.split("@")[0].replace("\"", "") + ";" + prefLabel.split("@")[1] + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// altLabels
				JSONArray altLabels_array = (JSONArray) data_object.get("altLabels");
				for (Object altLabel1 : altLabels_array) {
					JSONObject altLabels_object = (JSONObject) altLabel1;
					String altLabel = (String) altLabels_object.get("altLabel");
					output += altLabel.split("@")[0].replace("\"", "") + ";" + altLabel.split("@")[1] + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// notes
				JSONArray notes_array = (JSONArray) data_object.get("notes");
				for (Object note1 : notes_array) {
					JSONObject notes_object = (JSONObject) note1;
					String note = (String) notes_object.get("note");
					output += note.split("@")[0].replace("\"", "") + ";" + note.split("@")[1] + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// definitions
				JSONArray definitions_array = (JSONArray) data_object.get("definitions");
				for (Object definition1 : definitions_array) {
					JSONObject definitions_object = (JSONObject) definition1;
					String definition = (String) definitions_object.get("definition");
					output += definition.split("@")[0].replace("\"", "") + ";" + definition.split("@")[1] + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// prefLang
				String prefLang = (String) data_object.get("prefLang");
				output += prefLang + "\t";
				// broader
				JSONArray broader_array = (JSONArray) data_object.get("broader");
				for (Object broader1 : broader_array) {
					JSONObject broader_object = (JSONObject) broader1;
					String broader = (String) broader_object.get("broader");
					broader = broader.split("#")[1];
					String internalID = "";
					if (map.containsValue(broader)) {
						for (Object o : map.keySet()) {
							if (map.get(o).equals(broader)) {
								internalID = o.toString();
								output += internalID + ";";
							}
						}
					}
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// narrower
				JSONArray narrower_array = (JSONArray) data_object.get("narrower");
				for (Object narrower1 : narrower_array) {
					JSONObject narrower_object = (JSONObject) narrower1;
					String narrower = (String) narrower_object.get("narrower");
					narrower = narrower.split("#")[1];
					String internalID = "";
					if (map.containsValue(narrower)) {
						for (Object o : map.keySet()) {
							if (map.get(o).equals(narrower)) {
								internalID = o.toString();
								output += internalID + ";";
							}
						}
					}
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// related
				JSONArray related_array = (JSONArray) data_object.get("related");
				for (Object related1 : related_array) {
					JSONObject related_object = (JSONObject) related1;
					String related = (String) related_object.get("related");
					related = related.split("#")[1];
					String internalID = "";
					if (map.containsValue(related)) {
						for (Object o : map.keySet()) {
							if (map.get(o).equals(related)) {
								internalID = o.toString();
								output += internalID + ";";
							}
						}
					}
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// broadMatch
				JSONArray broadMatch_array = (JSONArray) data_object.get("broadMatch");
				for (Object broadMatch1 : broadMatch_array) {
					JSONObject broadMatch_object = (JSONObject) broadMatch1;
					String broadMatch = (String) broadMatch_object.get("broadMatch");
					output += broadMatch + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// narrowMatch
				JSONArray narrowMatch_array = (JSONArray) data_object.get("narrowMatch");
				for (Object narrowMatch1 : narrowMatch_array) {
					JSONObject narrowMatch_object = (JSONObject) narrowMatch1;
					String narrowMatch = (String) narrowMatch_object.get("narrowMatch");
					output += narrowMatch + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// relatedMatch
				JSONArray relatedMatch_array = (JSONArray) data_object.get("relatedMatch");
				for (Object relatedMatch1 : relatedMatch_array) {
					JSONObject relatedMatch_object = (JSONObject) relatedMatch1;
					String relatedMatch = (String) relatedMatch_object.get("relatedMatch");
					output += relatedMatch + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// closeMatch
				JSONArray closeMatch_array = (JSONArray) data_object.get("closeMatch");
				for (Object closeMatch1 : closeMatch_array) {
					JSONObject closeMatch_object = (JSONObject) closeMatch1;
					String closeMatch = (String) closeMatch_object.get("closeMatch");
					output += closeMatch + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// exactMatch
				JSONArray exactMatch_array = (JSONArray) data_object.get("exactMatch");
				for (Object exactMatch1 : exactMatch_array) {
					JSONObject exactMatch_object = (JSONObject) exactMatch1;
					String exactMatch = (String) exactMatch_object.get("exactMatch");
					output += exactMatch + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// seeAlso
				JSONArray seeAlso_array = (JSONArray) data_object.get("seeAlso");
				for (Object seeAlso1 : seeAlso_array) {
					JSONObject seeAlso_object = (JSONObject) seeAlso1;
					String seeAlso = (String) seeAlso_object.get("seeAlso");
					output += seeAlso + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// isDefinedBy
				JSONArray isDefinedBy_array = (JSONArray) data_object.get("isDefinedBy");
				for (Object isDefinedBy1 : isDefinedBy_array) {
					JSONObject isDefinedBy_object = (JSONObject) isDefinedBy1;
					String isDefinedBy = (String) isDefinedBy_object.get("isDefinedBy");
					output += isDefinedBy + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// sameAs
				JSONArray sameAs_array = (JSONArray) data_object.get("sameAs");
				for (Object sameAs1 : sameAs_array) {
					JSONObject sameAs_object = (JSONObject) sameAs1;
					String sameAs = (String) sameAs_object.get("sameAs");
					output += sameAs + ";";
				}
				if (output.charAt(output.length() - 1) == ';') {
					output = output.substring(0, output.length() - 1);
				}
				output += "\t";
				// internalID
				output += (j + 1) + "\n";
			}
			return Response.ok(output).header("Access-Control-Allow-Origin", "*").header("Content-disposition", "attachment;filename=" + voc + ".tsv").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * CSV representation of nodes in a vocabulary as download file
	 * @param voc
	 * @return CSV
	 * @throws IOException
	 * @throws JDOMException
	 * @throws RdfException
	 * @throws TransformerException
	 * @throws ParserConfigurationException 
	 */
	@GET
	@Path("/{voc}.gephiN")
	@Produces("text/csv;charset=UTF-8")
	public Response getConceptSchemeGephiNodesCSV(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		String OUTSTRING = "";
		try {
			List<String> NODES_TEMP = new ArrayList<String>();
			List<String> NODES = new ArrayList<String>();
			NODES.add("Id;Label");
			List<String> EDGES = new ArrayList<String>();
			EDGES.add("Source;Target;Type;Id;Label;Weight");
			HashSet NODE_URIS = new HashSet();
			Hashtable<Integer, String> URI_hashtable = new Hashtable<Integer, String>();
			Hashtable<String, String> label_hashtable = new Hashtable<String, String>();
			// VOCABULARY
			String vocquery = "SELECT ?label WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label . "
					+ "}";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> voclabel = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "label");
			if (voclabel.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			// NODES
			String labelquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?s . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang . "
					+ "?s " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?label . "
					+ "FILTER(LANGMATCHES(LANG(?label), ?prefLang))"
					+ "} ORDER BY (?identifier)";
			List<BindingSet> label_result = SesameConnect.SPARQLquery("labelingsystem", labelquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "s");
			List<String> label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "label");
			int z = 0;
			for (int j = 0; j < labels.size(); j++) {
				NODE_URIS.add(labels.get(j));
				label_hashtable.put(labels.get(j), label.get(j));
				String relquery = "SELECT * WHERE { "
						+ "?s ?p ?o "
						+ "FILTER (?s = <" + labels.get(j) + ">)"
						+ "}";
				List<BindingSet> rel_result = SesameConnect.SPARQLquery("labelingsystem", relquery);
				List<String> p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "p");
				List<String> o = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "o");
				for (int jj = 0; jj < p.size(); jj++) {
					if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "broader", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "narrower", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "related", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "broadMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "narrowMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "relatedMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "exactMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "closeMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("owl", "sameAs", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "seeAlso", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "isDefinedBy", false))) {
						NODE_URIS.add(o.get(jj));
					}
				}
			}
			Iterator it = NODE_URIS.iterator();
			int i = 0;
			while (it.hasNext()) {
				String setText = (String) it.next();
				NODES_TEMP.add(i + ";" + setText);
				URI_hashtable.put(i, setText);
				i++;
			}
			for (int j = 0; j < NODES_TEMP.size(); j++) {
				String split[] = NODES_TEMP.get(j).split(";");
				if (label_hashtable.containsKey(split[1])) {
					NODES.add(j + ";" + label_hashtable.get(split[1]));
				} else {
					NODES.add(j + ";" + split[1]);
				}
			}
			for (String OUT_ITEM : NODES) {
				OUT_ITEM = OUT_ITEM.replace(Config.Instance_LABEL, "");
				OUT_ITEM = OUT_ITEM.replaceAll("\"", "");
				OUTSTRING += OUT_ITEM + "\r\n";
			}
			//EDGES
			int edgeID = 0;
			for (int j = 0; j < labels.size(); j++) {
				NODE_URIS.add(labels.get(j));
				String relquery = "SELECT * WHERE { "
						+ "?s ?p ?o "
						+ "FILTER (?s = <" + labels.get(j) + ">)"
						+ "}";
				List<BindingSet> rel_result = SesameConnect.SPARQLquery("labelingsystem", relquery);
				List<String> o = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "o");
				List<String> p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "p");
				String s;
				for (int jj = 0; jj < o.size(); jj++) {
					int target = -1;
					int source = -1;
					for (Integer elem : URI_hashtable.keySet()) {
						s = URI_hashtable.get(elem);
						if (s.equals(labels.get(j))) { // source = label
							source = elem;
							break;
						}
					}
					for (Integer elem : URI_hashtable.keySet()) { // target = relation object
						s = URI_hashtable.get(elem);
						if (s.equals(o.get(jj))) {
							target = elem;
							break;
						}
					}
					if (target == -1 || source == -1) {
					} else {
						EDGES.add(source + ";" + target + ";" + "Directed;" + edgeID + ";" + p.get(jj) + ";1");
						edgeID++;
					}
				}
			}
			for (String OUT_ITEM : EDGES) {
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "broader", false), "broader");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "narrower", false), "narrower");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "related", false), "related");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "broadMatch", false), "broadMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "narrowMatch", false), "narrowMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "relatedMatch", false), "relatedMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "closeMatch", false), "closeMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "exactMatch", false), "exactMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("owl", "sameAs", false), "sameAs");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("rdfs", "seeAlso", false), "seeAlso");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", false), "isDefinedBy");
				//OUTSTRING += OUT_ITEM + "\r\n";
			}
			return Response.ok(OUTSTRING).header("Access-Control-Allow-Origin", "*").header("Content-disposition", "attachment;filename=" + voc + "_nodes.csv").build();
			//return Response.ok(OUTSTRING).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * CSV representation of edges in a vocabulary as download file
	 * @param voc
	 * @return
	 * @throws IOException
	 * @throws JDOMException
	 * @throws RdfException
	 * @throws TransformerException
	 * @throws ParserConfigurationException 
	 */
	@GET
	@Path("/{voc}.gephiE")
	@Produces("text/csv;charset=UTF-8")
	public Response getConceptSchemeGephiEdgesCSV(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		String OUTSTRING = "";
		try {
			List<String> NODES_TEMP = new ArrayList<String>();
			List<String> NODES = new ArrayList<String>();
			NODES.add("Id;Label");
			List<String> EDGES = new ArrayList<String>();
			EDGES.add("Source;Target;Type;Id;Label;Weight");
			HashSet NODE_URIS = new HashSet();
			Hashtable<Integer, String> URI_hashtable = new Hashtable<Integer, String>();
			Hashtable<String, String> label_hashtable = new Hashtable<String, String>();
			// VOCABULARY
			String vocquery = "SELECT ?label WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label . "
					+ "}";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
			List<String> voclabel = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "label");
			if (voclabel.size() < 1) {
				throw new ResourceNotAvailableException("vocabulary");
			}
			// NODES
			String labelquery = "SELECT * WHERE { "
					+ Config.Instance("vocabulary", voc, true) + " " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?s . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang . "
					+ "?s " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?label . "
					+ "FILTER(LANGMATCHES(LANG(?label), ?prefLang))"
					+ "} ORDER BY (?identifier)";
			List<BindingSet> label_result = SesameConnect.SPARQLquery("labelingsystem", labelquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "s");
			List<String> label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "label");
			int z = 0;
			for (int j = 0; j < labels.size(); j++) {
				NODE_URIS.add(labels.get(j));
				label_hashtable.put(labels.get(j), label.get(j));
				String relquery = "SELECT * WHERE { "
						+ "?s ?p ?o "
						+ "FILTER (?s = <" + labels.get(j) + ">)"
						+ "}";
				List<BindingSet> rel_result = SesameConnect.SPARQLquery("labelingsystem", relquery);
				List<String> p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "p");
				List<String> o = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "o");
				for (int jj = 0; jj < p.size(); jj++) {
					if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "broader", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "narrower", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "related", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "broadMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "narrowMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "relatedMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "exactMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "closeMatch", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("owl", "sameAs", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "seeAlso", false))) {
						NODE_URIS.add(o.get(jj));
					} else if (p.get(jj).equals(Config.getPrefixItemOfOntology("skos", "isDefinedBy", false))) {
						NODE_URIS.add(o.get(jj));
					}
				}
			}
			Iterator it = NODE_URIS.iterator();
			int i = 0;
			while (it.hasNext()) {
				String setText = (String) it.next();
				NODES_TEMP.add(i + ";" + setText);
				URI_hashtable.put(i, setText);
				i++;
			}
			for (int j = 0; j < NODES_TEMP.size(); j++) {
				String split[] = NODES_TEMP.get(j).split(";");
				if (label_hashtable.containsKey(split[1])) {
					NODES.add(j + ";" + label_hashtable.get(split[1]));
				} else {
					NODES.add(j + ";" + split[1]);
				}
			}
			for (String OUT_ITEM : NODES) {
				OUT_ITEM = OUT_ITEM.replace(Config.Instance_LABEL, "");
				OUT_ITEM = OUT_ITEM.replaceAll("\"", "");
				//OUTSTRING += OUT_ITEM + "\r\n";
			}
			//EDGES
			int edgeID = 0;
			for (int j = 0; j < labels.size(); j++) {
				NODE_URIS.add(labels.get(j));
				String relquery = "SELECT * WHERE { "
						+ "?s ?p ?o "
						+ "FILTER (?s = <" + labels.get(j) + ">)"
						+ "}";
				List<BindingSet> rel_result = SesameConnect.SPARQLquery("labelingsystem", relquery);
				List<String> o = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "o");
				List<String> p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(rel_result, "p");
				String s;
				for (int jj = 0; jj < o.size(); jj++) {
					int target = -1;
					int source = -1;
					for (Integer elem : URI_hashtable.keySet()) {
						s = URI_hashtable.get(elem);
						if (s.equals(labels.get(j))) { // source = label
							source = elem;
							break;
						}
					}
					for (Integer elem : URI_hashtable.keySet()) { // target = relation object
						s = URI_hashtable.get(elem);
						if (s.equals(o.get(jj))) {
							target = elem;
							break;
						}
					}
					if (target == -1 || source == -1) {
					} else {
						EDGES.add(source + ";" + target + ";" + "Directed;" + edgeID + ";" + p.get(jj) + ";1");
						edgeID++;
					}
				}
			}
			for (String OUT_ITEM : EDGES) {
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "broader", false), "broader");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "narrower", false), "narrower");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "related", false), "related");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "broadMatch", false), "broadMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "narrowMatch", false), "narrowMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "relatedMatch", false), "relatedMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "closeMatch", false), "closeMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("skos", "exactMatch", false), "exactMatch");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("owl", "sameAs", false), "sameAs");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("rdfs", "seeAlso", false), "seeAlso");
				OUT_ITEM = OUT_ITEM.replace(Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", false), "isDefinedBy");
				OUTSTRING += OUT_ITEM + "\r\n";
			}
			return Response.ok(OUTSTRING).header("Access-Control-Allow-Origin", "*").header("Content-disposition", "attachment;filename=" + voc + "_edges.csv").build();
			//return Response.ok(OUTSTRING).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * JSON representation of vocabulary
	 *
	 * @param voc
	 * @return JSON
	 * @throws IOException
	 * @throws JDOMException
	 * @throws RdfException
	 * @throws TransformerException
	 * @throws ParserConfigurationException
	 */
	@GET
	@Path("/{voc}.json")
	@Produces(MediaType.APPLICATION_JSON + ";charset=UTF-8")
	public Response getConceptSchemeJSON(@PathParam("voc") String voc) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String JSONOUT = null;
			String url = Config.LABELINGSERVER + "getVocabulary?id=" + voc;
			URL obj = new URL(url);
			HttpURLConnection con = (HttpURLConnection) obj.openConnection();
			con.setRequestMethod("GET");
			BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));
			String inputLine;
			String response = "";
			while ((inputLine = in.readLine()) != null) {
				response += inputLine;
			}
			in.close();
			JSONParser jsonParser = new JSONParser();
			JSONObject simpleJSONout = (JSONObject) jsonParser.parse(response);
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			JSONOUT = gson.toJson(simpleJSONout);
			return Response.ok(JSONOUT).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of label
	 *
	 * @param voc
	 * @param label
	 * @return RDF/XML
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}")
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getLabelXML(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String labquery = "SELECT * WHERE { "
					+ Config.Instance("label", label, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("label");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("label", label, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = rdf.getModel("RDF/XML");
			return Response.ok("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of label
	 *
	 * @param voc
	 * @param label
	 * @return RDF/XML
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}.rdf")
	@Produces("application/rdf+xml;charset=UTF-8")
	public Response getLabelRDF_RDF(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String labquery = "SELECT * WHERE { "
					+ Config.Instance("label", label, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("label");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("label", label, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = rdf.getModel("RDF/XML");
			return Response.ok("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/Turtle representation of label
	 *
	 * @param voc
	 * @param label
	 * @return RDF/Turtle
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}.ttl")
	@Produces("text/turtle;charset=UTF-8")
	public Response getLabelRDF_Turtle(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String labquery = "SELECT * WHERE { "
					+ Config.Instance("label", label, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("label");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("label", label, false), predicates.get(i), objects.get(i));
			}
			return Response.ok(rdf.getModel("Turtle")).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * N3 representation of label
	 *
	 * @param voc
	 * @param label
	 * @return N3
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}.n3")
	@Produces("text/n3;charset=UTF-8")
	public Response getLabelRDF_N3(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String labquery = "SELECT * WHERE { "
					+ Config.Instance("label", label, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("label");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("label", label, false), predicates.get(i), objects.get(i));
			}
			return Response.ok(rdf.getModel("N-Triples")).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * JSON-LD representation of label
	 *
	 * @param voc
	 * @param label
	 * @return JSON-LD
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}.jsonld")
	@Produces("application/ld+json;charset=UTF-8")
	public Response getLabelRDF_JSONLD(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String labquery = "SELECT * WHERE { "
					+ Config.Instance("label", label, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("label");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("label", label, false), predicates.get(i), objects.get(i));
			}
			return Response.ok(rdf.getModel("JSON-LD")).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of label as download file
	 *
	 * @param voc
	 * @param label
	 * @return RDF/XML
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}.skos")
	@Produces("application/rdf+xml;charset=UTF-8")
	public Response getLabelRDF_SKOS(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String labquery = "SELECT * WHERE { "
					+ Config.Instance("label", label, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> lab_result = SesameConnect.SPARQLquery("labelingsystem", labquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(lab_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("label");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("label", label, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").header("Content-disposition", "attachment;filename=" + label + ".rdf").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * JSON representation of label
	 *
	 * @param voc
	 * @param label
	 * @return JSON
	 * @throws IOException
	 * @throws JDOMException
	 * @throws TransformerException
	 * @throws ParserConfigurationException
	 */
	@GET
	@Path("/{voc}/{label}.json")
	@Produces(MediaType.APPLICATION_JSON + ";charset=UTF-8")
	public Response getLabelJSON(@PathParam("voc") String voc, @PathParam("label") String label) throws IOException, JDOMException, TransformerException, ParserConfigurationException {
		try {
			String JSONOUT = null;
			String url = Config.LABELINGSERVER + "getLabel?id=" + label;
			URL obj = new URL(url);
			HttpURLConnection con = (HttpURLConnection) obj.openConnection();
			con.setRequestMethod("GET");
			BufferedReader in = new BufferedReader(new InputStreamReader(con.getInputStream()));
			String inputLine;
			String response = "";
			while ((inputLine = in.readLine()) != null) {
				response += inputLine;
			}
			in.close();
			JSONParser jsonParser = new JSONParser();
			JSONObject simpleJSONout = (JSONObject) jsonParser.parse(response);
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			JSONOUT = gson.toJson(simpleJSONout);
			return Response.ok(JSONOUT).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.VocResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

}
