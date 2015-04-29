<script src="http://code.jquery.com/jquery-latest.js"></script>
<script src="config.js"></script>
<script>

var Config = {};

//Config.InputConcept = 'http://143.93.114.137/sesame/InputConcept';
//Config.DeleteConcept = 'http://143.93.114.137/sesame/DeleteConcept';
//Config.Test = 'http://143.93.114.137/sesame/ReadSendRDF';

//Config.InputConcept = 'http://localhost:8084/sesame/InputConcept';
//Config.DeleteConcept = 'http://localhost:8084/sesame/DeleteConcept';
//Config.Test = 'http://localhost:8084/sesame/ReadSendRDF';

FileSelect = {};
FileSelect.filename = "";
FileSelect.handleFileSelect = function(evt) {
		var files = evt.target.files; // FileList object
		filename = files[0].name;
                FileSelect.startRead(files[0]); 
	}    
FileSelect.startRead = function(projectFile) {  
		if(projectFile){
			FileSelect.getAsText(projectFile);
		}
	}
FileSelect.getAsText = function(readFile) {
		var reader = new FileReader();
  
		// Read file into memory as UTF-8      
		reader.readAsText(readFile, "UTF-8");
  
		// Handle progress, success, and errors
		reader.onprogress = FileSelect.updateProgress;
		reader.onload = FileSelect.loaded;
		reader.onerror = FileSelect.errorHandler;
	}
FileSelect.updateProgress = function(evt) {
		if (evt.lengthComputable) {
			// evt.loaded and evt.total are ProgressEvent properties
			var loaded = (evt.loaded / evt.total);
			if (loaded < 1) {
			}
		}
	}
FileSelect.loaded = function(evt) {  
		// Obtain the read file data    
		var fileString = evt.target.result;   
	
		//Senden an das Servlet
		var concept = fileString;
                IO.sendFile(Config.Test,concept,filename);
	}
FileSelect.errorHandler = function(evt) {
		if(evt.target.error.name == "NotReadableError") {
			console.err("not readable");
		}
	}
   
</script>

<?php

require_once("models/config.php");
if (!securePage($_SERVER['PHP_SELF'])){die();}
require_once("models/header.php");

//header
echo "
<body>
<div id='wrapper'>";
require_once("models/headline.php");
echo "	
<div id='content'>
	<br>
	<h1>Input Concept</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

//content
echo "
</div>
<div id='main'>
<br>

<center>
<b>Upload SKOS Concept-Scheme from URL: </b><input id='concepturl' type='text' size='50' maxlength='200'>
<input type='button' value='Upload Concept' id='uploadconcept' onclick='IO.sendURL(Config.InputConcept,document.getElementById(\"concepturl\").value)'>
<br><br>
<b>Upload SKOS Concept-Scheme from local File: </b><input type='file' id='files' name='files[]' multiple />
<script>
	document.getElementById(\"files\").addEventListener(\"change\", FileSelect.handleFileSelect, false);
</script>
<br><br>
<h1>Upload/Delete SKOS SPARQL endpoint</h1>
<br>
<b>SPARQL query url: </b><input id='sparqlurl' type='text' size='50' maxlength='200'>
<br>
<b>Endpoint name: </b><input id='sparqlname' type='text' size='50' maxlength='200'>
<br><br>
<center>
<span id='upload_ep'><a href='javaScript:IO.getinputconnectProjectVocabularyTriple()'>Upload new SPARQL endpoint </a></span> | 
<span id='delete_ep'><a href='javaScript:IO.getinputdisconnectProjectVocabularyTriple()'>Delete SPARQL endpoint (please type in \"Endpoint name\")</a></span>
<br><br>
<i>NOTE: The name should a 'speaking name' without any special chars.<br>
The URL should be the SPARQL endpoint to get a XML output if you write a SPARQL query next to it.<br>
Example: http://data.culture.fr/thesaurus/sparql?query=<br>
or http://vocab.getty.edu/sparql.xml?query=</i>
</center>
<br>
<div><span id='load'></span></div>
</center>

</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>


<script>
    
IO = {};
IO.sendURL = function(url, query, callback, info) {
    
    $('#load').html('<b>Loading...</b> <img src="loading.gif" height="40">');
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {url: query},
        error: function(jqXHR, textStatus, errorThrown) {
            //alert(errorThrown);
            $('#load').html('<b>'+errorThrown+'</b>');
        },
        success: function(output) {
            
            $('#load').html('<b>'+query+' loaded</b>');
            
        }
    });
}

IO.sendFile = function(url, file, filename, callback, info) {
    
    //query = mask(query);
    
    $('#load').html('<b>Loading...</b> <img src="loading.gif" height="40">');
        
    $.ajax({
        type: 'POST',
        url: url,
        data: {file: file},
        error: function(jqXHR, textStatus, errorThrown) {
            //alert(errorThrown);
            $('#load').html('<b>'+errorThrown+'</b>');
        },
        success: function(output) {
            
            IO.sendURL(Config.InputConcept,url)
            $('#load').html('<b>'+filename+' loaded</b>');
            
        }
    });
}

IO.getinputconnectProjectVocabularyTriple = function() {
    var url = document.getElementById('sparqlurl').value;
    var name = document.getElementById('sparqlname').value;
    if (url!=null && name!=null && url!="" && name!="") {
        var con = "{ ";
        con += "<http://"+Config.InstanceHOST+"/concept#"+name+"> ";
        con += "<http://www.w3.org/1999/02/22-rdf-syntax-ns#type> ";
        con += "<http://"+Config.VocabHOST+"/vocab#Concept>";
        con += ". ";
		con += "<http://"+Config.InstanceHOST+"/concept#"+name+"> ";
        con += "<http://"+Config.VocabHOST+"/vocab#BaseURI> ";
        con += "\""+url+"\"";
        con += ". ";
		con += "}";
        IO.sendUpdate(Config.InputConcept,con);
    } else {
        alert("no content!");
    }
}

IO.getinputdisconnectProjectVocabularyTriple = function() {
    var name = document.getElementById('sparqlname').value;
    if (name!=null && name!="") {
        var con = "{ ";
        con += "<http://143.93.114.137/concept#"+name+"> ";
        con += "?p ";
        con += "?o ";
        con += ". }";
        IO.sendDelete(Config.DeleteConcept,con);
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
            alert("concept saved");
        }
    });
}

IO.sendDelete = function(url, up, callback, info) {
    
    up = escape(up);
    up = mask(up);
        
    $.ajax({
        beforeSend: function(req) {
            req.setRequestHeader("Content-Type","application/x-www-form-urlencoded ; charset=UTF-8");
	},
        type: 'POST',
        url: url,
        data: {update: up, mode: 'deletewhere'},
        error: function(jqXHR, textStatus, errorThrown) {
            alert(errorThrown);
        },
        success: function(xml) {
            alert("concept deleted");
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

</script>