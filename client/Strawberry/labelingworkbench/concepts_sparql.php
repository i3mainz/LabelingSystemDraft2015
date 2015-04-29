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
	<h1>Results of SKOS Concepts query from the Web</h1>
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

<script> var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>'); </script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>

<style type="text/css">
html, body {
	background: #fff;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:0.95em;
	color:#4d4948;
	text-align: justify;
	height: 101%;
}
a {
	color:#4d4948;
	text-decoration:none;
}

a:hover {
	color:#ff0505;
	text-decoration:underline;
}

</style>

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
if(isset($_POST['submit']))
{
	
	$name = $_POST['searchstr'];
	$url = $_POST['sparql_url'];
	$query = $_POST['sparql_query'];
	
	// show only connected labels (to vocab) from the Labeling System
	// and sameAs results
	if (strpos($url,'143.93.114.137') !== false) {
		//$query = 'PREFIX skos: <http://www.w3.org/2004/02/skos/core#> SELECT ?s ?label ?scheme WHERE { ?s a skos:Concept . ?s skos:prefLabel ?label . ?s skos:inScheme ?scheme . FILTER(regex(?label, "?searchstring", "i")) . } ORDER BY ASC(?label)';
		$query = 'PREFIX skos: <http://www.w3.org/2004/02/skos/core#> PREFIX ls: <http://143.93.114.137/vocab#> SELECT ?s ?label ?scheme WHERE { ?v ls:sameAs ?scheme . ?l ls:sameAs ?s . ?l a skos:Concept . ?l skos:prefLabel ?label . ?l skos:inScheme ?v . FILTER(regex(?label, "?searchstring", "i")) . } ORDER BY ASC(?label)';
	}
	
	$query = str_replace("?searchstring",$name,$query);
	echo "searchstring : <b> $name </b><br>";
	echo "url : <b> $url </b><br>";
	if (strpos($url,'143.93.114.137') !== false) {
		echo "query:<br><b>PREFIX skos: &lt;http://www.w3.org/2004/02/skos/core#&gt; <br>PREFIX ls: &lt;http://143.93.114.137/vocab#&gt; <br>SELECT ?s ?label ?scheme WHERE {<br>?v ls:sameAs ?scheme .<br>?l ls:sameAs ?s .<br>?l a skos:Concept .<br>?l skos:prefLabel ?label .<br>?l skos:inScheme ?v .<br>FILTER(regex(?label, \"".$name."\", \"i\")) .<br>} ORDER BY ASC(?label)</b><br>";
	} else {
		echo "query:<br><b>PREFIX skos: &lt;http://www.w3.org/2004/02/skos/core#&gt; <br>SELECT ?s ?label ?scheme WHERE {<br>?s a skos:Concept .<br>?s skos:prefLabel ?label .<br>OPTIONAL { ?s skos:inScheme ?scheme . }<br>FILTER(regex(?label, \"".$name."\", \"i\")) .<br>} ORDER BY ASC(?label)</b><br>";
	}
	echo '<br><a href="http://'.$ConfigHOST.'/client/concepts.php">[ back to query ]</a><br><br>';
	echo "<br><hr width='70%'><br>";

	set_time_limit(3600000);

	//$searchstring = $name;
	//$sparqlurl = "http://data.culture.fr/thesaurus/sparql?query=";
	//$sparql = 'PREFIX skos: <http://www.w3.org/2004/02/skos/core#> SELECT ?s ?label ?scheme WHERE { ?s a skos:Concept . ?s skos:prefLabel ?label . ?s skos:inScheme ?scheme . FILTER(regex(?label, "'.$searchstring.'", "i")) . }';
	
	$query = urlencode($query);

	$c = curl_init($url.$query);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt( $c, CURLOPT_ENCODING, "UTF-8" );  
	$response = curl_exec($c);
	if (curl_error($c)) die(curl_error($c));
	curl_close($c);

	$xml = simplexml_load_string($response);

	//$xml->addAttribute('xmlns:xml', 'http://www.w3.org/XML/1998/namespace');

	foreach ($xml as $binding) {
		$count = $binding->count();
	}
	
	$table = "";
	
	//echo "<table border='1'>";
	$table = $table."<table border='1' align='center'>";
	
	$i = 0;
	$arr;
	foreach ($xml->results->result as $result) {
		//echo "<tr>";
		$table = $table."<tr>";
		foreach ($result->binding as $binding) {
			switch((string) $binding['name']) { // Verwende Attribute als Element-Indizes
			case 's':
				//echo $binding->uri."<br>";
				//echo "<td>".$binding->uri."</td>";
				$table = $table."<td><a href=\"".$binding->uri."\" target=\"_blank\">".$binding->uri."</a></td>";
				$arrS[$i] = $binding->uri;
				break;
			case 'scheme':
				//echo $binding->uri."<br>";
				//echo "<td>".$binding->uri."</td>";
				//$table = $table."<td>".$binding->uri."</td>";
				$arrScheme[$i] = $binding->uri;
				break;
			case 'label':
				//echo utf8_decode($binding->literal)."<br>";
				//echo "<td>".$binding->literal."</td>";
				//$table = $table."<td>".utf8_decode($binding->literal)."</td>";
				$table = $table."<td>".$binding->literal."</td>";
				$table = $table."<td>"."<a href='javaScript:sel(\"$i\");'>select</a>"."</td>";
				//$arr[$i] = utf8_decode($binding->literal);
				$arr[$i] = $binding->literal;
				$i = $i+1;
				break;
			}
		}
		//echo "</tr>";
		$table = $table."</tr>";
	}

	
	//echo "</table>";
	$table = $table."</table>";
	$table = $table."<br>";
	
}
	
