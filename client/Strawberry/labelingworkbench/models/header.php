<?php
/*
UserCake Version: 2.0.2
http://usercake.com
*/
echo "
<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<title>".$websiteName."</title>
<link href='".$template."' rel='stylesheet' type='text/css' />
<script src='models/funcs.js' type='text/javascript'>
<script src=\"http://code.jquery.com/jquery-latest.js\" type='text/javascript'></script>
<script src='gui.js'></script>
<script src='config.js'></script>
<script src='utils.js'></script>
<script>
var user = decodeURIComponent('<?php echo urlencode($loggedInUser->displayname);?>');
</script>
</head>";

?>
