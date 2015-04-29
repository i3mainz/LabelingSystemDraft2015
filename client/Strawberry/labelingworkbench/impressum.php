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
	<h1>Impressum</h1>
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
<h1>The Partner</h1>
<br><br>
<a href='http://www.ieg-mainz.de' target='_blank'><img src='models/site-templates/images/ieg.gif' height='200'></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='http://www.i3mainz.hs-mainz.de' target='_blank'><img src='models/site-templates/images/i3mainz-logo.png' height='200'></a>
</center>
<br>
<br>
<h1>Labels</h1>
<center>
<p>The created Labels are licenced by <a href='https://creativecommons.org/licenses/by-sa/4.0/' target='_blank'>CC BY SA 4.0</a>&nbsp;<img src='models/site-templates/images/ccbysa.png'></p>
</center>
<p></p>
<br>
<h1>userCake Managementsystem</h1>
<center>
<p>Copyright (c) 2009-2012</p>
<p>Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:</p>
<p>The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.</p>
<p>THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.</p>
</center>
<br><br><br>
</div>";

require_once("models/footerline.php");
echo "</div>";

//footer
echo "
</body>
</html>";

?>
