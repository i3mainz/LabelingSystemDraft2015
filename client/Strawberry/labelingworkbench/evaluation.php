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
	<h1>Evaluation and Problems</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");
echo "
</div>

<div id='maingrey'>
<br><br>
<center>
<h1>Questions? Problems?</h1>
<br><br>
<a href='http://www.i3mainz.hs-mainz.de' target='_blank'><img src='models/site-templates/images/i3mainz-logo.png' height='200'></a>
<br><br>
<b><a href='http://i3mainz.hs-mainz.de/de/personal/florian.thiery' target='_blank'>Florian Thiery [i3mainz]</a></b>
</center>
<br>
<br>

<h1>Evaluation</h1>
<center>
<p>
Please take a few minutes to answer the following questions to improve the Labeling System.
<br>
Send the mail to Florian Thiery (Email Address above). Thank you!
</p>
<h2>
<b>
<p>
Was it useful?
<br>
Was it working well?
<br>
What kind of features are missing?
<br>
What would you like to improve in the GUI?
<br>
Something more you want to comment.
</p>
</b>
</h2>
</center>
<p></p>

<br>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>
