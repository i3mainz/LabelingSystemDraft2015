<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var Config = {};

//Config.SPARQLConcepts = 'http://143.93.114.137/sesame/SPARQLconcepts';
Config.SPARQLConcepts = 'http://localhost:8084/sesame/SPARQLconcepts';
//Config.SPARQL = 'http://143.93.114.137/sesame/SPARQL';
Config.SPARQL = 'http://localhost:8084/sesame/SPARQL';
//Config.Input = 'http://143.93.114.137/sesame/Input';
Config.Input = 'http://localhost:8084/sesame/Input';
//Config.Delete = 'http://143.93.114.137/sesame/Delete';
Config.Delete = 'http://localhost:8084/sesame/Delete';

var TS = {};
TS.vars = [];
TS.bindings = [];
TS.bindingsP = [];
TS.bindingsO = [];
TS.concepts = "SELECT ?s WHERE { ?s a <http://www.w3.org/2004/02/skos/core#Concept> . }";
TS.labels = "SELECT ?o WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?o . ?s a <http://www.w3.org/2004/02/skos/core#Concept> . }";

TS.conceptschemeconceptmetadata = "SELECT ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#Concept> . FILTER (?s = <"+"$concept"+">) . }";
TS.conceptschemelabelmetadata = "SELECT ?s ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> \"$label\"@en . }";

TS.mylabels = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Label> . ?s <http://purl.org/dc/terms/creator> \"$creator\" .}"
TS.labelconcepts = "SELECT DISTINCT ?o WHERE { <$label> ?p ?o . FILTER (?p = <http://www.w3.org/2004/02/skos/core#related> || ?p = <http://www.w3.org/2004/02/skos/core#broader> || ?p = <http://www.w3.org/2004/02/skos/core#narrower>) . }";
TS.labelmetadata = "SELECT ?s ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Label> . FILTER (?s = <$label>) . }";

TS.URL2Label = "SELECT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> \"$label\"@en . }";



var IO = {};

IO.sendSPARQL_SKOSConcepts = function(url, query, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearSecondList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("secondlist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
            
            document.getElementById('secondlist').selectedIndex = 0;
            IO.sendSPARQL_SKOSConceptSchemeConceptMetadata(Config.SPARQLConcepts,TS.conceptschemeconceptmetadata);
        }
    });
    
}

IO.sendSPARQL_SKOSLabels = function(url, query, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearThirdList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].o.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("thirdlist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
            
            document.getElementById('thirdlist').selectedIndex = 0;
        }
    });
    
}

IO.sendSPARQL_SKOSConceptSchemeConceptMetadata = function(url, query, callback, info) {
    
    query = query.replace("$concept",document.getElementById('secondlist').value);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            TS.bindings.length = 0;
            TS.bindingsP.length = 0;
            TS.bindingsO.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindingsP.push(list[i].p.value);
                TS.bindingsO.push(list[i].o.value);
            }
        
            var html_strCSCM = "";   
            
            html_strCSCM += "<table border='1'>";
        
            html_strCSCM += "<tr>";
            html_strCSCM += "<td>";
            html_strCSCM += "<b>" + document.getElementById('secondlist').value + "</b>";
            html_strCSCM += "</td>";
            html_strCSCM += "<td>";
            html_strCSCM += "</td>";
            html_strCSCM += "</tr>";

            for (var i=0; i<TS.bindingsP.length; i++){

                    html_strCSCM += "<tr>";
                    
                    if (TS.bindingsP[i].contains("broader") || TS.bindingsP[i].contains("narrower") || TS.bindingsP[i].contains("related")) {
                        html_strCSCM += "<td>";
                        html_strCSCM += "<b>" + TS.bindingsP[i] + "</b>";
                        html_strCSCM += "</td>";
                        html_strCSCM += "<td>";
                        html_strCSCM += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQLConcepts,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
                        html_strCSCM += "</td>";
                    } else {
                        html_strCSCM += "<td>";
                        html_strCSCM += "<b>" + TS.bindingsP[i] + "</b>";
                        html_strCSCM += "</td>";
                        html_strCSCM += "<td>";
                        html_strCSCM += "<i>" + TS.bindingsO[i] + "</i>"
                        html_strCSCM += "</td>";
                    }

                    html_strCSCM += "</tr>";

            }

            html_strCSCM += "</table>";

            document.getElementById("info").innerHTML=html_strCSCM;
            
        }
        
        
    });
}

