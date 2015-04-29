package de.i3mainz.ls.fileinput;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.servlet.ServletException;
import javax.servlet.annotation.MultipartConfig;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.Part;
import org.jdom.JDOMException;
import org.json.simple.JSONObject;

/**
 * SERVLET imports a RDF to concept repository
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 06.02.2015
 */
@MultipartConfig(fileSizeThreshold = 1024 * 1024 * 10, // 10 MB
		maxFileSize = 1024 * 1024 * 50, // 50 MB
		maxRequestSize = 1024 * 1024 * 100)      // 100 MB
public class InputConcept extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, JDOMException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String url = null;
			if (request.getParameter("url") != null) {
				System.out.println("url");
				url = request.getParameter("url");
				SesameConnect.SPARQLupdate("concepts", "LOAD <" + url + ">");
			} else {
				String fileName = null;
				for (Part part : request.getParts()) {
					fileName = getFileName(part);
					part.write(Config.FILE_STORE_PATH_PUBLIC + fileName);
				}
				File datei = new File(Config.FILE_STORE_PATH_PUBLIC + fileName);
				SesameConnect.SPARQLupdate("concepts", "LOAD <" + Config.SERVER + "labelingserver/" + fileName + ">");
				if (datei.exists()) {
					datei.delete();
				}
			}
			JSONObject jsonobj_query = new JSONObject(); // {}
			jsonobj_query.put("result", "ok");
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

	/**
	 * Utility method to get file name from HTTP header content-disposition
	 *
	 * @param part
	 * @return
	 */
	private String getFileName(Part part) throws FileNotFoundException {
		try {
			String contentDisp = part.getHeader("content-disposition");
			System.out.println("content-disposition header= " + contentDisp);
			String[] tokens = contentDisp.split(";");
			for (String token : tokens) {
				if (token.trim().startsWith("filename")) {
					return token.substring(token.indexOf("=") + 2, token.length() - 1);
				}
			}
			return "";
		} catch (Exception e) {
			throw new FileNotFoundException();
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
		} catch (JDOMException ex) {
			Logger.getLogger(InputConcept.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(InputConcept.class.getName()).log(Level.SEVERE, null, ex);
		}
	}

	/**
	 * Returns a short description of the servlet.
	 *
	 * @return a String containing servlet description
	 */
	@Override
	public String getServletInfo() {
		return "SERVLET imports a RDF to concept repository";
	}// </editor-fold>

}
