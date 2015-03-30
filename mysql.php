<?php
define('MYSQL_SERVERNAME', "localhost");
define('MYSQL_USERNAME', "root");
define('MYSQL_PASSWORD', "");
define('MYSQL_DB', "itsecurity");

// Klasse für die MySQLi-Operationen
class DBConnect extends mysqli
{	

    function DBConnect()
    {
		parent::__construct(MYSQL_SERVERNAME,MYSQL_USERNAME,MYSQL_PASSWORD,MYSQL_DB);
		if($this->connect_error) {
			#echo "Keine Verbindung konnte hergestellt werden";
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
?>