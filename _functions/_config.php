<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* _config.php						*
*									*
************************************/

// Diese Datei beinhaltet Variablen, die Modulübergreifend benutzt werden - geladen wird die Datei über die _functions.php

// Konstanten für die Verbindung zur MySQL-Datenbank, in folgender Reihenfolge: Serveradresse, Datenbank, Benutzername, Passwort
define("SMAR_MYSQL_SERVER", "localhost");
define("SMAR_MYSQL_DB", "smar");
define("SMAR_MYSQL_USER", "root");
define("SMAR_MYSQL_PW", "");
define("SMAR_MYSQL_PREFIX", "smar");

// Konstanten für das CMS
define("SMAR_CMS_PASSWORT_SALZ", "Ih/VzAdKi8eD"); //Salz fürs Passwort. Wird an Passwort angehängt und mitverschlüsselt
?>