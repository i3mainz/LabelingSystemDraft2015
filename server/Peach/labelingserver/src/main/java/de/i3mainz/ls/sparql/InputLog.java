package de.i3mainz.ls.sparql;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.IOException;
import java.io.PrintWriter;
import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.json.simple.JSONObject;

/**
 * SERVLET send a log triple to the triplestore
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.02.2015
 */
public class InputLog extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, NullPointerException {
		PrintWriter out = response.getWriter();
		try {
			String user = null;
			if (request.getParameter("user") != null) {
				user = request.getParameter("user");
			}
			if (user == null) {
				throw new NullPointerException();
			}
			// GET DATE
			Calendar cal = Calendar.getInstance();
			Date time = cal.getTime();
			DateFormat formatter = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZ");
			String d = formatter.format(time);
			// SEND INPUT
			String update = "INSERT DATA { "
					+ Config.Instance("log", d, true) + " "
					+ Config.getPrefixItemOfOntology("ls", "login", true) + " "
					+ "\"" + user + "\" . }";
			SesameConnect.SPARQLupdate("labelingsystem", update);
			// OUTPUT
			JSONObject jsonobj_query = new JSONObject(); // {}
			jsonobj_query.put("logged", d);
			// pretty print JSON output (Gson)
			Gson gson = new GsonBuilder().setPrettyPrinting().create();
			out.print(gson.toJson(jsonobj_query));
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
		processRequest(request, response);
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
