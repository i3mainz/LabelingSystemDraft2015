<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var Config = {};

//Config.SPARQL = 'http://143.93.114.137/sesame/SPARQLconcepts';
Config.SPARQL = 'http://localhost:8084/sesame/SPARQLconcepts';

var TS = {};
TS.vars = [];
TS.bindings = [];
TS.bindingsP = [];
TS.bindingsO = [];
TS.conceptschemes = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#ConceptScheme> }";
TS.conceptschememetadata = "SELECT ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#ConceptScheme> . FILTER (?s = <"+"$conceptscheme"+">) . }";
TS.conceptschemeconcepts = "SELECT ?s WHERE { ?s <http://www.w3.org/2004/02/skos/core#inScheme> <"+"$conceptscheme"+"> . }";
TS.conceptschemelabels = "SELECT ?o WHERE { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?o . ?s <http://www.w3.org/2004/02/skos/core#inScheme> <$conceptscheme> . }";
TS.conceptschemeconceptmetadata = "SELECT ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#Concept> . FILTER (?s = <"+"$concept"+">) . }";
TS.conceptschemelabelmetadata = "SELECT ?s ?p ?o WHERE { ?s ?p ?o . ?s <http://www.w3.org/2004/02/skos/core#prefLabel> \"$label\"@en . ?s <http://www.w3.org/2004/02/skos/core#inScheme> <$conceptscheme> . }";

var IO = {};

IO.sendSPARQL_SKOSConceptScheme = function(url, query, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearFirstList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("firstlist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
            
        }
    });
}

IO.sendSPARQL_SKOSConceptSchemeMetadata = function(url, query, callback, info) {
    
    query = query.replace("$conceptscheme",document.getElementById('firstlist').value);
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
        
        
        var html_str = "";
        html_str = "<table border='1'>";
        for (var i=0; i<TS.bindingsP.length; i++){
                
                html_str += "<tr>";
                
                if (TS.bindingsP[i].contains("hasTopConcept")) {
                    html_str += "<td>";
                    html_str += "<b>" + TS.bindingsP[i] + "</b>"
                    html_str += "</td>";
                    html_str += "<td>";
                    html_str += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQL,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
                    html_str += "</td>";
                } else {
                    html_str += "<td>";
                    html_str += "<b>" + TS.bindingsP[i] + "</b>"
                    html_str += "</td>";
                    html_str += "<td>";
                    html_str += "<i>" + TS.bindingsO[i] + "</i>"
                    html_str += "</td>";
                }
                
                html_str += "</tr>";
                
            }
        
        html_str += "</table>";
            
        $('#info').html("");
        $('#info').html(html_str);
            
        }
        
        
    });
}

IO.sendSPARQL_SKOSConceptSchemeConcepts = function(url, query, callback, info) {
    
    query = query.replace("$conceptscheme",document.getElementById('firstlist').value);
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
        }
    });
    
}

IO.sendSPARQL_SKOSConceptSchemeLabels = function(url, query, callback, info) {
    
    query = query.replace("$conceptscheme",document.getElementById('firstlist').value);
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
                        html_strCSCM += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQL,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
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
    query = query.replace("$conceptscheme",document.getElementById('firstlist').value);
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
                        html_strCSCM += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQL,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
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
                        html_strCSCM += "<a href=\"javaScript:IO.sendSPARQL_SKOSConceptSchemeConceptMetadata2(Config.SPARQL,TS.conceptschemeconceptmetadata,\'"+TS.bindingsO[i]+"\')\"><i>" + TS.bindingsO[i] + "</i></a>"
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


function clearFirstList() {
    document.getElementById('firstlist').options.length = 0;
}

function clearSecondList() {
    document.getElementById('secondlist').options.length = 0;
}

function clearThirdList() {
    document.getElementById('thirdlist').options.length = 0;
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
    <body onLoad="IO.sendSPARQL_SKOSConceptScheme(Config.SPARQL,TS.conceptschemes)">
        <h1>SKOS concepts</h1>
        <table border="1">
            <tr>
              <td>
                <!--<a href="javaScript:IO.sendSPARQL_SKOSConceptScheme(Config.SPARQL,TS.conceptschemes)">Concept Schemes</a> |-->
                <b>Concept Schemes: </b>
                <a href="javaScript:IO.sendSPARQL_SKOSConceptSchemeMetadata(Config.SPARQL,TS.conceptschememetadata)">Concept Scheme Metadata</a>
              </td>
              <td>
                <!--<a href="javaScript:IO.sendSPARQL_SKOSConceptSchemeConcepts(Config.SPARQL,TS.conceptschemeconcepts)">Concept Scheme Concepts</a>-->
              </td>
              <td>
                <!--<a href="javaScript:IO.sendSPARQL_SKOSConceptSchemeLabels(Config.SPARQL,TS.conceptschemelabels)">Concept Scheme Labels</a>-->
              </td>
            </tr>
            <tr>
              <td><select id="firstlist" size="10" style="width: 500px;" onChange="IO.sendSPARQL_SKOSConceptSchemeConcepts(Config.SPARQL,TS.conceptschemeconcepts); IO.sendSPARQL_SKOSConceptSchemeMetadata(Config.SPARQL,TS.conceptschememetadata); IO.sendSPARQL_SKOSConceptSchemeLabels(Config.SPARQL,TS.conceptschemelabels);"></select></td>
              <td><select id="secondlist" size="10" style="width: 500px;" onChange="IO.sendSPARQL_SKOSConceptSchemeConceptMetadata(Config.SPARQL,TS.conceptschemeconceptmetadata);"></select></td>
              <td><select id="thirdlist" size="10" style="width: 500px;" onChange="IO.sendSPARQL_SKOSConceptSchemeLabelMetadata(Config.SPARQL,TS.conceptschemelabelmetadata);"></select></td>
            </tr>
        </table>
        <br>
        <span id="info"></span>
    </body>
</html>
