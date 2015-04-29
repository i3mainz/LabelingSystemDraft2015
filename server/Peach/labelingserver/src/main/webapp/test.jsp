<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var Config = {};

//Config.SPARQL = 'http://143.93.114.137/sesame/SPARQL';
//Config.Input = 'http://143.93.114.137/sesame/Input';
//Config.Delete = 'http://143.93.114.137/sesame/Delete';
Config.SPARQL = 'http://localhost:8084/sesame/SPARQL';
Config.Input = 'http://localhost:8084/sesame/Input';
Config.Delete = 'http://localhost:8084/sesame/Delete';

var TS = {};

TS.q = "SELECT * WHERE { ?s ?p ?o }";
TS.i = "{ <http://test.de#x> <http://test.de#y> <http://test.de#z> .}";
TS.d = "{ <http://test.de#x> <http://test.de#y> <http://test.de#z> .}";

TS.vars = [];
TS.bindings = [];

TS.projects = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Project> }";
TS.vocabularies = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Vocabulary> }";
TS.labels = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Label> }";

TS.provoc = "SELECT ?s WHERE { $p <http://143.93.114.137/vocab#contains> ?s }";
TS.vocpro = "SELECT ?s WHERE { ?s <http://143.93.114.137/vocab#contains> $v }";
TS.voclab = "SELECT ?s WHERE { $v <http://143.93.114.137/vocab#contains> ?s }";
TS.labvoc = "SELECT ?s WHERE { ?s <http://143.93.114.137/vocab#contains> $l }";
TS.labelinfo = "SELECT ?s ?o WHERE { $l ?o ?s }";


var IO = {};

IO.getinputprojectTriple = function(name) {
    
        var project = "{ ";
        project += "<http://143.93.114.137/projects#"+name+"> ";
        project += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        project += "<http://143.93.114.137/vocab#Project> ";
        project += ". }";
        
        IO.sendProjectInput(Config.Input,project);

}

IO.getdeleteprojectTriple = function(url) {
    
        var project = "{ ";
        project += "<"+url+"> ";
        project += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        project += "<http://143.93.114.137/vocab#Project> ";
        project += ". }";
        
        IO.sendProjectDelete(Config.Delete,project,clearProjectList);

}

IO.getinputvocabularyTriple = function(name) {
    
        var vocabulary = "{ ";
        vocabulary += "<http://143.93.114.137/vocabularies#"+name+"> ";
        vocabulary += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        vocabulary += "<http://143.93.114.137/vocab#Vocabulary> ";
        vocabulary += ". }";
        
        IO.sendVocabularyInput(Config.Input,vocabulary);

}

IO.getdeletevocabularyTriple = function(url) {
    
        var vocabulary = "{ ";
        vocabulary += "<"+url+"> ";
        vocabulary += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        vocabulary += "<http://143.93.114.137/vocab#Vocabulary> ";
        vocabulary += ". }";
        
        IO.sendVocabularyDelete(Config.Delete,vocabulary,clearVocabularyList);

}

IO.getinputlabelTriple = function(labelname,creatorname) {
    
        var label = "{ ";
        //own ontology
        label += "<http://143.93.114.137/labels/"+labelname+"> ";
        label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        label += "<http://143.93.114.137/vocab#Label> ";
        label += ".";
        // skos concept
        label += "<http://143.93.114.137/labels/"+labelname+"> ";
        label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        label += "<http://www.w3.org/2004/02/skos/core#Concept> ";
        label += ".";
        // skos label
        label += "<http://143.93.114.137/labels/"+labelname+"> ";
        label += "<http://www.w3.org/2004/02/skos/core#prefLabel> ";
        label += "\""+labelname+"\"@en ";
        label += ".";
        // dcterms creator
        label += "<http://143.93.114.137/labels/"+labelname+"> ";
        label += "<http://purl.org/dc/terms/creator> ";
        label += "\""+creatorname+"\"@de ";
        label += ".";
        // dcterms creator
        var date=new Date();
        var dd=date.getDate();  
        var mm=date.getMonth() + 1;  
        var yy=date.getYear() + 1900;  
        var HH=date.getHours();  
        var MM=date.getMinutes();  
        var d = dd+"."+mm+"."+yy+" "+HH+":"+MM+"h"; 
        label += "<http://143.93.114.137/labels/"+labelname+"> ";
        label += "<http://purl.org/dc/terms/date> ";
        label += "\""+d+"\" ";
        label += ".";
        //end
        label += " }";
        
        IO.sendLabelInput(Config.Input,label);

}

