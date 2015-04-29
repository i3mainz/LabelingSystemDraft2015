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
	<h1>The Labeling System</h1>
	<h2>supported by the UserCake Management System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");

echo "
</div>

<div id='maingrey'>
<br><br><br>
<center>
<h1>A System of the i3mainz and IEG</h1>
<br>
<h2>Leibnitz Institut für Europäische Geschichte</h2>
<h2>Institut für Raumbezogene Informations- und Messtechnik</h2>
<br><br>
<a href='http://www.ieg-mainz.de' target='_blank'><img src='models/site-templates/images/ieg.gif' height='200'></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='http://www.i3mainz.hs-mainz.de' target='_blank'><img src='models/site-templates/images/i3mainz-logo.png' height='200'></a>
</center>
<br><br><br>
<h1><a href='login.php'>Login</a> | <a href='register.php'>Register</a></h1>
<br><br><br>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>
