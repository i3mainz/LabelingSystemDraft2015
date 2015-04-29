package webservicetest;

import java.io.IOException;
import java.io.PrintWriter;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

public class WebService extends HttpServlet {

	public static int status = 0;
	public static String result = null;
	public static PrintWriter out;

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		out = response.getWriter();

		try {
			String mode = null;
			if (request.getParameter("mode") != null) {
				mode = request.getParameter("mode");
			}
			if (mode.equals("start")) {
				start();
				response.setContentType("text/xml;charset=UTF-8");
			} else if (mode.equals("update")) {
				update();
				response.setContentType("text/xml;charset=UTF-8");
			} else if (mode.equals("finish")) {
				finish();
				response.setContentType("text/plain;charset=UTF-8");
			}
		} finally {
			out.close();
		}
	}

	public void start() {
		status = 0;
		(new Thread(new MyRunnable())).start();
		update();
	}

	public void update() {
		out.print("<status>" + status + "</status>");
	}

	public void finish() {
		out.print(result);
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
	// </editor-fold>

}
