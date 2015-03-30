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
				<li><a href="users.html"><i class="nav-icon nav-icon-user"></i><span>User Management</span></a></li>
				<li><a href="#"><i class="nav-icon nav-icon-cog"></i><span>Settings</span></a></li>
			</ul>
		</nav>
		<section id="smar-content">
			<?php
			if(isset($_GET['page']) && !empty($_GET['page'])) {
				
				$page = urldecode($_GET['page']);
				if(strpos(' '.$page, '?'))
					$page .= '';
			
				echo file_get_contents($page);
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