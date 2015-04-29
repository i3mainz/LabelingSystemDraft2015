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
TS.result = [];
TS.output = [];
TS.output2 = [];
TS.vars = [];
TS.bindings = [];
TS.vocabularies = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Vocabulary> }";
TS.myvocabularies = "SELECT * WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Vocabulary> . ?s <http://purl.org/dc/terms/creator> \"$creator\" .}"
TS.myprojects = "SELECT * WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Project> . ?s <http://purl.org/dc/terms/creator> \"$creator\" .}"
TS.vocpro = "SELECT ?s WHERE { ?s <http://143.93.114.137/vocab#contains> <$v> }";

TS.vocmetadata = "SELECT ?verb ?value WHERE { <$v> ?verb ?value }";

var IO = {};

IO.getinputvocabularyTriple = function(v, creator) {

        var l = document.getElementById('label').value;
        var ll = document.getElementById('languageLabel').value;
        var c = document.getElementById('comment').value;
        var cl = document.getElementById('languageComment').value;
        
        var vocabulary = "{ ";
        vocabulary += "<http://143.93.114.137/vocabulary#"+v+"> ";
        vocabulary += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        vocabulary += "<http://143.93.114.137/vocab#Vocabulary> ";
        vocabulary += ".";
        // dcterms creator
        vocabulary += "<http://143.93.114.137/vocabulary#"+v+"> ";
        vocabulary += "<http://purl.org/dc/terms/creator> ";
        vocabulary += "\""+creator+"\" ";
        vocabulary += ".";
        if (document.getElementById('label').value != "") {
            // rdfs label
            vocabulary += "<http://143.93.114.137/vocabulary#"+v+"> ";
            vocabulary += "<http://www.w3.org/2000/01/rdf-schema#label> ";
            vocabulary += "\""+l+"\"@"+ll+" ";
            vocabulary += ".";
        }
        if (document.getElementById('comment').value != "") {
            // rdfs comment
            vocabulary += "<http://143.93.114.137/vocabulary#"+v+"> ";
            vocabulary += "<http://www.w3.org/2000/01/rdf-schema#comment> ";
            vocabulary += "\""+c+"\"@"+cl+" ";
            vocabulary += ".";
        }
        vocabulary += "}";
        
        if (document.getElementById('vocabulary').value != "") {
            IO.sendVocabularyInput(Config.Input,vocabulary);
        } else {
            alert("no content!");
            return;
        }

}

IO.getdeletevocabularyTriple = function(url, creator) {
        
        var vocabulary = "{ ";
        vocabulary += "<"+url+"> ";
        vocabulary += "?p ";
        vocabulary += "?o ";
        vocabulary += ". }";
        
        if (document.getElementById('vocabularylist').value != "") {
            IO.sendVocabularyDelete(Config.Delete,vocabulary,clearVocabularyList);
        } else {
            alert("no content!");
            return;
        }

}

IO.sendSPARQLVocabularyList = function(url, query, callback, info) {
    
    $('#deletevocabulary').hide();
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

IO.sendSPARQLMyVocabularyList = function(url, query, callback, info) {
    
    $('#deletevocabulary').show();
        
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

IO.sendSPARQLProjectVocabularyList = function(url, query, callback, info) {
    
    var q = query;
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

IO.sendVocabularyInput = function(url, input, callback, info) {
    
    input = escape(input);
    input = mask(input);
        
    $.ajax({
        beforeSend: function(req) {
            req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
	},
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            IO.sendSPARQLMyVocabularyList(Config.SPARQL,TS.myvocabularies);
        }
    });
}

IO.sendVocabularyDelete = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input, mode: 'deletewhere'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            IO.sendSPARQLMyVocabularyList(Config.SPARQL,TS.myvocabularies);
        }
    });
}

