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
	if ($return["email"] == '' || $return["pass"] == '')
	{
		$return["action"] = 'fail';
	} else {
	
		include('baseConnection.php');
	  
	  	// Hash the password
		$hashedPassword = md5("$98gDkc38!ndS*".$return["pass"]);
		
		try {
			$db = new baseConnection();
			$conn = $db->getConn();
			$query = $conn->prepare("SELECT uid, admin FROM vacations_users WHERE email = :email AND pass = :password");
			$query->execute(array(
				':email' => $return["email"],
				':password' => $hashedPassword
			));
			$results = $query->fetchAll();
			
			$count = count($results);
			
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		foreach($results as $row)
		{
			$userID = $row['uid'];
			$adminValue = $row['admin'];
		}
		
		if($count == 1)
		{
			$_SESSION["login_user"] = 'loggedIn';
			$_SESSION["login_id"] = $userID;
			$_SESSION["admin"] = $adminValue;
			$return["action"] = 'pass';
		} else {
			$return["action"] = 'fail';
		}
	}
	
	$return["json"] = json_encode($return);
	echo json_encode($return);
	}
?>