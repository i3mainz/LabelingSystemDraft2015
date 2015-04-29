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
TS.labels = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Label> }";
TS.mylabels = "SELECT ?s WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Label> . ?s <http://purl.org/dc/terms/creator> \"$creator\" .}"
TS.myvocabularies = "SELECT * WHERE { ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://143.93.114.137/vocab#Vocabulary> . ?s <http://purl.org/dc/terms/creator> \"$creator\" .}"
TS.labvoc = "SELECT ?s WHERE { ?s <http://143.93.114.137/vocab#contains> $l }";

TS.labmetadata = "SELECT ?verb ?value WHERE { <$l> ?verb ?value }";

var IO = {};

IO.getinputlabelTriple = function(creatorname) {

        var c = document.getElementById('concept').value;
        var l = document.getElementById('label').value;
        var ll = document.getElementById('languageLabel').value;
        var n = document.getElementById('note').value;
        var nl = document.getElementById('languageNote').value;
        var d = document.getElementById('definition').value;
        var dl = document.getElementById('languageDefinition').value;
        
        c = c.toLowerCase(); 
        c = specialCharacterReplace(c);
        var special = specialCharacterCheck(c);
        
        if (special==false) {
        
            var label = "{ ";
            //own ontology
            label += "<http://143.93.114.137/labels/"+c+"> ";
            label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
            label += "<http://143.93.114.137/vocab#Label> ";
            label += ".";
            // skos concept
            label += "<http://143.93.114.137/labels/"+c+"> ";
            label += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
            label += "<http://www.w3.org/2004/02/skos/core#Concept> ";
            label += ".";
            // skos label
            label += "<http://143.93.114.137/labels/"+c+"> ";
            label += "<http://www.w3.org/2004/02/skos/core#prefLabel> ";
            label += "\""+l+"\"@"+ll+" ";
            label += ".";
            if (document.getElementById('note').value != "") {
                // skos note
                label += "<http://143.93.114.137/labels/"+c+"> ";
                label += "<http://www.w3.org/2004/02/skos/core#note> ";
                label += "\""+n+"\"@"+nl+" ";
                label += ".";
            }
            if (document.getElementById('definition').value != "") {
                // skos definition
                label += "<http://143.93.114.137/labels/"+c+"> ";
                label += "<http://www.w3.org/2004/02/skos/core#definition> ";
                label += "\""+d+"\"@"+dl+" ";
                label += ".";
            }
            // dcterms creator
            label += "<http://143.93.114.137/labels/"+c+"> ";
            label += "<http://purl.org/dc/terms/creator> ";
            label += "\""+creatorname+"\" ";
            label += ".";
            // dcterms creator
            var date=new Date();
            var dd=date.getDate();  
            var mm=date.getMonth() + 1;  
            var yy=date.getYear() + 1900;  
            var HH=date.getHours();  
            var MM=date.getMinutes();  
            var d = dd+"."+mm+"."+yy+" "+HH+":"+MM+"h"; 
            label += "<http://143.93.114.137/labels/"+c+"> ";
            label += "<http://purl.org/dc/terms/date> ";
            label += "\""+d+"\" ";
            label += ".";
            //end
            label += " }";
        
        } else {
            console.log("false");
        }
        
        if (document.getElementById('label').value != "" && document.getElementById('concept').value != "" && special==false) {
            IO.sendLabelInput(Config.Input,label);
        } else {
            alert("no or false content!");
            return;
        }

}

IO.getdeletelabelTriple = function(url, creator) {
        
        var label = "{ ";
        label += "<"+url+"> ";
        label += "?p ";
        label += "?o ";
        label += ". }";
        
        if (document.getElementById('labellist').value != "") {
            IO.sendLabelDelete(Config.Delete,label,clearLabelList);
        } else {
            alert("no content!");
            return;
        }

}

