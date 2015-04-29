package de.i3mainz.ls.instances.java;

import java.util.HashSet;

/**
 * CLASS to describe an gui
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 10.02.2015
 */
public class GUI {

	private String id = "";
	private String comment = "";
	private String label = "";
	private String creator = "";
	private String menuLang = "";
	private String prefLang = "";
	private HashSet agents = new HashSet(); // optional

	public String getId() {
		return id;
	}

	public void setId(String id) {
		this.id = id;
	}

	public String getComment() {
		return comment;
	}

	public void setComment(String comment) {
		this.comment = comment;
	}

	public HashSet getAgents() {
		return agents;
	}

	public void setAgents(HashSet agents) {
		this.agents = agents;
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

	public String getLang_menu() {
		return menuLang;
	}

	public void setLang_menu(String lang_menu) {
		this.menuLang = lang_menu;
	}

	public String getLang_pref() {
		return prefLang;
	}

	public void setLang_pref(String lang_pref) {
		this.prefLang = lang_pref;
	}

}
