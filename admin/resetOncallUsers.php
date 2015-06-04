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
	require('../includes/baseConnection.php');
	require('../setup/setup.php');
	
	$oncallSelectDropdownArray = $_POST['oncallSelectDropdown'];
	
	$setupDB = new setupDB();
	$setupDB->addDataToOncall($oncallSelectDropdownArray);
		
	header('Location: '.$siteRoot.'/admin/oncallRotation.php');
?>