IO.sendSPARQLMyProjectList = function(url, query, callback, info) {
    
    $('#deleteproject').show();
        
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

IO.getinputconnectProjectVocabularyTriple = function() {
    var v = document.getElementById('vocabularylist').value;
    var p = document.getElementById('projectlist').value;
    if (v!=null && p!=null && v!="" && p!="") {
        var con = "{ ";
        con += "<"+p+"> ";
        con += "<http://143.93.114.137/vocab#contains> ";
        con += "<"+v+">";
        con += ". ";
        con += "<"+v+"> ";
        con += "<http://143.93.114.137/vocab#belongsTo> ";
        con += "<"+p+">";
        con += ". }";
        IO.sendUpdateCallback(Config.Input,con,IO.sendSPARQL_VocabularyMetadata);
    } else {
        alert("no content!");
    }
}

IO.getinputdisconnectProjectVocabularyTriple = function() {
    var v = document.getElementById('vocabularylist').value;
    var p = document.getElementById('projectlist').value;
    if (v!=null && p!=null && v!="" && p!="") {
        var con = "{ ";
        con += "<"+p+"> ";
        con += "<http://143.93.114.137/vocab#contains> ";
        con += "<"+v+">";
        con += ". ";
        con += "<"+v+"> ";
        con += "<http://143.93.114.137/vocab#belongsTo> ";
        con += "<"+p+">";
        con += ". }";
        IO.sendUpdateCallback(Config.Delete,con,IO.sendSPARQL_VocabularyMetadata);
    } else {
        alert("no content!");
    }
}

IO.getinputpublishVocabularyTriple = function() {
    var v = document.getElementById('vocabularylist').value;
    if (v!=null && v!="") {
        var pub = "{ ";
        pub += "<"+v+"> ";
        pub += "<http://143.93.114.137/vocab#state> ";
        pub += "\"public\"";
        pub += ". }";
        IO.sendUpdateCallback(Config.Input,pub,IO.sendSPARQL_VocabularyMetadata);
    } else {
        alert("no content!");
    }
}

IO.getinputhideVocabularyTriple = function() {
    var v = document.getElementById('vocabularylist').value;
    if (v!=null && v!="") {
        var pub = "{ ";
        pub += "<"+v+"> ";
        pub += "<http://143.93.114.137/vocab#state> ";
        pub += "\"public\"";
        pub += ". }";
        IO.sendUpdateCallback(Config.Delete,pub,IO.sendSPARQL_VocabularyMetadata);
    } else {
        alert("no content!");
    }
}

IO.sendUpdate = function(url, up, callback, info) {
    
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
            alert("action done");
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
            callback(Config.SPARQL,TS.vocmetadata);
        }
    });
}

IO.sendSPARQLVocabularyProjectList = function(url, query, callback, info) {
    
    var q = query;
    var P = document.getElementById('vocabularylist').value;
    if (document.getElementById('vocabularylist').value != "") {
        q = q.replace('$v',document.getElementById('vocabularylist').value);
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
            
            if (TS.bindings.length < 1) {
                alert("no project available!");
            }
            
        }
    });
}

function clearVocabularyList() {
    document.getElementById('vocabularylist').options.length = 0;
}

function clearProjectList() {
    document.getElementById('projectlist').options.length = 0;
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
    fieldCheck();
    IO.sendSPARQLMyProjectList(Config.SPARQL,TS.myprojects);
    IO.sendSPARQLMyVocabularyList(Config.SPARQL,TS.myvocabularies);
}

function fieldCheck() {
    
    var empty_v = false;
    var special = false;
    
    if (document.getElementById("vocabulary").value != "") {
        empty_v = false;
    } else {
        empty_v = true;
    }
    
    special = specialCharacterCheck(document.getElementById("vocabulary").value); // true if sonderzeichen
    
    if (special == false && empty_v == false) {
        document.getElementById("vocabulary").style.backgroundColor = '#C1FFC1'; //green
    } 
    if (special == true || empty_v == true) {
        document.getElementById("vocabulary").style.backgroundColor = '#EEA9B8'; //red
    }   
        
    if (special == false && empty_v == false) {
       document.getElementById("sendvocabulary").style.visibility = 'visible';
    } else {
        document.getElementById("sendvocabulary").style.visibility = 'hidden';
    }
}

