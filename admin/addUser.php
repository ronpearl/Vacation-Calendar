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
	
	try{
		$db = new baseConnection;
		
		// First check what the last user id is, and increment that number to add a new user.
		$conn = $db->getConn();
		$query = $conn->prepare("SELECT uid FROM vacations_users ORDER BY uid DESC");
		$query->execute();
		$singleResult = $query->fetch();
		
		$nextUID = $singleResult['uid'] + 1;
		
					
		$conn2 = $db->getConn();
		$query2 = $conn2->prepare("INSERT INTO vacations_users (uid, first, last, email, pass, color, admin) VALUES (:uid, :first, :last, :email, '19e59582214bc05df30d0263125a3498', :color, :admin)");
		$query2->execute(array(
			':uid' => $nextUID,
			':first' => $firstName,
			':last' => $lastName,
			':email' => $email,
			':color' => $color,
			':admin' => $adminStat
		));
	} catch(PDOException $e) {
		echo $e->getMessage();
	}
		
		
	header('Location: '.$siteRoot.'/admin');
?>