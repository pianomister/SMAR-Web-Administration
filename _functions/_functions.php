<?php
/************************************
*                                   *
* SMAR                              *
* by                                *
* Raffael Wojtas                    *
* Stephan Giesau                    *
* Sebastian Kowalski                *
*                                   *
* _functions.php                    *
*                                   *
************************************/

// Diese Datei beinhaltet Funktionen, die Modulübergreifend benutzt werden

// error_reporting(NULL);
error_reporting(E_ALL); // ... wir sind ja am programmieren :-) TODO

// Lade Konfiguration
require_once('_config.php');

// class for MySQLi operations
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
			echo '<span>'.$content.' <br>('.date('d.m.Y H:i:s').')</span></div>';
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
			echo('<i class="mdi mdi-alert bg-icon bg-orange color-white"></i>');
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


// get query string params from query string
function smar_get_query_array($string) {
	
	if(strpos(' '.$string, '?') > 1)
		$string = explode('?', $string)[1];
	
	$params = explode('&', $string);
	
	$return = array();
	foreach($params as $param) {
		$parts = explode('=', $param);
		if(count($parts) == 2)
			$return[$parts[0]] = $parts[1];
	}
	
	return $return;
}


// save output of input field values
function smar_form_input($inputtext) {
	return str_replace('"',"'",strip_tags($inputtext));
}


// create pagination
function smar_pagination($url, $num_items, $items_per_page, $current_page, $param_name = 'limit') {
	
	$html = '';
	$last_page = ceil($num_items / $items_per_page) - 1;
	
	if($num_items > 0 && $last_page != 0) {
		
		if($current_page != 0)
			$html .= '<a href="'.$url.'&'.$param_name.'='.($current_page-1).'" class="ajax" title="Page back"><i class="mdi mdi-arrow-left bg-icon bg-gray"></i></a>';
		
		if($current_page > 1)
			$html .= '<a href="'.$url.'&'.$param_name.'=0" class="ajax text-icon bg-gray">1</a>';
		
		if($current_page > 2)
			$html .= '...';
		
		for($i = $current_page-1; $i <= $current_page+1; $i++) {
			if($i >= 0 && $i <= $last_page)
				$html .= '<a href="'.$url.'&'.$param_name.'='.$i.'" class="ajax text-icon bg-gray">'.($i+1).'</a>';
		}
		
		if($current_page < $last_page-2)
			$html .= '...';

		if($current_page < $last_page-1)
			$html .= '<a href="'.$url.'&'.$param_name.'='.$last_page.'" class="ajax text-icon bg-gray">'.($last_page+1).'</a>';
		
		if($current_page != $last_page)
			$html .= '<a href="'.$url.'&'.$param_name.'='.($current_page+1).'" class="ajax" title="Page forward"><i class="mdi mdi-arrow-right bg-icon bg-gray"></i></a>';		
	}
	
	return $html;
}
?>