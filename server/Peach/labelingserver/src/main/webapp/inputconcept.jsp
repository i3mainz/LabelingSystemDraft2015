<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

var Config = {};

Config.InputConcept = 'http://143.93.114.137/sesame/InputConcept';
//Config.InputConcept = 'http://localhost:8084/sesame/InputConcept';

Config.Test = 'http://143.93.114.137/sesame/ReadSendRDF';
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

<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>JSP Page</title>
    </head>
    <body>
        <input id="concepturl" type="text" size="50" maxlength="200">
        <input type="button" value="Upload Concept" id="uploadconcept" onclick="IO.sendURL(Config.InputConcept,document.getElementById('concepturl').value)">
        <br><br>
        <input type="file" id="files" name="files[]" multiple />
        <script>
            document.getElementById('files').addEventListener('change', FileSelect.handleFileSelect, false);
        </script>
        <br><br>
        <div><span id="load"></span></div>
    </body>
</html>


<script>
    
IO = {};

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