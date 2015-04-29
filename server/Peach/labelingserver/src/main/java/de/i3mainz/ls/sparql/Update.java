package de.i3mainz.ls.sparql;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.URLDecoder;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.json.simple.JSONObject;
import org.openrdf.query.MalformedQueryException;
import org.openrdf.query.UpdateExecutionException;
import org.openrdf.repository.RepositoryException;

/**
 * SERVLET sends SPARQL UPDATE to triplestore
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 04.02.2015
 */
public class Update extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, RepositoryException, MalformedQueryException, UpdateExecutionException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		String update = null;
		try {
			if (request.getParameter("update") != null) {
				update = request.getParameter("update");
				// http://www.w3schools.com/jsref/jsref_encodeuricomponent.asp JavaScript encodeURIComponent() Function
				update = URLDecoder.decode(update, "UTF-8");
			}
			if (update == null) {
				throw new NullPointerException("[Update.java]");
			}
			SesameConnect.SPARQLupdate("labelingsystem", update);
			JSONObject jsonobj_query = new JSONObject(); // {}
			jsonobj_query.put("update", update);
			// pretty print JSON output (Gson)
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			out.print(gson.toJson(jsonobj_query));
			response.setStatus(200);
		} catch (Exception e) {
			response.setStatus(500);
			out.print(Logging.getMessageTEXT(e, getClass().getName()));
		} finally {
			response.setContentType("application/json;charset=UTF-8");
			response.setHeader("Access-Control-Allow-Origin", "*");
			response.setCharacterEncoding("UTF-8");
			out.close();
		}
	}

	// <editor-fold defaultstate="collapsed" desc="HttpServlet methods. Click on the + sign on the left to edit the code.">
	/**
	 * Handles the HTTP <code>POST</code> method.
	 *
	 * @param request servlet request
	 * @param response servlet response
	 * @throws ServletException if a servlet-specific error occurs
	 * @throws IOException if an I/O error occurs
	 */
	@Override
	protected void doPost(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		try {
			processRequest(request, response);
		} catch (RepositoryException ex) {
			Logger.getLogger(Update.class.getName()).log(Level.SEVERE, null, ex);
		} catch (MalformedQueryException ex) {
			Logger.getLogger(Update.class.getName()).log(Level.SEVERE, null, ex);
		} catch (UpdateExecutionException ex) {
			Logger.getLogger(Update.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(Update.class.getName()).log(Level.SEVERE, null, ex);
		}
	}

	/**
	 * Returns a short description of the servlet.
	 *
	 * @return a String containing servlet description
	 */
	@Override
	public String getServletInfo() {
		return "Servlet sends SPARQL UPDATE to triplestore";
	}// </editor-fold>

}
