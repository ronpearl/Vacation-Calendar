<?php
 	session_start();
	
	if(isset($_SESSION['login_user']))
	{
		$_SESSION['login_user'] = '';
		$_SESSION['login_id'] = '';
		
		// remove all session variables
		session_unset(); 
		
		// destroy the session 
		session_destroy(); 
	}
	
	header("Location:../index.php");
?>