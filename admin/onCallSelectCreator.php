<?php
	session_start();

if (is_ajax()) {
  if (isset($_POST["action"]) && !empty($_POST["action"])) { //Checks if action value exists
    $action = $_POST["action"];
    switch($action) { //Switch case for value of action
      case "test": test_function(); break;
    }
  }
}

//Function to check if the request is an AJAX request
function is_ajax() {
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function test_function(){
	include('../includes/baseConnection.php');
	require('../settings.php');
	
	$return = $_POST;
  	
	// Check variables passed
	if ($return["userID"] == '' || $return["weekNum"] == '')
	{
		$return["action"] = 'fail';
	} else {
		$return['dropdownInformation'] = "";
		
		try {
			$db = new baseConnection();
			$conn = $db->getConn();
			$query = $conn->prepare("SELECT uid, first, last FROM vacations_users ORDER BY first ASC");
			$query->execute();
			$allUsers = $query->fetchAll();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		foreach($allUsers as $val)
		{
			if ($return['userID'] == $val['uid'])
			{
				$return['dropdownInformation'] .= '<option value="'.$val['uid'].'" selected>'.$val['first'].' '.$val['last'].'</option>';
			} else {
				$return['dropdownInformation'] .= '<option value="'.$val['uid'].'">'.$val['first'].' '.$val['last'].'</option>';
			}
		}
	}
	
	$return["json"] = json_encode($return);
	echo json_encode($return);
	}
?>