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
	<h1>Create Label Hierarchy</h1>
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
<b>Expert Notes</b>
<br><br>
More information for Semantic Relations <a href='http://www.w3.org/TR/2009/REC-skos-reference-20090818/#semantic-relations'
  target='_blank'>here</a>:
<br><br>
<b>A </b>broader<b> B</b> means, A has a broader concept B.
<br><br>
If you create a broader relation, a narrower relation in the contrary direction is created, too. The same happens the other way round.
<br>
</i>
</p>
</info2>

<br>
<center>
<table border='0'>
	<tr>
	  <td>
		<b>My Label (1)</b>
	  </td>
	  <td>
		<b>My Label (2)</b>
	  </td>
	</tr>
	<tr>
	  <td><select id='labellist1' size='10' style='width: 500px;'></select></td>
	  <td><select id='labellist2' size='10' style='width: 500px;'></select></td>
	</tr>
</table>
</center>
<br>

<span id='mylabelfunctions'>
<center>
<span id='send_broader'><a href='javaScript:IO.setBroaderNarrower(\"broader\")'>Send Broader</a></span> | 
<span id='send_narrower'><a href='javaScript:IO.setBroaderNarrower(\"narrower\")'>Send Narrower</a></span> | 
<span id='send_related'><a href='javaScript:IO.setBroaderNarrower(\"related\")'>Send Related</a></span>
</center>
</span>
<br>

<h1>Relations</h1>
<br>
<center>
<select id='relationlist' size='10' style='width: 800px;'></select>
</center>
</span>
<br>

<span id='mylabelfunctions2'>
<center>
<span id='delete_relation'><a href='javaScript:IO.deleteBroaderNarrower()'>Delete Relation</a></span>
<!--<p><b>Hierarchical Tree - click in My Label (1)</b></p><p>broader left - narrower right - orange - related:green</p><p>click to change label</p>
<div id='chart' style='background-color:#dde2f8;width:875px'></div>-->
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
<script src="config.js"></script>
<script src="utils.js"></script>

<style type="text/css">

	.node circle {
	  fill: black;
	  stroke: black;
	  stroke-width: 1.5px;
	}

	.node text {
	  font: 10px sans-serif;
	}

	path.link {
	  fill: none;
	  stroke: steelblue;
	  stroke-width: 1.0px;
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

$(document).ready(function() {
	console.info("load GUI");
	LS.GUI.loadGUI();
	//
	document.getElementById('labellist1').options.length = 0;
	document.getElementById('labellist2').options.length = 0;
	document.getElementById('relationlist').options.length = 0;
	// query labels of user to fill the lists
	query = SPARQL.mylabels;
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
			for (var i=0; i<bindings.length; i++) {       
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
				var y = document.getElementById("labellist2");
				var option = document.createElement("option");
				option.text = "\"" + val + "\"" + "@" + lang;
				y.add(option);
				var x = document.getElementById("labellist1");
				var option = document.createElement("option");
				option.text = "\"" + val + "\"" + "@" + lang;
				x.add(option);
				val = "";
				lang = "";
			}
			// query broader relations
			query = SPARQL.labelbroaderlabel;
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
					for (var i=0; i<bindings.length; i++) {
						var t = bindings[i];
						var val = "";
						var lang = "";
						var val2 = "";
						var lang2 = "";
						for(var key in t.pl) {
							if (key == "value") {
								val = t.pl.value;
								val2 = t.pl2.value;
							}
							if (key == "xml:lang") {
								lang = t.pl['xml:lang'];
								lang2 = t.pl2['xml:lang'];
							}		
						}
						var a = document.getElementById("relationlist");
						var option = document.createElement("option");
						option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#broader> " + "\"" + val2 + "\"" + "@" + lang2;
						a.add(option);
						val = "";
						lang = "";
						val2 = "";
						lang2 = "";
					}
					// query narrower relations
					query = SPARQL.labelnarrowerlabel;
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
							for (var i=0; i<bindings.length; i++) {       
								var t = bindings[i];
								var val = "";
								var lang = "";
								var val2 = "";
								var lang2 = "";
								for(var key in t.pl) {
									if (key == "value") {
										val = t.pl.value;
										val2 = t.pl2.value;
									}
									if (key == "xml:lang") {
										lang = t.pl['xml:lang'];
										lang2 = t.pl2['xml:lang'];
									}		
								}
								var b = document.getElementById("relationlist");
								var option = document.createElement("option");
								option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#narrower> " + "\"" + val2 + "\"" + "@" + lang2;
								b.add(option);
								val = "";
								lang = "";
								val2 = "";
								lang2 = "";
							}
							// query related relations
							query = SPARQL.labelrelatedlabel;
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
									console.info('Daten geladen');
									try {
										output = JSON.parse(output);
									} catch (e) {
										console.log(e);
									} finally {
									}
									var bindings = output.results.bindings; 
									for (var i=0; i<bindings.length; i++) {       
										var t = bindings[i];
										var val = "";
										var lang = "";
										var val2 = "";
										var lang2 = "";
										for(var key in t.pl) {
											if (key == "value") {
												val = t.pl.value;
												val2 = t.pl2.value;
											}
											if (key == "xml:lang") {
												lang = t.pl['xml:lang'];
												lang2 = t.pl2['xml:lang'];
											}		
										}
										var c = document.getElementById("relationlist");
										var option = document.createElement("option");
										option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#related> " + "\"" + val2 + "\"" + "@" + lang2;
										c.add(option);
										val = "";
										lang = "";
										val2 = "";
										lang2 = "";
									}
								}
							});
						}
					});
				}
			});
        }
    });
});

