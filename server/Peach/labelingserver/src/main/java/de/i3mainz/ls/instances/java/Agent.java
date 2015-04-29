package de.i3mainz.ls.instances.java;

/**
 * CLASS to describe an agent
 *
 * @author Florian Thiery M.Sc.
 * @author i3mainz - Institute for Spatial Information and Surveying Technology
 * @version 10.02.2015
 */
public class Agent {

	private String accountName = "";
	private String title = "";
	private String firstName = "";
	private String lastName = "";
	private String mbox = "";
	private String status = ""; // optional
	private String topic_interest = ""; // optional
	private String workplaceHomepage = ""; // optional
	private String workInfoHomepage = ""; // optional
	private String homepage = ""; // optional
	private String gui = ""; // optional

	public String getAccountName() {
		return accountName;
	}

	public void setAccountName(String accountName) {
		this.accountName = accountName;
	}

	public String getFirstName() {
		return firstName;
	}

	public void setFirstName(String firstName) {
		this.firstName = firstName;
	}

	public String getLastName() {
		return lastName;
	}

	public void setLastName(String lastName) {
		this.lastName = lastName;
	}

	public String getMbox() {
		return mbox;
	}

	public void setMbox(String mbox) {
		this.mbox = mbox;
	}

	public String getStatus() {
		return status;
	}

	public void setStatus(String status) {
		this.status = status;
	}

	public String getTopic_interest() {
		return topic_interest;
	}

	public void setTopic_interest(String topic_interest) {
		this.topic_interest = topic_interest;
	}

	public String getWorkplaceHomepage() {
		return workplaceHomepage;
	}

	public void setWorkplaceHomepage(String workplaceHomepage) {
		this.workplaceHomepage = workplaceHomepage;
	}

	public String getWorkInfoHomepage() {
		return workInfoHomepage;
	}

	public void setWorkInfoHomepage(String workInfoHomepage) {
		this.workInfoHomepage = workInfoHomepage;
	}

	public String getHomepage() {
		return homepage;
	}

	public void setHomepage(String homepage) {
		this.homepage = homepage;
	}

	public String getGui() {
		return gui;
	}

	public void setGui(String gui) {
		this.gui = gui;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

}
