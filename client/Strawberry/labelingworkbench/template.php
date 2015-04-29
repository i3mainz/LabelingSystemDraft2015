<?php
	//Secure-Page
	require_once("models/config.php");
	if (!securePage($_SERVER['PHP_SELF'])){die();}
	//HTML-Snippets
	require_once("models/site-templates/header.htm");
	require_once("models/site-templates/headline.htm");
	require_once("models/site-templates/content.htm"); // page content
	require_once("models/site-templates/footerline.htm");
?>

<script>
	// get user name
	var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');
	// init function
	$(document).ready(function() {
		console.info("page loaded");
	});
</script>
