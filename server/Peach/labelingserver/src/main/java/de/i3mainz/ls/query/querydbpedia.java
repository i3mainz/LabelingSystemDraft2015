package de.i3mainz.ls.query;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import com.hp.hpl.jena.query.Query;
import com.hp.hpl.jena.query.QueryExecution;
import com.hp.hpl.jena.query.QueryExecutionFactory;
import com.hp.hpl.jena.query.QueryFactory;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.query.ResultSetFormatter;
import com.hp.hpl.jena.rdf.model.Model;
import com.hp.hpl.jena.rdf.model.ModelFactory;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.io.Reader;
import java.io.StringReader;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLDecoder;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import javax.servlet.ServletException;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import org.jdom.Attribute;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.input.SAXBuilder;
import org.json.simple.JSONArray;
import org.json.simple.JSONObject;

/**
 * SERVLET returns DBpedia lookup data
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 06.02.2015
 */
public class querydbpedia extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException {
		PrintWriter out = response.getWriter();
		try {
			// parse params
			String QueryString = null;
			if (request.getParameter("QueryString") != null) {
				QueryString = request.getParameter("QueryString");
				QueryString = URLDecoder.decode(QueryString, "UTF-8");
			}
			String QueryClass = null;
			if (request.getParameter("QueryClass") != null) {
				QueryClass = request.getParameter("QueryClass");
				QueryClass = URLDecoder.decode(QueryClass, "UTF-8");
			}
			int MaxHits = 5;
			String url_string = "";
			if ("".equals(QueryClass)) {
				url_string = "http://lookup.dbpedia.org/api/search.asmx/KeywordSearch?QueryString=" + QueryString + "&MaxHits=" + MaxHits;
			} else {
				url_string = "http://lookup.dbpedia.org/api/search.asmx/KeywordSearch?QueryClass=" + QueryClass + "&QueryString=" + QueryString + "&MaxHits=" + MaxHits;
			}
			// set JSON object
			JSONObject jsonobj_query = new JSONObject(); // {}
			// send DBpedia Lookup API query
			// http://wiki.dbpedia.org/Lookup
			// Classes: http://wiki.dbpedia.org/Ontology?v=120s
			// http://mappings.dbpedia.org/server/ontology/classes/
			URL url = new URL(url_string);
			//URLConnection conn = url.openConnection();
			HttpURLConnection conn = (HttpURLConnection) url.openConnection();
			// Response lesen
			String lookupXML_string = "";
			BufferedReader br = new BufferedReader(new InputStreamReader(conn.getInputStream(), "UTF-8"));
			String line;
			while ((line = br.readLine()) != null) {
				lookupXML_string += line;
			}
			br.close();
			lookupXML_string = lookupXML_string.replace("  ", "");
			lookupXML_string = lookupXML_string.replace("\t", "");
			lookupXML_string = lookupXML_string.replace("\n", "");
			lookupXML_string = lookupXML_string.replace("\r", "");
			lookupXML_string = lookupXML_string.replace("<ArrayOfResult xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"http://lookup.dbpedia.org/\">", "<ArrayOfResult>");
			// parse XML
			SAXBuilder builder = new SAXBuilder();
			Reader reader = new StringReader(lookupXML_string);
			Document lookupXML = (Document) builder.build(reader);
			Element ArrayOfResult = lookupXML.getRootElement();
			List Results = ArrayOfResult.getChildren("Result");
			JSONArray jsonarray_results = new JSONArray(); // []
			for (Object result : Results) {
				JSONObject jsonobj_result = new JSONObject(); // {}
				JSONArray jsonarray_labels = new JSONArray(); // []
				JSONArray jsonarray_comments = new JSONArray(); // []
				Element Result_element = (Element) result;
				// redircet check and get new URI for RDF parsing
				String dbpediaURL = Result_element.getChildText("URI");
				dbpediaURL = dbpediaURL.replace("resource", "page");
				URL url2 = new URL(dbpediaURL);
				HttpURLConnection conn2 = (HttpURLConnection) url2.openConnection();
				conn2.setInstanceFollowRedirects(false);
				dbpediaURL = conn2.getURL().toString();
				int status = conn2.getResponseCode();
				boolean redirect = false;
				if (status != HttpURLConnection.HTTP_OK) {
					if (status == HttpURLConnection.HTTP_MOVED_TEMP
							|| status == HttpURLConnection.HTTP_MOVED_PERM
							|| status == HttpURLConnection.HTTP_SEE_OTHER) {
						redirect = true;
					}
				}
				if (redirect) {
					dbpediaURL = conn2.getHeaderField("Location");
					conn2 = (HttpURLConnection) new URL(dbpediaURL).openConnection();
					System.out.println("Redirect to URL : " + dbpediaURL);
					dbpediaURL = dbpediaURL.replace("page", "resource");
					dbpediaURL = dbpediaURL.replace("resource", "data");
				}
				// parse RDF
				dbpediaURL = dbpediaURL.replace("page", "data") + ".rdf";
				Model model = ModelFactory.createDefaultModel();
				InputStream instream = new URL(dbpediaURL).openStream();
				model.read(instream, null); // null base RDF_URI, since model URIs are absolute
				instream.close();
				// set and execute SPARQL query to resource
				String queryString
						= "prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>"
						+ "prefix foaf: <http://xmlns.com/foaf/0.1/>"
						+ "SELECT ?label ?wiki ?comment WHERE { "
						+ "OPTIONAL { ?s rdfs:label ?label . }"
						+ "OPTIONAL { ?s foaf:isPrimaryTopicOf ?wiki . }"
						+ "OPTIONAL { ?s foaf:primaryTopic ?wiki . }"
						+ "OPTIONAL { ?s rdfs:comment ?comment . }"
						+ "}";
				Query query = QueryFactory.create(queryString);
				QueryExecution qe = QueryExecutionFactory.create(query, model);
				ResultSet results = qe.execSelect();
				String datasetXML = ResultSetFormatter.asXMLString(results).replace("<sparql xmlns=\"http://www.w3.org/2005/sparql-results#\">", "<sparql>");
				qe.close();
				jsonobj_result.put("URI", dbpediaURL.replace("data", "resource").replace(".rdf", ""));
				// parse SPARQL RESULTS XML
				SAXBuilder builder3 = new SAXBuilder();
				Reader in3 = new StringReader(datasetXML);
				Document document3 = (Document) builder3.build(in3);
				Element rootNode = document3.getRootElement(); //sparql
				List result3 = rootNode.getChildren("results"); //results
				HashSet label_set = new HashSet();
				HashSet comment_set = new HashSet();
				String wiki = "";
				for (Object result31 : result3) {
					Element resultsnode = (Element) result31;
					List resultlist = resultsnode.getChildren("result"); //result
					for (Object resultlist1 : resultlist) {
						Element resultlistnode = (Element) resultlist1;
						List bindinglist = resultlistnode.getChildren("binding"); //binding
						for (Object bindinglist1 : bindinglist) {
							Element bindinglistnode = (Element) bindinglist1;
							// binding-attribut:name, tag:uri
							if (bindinglistnode.getAttributeValue("name").equals("label")) {
								List<Attribute> literalattribute = bindinglistnode.getChild("literal").getAttributes();
								label_set.add(bindinglistnode.getChildText("literal") + "@" + literalattribute.get(0).getValue());
							}
							if (bindinglistnode.getAttributeValue("name").equals("wiki")) {
								wiki = bindinglistnode.getChildText("uri");
								jsonobj_result.put("wiki", wiki);
							}
							if (bindinglistnode.getAttributeValue("name").equals("comment")) {
								List<Attribute> literalattribute = bindinglistnode.getChild("literal").getAttributes();
								if (literalattribute.get(0).getValue().equals("de") || literalattribute.get(0).getValue().equals("en")) {
									comment_set.add(bindinglistnode.getChildText("literal") + "@" + literalattribute.get(0).getValue());
								}
							}
						}
					}
					// set data
					Iterator it = label_set.iterator();
					while (it.hasNext()) {
						JSONObject jsonobj_label = new JSONObject(); // {}
						jsonobj_label.put("label", it.next());
						jsonarray_labels.add(jsonobj_label);
					}
					Iterator it2 = comment_set.iterator();
					while (it2.hasNext()) {
						JSONObject jsonobj_comment = new JSONObject(); // {}
						jsonobj_comment.put("comment", it2.next());
						jsonarray_comments.add(jsonobj_comment);
					}
				}
				jsonarray_results.add(jsonobj_result);
				jsonobj_result.put("labels", jsonarray_labels);
				jsonobj_result.put("comments", jsonarray_comments);
			}
			jsonobj_query.put("results", jsonarray_results);
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
		return "Short description";
	}// </editor-fold>

}
