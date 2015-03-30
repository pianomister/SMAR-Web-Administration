<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* inc_login.php						*
*									*
************************************/

	if(isset($_POST['sendLogin']))
	{
		$user = $_POST['loginUsername'];
		$passwort = $_POST['loginPassword'];

		//Datenbankverbindung initialisieren
		require_once('_functions/_functions.php');
		if(!(isset($SMAR_DB))) {
			$SMAR_DB = new SMAR_MysqlConnect();
		}

		//Abfrage der Logindaten
		$result = $SMAR_DB->dbquery("SELECT * FROM smar_user WHERE name = '".$SMAR_DB->real_escape_string($user)."'");
		$row = $result->fetch_array();

		//Prüfen, ob Felder leer
        if(($user == "") || ($_POST['loginPassword'] == "")) {

			$SMAR_MESSAGES['error'][] = "Bitte jedes Eingabefeld ausfüllen.";

		} else {
			$passwort = hash("sha256", $passwort.$row['salt']);

			if(($user == $row['name']) && ($passwort == $row['password'])) {

				session_start();
				$sID = session_id();

				$_SESSION['loginID']		= $row['user_id'];
				$_SESSION['loginUsername']	= $row['name'];
				$_SESSION['loginTime']		= time();
				$_SESSION['loginLastActivity']		= time();

				$_SESSION['login'] = 1;

				header("location: index.php");

			} else {
				$SMAR_MESSAGES['error'][] = "Die Logindaten waren nicht korrekt.";
			}
		}
	}
?>