<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <link type="text/css" rel="stylesheet" href="style.css"/>
    <script type="text/javascript" src="d3/d3.js"></script>
    <script type="text/javascript" src="d3/d3.layout.js"></script>
	<script type="text/javascript" src="jquery.js"></script>
	<script src="../client/config.js"></script>
	<script src="../client/utils.js"></script>
	<title>Project Tree</title>
	<link href="bootstrap/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<script src="bootstrap/assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/assets/prettify/run_prettify.js"></script>
	<link href="bootstrap/assets/bootstrap-dialog/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css" />
	<script src="bootstrap/assets/bootstrap-dialog/js/bootstrap-dialog.min.js"></script>
	<style type="text/css">
	.node circle {
	  cursor: pointer;
	}
	.node text {
	  font-size: 11px;
	}
	path.link {
	  fill: none;
	  stroke: #ccc;
	  stroke-width: 1.5px;
	}
	.login-dialog .modal-dialog {
		width: 800px;
	}
	</style>
  </head>
  
  <body oncontextmenu="return false;">
    <center>
	<br>
	<table border="0">
		<tr>
			<td width="750">
				<img src='../client/models/site-templates/images/ls_color.png'>
			</td>
			<td width="60" align="left">
				<b>Height: </b>
				<br>
				<b>Width: </b>
				<br>
				<b>Name: </b>
				<br><br><br>
			</td>
			<td width="175" align="left">
				<input id='height' type='text' size='5' maxlength='4' onkeyup="isNumeric(document.getElementById('height').value, document.getElementById('height'))">
				<br>
				<input id='width' type='text' size='5' maxlength='4' onkeyup="isNumeric(document.getElementById('width').value, document.getElementById('width'))">
				<br>
				<select id='register' style='width: 150px;' onchange="isNumeric(document.getElementById('height').value, document.getElementById('height')); isNumeric(document.getElementById('width').value, document.getElementById('width'));"></select>
				<br><br>
				<span id="link"></span>
			</td>
		</tr>
	</table>
	</center>

	<div id="body"></div>
	<center>
	<span id="export"><a href="javaScript:export_svg();">Export active view as SVG-HTML page</a></span>
	</center>
	
<script type="text/javascript">

$('#export').hide();

function isNumeric(val, element) {
	if (!isNaN(val) == true) {
		element.style.backgroundColor = '#C1FFC1'; //green
	} else {
		element.style.backgroundColor = '#EEA9B8'; //red
	}
	if (val == "") {
		element.style.backgroundColor = '#EEA9B8'; //red
	}
	if ($('#height').css('backgroundColor') == 'rgb(193, 255, 193)' && $('#width').css('backgroundColor') == 'rgb(193, 255, 193)')
	{
		document.getElementById("link").innerHTML='<a href=\"?height='+document.getElementById("height").value+'&width='+document.getElementById("width").value+'&name='+document.getElementById("register").value+'\"><b>Repaint ProjectTree</b></a>';
	} else {
			document.getElementById("link").innerHTML='<b>not valid</b>';
	}
}

var TS = {};
TS.bindings = [];
var tmp = {};

// get names list
$(document).ready(function() {
	query = SPARQL.querynames;
    query = encodeURIComponent(query);
    $.ajax({
        type: 'GET',
        url: Config.SPARQL,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            TS.bindings.length = 0;
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].o.value);
            }
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("register");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                if (TS.bindings[i].indexOf("Florian Thiery") == -1) {
					x.add(option);
				}
            }
			document.getElementById("register").value = searchname;
			isNumeric(document.getElementById('height').value, document.getElementById('height'));
			isNumeric(document.getElementById('width').value, document.getElementById('width'));
        }
    });
});

var IO = {};
var searchname = "";
var heighttree;
var widthtree;

if (window.location.search != "")
  searchname = window.location.search;
  var arr = searchname.split("=");
  //[0]"?height" [1]{height}&width [2]{width}&name [3]{Name}
  searchname = arr[3];
  heighttree = arr[1].replace("&width","");
  widthtree = arr[2].replace("&name","");
  document.getElementById('height').value = heighttree;
  document.getElementById('width').value = widthtree;
  isNumeric(document.getElementById('height').value, document.getElementById('height'));
  isNumeric(document.getElementById('width').value, document.getElementById('width'));

