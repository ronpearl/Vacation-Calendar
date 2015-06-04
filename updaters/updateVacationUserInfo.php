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
	
	$fName = $lName = $email = $color = $submittedUserID = "";
	
	foreach ($_POST as $key => $val)
	{
		$$key = $val;
	}
	
	$color = str_replace("#", "", $color);
	
	// Check to see that the userID submitted is the same as the userID in the session
	// Little fail-safe against HTML form cross-user editing
	if ($userID != $submittedUserID)
	{
		// Do Nothing
	} else {
		include_once('../includes/baseConnection.php');
		
		$db = new baseConnection;
		$conn = $db->getConn();
		$query = $conn->prepare("UPDATE vacations_users SET first = :first, last = :last, email = :email, color = :color WHERE uid = '$userID'");
		$query->execute(array(
			':first' => $fName,
			':last' => $lName,
			':email' => $email,
			':color' => $color
		));
	}
	
	header('Location: '.$siteRoot);
?>