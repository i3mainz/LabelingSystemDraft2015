/*var margin = {top: 0, right: 0, bottom: 0, left: 0},
    width = 800 - margin.left - margin.right,
    halfWidth = width / 2,
    height = 400 - margin.top - margin.bottom,
    i = 0,
    duration = 500,
    root;*/
	
var margin = {top: 0, right: 0, bottom: 0, left: 0},
	width = 800 - margin.left - margin.right,
	halfWidth = width / 2,
	height = 400 - margin.top - margin.bottom,
	i = 0,
	duration = 500,
	root;

var vis = d3.select("#chart").append("svg")
	.attr("width", width + margin.right + margin.left)
	.attr("height", height + margin.top + margin.bottom)
	.append("g")
	.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

var getChildren = function(d){
      var a = [];
      if(d.broader) for(var i = 0; i < d.broader.length; i++){
        d.broader[i].isRight = false;
        d.broader[i].parent = d;
        a.push(d.broader[i]);
      }
      if(d.narrower) for(var i = 0; i < d.narrower.length; i++){
        d.narrower[i].isRight = true;
        d.narrower[i].parent = d;
        a.push(d.narrower[i]);
      }
      return a.length?a:null;
    };

var tree = d3.layout.tree()
    .size([height, width]);

var diagonal = d3.svg.diagonal()
    .projection(function(d) { return [d.y, d.x]; });
var elbow = function (d, i){
      var source = calcLeft(d.source);
      var target = calcLeft(d.target);
      var hy = (target.y-source.y)/2;
      if(d.isRight) hy = -hy;
      return "M" + source.y + "," + source.x
             + "H" + (source.y+hy)
             + "V" + target.x + "H" + target.y;
    };

var connector = elbow;

var calcLeft = function(d){
  var l = d.y;
  if(!d.isRight){
    l = d.y-halfWidth;
    l = halfWidth - l;
  }
  return {x : d.x, y : l};
};

var toArray = function(item, arr){
  arr = arr || [];
  var i = 0, l = item.children?item.children.length:0;
  arr.push(item);
  for(; i < l; i++){
    toArray(item.children[i], arr);
  }
  return arr;
};

function update(source) {
  // Compute the new tree layout.
  var nodes = toArray(source);

  // Normalize for fixed-depth.
  nodes.forEach(function(d) { d.y = d.depth * 180 + halfWidth; });

  // Update the nodes…
  var node = vis.selectAll("g.node")
      .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Enter any new nodes at the parent's previous position.
  var nodeEnter = node.enter().append("g")
      .attr("class", "node")
	  .on("click", function(d) { getTreeClick(d.identifier); })
	  .on("contextmenu", function(d) { console.info(d.identifier); })
      .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; });
      //.on("click", click);

  //nodeEnter.append("circle")
      //.attr("r", 1e-6)
      //.style("fill", function(d) { return d._children ? "lightsteelblue" : "#fff"; });
	  
  nodeEnter.append("circle")
      .attr("r", 1e-6)
	  .style("stroke", function(d) { return StrokeColor(d.state) })
	  .style("stroke-width", function(d) { return StrokeWidth(d.state) });
	  //.style("fill", function(d) { return StrokeColor(d.state) });
      //.style("fill", function(d) { return d._children ? "lightsteelblue" : "#000"; });

  nodeEnter.append("text")
      .attr("dy", function(d) { return d.isRight?14:-12;})
      .attr("text-anchor", "middle")
      .text(function(d) { return d.name; })
      .style("fill-opacity", 1e-6);

  // Transition nodes to their new position.
  var nodeUpdate = node.transition()
      .duration(duration)
      .attr("transform", function(d) { p = calcLeft(d); return "translate(" + p.y + "," + p.x + ")"; });

  nodeUpdate.select("circle")
      //.attr("r", 4.5)
	  .attr("r", function(d) { return Radius(d.state) })
      //.style("fill", function(d) { return d._children ? "lightsteelblue" : "#000"; });
	  .style("fill", function(d) { return StrokeColor(d.state) });

  nodeUpdate.select("text")
      .style("fill-opacity", 1);

  // Transition exiting nodes to the parent's new position.
  var nodeExit = node.exit().transition()
      .duration(duration)
      .attr("transform", function(d) { p = calcLeft(d.parent||source); return "translate(" + p.y + "," + p.x + ")"; })
      .remove();

  nodeExit.select("circle")
      .attr("r", 1e-6);

  nodeExit.select("text")
      .style("fill-opacity", 1e-6);

  // Update the links...
  var link = vis.selectAll("path.link")
      .data(tree.links(nodes), function(d) { return d.target.id; });

  // Enter any new links at the parent's previous position.
  link.enter().insert("path", "g")
      .attr("class", "link")
      .attr("d", function(d) {
        var o = {x: source.x0, y: source.y0};
        return connector({source: o, target: o});
      });

  // Transition links to their new position.
  link.transition()
      .duration(duration)
      .attr("d", connector);

  // Transition exiting nodes to the parent's new position.
  link.exit().transition()
      .duration(duration)
      .attr("d", function(d) {
        var o = calcLeft(d.source||source);
        if(d.source.isRight) o.y -= halfWidth - (d.target.y - d.source.y);
        else o.y += halfWidth - (d.target.y - d.source.y);
        return connector({source: o, target: o});
      })
      .remove();

  // Stash the old positions for transition.
  nodes.forEach(function(d) {
    var p = calcLeft(d);
    d.x0 = p.x;
    d.y0 = p.y;
  });
  
  // Toggle children on click.
  //function click(d) {
    //if (d.children) {
     // d._children = d.children;
     // d.children = null;
    //} else {
      //d.children = d._children;
     // d._children = null;
    //}
    //update(source);
  //}
}

function StrokeColor(state) {
	if (state == "center") {
		return "black";
	} else if (state=="related") {
		return "red";
	} else if (state=="broader") {
		return "blue";
	} else if (state=="narrower") {
		return "green";
	} else {
		return "orange";
	}
}

function StrokeWidth(state) {	
	if (state == "center") {
		return "1.0px";
	} else if (state=="related") {
		return "1.0px";
	} else {
		return "1.0px";
	}
}

function Radius(state) {	
	if (state == "center") {
		return 7.5;
	} else if (state=="related") {
		return 5;
	} else {
		return 5;
	}
}