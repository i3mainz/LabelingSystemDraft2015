package de.i3mainz.ls.fileinput;

import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.annotation.MultipartConfig;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.Part;

/**
 * SERVLET imports a CSV to labelingsystem repository
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 16.03.2015
 */
@MultipartConfig(fileSizeThreshold = 1024 * 1024 * 10, // 10 MB
		maxFileSize = 1024 * 1024 * 50, // 50 MB
		maxRequestSize = 1024 * 1024 * 100)      // 100 MB
public class InputLabelCSV extends HttpServlet {

	public static double status = -1.0;
	public static String action = "";
	public static String creator = null;
	public static boolean validator = false;
	public static String csvContent = "";
	public static String mode = "";
	public static PrintWriter out;
	public static int maxSteps = -1;
	public static int currentStep = -1;

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		out = response.getWriter();
		try {
			// parse mode
			if (request.getParameter("mode") != null) {
				mode = request.getParameter("mode");
			}
			if (mode.equals("start")) {
				// parse creator
				if (request.getParameter("creator") != null) {
					creator = request.getParameter("creator");
				}
				// parse validator
				if (request.getParameter("validator") != null) {
					validator = Boolean.valueOf(request.getParameter("validator"));
				}
				// upload file and read it to array
				String fileName = null;
				for (Part part : request.getParts()) {
					fileName = CSV.getFileName(part);
					part.write(Config.FILE_STORE_PATH + fileName);
				}
				File datei = new File(Config.FILE_STORE_PATH + fileName);
				String line = "";
				BufferedReader br = null;
				//String csvContent = "";
				br = new BufferedReader(new InputStreamReader(new FileInputStream(Config.FILE_STORE_PATH + fileName), "UTF8"));
				csvContent = "";
				while ((line = br.readLine()) != null) {
					csvContent += line + "\r\n";
				}
				if (datei.exists()) {
					datei.delete();
				}
				start();
				response.setStatus(200);
			} else if (mode.equals("update")) {
				update();
				response.setStatus(200);
			} else if (mode.equals("finish")) {
				finish();
				response.setStatus(200);
			} else {
				throw new IllegalArgumentException();
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

	/**
	 * start thread and parse data
	 */
	public void start() {
		status = 0.0;
		currentStep = 0;
		action = "start parsing...";
		System.out.println("=================================");
		String[] csvLines = csvContent.split("\r\n");
		if (validator) {
			maxSteps = (csvLines.length - 1) * 2; // 100%
		} else {
			maxSteps = (csvLines.length - 1) * 4; // 100%
		}
		(new Thread(new CSV())).start();
		update();
	}

	/**
	 * get update status
	 */
	public void update() {
		out.print("{ \"status\": \"" + status + "\",  \"action\": \"" + action + "\"}");
	}

	/**
	 * get result if finished
	 */
	public void finish() {
		out.print(CSV.JSON_STRING);
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
		processRequest(request, response);
	}

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
		return "SERVLET imports a CSV to labelingsystem repository";
	}// </editor-fold>

}
