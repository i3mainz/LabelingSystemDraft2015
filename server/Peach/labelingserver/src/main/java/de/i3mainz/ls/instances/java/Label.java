package de.i3mainz.ls.instances.java;

import java.util.HashSet;

/**
 * CLASS to describe a label
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 05.02.2015
 */
public class Label {

	private String id = "";
	private String label = "";
	private HashSet prefLabels = new HashSet();
	private String creator = "";
	private String date = "";
	private String prefLang = "";
	private HashSet vocabularies = new HashSet();
	private HashSet concepts = new HashSet();
	private HashSet altLabels = new HashSet();// optional
	private HashSet notes = new HashSet(); // optional
	private HashSet definitions = new HashSet(); // optional
	private HashSet broader = new HashSet(); // optional
	private HashSet narrower = new HashSet(); // optional
	private HashSet related = new HashSet(); // optional
	private HashSet broadMatch = new HashSet(); // optional
	private HashSet narrowMatch = new HashSet(); // optional
	private HashSet relatedMatch = new HashSet(); // optional
	private HashSet exactMatch = new HashSet(); // optional
	private HashSet closeMatch = new HashSet(); // optional
	private HashSet seeAlso = new HashSet(); // optional
	private HashSet isDefinedBy = new HashSet(); // optional
	private HashSet sameAs = new HashSet(); // optional

	public Label() {
	}

	public String getDate() {
		return date;
	}

	public void setDate(String date) {
		this.date = date;
	}

	public HashSet getNotes() {
		return notes;
	}

	public void setNotes(HashSet notes) {
		this.notes = notes;
	}

	public HashSet getDefinitions() {
		return definitions;
	}

	public void setDefinitions(HashSet definitions) {
		this.definitions = definitions;
	}

	public HashSet getVocabularies() {
		return vocabularies;
	}

	public void setVocabularies(HashSet vocabularies) {
		this.vocabularies = vocabularies;
	}

	public HashSet getBroader() {
		return broader;
	}

	public void setBroader(HashSet broader) {
		this.broader = broader;
	}

	public HashSet getNarrower() {
		return narrower;
	}

	public void setNarrower(HashSet narrower) {
		this.narrower = narrower;
	}

	public HashSet getRelated() {
		return related;
	}

	public void setRelated(HashSet related) {
		this.related = related;
	}

	public HashSet getBroadMatch() {
		return broadMatch;
	}

	public void setBroadMatch(HashSet broadMatch) {
		this.broadMatch = broadMatch;
	}

	public HashSet getNarrowMatch() {
		return narrowMatch;
	}

	public void setNarrowMatch(HashSet narrowMatch) {
		this.narrowMatch = narrowMatch;
	}

	public HashSet getRelatedMatch() {
		return relatedMatch;
	}

	public void setRelatedMatch(HashSet relatedMatch) {
		this.relatedMatch = relatedMatch;
	}

	public HashSet getExactMatch() {
		return exactMatch;
	}

	public void setExactMatch(HashSet exactMatch) {
		this.exactMatch = exactMatch;
	}

	public HashSet getCloseMatch() {
		return closeMatch;
	}

	public void setCloseMatch(HashSet closeMatch) {
		this.closeMatch = closeMatch;
	}

	public HashSet getSeeAlso() {
		return seeAlso;
	}

	public void setSeeAlso(HashSet seeAlso) {
		this.seeAlso = seeAlso;
	}

	public HashSet getIsDefinedBy() {
		return isDefinedBy;
	}

	public void setIsDefinedBy(HashSet isDefinedBy) {
		this.isDefinedBy = isDefinedBy;
	}

	public HashSet getSameAs() {
		return sameAs;
	}

	public void setSameAs(HashSet sameAs) {
		this.sameAs = sameAs;
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getLabel() {
		return label;
	}

	public void setLabel(String label) {
		this.label = label;
	}

	public String getCreator() {
		return creator;
	}

	public void setCreator(String creator) {
		this.creator = creator;
	}

	public HashSet getPrefLabels() {
		return prefLabels;
	}

	public void setPrefLabels(HashSet prefLabels) {
		this.prefLabels = prefLabels;
	}

	public HashSet getAltLabels() {
		return altLabels;
	}

	public void setAltLabels(HashSet altLabels) {
		this.altLabels = altLabels;
	}

	public HashSet getConcepts() {
		return concepts;
	}

	public void setConcepts(HashSet concepts) {
		this.concepts = concepts;
	}

	public String getPrefLang() {
		return prefLang;
	}

	public void setPrefLang(String prefLang) {
		this.prefLang = prefLang;
	}

}