IO.sendSPARQLLabelList = function(url, query, callback, info) {
        
    $('#deletelabel').hide();
    query = escape(query);
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

IO.sendSPARQLMyLabelsList = function(url, query, callback, info) {
    
    $('#deletelabel').show();
        
    query = query.replace('$creator',"Thiery");
                        
    query = escape(query);
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

IO.sendSPARQLVocabularyLabelList = function(url, query, callback, info) {
    
    var q = query;
    if (document.getElementById('vocabularylist').value != "") {
        q = q.replace('$v',"<"+document.getElementById('vocabularylist').value+">");
    } else {
        alert("no content!");
        return;
    }
    
    query = escape(query);
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
        }
    });
}

IO.sendLabelInput = function(url, input, callback, info) {
    
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
            IO.sendSPARQLMyLabelsList(Config.SPARQL,TS.mylabels);
        }
    });
}

IO.sendLabelDelete = function(url, input, callback, info) {
    
    input = escape(input);
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input, mode: 'deletewhere'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            IO.sendSPARQLMyLabelsList(Config.SPARQL,TS.mylabels);
        }
    });
}

IO.sendSPARQLMyVocabularyList = function(url, query, callback, info) {
    
    $('#deletelabel').show();
        
    query = query.replace('$creator',"Thiery");
                        
    query = escape(query);
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

IO.getinputconnectVocabularyLabelTriple = function() {
    var l = document.getElementById('labellist').value;
    var v = document.getElementById('vocabularylist').value;
    if (l!=null && v!=null && l!="" && v!="") {
        var con = "{ ";
        con += "<"+v+"> ";
        con += "<http://143.93.114.137/vocab#contains> ";
        con += "<"+l+">";
        con += ".";
        con += "<"+l+"> ";
        con += "<http://143.93.114.137/vocab#belongsTo> ";
        con += "<"+v+">";
        con += ". }";
        IO.sendUpdateCallback(Config.Input,con,IO.sendSPARQL_LabelMetadata);
    } else {
        alert("no content!");
    }
}

IO.getinputdisconnectVocabularyLabelTriple = function() {
    var l = document.getElementById('labellist').value;
    var v = document.getElementById('vocabularylist').value;
    if (l!=null && v!=null && l!="" && v!="") {
        var con = "{ ";
        con += "<"+v+"> ";
        con += "<http://143.93.114.137/vocab#contains> ";
        con += "<"+l+">";
        con += ".";
        con += "<"+l+"> ";
        con += "<http://143.93.114.137/vocab#belongsTo> ";
        con += "<"+v+">";
        con += ". }";
        IO.sendUpdateCallback(Config.Delete,con,IO.sendSPARQL_LabelMetadata);
    } else {
        alert("no content!");
    }
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
            callback(Config.SPARQL,TS.labmetadata);
        }
    });
}

IO.sendConnectionInput = function(url, input, callback, info) {
    
    input = escape(input);
    input = mask(input);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: input},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alert("connection created");
        }
    });
}

IO.sendConnectionDelete = function(url, del, callback, info) {
    
    del = escape(del);
    del = mask(del);
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {update: del},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alert("connection deleted");
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
    query = escape(query);
    query = mask(q);
        
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
            
            if (TS.bindings.length < 1) {
                alert("no vocabulary available!");
            }
            
        }
    });
}

function clearLabelList() {
    document.getElementById('labellist').options.length = 0;
}