IO.getdeletelabelTriple = function(url) {
    
        var label = "{ ";
        label += "<"+url+"> ";
        label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        label += "<http://143.93.114.137/vocab#Label> ";
        label += ". }";
        
        IO.sendLabelDelete(Config.Delete,label,clearLabelList);

}

    
IO.sendSPARQL = function(url, query, format, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: format},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
        //    var vars = output.head.vars;
        //    for (var i=0; i<vars.length; i++) {
        //        TS.vars.push(vars[i]);
        //    }
        //    var list = output.results.bindings;
        //    for (var i=0; i<list.length; i++) {
//		console.log(list[i].s.value);
         //       TS.bindings.push(list[i].s.value);
            //}
          
         // for (var i=0; i<TS.bindings.length; i++){
         //   var x = document.getElementById("projectlist");
         //   var option = document.createElement("option");
         //   option.text = TS.bindings[i];
         //   x.add(option);
         //}
        }
    });
}

IO.sendSPARQLShowTextArea = function(url, query, format, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: format},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            clearSPARQLAusgabe();
            //var string = (new XMLSerializer()).serializeToString(output);
            var string = JSON.stringify(output);
            document.getElementById('sparql_ausgabe').value = string;
        }
    });
}

function clearProjectList() {
    document.getElementById('projectlist').options.length = 0;
}

function clearVocabularyList() {
    document.getElementById('vocabularylist').options.length = 0;
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

function clearProjectVocabularyList() {
    document.getElementById('voc2pro').options.length = 0;
}

function clearVocabularyProjectList() {
    document.getElementById('pro2voc').options.length = 0;
}

function clearVocabularyLabelList() {
    document.getElementById('lab2voc').options.length = 0;
}

function clearLabelVocabularyList() {
    document.getElementById('voc2lab').options.length = 0;
}

function clearLabelInfo() {
    document.getElementById('labelinfo').options.length = 0;
}

function clearSPARQLAusgabe() {
    document.getElementById('sparql_ausgabe').value = "";
}
    
IO.sendSPARQLProjectList = function(url, query, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearProjectList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		//console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("projectlist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLVocabularyList = function(url, query, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearVocabularyList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		//console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("vocabularylist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLLabelList = function(url, query, callback, info) {
    
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
		//console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("labellist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLProjectVocabularyList = function(url, query, callback, info) {
    
    var q = query;
    var P = document.getElementById('projectlist').value;
    if (document.getElementById('projectlist').value != "") {
        q = q.replace('$p',"<"+document.getElementById('projectlist').value+">");
    } else {
        alert("no content!");
        return;
    }
    query = mask(q);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearProjectVocabularyList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		//console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("voc2pro");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLVocabularyProjectList = function(url, query, callback, info) {
    
    var q = query;
    var P = document.getElementById('vocabularylist').value;
    if (document.getElementById('vocabularylist').value != "") {
        q = q.replace('$v',"<"+document.getElementById('vocabularylist').value+">");
    } else {
        alert("no content!");
        return;
    }
    query = mask(q);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearVocabularyProjectList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		//console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("pro2voc");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLVocabularyLabelList = function(url, query, callback, info) {
    
    var q = query;
    if (document.getElementById('vocabularylist').value != "") {
        q = q.replace('$v',"<"+document.getElementById('vocabularylist').value+">");
    } else {
        alert("no content!");
        return;
    }
    query = mask(q);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearVocabularyLabelList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("lab2voc");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLLabelVocabularyList = function(url, query, callback, info) {
    
    var q = query;
    if (document.getElementById('labellist').value != "") {
        q = q.replace('$l',"<"+document.getElementById('labellist').value+">");
    } else {
        alert("no content!");
        return;
    }
    query = mask(q);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearLabelVocabularyList();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		console.log(list[i].s.value);
                TS.bindings.push(list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("voc2lab");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}

IO.sendSPARQLLabelInfo = function(url, query, callback, info) {
    
    var q = query;
    var P = document.getElementById('labellist').value;
    if (document.getElementById('labellist').value != "") {
        q = q.replace('$l',"<"+document.getElementById('labellist').value+">");
    } else {
        alert("no content!");
        return;
    }
    query = mask(q);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            clearLabelInfo();
            TS.bindings.length = 0;
        
            var list = output.results.bindings;
            for (var i=0; i<list.length; i++) {
		console.log(list[i].s.value);
                TS.bindings.push(list[i].o.value + " " + list[i].s.value);
            }
        
            for (var i=0; i<TS.bindings.length; i++){
                var x = document.getElementById("labelinfo");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
        }
    });
}


IO.sendProjectInput = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alertXML(xml);
            IO.sendSPARQLProjectLists(Config.SPARQL,TS.projects);
        }
    });
}