IO.sendSPARQL_SKOSConceptSchemeLabelMetadata = function(url, query, callback, info) {
    
    query = query.replace("$label",document.getElementById('thirdlist').value);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            TS.bindings.length = 0;
            TS.bindingsP.length = 0;
            TS.bindingsO.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].s.value);
                TS.bindingsP.push(list[i].p.value);
                TS.bindingsO.push(list[i].o.value);
            }
        
            var html_strCSCM = "";   
            
            html_strCSCM += "<table border='1'>";
        
            html_strCSCM += "<tr>";
            html_strCSCM += "<td>";
            html_strCSCM += "<b>" + TS.bindings[0] + "</b>";
            html_strCSCM += "</td>";
            html_strCSCM += "<td>";
            html_strCSCM += "</td>";
            html_strCSCM += "</tr>";

            for (var i=0; i<TS.bindingsP.length; i++){

                    html_strCSCM += "<tr>";
                    
                    if (TS.bindingsP[i].contains("broader") || TS.bindingsP[i].contains("narrower") || TS.bindingsP[i].contains("related")) {
                        html_strCSCM += "<td>";
                        html_strCSCM += "<b>" + TS.bindingsP[i] + "</b>";
                        html_strCSCM += "</td>";
                        html_strCSCM += "<td>";
                        html_strCSCM += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQLConcepts,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
                        html_strCSCM += "</td>";
                    } else {
                        html_strCSCM += "<td>";
                        html_strCSCM += "<b>" + TS.bindingsP[i] + "</b>";
                        html_strCSCM += "</td>";
                        html_strCSCM += "<td>";
                        html_strCSCM += "<i>" + TS.bindingsO[i] + "</i>"
                        html_strCSCM += "</td>";
                    }

                    html_strCSCM += "</tr>";

            }

            html_strCSCM += "</table>";

            document.getElementById("info").innerHTML=html_strCSCM;
            
        }
        
        
    });
}

IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2 = function(url, query, targeturl, callback, info) {
    
    query = query.replace("$concept",targeturl);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            TS.bindings.length = 0;
            TS.bindingsP.length = 0;
            TS.bindingsO.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindingsP.push(list[i].p.value);
                TS.bindingsO.push(list[i].o.value);
            }
        
            var html_strCSCM = "";   
            
            html_strCSCM += "<table border='1'>";
        
            html_strCSCM += "<tr>";
            html_strCSCM += "<td>";
            html_strCSCM += "<b>" + targeturl + "</b>";
            html_strCSCM += "</td>";
            html_strCSCM += "<td>";
            html_strCSCM += "</td>";
            html_strCSCM += "</tr>";

            for (var i=0; i<TS.bindingsP.length; i++){

                    html_strCSCM += "<tr>";
                    
                    if (TS.bindingsP[i].contains("broader") || TS.bindingsP[i].contains("narrower") || TS.bindingsP[i].contains("related")) {
                        html_strCSCM += "<td>";
                        html_strCSCM += "<b>" + TS.bindingsP[i] + "</b>";
                        html_strCSCM += "</td>";
                        html_strCSCM += "<td>";
                        html_strCSCM += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQLConcepts,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
                        html_strCSCM += "</td>";
                    } else {
                        html_strCSCM += "<td>";
                        html_strCSCM += "<b>" + TS.bindingsP[i] + "</b>";
                        html_strCSCM += "</td>";
                        html_strCSCM += "<td>";
                        html_strCSCM += "<i>" + TS.bindingsO[i] + "</i>"
                        html_strCSCM += "</td>";
                    }

                    html_strCSCM += "</tr>";

            }

            html_strCSCM += "</table>";

            document.getElementById("info").innerHTML=html_strCSCM;
            
        }
        
        
    });
}

IO.sendSPARQL_ConnectedConcepts = function(url, query, callback, info) {
                        
    query = query.replace("$label",document.getElementById('labellist2').value);
    query = query.replace("$label",document.getElementById('labellist2').value);
    query = query.replace("$label",document.getElementById('labellist2').value);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearConnectList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].o.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("connectlist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
            
            if (TS.bindings.length == 0) {
                var x = document.getElementById("connectlist");
                var option = document.createElement("option");
                option.text = "no related concept";
                x.add(option);
            }
        }
    });
}

