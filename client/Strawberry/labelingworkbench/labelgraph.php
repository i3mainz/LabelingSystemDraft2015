<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//header
echo "
<body oncontextmenu='return false;'>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Label Relation Graph and Label Hierarchies</h1>
	<h2>User Function</h2>
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
<i>Relation color-legend</i><br>
narrower:green - broader:blue - related:red
<br>
narrowMatch:green-dashed - broadMatch:blue-dashed - relatedMatch:red-dashed
<br>
exactMatch:purple - closeMatch:orange
<br>
seeAlso:#FE2EF7-dashed - sameAs:#2EFE2E-dashed - isDefinedBy:#2E9AFE-dashed
<br>
</i>
</p>
</info2>

<br>
<center>
<h1>Label Relation Graph</h1>
<br>
<select id='vocabularylist' size='10' style='width: 500px;' onClick='getGraphVocabulary()'></select>
<br><a href='javaScript:getGraph();'>get graph (all labels)</a>
<br><br>
<div id='graph' style='background-color:#dde2f8;width:875px'></div>
<br>
<h1>Label Hierarchies (in same language or all)</h1>
<br>
<select id='labellist1' size='10' style='width: 500px;' onClick='getTree()'></select>
<br>
<input type='checkbox' id='all' value='all'> show all labels
<br>
<p>broader:blue left - narrower:green right - related:red right</p><p>click to change label - rightclick to show identifier in command line</p>
<div id='chart' style='background-color:#dde2f8;width:875px'></div>
</center>
</span>
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
<script src="http://mbostock.github.com/d3/d3.v2.js"></script>
<script src="config.js"></script>
<script src="utils.js"></script>
<script src="tree.js"></script>

<style type="text/css">

.link {
  fill: none;
  stroke: #666;
  stroke-width: 1.5px;
}

#narrower {
  fill: green;
}
.link.narrower {
  stroke: green;
}

#broader {
  fill: blue;
}
.link.broader {
  stroke: blue;
}

#related {
  fill: red;
}
.link.related {
  stroke: red;
}

#relatedMatch {
  fill: red;
}
.link.relatedMatch {
  stroke: red;
  stroke-dasharray: 0,2 1;
}

#broadMatch {
  fill: blue;
}
.link.broadMatch {
  stroke: blue;
  stroke-dasharray: 0,2 1;
}

#narrowMatch {
  fill: green;
}
.link.narrowMatch {
  stroke: green;
  stroke-dasharray: 0,2 1;
}

#closeMatch {
  fill: orange;
}
.link.closeMatch {
  stroke: orange;
  stroke-dasharray: 0,2 1;
}

#exactMatch {
  fill: purple;
}
.link.exactMatch {
  stroke: purple;
  stroke-dasharray: 0,2 1;
}

#seeAlso {
  fill: #FE2EF7;
}
.link.seeAlso {
  stroke: #FE2EF7;
  stroke-dasharray: 0,2 1;
}

#isDefinedBy {
  fill: #2E9AFE;
}
.link.isDefinedBy {
  stroke: #2E9AFE;
  stroke-dasharray: 0,2 1;
}

#sameAs {
  fill: #2EFE2E;
}
.link.sameAs {
  stroke: #2EFE2E;
  stroke-dasharray: 0,2 1;
}

circle {
  fill: #ccc;
  stroke: #333;
  stroke-width: 1.5px;
}

text {
  font: 10px sans-serif;
  pointer-events: none;
  #text-shadow: 0 1px 0 #fff, 1px 0 0 #fff, 0 -1px 0 #fff, -1px 0 0 #fff;
}

</style>

<script>

var internalID = -1;

var IO = {};

var TS = {};
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];

var GLOBAL = {};
GLOBAL.selectedURL = "";

