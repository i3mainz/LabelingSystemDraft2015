package de.i3mainz.ls.instances.json;

import de.i3mainz.ls.instances.java.Label;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import de.i3mainz.ls.Config.Config;
import de.i3mainz.ls.rdfutils.SesameConnect;
import de.i3mainz.ls.rdfutils.exceptions.Logging;
import de.i3mainz.ls.rdfutils.exceptions.ResourceNotAvailableException;
import de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException;
import java.io.IOException;
import java.io.PrintWriter;
import java.util.HashMap;
import java.util.HashSet;
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
 * SERVLET returns a label object
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 05.02.2015
 */
public class getLabel extends HttpServlet {

	protected void processRequest(HttpServletRequest request, HttpServletResponse response)
			throws ServletException, IOException, ResourceNotAvailableException, de.i3mainz.ls.rdfutils.exceptions.ConfigException, de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException {
		PrintWriter out = response.getWriter();
		try {
			String label = null;
			if (request.getParameter("id") != null) {
				label = request.getParameter("id");
			}
			// QUERY FOR TRIPLESTORE
			String query = null;
			boolean check_voc_exists = false;
			boolean check_voc_notexists = false;
			// START BUILD JSON
			JSONObject jsonobj_query = new JSONObject(); // {}
			// SET QUERY
			query = "SELECT ?labelIdentifier ?prefLabels ?altLabels ?notes ?definitions ?concepts "
					+ "?vocabularyID ?broaders ?narrowers ?relateds ?broadMatchs ?narrowMatchs ?relatedMatchs "
					+ "?exactMatchs ?closeMatchs ?sameAss ?seeAlsos ?isDefinedBys ?label ?creator ?date ?prefLang WHERE { "
					+ "?label a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
					+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelIdentifier ."
					+ "?label " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
					+ "?label " + Config.getPrefixItemOfOntology("dcterms", "date", true) + " ?date ."
					+ "?label " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
					+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabels ."
					+ "?label " + Config.getPrefixItemOfOntology("ls", "sameAs", true) + " ?concepts . "
					+ "?label " + Config.getPrefixItemOfOntology("ls", "belongsTo", true) + " ?vocabulary . "
					+ "?vocabulary " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?vocabularyID . "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "broader", true) + " ?broaders . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?broaders . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "narrower", true) + " ?narrowers . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?narrowers . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "related", true) + " ?relateds . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?relateds . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "definition", true) + " ?definitions . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?definitions . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "broadMatch", true) + " ?broadMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?broadMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "narrowMatch", true) + " ?narrowMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?narrowMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "relatedMatch", true) + " ?relatedMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?relatedMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "exactMatch", true) + " ?exactMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?exactMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "closeMatch", true) + " ?closeMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?closeMatchs . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("owl", "sameAs", true) + " ?sameAss . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?sameAss . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("rdfs", "seeAlso", true) + " ?seeAlsos . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?seeAlsos . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", true) + " ?isDefinedBys . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?isDefinedBys . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "note", true) + " ?notes . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?notes . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabels . } "
					+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?altLabels . } "
					+ "FILTER(?labelIdentifier = \"" + label + "\" ) "
					+ "} "
					+ "ORDER BY ?labelIdentifier ?prefLabels ?altLabels ?notes ?definitions ?concepts "
					+ "?vocabularyID ?broaders ?narrowers ?relateds ?broadMatchs ?narrowMatchs ?relatedMatchs "
					+ "?exactMatchs ?closeMatchs ?sameAss ?seeAlsos ?isDefinedBys";
			// EXECUTE QUERY
			List<BindingSet> query_result = SesameConnect.SPARQLquery("labelingsystem", query);
			// results
			List<String> query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "labelIdentifier");
			List<String> query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
			List<String> query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
			List<String> query_date = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "date");
			List<String> query_prefLabels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "prefLabels");
			List<String> query_concept = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "concepts");
			List<String> query_vocabularyID = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "vocabularyID");
			List<String> query_prefLang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "prefLang");
			List<String> query_altLabels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "altLabels");
			List<String> query_notes = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "notes");
			List<String> query_definitions = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "definitions");
			List<String> query_broader = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "broaders");
			List<String> query_narrower = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "narrowers");
			List<String> query_related = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "relateds");
			List<String> query_broadMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "broadMatchs");
			List<String> query_narrowMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "narrowMatchs");
			List<String> query_relatedMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "relatedMatchs");
			List<String> query_exactMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "exactMatchs");
			List<String> query_closeMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "closeMatchs");
			List<String> query_seeAlso = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "seeAlsos");
			List<String> query_sameAs = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "sameAss");
			List<String> query_isDefinedBy = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "isDefinedBys");
			if (!query_id.isEmpty() && query_id.size() == query_label.size() && query_id.size() == query_creator.size()
					&& query_id.size() == query_date.size() && query_id.size() == query_prefLabels.size()
					&& query_id.size() == query_concept.size() && query_id.size() == query_vocabularyID.size()
					&& query_id.size() == query_altLabels.size() && query_id.size() == query_notes.size()
					&& query_id.size() == query_definitions.size() && query_id.size() == query_broader.size()
					&& query_id.size() == query_narrower.size() && query_id.size() == query_related.size()
					&& query_id.size() == query_broadMatch.size() && query_id.size() == query_narrowMatch.size()
					&& query_id.size() == query_relatedMatch.size() && query_id.size() == query_exactMatch.size()
					&& query_id.size() == query_closeMatch.size() && query_id.size() == query_seeAlso.size()
					&& query_id.size() == query_sameAs.size() && query_id.size() == query_isDefinedBy.size()
					&& query_id.size() == query_prefLang.size()) {
				check_voc_exists = true;
			}
			// if no vocabulary available
			if (query_id.isEmpty()) {
				// SET QUERY
				query = "SELECT ?labelIdentifier ?prefLabels ?altLabels ?notes ?definitions "
						+ "?broaders ?narrowers ?relateds ?broadMatchs ?narrowMatchs ?relatedMatchs "
						+ "?exactMatchs ?closeMatchs ?sameAss ?seeAlsos ?isDefinedBys ?label ?creator ?date ?prefLang WHERE { "
						+ "?label a " + Config.getPrefixItemOfOntology("ls", "Label", true) + " . "
						+ "?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?labelIdentifier ."
						+ "?label " + Config.getPrefixItemOfOntology("dcelements", "creator", true) + " ?creator ."
						+ "?label " + Config.getPrefixItemOfOntology("dcterms", "date", true) + " ?date ."
						+ "?label " + Config.getPrefixItemOfOntology("ls", "prefLang", true) + " ?prefLang ."
						+ "?label " + Config.getPrefixItemOfOntology("skos", "prefLabel", true) + " ?prefLabels ."
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "broader", true) + " ?broaders . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?broaders . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "narrower", true) + " ?narrowers . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?narrowers . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "related", true) + " ?relateds . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?relateds . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "definition", true) + " ?definitions . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?definitions . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "broadMatch", true) + " ?broadMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?broadMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "narrowMatch", true) + " ?narrowMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?narrowMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "relatedMatch", true) + " ?relatedMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?relatedMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "exactMatch", true) + " ?exactMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?exactMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "closeMatch", true) + " ?closeMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?closeMatchs . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("owl", "sameAs", true) + " ?sameAss . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?sameAss . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("rdfs", "seeAlso", true) + " ?seeAlsos . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?seeAlsos . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("rdfs", "isDefinedBy", true) + " ?isDefinedBys . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + "?isDefinedBys . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "note", true) + " ?notes . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?notes . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("skos", "altLabel", true) + " ?altLabels . } "
						+ "OPTIONAL { ?label " + Config.getPrefixItemOfOntology("ls", "identifier", true) + " ?altLabels . } "
						+ "FILTER(?labelIdentifier = \"" + label + "\" ) "
						+ "} "
						+ "ORDER BY ?labelIdentifier ?prefLabels ?altLabels ?notes ?definitions "
						+ "?broaders ?narrowers ?relateds ?broadMatchs ?narrowMatchs ?relatedMatchs "
						+ "?exactMatchs ?closeMatchs ?sameAss ?seeAlsos ?isDefinedBys";

				// EXECUTE QUERY
				query_result = SesameConnect.SPARQLquery("labelingsystem", query);
				// results
				query_id = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "labelIdentifier");
				query_label = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "label");
				query_creator = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "creator");
				query_date = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "date");
				query_prefLabels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "prefLabels");
				query_prefLang = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "prefLang");
				query_altLabels = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "altLabels");
				query_notes = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "notes");
				query_definitions = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "definitions");
				query_broader = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "broaders");
				query_narrower = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "narrowers");
				query_related = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "relateds");
				query_broadMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "broadMatchs");
				query_narrowMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "narrowMatchs");
				query_relatedMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "relatedMatchs");
				query_exactMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "exactMatchs");
				query_closeMatch = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "closeMatchs");
				query_seeAlso = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "seeAlsos");
				query_sameAs = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "sameAss");
				query_isDefinedBy = SesameConnect.getValuesFromBindingSet_ORDEREDLIST(query_result, "isDefinedBys");
				if (query_id.size() == query_label.size() && query_id.size() == query_creator.size()
						&& query_id.size() == query_date.size() && query_id.size() == query_prefLabels.size()
						&& query_id.size() == query_altLabels.size() && query_id.size() == query_notes.size()
						&& query_id.size() == query_definitions.size() && query_id.size() == query_broader.size()
						&& query_id.size() == query_narrower.size() && query_id.size() == query_related.size()
						&& query_id.size() == query_broadMatch.size() && query_id.size() == query_narrowMatch.size()
						&& query_id.size() == query_relatedMatch.size() && query_id.size() == query_exactMatch.size()
						&& query_id.size() == query_closeMatch.size() && query_id.size() == query_seeAlso.size()
						&& query_id.size() == query_sameAs.size() && query_id.size() == query_isDefinedBy.size()
						&& query_id.size() == query_prefLang.size()) {
					check_voc_notexists = true;
				}
			}
			// if no labelid
			if (query_id.isEmpty()) {
				throw new ResourceNotAvailableException("label");
			} else if (check_voc_exists || check_voc_notexists) {
				// create labeloutput object for label
				HashMap<String, Label> labelobject = new HashMap<String, Label>();
				for (int i = 0; i < query_id.size(); i++) {
					if (!labelobject.keySet().contains(query_id.get(i))) {
						labelobject.put(query_id.get(i), new Label());
						labelobject.get(query_id.get(i)).setId(query_id.get(i));
						labelobject.get(query_id.get(i)).setCreator(query_creator.get(i));
						labelobject.get(query_id.get(i)).setLabel(query_label.get(i));
						labelobject.get(query_id.get(i)).setDate(query_date.get(i));
						labelobject.get(query_id.get(i)).setPrefLang(query_prefLang.get(i));
						// multiple values
						if (check_voc_exists) {
							labelobject.get(query_id.get(i)).getConcepts().add(query_concept.get(i));
							labelobject.get(query_id.get(i)).getVocabularies().add(query_vocabularyID.get(i));
						}
						labelobject.get(query_id.get(i)).getPrefLabels().add(query_prefLabels.get(i));
						// multiple optional values
						if (!query_altLabels.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getAltLabels().add(query_altLabels.get(i));
						}
						if (!query_notes.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getNotes().add(query_notes.get(i));
						}
						if (!query_definitions.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getDefinitions().add(query_definitions.get(i));
						}
						if (!query_broader.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getBroader().add(query_broader.get(i));
						}
						if (!query_narrower.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getNarrower().add(query_narrower.get(i));
						}
						if (!query_related.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getRelated().add(query_related.get(i));
						}
						if (!query_broadMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getBroadMatch().add(query_broadMatch.get(i));
						}
						if (!query_narrowMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getNarrowMatch().add(query_narrowMatch.get(i));
						}
						if (!query_relatedMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getRelatedMatch().add(query_relatedMatch.get(i));
						}
						if (!query_exactMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getExactMatch().add(query_exactMatch.get(i));
						}
						if (!query_closeMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getCloseMatch().add(query_closeMatch.get(i));
						}
						if (!query_seeAlso.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getSeeAlso().add(query_seeAlso.get(i));
						}
						if (!query_sameAs.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getSameAs().add(query_sameAs.get(i));
						}
						if (!query_isDefinedBy.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getIsDefinedBy().add(query_isDefinedBy.get(i));
						}
					} else {
						// multiple values
						if (check_voc_exists) {
							labelobject.get(query_id.get(i)).getConcepts().add(query_concept.get(i));
							labelobject.get(query_id.get(i)).getVocabularies().add(query_vocabularyID.get(i));
						}
						labelobject.get(query_id.get(i)).getPrefLabels().add(query_prefLabels.get(i));
						// multiple optional values
						if (!query_altLabels.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getAltLabels().add(query_altLabels.get(i));
						}
						if (!query_notes.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getNotes().add(query_notes.get(i));
						}
						if (!query_definitions.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getDefinitions().add(query_definitions.get(i));
						}
						if (!query_broader.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getBroader().add(query_broader.get(i));
						}
						if (!query_narrower.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getNarrower().add(query_narrower.get(i));
						}
						if (!query_related.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getRelated().add(query_related.get(i));
						}
						if (!query_broadMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getBroadMatch().add(query_broadMatch.get(i));
						}
						if (!query_narrowMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getNarrowMatch().add(query_narrowMatch.get(i));
						}
						if (!query_relatedMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getRelatedMatch().add(query_relatedMatch.get(i));
						}
						if (!query_exactMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getExactMatch().add(query_exactMatch.get(i));
						}
						if (!query_closeMatch.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getCloseMatch().add(query_closeMatch.get(i));
						}
						if (!query_seeAlso.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getSeeAlso().add(query_seeAlso.get(i));
						}
						if (!query_sameAs.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getSameAs().add(query_sameAs.get(i));
						}
						if (!query_isDefinedBy.get(i).contains(query_id.get(i))) {
							labelobject.get(query_id.get(i)).getIsDefinedBy().add(query_isDefinedBy.get(i));
						}
					}
				}
				//				
				// create json output object for label
				//
				JSONArray jsonarray_data = new JSONArray(); // []
				for (String name : labelobject.keySet()) {
					JSONObject jsonobj_data = new JSONObject(); // {}
					// arrays for multiple values
					JSONArray jsonarray_prefLabels = new JSONArray(); // []
					JSONArray jsonarray_altLabels = new JSONArray(); // []
					JSONArray jsonarray_concepts = new JSONArray(); // []
					JSONArray jsonarray_vocabularies = new JSONArray(); // []
					JSONArray jsonarray_notes = new JSONArray(); // []
					JSONArray jsonarray_definitions = new JSONArray(); // []
					JSONArray jsonarray_broader = new JSONArray(); // []
					JSONArray jsonarray_narrower = new JSONArray(); // []
					JSONArray jsonarray_related = new JSONArray(); // []
					JSONArray jsonarray_broadMatch = new JSONArray(); // []
					JSONArray jsonarray_narrowMatch = new JSONArray(); // []
					JSONArray jsonarray_relatedMatch = new JSONArray(); // []
					JSONArray jsonarray_exactMatch = new JSONArray(); // []
					JSONArray jsonarray_closeMatch = new JSONArray(); // []
					JSONArray jsonarray_seeAlso = new JSONArray(); // []
					JSONArray jsonarray_sameAs = new JSONArray(); // []
					JSONArray jsonarray_isDefinedBy = new JSONArray(); // []
					// set prefLabels
					HashSet pls = labelobject.get(name).getPrefLabels();
					for (Object pl : pls) {
						JSONObject jsonobj_prefLabel = new JSONObject(); // {}
						jsonobj_prefLabel.put("prefLabel", pl);
						jsonarray_prefLabels.add(jsonobj_prefLabel);
					}
					// set altLabels
					HashSet als = labelobject.get(name).getAltLabels();
					for (Object al : als) {
						JSONObject jsonobj_altLabel = new JSONObject(); // {}
						jsonobj_altLabel.put("altLabel", al);
						jsonarray_altLabels.add(jsonobj_altLabel);
					}
					// set concepts
					HashSet cos = labelobject.get(name).getConcepts();
					for (Object co : cos) {
						JSONObject jsonobj_concept = new JSONObject(); // {}
						jsonobj_concept.put("concept", co);
						jsonarray_concepts.add(jsonobj_concept);
					}
					// set vocabularies
					HashSet vos = labelobject.get(name).getVocabularies();
					for (Object vo : vos) {
						JSONObject jsonobj_vocabulary = new JSONObject(); // {}
						jsonobj_vocabulary.put("vocabularyID", vo);
						jsonarray_vocabularies.add(jsonobj_vocabulary);
					}
					// set notes
					HashSet nos = labelobject.get(name).getNotes();
					for (Object no : nos) {
						JSONObject jsonobj_note = new JSONObject(); // {}
						jsonobj_note.put("note", no);
						jsonarray_notes.add(jsonobj_note);
					}
					// set definitions
					HashSet des = labelobject.get(name).getDefinitions();
					for (Object de : des) {
						JSONObject jsonobj_definition = new JSONObject(); // {}
						jsonobj_definition.put("definition", de);
						jsonarray_definitions.add(jsonobj_definition);
					}
					// set broader
					HashSet brs = labelobject.get(name).getBroader();
					for (Object br : brs) {
						JSONObject jsonobj_broader = new JSONObject(); // {}
						jsonobj_broader.put("broader", br);
						jsonarray_broader.add(jsonobj_broader);
					}
					// set narrower
					HashSet nas = labelobject.get(name).getNarrower();
					for (Object na : nas) {
						JSONObject jsonobj_narrower = new JSONObject(); // {}
						jsonobj_narrower.put("narrower", na);
						jsonarray_narrower.add(jsonobj_narrower);
					}
					// set related
					HashSet res = labelobject.get(name).getRelated();
					for (Object re : res) {
						JSONObject jsonobj_related = new JSONObject(); // {}
						jsonobj_related.put("related", re);
						jsonarray_related.add(jsonobj_related);
					}
					// set broadMatch
					HashSet bms = labelobject.get(name).getBroadMatch();
					for (Object bm : bms) {
						JSONObject jsonobj_broadMatch = new JSONObject(); // {}
						jsonobj_broadMatch.put("broadMatch", bm);
						jsonarray_broadMatch.add(jsonobj_broadMatch);
					}
					// set narrowMatch
					HashSet nms = labelobject.get(name).getNarrowMatch();
					for (Object nm : nms) {
						JSONObject jsonobj_narrowMatch = new JSONObject(); // {}
						jsonobj_narrowMatch.put("narrowMatch", nm);
						jsonarray_narrowMatch.add(jsonobj_narrowMatch);
					}
					// set relatedMatch
					HashSet rms = labelobject.get(name).getRelatedMatch();
					for (Object rm : rms) {
						JSONObject jsonobj_relatedMatch = new JSONObject(); // {}
						jsonobj_relatedMatch.put("relatedMatch", rm);
						jsonarray_relatedMatch.add(jsonobj_relatedMatch);
					}
					// set closeMatch
					HashSet cms = labelobject.get(name).getCloseMatch();
					for (Object cm : cms) {
						JSONObject jsonobj_closeMatch = new JSONObject(); // {}
						jsonobj_closeMatch.put("closeMatch", cm);
						jsonarray_closeMatch.add(jsonobj_closeMatch);
					}
					// set exactMatch
					HashSet ems = labelobject.get(name).getExactMatch();
					for (Object em : ems) {
						JSONObject jsonobj_exactMatch = new JSONObject(); // {}
						jsonobj_exactMatch.put("exactMatch", em);
						jsonarray_exactMatch.add(jsonobj_exactMatch);
					}
					// set seeAlso
					HashSet ses = labelobject.get(name).getSeeAlso();
					for (Object se : ses) {
						JSONObject jsonobj_seeAlso = new JSONObject(); // {}
						jsonobj_seeAlso.put("seeAlso", se);
						jsonarray_seeAlso.add(jsonobj_seeAlso);
					}
					// set sameAs
					HashSet sas = labelobject.get(name).getSameAs();
					for (Object sa : sas) {
						JSONObject jsonobj_sameAs = new JSONObject(); // {}
						jsonobj_sameAs.put("sameAs", sa);
						jsonarray_sameAs.add(jsonobj_sameAs);
					}
					// set isDefinedBy
					HashSet iss = labelobject.get(name).getIsDefinedBy();
					for (Object is : iss) {
						JSONObject jsonobj_isDefinedBy = new JSONObject(); // {}
						jsonobj_isDefinedBy.put("isDefinedBy", is);
						jsonarray_isDefinedBy.add(jsonobj_isDefinedBy);
					}
					// set single values
					jsonobj_data.put("id", labelobject.get(name).getId());
					jsonobj_data.put("label", labelobject.get(name).getLabel());
					jsonobj_data.put("creator", labelobject.get(name).getCreator());
					jsonobj_data.put("date", labelobject.get(name).getDate());
					jsonobj_data.put("prefLang", labelobject.get(name).getPrefLang());
					// set multiple values
					jsonobj_data.put("concepts", jsonarray_concepts);
					jsonobj_data.put("prefLabels", jsonarray_prefLabels);
					jsonobj_data.put("vocabularyIDs", jsonarray_vocabularies);
					// set multiple optional values
					jsonobj_data.put("altLabels", jsonarray_altLabels);
					jsonobj_data.put("notes", jsonarray_notes);
					jsonobj_data.put("definitions", jsonarray_definitions);
					jsonobj_data.put("broader", jsonarray_broader);
					jsonobj_data.put("narrower", jsonarray_narrower);
					jsonobj_data.put("related", jsonarray_related);
					jsonobj_data.put("broadMatch", jsonarray_broadMatch);
					jsonobj_data.put("narrowMatch", jsonarray_narrowMatch);
					jsonobj_data.put("relatedMatch", jsonarray_relatedMatch);
					jsonobj_data.put("exactMatch", jsonarray_exactMatch);
					jsonobj_data.put("closeMatch", jsonarray_closeMatch);
					jsonobj_data.put("seeAlso", jsonarray_seeAlso);
					jsonobj_data.put("sameAs", jsonarray_sameAs);
					jsonobj_data.put("isDefinedBy", jsonarray_isDefinedBy);
					// set data
					jsonarray_data.add(jsonobj_data);
				}
				jsonobj_query.put("label", label);
				jsonobj_query.put("data", jsonarray_data);
				// pretty print JSON output (Gson)
				Gson gson = new GsonBuilder().setPrettyPrinting().create();
				out.print(gson.toJson(jsonobj_query));
				response.setStatus(200);
			} else {
				throw new SesameSparqlException();
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
		} catch (ResourceNotAvailableException ex) {
			Logger.getLogger(getLabel.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.ConfigException ex) {
			Logger.getLogger(getLabel.class.getName()).log(Level.SEVERE, null, ex);
		} catch (de.i3mainz.ls.rdfutils.exceptions.SesameSparqlException ex) {
			Logger.getLogger(getLabel.class.getName()).log(Level.SEVERE, null, ex);
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
