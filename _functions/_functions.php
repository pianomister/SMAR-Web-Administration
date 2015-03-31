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

// parse role status
function smar_parse_role_web($role_web) {
	switch($role_web) {
		case '0':
			return "No web rights";
			break;
		case '1':
			return "Read only";
			break;
		case '2':
			return "Products & Units";
			break;
		case '3':
			return "Products, Units, Shelves, Sections";
			break;
		case '4':
			return "Edit all";
			break;
		case '8':
			return "Manager";
			break;
		case '9':
			return "Administrator";
			break;
		default:
			return "Unknown - Code: ".$role_web;
	}
}

// parse role status
function smar_parse_role_device($role_web) {
	switch($role_web) {
		case '0':
			return '<svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="#FF0000" d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41Z" /></svg>';
			break;
		case '1':
			return '<svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="#00FF00" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" /></svg>';
			break;
		default:
			return '<svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="#FF0000" d="M10,19H13V22H10V19M12,2A6,6 0 0,1 18,8C17.67,9.33 17.33,10.67 16.5,11.67C15.67,12.67 14.33,13.33 13.67,14.17C13,15 13,16 13,17H10C10,15.33 10,13.92 10.67,12.92C11.33,11.92 12.67,11.33 13.5,10.67C14.33,10 14.67,9 15,8A3,3 0 0,0 12,5A3,3 0 0,0 9,8H6A6,6 0 0,1 12,2Z" /></svg>';
	}
}
?>