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
 * SERVLET returns Broader-Narrower-Tree JSON object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 06.02.2015
 */
public class getBroaderNarrowerTree extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String label = null;
			if (request.getParameter("label") != null) {
				label = request.getParameter("label");
			}
			boolean all = false;
			if (request.getParameter("all") != null) {
				all = true;
			}
			// Grundobjekte
			JSONObject jsonobj_label = new JSONObject(); // {}
			JSONArray jsonarray_broader = new JSONArray(); // {}
			JSONArray jsonarray_narrower = new JSONArray(); // {}			
			//center prefLabel
			String center_query = "SELECT * WHERE { "
					+ "?l " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
					+ "?l " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
					+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " \"" + label + "\"."
					+ "FILTER(LANGMATCHES(LANG(?pl), ?prefLang))"
					+ "}";
			List<BindingSet> center_result = SesameConnect.SPARQLquery("labelingsystem", center_query);
			List<String> center_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(center_result, "pl");
			String centerPrefLabel = center_pl.get(0);
			//broader 1
			String broader1_query = "SELECT * WHERE { "
					+ "?l2 " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?i2 ."
					+ "?l2 " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
					+ "?l2 " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
					+ "?l2 a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("skos", "broader", true) + " ?l2 ."
					+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " \"" + label + "\".";
			if (!all) {
				broader1_query += "FILTER(LANGMATCHES(LANG(?pl), ?prefLang)) }";
			} else {
				broader1_query += "}";
			}
			List<BindingSet> broader1_result = SesameConnect.SPARQLquery("labelingsystem", broader1_query);
			List<String> broader1_l2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(broader1_result, "l2");
			List<String> broader1_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(broader1_result, "pl");
			List<String> broader1_i2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(broader1_result, "i2");
			for (int i = 0; i < broader1_l2.size(); i++) {
				JSONObject jsonobj_broader1_tmp1 = new JSONObject(); // {}
				JSONArray jsonarray_broader1_tmp1 = new JSONArray(); // {}
				jsonobj_broader1_tmp1.put("name", broader1_pl.get(i));
				jsonobj_broader1_tmp1.put("identifier", broader1_i2.get(i));
				jsonobj_broader1_tmp1.put("state", "broader");
				//broader 2
				String broader2_query = "SELECT * WHERE { "
						+ "?l2 " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?i2 ."
						+ "?l2 " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
						+ "?l2 " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "?l2 a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
						+ "<" + broader1_l2.get(i) + "> " + Config.getPrefixItemOfOntology("skos", "broader", true) + " ?l2 .";
				if (!all) {
					broader2_query += "FILTER(LANGMATCHES(LANG(?pl), ?prefLang)) }";
				} else {
					broader2_query += "}";
				}
				List<BindingSet> broader2_result = SesameConnect.SPARQLquery("labelingsystem", broader2_query);
				List<String> broader2_l2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(broader2_result, "l2");
				List<String> broader2_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(broader2_result, "pl");
				List<String> broader2_i2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(broader2_result, "i2");
				for (int k = 0; k < broader2_l2.size(); k++) {
					JSONObject jsonobj_broader2_tmp1 = new JSONObject(); // {}
					jsonobj_broader2_tmp1.put("name", broader2_pl.get(k));
					jsonobj_broader2_tmp1.put("identifier", broader2_i2.get(k));
					jsonobj_broader2_tmp1.put("state", "broader");
					jsonarray_broader1_tmp1.add(jsonobj_broader2_tmp1);
				}
				jsonobj_broader1_tmp1.put("broader", jsonarray_broader1_tmp1);
				jsonarray_broader.add(jsonobj_broader1_tmp1);
			} //end for broader1
			//narrower 1
			String narrower1_query = "SELECT * WHERE { "
					+ "?l2 " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?i2 ."
					+ "?l2 " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
					+ "?l2 " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
					+ "?l2 a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("skos", "narrower", true) + " ?l2 ."
					+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " \"" + label + "\".";
			if (!all) {
				narrower1_query += "FILTER(LANGMATCHES(LANG(?pl), ?prefLang)) }";
			} else {
				narrower1_query += "}";
			}
			List<BindingSet> narrower1_result = SesameConnect.SPARQLquery("labelingsystem", narrower1_query);
			List<String> narrower1_l2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(narrower1_result, "l2");
			List<String> narrower1_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(narrower1_result, "pl");
			List<String> narrower1_i2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(narrower1_result, "i2");
			for (int i = 0; i < narrower1_l2.size(); i++) {
				JSONObject jsonobj_narrower1_tmp1 = new JSONObject(); // {}
				JSONArray jsonarray_narrower1_tmp1 = new JSONArray(); // {}
				jsonobj_narrower1_tmp1.put("name", narrower1_pl.get(i));
				jsonobj_narrower1_tmp1.put("identifier", narrower1_i2.get(i));
				jsonobj_narrower1_tmp1.put("state", "narrower");
				//narrower 2
				String narrower2_query = "SELECT * WHERE { "
						+ "?l2 " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?i2 ."
						+ "?l2 " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
						+ "?l2 " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "?l2 a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
						+ "<" + narrower1_l2.get(i) + "> " + Config.getPrefixItemOfOntology("skos", "narrower", true) + " ?l2 .";
				if (!all) {
					narrower2_query += "FILTER(LANGMATCHES(LANG(?pl), ?prefLang)) }";
				} else {
					narrower2_query += "}";
				}
				List<BindingSet> narrower2_result = SesameConnect.SPARQLquery("labelingsystem", narrower2_query);
				List<String> narrower2_l2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(narrower2_result, "l2");
				List<String> narrower2_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(narrower2_result, "pl");
				List<String> narrower2_i2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(narrower2_result, "i2");
				for (int k = 0; k < narrower2_l2.size(); k++) {
					JSONObject jsonobj_narrower2_tmp1 = new JSONObject(); // {}
					jsonobj_narrower2_tmp1.put("name", narrower2_pl.get(k));
					jsonobj_narrower2_tmp1.put("identifier", narrower2_i2.get(k));
					jsonobj_narrower2_tmp1.put("state", "narrower");
					jsonarray_narrower1_tmp1.add(jsonobj_narrower2_tmp1);
				}
				jsonobj_narrower1_tmp1.put("narrower", jsonarray_narrower1_tmp1);
				jsonarray_narrower.add(jsonobj_narrower1_tmp1);
			} //end for narrower1
			//related
			String related_query = "SELECT * WHERE { "
					+ "?l2 " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?i2 ."
					+ "?l2 " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?pl ."
					+ "?l2 " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
					+ "?l2 a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("skos", "related", true) + " ?l2 ."
					+ "?l a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " ."
					+ "?l " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " \"" + label + "\".";
			if (!all) {
				related_query += "FILTER(LANGMATCHES(LANG(?pl), ?prefLang)) }";
			} else {
				related_query += "}";
			}
			List<BindingSet> related_result = SesameConnect.SPARQLquery("labelingsystem", related_query);
			List<String> related_l2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(related_result, "l2");
			List<String> related_pl = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(related_result, "pl");
			List<String> related_i2 = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(related_result, "i2");
			for (int i = 0; i < related_l2.size(); i++) {
				JSONObject jsonobj_narrower1_tmp1 = new JSONObject(); // {}
				jsonobj_narrower1_tmp1.put("name", related_pl.get(i));
				jsonobj_narrower1_tmp1.put("identifier", related_i2.get(i));
				jsonobj_narrower1_tmp1.put("state", "related");
				jsonarray_narrower.add(jsonobj_narrower1_tmp1);
			} //end for related
			// Daten zum Center Label
			jsonobj_label.put("broader", jsonarray_broader);
			jsonobj_label.put("narrower", jsonarray_narrower);
			jsonobj_label.put("name", centerPrefLabel);
			jsonobj_label.put("identifier", label);
			jsonobj_label.put("state", "center");
			// pretty print JSON output (Gson)
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			out.print(gson.toJson(jsonobj_label));
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

		// Beispielstruktur
		/*JSONObject jsonobj_label = new JSONObject(); // {}
		 JSONArray jsonarray_broader = new JSONArray(); // {}		
		 JSONObject jsonobj_broader1_tmp1 = new JSONObject(); // {}
		 JSONArray jsonarray_broader1_tmp1 = new JSONArray(); // {}
		 JSONObject jsonobj_broader1_tmp2 = new JSONObject(); // {}
		 JSONArray jsonarray_broader1_tmp2 = new JSONArray(); // {}
		 JSONObject jsonobj_broader2_tmp1 = new JSONObject(); // {}
		 JSONObject jsonobj_broader2_tmp2 = new JSONObject(); // {}
		 jsonobj_broader2_tmp1.put("name", "b11");
		 jsonobj_broader2_tmp2.put("name", "b12");
		 jsonarray_broader1_tmp1.add(jsonobj_broader2_tmp1);
		 jsonarray_broader1_tmp1.add(jsonobj_broader2_tmp2);
		
		 jsonobj_broader1_tmp1.put("name", "b1");
		 jsonobj_broader1_tmp1.put("broader", jsonarray_broader1_tmp1);
		 jsonarray_broader.add(jsonobj_broader1_tmp1);
		 jsonobj_broader1_tmp2.put("name", "b2");
		 jsonobj_broader1_tmp2.put("broader", jsonarray_broader1_tmp2);
		 jsonarray_broader.add(jsonobj_broader1_tmp2);
		
		 jsonobj_label.put("name", name_clear + "@" + lang);
		 jsonobj_label.put("creator", creator);
		 jsonobj_label.put("broader", jsonarray_broader);*/
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
			Logger.getLogger(getBroaderNarrowerTree.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getBroaderNarrowerTree.class.getName()).log(Level.SEVERE, null, ex);
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