function relationStrokeColor(relation) {
	if (relation == "broader") {
		return "blue";
	} else if (relation == "narrower") {
		return "green";
	} else if (relation == "related") {
		return "red";
	} else if (relation == "isDefinedBy") {
		return "#2E9AFE";
	} else if (relation == "seeAlso") {
		return "#FE2EF7";
	} else if (relation == "sameAs") {
		return "#2EFE2E";
	} else if (relation == "closeMatch") {
		return "orange ";
	} else if (relation == "exactMatch") {
		return "purple";
	} else if (relation == "relatedMatch") {
		return "red";
	} else if (relation == "narrowMatch") {
		return "green";
	} else if (relation == "broadMatch") {
		return "blue";
	} else {
		return "steelblue";
	}
}

function relationFillColor(relation) {
	if (relation == "broader") {
		return "white";
	} else if (relation == "narrower") {
		return "white";
	} else if (relation == "related") {
		return "white";
	} else if (relation == "isDefinedBy") {
		return "#2E9AFE";
	} else if (relation == "seeAlso") {
		return "#FE2EF7";
	} else if (relation == "sameAs") {
		return "#2EFE2E";
	} else if (relation == "closeMatch") {
		return "orange";
	} else if (relation == "exactMatch") {
		return "purple";
	} else if (relation == "relatedMatch") {
		return "red";
	} else if (relation == "narrowMatch") {
		return "green";
	} else if (relation == "broadMatch") {
		return "blue";
	} else {
		return "steelblue";
	}
}

function relationStrokeWidth(relation) {
	if (relation == "broader") {
		return "2.0px";
	} else if (relation == "narrower") {
		return "2.0px";
	} else if (relation == "related") {
		return "2.0px";
	} else if (relation == "isDefinedBy") {
		return "2.0px";
	} else if (relation == "seeAlso") {
		return "2.0px";
	} else if (relation == "sameAs") {
		return "2.0px";
	} else if (relation == "closeMatch") {
		return "2.0px";
	} else if (relation == "exactMatch") {
		return "2.0px";
	} else if (relation == "relatedMatch") {
		return "2.0px";
	} else if (relation == "narrowMatch") {
		return "2.0px";
	} else if (relation == "broadMatch") {
		return "2.0px";
	} else {
		return "2.0px";
	}
}
  
