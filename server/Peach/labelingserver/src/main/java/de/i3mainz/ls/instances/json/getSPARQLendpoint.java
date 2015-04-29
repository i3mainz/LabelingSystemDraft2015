package de.i3mainz.ls.instances.json;

import de.i3mainz.ls.instances.java.SPARQLendpoint;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import de.i3mainz.ls.rdfutils.exceptions.ResourceNotAvailableException;
import de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.openrdf.query.BindingSet;

/**
 * SERVLET returns a sparql endpoint object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.03.2015
 */
public class getSPARQLendpoint extends HttpServlet {

	/**
	 * Processes requests for both HTTP <code>GET</code> and <code>POST</code>
	 * methods.
	 *
	 * @param request servlet request
	 * @param response servlet response
	 * @throws ServletException if a servlet-specific error occurs
	 * @throws IOException if an I/O error occurs
	 */
	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		PrintWriter out = response.getWriter();
		try {
			String se = null;
			if (request.getParameter("id") != null) {
				se = request.getParameter("id");
			}
			// QUERY FOR TRIPLESTORE
			String query = null;
			boolean check_se_exists = false;
			// START BUILD JSON
			JSONObject jsonobj_query = new JSONObject(); // {}
			// SET QUERY
			query = "SELECT ?se ?sparqlname ?sparqlxmluri ?sparqlquery WHERE { "
					+ "?se a " + Config.getPrefixItemOfOntology("ls", "SPARQLendpoint", true) + " . "
					+ "?se " + Config.getPrefixItemOfOntology("ls", "sparqlname", true) + " ?sparqlname . "
					+ "?se " + Config.getPrefixItemOfOntology("ls", "sparqlxmluri", true) + " ?sparqlxmluri . "
					+ "?se " + Config.getPrefixItemOfOntology("ls", "sparqlquery", true) + " ?sparqlquery . "
					+ "FILTER(?se = <" + Config.Instance_SPARQLENDPOINT + se + "> ) "
					+ "} ";
			// EXECUTE QUERY
			List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
			// results
			List<String> query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "se");
			List<String> query_name = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "sparqlname");
			List<String> query_uri = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "sparqlxmluri");
			List<String> query_query = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "sparqlquery");
			if (!query_id.isEmpty() && query_id.size() == query_uri.size() && query_id.size() == query_name.size()
					&& query_id.size() == query_query.size()) {
				check_se_exists = true;
			}
			// if no se ID
			if (query_id.isEmpty()) {
				throw new ResourceNotAvailableException("sparqlendpoint");
			} else if (check_se_exists) {
				// create output object for se
				HashMap<String, SPARQLendpoint> seobject = new HashMap<String, SPARQLendpoint>();
				for (int i = 0; i < query_id.size(); i++) {
					if (!seobject.keySet().contains(query_id.get(i))) {
						seobject.put(query_id.get(i), new SPARQLendpoint());
						seobject.get(query_id.get(i)).setId(se);
						seobject.get(query_id.get(i)).setSparqlname(query_name.get(i));
						seobject.get(query_id.get(i)).setSparqlxmluri(query_uri.get(i));
						seobject.get(query_id.get(i)).setSparqlquery(query_query.get(i));
					}
				}
				// create json output object for sparqlendpoint
				JSONArray jsonarray_data = new JSONArray(); // []
				for (String name : seobject.keySet()) {
					JSONObject jsonobj_data = new JSONObject(); // {}
					// set single values
					jsonobj_data.put("id", seobject.get(name).getId());
					jsonobj_data.put("sparqlname", seobject.get(name).getSparqlname());
					jsonobj_data.put("sparqlxmluri", seobject.get(name).getSparqlxmluri());
					jsonobj_data.put("sparqlquery", seobject.get(name).getSparqlquery());
					// set data
					jsonarray_data.add(jsonobj_data);
				}
				jsonobj_query.put("sparqlendpoint", se);
				jsonobj_query.put("data", jsonarray_data);
				// pretty print JSON output (Gson)
				Gson gson = new GsonBuilder().setPrettyPrinting().create();
				out.print(gson.toJson(jsonobj_query));
				response.setStatus(200);
			} else {
				throw new SesameSparqlException();
			}
		} catch (Exception e) {
			response.setStatus(500);
			out.print(Logging.getMessageJSON(e, getClass().getName()));
		} finally {
			response.setContentType("application/json;charset=UTF-8");
			response.setHeader("Access-Control-Allow-Origin", "*");
			response.setCharacterEncoding("UTF-8");
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
		processRequest(request, response);
	}// </editor-fold>

}
