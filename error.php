<?php
$target = isset($_GET['target']) ? $_GET['target'] : '(unknown)';
?>
<div id="smar-content-inner">
	<?php
	// TODO: print messages
	?>
	<h1>Error 404</h1>
	<p>The requested page '<?php echo htmlspecialchars(strip_tags(urldecode($target))); ?>' could not be found.</p>
</div>