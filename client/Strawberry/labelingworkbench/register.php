<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Prevent the user visiting the logged in page if he/she is already logged in
if(isUserLoggedIn()) { header("Location: account.php"); die(); }

//Forms posted
if(!empty($_POST))
{
	$errors = array();
	$email = trim($_POST["email"]);
	$username = trim($_POST["username"]);
	//$displayname = trim($_POST["displayname"]);
	$displayname = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	$confirm_pass = trim($_POST["passwordc"]);
	$captcha = md5($_POST["captcha"]);
	// new
	$firstname = trim($_POST["firstname"]);
	$lastname = trim($_POST["lastname"]);
	$title = trim($_POST["title"]);
	
	if ($captcha != $_SESSION['captcha'])
	{
		$errors[] = lang("CAPTCHA_FAIL");
	}
	if(minMaxRange(5,20,$username))
	{
		$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,20));
	}
	if(!ctype_alnum($username)){
		$errors[] = lang("ACCOUNT_USER_INVALID_CHARACTERS");
	}
	if(minMaxRange(5,20,$displayname))
	{
		$errors[] = lang("ACCOUNT_DISPLAY_CHAR_LIMIT",array(5,20));
	}
	if(!ctype_alnum($displayname)){
		$errors[] = lang("ACCOUNT_DISPLAY_INVALID_CHARACTERS");
	}
	if(minMaxRange(5,20,$password) && minMaxRange(5,20,$confirm_pass))
	{
		$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(5,20));
	}
	else if($password != $confirm_pass)
	{
		$errors[] = lang("ACCOUNT_PASS_MISMATCH");
	}
	if(!isValidEmail($email))
	{
		$errors[] = lang("ACCOUNT_INVALID_EMAIL");
	}
	//End data validation
	if(count($errors) == 0)
	{	
		//Construct a user object
		$user = new User($username,$displayname,$password,$email);
		
		//Checking this flag tells us whether there were any errors such as possible data duplication occured
		if(!$user->status)
		{
			if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
			if($user->displayname_taken) $errors[] = lang("ACCOUNT_DISPLAYNAME_IN_USE",array($displayname));
			if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));		
		}
		else
		{
			//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)
			if(!$user->userCakeAddUser())
			{
				if($user->mail_failure) $errors[] = lang("MAIL_ERROR");
				if($user->sql_failure)  $errors[] = lang("SQL_ERROR");
			}
		}
	}
	if(count($errors) == 0) {
		$sendToTriplestore = true;
		$successes[] = $user->success;
	}
}

require_once("models/header.php");

//header
echo "
<body>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Register</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

echo "
</div>

<div id='main'>";

echo resultBlock($errors,$successes);

echo "

<info>
<ul>
<li>Your loginname must be between 5 and 20 characters in length</li>
<li>Your username must be between 5 and 20 characters in length</li>
<li>Your username name can only include alpha-numeric characters</li>
<li>Your password must be between 5 and 20 characters in length</li>
</ul>
</info>
<br>

<div id='regbox'>
<form name='newUser' action='".$_SERVER['PHP_SELF']."' method='post'>

<p>
<label>Login Name:</label>
<input type='text' name='username' />
</p>
<!--<p>
<label>User Name:</label>
<input type='text' name='displayname' />
&nbsp;<i>e.g. same as login name</i>
</p>-->
<p>
<label>Title:</label>
<select name='title'><option value='Mr.'>Mr.</option><option value='Mrs.'>Mrs.</option><option value='Ms.'>Ms.</option><option value='Dr.'>Dr.</option><option value='Prof.'>Prof.</option></select>
</p>
<p>
<label>First Name:</label>
<input type='text' name='firstname' />
</p>
<p>
<label>Last Name:</label>
<input type='text' name='lastname' />
</p>
<p>
<label>Email:</label>
<input type='text' name='email' />
</p>
<p>
<label>Password:</label>
<input type='password' name='password' />
</p>
<p>
<label>Confirm Pwd.:</label>
<input type='password' name='passwordc' />
</p>
<p>
<label>Security Code:</label>
<img src='models/captcha.php'>
</p>
<label>Enter Code:</label>
<input name='captcha' type='text'>
</p>
<label>&nbsp;<br>
<input type='submit' value='Register'/>
</p>

</form>
</div>

</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

if ($sendToTriplestore==true) {
	echo("<script>var user = '".$username."'; var email = '".$email."'; var firstname = '".$firstname."'; var lastname = '".$lastname."'; var title = '".$title."';</script>");
} else {
	echo("<script>var user = null; var email = null; var firstname = null; var lastname = null; var title = title;</script>");
}

?>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>
<script>
	if (user==null || email==null || firstname==null || lastname==null || title==null) {
	} else {
		console.info(user);
		console.info(email);
		console.info(title);
		console.info(firstname);
		console.info(lastname);
		var agent = "";
		// create agent in triplestore
		// ls:Agent
		agent += Config.Instance("agent",user,true);
		agent += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
		agent += "<http://xmlns.com/foaf/0.1/Person>" ;
		agent += ". ";
		// foaf:accountName
		agent += Config.Instance("agent",user,true);
		agent += "<http://xmlns.com/foaf/0.1/accountName> ";
		agent += "\""+user+"\" ";
		agent += ". ";
		// foaf:title
		agent += Config.Instance("agent",user,true);
		agent += "<http://xmlns.com/foaf/0.1/title> ";
		agent += "\""+title+"\" ";
		agent += ". ";
		// foaf:firstName
		agent += Config.Instance("agent",user,true);
		agent += "<http://xmlns.com/foaf/0.1/firstName> ";
		agent += "\""+firstname+"\" ";
		agent += ". ";
		// foaf:lastName
		agent += Config.Instance("agent",user,true);
		agent += "<http://xmlns.com/foaf/0.1/lastName> ";
		agent += "\""+lastname+"\" ";
		agent += ". ";
		// foaf:mbox
		agent += Config.Instance("agent",user,true);
		agent += "<http://xmlns.com/foaf/0.1/mbox> ";
		agent += "<mailto:"+email+"> ";
		agent += ". ";
		// ls:sameAs
		agent += Config.Instance("agent",user,true);
		agent += Config.Ontology("sameAs",true);
		agent += "<"+Config.Rest_AGENTS+user+"> ";
		agent += ". ";
		// connect with default gui
		// ls:hasGUI
		agent += Config.Instance("agent",user,true);
		agent += Config.Ontology("hasGUI",true);
		agent += Config.Instance("gui","5436e10616f840859e29c0ab0876114c",true);
		agent += ". ";
		// ls:isGUIfrom
		agent += Config.Instance("gui","5436e10616f840859e29c0ab0876114c",true);
		agent += Config.Ontology("isGUIof",true);
		agent += Config.Instance("agent",user,true);
		agent += ". ";
		var update = "";
		update = SPARQLUPDATE.insertAgentByIdentifier;
		update = update.replace("$data",agent);
		console.info(update);
		update = encodeURIComponent(update);
		$.ajax({
			type: 'POST',
			url: Config.Update,
			data: {update: update},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			success: function(output) {
				try {
					output = JSON.parse(output);
				} catch (e) {
					console.log(e);
				} finally {
				}
			}
		});
	}
</script>