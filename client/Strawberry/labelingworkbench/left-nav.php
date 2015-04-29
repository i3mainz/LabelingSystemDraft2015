<?php
if (!securePage($_SERVER['PHP_SELF'])){die();}

//Links for logged in user
if(isUserLoggedIn()) {
	
	echo "
	<ul>
	<li><b><term id='t_f_overall'></term></b></li>
	<li><a href='account.php'><term id='t_home'></term></a></li>
	<li><a href='usermetadata.php'><term id='t_user_metadata'></term></a></li>
	<li><a href='userguicreate.php'><term id='t_user_guicreate'></term></a></li>
	<li><a href='usergui.php'><term id='t_user_gui'></term></a></li>
	<li><a href='userguimod.php'><term id='t_user_guimod'></term></a></li>
	<li><a href='user_settings.php'><term id='t_user_settings'></term></a></li>
	<li><a href='documentation.php'><term id='t_documentation'></term></a></li>
	<li><b><a href='logout.php'><term id='t_logout'></term></a></b></li>
	<br>
	<li><i><term id='t_test'></term></i></li>
	<li><a href='logs.php'><term id='t_logs'></term></a></li>
	<li><a href='template.php'><term id='t_template'></term></a></li>
	<li><a href='http://labeling.i3mainz.hs-mainz.de/openrdf-sesame/repositories/labelingsystem/statements?Accept=text/plain'><term id='t_dump'></term></a></li>
	<li><a href='http://labeling.i3mainz.hs-mainz.de/openrdf-sesame/repositories/concepts/statements?Accept=text/plain'><term id='t_dump2'></term></a></li><br>";
	
	$logs = false;
	if ( strcmp($loggedInUser->displayname,"admin") == 0 ) {
		$logs = true;
	}
	if ( $logs == true ) {
	}
	
	//Links for permission level 1 (default user)
	if ($loggedInUser->checkPermission(array(1))){
	echo "
	<ul>
	<li><b><term id='t_f_user'></term></b></li>
	<br>
	<li><i><term id='t_create'></term></i></li>
	<li><a href='myprojects.php'><term id='t_projects'></term></a></li>
	<li><a href='myvocs.php'><term id='t_vocabs'></term></a></li>
	<li><a href='mylabels.php'><term id='t_labels'></term></a></li>
	<li><a href='modifylabel.php'><term id='t_modlabels'></term></a></li>
	<li><a href='labelcsv.php'><term id='t_csvlabels'></term></a></li>
	<br>
	<li><i><term id='t_linking'></term></i></li>
	<li><a href='sparqlendpoint.php'><term id='t_linksparql'></term></a></li>
	<li><a href='concepts2.php'><term id='t_linkresource'></term></a></li>
	<li><a href='labelhierarchy.php'><term id='t_linkhierarchy'></term></a></li>
	<li><a href='connectlabels.php'><term id='t_linkstore'></term></a></li>
	<br>
	<li><i><term id='t_lookup'></term></i></li>
	<li><a href='dbpedia.php'><term id='t_dbpedia'></term></a></li>
	<li><a href='geonames.php'><term id='t_geonames'></term></a></li>
	<li><a href='resourcelookup.php'><term id='t_reslookup'></term></a></li>
	<br>
	<li><i><term id='t_vis'></term></i></li>
	<li><a href='labelautocomplete.php'><term id='t_labelsearch'></term></a></li>
	<li><a href='labelgraph.php'><term id='t_labelgraphs'></term></a></li>
	<li><a href='http://labeling.i3mainz.hs-mainz.de/ProjectTree/tree.jsp?height=700&width=2000&name=".$loggedInUser->displayname."' target='_blank'><term id='t_tree'></term></a></li>
	
	</ul>
	";}
	
	//Links for permission level 2 (default admin)
	if ($loggedInUser->checkPermission(array(2))){
	echo "
	<ul>
	<li><b>Admin Functions</b></li>
	<li><a href='admin_configuration.php'>Admin Configuration</a></li>
	<li><a href='admin_users.php'>Admin Users</a></li>
	<li><a href='admin_permissions.php'>Admin Permissions</a></li>
	<li><a href='admin_pages.php'>Admin Pages</a></li>
	</ul>";
	}
	
	//Links for permission level 3 (default ontologist)
	if ($loggedInUser->checkPermission(array(3))){
	echo "
	<ul>
	<li><b><term id='t_f_ontologist'></term></b></li>
	<li><a href='input_sparqlendpoint.php'><term id='t_inputsparql'></term></a></li>
	<li><a href='inputconcept.php'><term id='t_inputconcept'></term></a></li>
	</ul>";
	}
	
	echo "
	<ul>
	<li><b><term id='t_f_actor'></term></b></li>
	<li><a href='/rest' target='blank'><term id='t_rest'></term></a></li>
	<li><a href='/sparql_label.jsp' target='blank'><term id='t_sparql'></term></a></li>
	<li><a href='http://labeling.i3mainz.hs-mainz.de/ProjectTreePublic/tree.jsp?height=700&width=2000&name=".$loggedInUser->displayname."' target='_blank'>Public Project Tree</a></li>
	</ul>";
} 
//Links for users not logged in
else {
	echo "
	<ul>
	<li><a href='index.php'>Home</a></li>
	<br>
	<li><a href='login.php'>Login</a></li>
	<li><a href='register.php'>Register</a></li>
	<br>
	<li><a href='evaluation.php'>Questions?</a></li>
	<br>
	<li><a href='impressum.php'>Impressum</a></li>
	</ul>";
}

//else {
//	echo "
//	<ul>

//	<li><a href='index.php'>Home</a></li>
//	<li><a href='login.php'>Login</a></li>
//	<li><a href='register.php'>Register</a></li>
//	<li><a href='forgot-password.php'>Forgot Password</a></li>";
//	if ($emailActivation)
//	{
//	echo "<li><a href='resend-activation.php'>Resend Activation Email</a></li>";
//	}
//	echo "</ul>";
//}

?>