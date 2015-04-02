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
error_reporting(E_ALL); // ... wir sind ja am programmieren :-) TODO

// Lade Konfiguration
require_once('_config.php');

// Klasse für die MySQLi-Operationen
class SMAR_MysqlConnect extends mysqli
{	

    function SMAR_MysqlConnect()
    {
		parent::__construct(SMAR_MYSQL_SERVER,SMAR_MYSQL_USER,SMAR_MYSQL_PW,SMAR_MYSQL_DB);
		if($this->connect_error) {
			echo "MySQL connection could not be created";
			return false;
		}
    }
 
    function dbquery($sql)
    {
		if(!($erg = $this->query($sql))) {
			echo "Error with SQL request, the statement: ".$sql." --- Fehlernummer ".$this->errno." ::: ".$this->error;
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

// get current tld
function smar_site_url()
{
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$domainName = $_SERVER['HTTP_HOST'];
	return $protocol.$domainName;
}
define( 'SMAR_SITE_URL', smar_site_url() );

// get current directory
function smar_current_dir() {
	$folders = explode('/', $_SERVER['REQUEST_URI']);
	unset($folders[count($folders)-1]);
	return implode('/', $folders).'/';
}
define( 'SMAR_CURRENT_DIR', smar_current_dir() );

// status to icon
function smar_post_status($status) {
	switch($status) {
		case '0':
			echo('<i class="mdi mdi-help-circle bg-icon bg-gray color-white"></i>');
			break;
		case '1':
		case 'success':
			echo('<i class="mdi mdi-check bg-icon bg-green color-white"></i>');
			break;
		case '2':
		case 'warning':
			echo('<i class="mdi mdi-close bg-icon bg-red color-white"></i>');
			break;		
		case '3':
		case 'error':
			echo('<i class="mdi mdi-close bg-icon bg-red color-white"></i>');
			break;	
		default:
			echo('<i class="mdi mdi-help-circle bg-icon bg-gray color-white"></i>');
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
			return '<i class="mdi mdi-close bg-icon bg-red color-white"></i>';
			break;
		case '1':
			return '<i class="mdi mdi-check bg-icon bg-green color-white"></i>';
			break;
		default:
			return '<i class="mdi mdi-help-circle bg-icon bg-gray color-white"></i>';
	}
}
?>