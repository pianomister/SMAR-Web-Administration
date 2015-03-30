<?php
/************************************
*									*
* SMAR								*
* by								*
* Raffael Wojtas					*
* Stephan Giesau					*
* Sebastian Kowalski				*
*									*
* logout.php						*
*									*
************************************/

	session_start();
	if($_SESSION['login'] == 0)
	{
		header("location: login.php");
	}
	else
	{
		$_SESSION['login'] = 0;
		session_destroy();
		header("location: login.php?action=logout");
	}
?>