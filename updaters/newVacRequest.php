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
	require('../settings.php');
	
	$return = $_POST;
  	
	// Check variables passed
	if ($return["startDate"] == '' || $return["endDate"] == '' || $return["requestDate"] == '' || $return["userID"] == '')
	{
		$return["action"] = 'fail';
	} else {
		date_default_timezone_set('America/Phoenix');
		
		// Check the date differences
		// Must be at least 7 days out or with the next 90 days
		$todayDateTime = new DateTime($return["requestDate"]);
		$startDateTime = new DateTime($return["startDate"]);
		$endDateTime = new DateTime($return["endDate"]);
		$interval = $todayDateTime->diff($startDateTime);
		$daysDifference = $interval->format('%a');
		
		// Set certain values depending on if this new request was submitted by an admin
		$userForSubmission = "";
		
		if(!isset($return['adminSubmission']))
		{
			$userForSubmission = $return['userID'];
		} else if ($return['adminSubmission'] == 'yes') {
			$userForSubmission = $return['userSelect'];
		}
		
		if(!isset($return['overrideTimeframes']))
		{
			$return['overrideTimeframes'] = "";
		}
		
		// Set vacation approval status
		$vacationApprovalStatus = "";
		
		if ($return['vacApproval'] == "vacApproved")
		{
			$vacationApprovalStatus = '1';
		} else {
			$vacationApprovalStatus = '0';
		}
		
		
		if ($return['overrideTimeframes'] === 'true' || ($daysDifference >= $minTimeframeLimitation && $daysDifference <= $maxTimeframeLimitation && $startDateTime <= $endDateTime && $startDateTime > $todayDateTime))
		{
			include('../includes/baseConnection.php');
			require('../includes/PHPMailer/PHPMailerAutoload.php');
			$nextRequestID = "";
			
			try {
				$db = new baseConnection();
				$conn = $db->getConn();
				
				// Find the last request ID
				$query = $conn->prepare("SELECT requestID FROM vacations_requests ORDER BY requestID DESC");
				$query->execute();
				$singleResult = $query->fetch();
				
				$nextRequestID = $singleResult['requestID'] + 1;
				
				// Now add the new request with the updated requestID number
				$query = $conn->prepare("INSERT INTO vacations_requests (requestID, user, startDate, endDate, requestDate, approval, description) VALUES ('".$nextRequestID."', '".$userForSubmission."', '".$return['startDate']."', '".$return['endDate']."', '".$return['requestDate']."', '".$vacationApprovalStatus."', :description)");
				$query->execute(array(
					':description' => $return['shortDescr']
				));
				
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
			
			// Now see if email submission was requested to be stopped
			
			if ($return['emailConfirm'] == "send_email") {
				// Send email with reset information
				$mail = new PHPMailer;
				
				$mail->From = $noReplyEmail;
				$mail->FromName = 'Vacation Request';
				$mail->addAddress($adminEmail);
				$mail->addReplyTo($noReplyEmail, 'NoReply');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');
				
				$mail->isHTML(true);   
				$mail->Subject = "Vacation Request";
				
				$mail->Body    = 'Vacation request has been submitted for '.$return['startDate'].'
				<br><br>
				<a href="'.$siteRoot.'">Visit Website</a>';
				
				$mail->send();
			}
			
			$return["action"] = 'pass';
		} else {
			$return["action"] = 'fail';
		}
	}
	
	//$return["json"] = json_encode($return);
	echo json_encode($return);
}
?>