function clearVocabularyList() {
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
    IO.sendSPARQLMyVocabularyList(Config.SPARQL,TS.myvocabularies);
    IO.sendSPARQLMyLabelsList(Config.SPARQL,TS.mylabels);
    fieldCheck();
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

function specialCharacterReplace(char) {
    
    var char_tmp = char;
        
    if (char.indexOf("â") != -1) {
        char_tmp = char.replace("â","a");
        console.log("replacement");
    } else if (char_tmp.indexOf("á") != -1) {
        char_tmp = char.replace("â","a");
        console.log("replacement");
    } else if (char_tmp.indexOf("á") != -1) {
        char_tmp = char.replace("â","a");
        console.log("replacement");
    } else if (char_tmp.indexOf("ä") != -1) {
        char_tmp = char.replace("ä","a");
        console.log("replacement");
    } else if (char_tmp.indexOf("ê") != -1) {
        char_tmp = char.replace("ê","e");
        console.log("replacement");
    } else if (char_tmp.indexOf("é") != -1) {
        char_tmp = char.replace("é","e");
        console.log("replacement");
    } else if (char_tmp.indexOf("è") != -1) {
        char_tmp = char.replace("è","e");
        console.log("replacement");
    } else if (char_tmp.indexOf("î") != -1) {
        char_tmp = char.replace("î","i");
        console.log("replacement");
    } else if (char_tmp.indexOf("í") != -1) {
        char_tmp = char.replace("í","i");
        console.log("replacement");
    } else if (char_tmp.indexOf("ì") != -1) {
        char_tmp = char.replace("ì","i");
        console.log("replacement");
    } else if (char_tmp.indexOf("ô") != -1) {
        char_tmp = char.replace("ô","o");
        console.log("replacement");
    } else if (char_tmp.indexOf("ò") != -1) {
        char_tmp = char.replace("ò","o");
        console.log("replacement");
    } else if (char_tmp.indexOf("ó") != -1) {
        char_tmp = char.replace("ó","o");
        console.log("replacement");
    } else if (char_tmp.indexOf("ö") != -1) {
        char_tmp = char.replace("ö","o");
        console.log("replacement");
    } else if (char_tmp.indexOf("û") != -1) {
        char_tmp = char.replace("û","u");
        console.log("replacement");
    } else if (char_tmp.indexOf("ü") != -1) {
        char_tmp = char.replace("ü","u");
        console.log("replacement");
    } else if (char_tmp.indexOf("ú") != -1) {
        char_tmp = char.replace("ú","u");
        console.log("replacement");
    } else if (char_tmp.indexOf("ë") != -1) {
        char_tmp = char.replace("ë","e");
        console.log("replacement");
    } else if (char_tmp.indexOf("ç") != -1) {
        char_tmp = char.replace("ç","c");
        console.log("replacement");
    } else if (char_tmp.indexOf("ã") != -1) {
        char_tmp = char.replace("ã","a");
        console.log("replacement");
    } else if (char_tmp.indexOf("å") != -1) {
        char_tmp = char.replace("å","a");
        console.log("replacement");
    } else if (char_tmp.indexOf("õ") != -1) {
        char_tmp = char.replace("õ","o");
        console.log("replacement");
    } else if (char_tmp.indexOf("ï") != -1) {
        char_tmp = char.replace("ï","i");
        console.log("replacement");
    } else if (char_tmp.indexOf("ù") != -1) {
        char_tmp = char.replace("ù","u");
        console.log("replacement");
    } else if (char_tmp.indexOf("ß") != -1) {
        char_tmp = char.replace("ß","ss");
        console.log("replacement");
    } 
    
    return char_tmp;
    
}

function fieldCheck() {
    
    var empty_c = false;
    var empty_l = false;
    var special = false;
    
    if (document.getElementById("concept").value != "") {
        empty_c = false;
    } else {
        empty_c = true;
    }
    
    if (document.getElementById("label").value != "") {
        empty_l = false;
    } else {
        empty_l = true;
    }
    
    special = specialCharacterCheck(document.getElementById("concept").value); // true if sonderzeichen
    
    if (special == false && empty_c == false) {
        document.getElementById("concept").style.backgroundColor = '#C1FFC1'; //green
    } 
    if (special == true || empty_c == true) {
        document.getElementById("concept").style.backgroundColor = '#EEA9B8'; //red
    }   
    
    if (empty_l == false) {
        document.getElementById("label").style.backgroundColor = '#C1FFC1'; //green
    } else {
        document.getElementById("label").style.backgroundColor = '#EEA9B8'; //red
    }
    
    // document.getElementById("concept").style.backgroundColor = '#C1FFC1'; //green
    // document.getElementById("concept").style.backgroundColor = '#EEA9B8'; //red
        
    if (special == false && empty_c == false && empty_l == false) {
       document.getElementById("sendlabel").style.visibility = 'visible';
       //console.log(new Date() + " " + "if");
    } else {
        document.getElementById("sendlabel").style.visibility = 'hidden';
        //console.log(new Date() + " " + "else");
    }
}

function raiseEvent (eventType, elementID)
{ 
    var o = document.getElementById(elementID); 
    if (document.createEvent) { 
        var evt = document.createEvent("Events"); 
        evt.initEvent(eventType, true, true); 
        o.dispatchEvent(evt); 
    } 
    else if (document.createEventObject) 
    {
        var evt = document.createEventObject(); 
        o.fireEvent('on' + eventType, evt); 
    } 
    o = null;
}

IO.sendSPARQL_LabelMetadata = function(url, query, callback, info) {
    
    query = query.replace("$l",document.getElementById('labellist').value);
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
            html_str += "<h1>Label Metadata</h1>";
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
        <title>New Label</title>
    </head>
    <body onLoad="init();">
        
        <h1>My labels</h1>
        <table border="1">
            <tr>
              <td>
                <a href="javaScript:IO.sendSPARQLMyLabelsList(Config.SPARQL,TS.mylabels)">My Labels</a> | 
                <span id="deletelabel"><a href="javaScript:IO.getdeletelabelTriple(document.getElementById('labellist').value,'Thiery')">Delete Label</a> | </span> 
                <a href="javaScript:IO.sendSPARQLLabelList(Config.SPARQL,TS.labels)">All Labels</a>
              </td>
              <td>
                <a href="javaScript:IO.sendSPARQLMyVocabularyList(Config.SPARQL,TS.mylabels)">My Vocabularies</a> | 
                <a href="javaScript:IO.sendSPARQLLabelVocabularyList(Config.SPARQL,TS.labvoc)">Vocabularies of selected Label</a>
              </td>
            </tr>
            <tr>
              <td><select id="labellist" size="10" style="width: 500px;" onclick="IO.sendSPARQL_LabelMetadata(Config.SPARQL,TS.labmetadata)"></select></td>
              <td><select id="vocabularylist" size="10" style="width: 500px;"></select></td>
            </tr>
        </table>    
        
        <h1>Connect/Disconnect vocabulary and label</h1>
        <span id="connect_vl"><a href="javaScript:IO.getinputconnectVocabularyLabelTriple()">Connect Label to Vocabulary</a></span> | 
        <span id="disconnect_vl"><a href="javaScript:IO.getinputdisconnectVocabularyLabelTriple()">Disconnect Label from Vocabulary</a></span>
        
        <h1>Create new label</h1>
        <table border="0">
            <tr>
                <td><b>skos:concept* </b></td>
                <td><input id="concept" type="text" size="30" maxlength="30" onkeyup="fieldCheck()"></td>
                <td><input type="button" value="Create" id="sendlabel" onclick="IO.getinputlabelTriple('Thiery')"></td>
            </tr>
            <tr>
                <td><b>skos:prefLabel* </b></td>
                <td><input id="label" type="text" size="50" maxlength="50" onkeyup="fieldCheck()"></td>
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
                <td><b>skos:note (optional) </b></td>
                <td><input id="note" type="text" size="50" maxlength="50"></td>
                <td>
                    <b>language </b>
                    <select id="languageNote">
                        <option value="de">de</option>
                        <option value="en">en</option>
                        <option value="fr">fr</option>
                        <option value="pl">pl</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><b>skos:definition (optional) </b></td>
                <td><input id="definition" type="text" size="50" maxlength="50"></td>
                <td>
                    <b>language </b>
                    <select id="languageDefinition">
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