IO.getinputconnectLabelConceptTriple = function() {
    var c = document.getElementById('secondlist').value;
    var l = document.getElementById('labellist').value;
    var r = document.getElementById('relation').value;
    if (c!=null && l!=null && c!="" && l!="") {
        var con = "{ ";
        con += "<"+l+"> ";
        con += "<http://www.w3.org/2004/02/skos/core#"+r+"> ";
        con += "<"+c+">";
        con += ". }";
        IO.sendConnectionInput(Config.Input,con);
    } else {
        alert("no content!");
    }
}

IO.getinputdisconnectLabelConceptTriple = function() {
    var c = document.getElementById('secondlist').value;
    var l = document.getElementById('labellist').value;
    var r = document.getElementById('relation').value;
    if (c!=null && l!=null && c!="" && l!="") {
        var con = "{ ";
        con += "<"+l+"> ";
        con += "<http://www.w3.org/2004/02/skos/core#"+r+"> ";
        con += "<"+c+">";
        con += ". }";
        IO.sendConnectionDelete(Config.Delete,con);
    } else {
        alert("no content!");
    }
}

IO.getinputconnectLabelConceptLabelTriple = function(concept) {
    var c = concept;
    console.log(c);
    var l = document.getElementById('labellist').value;
    var r = document.getElementById('relation2').value;
    if (c!=null && l!=null && c!="" && l!="") {
        var con = "{ ";
        con += "<"+l+"> ";
        con += "<http://www.w3.org/2004/02/skos/core#"+r+"> ";
        con += "<"+c+">";
        con += ". }";
        IO.sendConnectionInput(Config.Input,con);
    } else {
        alert("no content!");
    }
}

IO.getinputdisconnectLabelConceptLabelTriple = function(concept) {
    var c = concept;
    console.log(c);
    var l = document.getElementById('labellist').value;
    var r = document.getElementById('relation2').value;
    if (c!=null && l!=null && c!="" && l!="") {
        var con = "{ ";
        con += "<"+l+"> ";
        con += "<http://www.w3.org/2004/02/skos/core#"+r+"> ";
        con += "<"+c+">";
        con += ". }";
        IO.sendConnectionDelete(Config.Delete,con);
    } else {
        alert("no content!");
    }
}

IO.sendConnectionInput = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            IO.sendSPARQL_LabelMetadata(Config.SPARQL,TS.labelmetadata);
            alert("connection created");
        }
    });
}

IO.sendConnectionDelete = function(url, del, callback, info) {
    
    del = mask(del);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: del},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            IO.sendSPARQL_LabelMetadata(Config.SPARQL,TS.labelmetadata);
            alert("connection deleted");
        }
    });
}

IO.sendSPARQLMyLabelsList = function(url, query, callback, info) {
        
    query = query.replace('$creator',"Thiery");
                        
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
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("labellist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
                //var y = document.getElementById("labellist2");
                //var option = document.createElement("option");
                //option.text = TS.bindings[i];
                //y.add(option);
            }
            
            document.getElementById('labellist').selectedIndex = 0;
        }
    });
}

IO.sendSPARQL_LabelMetadata = function(url, query, callback, info) {
    
    query = query.replace("$label",document.getElementById('labellist').value);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            TS.bindings.length = 0;
            TS.bindingsP.length = 0;
            TS.bindingsO.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].s.value);
                TS.bindingsP.push(list[i].p.value);
                TS.bindingsO.push(list[i].o.value);
            }
        
            var html_str = "";   
            
            html_str += "<table border='1'>";
        
            html_str += "<tr>";
            html_str += "<td>";
            html_str += "<b>" + TS.bindings[0] + "</b>";
            html_str += "</td>";
            html_str += "<td>";
            html_str += "</td>";
            html_str += "</tr>";

            for (var i=0; i<TS.bindingsP.length; i++){

                    html_str += "<tr>";
                    
                    if (TS.bindingsP[i].contains("broader") || TS.bindingsP[i].contains("narrower") || TS.bindingsP[i].contains("related")) {
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

            document.getElementById("info").innerHTML=html_str;
            
        }
        
        
    });
}

