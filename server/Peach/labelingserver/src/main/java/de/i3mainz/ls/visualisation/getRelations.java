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
import org.jdom.JDOMException;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;
import org.openrdf.query.BindingSet;

/**
 * SERVLET returns Relations-Graph JSON object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 06.02.2015
 */
public class getRelations extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, JDOMException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String creator = null;
			if (request.getParameter("creator") != null) {
				creator = request.getParameter("creator");
			}
			String vocabulary = null;
			if (request.getParameter("vocabulary") != null) {
				vocabulary = request.getParameter("vocabulary");
			}
			JSONArray jsonarray_relations = new JSONArray(); // {}
			// skos:broader,narrower,related
			String relation_query = null;
			if (vocabulary != null) { // labels of vocabulary identifier
				relation_query = "SELECT * WHERE { "
						+ "?l " + Config.getPrefixItemOfOntology("ls", "belongsTo", true) + " ?v ."
						+ "?v " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " \"" + vocabulary + "\" ."
						+ "?v a " + Config.getPrefixItemOfOntology("ls", "Vocabulary", true) + " ."
						+ "?o " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?po ."
						+ "?o " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang2 ."
						+ "?l ?p ?o ."
						+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
						+ "?l " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
						+ "?l " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "FILTER (?p = " + Config.getPrefixItemOfOntology("skos", "broader", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "narrower", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "related", true) + ")"
						+ "FILTER(LANGMATCHES(LANG(?pl), ?prefLang))"
						+ "FILTER(LANGMATCHES(LANG(?po), ?prefLang2))"
						+ "}";
			} else { // all labels of creator
				relation_query = "SELECT * WHERE { "
						+ "?o " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?po ."
						+ "?o " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang2 ."
						+ "?l ?p ?o ."
						+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
						+ "?l " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
						+ "?l " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "?l " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " \"" + creator + "\"."
						+ "FILTER (?p = " + Config.getPrefixItemOfOntology("skos", "broader", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "narrower", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "related", true) + ")"
						+ "FILTER(LANGMATCHES(LANG(?pl), ?prefLang))"
						+ "FILTER(LANGMATCHES(LANG(?po), ?prefLang2))"
						+ "}";
			}
			List<BindingSet> relation_result = SesameConnect.SPARQLquery("labelingsystem", relation_query);
			List<String> relation_l = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation_result, "l");
			List<String> relation_p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation_result, "p");
			List<String> relation_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation_result, "pl");
			List<String> relation_po = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation_result, "po");
			for (int i = 0; i < relation_l.size(); i++) {
				JSONObject jsonobj_tmp = new JSONObject(); // {}
				jsonobj_tmp.put("source", relation_pl.get(i));
				jsonobj_tmp.put("type", relation_p.get(i).replace("http://www.w3.org/2004/02/skos/core#", ""));
				jsonobj_tmp.put("target", relation_po.get(i));
				jsonarray_relations.add(jsonobj_tmp);
			}
			// skos:broaderMatch,narrowerMatch,relatedMatch,closeMarch,exactMatch,owl:sameAs,rdfs:seeAlso,rdfs:isDefinedBy
			String relation2_query = null;
			if (vocabulary != null) { // labels of vocabulary identifier
				relation2_query = "SELECT * WHERE { "
						+ "?l " + Config.getPrefixItemOfOntology("ls", "belongsTo", true) + " ?v ."
						+ "?v " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " \"" + vocabulary + "\" ."
						+ "?v a " + Config.getPrefixItemOfOntology("ls", "Vocabulary", true) + " ."
						+ "?l ?p ?o ."
						+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
						+ "?l " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
						+ "?l " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "FILTER (?p = " + Config.getPrefixItemOfOntology("skos", "broadMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "narrowMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "relatedMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "closeMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "exactMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("owl", "sameAs", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("rdfs", "seeAlso", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", true) + ")"
						+ "FILTER(LANGMATCHES(LANG(?pl), ?prefLang))"
						+ "}";
			} else { // all labels of creator
				relation2_query = "SELECT * WHERE { "
						+ "?l ?p ?o ."
						+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
						+ "?l " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
						+ "?l " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "?l " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " \"" + creator + "\"."
						+ "FILTER (?p = " + Config.getPrefixItemOfOntology("skos", "broadMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "narrowMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "relatedMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "closeMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("skos", "exactMatch", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("owl", "sameAs", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("rdfs", "seeAlso", true) + " "
						+ "|| ?p = " + Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", true) + ")"
						+ "FILTER(LANGMATCHES(LANG(?pl), ?prefLang))"
						+ "}";
			}
			List<BindingSet> relation2_result = SesameConnect.SPARQLquery("labelingsystem", relation2_query);
			List<String> relation2_l = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation2_result, "l");
			List<String> relation2_p = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation2_result, "p");
			List<String> relation2_o = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation2_result, "o");
			List<String> relation2_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(relation2_result, "pl");
			for (int i = 0; i < relation2_l.size(); i++) {
				JSONObject jsonobj_tmp = new JSONObject(); // {}
				jsonobj_tmp.put("source", relation2_pl.get(i).replace("$literal###", "@"));
				String type = relation2_p.get(i).replace("http://www.w3.org/2004/02/skos/core#", "");
				type = type.replace("http://www.w3.org/2002/07/owl#", "");
				type = type.replace("http://www.w3.org/2000/01/rdf-schema#", "");
				jsonobj_tmp.put("type", type);
				if (relation2_o.get(i).contains("vocab.getty.edu")) {
					String[] split = relation2_o.get(i).split("/");
					jsonobj_tmp.put("target", "getty:" + split[split.length - 2] + "/" + split[split.length - 1]);
				} else if (relation2_o.get(i).contains("labeling.i3mainz.hs-mainz.de")) {
					String[] split = relation2_o.get(i).split("/");
					jsonobj_tmp.put("target", "ls:" + split[split.length - 1]);
				} else if (relation2_o.get(i).contains("data.culture.fr")) {
					String[] split = relation2_o.get(i).split("/");
					jsonobj_tmp.put("target", "dc:" + split[split.length - 2] + "/" + split[split.length - 1]);
				} else if (relation2_o.get(i).contains("vocabulary.wolterskluwer.de")) {
					String[] split = relation2_o.get(i).split("/");
					jsonobj_tmp.put("target", "wkd:" + split[split.length - 2] + "/" + split[split.length - 1]);
				} else if (relation2_o.get(i).contains("dbpedia")) {
					String[] split = relation2_o.get(i).split("/");
					jsonobj_tmp.put("target", "dbpedia:" + split[split.length - 1]);
				} else {
					jsonobj_tmp.put("target", relation2_o.get(i));
				}
				jsonarray_relations.add(jsonobj_tmp);
			}
			// pretty print JSON output (Gson)
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			out.print(gson.toJson(jsonarray_relations));
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
		} catch (JDOMException ex) {
			Logger.getLogger(getRelations.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.ConfigException ex) {
			Logger.getLogger(getRelations.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getRelations.class.getName()).log(Level.SEVERE, null, ex);
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
