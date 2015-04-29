package de.i3mainz.ls.instances.java;

/**
 * CLASS to describe a SPARQL endpoint
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 18.03.2015
 */
public class SPARQLendpoint {

	private String id = "";
	private String sparqlname = "";
	private String sparqlxmluri = "";
	private String sparqlquery = "";

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getSparqlname() {
		return sparqlname;
	}

	public void setSparqlname(String sparqlname) {
		this.sparqlname = sparqlname;
	}

	public String getSparqlxmluri() {
		return sparqlxmluri;
	}

	public void setSparqlxmluri(String sparqlxmluri) {
		this.sparqlxmluri = sparqlxmluri;
	}

	public String getSparqlquery() {
		return sparqlquery;
	}

	public void setSparqlquery(String sparqlquery) {
		this.sparqlquery = sparqlquery;
	}

}
