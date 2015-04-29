package de.i3mainz.ls.autocomplete;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.instances.java.Label;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.AutocompleteLengthException;
import de.i3mainz.ls.rdfutils.exceptions.ConfigException;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.URLDecoder;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.jdom.JDOMException;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.openrdf.query.BindingSet;

/**
 * SERVLET autocomplete for labels with connection to a vocabulary
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 04.02.2015
 */
public class autocomplete extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, JDOMException, ConfigException, AutocompleteLengthException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			// QUERY SUBSTRING
			String substing = null;
			if (request.getParameter("query") != null) {
				substing = request.getParameter("query").toLowerCase();
				substing = URLDecoder.decode(substing, "UTF-8");
			}
			// FILTER
			String creator = null;
			if (request.getParameter("creator") != null) {
				creator = request.getParameter("creator");
			}
			String vocabulary = null;
			if (request.getParameter("vocabulary") != null) {
				vocabulary = request.getParameter("vocabulary");
			}
			int suggestions = 20;
			// substring in prefLabels
			if (substing.length() <= 1) {
				throw new AutocompleteLengthException();
			} else {
				// QUERY FOR TRIPLESTORE
				String query = null;
				// START BUILD JSON
				JSONObject jsonobj_query = new JSONObject(); // {}
				if (vocabulary == null && creator == null) { // no filter set
					query = "SELECT ?labelIdentifier ?prefLabels ?altLabels ?concept ?label ?creator ?prefLang WHERE { "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabels ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelIdentifier ."
							+ "?label " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?concept . "
							+ "?label a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabel . "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabel . }"
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabels . } "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?altLabels . } "
							+ "FILTER(regex(?prefLabel, '" + substing + "', 'i') || regex(?altLabel, '" + substing + "', 'i'))"
							+ "} "
							+ "ORDER BY ?labelIdentifier ?prefLabels ?altLabels "
							+ "LIMIT " + suggestions;
				} else if (vocabulary != null && creator != null) { // both filter set --> not possible
					query = "SELECT ?labelIdentifier ?prefLabels ?altLabels ?concept ?label ?creator ?prefLang WHERE { "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabels ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelIdentifier ."
							+ "?label " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?concept . "
							+ "?label a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabel . "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabel . }"
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabels . } "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?altLabels . } "
							+ "FILTER(regex(?prefLabel, '" + substing + "', 'i') || regex(?altLabel, '" + substing + "', 'i'))"
							+ "} "
							+ "ORDER BY ?labelIdentifier ?prefLabels ?altLabels "
							+ "LIMIT " + suggestions;
				} else if (creator != null) { // creator filter
					query = "SELECT ?labelIdentifier ?prefLabels ?altLabels ?concept ?label ?creator ?prefLang WHERE { "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabels ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelIdentifier ."
							+ "?label " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?concept . "
							+ "?label a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabel . "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altlabel", true) + " ?altLabel . }"
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabels . } "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?altLabels . } "
							+ "FILTER(regex(?prefLabel, '" + substing + "', 'i') || regex(?altLabel, '" + substing + "', 'i'))"
							+ "FILTER(regex(?creator, '" + creator + "', 'i')) "
							+ "} "
							+ "ORDER BY ?labelIdentifier ?prefLabels ?altLabels "
							+ "LIMIT " + suggestions;
				} else if (vocabulary != null) { // vocabulary filter
					query = "SELECT ?labelIdentifier ?prefLabels ?altLabels ?concept ?label ?creator ?prefLang WHERE { "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabels ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelIdentifier ."
							+ "?label " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
							+ "?label" + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
							+ "?label " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?concept . "
							+ "?label a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
							+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabel . "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabel . }"
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabels . } "
							+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?altLabels . } "
							+ "FILTER(regex(?prefLabel, '" + substing + "', 'i') || regex(?altLabel, '" + substing + "', 'i'))"
							+ "FILTER(?vocabularyIdentifier = \"" + vocabulary + "\" ) "
							+ "} "
							+ "ORDER BY ?labelIdentifier ?prefLabels ?altLabels "
							+ "LIMIT " + suggestions;
				}
				// EXECUTE QUERY
				List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
				// results
				List<String> query_concept = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "concept");
				List<String> query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
				List<String> query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "labelIdentifier");
				List<String> query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
				List<String> query_prefLang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "prefLang");
				List<String> query_prefLabels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "prefLabels");
				List<String> query_altLabels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "altLabels");
				// create labeloutput object for each unique id
				HashMap<String, Label> hm = new HashMap<String, Label>();
				for (int i = 0; i < query_id.size(); i++) {
					if (!hm.keySet().contains(query_id.get(i))) {
						hm.put(query_id.get(i), new Label());
						hm.get(query_id.get(i)).setId(query_id.get(i));
						hm.get(query_id.get(i)).setCreator(query_creator.get(i));
						hm.get(query_id.get(i)).setPrefLang(query_prefLang.get(i));
						hm.get(query_id.get(i)).setLabel(query_label.get(i));
						hm.get(query_id.get(i)).getConcepts().add(query_concept.get(i));
						hm.get(query_id.get(i)).getPrefLabels().add(query_prefLabels.get(i));
						if (!query_altLabels.get(i).contains(query_id.get(i))) {
							hm.get(query_id.get(i)).getAltLabels().add(query_altLabels.get(i));
						}
					} else {
						hm.get(query_id.get(i)).getConcepts().add(query_concept.get(i));
						hm.get(query_id.get(i)).getPrefLabels().add(query_prefLabels.get(i));
						if (!query_altLabels.get(i).contains(query_id.get(i))) {
							hm.get(query_id.get(i)).getAltLabels().add(query_altLabels.get(i));
						}
					}
				}
				JSONArray jsonarray_suggestions = new JSONArray(); // []
				for (String name : hm.keySet()) {
					String match = "";
					JSONObject jsonobj_suggestion = new JSONObject(); // {}
					JSONArray jsonarray_prefLabels = new JSONArray(); // []
					JSONArray jsonarray_altLabels = new JSONArray(); // []
					JSONArray jsonarray_concepts = new JSONArray(); // []
					// set prefLabels
					HashSet pls = hm.get(name).getPrefLabels();
					for (Object pl : pls) {
						if (pl.toString().toLowerCase().contains(substing)) {
							match = pl.toString(); // query for value
						}
						JSONObject jsonobj_prefLabel = new JSONObject(); // {}
						jsonobj_prefLabel.put("prefLabel", pl);
						jsonarray_prefLabels.add(jsonobj_prefLabel);
					}
					// set altLabels
					HashSet als = hm.get(name).getAltLabels();
					for (Object al : als) {
						if (al.toString().toLowerCase().contains(substing)) {
							match = al.toString(); // query for value
						}
						JSONObject jsonobj_altLabel = new JSONObject(); // {}
						jsonobj_altLabel.put("altLabel", al);
						jsonarray_altLabels.add(jsonobj_altLabel);
					}
					// set concepts
					HashSet cos = hm.get(name).getConcepts();
					for (Object co : cos) {
						JSONObject jsonobj_concept = new JSONObject(); // {}
						jsonobj_concept.put("concept", co);
						jsonarray_concepts.add(jsonobj_concept);
					}
					// autocomplete required
					jsonobj_suggestion.put("value", match + " - " + hm.get(name).getId());
					jsonobj_suggestion.put("data", hm.get(name).getId());
					// autocomplete more information
					jsonobj_suggestion.put("id", hm.get(name).getId());
					jsonobj_suggestion.put("label", hm.get(name).getLabel());
					jsonobj_suggestion.put("creator", hm.get(name).getCreator());
					jsonobj_suggestion.put("prefLang", hm.get(name).getPrefLang());
					jsonobj_suggestion.put("prefLabels", jsonarray_prefLabels);
					jsonobj_suggestion.put("altLabels", jsonarray_altLabels);
					jsonobj_suggestion.put("concepts", jsonarray_concepts);
					jsonarray_suggestions.add(jsonobj_suggestion);
				}
				jsonobj_query.put("suggestions", jsonarray_suggestions);
				jsonobj_query.put("query", substing);
				// pretty print JSON output (Gson)
				Gson gson = new GsonBuilder().setPrettyPrinting().create();
				out.print(gson.toJson(jsonobj_query));
				response.setStatus(200);
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
		} catch (JDOMException ex) {
			Logger.getLogger(autocomplete.class.getName()).log(Level.SEVERE, null, ex);
		} catch (ConfigException ex) {
			Logger.getLogger(autocomplete.class.getName()).log(Level.SEVERE, null, ex);
		} catch (AutocompleteLengthException ex) {
			Logger.getLogger(autocomplete.class.getName()).log(Level.SEVERE, null, ex);
		} catch (SesameSparqlException ex) {
			Logger.getLogger(autocomplete.class.getName()).log(Level.SEVERE, null, ex);
		}
	}

	/**
	 * Returns a short description of the servlet.
	 *
	 * @return a String containing servlet description
	 */
	@Override
	public String getServletInfo() {
		return " Autocomplete for labels with connection to a vocabulary";
	}// </editor-fold>

}
