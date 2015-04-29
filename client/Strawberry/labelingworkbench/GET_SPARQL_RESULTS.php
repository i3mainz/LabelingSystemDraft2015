<?php

	if(isset($_POST['searchstr'])) {
	ob_start(); // activate Ausgabepufferung
		try {
			$rslt = array();
			if (isset($_POST['searchstr']) && isset($_POST['sparql_name']) && isset($_POST['sparql_url']) && isset($_POST['sparql_query'])) {
				
				// parse params
				$substring = $_POST['searchstr'];
				$thesaurus = $_POST['sparql_name'];
				$url = $_POST['sparql_url'];
				$query = $_POST['sparql_query'];
				
				// send request
				$query = str_replace("?searchstring",$substring,$query);
				set_time_limit(3600000);
				$query = urlencode($query);
				$c = curl_init($url.$query);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($c);
				if (curl_error($c)) die(curl_error($c));
				curl_close($c);

				$xml = simplexml_load_string($response);
				foreach ($xml as $binding) {
					$count = $binding->count();
				}
				
				// parse output
				$rslt = array();
				foreach ($xml->results->result as $result) {
					$s = "";
					$scheme = "undefined";
					$label = "";
					foreach ($result->binding as $binding) {
						if ((string) $binding['name'] == "s") {
							$s = (string) $binding->uri;
						} else if ((string) $binding['name'] == "scheme") {
							$scheme = (string) $binding->uri;
						} else if ((string) $binding['name'] == "label") {
							$label = (string) $binding->literal;
						}
					}
					$rslt[] = array('concept' => $s, 'scheme' => $scheme, 'label' => $label);
				}
			}
			print_r(json_encode($rslt));
			header('Content-Type: application/json; charset=utf8');
		} catch (Exception $e) {
			header($_SERVER["SERVER_PROTOCOL"] . ' 500 Server Error');
			header('Status:  500 Server Error');
		}
		die();
	}
	
?>