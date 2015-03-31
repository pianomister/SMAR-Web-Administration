<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* login.php                         *
*                                   *
************************************/

require_once('_functions/_functions.php');
require_once('inc_login.php');
if(isset($_GET['action']))
	if($_GET['action'] == 'logout')
		$SMAR_MESSAGES['success'][] = "Sie wurden ausgeloggt.";
	else if($_GET['action'] == 'timeout')
		$SMAR_MESSAGES['warning'][] = "Ihre Sitzung ist abgelaufen, bitte erneut einloggen.";
	else if($_GET['action'] == 'sessionFehler')
		$SMAR_MESSAGES['error'][] = "In Ihrer Sitzung waren widersprüchliche Daten gespeichert. Bitte erneut einloggen.";
?>
<!doctype html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<title>SMAR Web Administration</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="http://fonts.googleapis.com/css?family=Roboto:400,300,700" rel="stylesheet" type="text/css">
	<link href="smar.css" rel="stylesheet">
</head>
<body>
	<?php /*Print messages*/ if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); } ?>
	<form id="login-form" name="login" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div id="smar-login">
		<div id="smar-login-box" class="dark-ui">
			<div id="smar-logo"></div>
			<div class="form-box swap-order">
				<input type="text" name="loginUsername" id="loginUsername" placeholder="Username" />
				<label for="loginUsername">Username</label>
			</div>
			<div class="form-box swap-order">
				<input type="password" name="loginPassword" id="loginPassword" placeholder="Password" />
				<label for="loginPassword">Password</label>
			</div>
			<input type="submit" value="Login" name="sendLogin" class="flt" />
		</div>
	</div>
	</form>
</body>
</html>