function specialCharacterCheck(char) {
    
    if (char.indexOf("â") != -1) {
        return true;
    } else if (char.indexOf("á") != -1) {
        return true;
    } else if (char.indexOf("à") != -1) {
        return true;
    } else if (char.indexOf("ä") != -1) {
        return true;
    } else if (char.indexOf("ê") != -1) {
        return true;
    } else if (char.indexOf("é") != -1) {
        return true;
    } else if (char.indexOf("è") != -1) {
        return true;
    } else if (char.indexOf("î") != -1) {
        return true;
    } else if (char.indexOf("í") != -1) {
        return true;
    } else if (char.indexOf("ì") != -1) {
        return true;
    } else if (char.indexOf("ô") != -1) {
        return true;
    } else if (char.indexOf("ò") != -1) {
        return true;
    } else if (char.indexOf("ó") != -1) {
        return true;
    } else if (char.indexOf("ö") != -1) {
        return true;
    } else if (char.indexOf("û") != -1) {
        return true;
    } else if (char.indexOf("ü") != -1) {
        return true;
    } else if (char.indexOf("ú") != -1) {
        return true;
    } else if (char.indexOf("ë") != -1) {
        return true;
    } else if (char.indexOf("ç") != -1) {
        return true;
    } else if (char.indexOf("ã") != -1) {
        return true;
    } else if (char.indexOf("å") != -1) {
        return true;
    } else if (char.indexOf("õ") != -1) {
        return true;
    } else if (char.indexOf("ï") != -1) {
        return true;
    } else if (char.indexOf("ù") != -1) {
        return true;
    } else if (char.indexOf("^") != -1) {
        return true;
    } else if (char.indexOf("´") != -1) {
        return true;
    } else if (char.indexOf("`") != -1) {
        return true;
    } else if (char.indexOf("#") != -1) {
        return true;
    } else if (char.indexOf("?") != -1) {
        return true;
    } else if (char.indexOf("/") != -1) {
        return true;
    } else if (char.indexOf("\\") != -1) {
        return true;
    } else if (char.indexOf("+") != -1) {
        return true;
    } else if (char.indexOf("~") != -1) {
        return true;
    } else if (char.indexOf("*") != -1) {
        return true;
    } else if (char.indexOf("'") != -1) {
        return true;
    } else if (char.indexOf("\"") != -1) {
        return true;
    } else if (char.indexOf("%") != -1) {
        return true;
    } else if (char.indexOf("&") != -1) {
        return true;
    } else if (char.indexOf("$") != -1) {
        return true;
    } else if (char.indexOf("§") != -1) {
        return true;
    } else if (char.indexOf("!") != -1) {
        return true;
    } else if (char.indexOf("{") != -1) {
        return true;
    } else if (char.indexOf("}") != -1) {
        return true;
    } else if (char.indexOf("(") != -1) {
        return true;
    } else if (char.indexOf(")") != -1) {
        return true;
    } else if (char.indexOf("[") != -1) {
        return true;
    } else if (char.indexOf("]") != -1) {
        return true;
    } else if (char.indexOf("°") != -1) {
        return true;
    } else if (char.indexOf("=") != -1) {
        return true;
    } else if (char.indexOf(",") != -1) {
        return true;
    } else if (char.indexOf(".") != -1) {
        return true;
    } else if (char.indexOf(":") != -1) {
        return true;
    } else if (char.indexOf(";") != -1) {
        return true;
    } else if (char.indexOf("?") != -1) {
        return true;
    } else if (char.indexOf("@") != -1) {
        return true;
    } else if (char.indexOf("ß") != -1) {
        return true;
    } else {
        return false;
    }
    
}

IO.sendSPARQL_VocabularyMetadata = function(url, query, callback, info) {
    
    query = query.replace("$v",document.getElementById('vocabularylist').value);
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: 'json'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            // set help array to null
            TS.vars.length = 0;
            TS.result.length = 0;
            TS.output.length = 0;
            TS.output2.length = 0;
            
            // html output
            html_str = "";
            html_str += "<h1>Vocabulary Metadata</h1>";
            html_str += "<table border='1'>";
            html_str += "<tr>";
            
            // read JSON head-->vars objects to array (e.g. spo)
            var varsj = output.head.vars;
            for (var i=0; i<varsj.length; i++) {
                TS.vars.push(varsj[i]);
            }
            // html output
            for (var i=0; i<TS.vars.length; i++){
                html_str += "<th>"+TS.vars[i]+"</th>";
            }
            
            // html output
            html_str += "</tr>";
            html_str += "</tr>";
            
            // read JSON rasults-->bindings[i] objects to array with key (e.g. pppsssooo)
            var bindings = output.results.bindings; 
            for (var i=0; i<bindings.length; i++) {       
                var t = bindings[i];     
                for(var key in t) {
                    TS.result.push(key + "__" + t[key].value);
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
            var b1 = false;
            var b2 = false;
            for (var i=0; i<TS.output2.length; i++) {
                var split = TS.output2[i].split("__");
                html_str += "<td>"+split[1]+"</td>";
                if ((i+1)%TS.vars.length==0) {
                    html_str += "</tr>";
                    html_str += "<tr>";
                }
                
                var s1 = split[1];
                if (s1.indexOf("http://143.93.114.137/vocab#belongsTo")!=-1) {
                    b1=true;
                }
                if (s1.indexOf("public")!=-1) {
                    b2=true;
                }
            }
            if (b1){
                console.log("project");
                document.getElementById("setpro").style.visibility = "";
                document.getElementById("setpro").style.backgroundColor = '#C1FFC1'; //green
                document.getElementById("setpro").value = "vocab projects available";
            } else {
                console.log("no-project");
                document.getElementById("setpro").style.visibility = "";
                document.getElementById("setpro").style.backgroundColor = '#EEA9B8'; //red
                document.getElementById("setpro").value = "no vocab projects available";
            }
            if (b2){
                console.log("publish");
                document.getElementById("setpub").style.visibility = "";
                document.getElementById("setpub").style.backgroundColor = '#C1FFC1'; //green
                document.getElementById("setpub").value = "vocabulary published";
            } else {
                console.log("hidden");
                document.getElementById("setpub").style.visibility = "";
                document.getElementById("setpub").style.backgroundColor = '#EEA9B8'; //red
                document.getElementById("setpub").value = "vocabulary hidden";
            }
            
            // html output
            html_str += "</tr>";
            html_str += "</table>";

            document.getElementById("info").innerHTML=html_str;
            
        }
        
        
    });
}

