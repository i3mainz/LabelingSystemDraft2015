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
TS.projects = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Project> }";
TS.myprojects = "SELECT * WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Project> . ?s <http://purl.org/dc/terms/creator> \"$creator\" .}"
TS.provoc = "SELECT ?s WHERE { $p <http://143.93.114.137/vocab#contains> ?s }";

TS.prometadata = "SELECT ?verb ?value WHERE { <$p> ?verb ?value }";

var IO = {};

IO.getinputprojectTriple = function(p, creator) {
    
        var l = document.getElementById('label').value;
        var ll = document.getElementById('languageLabel').value;
        var c = document.getElementById('comment').value;
        var cl = document.getElementById('languageComment').value;
        
        var project = "{ ";
        project += "<http://143.93.114.137/project#"+p+"> ";
        project += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        project += "<http://143.93.114.137/vocab#Project> ";
        project += ".";
        // dcterms creator
        project += "<http://143.93.114.137/project#"+p+"> ";
        project += "<http://purl.org/dc/terms/creator> ";
        project += "\""+creator+"\" ";
        project += ".";
        if (document.getElementById('label').value != "") {
            // rdfs label
            project += "<http://143.93.114.137/project#"+p+"> ";
            project += "<http://www.w3.org/2000/01/rdf-schema#label> ";
            project += "\""+l+"\"@"+ll+" ";
            project += ".";
        }
        if (document.getElementById('comment').value != "") {
            // rdfs comment
            project += "<http://143.93.114.137/project#"+p+"> ";
            project += "<http://www.w3.org/2000/01/rdf-schema#comment> ";
            project += "\""+c+"\"@"+cl+" ";
            project += ".";
        }
        project += "}";
        
        if (document.getElementById('project').value != "") {
            IO.sendProjectInput(Config.Input,project);
        } else {
            alert("no content!");
            return;
        }

}

IO.getdeleteprojectTriple = function(url, creator) {
    
        var project = "{ ";
        project += "<"+url+"> ";
        project += "?p ";
        project += "?o ";
        project += ". }";
        
        if (document.getElementById('projectlist').value != "") {
            IO.sendProjectDelete(Config.Delete,project,clearProjectList);
        } else {
            alert("no content!");
            return;
        }

}

IO.sendSPARQLProjectList = function(url, query, callback, info) {
    
    $('#deleteproject').hide();
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
                var x = document.getElementById("vocabularylist");
                var option = document.createElement("option");
                option.text = TS.bindings[i];
                x.add(option);
            }
            
            if (TS.bindings.length < 1) {
                alert("no vocabulary available!");
            }
        }
    });
}

IO.sendProjectInput = function(url, input, callback, info) {
    
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
            IO.sendSPARQLMyProjectList(Config.SPARQL,TS.myprojects);
        }
    });
}

IO.sendProjectDelete = function(url, input, callback, info) {
    
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input, mode: 'deletewhere'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            IO.sendSPARQLMyProjectList(Config.SPARQL,TS.myprojects);
        }
    });
}

function clearProjectList() {
    document.getElementById('projectlist').options.length = 0;
}

function clearProjectVocabularyList() {
    document.getElementById('vocabularylist').options.length = 0;
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
}

function fieldCheck() {
    
    var empty_p = false;
    var empty_l = false;
    var special = false;
    
    if (document.getElementById("project").value != "") {
        empty_p = false;
    } else {
        empty_p = true;
    }
    
    special = specialCharacterCheck(document.getElementById("project").value); // true if sonderzeichen
    
    if (special == false && empty_p == false) {
        document.getElementById("project").style.backgroundColor = '#C1FFC1'; //green
    } 
    if (special == true || empty_p == true) {
        document.getElementById("project").style.backgroundColor = '#EEA9B8'; //red
    }   
        
    if (special == false && empty_p == false) {
       document.getElementById("sendproject").style.visibility = 'visible';
    } else {
        document.getElementById("sendproject").style.visibility = 'hidden';
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

IO.sendSPARQL_ProjectMetadata = function(url, query, callback, info) {
    
    query = query.replace("$p",document.getElementById('projectlist').value);
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
            html_str += "<h1>Project Metadata</h1>";
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
            for (var i=0; i<TS.output2.length; i++) {
                var split = TS.output2[i].split("__");
                html_str += "<td>"+split[1]+"</td>";
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
}

</script>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>New Project</title>
    </head>
    <body onLoad="init();">
        
        <h1>My projects</h1>
        <table border="1">
            <tr>
              <td>
                <a href="javaScript:IO.sendSPARQLMyProjectList(Config.SPARQL,TS.myprojects)">My Projects</a> | 
                <span id="deleteproject"><a href="javaScript:IO.getdeleteprojectTriple(document.getElementById('projectlist').value,'Thiery')">Delete Project</a> | </span>
                <a href="javaScript:IO.sendSPARQLProjectList(Config.SPARQL,TS.projects)">All Projects</a>
              </td>
              <td>
                <a href="javaScript:IO.sendSPARQLProjectVocabularyList(Config.SPARQL,TS.provoc)">Vocabularies of selected Project</a>
              </td>
            </tr>
            <tr>
              <td><select id="projectlist" size="10" style="width: 500px;" onclick="IO.sendSPARQL_ProjectMetadata(Config.SPARQL,TS.prometadata)"></select></td>
              <td><select id="vocabularylist" size="10" style="width: 500px;"></select></td>
            </tr>
        </table>
        
        <h1>Create new project</h1>
        <table border="0">
            <tr>
                <td><b>ls:project* </b></td>
                <td><input id="project" type="text" size="30" maxlength="30" onkeyup="fieldCheck()"></td>
                <td><input type="button" value="Create" id="sendproject" onclick="IO.getinputprojectTriple(document.getElementById('project').value,'Thiery')"></td>
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
        
        <span id="info"></span>
    </body>
</html>
