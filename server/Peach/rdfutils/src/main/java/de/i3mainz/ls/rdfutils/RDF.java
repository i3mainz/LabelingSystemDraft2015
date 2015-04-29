package de.i3mainz.ls.rdfutils;

import com.github.jsonldjava.jena.JenaJSONLD;
import com.hp.hpl.jena.rdf.model.Literal;
import com.hp.hpl.jena.rdf.model.Model;
import com.hp.hpl.jena.rdf.model.ModelFactory;
import com.hp.hpl.jena.rdf.model.Property;
import com.hp.hpl.jena.rdf.model.Resource;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.exceptions.RdfException;
import java.io.ByteArrayOutputStream;
import java.io.UnsupportedEncodingException;

/**
 * CLASS for set up a RDF graph and export it
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 04.02.2015
 */
public class RDF {

	private Model model = null;

	/**
	 * Constructor
	 */
	public RDF() {
		model = ModelFactory.createDefaultModel();
	}

	/**
	 * set triple with literal
	 *
	 * @param subject
	 * @param predicate
	 * @param object
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	public void setModelLiteral(String subject, String predicate, String object) throws RdfException {
		try {
			Resource s = model.createResource(subject);
			Property p = model.createProperty(predicate);
			Literal o = model.createLiteral(object);
			model.add(s, p, o);
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * set triple with literal and language
	 *
	 * @param subject
	 * @param predicate
	 * @param object
	 * @param lang
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	public void setModelLiteralLanguage(String subject, String predicate, String object, String lang) throws RdfException {
		try {
			Resource s = model.createResource(subject);
			Property p = model.createProperty(predicate);
			Literal o = model.createLiteral(object, lang);
			model.add(s, p, o);
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * set triple with uri
	 *
	 * @param subject
	 * @param predicate
	 * @param object
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	public void setModelURI(String subject, String predicate, String object) throws RdfException {
		try {
			Resource s = model.createResource(subject);
			Property p = model.createProperty(predicate);
			Resource o = model.createResource(object);
			model.add(s, p, o);
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * set triple with blank node
	 *
	 * @param subject
	 * @param predicate
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	public Resource setModelBlankNode(String subject, String predicate) throws RdfException {
		try {
			Resource s = model.createResource(subject);
			Property p = model.createProperty(predicate);
			Resource o = model.createResource();
			model.add(s, p, o);
			return o;
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * set triple and create model statement automaticly
	 * @param subject
	 * @param predicate
	 * @param object
	 * @throws RdfException 
	 */
	public void setModelTriple(String subject, String predicate, String object) throws RdfException {
		try {
			if (object.startsWith("http://") || object.contains("mailto")) {
				setModelURI(subject, predicate, object);
			} else {
				if (object.contains("@")) {
					String literalLanguage[] = object.split("@");
					setModelLiteralLanguage(subject, predicate, literalLanguage[0].replaceAll("\"", ""), literalLanguage[1]);
				} else {
					setModelLiteral(subject, predicate, object.replaceAll("\"", ""));
				}
			}
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * get RDF model as RDF/XML
	 *
	 * @return
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	public String getModel() throws RdfException {
		try {
			ByteArrayOutputStream o = new ByteArrayOutputStream();
			model.write(o, "RDF/XML");
			model.removeAll();
			return o.toString("UTF-8");
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

	/**
	 * get RDF model in several formats
	 * [Turtle,N-Triples,RDF/XML,RDF/JSON,TriG,NQuads]
	 *
	 * @param format
	 * @return
	 * @throws UnsupportedEncodingException
	 * @throws de.i3mainz.ls.rdfutils.exceptions.RdfException
	 */
	public String getModel(String format) throws UnsupportedEncodingException, RdfException {
		// https://jena.apache.org/documentation/io/rdf-output.html#jena_model_write_formats
		try {
			JenaJSONLD.init();
			ByteArrayOutputStream o = new ByteArrayOutputStream();
			model.setNsPrefix("rdf", Config.PREFIX_RDF);
			model.setNsPrefix("rdfs", Config.PREFIX_RDFS);
			model.setNsPrefix("dct", Config.PREFIX_DCTERMS);
			model.setNsPrefix("dc", Config.PREFIX_DCELEMENTS);
			model.setNsPrefix("skos", Config.PREFIX_SKOS);
			model.setNsPrefix("owl", Config.PREFIX_OWL);
			model.setNsPrefix("ls", Config.PREFIX_LABELINGSYSTEM);
			model.setNsPrefix("foaf", Config.PREFIX_FOAF);
			model.write(o, format);
			model.removeAll();
			return o.toString("UTF-8");
		} catch (Exception e) {
			throw new RdfException("[" + RDF.class.getName() + " | " + Thread.currentThread().getStackTrace()[1].getMethodName() + ": " + e + "]");
		}
	}

}