function clickfunctionDialog(name, url, relation, parentnode, language, parentname, publicVoc, comment, commentlang, note, notelang, definition, definionlang, conceptscheme, concept) {
	
	if (relation == undefined) { relation = ""; }
	// relation dialogs
	var mess = "";
	if (relation == "broader" || relation == "narrower" || relation == "related") {
		mess = "<h2>Label <i>"+parentname+"</i> has "+relation+" concept <i>"+name.split("@")[0]+"</i></h2><br><h4><a href='"+url+"' target='_blank'>"+ url + "</a></h4>";
	} else if (relation == "closeMatch" || relation == "exactMatch" || relation == "narrowMatch" || relation == "relatedMatch" || relation == "broadMatch" || relation == "seeAlso" || relation == "isDefinedBy" || relation == "sameAs"){
		mess = "<h2>Label <i>"+parentname+"</i> "+relation+"</h2><br><h4><a href='"+url+"' target='_blank'>"+ url + "</a></h4>";
	} else {
		mess = "<h1>wrong relation!</h1>";
	}
	// item dialogs
	if (url.indexOf("project#") != -1) {
		var com = "";
		com += "<h1>Project: "+name+"</h1><h3>language: "+language+"</h3><i><h3>comment: "+comment+"</h3></i>";
		BootstrapDialog.show({
			size: BootstrapDialog.SIZE_NORMAL,
			title: 'Information',
			message: com,
			draggable: true,
			cssClass: 'login-dialog',
			buttons: [{
				label: 'close',
				action: function(dialogItself){
					dialogItself.close();
				}
			}]
		});
	} else if (url.indexOf("vocabulary#") != -1) {
		var com = "";
		com += "<h1>Vocabulary: "+name+"</h1><h3>language: "+language+"</h3><h3>the vocabulary is <i>"+publicVoc+"</i></h3><br><h4><a href='"+conceptscheme+".rdf' target='_blank'>"+conceptscheme+".rdf</h4></a><br><h3><a href='"+conceptscheme+".skos' target='_blank'>Download Concept Scheme</a></h3>";
		var ressourcename = url.replace(Config.Instance_VOCABULARY,"");
		BootstrapDialog.show({
			size: BootstrapDialog.SIZE_NORMAL,
			title: 'Information',
			message: com,
			draggable: true,
			cssClass: 'login-dialog',
			buttons: [{
				label: 'close',
				action: function(dialogItself){
					dialogItself.close();
				}
			}]
		});
	} else if (url.indexOf("label#") != -1) {
		var com = "";
		concept = concept.substring(0, concept.length - 1);
		com += "<h1>Label: "+name+"</h1><h3>language: "+language+"</h3><i><h3>note: "+note+"</h3></i><i><h3>definition: "+definition+"</h3></i><h4><a href='"+concept+".rdf' target='_blank'>"+concept+".rdf</h4></a>";
		BootstrapDialog.show({
			size: BootstrapDialog.SIZE_NORMAL,
			title: 'Information',
			message: com,
			draggable: true,
			cssClass: 'login-dialog',
			buttons: [{
				label: 'close',
				action: function(dialogItself){
					dialogItself.close();
				}
			}]
		});
	} else if (name.indexOf("user") != -1) {
		BootstrapDialog.show({
			size: BootstrapDialog.SIZE_NORMAL,
			title: 'Information',
			message: "<h1>"+name+"</h1>",
			draggable: true,
			cssClass: 'login-dialog',
			buttons: [{
				label: 'close',
				action: function(dialogItself){
					dialogItself.close();
				}
			}]
		});
	} else {
		BootstrapDialog.show({
			size: BootstrapDialog.SIZE_NORMAL,
			title: 'Information',
			message: mess,
			draggable: true,
			cssClass: 'login-dialog',
			buttons: [{
				label: 'close',
				action: function(dialogItself){
					dialogItself.close();
				}
			}]
		});
	}
}

var m = [20, 120, 20, 120],
    w = widthtree - m[1] - m[3],
    h = heighttree - m[0] - m[2],
    i = 0,
    root;

var tree = d3.layout.tree()
    .size([h, w]);

var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });

var vis = d3.select("#body").append("svg:svg")
    .attr("id", "tree")
	.attr("width", w + m[1] + m[3])
    .attr("height", h + m[0] + m[2])
	.append("svg:g")
    .attr("transform", "translate(" + m[3] + "," + m[0] + ")");
	


//d3.json("flare.json", function(json) {
d3.json(Config.ProjectTreePublic+searchname, function(json) {
  root = json;
  root.x0 = h / 2;
  root.y0 = 0;
  $('#export').show();
  function toggleAll(d) {
    if (d.children) {
      d.children.forEach(toggleAll);
      toggle(d);
    }
  }
  // Initialize the display to show a few nodes.
  root.children.forEach(toggleAll);
  update(root);
});

