package de.i3mainz.ls.visualisation;

import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

/**
 * SERVLET returns SVG-Tree HTML/SVG object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 06.02.2015
 */
public class getTreeSVG extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		PrintWriter out = response.getWriter();
		try {
			// PARSE PARAMETER
			String svg = request.getParameter("svg");
			String width = request.getParameter("width");
			String height = request.getParameter("height");
			String user = request.getParameter("user");
			// REPLACE SPECIAL CHARS
			svg = svg.replace("ä", "&auml;");
			svg = svg.replace("Ä", "&Auml;");
			svg = svg.replace("ü", "&uuml;");
			svg = svg.replace("Ü", "&Uuml;");
			svg = svg.replace("ö", "&ouml;");
			svg = svg.replace("Ö", "&Ouml;");
			svg = svg.replace("ß", "&szlig;");
			svg = svg.replace("à", "&agrave;");
			svg = svg.replace("À", "&Agrave;");
			svg = svg.replace("á", "&aacute;");
			svg = svg.replace("Á", "&Aacute;");
			svg = svg.replace("â", "&acirc;");
			svg = svg.replace("Â", "&Acirc;");
			svg = svg.replace("ã", "&atilde;");
			svg = svg.replace("Ã", "&Atilde;");
			svg = svg.replace("å", "&aring;");
			svg = svg.replace("Å", "&Aring;");
			svg = svg.replace("æ", "&aelig;");
			svg = svg.replace("Æ", "&Aelig;");
			svg = svg.replace("Ç", "&Ccedil;");
			svg = svg.replace("ç", "&ccedil;");
			svg = svg.replace("è", "&egrave;");
			svg = svg.replace("È", "&Egrave;");
			svg = svg.replace("é", "&eacute;");
			svg = svg.replace("E", "&Eacute;");
			svg = svg.replace("ê", "&ecirc;");
			svg = svg.replace("Ê", "&Ecirc;");
			svg = svg.replace("ì", "&igrave;");
			svg = svg.replace("Ì", "&Igrave;");
			svg = svg.replace("í", "&iacute;");
			svg = svg.replace("Ì", "&Iacute;");
			svg = svg.replace("î", "&icirc;");
			svg = svg.replace("Î", "&Icirc;");
			svg = svg.replace("ò", "&ograve;");
			svg = svg.replace("Ò", "&Ograve;");
			svg = svg.replace("ó", "&oacute;");
			svg = svg.replace("Ó", "&Oacute;");
			svg = svg.replace("ô", "&ocirc;");
			svg = svg.replace("Ô", "&Ocirc;");
			svg = svg.replace("õ", "&otilde;");
			svg = svg.replace("Õ", "&Otilde;");
			svg = svg.replace("ø", "&otilde;");
			svg = svg.replace("Ø", "&Oslash;");
			svg = svg.replace("ù", "&ugrave;");
			svg = svg.replace("Ù", "&Ugrave;");
			svg = svg.replace("ú", "&uacute;");
			svg = svg.replace("Ú", "&Uacute;");
			svg = svg.replace("û", "&ucirc;");
			svg = svg.replace("Û", "&Ucirc;");
			// WRITE OUTPUT HTML
			out.println("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\">");
			out.println("<html>");
			out.println("<head>");
			out.println("<meta charset=\"utf-8\">");
			out.println("<style type=\"text/css\">");
			out.println(".node circle {");
			out.println("fill: #fff;");
			out.println("stroke: steelblue;");
			out.println("stroke-width: 1.5px;");
			out.println("}");
			out.println(".node text {");
			out.println("font-size: 11px;");
			out.println("color: #333333;");
			out.println("font-family: \"Helvetica Neue\",Helvetica,Arial,sans-serif;");
			out.println("}");
			out.println("path.link {");
			out.println("fill: none;");
			out.println("stroke: #ccc;");
			out.println("stroke-width: 1.5px;");
			out.println("}");
			out.println("</style>");
			out.println("</head>");
			out.println("<body>");
			out.println("<h1>Labeling System Project Tree Export (excerpt) from " + user + "</h1>");
			out.print("<svg id=\"tree\" width=\"" + width + "\" height=\"" + height + "\">");
			out.print(svg);
			out.print("</svg>\n");
			out.println("</body>");
			out.println("</html>");
		} catch (Exception e) {
			response.sendError(500, Logging.getMessageTEXT(e, getClass().getName()));
		} finally {
			response.setContentType("text/html;charset=iso-8859-1");
			response.setHeader("Content-disposition", "attachment;filename=tree.htm");
			response.setHeader("Access-Control-Allow-Origin", "*");
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

	// </editor-fold>
}
