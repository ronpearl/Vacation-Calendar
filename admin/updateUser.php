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
	
	$firstName = $lastName = $email = $color = $userID = $adminStat = "";
	
	foreach ($_POST as $key => $val)
	{
		$$key = $val;
	}
	
	$color = str_replace("#", "", $color);
	
	
	include_once('../includes/baseConnection.php');
	
	$db = new baseConnection;
	$conn = $db->getConn();
	$query = $conn->prepare("UPDATE vacations_users SET first = :first, last = :last, email = :email, color = :color, admin = :admin WHERE uid = '$userID'");
	$query->execute(array(
		':first' => $firstName,
		':last' => $lastName,
		':email' => $email,
		':color' => $color,
		':admin' => $adminStat
	));
		
		
	header('Location: '.$siteRoot.'/admin');
?>