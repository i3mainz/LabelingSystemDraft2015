package de.i3mainz.rest;

import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.TransformerException;

/**
 * ReST-Klasse der Ressource "Rest"
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 10.02.2015
 */
@Path("/")
public class RestResource {

	/**
	 * XML list of collections
	 *
	 * @return XML list of collections
	 */
	@GET
	@Produces(MediaType.APPLICATION_XML)
	public Response getCollectionsXML() throws TransformerException, ParserConfigurationException {
		try {
			String xml = "";
			xml += "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
			xml += "<collections>\n";
			xml += "\t<collection id=\"vocabs\" href=\"" + Config.RestVocabularies(false) + "\"/>\n";
			xml += "\t<collection id=\"sparqlendpoints\" href=\"" + Config.RestVocabularies(false).replace("vocabs", "sparqlendpoints") + "\"/>\n";
			xml += "\t<collection id=\"agents\" href=\"" + Config.RestVocabularies(false).replace("vocabs", "agents") + "\"/>\n";
			xml += "\t<collection id=\"guis\" href=\"" + Config.RestVocabularies(false).replace("vocabs", "guis") + "\"/>\n";
			xml += "</collections>";
			return Response.ok(xml).header("Access-Control-Allow-Origin", "*").build();
		} catch (Exception e) {
			return Response.status(Response.Status.BAD_REQUEST).entity(Logging.getMessageXML(e, "de.i3mainz.rest.RestResource")).
					header("Access-Control-Allow-Origin", "*").header("Content-Type", "application/xml;charset=UTF-8").build();
		}
	}

}