IO.sendVocabularyInput = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alertXML(xml);
            IO.sendSPARQLVocabularyList(Config.SPARQL,TS.vocabularies);
        }
    });
}

IO.sendLabelInput = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alertXML(xml);
            IO.sendSPARQLLabelList(Config.SPARQL,TS.labels);
        }
    });
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
            alertXML(xml);
            //IO.sendSPARQLVocabularyList(Config.SPARQL,TS.vocabularies);
        }
    });
}

IO.sendProjectDelete = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alertXML(xml);
            IO.sendSPARQLProjectList(Config.SPARQL,TS.projects);
        }
    });
}

IO.sendVocabularyDelete = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alertXML(xml);
            IO.sendSPARQLVocabularyList(Config.SPARQL,TS.vocabularies);
        }
    });
}

IO.sendLabelDelete = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alertXML(xml);
            IO.sendSPARQLLabelList(Config.SPARQL,TS.labels);
        }
    });
}

IO.getinputconnectProjectVocabularyTriple = function() {
    var v = document.getElementById('vocabularylist').value;
    var p = document.getElementById('projectlist').value;
    if (v!=null && p!=null && v!="" && p!="") {
        alert(v+" "+p);
        var con = "{ ";
        con += "<"+p+">";
        con += "<http://143.93.114.137/vocab#contains> ";
        con += "<"+v+">";
        con += ". }";
        IO.sendConnectionInput(Config.Input,con);
    } else {
        alert("was eingeben!");
    }
}

IO.getinputconnectVocabularyLabelTriple = function() {
    var v = document.getElementById('vocabularylist').value;
    var l = document.getElementById('labellist').value;
    if (v!=null && l!=null && v!="" && l!="") {
        alert(v+" "+l);
        var con = "{ ";
        con += "<"+v+">";
        con += "<http://143.93.114.137/vocab#contains> ";
        con += "<"+l+">";
        con += ". }";
        IO.sendConnectionInput(Config.Input,con);
    } else {
        alert("was eingeben!");
    }
}
    
function alertXML(xml) {

    var string = (new XMLSerializer()).serializeToString(xml);
    alert(string);

}

function mask(text) {
    //var replacer = new RegExp("\/", "g");
    //var replacer2 = new RegExp("\r", "g");
    //test2 = test2.replace(replacer, "k");
    //test2 = test2.replace(replacer2, "");
    
    
   // query = query.replace(" ", "%20");
   //     query = query.replace(":", "%3A");
    //    query = query.replace("?", "%3F");
    //    query = query.replace("^", "%5E");
    //    query = query.replace("{", "%7B");
     //   query = query.replace("}", "%7D");
    //    query = query.replace("/", "%2F");
    //    query = query.replace("\"", "%22");
    //    query = query.replace("<", "%3C");
    //    query = query.replace(">", "%3E");
    //    query = query.replace("#", "%23");
    //    
    var replacer = new RegExp(" ", "g");
    text = text.replace(replacer, "%20");
    var replacer2 = new RegExp(":", "g");
    text = text.replace(replacer2, "%3A");
    //var replacer3 = new RegExp("?", "g");
    //text = text.replace(replacer3, "%3F");
    //var replacer4 = new RegExp("^", "g");
    //text = text.replace(replacer4, "%5E");
    var replacer5 = new RegExp("{", "g");
    text = text.replace(replacer5, "%7B");
    var replacer6 = new RegExp("}", "g");
    text = text.replace(replacer6, "%7D");
    var replacer7 = new RegExp("/", "g");
    text = text.replace(replacer7, "%2F");
    //var replacer8 = new RegExp('\\', "g");
    //text = text.replace(replacer8, "%2F");
    var replacer9 = new RegExp("<", "g");
    text = text.replace(replacer9, "%3C");
    var replacer10 = new RegExp(">", "g");
    text = text.replace(replacer10, "%3E");
    var replacer11 = new RegExp("#", "g");
    text = text.replace(replacer11, "%23");
    
    return text;
}

