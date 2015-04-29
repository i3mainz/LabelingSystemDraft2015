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
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.List;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;
import org.jdom.JDOMException;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.openrdf.query.BindingSet;

/**
 * REST CLASS of resource sparqlendpoints
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.03.2015
 */
@Path("/sparqlendpoints")
public class SparqlEndpointsResource {

	/**
	 * XML list of sparql endpoints
	 *
	 * @return XML list of sparql endpoints
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getSparqlEndpointsXML() throws IOException, JDOMException, ConfigException, TransformerException, ParserConfigurationException {
		try {
			String sparqlendpointquery = "SELECT ?s ?label WHERE { "
					+ "?s a " + Config.getPrefixItemOfOntology("ls", "SPARQLendpoint", true) + " . "
					+ "?s " + Config.getPrefixItemOfOntology("ls", "sparqlname", true) + " ?label . "
					+ "}";
			List<BindingSet> sparql_result = SesameConnect.SPARQLquery("labelingsystem", sparqlendpointquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(sparql_result, "label");
			List<String> sep = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(sparql_result, "s");
			String xml = "";
			xml += "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>";
			xml += "<collection id=\"sparqlendpoint\" href=\"" + Config.RestVocabularies(false).replace("vocabs", "sparqlendpoints") + "\">" + "\n";
			for (int i = 0; i < sep.size(); i++) {
				xml += "\t" + "<sparql id=\"" + sep.get(i).split("#")[1] + "\" "
						+ "label=\"" + labels.get(i).replaceAll("\"", "") + "\" "
						+ "href=\"" + Config.RestVocabulary(sep.get(i).split("#")[1], false, false).replace("vocabs", "sparqlendpoints") + "\"/>" + "\n";
			}
			xml += "</collection>";
			return Response.ok(xml).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.SparqlEndpointsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of sparql endpoint data
	 *
	 * @param sparqlendpoint
	 * @return RDF/XML representation of sparql endpoint data
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{sparqlendpoint}")
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getSPARQLEndpointXML(@PathParam("sparqlendpoint") String sparqlendpoint) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String sparqlendpointquery = "SELECT * WHERE { "
					+ Config.Instance("sparqlendpoint", sparqlendpoint, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", sparqlendpointquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("sparqlendpoint");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("sparqlendpoint", sparqlendpoint, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.SparqlEndpointsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of sparql endpoint data
	 *
	 * @param sparqlendpoint
	 * @return RDF/XML representation of sparql endpoint data
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{sparqlendpoint}.rdf")
	@Produces("application/rdf+xml;charset=UTF-8")
	public Response getSPARQLEndpointRDF(@PathParam("sparqlendpoint") String sparqlendpoint) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String sparqlendpointquery = "SELECT * WHERE { "
					+ Config.Instance("sparqlendpoint", sparqlendpoint, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", sparqlendpointquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("sparqlendpoint");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("sparqlendpoint", sparqlendpoint, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.SparqlEndpointsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * JSON representation of sparql endpoint data
	 *
	 * @param sparqlendpoint
	 * @return RDF/XML representation of gui data
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 * @throws javax.xml.transform.TransformerException
	 * @throws javax.xml.parsers.ParserConfigurationException
	 */
	@GET
	@Path("/{sparqlendpoint}.json")
	@Produces(MediaType.APPLICATION_JSON + ";charset=UTF-8")
	public Response getSPARQLendpointJSON(@PathParam("sparqlendpoint") String sparqlendpoint) throws IOException, JDOMException, RdfException, TransformerException, ParserConfigurationException {
		try {
			String JSONOUT = null;
			String url = Config.LABELINGSERVER + "getSPARQLendpoint?id=" + sparqlendpoint;
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
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.GuisResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

}
