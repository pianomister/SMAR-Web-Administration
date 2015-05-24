<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>SMAR Web Administration</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="favicon.ico" rel="shortcut icon">
	
	<link href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" asnyc>
	<link href="css/materialdesignicons.min.css" rel="stylesheet">
	<link href="css/jquery.colorbox.css" rel="stylesheet">
	<link href="css/jquery.autocomplete.css" rel="stylesheet">
	<link href="css/smar.css" rel="stylesheet">
	
	<script>
	// JWT token for API calls
	window.loginJWTToken = '<?php echo $_SESSION['loginJWTToken']; ?>';
	</script>
	<script src="js/interact-1.2.4.min.js"></script>
	<script src="js/jquery.min.js"></script>
	<script src="js/plugins.js"></script>
	<script src="js/smar-frontend.js"></script>
</head>