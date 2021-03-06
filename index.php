<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* index.php                         *
*                                   *
************************************/

$topinclude = 1;
require_once('_functions/_functions.php');
require_once('inc_session_check.php');

include('inc_header.php')
?>
<body>
	<div id="smar-wrapper">
		<nav id="nav-main">
			<ul>
				<li><a id="smar-logo" href="start.php"></a></li>
				<li><a id="nav-main-1" href="products.php"><i class="nav-icon nav-icon-cart"></i><span>Products &amp; Units</span></a></li>
				<li><a id="nav-main-2" href="shelves.php"><i class="nav-icon nav-icon-shelf"></i><span>Shelves &amp; Sections</span></a></li>
				<li><a id="nav-main-3" href="orders.php"><i class="nav-icon nav-icon-list"></i><span>Orders</span></a></li>
				<!--<li><a id="nav-main-4" href=""><i class="nav-icon nav-icon-map"></i><span>Market Map</span></a></li>-->
				<li><a id="nav-main-5" href="users.php"><i class="nav-icon nav-icon-user"></i><span>User Management</span></a></li>
				<?php if($_SESSION['loginRole'] >= 50) { ?><li><a id="nav-main-6" href="devices.php"><i class="nav-icon nav-icon-device"></i><span>Devices</span></a></li><?php } ?>
			</ul>
		</nav>
		<section id="smar-content">
		<?php

			if(isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] != 'start.php') {
				
				$page = urldecode($_GET['page']);
				$page_exploded = explode('?', $page);
	
				// add GET params from page redirect to $_GET
				$params = smar_get_query_array($page);
				$params['smar_include'] = true;
				$params['smar_nav'] = true;
				foreach($params as $k => $v) {
					$_REQUEST[$k] = $v;
					$_GET[$k] = $v;
				}
	
				if(file_exists($page_exploded[0]))
					include $page_exploded[0];
				else
					echo file_get_contents(SMAR_SITE_URL.SMAR_CURRENT_DIR.'error.php?target='.urlencode($page));
				
			} else {
				$params['smar_include'] = true;
				$params['smar_nav'] = true;
				foreach($params as $k => $v)
					$_GET[$k] = $v;
				include 'start.php';
			}
			?>
		</section>
	</div>
	<div id="smar-loading" class="smar-overlay"><div><img src="img/ajax-loader.gif"></div></div>
	<div id="smar-timeout" class="smar-overlay"><div><span>Your session has timed out.<br>Please login again.<br><button onclick="document.location.href='login.php';" class="raised">Login</button></span></div></div>
</body>
</html>