// hidden value
var vocabularyIdentifiers = {};
var labelIdentifiers = {};

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	//
	document.getElementById('labellist1').options.length = 0;
	// query labels of user to fill the lists
	//query = SPARQL.mylabels;
	query = SPARQL.labelPrefLabelAndIdentifierByCreator;
	query = query.replace('$creator',user);         
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
			var bindings = output.results.bindings;
			/*for (var i=0; i<bindings.length; i++) {       
				var t = bindings[i];
				var val = "";
				var lang = "";
				for(var key in t.s) {
					if (key == "value") {
						val = t.s.value;
					}
					if (key == "xml:lang") {
						lang = t.s['xml:lang'];
					}		
				}
				var x = document.getElementById("labellist1");
				var option = document.createElement("option");
				option.text = "\"" + val + "\"" + "@" + lang;
				x.add(option);
				val = "";
				lang = "";
			}*/
			for (var i=0; i<bindings.length; i++) {
				var binding = bindings[i];
				var val = "";
				var lang = "";
				for(var key in binding.preflabellabel) {
					if (key == "value") {
						val = binding.preflabellabel.value;
					}
					if (key == "xml:lang") {
						lang = binding.preflabellabel['xml:lang'];
					}		
				}
				var x = document.getElementById("labellist1");
				var option = document.createElement("option");
				option.text = val + "@" + lang;
				//option.text = "\"" + val + "\"" + "@" + lang;
				x.add(option);
				// set hidden vocabulary identifier list
				for(var key in binding.labelidentifier) {
					if (key == "value") {
						labelIdentifiers[val + "@" + lang] = binding.labelidentifier.value;
					}	
				}
				// reset
				val = "";
				lang = "";
			}
			// init hierarchy tree with first label value
			document.getElementById("labellist1").selectedIndex = 0;
			getTreeClick(labelIdentifiers[document.getElementById("labellist1").value]);
			//get vocabularylist
			document.getElementById('vocabularylist').options.length = 0;
			// query labels of user to fill the lists
			//query = SPARQL.myvocabularies;
			query = SPARQL.vocabularyLabelAndIdentifierByCreator;
			query = query.replace('$creator',user);         
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
					var bindings = output.results.bindings; 
					//console.info(bindings);
					/*for (var i=0; i<bindings.length; i++) {       
						var t = bindings[i];
						var val = "";
						var lang = "";
						for(var key in t.s) {
							if (key == "value") {
								val = t.s.value;
							}
							if (key == "xml:lang") {
								lang = t.s['xml:lang'];
							}		
						}
						var x = document.getElementById("vocabularylist");
						var option = document.createElement("option");
						option.text = "\"" + val + "\"" + "@" + lang;
						x.add(option);
						val = "";
						lang = "";
					}*/
					for (var i=0; i<bindings.length; i++) {
						var binding = bindings[i];
						var val = "";
						var lang = "";
						for(var key in binding.vocabularylabel) {
							if (key == "value") {
								val = binding.vocabularylabel.value;
							}
							if (key == "xml:lang") {
								lang = binding.vocabularylabel['xml:lang'];
							}		
						}
						var x = document.getElementById("vocabularylist");
						var option = document.createElement("option");
						option.text = val + "@" + lang;
						x.add(option);
						// set hidden vocabulary identifier list
						for(var key in binding.vocabularyidentifier) {
							if (key == "value") {
								vocabularyIdentifiers[val + "@" + lang] = binding.vocabularyidentifier.value;
							}	
						}
						// reset
						val = "";
						lang = "";
					}
					// init vocabulary-list with first entry
					//console.info(vocabularyIdentifiers);
					document.getElementById("vocabularylist").selectedIndex = 0;
					//init graph
					getGraphVocabulary();
				}
			});
        }
    });
});

function getGraph() {

	//http://bl.ocks.org/mbostock/1153292
	
	var links = [];
	
	document.getElementById("graph").innerHTML = "";

	$.ajax({
		type: 'GET',
		url: Config.Relations,
		data: {creator: user},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(output) {
			
			links = output;
			
			var nodes = {};

			// Compute the distinct nodes from the links.
			links.forEach(function(link) {
			  link.source = nodes[link.source] || (nodes[link.source] = {name: link.source});
			  link.target = nodes[link.target] || (nodes[link.target] = {name: link.target});
			});

			var width = 800,
				height = 500;

			var force = d3.layout.force()
				.nodes(d3.values(nodes))
				.links(links)
				.size([width, height])
				.linkDistance(50)
				//.charge(-300)
				.charge(-200)
				.on("tick", tick)
				.start();

			var svg = d3.select("#graph").append("svg")
				.attr("width", width)
				.attr("height", height);

			// Per-type markers, as they don't inherit styles.
			svg.append("defs").selectAll("marker")
				.data(["related", "narrower", "broader", "relatedMatch", "closeMatch", "exactMatch", "broadMatch", "narrowMatch", "seeAlso", "isDefinedBy", "sameAs"])
				.enter().append("marker")
				.attr("id", function(d) { return d; })
				.attr("viewBox", "0 -5 10 10")
				.attr("refX", 15)
				.attr("refY", -1.5)
				.attr("markerWidth", 6)
				.attr("markerHeight", 6)
				.attr("orient", "auto")
				.append("path")
				.attr("d", "M0,-5L10,0L0,5");

			var path = svg.append("g").selectAll("path")
				.data(force.links())
				.enter().append("path")
				.attr("class", function(d) { return "link " + d.type; })
				.attr("marker-end", function(d) { return "url(#" + d.type + ")"; });

			var circle = svg.append("g").selectAll("circle")
				.data(force.nodes())
				.enter().append("circle")
				.attr("r", 6)
				.call(force.drag);

			var text = svg.append("g").selectAll("text")
				.data(force.nodes())
				.enter().append("text")
				.attr("x", 8)
				.attr("y", ".31em")
				.text(function(d) { return d.name; });
				
			// Use elliptical arc path segments to doubly-encode directionality.
			function tick() {
			  path.attr("d", linkArc);
			  circle.attr("transform", transform);
			  text.attr("transform", transform);
			}

			function linkArc(d) {
			  var dx = d.target.x - d.source.x,
				  dy = d.target.y - d.source.y,
				  dr = Math.sqrt(dx * dx + dy * dy);
			  return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
			}

			function transform(d) {
			  return "translate(" + d.x + "," + d.y + ")";
			}

		}
	});

}

