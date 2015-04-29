<%-- 
    Document   : index
    Created on : 18.02.2014, 09:33:52
    Author     : florian.thiery
--%>

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Labeling System APPS</title>
        <style type="text/css">
            a:link    {color: blue;text-decoration:underline}
            a:visited {color: blue;text-decoration:underline}
            a:hover   {color: blue;text-decoration:none}
            a.hover   {color: blue;text-decoration:none}
            a:active  {color: blue;text-decoration:underline}
        </style>
    </head>
    <body>
        <center>
        <h1>The Labeling System APPS</h1>
        <a href='http://www.ieg-mainz.de' target='_blank'><img src='ieg.gif'></a>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a href='http://www.i3mainz.fh-mainz.de' target='_blank'><img src='i3mainz.png'></a>
        <br><br><br>
        <table border="0">
            <tr>
                <td width="40%">
                    <b>LabelingSystem: </b><a href="http://143.93.114.137/client/" target="_blank">Login and Register</a>
                    <br><br>
                    <b>REST Interface: </b><a href="http://143.93.114.137/rest/" target="_blank">REST</a>
                    <br>
                    <b>SPARQL Endpoint: </b><a href="http://143.93.114.137/sesame/SPARQL" target="_blank">Endpoint</a>
                    <br>?query={SPARQL query} & format={xml;json;csv}
                    <br><br>
                    <b>SPARQL the Labels: </b><a href="sparql_label.jsp" target="_blank">SPARQL Label</a>
                    <br>
                    <b>SPARQL the Concepts: </b><a href="sparql_concept.jsp" target="_blank">SPARQL Concept</a>
                </td>
                <td width="40%"><img src="ls_apps_tiny.png"></td>
            </tr>
        </table>
        
        <br><br>
        
        </center>
    </body>
</html>