?>
	
<?php

if(isset($_POST['submit'])) {

	echo "
	<body onload='IO.sendSPARQLMyLabelsList(Config.SPARQL,TS.mylabels);'>
	<table border=0 align='center'>
		<tr>
			<td><b>Concepts by SRARQL:</b></td>
			<td><b>My Labels: (get info on dblclick)</b></td>
		</tr>
		<tr>
			<td>
				<table border=0>
					<tr>
						<td><b>Label: </b></td>
						<td><select id='test' onchange='info(document.getElementById(\"test\").value, document.getElementById(\"test\").selectedIndex);'></td>
						<td> </td>
					</tr>
					<tr>
						<td><b>URI: </b></td>
						<td><input id='test2' type='text' size='100' maxlength='100' disabled /></td>
						<td><span id='urispan'></span></td>
					</tr>
					<tr>
						<td><b>Scheme: </b></td>
						<td><input id='test3' type='text' size='100' maxlength='100' disabled /></td>
						<td><span id='schemaspan'></span></td>
					</tr>
				</table>
			</td>
			<td><select id='labellist' size='5' style='width: 350px;' ondblclick='IO.sendSPARQL_LabelMetadata(Config.SPARQL,TS.labelmetadata);'></select></td>
		</tr>
	</table>
	</body>";
	
	echo "
	<center>
	<br>
	<h1>Connect Label and Concept</h1>
        <span id='connect_lc'><input type='button' value='Connect Label with Concept' id='sendvocabulary' onclick='IO.getinputconnectLabelConceptTriple();'></span>
		 skos:
        <select id='relation'>
			<option value='related'>related</option>
            <option value='broader'>broader</option>
            <option value='narrower'>narrower</option>
			<option value='closeMatch'>closeMatch</option>
			<option value='exactMatch'>exactMatch</option>
			<option value='relatedMatch'>relatedMatch</option>
			<option value='narrowMatch'>narrowMatch</option>
			<option value='broadMatch'>broadMatch</option>
        </select>
	</center><br>";
	
	echo $table;

}

?>

<script>

var labels = <?php echo js_array($arr); ?>;
var s = <?php echo js_array($arrS); ?>;
var scheme = <?php echo js_array($arrScheme); ?>;

for (var i=0; i<s.length; i++) {
	//console.log(s[i]);
}

for (var i=0; i<labels.length; i++){
	var x = document.getElementById("test");
	var option = document.createElement("option");
	option.text = labels[i];
	x.add(option);
}

info(document.getElementById('test').value, document.getElementById("test").selectedIndex);

///////////////
// functions //
///////////////

