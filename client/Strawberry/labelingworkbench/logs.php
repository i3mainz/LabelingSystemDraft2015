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
	<h1>Logs</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

//content
echo "
</div>
<div id='maingrey'>
<br>

<center>
<span id='info'></span>
</center>
<br>

</div>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>


<script>var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');</script>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>


<script>

var IO = {};
var TS = {};
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];

//////////////
// Metadata //
//////////////

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	//
	var query = SPARQL.logs;
	query = encodeURIComponent(query);
		
	$.ajax({
		type: 'GET',
		url: Config.SPARQL,
		data: {query: query, format: 'json'},
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
			
			// set help array to null
			TS.vars.length = 0;
			TS.result.length = 0;
			TS.output.length = 0;
			TS.output2.length = 0;
			
			// html output
			html_str = "";
			html_str += "<h1>100 last logs</h1>";
			html_str += "<br>";
			html_str += "<table border='1' width='75%'>";
			html_str += "<colgroup>";
			html_str += "<col width='50%'>";
			html_str += "<col width='50%'>";
			html_str += "</colgroup>";
			html_str += "<tr>";
			
			// read JSON head-->vars objects to array (e.g. spo)
			var varsj = output.head.vars;
			
			for (var i=0; i<varsj.length; i++) {
				TS.vars.push(varsj[i]);
			}
			
			// html output
			//for (var i=0; i<TS.vars.length; i++){
				//html_str += "<th style='background:#dddddd'>"+TS.vars[i]+"</th>";
			//}
			
			// html output
			for (var i=0; i<TS.vars.length; i++){
				if (TS.vars[i].indexOf("s") != -1) {
				} else {
					html_str += "<th style='background:#dddddd'>"+TS.vars[i]+"</th>";
				}
			}
			
			// html output
			html_str += "</tr>";
			//html_str += "</tr>";
			
			// read JSON rasults-->bindings[i] objects to array with key (e.g. pppsssooo)
			var bindings = output.results.bindings; 
			for (var i=0; i<bindings.length; i++) {       
				var t = bindings[i];
				for(var key in t) {
					if (key == "value") {
						TS.result.push(key + "__" + t[key].value + language);
					} else {
						TS.result.push(key + "__" + t[key].value);
					}
				}
			}
			
			// sort sparql output like the vars e.g. ssspppooo
			var k = 0;
			for (var i=0; i<TS.vars.length; i++) {
				for (var j=0; j<TS.result.length; j++) {
					if (TS.result[j].indexOf(TS.vars[i]+"__") != -1){
						var split = TS.result[j].split("__");
						TS.output[k] = k + "__" + split[1];
						k++;
					}
				}
			}
		
			// sort output like the vars triples e.g. spospospo
			var k = -1;
			var l = (TS.output.length)/TS.vars.length;
			for (var i=0; i<l; i++) {
				if (TS.vars.length==1){
					TS.output2[k+1] = TS.output[i];
					k++;
				} else {
					for (var j=0; j<TS.vars.length; j++) {
						var tmp1 = k+1;
						var tmp2 = i+(j*l);
						TS.output2[tmp1] = TS.output[tmp2];
						k++;
					}
				}
			}
		
			// html output
			html_str += "<tr>";
			var identifier = "";
			for (var i=0; i<TS.output2.length; i++) {
				var split = TS.output2[i].split("__");
				if (split[1].indexOf("project#") != -1) {
					identifier = split[1].replace("http://143.93.114.137/project#","");
					//console.log(identifier);
				} else {
					html_str += "<td align=\"center\">"+split[1].replace("http://143.93.114.137/log#","")+"</td>";
				}
				if ((i+1)%TS.vars.length==0) {
					html_str += "</tr>";
					html_str += "<tr>";
				}
			}
			
			// html output
			html_str += "</tr>";
			html_str += "</table>";

			document.getElementById("info").innerHTML=html_str;
			
		}	
	});
});

</script>