IO.sendSPARQL_ConceptURL2Label = function(url, query, callback, info) {
    
    query = query.replace("$label",document.getElementById('thirdlist').value);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            console.log("asd");
            var ret = output.results.bindings[0].s.value;
            callback(ret);

        }   
        });
    
}

function clearFirstList() {
    document.getElementById('firstlist').options.length = 0;
}

function clearSecondList() {
    document.getElementById('secondlist').options.length = 0;
}

function clearThirdList() {
    document.getElementById('thirdlist').options.length = 0;
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

function clearConnectList() {
    document.getElementById('connectlist').options.length = 0;
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

function init() {
    //IO.sendSPARQLMyProjectList(Config.SPARQL,TS.myprojects);
}

</script>

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body onLoad="IO.sendSPARQL_SKOSConcepts(Config.SPARQLConcepts,TS.concepts); IO.sendSPARQL_SKOSLabels(Config.SPARQLConcepts,TS.labels); IO.sendSPARQLMyLabelsList(Config.SPARQL,TS.mylabels);">
        <h1>SKOS concepts</h1>
        <table border="1">
            <tr>
              <td>
                  <b>Concepts: getMetadata, selectForConnection</b>
              </td>
              <td>
                <b>Concept Labels: getMetadata</b>
              </td>
              <td>
                <b>My Labels: getMetadata, selectForConnection</b>
              </td>
            </tr>
            <tr>
              <td><select id="secondlist" size="10" style="width: 350px;" onChange="IO.sendSPARQL_SKOSConceptSchemeConceptMetadata(Config.SPARQLConcepts,TS.conceptschemeconceptmetadata);"></select></td>
              <td><select id="thirdlist" size="10" style="width: 350px;" onChange="IO.sendSPARQL_SKOSConceptSchemeLabelMetadata(Config.SPARQLConcepts,TS.conceptschemelabelmetadata);"></select></td>
              <td><select id="labellist" size="10" style="width: 350px;" onChange="IO.sendSPARQL_LabelMetadata(Config.SPARQL,TS.labelmetadata);"></select></td>
            </tr>
        </table>
        <br>
        <span id="info"></span>
        <h1>Connect/Disconnect Label and Concept</h1>
        <span id="connect_lc"><a href="javaScript:IO.getinputconnectLabelConceptTriple();">Connect Label to Concept</a></span>
        <select id="relation">
            <option value="related">related</option>
            <option value="broader">broader</option>
            <option value="narrower">narrower</option>
        </select>
        <span id="disconnect_lc"><a href="javaScript:IO.getinputdisconnectLabelConceptTriple()">Disconnect Label to Concept</a></span> 
        <br>
        <span id="connect_ll"><a href="javaScript:IO.sendSPARQL_ConceptURL2Label(Config.SPARQLConcepts,TS.URL2Label,IO.getinputconnectLabelConceptLabelTriple);">Connect Label to ConceptLabel</a></span>
        <select id="relation2">
            <option value="related">related</option>
            <option value="broader">broader</option>
            <option value="narrower">narrower</option>
        </select>
        <span id="disconnect_ll"><a href="javaScript:IO.sendSPARQL_ConceptURL2Label(Config.SPARQLConcepts,TS.URL2Label,IO.getinputdisconnectLabelConceptLabelTriple);">Disconnect Label to ConceptLabel</a></span> 
        <!--<h1>Concept and Label Relations</h1>-->
        <!--<table border="1">-->
            <!--<tr>-->
              <!--<td>-->
                <!--<b>My Labels: getRelatedConcepts</b>-->
             <!-- </td>-->
              <!--<td>-->
                 <!-- <b>Related Concepts</b>-->
              <!--</td>-->
            <!--</tr>-->
            <!--<tr>-->
                <!--<td><select id="labellist2" size="10" style="width: 500px;" onChange="IO.sendSPARQL_ConnectedConcepts(Config.SPARQL,TS.labelconcepts);"></select></td>-->
                <!--<td><select id="connectlist" size="10" style="width: 500px;" onChange=""></select></td>-->
            <!--</tr>-->
        <!--</table>-->
    </body>
</html>
