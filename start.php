<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* start.php                         *
*                                   *
************************************/

// extract file name
$self = explode('/', $_SERVER['SCRIPT_NAME']);
$self = $self[count($self)-1];
$subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';

// check for type of call (direct/AJAX)
// on direct call, redirect to index for full layout
if(!isset($_GET['smar_include']) || $_GET['smar_include'] != 'true') {
	$url = $self;
	$url .= (strlen($_SERVER['QUERY_STRING']) != 0) ? '?'.$_SERVER['QUERY_STRING'] : '';
	header( 'location: index.php?page='.urlencode($url) );
} else {
	$topinclude = 0;
}

require_once('_functions/_functions.php');
require_once('inc_session_check.php');
?>
<div id="smar-content-inner">
	<h1>Welcome</h1>
	<?php
	// print messages
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
	?>
	<p>Choose an action to start.</p>
	<p><a href="logout.php">Logout</a></p>
	<br><br>
	<div>Some icons made by <a href="http://www.flaticon.com/authors/situ-herrera" title="Situ Herrera">Situ Herrera</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> are licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a></div>
</div>