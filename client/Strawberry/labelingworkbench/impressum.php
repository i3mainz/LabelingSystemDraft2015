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
	<h1>About</h1>
	<h2>The Labeling System</h2>
	<br>
	<div id='left-nav'>";
	
//navigation
include("left-nav.php");
echo "
</div>

<div id='maingrey'>
<p>&nbsp;</p>

<center>
<h1>Partner</h1>
<br>
<a href='http://www.ieg-mainz.de' target='_blank'><img src='models/site-templates/images/ieg.gif' height='200'></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='http://www.i3mainz.hs-mainz.de' target='_blank'><img src='models/site-templates/images/i3mainz-logo.png' height='200'></a>
<p>&nbsp;</p>
<p><b>
Florian Thiery (Developer, Idea and Concept) [i3mainz] </br>
Prof. Dr. phil. Kai Christian Bruhn (Idea and Concept) [i3mainz] </br>
Dr. Michael Piotrowski (Idea and Concept) [IEG]</br>
Giovanni Colavizza (Idea and Concept) [IEG]</br>
</b>
</p>
</center>
<p>&nbsp;</p>

<center>
<h1>Project</h1>
<p>Find out more about the project at the <a href='http://i3mainz.hs-mainz.de/de/projekte/labelingsystem' target='_blank'>i3mainz website</a>.</p>
</center>
<p>&nbsp;</p>

<center>
<h1>Licences</h1>
<p>The created Labels are licenced by <a href='https://creativecommons.org/licenses/by-sa/4.0/' target='_blank'>CC BY SA 4.0</a>&nbsp;<img src='models/site-templates/images/ccbysa.png'></p>
<p>The Labeling System is developed by i3mainz - Institute for Spatial Information and Surveying Technology under a <a href='https://github.com/i3mainz/LabelingSystem/blob/master/LICENSE' target='_blank'>MIT License</a>.</p>
<p>The User Cake Management System is developed by Adam Davis and Jonathan Cassels under a <a href='http://usercake.com/licence.txt' target='_blank'>MIT License</a>.</p>
<p>The sourcecode is available on <a href='https://github.com/i3mainz/LabelingSystem' target='_blank'>GitHub</a>.</p>
</center>
<p>&nbsp;</p>

</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>
