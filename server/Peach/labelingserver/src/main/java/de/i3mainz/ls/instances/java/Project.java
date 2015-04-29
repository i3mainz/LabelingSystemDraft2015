package de.i3mainz.ls.instances.java;

import java.util.HashSet;

/**
 * CLASS to describe a project
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 05.02.2015
 */
public class Project {

	private String id = "";
	private String project = "";
	private String label = "";
	private String creator = "";
	private String date = "";
	private HashSet vocabularies = new HashSet();
	private String comment = ""; // optional

	public Project() {
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

	public String getDate() {
		return date;
	}

	public void setDate(String date) {
		this.date = date;
	}

	public HashSet getVocabularies() {
		return vocabularies;
	}

	public void setVocabularies(HashSet vocabularies) {
		this.vocabularies = vocabularies;
	}

	public String getComment() {
		return comment;
	}

	public void setComment(String comment) {
		this.comment = comment;
	}

	public String getProject() {
		return project;
	}

	public void setProject(String project) {
		this.project = project;
	}

}
