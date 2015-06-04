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
	
	$onCallPerson = $weekNumber  = "";
	
	foreach ($_POST as $key => $val)
	{
		$$key = $val;
	}
	
	
	include_once('../includes/baseConnection.php');
	
	$db = new baseConnection;
	$conn = $db->getConn();
	$query = $conn->prepare("UPDATE vacations_oncall SET user = :newUser WHERE week_number = '$weekNumber'");
	$query->execute(array(
		':newUser' => $onCallPerson
	));
	$db = null;
		
	header('Location: '.$siteRoot.'/admin/oncallRotation.php');
?>