function sel(no) {
	document.getElementById('test').selectedIndex = no;
	info(document.getElementById('test').value, document.getElementById("test").selectedIndex);
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

function info(label, selIndex) {
	
	var r = -1;

	r = selIndex;
	
	//for (var i=0; i<labels.length; i++) {
		//var t = labels[i];	
		////if (t.indexOf(label) != -1) {
		//if (t == label) {
			//r = i;
		//}
	//}
	
	document.getElementById('test2').value = s[r];
	document.getElementById('test3').value = scheme[r];
	document.getElementById('urispan').innerHTML = "<a href=\""+s[r]+"\" target=\"_blank\">Link</a>";
	document.getElementById('schemaspan').innerHTML = "<a href=\""+scheme[r]+"\" target=\"_blank\">Link</a>";
	
}

function mask(text) {
    var replacer = new RegExp(" ", "g");
    text = text.replace(replacer, "%20");
    var replacer2 = new RegExp(":", "g");
    text = text.replace(replacer2, "%3A");
    var replacer5 = new RegExp("{", "g");
    text = text.replace(replacer5, "%7B");
    var replacer6 = new RegExp("}", "g");
    text = text.replace(replacer6, "%7D");
    var replacer7 = new RegExp("/", "g");
    text = text.replace(replacer7, "%2F");
    var replacer9 = new RegExp("<", "g");
    text = text.replace(replacer9, "%3C");
    var replacer10 = new RegExp(">", "g");
    text = text.replace(replacer10, "%3E");
    var replacer11 = new RegExp("#", "g");
    text = text.replace(replacer11, "%23");
    return text;
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

function setHiddenURL() {
	document.getElementById('sparql_url').value = document.getElementById('test').value;
	//console.log(document.getElementById('sparql_url').value);
}

var IO = {};
var Config = {};
var TS = {};

Config.SPARQL = 'http://143.93.114.137/sesame/SPARQL';
Config.Input = 'http://143.93.114.137/sesame/Input';
Config.Delete = 'http://143.93.114.137/sesame/Delete';

TS.vars = [];
TS.bindings = [];
TS.bindingsP = [];
TS.bindingsO = [];
//TS.mylabels = "SELECT DISTINCT ?s WHERE { ?l a <http://143.93.114.137/vocab#Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?s . ?l <http://purl.org/dc/terms/creator> \"$creator\" . }";
TS.mylabels = "SELECT DISTINCT ?s WHERE { ?l a <http://143.93.114.137/vocab#Label> . ?l <http://www.w3.org/2004/02/skos/core#prefLabel> ?s . ?l <http://purl.org/dc/terms/creator> \"$creator\" . } ORDER BY ASC(?s)";
TS.labelconcepts = "SELECT DISTINCT ?o WHERE { <$label> ?p ?o . FILTER (?p = <http://www.w3.org/2004/02/skos/core#related> || ?p = <http://www.w3.org/2004/02/skos/core#broader> || ?p = <http://www.w3.org/2004/02/skos/core#narrower>) . }";
TS.labelmetadata = "SELECT DISTINCT ?s ?verb ?value WHERE { ?s ?verb ?value . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <http://143.93.114.137/vocab#Label> . }";
TS.uriLab = "SELECT DISTINCT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> $label . ?s a <http://143.93.114.137/vocab#Label> . }";

//////////////
// Metadata //
//////////////

IO.sendSPARQLMyLabelsList = function(url, query, callback, info) {
    
    $('#deletelabel').show();
	$('#mylabelfunctions').show();
        
    query = query.replace('$creator',user);
                        
    query = escape(query);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearLabelList();
            
			var bindings = output.results.bindings; 
			for (var i=0; i<bindings.length; i++) {       
				var t = bindings[i];
				var val = "";
				var lang = "";
				for(var key in t.s) {
						
						if (key == "value") {
							val = t.s.value;
							//console.log(val);
						}
						if (key == "xml:lang") {
							lang = t.s['xml:lang'];
							//console.log(lang);
						}
						
				}
				
				var x = document.getElementById("labellist");
				var option = document.createElement("option");
				option.text = val + "@" + lang;
				x.add(option);
				
				val = "";
				lang = "";
				
			}
        }
    });
}

