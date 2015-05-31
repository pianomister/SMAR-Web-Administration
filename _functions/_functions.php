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

// Timezone
date_default_timezone_set('Europe/Berlin');

// error_reporting(NULL);
error_reporting(E_ALL); // ... wir sind ja am programmieren :-) TODO

// Lade Konfiguration
require_once('_config.php');
require_once('_jwt/JWT.php');

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
			$this->set_charset('utf8');
    }
 
    function dbquery($sql)
    {
			if(!($erg = $this->query($sql))) {
				//TODO remove
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
		case '10':
			return "Read only";
			break;
		case '20':
			return "Products & Units";
			break;
		case '30':
			return "Products, Units, Orders";
			break;
		case '40':
			return "Products, Units, Shelves, Sections, Orders";
			break;
		case '50':
			return "Products, Units, Shelves, Sections, Orders, Device Management";
			break;
		case '60':
			return "Products, Units, Shelves, Sections, Orders, Device Management, User Management";
			break;
		case '70':
			return "System Administrator";
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


// generate new SVG for a shelf and save it to DB
// called after changes on shelf or sections
function smar_update_shelf_svg($shelfid) {
	
	$shelfid = intval($shelfid);
	
	// init database
	if(!(isset($SMAR_DB))) {
		$SMAR_DB = new SMAR_MysqlConnect();
	}
	
	// check if shelf exists
	$result = $SMAR_DB->dbquery("SELECT name FROM ".SMAR_MYSQL_PREFIX."_shelf WHERE shelf_id = '".$SMAR_DB->real_escape_string($shelfid)."'");
	if($result->num_rows != 0) {
	
		$svg = file_get_contents(SMAR_SITE_URL.SMAR_ROOT_PATH.'svg_generator.php?id='.$shelfid);
		
		$check = $SMAR_DB->dbquery("SELECT shelf_id FROM ".SMAR_MYSQL_PREFIX."_shelf_graphic WHERE shelf_id = '".$SMAR_DB->real_escape_string($shelfid)."'");
		if($check->num_rows == 0) {
			$update = $SMAR_DB->dbquery("INSERT INTO ".SMAR_MYSQL_PREFIX."_shelf_graphic
														(shelf_id, graphic, created) VALUES
														('".$SMAR_DB->real_escape_string($shelfid)."', '".$SMAR_DB->real_escape_string($svg)."', NOW())
														");
		} else {
			$update = $SMAR_DB->dbquery("UPDATE ".SMAR_MYSQL_PREFIX."_shelf_graphic SET
																			graphic = '".$SMAR_DB->real_escape_string($svg)."'
																			WHERE shelf_id = '".$SMAR_DB->real_escape_string($shelfid)."'");
		}
		
		return ($update === TRUE);
	}
	return false;
}


// create pagination
function smar_pagination($url, $num_items, $items_per_page, $current_page, $param_name = 'limit') {
	
	$html = '';
	$last_page = ceil($num_items / $items_per_page) - 1;
	
	if($num_items > 0 && $last_page != 0) {
		
		if($current_page < 0)
			$current_page = 0;
		if($current_page > $last_page)
			$current_page = $last_page;
		
		if($current_page != 0)
			$html .= '<a href="'.$url.'&'.$param_name.'='.($current_page-1).'" class="ajax" title="Page back"><i class="mdi mdi-arrow-left bg-icon"></i></a>';
		
		if($current_page > 1)
			$html .= '<a href="'.$url.'&'.$param_name.'=0" class="ajax text-icon '.($current_page == 0 ? 'bg-black' : 'bg-gray').'">1</a>';
		
		if($current_page > 2)
			$html .= '...';
		
		for($i = $current_page-1; $i <= $current_page+1; $i++) {
			if($i >= 0 && $i <= $last_page)
				$html .= '<a href="'.$url.'&'.$param_name.'='.$i.'" class="ajax text-icon '.($current_page == $i ? 'bg-black' : 'bg-gray').'">'.($i+1).'</a>';
		}
		
		if($current_page < $last_page-2)
			$html .= '...';

		if($current_page < $last_page-1)
			$html .= '<a href="'.$url.'&'.$param_name.'='.$last_page.'" class="ajax text-icon '.($current_page == $last_page ? 'bg-black' : 'bg-gray').'">'.($last_page+1).'</a>';
		
		if($current_page != $last_page)
			$html .= '<a href="'.$url.'&'.$param_name.'='.($current_page+1).'" class="ajax" title="Page forward"><i class="mdi mdi-arrow-right bg-icon bg-gray"></i></a>';		
	}
	
	return $html;
}
?>