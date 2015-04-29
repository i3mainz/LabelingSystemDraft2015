package de.i3mainz.ls.instances.json;

import de.i3mainz.ls.instances.java.GUI;
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
import java.util.HashSet;
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
import org.openrdf.query.MalformedQueryException;
import org.openrdf.query.QueryEvaluationException;
import org.openrdf.repository.RepositoryException;

/**
 * SERVLET returns a gui object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.02.2015
 */
public class getGUI extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException, QueryEvaluationException, RepositoryException, MalformedQueryException, ResourceNotAvailableException {
		PrintWriter out = response.getWriter();
		try {
			String gui = null;
			if (request.getParameter("id") != null) {
				gui = request.getParameter("id");
			}
			// QUERY FOR TRIPLESTORE
			String query = null;
			boolean check_agent_exists = false;
			boolean check_agent_notexists = false;
			// START BUILD JSON
			JSONObject jsonobj_query = new JSONObject(); // {}
			// SET QUERY
			query = "SELECT ?gui ?identifier ?label ?comment ?agent ?agentID ?creatorID ?GUIprefLang ?GUImenuLang WHERE { "
					+ "?gui a " + Config.getPrefixItemOfOntology("ls", "GUI", true) + " . "
					+ "?gui " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier ."
					+ "?gui " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label ."
					+ "?gui " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?comment ."
					+ "?gui " + Config.getPrefixItemOfOntology("ls", "GUIcreator", true) + " ?creatorID . "
					+ "?gui " + Config.getPrefixItemOfOntology("ls", "GUIprefLang", true) + " ?GUIprefLang . "
					+ "?gui " + Config.getPrefixItemOfOntology("ls", "GUImenuLang", true) + " ?GUImenuLang . "
					+ "?gui " + Config.getPrefixItemOfOntology("ls", "isGUIof", true) + " ?agent . "
					+ "?agent " + Config.getPrefixItemOfOntology("foaf", "accountName", true) + " ?agentID . "
					+ "FILTER(?identifier = \"" + gui + "\" ) "
					+ "} ";
			// EXECUTE QUERY
			List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
			// results
			List<String> query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "gui");
			List<String> query_identifier = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "identifier");
			List<String> query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
			List<String> query_comment = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "comment");
			List<String> query_agent = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "agent");
			List<String> query_agentID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "agentID");
			List<String> query_creatorID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creatorID");
			List<String> query_preflang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "GUIprefLang");
			List<String> query_menuLang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "GUImenuLang");
			if (!query_id.isEmpty() && query_id.size() == query_identifier.size() && query_id.size() == query_comment.size()
					&& query_id.size() == query_agent.size() && query_id.size() == query_agentID.size()
					&& query_id.size() == query_label.size() && query_id.size() == query_creatorID.size()
					&& query_id.size() == query_preflang.size() && query_id.size() == query_menuLang.size()) {
				check_agent_exists = true;
			}
			if (!check_agent_exists) {
				// if no agent available
				query = "SELECT ?gui ?identifier ?label ?comment ?agent ?creator ?creatorID ?GUIprefLang ?GUImenuLang WHERE { "
						+ "?gui a " + Config.getPrefixItemOfOntology("ls", "GUI", true) + " . "
						+ "?gui " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?identifier ."
						+ "?gui " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label ."
						+ "?gui " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?comment ."
						+ "?gui " + Config.getPrefixItemOfOntology("ls", "GUIprefLang", true) + " ?GUIprefLang . "
						+ "?gui " + Config.getPrefixItemOfOntology("ls", "GUImenuLang", true) + " ?GUImenuLang . "
						+ "?gui " + Config.getPrefixItemOfOntology("ls", "GUIcreator", true) + " ?creatorID . "
						+ "FILTER(?identifier = \"" + gui + "\" ) "
						+ "} ";
				// EXECUTE QUERY
				query_result = SesameConnect.SPARQLquery("labelingsystem", query);
				// results
				query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "gui");
				query_identifier = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "identifier");
				query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
				query_comment = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "comment");
				query_creatorID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creatorID");
				query_preflang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "GUIprefLang");
				query_menuLang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "GUImenuLang");
				if (!query_id.isEmpty() && query_id.size() == query_identifier.size() && query_id.size() == query_comment.size()
						&& query_id.size() == query_label.size() && query_id.size() == query_creatorID.size()
						&& query_id.size() == query_preflang.size() && query_id.size() == query_menuLang.size()) {
					check_agent_notexists = true;
				}
			}
			// if no gui ID
			if (query_id.isEmpty()) {
				throw new ResourceNotAvailableException("gui");
			} else if (check_agent_exists || check_agent_notexists) {
				// create labeloutput object for gui
				HashMap<String, GUI> guiobject = new HashMap<String, GUI>();
				for (int i = 0; i < query_id.size(); i++) {
					if (!guiobject.keySet().contains(query_id.get(i))) {
						guiobject.put(query_id.get(i), new GUI());
						guiobject.get(query_id.get(i)).setId(query_identifier.get(i));
						guiobject.get(query_id.get(i)).setLabel(query_label.get(i));
						guiobject.get(query_id.get(i)).setComment(query_comment.get(i));
						guiobject.get(query_id.get(i)).setCreator(query_creatorID.get(i));
						guiobject.get(query_id.get(i)).setLang_pref(query_preflang.get(i));
						guiobject.get(query_id.get(i)).setLang_menu(query_menuLang.get(i));
						// multiple optional values
						if (check_agent_exists) {
							if (!query_agent.get(i).contains(query_id.get(i))) {
								guiobject.get(query_id.get(i)).getAgents().add(query_agentID.get(i));
							}
						}
					} else {
						// multiple optional values
						if (check_agent_exists) {
							if (!query_agent.get(i).contains(query_id.get(i))) {
								guiobject.get(query_id.get(i)).getAgents().add(query_agentID.get(i));
							}
						}
					}
				}
				// create json output object for label
				JSONArray jsonarray_data = new JSONArray(); // []
				for (String name : guiobject.keySet()) {
					JSONObject jsonobj_data = new JSONObject(); // {}
					// arrays for multiple values
					JSONArray jsonarray_agents = new JSONArray(); // []
					// set agents
					HashSet ags = guiobject.get(name).getAgents();
					for (Object ag : ags) {
						JSONObject jsonobj_agent = new JSONObject(); // {}
						jsonobj_agent.put("agent", ag);
						jsonarray_agents.add(jsonobj_agent);
					}
					// set single values
					jsonobj_data.put("id", guiobject.get(name).getId());
					jsonobj_data.put("label", guiobject.get(name).getLabel());
					jsonobj_data.put("comment", guiobject.get(name).getComment());
					jsonobj_data.put("creator", guiobject.get(name).getCreator());
					jsonobj_data.put("prefLang", guiobject.get(name).getLang_pref());
					jsonobj_data.put("menuLang", guiobject.get(name).getLang_menu());
					// set multiple values
					jsonobj_data.put("agents", jsonarray_agents);
					// set data
					jsonarray_data.add(jsonobj_data);
				}
				jsonobj_query.put("gui", gui);
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
			Logger.getLogger(getGUI.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getGUI.class.getName()).log(Level.SEVERE, null, ex);
		} catch (QueryEvaluationException ex) {
			Logger.getLogger(getGUI.class.getName()).log(Level.SEVERE, null, ex);
		} catch (RepositoryException ex) {
			Logger.getLogger(getGUI.class.getName()).log(Level.SEVERE, null, ex);
		} catch (MalformedQueryException ex) {
			Logger.getLogger(getGUI.class.getName()).log(Level.SEVERE, null, ex);
		} catch (ResourceNotAvailableException ex) {
			Logger.getLogger(getGUI.class.getName()).log(Level.SEVERE, null, ex);
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
