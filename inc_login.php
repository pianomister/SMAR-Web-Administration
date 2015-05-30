<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* inc_login.php                     *
*                                   *
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
		$result = $SMAR_DB->dbquery("SELECT * FROM ".SMAR_MYSQL_PREFIX."_user WHERE username = '".$SMAR_DB->real_escape_string($user)."'");
		$row = $result->fetch_array();

		//Prüfen, ob Felder leer
    if(($user == "") || ($_POST['loginPassword'] == "")) {

			$SMAR_MESSAGES['error'][] = "Please fill in all fields.";

		} else {
			$passwort = hash("sha256", $passwort.$row['salt']);

			if(($user == $row['username']) && ($passwort == $row['password'])) {

				session_start();
				$sID = session_id();

				$_SESSION['loginID']		= $row['user_id'];
				$_SESSION['loginUsername']	= $row['username'];
				$_SESSION['loginSurname']	= $row['surname'];
				$_SESSION['loginLastname']	= $row['lastname'];
				$_SESSION['loginPnr']		= $row['pnr'];
				$_SESSION['loginRole']		= intval($row['role_web']);
				$_SESSION['loginTime']		= time();
				$_SESSION['loginLastActivity']		= time();
				$token = array(
					"user_id" => $row['user_id'],
					"username" => $row['username'],
					"user_role" => $row['role_web'],
					"device" => "false"
				);
				$_SESSION['loginJWTToken'] = JWT::encode($token, SMAR_JWT_SSK);

				$_SESSION['login'] = 1;

				header("location: index.php");

			} else {
				$SMAR_MESSAGES['error'][] = "Your login data was incorrect.";
			}
		}
	}
?>