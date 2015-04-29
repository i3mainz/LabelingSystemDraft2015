<%-- 
    Document   : readconcepts
    Created on : 20.02.2014, 14:13:04
    Author     : florian.thiery
--%>

<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var IO = {};

IO.sendSPARQL_SKOSConceptScheme = function(url, query, callback, info) {
    
    //query = mask(query);
        
    $.ajax({
        type: 'GET',
        url: url,
        //data: {query: query},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(output) {
            
            console.log("asd");
            
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

//IO.sendSPARQL_SKOSConceptScheme("http://data.culture.fr/thesaurus/sparql", "SELECT+%3Fs+WHERE+{+%3Fs+a+%3Chttp%3A%2F%2Fwww.w3.org%2F2004%2F02%2Fskos%2Fcore%23Concept%3E+.+}");
//IO.sendSPARQL_SKOSConceptScheme("http://data.culture.fr/thesaurus/sparql", "SELECT ?s WHERE { ?s a <http://www.w3.org/2004/02/skos/core#Concept> . }");
IO.sendSPARQL_SKOSConceptScheme("http://localhost:8084/sesame/Read");

</script>

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>
        <h1>Hello World!</h1>
    </body>
</html>
