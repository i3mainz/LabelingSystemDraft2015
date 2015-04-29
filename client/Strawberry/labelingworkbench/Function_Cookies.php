<?php
	// set or read cookie
	if (!isset($_COOKIE["labelingsystem-menulang"])) {
		// set cookie "labelingsystem-menulang" with default language
		GlobalVariables::$menuLanguage = "de";
		setcookie("labelingsystem-menulang",GlobalVariables::$menuLanguage,time() + 60*60*24*360*20); // Cookie erlischt in 20 Jahren
	} else {
		// read cookie
		GlobalVariables::$menuLanguage = $_COOKIE["labelingsystem-menulang"];
	}
	
	// language change with form
	if (isset($_POST["menulanguage-select"])) {
		// read language out of form and rewrite cookie
		GlobalVariables::$menuLanguage = $_POST["menulanguage-select"];
		setcookie("labelingsystem-menulang",GlobalVariables::$menuLanguage,time() + 60*60*24*360*20); // Cookie erlischt in 20 Jahren
	}
	
	if (!isset($_COOKIE["labelingsystem-preflang"])) {
		// set cookie "labelingsystem-preflang" with default language
		GlobalVariables::$prefLanguage = "de";
		setcookie("labelingsystem-preflang",GlobalVariables::$prefLanguage,time() + 60*60*24*360*20); // Cookie erlischt in 20 Jahren
	} else {
		// read cookie
		GlobalVariables::$prefLanguage = $_COOKIE["labelingsystem-preflang"];
	}
	
	// language change with form
	if (isset($_POST["preflanguage-select"])) {
		// read language out of form and rewrite cookie
		GlobalVariables::$prefLanguage = $_POST["preflanguage-select"];
		setcookie("labelingsystem-preflang",GlobalVariables::$prefLanguage,time() + 60*60*24*360*20); // Cookie erlischt in 20 Jahren
	}
?>