IO.setBroaderNarrower = function(mode) {
	if (document.getElementById('labellist1').value.indexOf("@")>-1 && document.getElementById('labellist2').value.indexOf("@")>-1) {
		// send broader/narrower insert
		var update = "";
		if (mode=="broader") {
			update = SPARQLUPDATE.sendbroader;
			update = update.replace("$pl1",document.getElementById('labellist1').value);
			update = update.replace("$pl2",document.getElementById('labellist2').value);
		} else if (mode=="narrower") {
			update = SPARQLUPDATE.sendnarrower;
			update = update.replace("$pl1",document.getElementById('labellist1').value);
			update = update.replace("$pl2",document.getElementById('labellist2').value);
		} else if (mode=="related") {
			update = SPARQLUPDATE.sendrelated;
			update = update.replace("$pl1",document.getElementById('labellist1').value);
			update = update.replace("$pl2",document.getElementById('labellist2').value);
		}
		update = update.replace("$creator","\""+user+"\"");
		update = encodeURIComponent(update);
		$.ajax({
			beforeSend: function(req) {
				req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
			},
			type: 'POST',
			url: Config.Update,
			data: {update: update},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error(errorThrown);
				alert(errorThrown);
			},
			success: function(xml) {
				console.info('triple gespeichert');
				document.getElementById('relationlist').options.length = 0;
				// query broader relations
				query = SPARQL.labelbroaderlabel;
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
						for (var i=0; i<bindings.length; i++) {
							var t = bindings[i];
							var val = "";
							var lang = "";
							var val2 = "";
							var lang2 = "";
							for(var key in t.pl) {
								if (key == "value") {
									val = t.pl.value;
									val2 = t.pl2.value;
								}
								if (key == "xml:lang") {
									lang = t.pl['xml:lang'];
									lang2 = t.pl2['xml:lang'];
								}		
							}
							var a = document.getElementById("relationlist");
							var option = document.createElement("option");
							option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#broader> " + "\"" + val2 + "\"" + "@" + lang2;
							a.add(option);
							val = "";
							lang = "";
							val2 = "";
							lang2 = "";
						}
						// query narrower relations
						query = SPARQL.labelnarrowerlabel;
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
								for (var i=0; i<bindings.length; i++) {       
									var t = bindings[i];
									var val = "";
									var lang = "";
									var val2 = "";
									var lang2 = "";
									for(var key in t.pl) {
										if (key == "value") {
											val = t.pl.value;
											val2 = t.pl2.value;
										}
										if (key == "xml:lang") {
											lang = t.pl['xml:lang'];
											lang2 = t.pl2['xml:lang'];
										}		
									}
									var b = document.getElementById("relationlist");
									var option = document.createElement("option");
									option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#narrower> " + "\"" + val2 + "\"" + "@" + lang2;
									b.add(option);
									val = "";
									lang = "";
									val2 = "";
									lang2 = "";
								}
								// query related relations
								query = SPARQL.labelrelatedlabel;
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
										console.info('Daten geladen');
										try {
											output = JSON.parse(output);
										} catch (e) {
											console.log(e);
										} finally {
										}
										var bindings = output.results.bindings; 
										for (var i=0; i<bindings.length; i++) {       
											var t = bindings[i];
											var val = "";
											var lang = "";
											var val2 = "";
											var lang2 = "";
											for(var key in t.pl) {
												if (key == "value") {
													val = t.pl.value;
													val2 = t.pl2.value;
												}
												if (key == "xml:lang") {
													lang = t.pl['xml:lang'];
													lang2 = t.pl2['xml:lang'];
												}		
											}
											var c = document.getElementById("relationlist");
											var option = document.createElement("option");
											option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#related> " + "\"" + val2 + "\"" + "@" + lang2;
											c.add(option);
											val = "";
											lang = "";
											val2 = "";
											lang2 = "";
										}
									}
								});
							}
						});
					}
				});
			}
		});	
	} else {
		alert("no content in label1");
	}
}

