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
	<h1>Query SKOS Concepts from the Web</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

//content
echo "
</div>
<div id='main'>
<br>

</div>
</div>";

//footer
echo "
</body>
</html>";


?>

<?php

function js_str($s)
{
    return '"' . addcslashes($s, "\0..\37\"\\") . '"';
}
function js_array($array)
{
    $temp = array_map('js_str', $array);
    return '[' . implode(',', $temp) . ']';
}

?>

<?php
	
	include('config.php');
	set_time_limit(3600000);
	$url = "http://labelingsystem:lSIi3EmG14@".$ConfigHOST."/sesame/SPARQLconcepts?format=csv&query=";
	//$url = "http://143.93.114.137/sesame/SPARQLconcepts?format=xml&query=";
	$query = "SELECT * WHERE { ?s <http://".$ConfigVocabHOST."/vocab#BaseURI> ?o . }";
	$query = urlencode($query);

	$c = curl_init($url.$query);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt( $c, CURLOPT_ENCODING, "UTF-8" );  
	$response = curl_exec($c);
	if (curl_error($c)) die(curl_error($c));
	curl_close($c);
	
	$tz = explode("\n",$response);
	$i = 0;
	$arr;
	foreach($tz as $v){
		$t = explode(",",$v);
		foreach($t as $vv){
			if ($vv == "" or $vv == "s" or $vv == "o") {
			} else {
				$arr[$i] = $vv;
				//echo $vv."<br>";
				$i = $i + 1;
			}
		}
	}
	
// functions to convert php array to js array


?>

<script src="http://code.jquery.com/jquery-latest.js"></script>

<center>
<h2><i>
Here you can explore stored thesauri of different providers.
<br>
You can have a look at the concepts where a substring is included.
<br>
Therefore the prefLabels are browsed via a SPARQL endpoint.
<br>
Be careful because of the licenses of the different providers.
</i></h2>
<br><br>
<form method="post" action="concepts_sparql.php">
   <b>Searchstring (substring in prefLabel): </b><input type="text" name="searchstr" id="searchstr" size="50"><br><br>
   <b>Choose SPARQL Endpoint: </b><select id='test' onChange='setHiddenURL()'><br>
   <input type="text" name="sparql_url" id="sparql_url" hidden>
   <input type="text" name="sparql_query" id="sparql_query" hidden>
   <br><br><input type="submit" name="submit" value="query thesaurus">
</form>
</center>

<script>

function setHiddenURL() {
	var split = document.getElementById('test').value.split(": ");
	//console.log(split[1]);
	document.getElementById('sparql_url').value = split[1];
	document.getElementById('sparql_query').value = 'PREFIX skos: <http://www.w3.org/2004/02/skos/core#> SELECT ?s ?label ?scheme WHERE { ?s a skos:Concept . ?s skos:prefLabel ?label . OPTIONAL { ?s skos:inScheme ?scheme . } FILTER(regex(?label, "?searchstring", "i")) . } ORDER BY ASC(?label)';
}

function raiseEvent (eventType, elementID)
{ 
    var o = document.getElementById(elementID); 
    if (document.createEvent) { 
        var evt = document.createEvent("Events"); 
        evt.initEvent(eventType, true, true); 
        o.dispatchEvent(evt); 
    } 
    else if (document.createEventObject) 
    {
        var evt = document.createEventObject(); 
        o.fireEvent('on' + eventType, evt); 
    } 
    o = null;
} 

var labels = <?php echo js_array($arr); ?>;

var k = 0;
var text = "";
for (var i=0; i<labels.length; i++) {

	if (k==1) {
		//console.log(labels[i]);
		text += labels[i];
		var x = document.getElementById("test");
		var option = document.createElement("option");
		option.text = text;
		x.add(option);
		text = "";
		k = 0;
	} else {
		k = 1;
		var split = labels[i].split("#");
		text += split[split.length-1] + ": ";
	}
}

setHiddenURL();

</script>

<?php require_once("models/footerline.php"); ?>