package de.i3mainz.ls.instances.java;

import java.util.HashSet;

/**
 * CLASS to describe a vocabulary
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 05.02.2015
 */
public class Vocabulary {

	private String id = "";
	private String vocabulary = "";
	private String title = "";
	private String creator = "";
	private String date = "";
	private String language = "";
	private HashSet labels = new HashSet();
	private HashSet projetcs = new HashSet();
	private String state = "hidden"; // default:hidden
	private String comment = ""; // optional

	public Vocabulary() {
	}

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public String getCreator() {
		return creator;
	}

	public void setCreator(String creator) {
		this.creator = creator;
	}

	public String getDate() {
		return date;
	}

	public void setDate(String date) {
		this.date = date;
	}

	public String getLanguage() {
		return language;
	}

	public void setLanguage(String language) {
		this.language = language;
	}

	public String getComment() {
		return comment;
	}

	public void setComment(String comment) {
		this.comment = comment;
	}

	public String getState() {
		return state;
	}

	public void setState(String state) {
		this.state = state;
	}

	public HashSet getLabels() {
		return labels;
	}

	public void setLabels(HashSet labels) {
		this.labels = labels;
	}

	public String getVocabulary() {
		return vocabulary;
	}

	public void setVocabulary(String vocabulary) {
		this.vocabulary = vocabulary;
	}

	public HashSet getProjects() {
		return projetcs;
	}

	public void setProjetcs(HashSet projetcs) {
		this.projetcs = projetcs;
	}

}