</script>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Sesame Communication Test</title>
    </head>
    <body>
        <!--<a href="javaScript:IO.sendSPARQL(Config.SPARQL,TS.q,'xml')">SPARQL-XML</a><br>
        <a href="javaScript:IO.sendSPARQL(Config.SPARQL,TS.q,'json')">SPARQL-JSON</a><br>
        <a href="javaScript:IO.sendInput(Config.Input,TS.i)">Input</a><br>
        <a href="javaScript:IO.sendDelete(Config.Delete,TS.d)">Delete</a><br>
        <br>-->
        <a href="javaScript:IO.sendSPARQLProjectList(Config.SPARQL,TS.projects)">SPARQL-Projects</a> | 
        <a href="javaScript:clearProjectList()">Clear Project List</a> 
        <input type="button" value="Delete Project" id="deleteproject" onclick="IO.getdeleteprojectTriple(document.getElementById('projectlist').value)"> |
        <a href="javaScript:IO.sendSPARQLProjectVocabularyList(Config.SPARQL,TS.provoc)">SPARQL-Project-Vocabularies</a> | 
        <a href="javaScript:clearProjectVocabularyList()">Clear Project-Vocabularies</a>
        <br><br>
        <select id="projectlist" size="10" style="width: 300px;"></select>
        <select id="voc2pro" size="10" style="width: 300px;"></select>
        <br><br>
        <input id="project" type="text" size="30" maxlength="30"><br>
        <input type="button" value="New Project" id="sendproject" onclick="IO.getinputprojectTriple(document.getElementById('project').value)">
        <input type="button" value="Connect P+V" id="connect_pv" onclick="IO.getinputconnectProjectVocabularyTriple();">
        
        <br><br>
        <a href="javaScript:IO.sendSPARQLVocabularyList(Config.SPARQL,TS.vocabularies)">SPARQL-Vocabularies</a> | 
        <a href="javaScript:clearVocabularyList()">Clear Vocabulary List</a> 
        <input type="button" value="Delete Vocabulary" id="deletevocabulary" onclick="IO.getdeletevocabularyTriple(document.getElementById('vocabularylist').value)">
        <a href="javaScript:IO.sendSPARQLVocabularyProjectList(Config.SPARQL,TS.vocpro)">SPARQL-Vocabulary-Projects</a> | 
        <a href="javaScript:clearVocabularyProjectList()">Clear Vocabulary-Project</a> |
        <a href="javaScript:IO.sendSPARQLVocabularyLabelList(Config.SPARQL,TS.voclab)">SPARQL-Vocabulary-Labels</a> | 
        <a href="javaScript:clearVocabularyLabelList()">Clear Vocabulary-Labels</a>
        <br><br>
        <select id="vocabularylist" size="10" style="width: 300px;"></select>
        <select id="pro2voc" size="10" style="width: 300px;"></select>
        <select id="lab2voc" size="10" style="width: 300px;"></select>
        <br><br>
        <input id="vocabulary" type="text" size="30" maxlength="30"><br>
        <input type="button" value="New Vocabulary" id="sendvocabulary" onclick="IO.getinputvocabularyTriple(document.getElementById('vocabulary').value)">
        <input type="button" value="Connect V+L" id="connect_vl" onclick="IO.getinputconnectVocabularyLabelTriple();">
        
        <br><br>
        <a href="javaScript:IO.sendSPARQLLabelList(Config.SPARQL,TS.labels)">SPARQL-Labels</a> | 
        <a href="javaScript:clearLabelList()">Clear Label List</a> 
        <input type="button" value="Delete Label" id="deletelabel" onclick="IO.getdeletelabelTriple(document.getElementById('labellist').value)">
        <a href="javaScript:IO.sendSPARQLLabelVocabularyList(Config.SPARQL,TS.labvoc)">SPARQL-Label-Vocabularies</a> | 
        <a href="javaScript:clearLabelVocabularyList()">Clear Label-Vocabularies</a> |
        <a href="javaScript:IO.sendSPARQLLabelInfo(Config.SPARQL,TS.labelinfo)">SPARQL-Label-Info</a> | 
        <a href="javaScript:clearLabelInfo()">Clear Label-Infos</a>
        <br><br>
        <select id="labellist" size="10" style="width: 300px;"></select>
        <select id="voc2lab" size="10" style="width: 300px;"></select>
        <select id="labelinfo" size="10" style="width: 600px;"></select>
        <br><br>
        <input id="label" type="text" size="30" maxlength="30"><br>
        <input type="button" value="New Label" id="sendlabel" onclick="IO.getinputlabelTriple(document.getElementById('label').value,'thiery')">
        <br><br>
        <textarea id="sparql_eingabe" cols="50" rows="20" value="">SELECT * WHERE { ?s ?p ?o }</textarea>
        <textarea id="sparql_ausgabe" cols="100" rows="20" value=""></textarea>
        <br>
        <input type="button" value="Send SPARQL" id="sendsparqltextarea" onclick="IO.sendSPARQLShowTextArea(Config.SPARQL,document.getElementById('sparql_eingabe').value,'json')">
        <br><br>
    </body>
</html>
