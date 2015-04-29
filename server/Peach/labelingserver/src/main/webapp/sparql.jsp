<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>SPARQL</title>
    </head>
    <body>
        
        <h1>SPARQL Query</h1>
        <textarea id="sparql_eingabe" cols="150" rows="20" value="" style="background-color:#dddddd;">SELECT * WHERE { ?s ?p ?o }</textarea>
        <br>
        <span id="sendlink">
            <input type="button" value="Send SPARQL" id="sendsparqltextarea" onclick="IO.sendSPARQLShowTable(Config.SPARQL,document.getElementById('sparql_eingabe').value,'json')">
            <span id="xmllink"></span><span id="jsonlink"></span>
        </span>
        <br><br>
        <span id="example">
            Example SPARQL-Querys: 
            <!--<a href="javaScript:ex1()">SELECT all</a> | -->
            <a href="javaScript:ex2()">SELECT all projects</a> | 
            <a href="javaScript:ex3()">SELECT all vocabularies</a> | 
            <a href="javaScript:ex4()">SELECT all labels</a>
        </span>
        <br><br>
        <span id="sparql_result"></span>
        <br>
        
    </body>
</html>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var Config = {};

Config.SPARQL = 'http://143.93.114.137/sesame/SPARQL';
//Config.SPARQL = 'http://localhost:8084/sesame/SPARQL';

var TS = {};
TS.vars = [];
TS.result = [];
TS.output = [];
TS.output2 = [];

var html_str = "";

var IO = {};

IO.sendSPARQLShowTable = function(url, query, format, callback, info) {
    
    query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        data: {query: query, format: format},
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
            
            // set div/span with sparql table content
            $('#sparql_result').html("");
            $('#sparql_result').html(html_str);
            
            // set links to XML and JSON
            setXMLJSONlink();
        
        }
    });
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

function setXMLJSONlink() {
    var m = mask(document.getElementById('sparql_eingabe').value);
    var i= Config.SPARQL + "?query=" + m + "&format=xml";
    var h = "<a href='"+i+"' target='_blank'>XML-Dokument</a> | ";
    $('#xmllink').html(h);
    var j= Config.SPARQL + "?query=" + m + "&format=json";
    var h = "<a href='"+j+"' target='_blank'>JSON-Dokument</a>";
    $('#jsonlink').html(h);
}

function ex1() {
    $('#sparql_eingabe').val("SELECT * WHERE { ?s ?p ?o }");
    $('#xmllink').html("");
    $('#jsonlink').html("");
    IO.sendSPARQLShowTable(Config.SPARQL,document.getElementById('sparql_eingabe').value,'json');
}
function ex2() {
    $('#sparql_eingabe').val("SELECT * WHERE { ?project a <http://143.93.114.137/vocab#Project> }");
    $('#xmllink').html("");
    $('#jsonlink').html("");
    IO.sendSPARQLShowTable(Config.SPARQL,document.getElementById('sparql_eingabe').value,'json');
}
function ex3() {
    $('#sparql_eingabe').val("SELECT * WHERE { ?vocabulary a <http://143.93.114.137/vocab#Vocabulary> }");
    $('#xmllink').html("");
    $('#jsonlink').html("");
    IO.sendSPARQLShowTable(Config.SPARQL,document.getElementById('sparql_eingabe').value,'json');
}
function ex4() {
    $('#sparql_eingabe').val("SELECT * WHERE { ?label a <http://143.93.114.137/vocab#Label> }");
    $('#xmllink').html("");
    $('#jsonlink').html("");
    IO.sendSPARQLShowTable(Config.SPARQL,document.getElementById('sparql_eingabe').value,'json');
}

$('#sparql_eingabe').keyup(function(){
    $('#xmllink').html("");
    $('#jsonlink').html("");
});

</script>