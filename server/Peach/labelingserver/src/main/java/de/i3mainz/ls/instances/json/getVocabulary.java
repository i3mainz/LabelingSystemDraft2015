package de.i3mainz.ls.instances.json;

import de.i3mainz.ls.instances.java.Vocabulary;
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
 * SERVLET returns a vocabulary object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 05.02.2015
 */
public class getVocabulary extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, ResourceNotAvailableException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String vocabulary = null;
			if (request.getParameter("id") != null) {
				vocabulary = request.getParameter("id");
			}
			// QUERY FOR TRIPLESTORE
			String query = null;
			boolean check_lab_exists = false;
			boolean check_lab_notexists = false;
			boolean check_pro_exists = false;
			boolean check_pro_notexists = false;
			// START BUILD JSON
			JSONObject jsonobj_query = new JSONObject(); // {}
			// SET QUERY
			query = "SELECT ?vocabularyIdentifier ?vocabulary ?creator ?date ?title ?language ?comment ?state ?labelID ?projectID WHERE { "
					+ "?vocabulary a " + Config.getPrefixItemOfOntology("ls", "Vocabulary", true) + " . "
					+ "?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?vocabularyIdentifier ."
					+ "?vocabulary " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
					+ "?vocabulary " + Config.getPrefixItemOfOntology("dcterms", "date", true) + " ?date ."
					+ "?vocabulary " + Config.getPrefixItemOfOntology("dcterms", "title", true) + " ?title ."
					+ "?vocabulary " + Config.getPrefixItemOfOntology("dcelements", "language", true) + " ?language ."
					+ "?vocabulary " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?label . "
					+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelID . "
					+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "belongsTo", true) + " ?project . "
					+ "?project " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?projectID . }"
					+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?comment . } "
					+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?comment . } "
					+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "state", true) + " ?state . } "
					+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?state . } "
					+ "FILTER(?vocabularyIdentifier = \"" + vocabulary + "\" ) "
					+ "} "
					+ "ORDER BY ?vocabularyIdentifier ?vocabulary ?creator ?date ?title ?language ?comment ?state ?labelID ?projectID";
			// EXECUTE QUERY
			List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
			// results
			List<String> query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "vocabularyIdentifier");
			List<String> query_vocabulary = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "vocabulary");
			List<String> query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
			List<String> query_date = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "date");
			List<String> query_title = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "title");
			List<String> query_language = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "language");
			List<String> query_labelID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "labelID");
			List<String> query_projectID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "projectID");
			List<String> query_comment = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "comment");
			List<String> query_state = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "state");
			if (!query_id.isEmpty() && query_id.size() == query_vocabulary.size() && query_id.size() == query_creator.size()
					&& query_id.size() == query_date.size() && query_id.size() == query_title.size()
					&& query_id.size() == query_language.size() && query_id.size() == query_labelID.size()
					&& query_id.size() == query_comment.size() && query_id.size() == query_state.size()) {
				check_lab_exists = true;
			}
			// if no label available
			if (query_id.isEmpty()) {
				// SET QUERY
				query = "SELECT ?vocabularyIdentifier ?vocabulary ?creator ?date ?title ?language ?comment ?state WHERE { "
						+ "?vocabulary a " + Config.getPrefixItemOfOntology("ls", "Vocabulary", true) + " . "
						+ "?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?vocabularyIdentifier ."
						+ "?vocabulary " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
						+ "?vocabulary " + Config.getPrefixItemOfOntology("dcterms", "date", true) + " ?date ."
						+ "?vocabulary " + Config.getPrefixItemOfOntology("dcterms", "title", true) + " ?title ."
						+ "?vocabulary " + Config.getPrefixItemOfOntology("dcelements", "language", true) + " ?language ."
						+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?comment . } "
						+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?comment . } "
						+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "state", true) + " ?state . } "
						+ "OPTIONAL { ?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?state . } "
						+ "FILTER(?vocabularyIdentifier = \"" + vocabulary + "\" ) "
						+ "} "
						+ "ORDER BY ?vocabularyIdentifier ?vocabulary ?creator ?date ?title ?language ?comment ?state";
				// EXECUTE QUERY
				query_result = SesameConnect.SPARQLquery("labelingsystem", query);
				// results
				query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "vocabularyIdentifier");
				query_vocabulary = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "vocabulary");
				query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
				query_date = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "date");
				query_title = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "title");
				query_language = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "language");
				// optional
				query_comment = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "comment");
				query_state = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "state");
				if (!query_id.isEmpty() && query_id.size() == query_vocabulary.size() && query_id.size() == query_creator.size()
						&& query_id.size() == query_date.size() && query_id.size() == query_title.size()
						&& query_id.size() == query_language.size()
						&& query_id.size() == query_comment.size() && query_id.size() == query_state.size()) {
					check_lab_notexists = true;
				}
			}
			// if no vocabularyid
			if (query_id.isEmpty()) {
				throw new ResourceNotAvailableException("vocabulary");
			} else if (check_lab_exists || check_lab_notexists) {
				// create labeloutput object for label
				HashMap<String, Vocabulary> vocabularyobject = new HashMap<String, Vocabulary>();
				for (int i = 0; i < query_id.size(); i++) {
					if (!vocabularyobject.keySet().contains(query_id.get(i))) {
						vocabularyobject.put(query_id.get(i), new Vocabulary());
						vocabularyobject.get(query_id.get(i)).setId(query_id.get(i));
						vocabularyobject.get(query_id.get(i)).setVocabulary(query_vocabulary.get(i));
						vocabularyobject.get(query_id.get(i)).setCreator(query_creator.get(i));
						vocabularyobject.get(query_id.get(i)).setDate(query_date.get(i));
						vocabularyobject.get(query_id.get(i)).setTitle(query_title.get(i));
						vocabularyobject.get(query_id.get(i)).setLanguage(query_language.get(i));
						// optional values
						if (!query_comment.get(i).contains(query_id.get(i))) {
							vocabularyobject.get(query_id.get(i)).setComment(query_comment.get(i));
						}
						if (!query_state.get(i).contains(query_id.get(i))) {
							vocabularyobject.get(query_id.get(i)).setState(query_state.get(i));
						}
						// multiple optional values
						if (check_lab_exists) {
							if (!query_labelID.get(i).contains(query_id.get(i))) {
								vocabularyobject.get(query_id.get(i)).getLabels().add(query_labelID.get(i));
							}
						}
						if (!query_projectID.isEmpty()) {
							if (!query_labelID.get(i).contains(query_id.get(i))) {
								vocabularyobject.get(query_id.get(i)).getProjects().add(query_projectID.get(i));
							}
						}
					} else {
						// multiple optional values
						if (check_lab_exists) {
							if (!query_labelID.get(i).contains(query_id.get(i))) {
								vocabularyobject.get(query_id.get(i)).getLabels().add(query_labelID.get(i));
							}
						}
						if (!query_projectID.isEmpty()) {
							if (!query_labelID.get(i).contains(query_id.get(i))) {
								vocabularyobject.get(query_id.get(i)).getProjects().add(query_projectID.get(i));
							}
						}
					}
				}
				// create json output object for label
				JSONArray jsonarray_data = new JSONArray(); // []
				for (String name : vocabularyobject.keySet()) {
					JSONObject jsonobj_data = new JSONObject(); // {}
					// arrays for multiple values
					JSONArray jsonarray_labels = new JSONArray(); // []
					JSONArray jsonarray_projects = new JSONArray(); // []
					// set labels
					HashSet las = vocabularyobject.get(name).getLabels();
					for (Object la : las) {
						JSONObject jsonobj_label = new JSONObject(); // {}
						jsonobj_label.put("labelID", la);
						jsonarray_labels.add(jsonobj_label);
					}
					// set projects
					HashSet prs = vocabularyobject.get(name).getProjects();
					for (Object pr : prs) {
						JSONObject jsonobj_project = new JSONObject(); // {}
						jsonobj_project.put("projectID", pr);
						jsonarray_projects.add(jsonobj_project);
					}
					// set single values
					jsonobj_data.put("id", vocabularyobject.get(name).getId());
					jsonobj_data.put("vocabulary", vocabularyobject.get(name).getVocabulary());
					jsonobj_data.put("creator", vocabularyobject.get(name).getCreator());
					jsonobj_data.put("date", vocabularyobject.get(name).getDate());
					jsonobj_data.put("title", vocabularyobject.get(name).getTitle());
					jsonobj_data.put("language", vocabularyobject.get(name).getTitle());
					jsonobj_data.put("state", vocabularyobject.get(name).getState());
					// set optional single values
					jsonobj_data.put("comment", vocabularyobject.get(name).getComment());
					// set multiple values
					jsonobj_data.put("labelIDs", jsonarray_labels);
					jsonobj_data.put("projectIDs", jsonarray_projects);
					// set data
					jsonarray_data.add(jsonobj_data);
				}
				jsonobj_query.put("vocabulary", vocabulary);
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
			Logger.getLogger(getVocabulary.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.ConfigException ex) {
			Logger.getLogger(getVocabulary.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getVocabulary.class.getName()).log(Level.SEVERE, null, ex);
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