IO.deleteBroaderNarrower = function() {
	if (document.getElementById('relationlist').value.indexOf("@")>-1) {
		var x;
		var r=confirm("Delete?");
		if (r==true) {
			var temp = document.getElementById('relationlist').value.split(" ");
			var label1 = temp[0];
			var label2 = temp[2];
			var relation = temp[1];
			// send broader/narrower delete
			var update = "";
			if (relation.indexOf("broader")>-1) {
				update = SPARQLUPDATE.deletebroader;
				update = update.replace("$pl1",label1);
				update = update.replace("$pl2",label2);
			} else if (relation.indexOf("narrower")>-1) {
				update = SPARQLUPDATE.deletenarrower;
				update = update.replace("$pl1",label1);
				update = update.replace("$pl2",label2);
			} else if (relation.indexOf("related")>-1) {
				update = SPARQLUPDATE.deleterelated;
				update = update.replace("$pl1",label1);
				update = update.replace("$pl2",label2);
			}
			update = update.replace("$creator","\""+user+"\"");
			query = encodeURIComponent(query);
			$.ajax({
				beforeSend: function(req) {
					req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
				},
				type: 'POST',
				url: Config.Update,
				data: {update: update},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error(errorThrown);
					alert(errorThrown);
				},
				success: function(output) {
					console.info('triple gelöscht');
					document.getElementById('relationlist').options.length = 0;
					// query broader relations
					query = SPARQL.labelbroaderlabel;
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
							for (var i=0; i<bindings.length; i++) {
								var t = bindings[i];
								var val = "";
								var lang = "";
								var val2 = "";
								var lang2 = "";
								for(var key in t.pl) {
									if (key == "value") {
										val = t.pl.value;
										val2 = t.pl2.value;
									}
									if (key == "xml:lang") {
										lang = t.pl['xml:lang'];
										lang2 = t.pl2['xml:lang'];
									}		
								}
								var a = document.getElementById("relationlist");
								var option = document.createElement("option");
								option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#broader> " + "\"" + val2 + "\"" + "@" + lang2;
								a.add(option);
								val = "";
								lang = "";
								val2 = "";
								lang2 = "";
							}
							// query narrower relations
							query = SPARQL.labelnarrowerlabel;
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
									for (var i=0; i<bindings.length; i++) {       
										var t = bindings[i];
										var val = "";
										var lang = "";
										var val2 = "";
										var lang2 = "";
										for(var key in t.pl) {
											if (key == "value") {
												val = t.pl.value;
												val2 = t.pl2.value;
											}
											if (key == "xml:lang") {
												lang = t.pl['xml:lang'];
												lang2 = t.pl2['xml:lang'];
											}		
										}
										var b = document.getElementById("relationlist");
										var option = document.createElement("option");
										option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#narrower> " + "\"" + val2 + "\"" + "@" + lang2;
										b.add(option);
										val = "";
										lang = "";
										val2 = "";
										lang2 = "";
									}
									// query related relations
									query = SPARQL.labelrelatedlabel;
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
											console.info('Daten geladen');
											try {
												output = JSON.parse(output);
											} catch (e) {
												console.log(e);
											} finally {
											}
											var bindings = output.results.bindings; 
											for (var i=0; i<bindings.length; i++) {       
												var t = bindings[i];
												var val = "";
												var lang = "";
												var val2 = "";
												var lang2 = "";
												for(var key in t.pl) {
													if (key == "value") {
														val = t.pl.value;
														val2 = t.pl2.value;
													}
													if (key == "xml:lang") {
														lang = t.pl['xml:lang'];
														lang2 = t.pl2['xml:lang'];
													}		
												}
												var c = document.getElementById("relationlist");
												var option = document.createElement("option");
												option.text = "\"" + val + "\"" + "@" + lang + " <http://www.w3.org/2004/02/skos/core#related> " + "\"" + val2 + "\"" + "@" + lang2;
												c.add(option);
												val = "";
												lang = "";
												val2 = "";
												lang2 = "";
											}
										}
									});
								}
							});
						}
					});
				}
			});
		} else {
			alert("no content in relationlist");
		}
	} 
}

</script>