function getGraphVocabulary() {

	//var vocabulary = document.getElementById('vocabularylist').value;
	var vocabulary = vocabularyIdentifiers[document.getElementById("vocabularylist").value];
	
	var links = [];
	
	document.getElementById("graph").innerHTML = "";

	$.ajax({
		type: 'GET',
		url: Config.Relations,
		data: {vocabulary: vocabulary},
		error: function(jqXHR, textStatus, errorThrown) {
			alert(errorThrown);
		},
		success: function(output) {
			
			links = output;
			
			var nodes = {};

			// Compute the distinct nodes from the links.
			links.forEach(function(link) {
			  link.source = nodes[link.source] || (nodes[link.source] = {name: link.source});
			  link.target = nodes[link.target] || (nodes[link.target] = {name: link.target});
			});

			var width = 800,
				height = 500;

			var force = d3.layout.force()
				.nodes(d3.values(nodes))
				.links(links)
				.size([width, height])
				.linkDistance(50)
				.charge(-120)
				.gravity(.05)
				.charge(-200)
				.on("tick", tick)
				.start();

			var svg = d3.select("#graph").append("svg")
				.attr("width", width)
				.attr("height", height);

			// Per-type markers, as they don't inherit styles.
			svg.append("defs").selectAll("marker")
				.data(["related", "narrower", "broader", "relatedMatch", "closeMatch", "exactMatch", "broadMatch", "narrowMatch", "seeAlso", "isDefinedBy", "sameAs"])
				.enter().append("marker")
				.attr("id", function(d) { return d; })
				.attr("viewBox", "0 -5 10 10")
				.attr("refX", 15)
				.attr("refY", -1.5)
				.attr("markerWidth", 6)
				.attr("markerHeight", 6)
				.attr("orient", "auto")
				.append("path")
				.attr("d", "M0,-5L10,0L0,5");

			var path = svg.append("g").selectAll("path")
				.data(force.links())
				.enter().append("path")
				.attr("class", function(d) { return "link " + d.type; })
				.attr("marker-end", function(d) { return "url(#" + d.type + ")"; });

			var circle = svg.append("g").selectAll("circle")
				.data(force.nodes())
				.enter().append("circle")
				.attr("r", 6)
				.call(force.drag);

			var text = svg.append("g").selectAll("text")
				.data(force.nodes())
				.enter().append("text")
				.attr("x", 8)
				.attr("y", ".31em")
				.text(function(d) { return d.name; });
				
			// Use elliptical arc path segments to doubly-encode directionality.
			function tick() {
			  path.attr("d", linkArc);
			  circle.attr("transform", transform);
			  text.attr("transform", transform);
			}

			function linkArc(d) {
			  var dx = d.target.x - d.source.x,
				  dy = d.target.y - d.source.y,
				  dr = Math.sqrt(dx * dx + dy * dy);
			  return "M" + d.source.x + "," + d.source.y + "A" + dr + "," + dr + " 0 0,1 " + d.target.x + "," + d.target.y;
			}

			function transform(d) {
			  return "translate(" + d.x + "," + d.y + ")";
			}

		}
	});

}

function getTree() {

	var url = Config.BroaderNarrowerTree;
	var getTreeURL = url + "?label=" + labelIdentifiers[document.getElementById("labellist1").value];
	if (document.getElementById('all').checked) {
		getTreeURL += "&all=true";
	}

	d3.json(getTreeURL, function(json) {
		root = json;
		root.x0 = height / 2;
		root.y0 = width / 2;
		var t1 = d3.layout.tree().size([height, halfWidth]).children(function(d){return d.broader;}),
			t2 = d3.layout.tree().size([height, halfWidth]).children(function(d){return d.narrower;});
			t1.nodes(root);
			t2.nodes(root);
		var rebuildChildren = function(node){
			node.children = getChildren(node);
			if(node.children) node.children.forEach(rebuildChildren);
		}
		rebuildChildren(root);
		root.isRight = false;
		update(root);
	});

}

function getTreeClick(identifier) {

	var splitname = name.split("@");
	var url = Config.BroaderNarrowerTree;
	var getTreeURL = url + "?label=" + identifier;
	if (document.getElementById('all').checked) {
		getTreeURL += "&all=true";
	}

	d3.json(getTreeURL, function(json) {
		root = json;
		root.x0 = height / 2;
		root.y0 = width / 2;
		var t1 = d3.layout.tree().size([height, halfWidth]).children(function(d){return d.broader;}),
			t2 = d3.layout.tree().size([height, halfWidth]).children(function(d){return d.narrower;});
			t1.nodes(root);
			t2.nodes(root);
		var rebuildChildren = function(node){
			node.children = getChildren(node);
			if(node.children) node.children.forEach(rebuildChildren);
		}
		rebuildChildren(root);
		root.isRight = false;
		update(root);
	});

}

</script>