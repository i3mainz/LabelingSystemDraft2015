package de.i3mainz.ls.instances.json;

import de.i3mainz.ls.instances.java.Agent;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.ConfigException;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import de.i3mainz.ls.rdfutils.exceptions.ResourceNotAvailableException;
import de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.openrdf.query.BindingSet;

/**
 * SERVLET returns an agent object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 11.02.2015
 */
public class getAgent extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String agent = null;
			if (request.getParameter("id") != null) {
				agent = request.getParameter("id");
			}
			// QUERY FOR TRIPLESTORE
			String query = null;
			boolean check_gui_exists = false;
			// START BUILD JSON
			JSONObject jsonobj_query = new JSONObject(); // {}
			// SET QUERY
			query = "SELECT ?agent ?accountName ?title ?firstName ?lastName ?mbox ?status ?topicInterest ?workplaceHomepage ?workInfoHomepage ?homepage ?gui ?guiID WHERE { "
					+ "?agent a " + Config.getPrefixItemOfOntology("foaf", "Person", true) + " . "
					+ "?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?accountName ."
					+ "?agent " + Config.getPrefixItemOfOntology("foaf", "title", true) + " ?title ."
					+ "?agent " + Config.getPrefixItemOfOntology("foaf", "firstName", true) + " ?firstName ."
					+ "?agent " + Config.getPrefixItemOfOntology("foaf", "lastName", true) + " ?lastName ."
					+ "?agent " + Config.getPrefixItemOfOntology("foaf", "mbox", true) + " ?mbox ."
					+ "?agent " + Config.getPrefixItemOfOntology("ls", "hasGUI", true) + " ?gui ."
					+ "?gui " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?guiID ."
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "status", true) + " ?status . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?status . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "topic_interest", true) + " ?topicInterest . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?topicInterest . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "workplaceHomepage", true) + " ?workplaceHomepage . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?workplaceHomepage . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "workInfoHomepage", true) + " ?workInfoHomepage . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?workInfoHomepage . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "homepage", true) + " ?homepage . } "
					+ "OPTIONAL { ?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?homepage . } "
					+ "FILTER(?accountName = \"" + agent + "\" ) "
					+ "} ";
			// EXECUTE QUERY
			List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
			// results
			List<String> query_agent = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "agent");
			List<String> query_accountName = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "accountName");
			List<String> query_title = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "title");
			List<String> query_firstName = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "firstName");
			List<String> query_lastName = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "lastName");
			List<String> query_mbox = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "mbox");
			List<String> query_status = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "status");
			List<String> query_topicInterest = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "topicInterest");
			List<String> query_workplaceHomepage = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "workplaceHomepage");
			List<String> query_workInfoHomepage = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "workInfoHomepage");
			List<String> query_homepage = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "homepage");
			List<String> query_gui = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "gui");
			List<String> query_guiID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "guiID");
			if (!query_accountName.isEmpty() && query_accountName.size() == query_agent.size() && query_accountName.size() == query_firstName.size()
					&& query_accountName.size() == query_lastName.size() && query_accountName.size() == query_mbox.size()
					&& query_accountName.size() == query_status.size() && query_accountName.size() == query_topicInterest.size()
					&& query_accountName.size() == query_workplaceHomepage.size() && query_accountName.size() == query_workInfoHomepage.size()
					&& query_accountName.size() == query_homepage.size() && query_accountName.size() == query_gui.size()
					&& query_accountName.size() == query_title.size() && query_accountName.size() == query_guiID.size()) {
				check_gui_exists = true;
			}
			// if no agentid
			if (query_accountName.isEmpty()) {
				throw new ResourceNotAvailableException("agent");
			} else if (check_gui_exists) {
				// create labeloutput object for agent
				HashMap<String, Agent> agentobject = new HashMap<String, Agent>();
				for (int i = 0; i < query_accountName.size(); i++) {
					if (!agentobject.keySet().contains(query_accountName.get(i))) {
						agentobject.put(query_accountName.get(i), new Agent());
						agentobject.get(query_accountName.get(i)).setAccountName(query_accountName.get(i));
						agentobject.get(query_accountName.get(i)).setTitle(query_title.get(i));
						agentobject.get(query_accountName.get(i)).setFirstName(query_firstName.get(i));
						agentobject.get(query_accountName.get(i)).setLastName(query_lastName.get(i));
						agentobject.get(query_accountName.get(i)).setMbox(query_mbox.get(i).replaceAll("\"", ""));
						agentobject.get(query_accountName.get(i)).setGui(query_guiID.get(i));
						// optional values
						if (!query_status.get(i).contains(query_accountName.get(i))) {
							agentobject.get(query_accountName.get(i)).setStatus(query_status.get(i));
						}
						if (!query_topicInterest.get(i).contains(query_accountName.get(i))) {
							agentobject.get(query_accountName.get(i)).setTopic_interest(query_topicInterest.get(i));
						}
						if (!query_workplaceHomepage.get(i).contains(query_accountName.get(i))) {
							agentobject.get(query_accountName.get(i)).setWorkplaceHomepage(query_workplaceHomepage.get(i));
						}
						if (!query_workInfoHomepage.get(i).contains(query_accountName.get(i))) {
							agentobject.get(query_accountName.get(i)).setWorkInfoHomepage(query_workInfoHomepage.get(i));
						}
						if (!query_homepage.get(i).contains(query_accountName.get(i))) {
							agentobject.get(query_accountName.get(i)).setHomepage(query_homepage.get(i));
						}
					} else {
					}
				}
				// create json output object for label
				JSONArray jsonarray_data = new JSONArray(); // []
				for (String name : agentobject.keySet()) {
					JSONObject jsonobj_suggestion = new JSONObject(); // {}
					// set single values
					jsonobj_suggestion.put("accountName", agentobject.get(name).getAccountName());
					jsonobj_suggestion.put("title", agentobject.get(name).getTitle());
					jsonobj_suggestion.put("firstName", agentobject.get(name).getFirstName());
					jsonobj_suggestion.put("lastName", agentobject.get(name).getLastName());
					jsonobj_suggestion.put("mbox", agentobject.get(name).getMbox());
					jsonobj_suggestion.put("gui", agentobject.get(name).getGui());
					// set optional single values
					jsonobj_suggestion.put("status", agentobject.get(name).getStatus());
					jsonobj_suggestion.put("topic_interest", agentobject.get(name).getTopic_interest());
					jsonobj_suggestion.put("workplaceHomepage", agentobject.get(name).getWorkplaceHomepage());
					jsonobj_suggestion.put("workInfoHomepage", agentobject.get(name).getWorkInfoHomepage());
					jsonobj_suggestion.put("homepage", agentobject.get(name).getHomepage());
					// set data
					jsonarray_data.add(jsonobj_suggestion);
				}
				jsonobj_query.put("agent", agent);
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
		try {
			processRequest(request, response);
		} catch (ConfigException ex) {
			Logger.getLogger(getAgent.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getAgent.class.getName()).log(Level.SEVERE, null, ex);
		}
	}

	/**
	 * Returns a short description of the servlet.
	 *
	 * @return a String containing servlet description
	 */
	@Override
	public String getServletInfo() {
		return "Short description";
	}// </editor-fold>

}
