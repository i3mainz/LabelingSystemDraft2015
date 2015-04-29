package de.i3mainz.ls.sparql;

import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.ConfigException;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.IOException;
import java.net.URLDecoder;
import java.nio.charset.Charset;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.servlet.ServletOutputStream;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/**
 * SERVLET returns SPARQL XML/JSON/CSV/TSV from triplestore (repository
 * labelingsystem)
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 03.02.2015
 */
public class SPARQL extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, ConfigException, de.i3mainz.ls.rdfutils.exceptions.ConfigException {
		String format = "json"; // default: format json
		String file = "false"; // default: false
		String result = "";
		String query = "SELECT * WHERE { " // default: query vocabs
				+ "?conceptscheme a " + Config.getPrefixItemOfOntology("skos", "ConceptScheme", true) + " }";
		// PARSE PARAMETER
		if (request.getParameter("query") == null) {
			format = "html";
			result = "<html>"
					+ "<h1>Labeling System SPARQL endpoint</h1>"
					+ "<h2>HTTP-GET</h2>"
					+ "<h3>param: query {encoded SPARQL QUERY} <a href='http://www.w3schools.com/jsref/jsref_encodeuricomponent.asp'>JavaScript-example</a></h3>"
					+ "<h3>param: format {xml;json;csv;tsv}</h3>"
					+ "<h4>example: <a href='" + Config.SERVER + "labelingserver/SPARQL?query=SELECT%20*%20WHERE%20%7B%20%3Fconceptscheme%20a%20%3Chttp%3A%2F%2Fwww.w3.org%2F2004%2F02%2Fskos%2Fcore%23ConceptScheme%3E%20%7D&format=xml'>query all concept schemes as SPARQL XML</a></h4>"
					+ "</html>";
		} else {
			query = request.getParameter("query");
			// http://www.w3schools.com/jsref/jsref_encodeuricomponent.asp JavaScript encodeURIComponent() Function
			query = URLDecoder.decode(query, "UTF-8");
		}
		if (request.getParameter("format") != null) {
			format = request.getParameter("format");
		}
		if (request.getParameter("file") != null) {
			file = request.getParameter("file");
		}
		if (format.equals("xml")) {
			response.setContentType("application/xml;charset=UTF-8");
		} else if (format.equals("json")) {
			response.setContentType("application/json;charset=UTF-8");
		} else if (format.equals("csv")) {
			response.setContentType("text/plain;charset=UTF-8");
			if (file.equals("true")) {
				response.setHeader("Content-disposition", "attachment;filename=sparql_result.csv");
			}
		} else if (format.equals("html")) {
			response.setContentType("text/html;charset=UTF-8");
		} else {
			response.setContentType("application/json;charset=UTF-8");
		}
		response.setHeader("Access-Control-Allow-Origin", "*");
		response.setCharacterEncoding("UTF-8");
		ServletOutputStream out = response.getOutputStream();
		try {
			if (format.equals("html")) {
				out.write(result.getBytes(Charset.forName("UTF-8")));
			} else {
				SesameConnect.SPARQLqueryOutputFile("labelingsystem", query, format, out);
			}
			response.setStatus(200);
		} catch (Exception e) {
			out.write(Logging.getMessageJSON(e, getClass().getName()).getBytes(Charset.forName("UTF-8")));
			response.setContentType("application/json;charset=UTF-8");
			response.setStatus(500);
		} finally {
			out.flush();
			out.close();
		}
	}

	// <editor-fold defaultstate="collapsed" desc="HttpServlet methods. Click on the + sign on the left to edit the code.">
	/**
	 * Handles the HTTP <code>GET</code> method.
	 *
	 * @param request servlet request
	 * @param response servlet response
	 * @throws ServletException if a servlet-specific error occurs
	 * @throws IOException if an I/O error occurs
	 */
	@Override
	protected void doGet(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		try {
			processRequest(request, response);
		} catch (ConfigException ex) {
			Logger.getLogger(SPARQL.class.getName()).log(Level.SEVERE, null, ex);
		}
	}

	/**
	 * Returns a short description of the servlet.
	 *
	 * @return a String containing servlet description
	 */
	@Override
	public String getServletInfo() {
		return "Servlet returns SPARQL XML/JSON/CSV/TSV from triplestore (repository labelingsystem";
	}// </editor-fold>

}
