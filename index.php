<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* index.php							*
*									*
************************************/

$topinclude = 1;
require_once('_functions/_functions.php');
require_once('inc_session_check.php');
?>
<!doctype html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title>SMAR Web Administration</title>
	<link href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">
	<link href="smar.css" rel="stylesheet">
</head>
<body>
	<div id="smar-wrapper">
		<nav id="nav-main">
			<ul>
				<li id="smar-logo"></li>
				<li><a href="#"><i class="nav-icon nav-icon-cart"></i><span>Products &amp; Units</span></a></li>
				<li><a href="#"><i class="nav-icon nav-icon-shelf"></i><span>Shelves &amp; Sections</span></a></li>
				<li><a href="#" class="smar-active"><i class="nav-icon nav-icon-list"></i><span>Orders</span></a></li>
				<li><a href="http://example.com/"><i class="nav-icon nav-icon-map"></i><span>Market Map</span></a></li>
				<li><a href="users.php"><i class="nav-icon nav-icon-user"></i><span>User Management</span></a></li>
				<li><a href="TEMPLATE.php"><i class="nav-icon nav-icon-cog"></i><span>Settings</span></a></li>
			</ul>
		</nav>
		<section id="smar-content">
			<?php

function siteURL()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	return $protocol.$domainName;
}
define( 'SMAR_SITE_URL', siteURL() );


function currentDir() {
	$folders = explode('/', $_SERVER['REQUEST_URI']);
	unset($folders[count($folders)-1]);
	return implode('/', $folders).'/';
}
define( 'SMAR_CURRENT_DIR', currentDir() );


			if(isset($_GET['page']) && !empty($_GET['page'])) {
				
				$page = urldecode($_GET['page']);

				if( strpos($page, '?') )
					$page .= '&';
				else
					$page .= '?';
				$page .= 'smar_include=true&smar_nav=true';

				if(file_exists(SMAR_CURRENT_DIR.$page))
					echo file_get_contents(SMAR_SITE_URL.SMAR_CURRENT_DIR.$page);
				else
					echo file_get_contents(SMAR_SITE_URL.SMAR_CURRENT_DIR.'error.php?target='.urlencode($page));
				
			} else {
				?>
				<h1>Welcome</h1>
				<p>Choose an action to start.</p>
				<?php
			}
			?>
		</section>
	</div>
	<div id="smar-loading">Loading ...</div>
	<script src="js/jquery.min.js"></script>
	<script src="js/plugins.js"></script>
	<script src="js/smar-frontend.js"></script>
</body>
</html>