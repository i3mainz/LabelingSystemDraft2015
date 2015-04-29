<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/
echo "
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>".$websiteName."</title>
<link href='".$template."' rel=\"stylesheet\" type=\"text/css\" />
<link href=\"https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/themes/redmond/jquery-ui.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"http://tompi.github.io/jeoquery/jeoquery.css\" rel=\"stylesheet\" type=\"text/css\" />
<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
<script src=\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js\" type=\"text/javascript\"></script>
<script src=\"jeoquery.js\" type=\"text/javascript\" ></script>
<script src='gui.js'></script>
<script src=\"config.js\"></script>
<script src=\"utils.js\"></script>
<script>
	var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');
	var initGeonames = function() {
		jeoquery.defaultData.userName = 'tompi';
		$('#location').jeoCityAutoComplete({callback: setCity});
	};
</script>
<style type=\"text/css\">
#location { font-size: 28px; padding: 10px; border: 1px solid #888; margin: 20px 20px; font-family: sans-serif; line-height: 1.6em; width: 600px;}
</style>
</head>";

?>
