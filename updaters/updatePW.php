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
	$return = $_POST;
  	
	// Check variables passed
	if ($return["email"] == '' || $return["verifyCode"] == '' || $return["passw"] == '')
	{
		$return["action"] = 'fail';
	} else {
		
		// start DB connection
		include('../includes/baseConnection.php');
		$db = new baseConnection();
		
		// Validate some information first
		$conn = $db->getConn();
		$query = $conn->prepare("SELECT count(*) FROM vacations_users WHERE email = :email AND pwVerify = :pwVerify");
		$query->execute(array(
			':email' => $return["email"],
			':pwVerify' => $return["verifyCode"]
		));
		$numberOfRows = $query->fetchColumn();
		
		if ($numberOfRows == 1)
		{
			// Hash the password
			$hashedPassword = md5("$98gDkc38!ndS*".$return["passw"]);
			
			try {
				// Now setup to have the pwVerify code removed from the DB since it's only a one-time use item
				// AND change the password
				$conn2 = $db->getConn();
				$query2 = $conn2->prepare("UPDATE vacations_users SET pwVerify = '', pass = :pass WHERE email = :email");
				$query2->execute(array(
					':email' => $return["email"],
					':pass' => $hashedPassword
				));
				
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			$return["action"] = 'pass';
		} else {
			$return["action"] = 'fail';
		}
	}
	
	$return["json"] = json_encode($return);
	echo json_encode($return);
	}
?>