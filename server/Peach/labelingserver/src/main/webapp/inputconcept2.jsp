<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>

	var Config = {};
	//Config.InputConcept = 'http://143.93.114.137/labelingserver/InputConcept';
	Config.InputConcept = 'http://localhost:8084/labelingserver/InputConcept';

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
        <input type="button" value="Upload Concept" id="uploadconcept" onclick="IO.sendURL(Config.InputConcept, document.getElementById('concepturl').value)">
        <br><br>
        <form id="data">
			Select File to Upload:<input type="file" name="fileName">
			<input type="submit" value="Upload">
		</form>
        <br><br>
        <div><span id="load"></span></div>
    </body>
</html>


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
				$('#load').html('<b>' + errorThrown + '</b>');
			},
			success: function(output) {

				$('#load').html('<b>' + query + ' loaded</b>');

			}
		});
	}

	$("form#data").submit(function(event) {
		event.preventDefault();
		var formData = new FormData($(this)[0]);
		$.ajax({
			url: Config.InputConcept,
			type: 'POST',
			data: formData,
			async: false,
			cache: false,
			contentType: false,
			processData: false,
			success: function(returndata) {
				alert(returndata.ids);
			}
		});
		return false;
	});

</script>