function update(source) {
  var duration = d3.event && d3.event.altKey ? 5000 : 500;

  // Compute the new tree layout.
  var nodes = tree.nodes(root).reverse();
 
  // Normalize for fixed-depth.
  nodes.forEach(function(d) { d.y = d.depth * 180; });

  // Update the nodes…
  var node = vis.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Enter any new nodes at the parent's previous position.
  var nodeEnter = node.enter().append("svg:g")
      .attr("class", "node")
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
      .on("contextmenu", function(d) { clickfunctionDialog(d.name, d.url, d.relation, d.parent.url, d.lang, d.parent.name, d.public, d.comment, d.commentlang, d.note, d.notelang, d.definition, d.definitionlang, d.conceptscheme, d.concept); })
	  //.on("click", function(d) { toggle(d); update(d); });
	  .on("click", function(d) { if (d.relation=="broader" || d.relation=="narrower" || d.relation=="related" || d.relation=="broadMatch" || d.relation=="narrowMatch" || d.relation=="relatedMatch" || d.relation=="closeMatch" || d.relation=="exactMatch" || d.relation=="seeAlso" || d.relation=="sameAs"  || d.relation=="isDefinedBy") { window.open(d.url); } else { toggle(d); update(d); } });
  
  nodeEnter.append("svg:circle")
      .attr("r", 1e-6)
	  //.style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; }) // not used here
	  .style("fill", function(d) { if (d.relation=="broader" || d.relation=="narrower" || d.relation=="related" || d.relation=="broadMatch" || d.relation=="narrowMatch" || d.relation=="relatedMatch" || d.relation=="closeMatch" || d.relation=="exactMatch" || d.relation=="seeAlso" || d.relation=="sameAs"  || d.relation=="isDefinedBy") { return relationFillColor(d.relation); } else { return d._children ? "lightsteelblue" : "#fff"; } })
	  .style("stroke", function(d) { return relationStrokeColor(d.relation) })
	  .style("stroke-width", function(d) { return relationStrokeWidth(d.relation) })

  nodeEnter.append("svg:text")
      .attr("x", function(d) { return d.children || d._children ? -10 : 10; })
      .attr("dy", ".35em")
      .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
      .text(function(d) { return d.name; })
      .style("fill-opacity", 1e-6);

  // Transition nodes to their new position.
  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

  nodeUpdate.select("circle")
	  .attr("r", 4.5)
      //.style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });
	  .style("fill", function(d) { if (d.relation=="broader" || d.relation=="narrower" || d.relation=="related" || d.relation=="broadMatch" || d.relation=="narrowMatch" || d.relation=="relatedMatch" || d.relation=="closeMatch" || d.relation=="exactMatch" || d.relation=="seeAlso" || d.relation=="sameAs"  || d.relation=="isDefinedBy") { return relationFillColor(d.relation); } else { return d._children ? "lightsteelblue" : "#fff"; } })

  nodeUpdate.select("text")
      .style("fill-opacity", 1);

  // Transition exiting nodes to the parent's new position.
  var nodeExit = node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
      .remove();

  nodeExit.select("circle")
      .attr("r", 1e-6);

  nodeExit.select("text")
      .style("fill-opacity", 1e-6);

  // Update the links…
  var link = vis.selectAll("path.link")
      .data(tree.links(nodes), function(d) { return d.target.id; });

  // Enter any new links at the parent's previous position.
  link.enter().insert("svg:path", "g")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
		return diagonal({source: o, target: o});
      })
    .transition()
      .duration(duration)
      .attr("d", diagonal);

  // Transition links to their new position.
  link.transition()
      .duration(duration)
      .attr("d", diagonal);

  // Transition exiting nodes to the parent's new position.
  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = {x: source.x, y: source.y};
        return diagonal({source: o, target: o});
      })
      .remove();

  // Stash the old positions for transition.
  nodes.forEach(function(d) {
    d.x0 = d.x;
    d.y0 = d.y;
  });
  
}

// Toggle children.
function toggle(d) {
  if (d.children) {
    d._children = d.children;
    d.children = null;
  } else {
    d.children = d._children;
    d._children = null;
  }
}

function post_to_url(path, params, method) {
    method = method || "post";
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
         }
    }
    document.body.appendChild(form);
    form.submit();
}

function export_svg() {
	var svg_mask = document.getElementById('tree').innerHTML;
	var svg = Config.SVG;
	svg += "?width="+widthtree+"&height="+heighttree;
	svg += "&user="+searchname;
	svg += "&svg=";
	svg += svg_mask;
	post_to_url(Config.SVG, {width: widthtree, height: heighttree, user: searchname, svg: svg_mask});
}
    </script>
  </body>
</html>
