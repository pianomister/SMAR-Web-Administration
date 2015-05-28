<?php
$error = FALSE;
require_once('_functions/_phpqrcode/qrlib.php');
require_once('_functions/_functions.php');
require_once('inc_session_check.php');

if($_SESSION['loginRole'] < 60) {
	$SMAR_MESSAGES['error'][] = 'Insufficient permissions for user management';
	$error = TRUE;
} else {
	if(isset($_GET['editID']) && !empty($_GET['editID'])) {
		$userid = $_GET['editID'];
		$result = $SMAR_DB->dbquery("SELECT password_device, role_device FROM ".SMAR_MYSQL_PREFIX."_user WHERE user_id = '".$SMAR_DB->real_escape_string($userid)."'");
		if(!($row = $result->fetch_array())) {
			$SMAR_MESSAGES['error'][] = 'User with ID '.$userid.' not known.';
			$error = TRUE;
		} else {
			if($row['role_device'] == 1 && $row['password_device'] != NULL) {
				// Configuring SVG
				$qrData = $row['password_device'];
				$qrToFile = false;
				$qrWidth = 10;
				$qrMarginWidth = 2;
				
				if(isset($_GET['mode']) && !empty($_GET['mode'])) {
					if($_GET['mode'] == "svg") {
						QRcode::svg($qrData, $qrToFile, QR_ECLEVEL_H, $qrWidth, $qrMarginWidth);
					} elseif($_GET['mode'] == "png") {
						QRcode::png($qrData, $qrToFile, QR_ECLEVEL_H, $qrWidth, $qrMarginWidth);
					} else {
						$error = TRUE;
						$SMAR_MESSAGES['error'][] = 'Unknown output mode given.';
					}
				} else {
					$error = TRUE;
					$SMAR_MESSAGES['error'][] = 'No output mode given.';
				}
			} else {
				$SMAR_MESSAGES['error'][] = 'User with ID '.$userid.' not prepared for QR-Code.';
				$error = TRUE;
			}
		}
	} else {
		$error = TRUE;
		$SMAR_MESSAGES['error'][] = 'No user id given.';
	}
}

if($error) {
	if(isset($SMAR_MESSAGES)) { smar_print_messages($SMAR_MESSAGES); unset($SMAR_MESSAGES); }
}
?>