</script>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>New Vocabulary</title>
    </head>
    <body onLoad="init();">
        
        <h1>My vocabularies</h1>
        <table border="1">
            <tr>
              <td>
                <a href="javaScript:IO.sendSPARQLMyVocabularyList(Config.SPARQL,TS.myvocabularies)">My Vocabularies</a> | 
                <span id="deletevocabulary"><a href="javaScript:IO.getdeletevocabularyTriple(document.getElementById('vocabularylist').value,'Thiery')">Delete Vocabulary</a> | </span> 
                <a href="javaScript:IO.sendSPARQLVocabularyList(Config.SPARQL,TS.vocabularies)">All Vocabularies</a>
              </td>
              <td>
                <a href="javaScript:IO.sendSPARQLMyProjectList(Config.SPARQL,TS.myprojects)">My Projects</a> | 
                <a href="javaScript:IO.sendSPARQLVocabularyProjectList(Config.SPARQL,TS.vocpro)">Projects of selected Vocabulary</a>
              </td>
            </tr>
            <tr>
                <td><select id="vocabularylist" size="10" style="width: 500px;" onclick="IO.sendSPARQL_VocabularyMetadata(Config.SPARQL,TS.vocmetadata)"></select></td>
              <td><select id="projectlist" size="10" style="width: 500px;"></select></td>
            </tr>
        </table>    
        
        <h1>Connect/Disconnect project and vocabulary</h1>
        <span id="connect_pv"><a href="javaScript:IO.getinputconnectProjectVocabularyTriple()">Connect Vocabulary to Project</a></span> | 
        <span id="disconnect_pv"><a href="javaScript:IO.getinputdisconnectProjectVocabularyTriple()">Disconnect Vocabulary from Project</a></span>
        
        <h1>Create new vocabulary</h1>
        <table border="0">
            <tr>
                <td><b>ls:vocab* </b></td>
                <td><input id="vocabulary" type="text" size="30" maxlength="30" onkeyup="fieldCheck()"></td>
                <td><input type="button" value="Create" id="sendvocabulary" onclick="IO.getinputvocabularyTriple(document.getElementById('vocabulary').value,'Thiery')"></td>
            </tr>
            <tr>
                <td><b>rdfs:label (optional) </b></td>
                <td><input id="label" type="text" size="50" maxlength="50"></td>
                <td>
                    <b>language </b>
                    <select id="languageLabel">
                        <option value="de">de</option>
                        <option value="en">en</option>
                        <option value="fr">fr</option>
                        <option value="pl">pl</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>rdfs:comment (optional) </b></td>
                <td><input id="comment" type="text" size="50" maxlength="50"></td>
                <td>
                    <b>language </b>
                    <select id="languageComment">
                        <option value="de">de</option>
                        <option value="en">en</option>
                        <option value="fr">fr</option>
                        <option value="pl">pl</option>
                    </select>
                </td>
            </tr>
        </table>
        
        <h1>Publish/Hide vocabulary</h1>
        <span id="publish_voc"><a href="javaScript:IO.getinputpublishVocabularyTriple()">Publish Vocabulary</a></span> | 
        <span id="hide_voc"><a href="javaScript:IO.getinputhideVocabularyTriple()">Hide Vocabulary</a></span>
        
        <br><br>
        
        <span id="info"></span>
        
        <br>
        
        <input id="setpub" type="text" size="30" maxlength="30" disabled value="select vocabulary">
        <input id="setpro" type="text" size="30" maxlength="30" disabled value="select vocabulary">
        
        <br><br>
    </body>
</html>
