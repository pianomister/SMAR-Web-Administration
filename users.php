<?php
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
}

// include subnav if requested
if(isset($_GET['smar_nav']) && $_GET['smar_nav'] == 'true') {
	
	?>
	<nav id="nav-page">
		<ul>
			<li><a href="<?php echo $self; ?>" <?php echo ($subpage == '') ? 'class="smar-active"' : ''; ?>>First page</a></li>
			<li><a href="<?php echo $self.'?subpage=new'; ?>" <?php echo ($subpage == 'new') ? 'class="smar-active"' : ''; ?>>Test</a></li>
		</ul>
	</nav>
	<?php
}
?>
<div id="smar-content-inner">
	<?php
	// TODO: print messages

	// page content
	switch($subpage) {
		
		case 'new':
			?>
			<h1>Add new user</h1>
			<?php
			break;
		default:
			?>
			<h1>User Management</h1>
			<?php
	}
	?>
</div>