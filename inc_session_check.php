<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* inc_session_check.php				*
*									*
************************************/

session_start();
if($_SESSION['login'] == 0)
{
	session_destroy();
	smar_handle_logout('login');
}
else
{	require_once('_functions/_functions.php');
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
    //Abfrage der Logindaten
	$result = $SMAR_DB->dbquery("SELECT * FROM smar_user WHERE username = '".$SMAR_DB->real_escape_string($_SESSION['loginUsername'])."'");
	$row = $result->fetch_array();

    if(($_SESSION['loginID'] != $row['user_id']) OR ($_SESSION['loginUsername'] != $row['username']))
    {
  	    $_SESSION['login'] = 0;
		session_destroy();
		smar_handle_logout('sessionFehler');
  	}

	if (time() - $_SESSION['loginLastActivity'] > 720)
  	{
  	    $_SESSION['login'] = 0;
		session_destroy();
		smar_handle_logout('timeout');
  	} else {
		//Zeit für neuen Klick setzen
		$_SESSION['loginLastActivity']		= time();
	}
}



// Wenn Topinclude, den Timeout anders behandeln
if(isset($topinclude) && $topinclude == 1) {
	#$topinclude = 0;
}
if(!isset($topinclude)) {
	$topinclude = 0;
}

/**
  * Funktion zur Behandlung von Session-Timeout und Login
  * Abhängig von AJAX- oder Topinclude
  */
function smar_handle_logout($type) {
	//Topinclude in Funktion zugänglich machen
	global $topinclude;
	$topinclude = 1; // DEBUG
	
	//JS-Code zum Anzeigen der Timeout-Box auf der Seite
	$ajaxTimeout =	'<script>document.getElementById("overlay").style.display = "block";'.
					'document.getElementById("timeout-box").style.display = "block";</script>';

	//Je nach Fehler anders reagieren
	switch($type) {
		case 'sessionFehler':
		case 'timeout':
			if($topinclude == 1)
				header("Location: login.php?action=".$type);
			else
				echo $ajaxTimeout;
		break;
		//standardmäßig ohne Meldung auf Login-Seite weiterleiten
		default:
			if($topinclude == 1)
				header("Location: login.php");
			else
				echo $ajaxTimeout;
		break;
	}
	
	//$topinclude zurücksetzen
	if($topinclude == 1)
		$topinclude = 0;
}
?>