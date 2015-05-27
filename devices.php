<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
*                                   *
*                                   *
************************************/

// extract file name
$self = 'devices.php';
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


// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>Device manager</a></li>
			<li><a href="<?php echo $self.'?subpage=adddevice'; ?>" <?php echo ($subpage == 'adddevice') ? 'class="smar-active"' : ''; ?>>Add device</a></li>
		</ul>
	</nav>
	<?php
}
?>
<div id="smar-content-inner">
	<?php
	// print messages
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }

	// page content
	switch($subpage) {
		
		case 'adddevice':
			?>
			<h1>Add device</h1>
			<?php
			break;
		default:
			?>
			<h1>Device manager</h1>
			<?php
	}
	?>
</div>