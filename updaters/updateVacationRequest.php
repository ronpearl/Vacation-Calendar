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
	
	$requestID = $approved = $endDate = $startDate = $requestDate = $description = "";
	
	foreach ($_POST as $key => $val)
	{
		$$key = $val;
	}
	
	include_once('../includes/baseConnection.php');
	
	$db = new baseConnection;
	$conn = $db->getConn();
	$query = $conn->prepare("UPDATE vacations_requests SET startDate = :startDate, endDate = :endDate, requestDate = :requestDate, approval = :approval, description = :description WHERE requestID = '$requestID'");
	$query->execute(array(
		':startDate' => $startDate,
		':endDate' => $endDate,
		':requestDate' => $requestDate,
		':approval' => $approved,
		':description' => $description
	));
	
	header('Location: '.$siteRoot);
?>