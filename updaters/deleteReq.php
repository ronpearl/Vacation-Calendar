<?php
	session_start();
	
	if (!isset($_SESSION["login_user"]))
	{
		$loggedIn = false;
	} else {
		$loggedIn = true;
		$userID = $_SESSION["login_id"];
	}
	
	require('../settings.php');
	
	$requestID = $_GET['requestID'];
	
	include_once('../includes/baseConnection.php');
	
	$db = new baseConnection;
	$conn = $db->getConn();
	$query = $conn->prepare("DELETE FROM vacations_requests WHERE requestID = '$requestID'");
	$query->execute();
	
	header('Location: '.$siteRoot);
?>