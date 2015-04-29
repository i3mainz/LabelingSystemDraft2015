<%-- 
    Document   : test2
    Created on : 19.02.2014, 10:07:19
    Author     : florian.thiery
--%>

<script>

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

</script>

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>
        <span id="info"></span>
    </body>
</html>


