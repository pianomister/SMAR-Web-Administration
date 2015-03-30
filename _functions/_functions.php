<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* _functions.php					*
*									*
************************************/

// Diese Datei beinhaltet Funktionen, die Modulübergreifend benutzt werden

// error_reporting(NULL);
error_reporting(E_ALL); // ... wir sind ja am programmieren :-)

// Lade Konfiguration
require_once('_config.php');

// Klasse für die MySQLi-Operationen
class SMAR_MysqlConnect extends mysqli
{	

    function SMAR_MysqlConnect()
    {
		parent::__construct(SMAR_MYSQL_SERVER,SMAR_MYSQL_USER,SMAR_MYSQL_PW,SMAR_MYSQL_DB);
		if($this->connect_error) {
			echo "Keine Verbindung konnte hergestellt werden";
			return false;
		}
    }
 
    function dbquery($sql)
    {
		if(!($erg = $this->query($sql))) {
			echo "Fehler bei der Abfrage, hier ist das Statement: ".$sql." --- Fehlernummer ".$this->errno." ::: ".$this->error;
			return false;
			#die('Query Error (' . $this->errno . ') '.$sql. $this->error);
		}
        return $erg;
    }
}

// Print messages
// Types: success (green), warning (yellow), error (red)
function smar_print_messages($messages)
{
	foreach($messages as $mes_type => $type_array) {
		foreach($type_array as $type => $content) {
			echo '<div class="messages messages-'.$mes_type.'">';
			echo smar_post_status($mes_type);
			echo '&nbsp;&nbsp;'.$content.' <br>('.date('d.m.Y H:i:s').')</div>';
		}
	}
}

// status to icon
function smar_post_status($status) {
	switch($status) {
		case '0':
			echo('<img src="img/icons/help.png" width="16" alt="">');
			break;
		case '1':
		case 'success':
			echo('<img src="img/icons/accept.png" width="16" alt="">');
			break;
		case '2':
		case 'warning':
			echo('<img src="img/icons/error.png" width="16" alt="">');
			break;		
		case '3':
		case 'error':
			echo('<img src="img/icons/cross.png" width="16" alt="">');
			break;	
		default:
			echo('<img src="img/icons/help.png" width="16" alt="">');
			break;
	}
}
?>