<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>

<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//header
echo "
<body>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Input Concept to Concept Repository</h1>
	<h2>Ontologist Function</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

//content
echo "
</div>
<div id='maingrey'>
<br>
<info2>
<p align='center'>
<b>Notes</b>
<br><br>
Upload a valid RDF/XML file by URL or local file
<br>
</i>
</p>
</info2>
<br>
<center>
<b>Upload SKOS Concept-Scheme from URL: </b><input id='concepturl' type='text' size='50' maxlength='200'>
<input type='button' value='Upload Concept' id='uploadconcept' onclick='sendURL(document.getElementById(\"concepturl\").value)'>
<br><br>
<form id='data'>
	<b>Upload SKOS Concept-Scheme from local File: </b><input type='file' name='fileName'>
	<input type='submit' value='Upload'>
</form>
<br>
<br>
<div><span id='load'></span></div>
</center>

</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>


<script>var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');</script>
<script>

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
});

function sendURL(query) {
	$('#load').html('<b>Loading...</b> <img src="loading.gif" height="40">');
    $.ajax({
        beforeSend: function(req) {
			req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
		},
		type: 'POST',
        url: Config.InputConcept,
        data: {url: query},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
            $('#load').html('<b>'+errorThrown+'</b>');
        },
        success: function(output) {
            $('#load').html('<b>'+query+' loaded</b>');
        }
    });
}

$("form#data").submit(function(event) {
	$('#load').html('<b>Loading...</b> <img src="loading.gif" height="40">');
	event.preventDefault();
	var formData = new FormData($(this)[0]);
	$.ajax({
		url: Config.InputConcept,
		type: 'POST',
		data: formData,
		async: false,
		cache: false,
		contentType: false,
		processData: false,
		error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
            $('#load').html('<b>'+errorThrown+'</b>');
        },
		success: function(output) {
			$('#load').html('<b>upload ok</b>');
		}
	});
	return false;
});

</script>