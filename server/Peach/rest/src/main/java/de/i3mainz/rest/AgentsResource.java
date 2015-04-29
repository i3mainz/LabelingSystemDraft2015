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
 * REST CLASS of resource agents
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.03.2015
 */
@Path("/agents")
public class AgentsResource {

	/**
	 * XML list of agents
	 *
	 * @return XML list of agents
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.ConfigException
	 */
	@GET
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getAgentsXML() throws IOException, JDOMException, ConfigException, ParserConfigurationException, TransformerException {
		try {
			String agentquery = "SELECT ?s ?label WHERE { "
					+ "?s a " + Config.getPrefixItemOfOntology("foaf", "Person", true) + " . "
					+ "?s " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?label . "
					+ "}";
			List<BindingSet> sparql_result = SesameConnect.SPARQLquery("labelingsystem", agentquery);
			List<String> labels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(sparql_result, "label");
			List<String> agents = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(sparql_result, "s");
			String xml = "";
			xml += "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>";
			xml += "<collection id=\"agents\" href=\"" + Config.RestVocabularies(false).replace("vocabs", "agents") + "\">" + "\n";
			for (int i = 0; i < agents.size(); i++) {
				xml += "\t" + "<agent id=\"" + agents.get(i).split("#")[1] + "\" "
						+ "label=\"" + labels.get(i).replaceAll("\"", "") + "\" "
						+ "href=\"" + Config.RestVocabulary(agents.get(i).split("#")[1], false, false).replace("vocabs", "agents") + "\"/>" + "\n";
			}
			xml += "</collection>";
			return Response.ok(xml).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.AgentsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

	/**
	 * RDF/XML representation of agent data
	 *
	 * @param agent
	 * @return RDF/XML representation of agent data
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	@GET
	@Path("/{agent}")
	@Produces(MediaType.APPLICATION_XML + ";charset=UTF-8")
	public Response getAgentXML(@PathParam("agent") String agent) throws IOException, JDOMException, RdfException, ParserConfigurationException, TransformerException {
		try {
			String agentquery = "SELECT * WHERE { "
					+ Config.Instance("agent", agent, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", agentquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("agent");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("agent", agent, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.AgentsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}
	
	/**
	 * RDF/XML representation of agent data
	 *
	 * @param agent
	 * @return RDF/XML representation of agent data
	 * @throws java.io.IOException
	 * @throws org.jdom.JDOMException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	@GET
	@Path("/{agent}.rdf")
	@Produces("application/rdf+xml;charset=UTF-8")
	public Response getAgentRDF(@PathParam("agent") String agent) throws IOException, JDOMException, RdfException, ParserConfigurationException, TransformerException {
		try {
			String agentquery = "SELECT * WHERE { "
					+ Config.Instance("agent", agent, true) + " ?p ?o. } "
					+ "ORDER BY ASC(?p)";
			List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", agentquery);
			List<String> predicates = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "p");
			List<String> objects = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "o");
			if (predicates.size() < 1) {
				throw new ResourceNotAvailableException("agent");
			}
			RDF rdf = new RDF();
			for (int i = 0; i < predicates.size(); i++) {
				rdf.setModelTriple(Config.Instance("agent", agent, false), predicates.get(i), objects.get(i));
			}
			String RDFoutput = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n" + rdf.getModel("RDF/XML");
			return Response.ok(RDFoutput).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.AgentsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}
	
	/**
	 * JSON representation of agent data
	 * @param agent
	 * @return
	 * @throws IOException
	 * @throws TransformerException
	 * @throws ParserConfigurationException 
	 */
	@GET
	@Path("/{agent}.json")
	@Produces(MediaType.APPLICATION_JSON + ";charset=UTF-8")
	public Response getAgentJSON(@PathParam("agent") String agent) throws IOException, ParserConfigurationException, TransformerException {
		try {
			String JSONOUT = null;
			String url = Config.LABELINGSERVER + "getAgent?id=" + agent;
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
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.AgentsResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

}
