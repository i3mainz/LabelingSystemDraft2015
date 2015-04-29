package de.i3mainz.ls.instances.json;

import de.i3mainz.ls.instances.java.Project;
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

/**
 * SERVLET returns a project object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 05.02.2015
 */
public class getProject extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, ResourceNotAvailableException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String project = null;
			if (request.getParameter("id") != null) {
				project = request.getParameter("id");
			}
			// QUERY FOR TRIPLESTORE
			String query = null;
			boolean check_voc_exists = false;
			boolean check_voc_notexists = false;
			// START BUILD JSON
			JSONObject jsonobj_query = new JSONObject(); // {}
			// SET QUERY
			query = "SELECT ?projectIdentifier ?project ?creator ?date ?label ?comment ?vocabularyID WHERE { "
					+ "?project a " + Config.getPrefixItemOfOntology("ls", "Project", true) + " . "
					+ "?project " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?projectIdentifier ."
					+ "?project " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
					+ "?project " + Config.getPrefixItemOfOntology("dcterms", "date", true) + " ?date ."
					+ "?project " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label ."
					+ "?project " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?vocabulary . "
					+ "?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?vocabularyID . "
					+ "OPTIONAL { ?project " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?comment . } "
					+ "OPTIONAL { ?project " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?comment . } "
					+ "FILTER(?projectIdentifier = \"" + project + "\" ) "
					+ "} "
					+ "ORDER BY ?projectIdentifier ?project ?creator ?date ?label ?comment ?vocabularyID";
			// EXECUTE QUERY
			List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
			// results
			List<String> query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "projectIdentifier");
			List<String> query_project = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "project");
			List<String> query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
			List<String> query_date = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "date");
			List<String> query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
			List<String> query_vocabularyID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "vocabularyID");
			List<String> query_comment = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "comment");
			if (!query_id.isEmpty() && query_id.size() == query_project.size() && query_id.size() == query_creator.size()
					&& query_id.size() == query_date.size() && query_id.size() == query_label.size()
					&& query_id.size() == query_vocabularyID.size()) {
				check_voc_exists = true;
			}
			// if no vocabulary available
			if (query_id.isEmpty()) {
				// SET QUERY
				query = "SELECT ?projectIdentifier ?project ?creator ?date ?label ?comment WHERE { "
						+ "?project a " + Config.getPrefixItemOfOntology("ls", "Project", true) + " . "
						+ "?project " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?projectIdentifier ."
						+ "?project " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
						+ "?project " + Config.getPrefixItemOfOntology("dcterms", "date", true) + " ?date ."
						+ "?project " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?label ."
						+ "OPTIONAL { ?project " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?comment . } "
						+ "OPTIONAL { ?project " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?comment . } "
						+ "FILTER(?projectIdentifier = \"" + project + "\" ) "
						+ "} "
						+ "ORDER BY ?projectIdentifier ?project ?creator ?date ?label ?comment";
				// EXECUTE QUERY
				query_result = SesameConnect.SPARQLquery("labelingsystem", query);
				// results
				query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "projectIdentifier");
				query_project = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "project");
				query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
				query_date = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "date");
				query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
				query_comment = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "comment");
				if (!query_id.isEmpty() && query_id.size() == query_project.size() && query_id.size() == query_creator.size()
						&& query_id.size() == query_date.size() && query_id.size() == query_label.size()) {
					check_voc_notexists = true;
				}
			}
			// if no projectid
			if (query_id.isEmpty()) {
				throw new ResourceNotAvailableException("project");
			} else if (check_voc_exists || check_voc_notexists) {
				// create labeloutput object for project
				HashMap<String, Project> projectobject = new HashMap<String, Project>();
				for (int i = 0; i < query_id.size(); i++) {
					if (!projectobject.keySet().contains(query_id.get(i))) {
						projectobject.put(query_id.get(i), new Project());
						projectobject.get(query_id.get(i)).setId(query_id.get(i));
						projectobject.get(query_id.get(i)).setProject(query_project.get(i));
						projectobject.get(query_id.get(i)).setCreator(query_creator.get(i));
						projectobject.get(query_id.get(i)).setDate(query_date.get(i));
						projectobject.get(query_id.get(i)).setLabel(query_label.get(i));
						// optional values
						if (!query_comment.get(i).contains(query_id.get(i))) {
							projectobject.get(query_id.get(i)).setComment(query_comment.get(i));
						}
						// multiple optional values
						if (check_voc_exists) {
							if (!query_vocabularyID.get(i).contains(query_id.get(i))) {
								projectobject.get(query_id.get(i)).getVocabularies().add(query_vocabularyID.get(i));
							}
						}
					} else {
						// multiple optional values
						if (check_voc_exists) {
							if (!query_vocabularyID.get(i).contains(query_id.get(i))) {
								projectobject.get(query_id.get(i)).getVocabularies().add(query_vocabularyID.get(i));
							}
						}
					}
				}
				// create json output object for label
				JSONArray jsonarray_data = new JSONArray(); // []
				for (String name : projectobject.keySet()) {
					JSONObject jsonobj_suggestion = new JSONObject(); // {}
					// arrays for multiple values
					JSONArray jsonarray_vocabularies = new JSONArray(); // []
					// set vocabularies
					HashSet vos = projectobject.get(name).getVocabularies();
					for (Object vo : vos) {
						JSONObject jsonobj_vocabulary = new JSONObject(); // {}
						jsonobj_vocabulary.put("vocabularyID", vo);
						jsonarray_vocabularies.add(jsonobj_vocabulary);
					}
					// set single values
					jsonobj_suggestion.put("id", projectobject.get(name).getId());
					jsonobj_suggestion.put("project", projectobject.get(name).getProject());
					jsonobj_suggestion.put("creator", projectobject.get(name).getCreator());
					jsonobj_suggestion.put("date", projectobject.get(name).getDate());
					jsonobj_suggestion.put("label", projectobject.get(name).getLabel());
					// set optional single values
					jsonobj_suggestion.put("comment", projectobject.get(name).getComment());
					// set multiple values
					jsonobj_suggestion.put("vocabularyIDs", jsonarray_vocabularies);
					// set data
					jsonarray_data.add(jsonobj_suggestion);
				}
				jsonobj_query.put("project", project);
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
		} catch (ResourceNotAvailableException ex) {
			Logger.getLogger(getProject.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.ConfigException ex) {
			Logger.getLogger(getProject.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getProject.class.getName()).log(Level.SEVERE, null, ex);
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
