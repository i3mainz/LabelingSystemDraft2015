package de.i3mainz.ls.visualisation;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.IOException;
import java.io.PrintWriter;
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
 * SERVLET returns Project-Tree JSON object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 06.02.2015
 */
public class getProjectTreeConcepts extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		String name = null;
		if (request.getParameter("name") != null) {
			name = request.getParameter("name");
		}
		// Grundobjekte
		JSONObject json_user = new JSONObject(); // {}
		// Daten zum User
		json_user.put("name", "user[" + name + "]");
		json_user.put("url", "#");
		try {
			// SELECT PROJECTS
			String projectquery = "SELECT ?s ?l ?c WHERE { "
					+ "?l a " + Config.getPrefixItemOfOntology("ls", "Project", true) + " . "
					+ "?l " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?s . "
					+ "?l " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " \"" + name + "\" . "
					+ "OPTIONAL { ?l " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?c. } "
					+ "OPTIONAL { ?l " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?c. } }";
			List<BindingSet> project_result = SesameConnect.SPARQLquery("labelingsystem", projectquery);
			List<String> projects_l = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(project_result, "l");
			List<String> projects2_s = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(project_result, "s");
			List<String> projects3_c = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(project_result, "c");
			// PROJECT DATA
			JSONArray json_projects = new JSONArray(); // []
			for (int i = 0; i < projects_l.size(); i++) {
				JSONObject json_project_tmp = new JSONObject(); // {}
				String[] split_p2 = projects2_s.get(i).split("@");
				json_project_tmp.put("name", split_p2[0]);
				json_project_tmp.put("lang", split_p2[1]);
				String[] split_p3 = projects3_c.get(i).split("@");
				if (split_p3.length == 2) {
					json_project_tmp.put("comment", split_p3[0]);
					json_project_tmp.put("commentlang", split_p3[1]);
				} else {
					json_project_tmp.put("comment", "none");
					json_project_tmp.put("commentlang", "none");
				}
				json_project_tmp.put("url", projects_l.get(i));
				// SELECT VOCABULARIES
				String vocquery = "SELECT * WHERE { "
						+ "?s " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?id ."
						+ "?s " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?cs ."
						+ "?s " + Config.getPrefixItemOfOntology("rdfs", "label", true) + " ?l ."
						+ " <" + projects_l.get(i) + "> " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?s ."
						//+ "?s " + Config.getPrefixItemOfOntology("ls", "state", true) + " ?public. "
						+ " OPTIONAL { ?s " + Config.getPrefixItemOfOntology("ls", "state", true) + " ?public} "
						+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?public} "
						+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("rdfs", "comment", true) + " ?c } "
						+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?c. }  }";
				List<BindingSet> voc_result = SesameConnect.SPARQLquery("labelingsystem", vocquery);
				List<String> vocs_s = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "s");
				List<String> vocs_l = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "l");
				List<String> vocs_c = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "c");
				List<String> vocs_public = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "public");
				List<String> vocs_cs = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "cs");
				List<String> vocs_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(voc_result, "id");
				// VOCABULARY DATA
				JSONArray json_vocabularies = new JSONArray(); // []
				for (int j = 0; j < vocs_s.size(); j++) {
					JSONObject json_vovab_tmp = new JSONObject(); // {}
					String[] split_v2 = vocs_l.get(j).split("@");
					json_vovab_tmp.put("name", split_v2[0]);
					json_vovab_tmp.put("lang", split_v2[1]);
					String[] split_v3 = vocs_c.get(j).split("@");
					if (split_v3.length == 2) {
						json_vovab_tmp.put("comment", split_v3[0]);
						json_vovab_tmp.put("commentlang", split_v3[1]);
					} else {
						json_vovab_tmp.put("comment", "none");
						json_vovab_tmp.put("commentlang", "none");
					}
					String v4_replace = vocs_public.get(j);
					if (v4_replace.equals("public")) {
						json_vovab_tmp.put("public", "public");
					} else {
						json_vovab_tmp.put("public", "hidden");
					}
					json_vovab_tmp.put("url", vocs_s.get(j));
					json_vovab_tmp.put("conceptscheme", vocs_cs.get(j));
					// SELECT LABELS
					String labelquery = "SELECT ?s ?l ?n ?d ?c WHERE { "
							+ "?s " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?c . "
							+ "?s " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?pl . "
							+ "?s " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?l . "
							+ " <" + vocs_s.get(j) + "> " + Config.getPrefixItemOfOntology("ls", "contains", true) + " ?s . "
							+ " <" + vocs_s.get(j) + "> " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?vid . "
							+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("skos", "note", true) + " ?n. FILTER(LANGMATCHES(LANG(?n), ?pl)) } "
							+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?n. } "
							+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("skos", "definition", true) + " ?d. FILTER(LANGMATCHES(LANG(?d), ?pl)) } "
							+ "OPTIONAL { ?s " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?d. } "
							+ "FILTER(LANGMATCHES(LANG(?l), ?pl)) "
							+ "FILTER(REGEX(STR(?c), \"" + vocs_id.get(j) + "\", \"i\")) "
							+ "} "
							+ "ORDER BY ASC(?l)";
					List<BindingSet> label_result = SesameConnect.SPARQLquery("labelingsystem", labelquery);
					List<String> labels_s = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "s");
					List<String> labels_l = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "l");
					List<String> labels_n = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "n");
					List<String> labels_d = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "d");
					List<String> labels_c = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(label_result, "c");
					// LABEL DATA
					JSONArray json_labels = new JSONArray(); // []
					for (int k = 0; k < labels_s.size(); k++) {
						JSONObject json_label_tmp = new JSONObject(); // {}
						String[] split_l = labels_l.get(k).split("@");
						json_label_tmp.put("name", split_l[0]);
						json_label_tmp.put("lang", split_l[1]);
						String[] split_l3 = labels_n.get(k).split("@");
						if (split_l3.length == 2) {
							json_label_tmp.put("note", split_l3[0]);
							json_label_tmp.put("notelang", split_l3[1]);
						} else {
							json_label_tmp.put("note", "none");
							json_label_tmp.put("notelang", "none");
						}
						String[] split_l4 = labels_d.get(k).split("@");
						if (split_l4.length == 2) {
							json_label_tmp.put("definition", split_l4[0]);
							json_label_tmp.put("definitionlang", split_l4[1]);
						} else {
							json_label_tmp.put("definition", "none");
							json_label_tmp.put("definitionlang", "none");
						}
						json_label_tmp.put("url", labels_s.get(k));
						json_label_tmp.put("concept", labels_c.get(k));
						// SELECT LINKS (EXTERN)
						String conceptquery = "SELECT ?p ?s WHERE { "
								+ "<" + labels_s.get(k) + "> ?p ?s . "
								+ "FILTER (?p = " + Config.getPrefixItemOfOntology("rdfs", "seeAlso", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("owl", "sameAs", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "closeMatch", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "exactMatch", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "narrowMatch", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "relatedMatch", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "broadMatch", true) + ") . }";
						List<BindingSet> concept_result = SesameConnect.SPARQLquery("labelingsystem", conceptquery);
						List<String> concepts_s = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(concept_result, "s");
						List<String> concepts_p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(concept_result, "p");
						// SELECT LINKS (INTERN)
						String hierarchyquery = "SELECT * WHERE { "
								+ "?s " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?c . "
								+ "?s " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabel . "
								+ "?s " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang . "
								+ "<" + labels_s.get(k) + "> ?p ?s . "
								+ "FILTER (?p = " + Config.getPrefixItemOfOntology("skos", "related", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "broader", true) + " || "
								+ "?p = " + Config.getPrefixItemOfOntology("skos", "narrower", true) + ") "
								+ "FILTER(LANGMATCHES(LANG(?prefLabel), ?prefLang)) "
								+ "FILTER(REGEX(STR(?c), \"" + vocs_id.get(j) + "\", \"i\")) "
								+ "}";
						List<BindingSet> hierarchy_result = SesameConnect.SPARQLquery("labelingsystem", hierarchyquery);
						List<String> hierarchy_s = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(hierarchy_result, "s");
						List<String> hierarchy_p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(hierarchy_result, "p");
						List<String> hierarchy_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(hierarchy_result, "prefLabel");
						List<String> hierarchy_c = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(hierarchy_result, "c");
						// LINK DATA
						JSONArray json_concepts = new JSONArray(); // []
						for (int l = 0; l < concepts_s.size(); l++) {
							String[] split_rel = concepts_p.get(l).split("#");
							JSONObject json_concept_tmp = new JSONObject(); // {}
							json_concept_tmp.put("name", concepts_s.get(l));
							json_concept_tmp.put("url", concepts_s.get(l));
							json_concept_tmp.put("relation", split_rel[1]);
							json_concepts.add(json_concept_tmp);
						} // end for concepts_s
						for (int l = 0; l < hierarchy_s.size(); l++) {
							String[] split_rel = hierarchy_p.get(l).split("#");
							JSONObject json_concept_tmp = new JSONObject(); // {}
							json_concept_tmp.put("name", hierarchy_label.get(l));
							json_concept_tmp.put("url", hierarchy_c.get(l));
							json_concept_tmp.put("relation", split_rel[1]);
							json_concepts.add(json_concept_tmp);
						} // end for concepts_s
						if (hierarchy_s.size() > 0 || concepts_s.size() > 0) {
							json_label_tmp.put("children", json_concepts);
						}
						json_labels.add(json_label_tmp);
					}
					if (labels_s.size() > 0) {
						json_vovab_tmp.put("children", json_labels);
					}
					json_vocabularies.add(json_vovab_tmp);
				}
				if (vocs_s.size() > 0) {
					json_project_tmp.put("children", json_vocabularies);
				}
				json_projects.add(json_project_tmp);
			}
			if (projects_l.size() > 0) {
				json_user.put("children", json_projects);
			}
			// pretty print JSON output (Gson)
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			out.print(gson.toJson(json_user));
			response.setStatus(200);
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
		} catch (de.i3mainz.ls.rdfutils.exceptions.ConfigException ex) {
			Logger.getLogger(getProjectTreeConcepts.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getProjectTreeConcepts.class.getName()).log(Level.SEVERE, null, ex);
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
