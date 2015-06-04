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
	require('../includes/PHPMailer/PHPMailerAutoload.php');
	
	$return = $_POST;
  	
	// Check variables passed
	if ($return["email"] == '')
	{
		$return["action"] = 'fail';
	} else {
		// Check if email address is on file
		try {
			$db = new baseConnection();
			$conn = $db->getConn();
			
			$query = $conn->prepare("SELECT * FROM vacations_users WHERE email = '".$return['email']."'");
			$query->execute();
			$results = $query->fetchAll();
		} catch(PDOException $e) {
			echo $e->getMessage();
		}
		
		$emailCount = count($results);
		
		// If found email address... Continue with form
		if ($emailCount === 1)
		{
			// Create password verification value, and set it in the DB.
			$randomNum = rand(1000, 999999999);
			$pwVerifyCode = md5('55hdUGVslehHw9rjvi3'.$randomNum.$return['email']);
						
			try {
				$conn2 = $db->getConn();
				
				// Find the last request ID
				$query2 = $conn2->prepare("UPDATE vacations_users SET pwVerify = '$pwVerifyCode' WHERE email = '".$return['email']."'");
				$query2->execute();
				
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			// Send email with reset information
			$mail = new PHPMailer;
			
			$mail->From = $noReplyEmail;
			$mail->FromName = 'NoReply';
			$mail->addAddress($return['email']);
			$mail->addReplyTo($noReplyEmail, 'NoReply');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');
			
			$mail->isHTML(true);   
			$mail->Subject = "Password Reset - GCU Web Design Vacations";
			
			$mail->Body    = 'You are receiving this message because there has been a request to reset your password.  If you are receiving this email in error, please contact your admin.
			<br><br>
			To continue with your reset, please click this link:
			<br><br>
			<a href="'.$siteRoot.'/updaters/doPwReset.php?pwvar='.$pwVerifyCode.'">Reset Your Password</a>';
			
			$mail->send();
			
		} else {
			$return["action"] = 'fail';
		}
	}
	
	$return["json"] = json_encode($return);
	echo json_encode($return);
	}
?>