IO.sendSPARQL_LabelMetadata = function(url, query, callback, info) {
    
    var tmp = document.getElementById('labellist').value.replace("@","__");
	var tmp2 = tmp.split("__");
	var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
	query = query.replace("$label",tmp3);
	query = escape(query);
    query = mask(query);
	
	//query = query.replace("$label",document.getElementById('labellist').value);
    //query = mask(query);
        
    if (query != "SELECT%20?verb%20?value%20WHERE%20%7B%20%3C%3E%20?verb%20?value%20%7D") {
	
		$.ajax({
			type: 'GET',
			url: url,
			data: {query: query, format: 'json'},
			error: function(jqXHR, textStatus, errorThrown) {
				alert(errorThrown);
			},
			success: function(output) {
				
				var outputObj;
				
				if (typeof(output)=="string") {
					outputObj = JSON.parse(output);
				} else {
					outputObj = output;
				}
				
				TS.bindings.length = 0;
				TS.bindingsP.length = 0;
				TS.bindingsO.length = 0;
			
				var list = output.results.bindings;
				for (var i=0; i<list.length; i++) {
					TS.bindings.push(list[i].s.value);
					TS.bindingsP.push(list[i].verb.value);
					TS.bindingsO.push(list[i].value.value);
				}
			
				var html_str = "";
				html_str += "<h1>Metadata - "+TS.bindings[0]+"</h1>";
				html_str += "<br>";
				
				html_str += "<table border='1' width='75%'>";
				html_str += "<colgroup>";
				html_str += "<col width='50%'>";
				html_str += "<col width='50%'>";
				html_str += "</colgroup>";

				for (var i=0; i<TS.bindingsP.length; i++){

						html_str += "<tr>";
						
						if (TS.bindingsP[i].indexOf("broader")!=-1 || TS.bindingsP[i].indexOf("narrower")!=-1 || TS.bindingsP[i].indexOf("related")!=-1) {
							html_str += "<td>";
							html_str += "<b>" + TS.bindingsP[i] + "</b>";
							html_str += "</td>";
							html_str += "<td>";
							html_str += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQLConcepts,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
							html_str += "</td>";
						} else {
							html_str += "<td>";
							html_str += "<b>" + TS.bindingsP[i] + "</b>";
							html_str += "</td>";
							html_str += "<td>";
							html_str += "<i>" + TS.bindingsO[i] + "</i>"
							html_str += "</td>";
						}

						html_str += "</tr>";

				}

				html_str += "</table>";
				
				var w = window.open();
				var html = $("#toNewWindow").html();

				$(w.document.body).html(html_str);
				
			}
			
			
		});
	}
}


/////////////
// updates //
/////////////

// IO.getinputconnectProjectVocabularyTriple --> IO.getURIVoc --> IO.getURIPro --> IO.sendUpdateCallback --> IO.sendSPARQL_VocabularyMetadata

IO.getinputconnectLabelConceptTriple = function() {
	
	IO.getURILab(Config.SPARQL, TS.uriLab, "connect");
    
}

IO.getinputdisconnectLabelConceptTriple = function() {

	IO.getURILab(Config.SPARQL, TS.uriLab, "disconnect");
	
}

IO.getURILab = function(url, query, mode) {
	
	input = escape(query);
	input = mask(query);
	
	var tmp = document.getElementById('labellist').value.replace("@","__");
	var tmp2 = tmp.split("__");
	var tmp3 = "\"" + tmp2[0] + "\"" + "@" + tmp2[1];
	query = query.replace("$label",tmp3);
	query = escape(query);
    query = mask(query);
        
    $.ajax({
        beforeSend: function(req) {
            req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
	},
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
			
			var laburi = output.results.bindings[0].s.value; 
			
			var concepturi = document.getElementById('test2').value;
			var p = document.getElementById('labellist').value;
			var relation = document.getElementById('relation').value;
			
			//console.log(laburi);
			//console.log(concepturi);
			//console.log(p);
			
			if (mode == "connect") {
			
				if (concepturi!=null && p!=null && concepturi!="" && p!="") {
					
					var tri = "{ ";
					tri += "<"+laburi+"> ";
					tri += "<http://www.w3.org/2004/02/skos/core#"+relation+"> ";
					tri += "<"+concepturi+">";
					tri += ". ";
					
					// if broader or narrower create the transitive ones
					if (relation == "narrower" && concepturi.indexOf("143.93.114.137") != -1) {
						tri += "<"+concepturi+"> ";
						tri += "<http://www.w3.org/2004/02/skos/core#"+"broader"+"> ";
						tri += "<"+laburi+">";
						tri += ". }";
					} else if (relation == "broader" && concepturi.indexOf("143.93.114.137") != -1) {
						tri += "<"+concepturi+"> ";
						tri += "<http://www.w3.org/2004/02/skos/core#"+"narrower"+"> ";
						tri += "<"+laburi+">";
						tri += ". }";
					} else {
						tri += " }";
					}
					
					IO.sendUpdateCallback(Config.Input,tri,alert);
				} else {
					alert("no content!");
				}
			
			} 
			
			if (mode == "disconnect") {
			
				if (concepturi!=null && p!=null && concepturi!="" && p!="") {
					var tri = "{ ";
					tri += "<"+laburi+"> ";
					tri += "<http://www.w3.org/2004/02/skos/core#"+relation+"> ";
					tri += "<"+concepturi+">";
					tri += ". }";
					IO.sendUpdateCallback(Config.Delete,tri,alert);
				} else {
					alert("no content!");
				}
			
			} 

        }
    });
}


IO.sendUpdateCallback = function(url, up, callback, info) {
    
    up = escape(up);
    up = mask(up);
        
    $.ajax({
        beforeSend: function(req) {
            req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
	},
        type: 'POST',
        url: url,
        data: {update: up},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            callback("action successfully!");
        }
    });
}




</script>
