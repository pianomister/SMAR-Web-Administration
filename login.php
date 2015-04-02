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
		$SMAR_MESSAGES['success'][] = "You have been logged out.";
	else if($_GET['action'] == 'timeout')
		$SMAR_MESSAGES['warning'][] = "Your session was timed out, please login again.";
	else if($_GET['action'] == 'sessionFehler')
		$SMAR_MESSAGES['error'][] = "Your session data was corrupt. Please login again.";
	
include('inc_header.php');
?>
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